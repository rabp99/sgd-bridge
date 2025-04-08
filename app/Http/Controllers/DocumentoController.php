<?php

namespace App\Http\Controllers;

use App\Http\Services\CuoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Artisaninweb\SoapWrapper\SoapWrapper;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    protected SoapWrapper $soapWrapper;

    protected CuoService $cuoService;

    public function __construct(
        SoapWrapper $soapWrapper,
        CuoService $cuoService
    ) {
        $this->soapWrapper = $soapWrapper;
        $this->cuoService = $cuoService;
    }

    public function cargoTramite(Request $request) {}

    public function consultarTramite(Request $request) {}

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

    /*
    public function recepcionarTramite(Request $request) {
        $request->validate([
            'vrucentrem' => 'required|string',
            'vrucentrec' => 'required|string',
            'vnomentemi' => 'required|string',
            'vuniorgrem' => 'required|string',
            'vcuo' => 'required|string',
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
            'vnomdoc2' => 'nullable|string',
            'vurldocanx' => 'nullable|url',
            'ctipdociderem' => 'required|in:1,2',
            'vnumdociderem' => 'required|string',
        ]);

        // $url = "https://ws2.pide.gob.pe/Rest/Pcm/RecepcionarTramite?out=json";
        $url = "https://ws2.pide.gob.pe/Rest/Pcm/RecepcionarTramite?out=json";
        
        try {
            $payload = [
                "PIDE" => [
                    "vrucentrem" => $request->vrucentrem,
                    "vrucentrec" => $request->vrucentrec,
                    "vnomentemi" => $request->vnomentemi,
                    "vuniorgrem" => $request->vuniorgrem,
                    "vcuo" => $request->vcuo,
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
                    "vnomdoc2" => $request->vnomdoc2,
                    "vurldocanx" => $request->vurldocanx,
                    "ctipdociderem" => $request->ctipdociderem,
                    "vnumdociderem" => $request->vnumdociderem
                ]
            ];

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
    */

    public function testRecepcionarTramite(Request $request)
    {
        $url = "http://mpv-iotd.gobiernodigital.gob.pe/wsiopidetramite/IOTramite?wsdl";

        $client = new \SoapClient($url);

        $path = 'app/private/test.pdf';
        $fileContent = Storage::get($path);
        $base64 = base64_encode($fileContent);

        try {
            $payload = [
                "request" => [
                    "vrucentrem" => '20477412549',
                    "vrucentrec" => '20175639391',
                    "vnomentemi" => 'TRANSPORTES METROPOLITANOS DE TRUJILLO',
                    "vuniorgrem" => 'GERENCIA GENERAL',
                    "vcuo" => $this->cuoService->getCuoTest("20175639391", "1"),
                    "vcuoref" => "",
                    "ccodtipdoc" => '01',
                    "vnumdoc" => '1',
                    "dfecdoc" => '2025-04-04T15:21:48.857-05:00',
                    "vuniorgdst" => 'GERENCIA MUNICIPAL',
                    "vnomdst" => 'MARIO REYNA',
                    "vnomcardst" => 'ALCALDE',
                    "vasu" => 'OFICIO DE PRUEBA',
                    "snumanx" => 0,
                    "snumfol" => 1,
                    "bpdfdoc" => $base64,
                    "vnomdoc" => 'DOCUMENTO.pdf',
                    "vnomdoc2" => '',
                    "vurldocanx" => '',
                    "ctipdociderem" => '1',
                    "vnumdociderem" => '45415710'
                ]
            ];

            $response = $client->recepcionarTramiteResponse($payload);

            dd($response);
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }
}
