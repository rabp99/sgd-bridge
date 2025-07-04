<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class CuoService
{
    public function getCuoTest($ip)
    {
        $url = env('CUO_WS');

        $client = new \SoapClient($url, [
            'trace' => 1,
            'exceptions' => true
        ]);

        try {
            $response = $client->getCuo($ip);
            $return = $response->return;

            dd($return);

            /*
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
            */
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }

    public function getCuoEntidad($ruc, $servicio)
    {
        /*
        $url = env('CUO_WS');

        $client = new \SoapClient($url, [
            'trace' => 1,
            'exceptions' => true
        ]);

        try {
            $payload = [
                "ruc" => $ruc,
                "servicio" => $servicio,
            ];

            $response = $client->getCuoEntidad($payload);
            $return = $response->return;

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
        */
    }
}
