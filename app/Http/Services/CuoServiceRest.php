<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class CuoServiceRest
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
                    return $cuo;
                }

                throw new \Exception('No se pudo obtener el CUO.');
            }
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }

    public function getCuoEntidad($ruc, $servicio)
    {
        $url = "https://ws2.pide.gob.pe/Rest/PCM/CEntidad?out=json";

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
                if ($cuo = $data['getCUOEntidadResponse']['return']['$']) {
                    return $cuo;
                }

                throw new \Exception('No se pudo obtener el CUO.');
            }
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }
}
