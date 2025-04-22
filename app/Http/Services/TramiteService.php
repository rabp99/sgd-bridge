<?php

namespace App\Http\Services;

use App\Soap\Types\RespuestaConsultaTramite;
use App\Soap\Types\RespuestaTramite;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TramiteService
{

    public function cargoResponse($params)
    {
        return [
            'return' => [
                'vcodres' => '0000',
                'vdesres' => 'Cargo recibido correctamente'
            ]
        ];
    }

    public function consultarTramiteResponse($params)
    {
        $responseData = new RespuestaConsultaTramite();
        $responseData->return->vcodres = '0001';

        try {
            $api = env('SGD_API_URL') . '?action=consultarTramite';
            $response = Http::post($api, $params->request);

            if ($response->ok()) {
                $data = $response->json();
                if ($data['result']) {
                    $responseData->return->vcodres = '0000';
                    $responseData->return->vdesres = 'DATOS ENCONTRADOS';
                    $responseData->return->vcuo = $params->request->vcuo;
                    $responseData->return->vcuoref = $data['vcuoref'];
                    $responseData->return->vnumregstd = $data['vnumregstd'];
                    $responseData->return->vanioregstd = $data['vanioregstd'];
                    $responseData->return->vuniorgstd = $data['vuniorgstd'] ?? '';
                    $responseData->return->dfecregstd = $data['dfecregstd'];
                    $responseData->return->vusuregstd = $data['vusuregstd'];
                    $responseData->return->bcarstd = $data['bcarstd'];
                    $responseData->return->vobs = $data['vobs'] ?? '';
                    $responseData->return->cflgest = $data['cflgest'];
                }

                return $responseData;
            }

            $responseData->return->vdesres = 'DATOS NO ENCONTRADOS';
            return $responseData;
        } catch (\Throwable $th) {
            logger($th);
            $responseData->return->vcodres = '-1';
            return $responseData;
        }
    }

    public function recepcionarTramiteResponse($params)
    {
        $responseData = new RespuestaTramite();
        $responseData->return->vcodres = '-1';

        try {
            $webhook = env('SGD_WEBHOOK_URL') . '?action=recepcionarTramite';
            $response = Http::post($webhook, $params->request);
            logger($response->body());
            if ($response->ok()) {
                $vcuo = $params->request->vcuo;
                $entidad = env('SGD_ENTIDAD');

                $responseData->return->vcodres = '0000';
                $responseData->return->vdesres = "El documento N° CUO $vcuo se encuentra a disposición para la recepción formal de la entidad destinataria $entidad en los horarios de atención de su Mesa de Partes.";
                return $responseData;
            }
            return $responseData;
        } catch (\Throwable $th) {
            logger($th);
            return $responseData;
        }
    }
}
