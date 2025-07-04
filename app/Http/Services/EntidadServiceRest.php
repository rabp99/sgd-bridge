<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class EntidadServiceRest
{
    public function getList($sidcatent)
    {
        $url = "https://ws2.pide.gob.pe/Rest/Pcm/ListaEntidad?out=json";

        try {
            $payload = [
                "PIDE" => [
                    "sidcatent" => $sidcatent
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8'
            ])
                ->post($url, $payload);
            if ($response->successful()) {
                $data = $response->json();
                if ($data['getListaEntidadResponse']) {
                    return $data['getListaEntidadResponse']['return'];
                }
                throw new \Exception('No se pudo obtener la lista de Entidades.');
            }
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }

    public function validate($vrucent)
    {
        $url = 'https://ws2.pide.gob.pe/Rest/Pcm/ValidarEntidad?out=json';

        try {
            $payload = [
                "PIDE" => [
                    "vrucent" => $vrucent
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8'
            ])
                ->post($url, $payload);
            if ($response->successful()) {
                $data = $response->json();
                if ($data['validarEntidadResponse']['return'] === '0000') {
                    return true;
                }
                return false;
            }
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }
}
