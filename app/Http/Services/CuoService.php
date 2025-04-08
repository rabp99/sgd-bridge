<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class CuoService
{
    public function getCuoTest($ruc, $servicio)
    {
        $url = "https://ws2.pide.gob.pe/Rest/PCM/CTest?out=json";

        try {
            $payload = [
                "PIDE" => [
                    "ruc" => $ruc,
                    "servicio" => $servicio,
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
