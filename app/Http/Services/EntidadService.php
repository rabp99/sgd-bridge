<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class EntidadService
{
    public function getList($sidcatent)
    {
        $url = env('ENTIDAD_WS');

        ini_set('default_socket_timeout', 600);
        $client = new \SoapClient($url, [
            'trace' => 1,
            'exceptions' => true,
            'connection_timeout' => 600
        ]);

        try {
            $payload = [
                "sidcatent" => $sidcatent
            ];
            $response = $client->getListaEntidad($payload);
            if ($response->return) {
                if (is_array($response->return)) {
                    return $response->return;
                } else {
                    return [$response->return];
                }
            }

            throw new \Exception('No se pudo obtener la lista de Entidades.');
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }

    public function validate($vrucent)
    {
        $url = env('ENTIDAD_WS');

        ini_set('default_socket_timeout', 600);
        $client = new \SoapClient($url, [
            'trace' => 1,
            'exceptions' => true,
            'connection_timeout' => 600
        ]);

        try {
            $payload = [
                "vrucent" => $vrucent
            ];

            $response = $client->validarEntidad($payload);
            return $response->return === '0000';
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }
}
