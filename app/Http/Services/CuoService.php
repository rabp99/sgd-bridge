<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class CuoService
{
    public function getCuoTest($ruc, $servicio)
    {
        $url = env('CUO_WS');

        ini_set('default_socket_timeout', 600);
        $client = new \SoapClient($url, [
            'trace' => 1,
            'exceptions' => true,
            'connection_timeout' => 600
        ]);

        try {
            $payload = [
                "ruc" => $ruc,
                "servicio" => $servicio,
            ];

            $response = $client->getCUO($payload);
            $return = $response->return;
            if ($return) {
                return $return;
            }

            throw new \Exception('No se pudo obtener el CUO.');
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }
    public function getCuoEntidad($ruc, $servicio)
    {
        $url = env('CUO_WS');

        ini_set('default_socket_timeout', 600);
        $client = new \SoapClient($url, [
            'trace' => 1,
            'exceptions' => true,
            'connection_timeout' => 600
        ]);

        try {
            $payload = [
                "ruc" => $ruc,
                "servicio" => $servicio,
            ];

            $response = $client->getCUOEntidad($payload);
            $return = $response->return;
            if ($return) {
                return $return;
            }

            throw new \Exception('No se pudo obtener el CUO.');
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }
}
