<?php

namespace App\Http\Controllers\REST;

use App\Http\Services\CuoService;
use App\Http\Services\EntidadService;
use App\Http\Services\DocumentoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class DocumentoController extends Controller
{
    protected CuoService $cuoService;

    protected EntidadService $entidadService;

    protected DocumentoService $documentoService;

    public function __construct(
        CuoService $cuoService,
        EntidadService $entidadService,
        DocumentoService $documentoService
    ) {
        $this->cuoService = $cuoService;
        $this->entidadService = $entidadService;
        $this->documentoService = $documentoService;
    }

    public function cargoTramite(Request $request)
    {
        $request->validate([
            'vrucentrem' => 'required|string',
            'vrucentrec' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (env('APP_ENV') === 'local' || env('APP_ENV') === 'staging') {
                        return true;
                    }

                    $isValid = $this->entidadService->validate($value);
                    if (!$isValid) {
                        $fail("El RUC de la entidad receptora no es válido.");
                    }
                },
            ],
            'vcuo' => 'required|string',
            'vcuoref' => 'nullable|string',
            'vnumregstd' => 'required|string',
            'vanioregstd' => 'required|string',
            'dfecregstd' => 'required|date',
            'vuniorgstd' => 'required|string',
            'vusuregstd' => 'required|string',
            'bcarstd' => 'required|file',
            'vobs' => 'nullable|string',
            'cflgest' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, ['E', 'P', 'R', 'O'])) {
                        $fail("El campo {$attribute} debe ser uno de los siguientes valores: E, P, R, O.");
                    }
                },
            ],
        ]);

        if (env('APP_ENV') === 'local' || env('APP_ENV') === 'staging') {
            $nginxIp = trim(shell_exec("getent hosts sgd-bridge-nginx | awk '{ print $1 }'"));
            $url = 'http://' . $nginxIp . '/wsiotramite/Tramite?wsdl';
        } else {
            $url = env('TRAMITE_WS');
        }

        $client = new \SoapClient($url, [
            'trace' => 1,
            'exceptions' => true
        ]);

        try {
            $payload = [
                'request' => [
                    'vrucentrem' => $request->vrucentrem,
                    'vrucentrec' => $request->vrucentrec,
                    'vcuo' => $request->vcuo,
                    'vcuoref' => $request->vcuoref,
                    'vnumregstd' => $request->vnumregstd,
                    'vanioregstd' => $request->vanioregstd,
                    'dfecregstd' => $request->dfecregstd,
                    'vuniorgstd' => $request->vuniorgstd,
                    'vusuregstd' => $request->vusuregstd,
                    "bcarstd" => base64_encode(file_get_contents($request->file('bcarstd')->getRealPath())),
                    'vobs' => $request->vobs,
                    'cflgest' => $request->cflgest,
                ]
            ];

            $response = $client->cargoResponse($payload);
            $return = $response->return;

            if ($return->vcodres === '0000') {
                return response()->json([
                    'result' => true,
                    'message' => $return->vdesres,
                ]);
            }

            return response()->json([
                'result' => false,
                'message' => 'No se pudo realizar el envio del cargo.'
            ], 500);
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }

    public function consultarTramite(Request $request)
    {
        $request->validate([
            'vrucentrem' => 'required|string',
            'vrucentrec' => 'required|string',
            'vcuo' => 'required|string',
        ]);

        if (env('APP_ENV') === 'local' || env('APP_ENV') === 'staging') {
            $nginxIp = trim(shell_exec("getent hosts sgd-bridge-nginx | awk '{ print $1 }'"));
            $url = 'http://' . $nginxIp . '/wsiotramite/Tramite?wsdl';
        } else {
            $url = env('TRAMITE_WS');
        }

        $client = new \SoapClient($url, [
            'trace' => 1,
            'exceptions' => true
        ]);

        try {
            $payload = [
                'request' => [
                    'vrucentrem' => $request->vrucentrem,
                    'vrucentrec' =>  $request->vrucentrec,
                    'vcuo' => $request->vcuo
                ]
            ];

            $response = $client->consultarTramiteResponse($payload);
            $return = $response->return;

            if ($return->vcodres === '0000') {
                return response()->json([
                    'result' => true,
                    'message' => $return->vdesres,
                    'vcuo' => $return->vcuo,
                    'vcuoref' => $return->vcuoref,
                    'vnumregstd' => $return->vnumregstd,
                    'vanioregstd' => $return->vanioregstd,
                    'vuniorgstd' => $return->vuniorgstd,
                    'dfecregstd' => $return->dfecregstd,
                    'vusuregstd' => $return->vusuregstd,
                    'bcarstd' => $return->bcarstd ?? null,
                    'vobs' => $return->vobs,
                    'cflgest' => $return->cflgest,
                ]);
            }

            return response()->json([
                'result' => false,
                'message' => 'La consulta no encontró el documento.'
            ], 500);
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
                    if (env('APP_ENV') === 'local') {
                        return true;
                    }
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

        if (env('APP_ENV') === 'local' || env('APP_ENV') === 'staging') {
            $nginxIp = trim(shell_exec("getent hosts sgd-bridge-nginx | awk '{ print $1 }'"));
            $url = 'http://' . $nginxIp . '/wsiotramite/Tramite?wsdl';
        } else {
            $url = env('TRAMITE_WS');
        }

        $client = new \SoapClient($url, [
            'trace' => 1,
            'exceptions' => true
        ]);

        $rucEntidadEmisora = $request->vrucentrem;
        $vcuo = null;

        if (env('APP_ENV') === 'local') {
            $vcuo = '123456789';
        } elseif (env('APP_ENV') === 'staging') {
            $vcuo = $this->cuoService->getCuoTest($rucEntidadEmisora, "3011");
        } else {
            $vcuo = $this->cuoService->getCuoEntidad($rucEntidadEmisora, "3011");
        }

        try {
            $payload = [
                "request" => [
                    "vrucentrem" => $rucEntidadEmisora,
                    "vrucentrec" => $request->vrucentrec,
                    "vnomentemi" => $request->vnomentemi,
                    "vuniorgrem" => $request->vuniorgrem,
                    "vcuo" => $vcuo,
                    "vcuoref" => $request->vcuoref ?? '',
                    "ccodtipdoc" => $request->ccodtipdoc,
                    "vnumdoc" => $request->vnumdoc,
                    "dfecdoc" => $request->dfecdoc,
                    "vuniorgdst" => $request->vuniorgdst,
                    "vnomdst" => $request->vnomdst,
                    "vnomcardst" => $request->vnomcardst,
                    "vasu" => utf8_encode($request->vasu),
                    "snumanx" => $request->snumanx,
                    "snumfol" => $request->snumfol,
                    "bpdfdoc" => base64_encode(file_get_contents($request->file('bpdfdoc')->getRealPath())),
                    "vnomdoc" => $request->vnomdoc,
                    'lstanexos' => $request->lstanexos ?? [],
                    "vurldocanx" => $request->vurldocanx ?? '',
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
            ], 500);
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

    public function getTipo(Request $request)
    {
        $this->documentoService->getTipo($request->ccodtipdoc);
    }
}
