<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CuoController extends Controller
{
    public function getCuoTest(Request $request)
    {
        $url = "https://ws2.pide.gob.pe/Rest/PCM/CTest?out=json";

        try {
            $payload = [
                "PIDE" => [
                    "ruc" => $request->ruc,
                    "servicio" => $request->servicio,
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8'
            ])
                ->post($url, $payload);
            if ($response->successful()) {
                $data = $response->json();
                if ($cuo = $data['getCUOResponse']['return']['$']) {
                    return response()->json([
                        'cuo' => $cuo
                    ]);
                }
                return response()->json([
                    'error' => true,
                    'message' => 'No se pudo obtener el CUO.'
                ], 400);
            }
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }

    public function getCuoEntidad(Request $request)
    {
        $url = "https://ws2.pide.gob.pe/Rest/PCM/CEntidad?out=json";

        try {
            $payload = [
                "PIDE" => [
                    "ruc" => $request->ruc,
                    "servicio" => $request->servicio,
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8'
            ])
                ->post($url, $payload);
            if ($response->successful()) {
                $data = $response->json();
                if ($cuo = $data['getCUOResponse']['return']['$']) {
                    return response()->json([
                        'cuo' => $cuo
                    ]);
                }
                return response()->json([
                    'error' => true,
                    'message' => 'No se pudo obtener el CUO.'
                ], 400);
            }
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }
}
