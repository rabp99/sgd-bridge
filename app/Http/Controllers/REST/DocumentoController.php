<?php

namespace App\Http\Controllers\REST;

use App\Http\Services\CuoService;
use App\Http\Services\EntidadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class DocumentoController extends Controller
{
    protected CuoService $cuoService;

    protected EntidadService $entidadService;

    public function __construct(
        CuoService $cuoService,
        EntidadService $entidadService
    ) {
        $this->cuoService = $cuoService;
        $this->entidadService = $entidadService;
    }

    public function cargoTramite(Request $request)
    {
        $url = "https://ws2.pide.gob.pe/Rest/Pcm/CargoTramite?out=json";

        try {
            $payload = [
                'PIDE' => [
                    'vrucentrem' => $request->vrucentrem,
                    'vrucentrec' => $request->vrucentrec,
                    'vcuo' => $request->vcuo,
                    'vcuoref' => $request->vcuoref,
                    'vnumregstd' => $request->vnumregstd,
                    'vanioregstd' => $request->vanioregstd,
                    'dfecregstd' => $request->dfecregstd,
                    'vuniorgstd' => $request->vuniorgstd,
                    'vusuregstd' => $request->vusuregstd,
                    'bcarstd' => $request->bcarstd,
                    'vobs' => $request->vobs,
                    'cflgest' => $request->cflgest,
                ]
            ];
            $response = Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8'
            ])
                ->post($url, $payload);
            if ($response->successful()) {
                $data = $response->json();
                dd($data);
                /*
                if ($data['getTipoDocumentoResponse']) {
                    return response()->json($data['getTipoDocumentoResponse']['return']);
                }
                */
                return response()->json([
                    'error' => true,
                    'message' => 'No se pudo enviar el cargo.'
                ], 400);
            }
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }

    public function consultarTramite(Request $request)
    {
        $request->validate([
            'vcuo' => 'nullable|string',
        ]);

        // $url = "https://ws2.pide.gob.pe/Rest/Pcm/ConsultarTramite?out=json";
        $url = 'http://wildfly:8080/wsiotramite/Tramite?wsdl';
        $client = new \SoapClient($url, [
            'trace' => 1,
            'exceptions' => true
        ]);

        try {
            $payload = [
                'request' => $request->vcuo
            ];

            $response = $client->consultarTramiteResponse($payload);
            $return = $response->return;

            if ($return->vcodres === '0000') {
                return response()->json([
                    'result' => true,
                    'message' => $return->vdesres,
                    'cflgest' => $return->cflgest
                ]);
            }

            return response()->json([
                'result' => false,
                'message' => 'La consulta no encontró el documento.'
            ]);
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }

    public function getTipos(Request $request)
    {
        $url = "https://ws2.pide.gob.pe/Rest/Pcm/TipoDocumento?out=json";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8'
            ])
                ->post($url, null);
            if ($response->successful()) {
                $data = $response->json();
                if ($data['getTipoDocumentoResponse']) {
                    return response()->json($data['getTipoDocumentoResponse']['return']);
                }
                return response()->json([
                    'error' => true,
                    'message' => 'No se encontraron tipo de documentos.'
                ], 400);
            }
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }

    public function recepcionarTramite(Request $request)
    {
        $request->validate([
            'vrucentrem' => 'required|string',
            'vrucentrec' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $isValid = $this->entidadService->validate($value);
                    if (!$isValid) {
                        $fail("El RUC de la entidad receptora no es válido.");
                    }
                },
            ],
            'vnomentemi' => 'required|string',
            'vuniorgrem' => 'required|string',
            'vcuoref' => 'nullable|string',
            'ccodtipdoc' => 'required|string',
            'vnumdoc' => 'required|string',
            'dfecdoc' => 'required|date',
            'vuniorgdst' => 'required|string',
            'vnomdst' => 'required|string',
            'vnomcardst' => 'required|string',
            'vasu' => 'required|string',
            'snumanx' => 'required|integer|min:0',
            'snumfol' => 'required|integer|min:0',
            'bpdfdoc' => 'required|file',
            'vnomdoc' => 'required|string',
            'lstanexos' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    foreach ($value as $item) {
                        if (!is_array($item) || !isset($item['vnomdoc'])) {
                            $fail("Cada elemento de {$attribute} debe ser un array con el campo 'vnomdoc'.");
                        }
                    }
                },
            ],
            'vurldocanx' => 'nullable|url',
            'ctipdociderem' => 'required|in:1,2',
            'vnumdociderem' => 'required|string',
        ]);

        // $url = "https://ws2.pide.gob.pe/services/PcmIMgdTramite?wsdl";
        $nginxIp = trim(shell_exec("getent hosts sgd-bridge-nginx | awk '{ print $1 }'"));
        $url = 'http://' . $nginxIp . '/wsiotramite/Tramite?wsdl';

        $client = new \SoapClient($url, [
            'trace' => 1,
            'exceptions' => true
        ]);

        $rucEntidadReceptora = $request->vrucentrem;
        $vcuo = $this->cuoService->getCuoEntidad($rucEntidadReceptora, "1");

        try {
            $payload = [
                "receptionRequest" => [
                    "vrucentrem" => $request->vrucentrem,
                    "vrucentrec" => $rucEntidadReceptora,
                    "vnomentemi" => $request->vnomentemi,
                    "vuniorgrem" => $request->vuniorgrem,
                    "vcuo" => $vcuo,
                    "vcuoref" => $request->vcuoref,
                    "ccodtipdoc" => $request->ccodtipdoc,
                    "vnumdoc" => $request->vnumdoc,
                    "dfecdoc" => $request->dfecdoc,
                    "vuniorgdst" => $request->vuniorgdst,
                    "vnomdst" => $request->vnomdst,
                    "vnomcardst" => $request->vnomcardst,
                    "vasu" => $request->vasu,
                    "snumanx" => $request->snumanx,
                    "snumfol" => $request->snumfol,
                    "bpdfdoc" => base64_encode(file_get_contents($request->file('bpdfdoc')->getRealPath())),
                    "vnomdoc" => $request->vnomdoc,
                    'lstanexos' => $request->lstanexos,
                    "vurldocanx" => $request->vurldocanx,
                    "ctipdociderem" => $request->ctipdociderem,
                    "vnumdociderem" => $request->vnumdociderem
                ]
            ];

            $response = $client->recepcionarTramiteResponse($payload);
            $return = $response->return;
            if ($return->vcodres === '0000') {
                return response()->json([
                    'result' => true,
                    'message' => $return->vdesres,
                    'vcuo' => $vcuo
                ]);
            }

            return response()->json([
                'result' => false,
                'message' => 'No se pudo recepcionar el documento.'
            ]);
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }
}
