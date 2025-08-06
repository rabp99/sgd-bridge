<?php

namespace App\Http\Services;

use App\Soap\Types\RespuestaConsultaTramite;
use App\Soap\Types\RespuestaCargoTramite;
use App\Soap\Types\RespuestaTramite;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecepcionarTramiteMail;

class TramiteService
{
    public function cargoResponse($params)
    {
        $responseData = new RespuestaCargoTramite();
        $responseData->return->vcodres = '0001';

        try {
            $api = env('SGD_WEBHOOK_URL') . '?action=cargoTramite';
            $response = Http::post($api, $params->cargoRequest);

            if ($response->ok()) {
                $data = $response->json();
                if ($data['result']) {
                    $responseData->return->vcodres = '0000';
                    $responseData->return->vdesres = 'RECEPCIÓN DE CARGO EXITOSO';
                }

                return $responseData;
            }

            $responseData->return->vdesres = 'RECEPCIÓN DE CARGO CON ERROR';
            return $responseData;
        } catch (\Throwable $th) {
            logger($th);
            $responseData->return->vcodres = '-1';
            return $responseData;
        }
    }

    public function consultarTramiteResponse($params)
    {
        $responseData = new RespuestaConsultaTramite();
        $responseData->return->vcodres = '0001';

        try {
            $vcuo = $params->request;
            $api = env('SGD_API_URL') . '?action=consultarTramite';
            $response = Http::post($api, ['vcuo' => $vcuo]);

            if ($response->ok()) {
                $data = $response->json();
                if ($data['result']) {
                    $responseData->return->vcodres = '0000';
                    $responseData->return->vdesres = 'DATOS ENCONTRADOS';
                    $responseData->return->vcuo = $vcuo;
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
            logger($webhook);
            logger(json_encode($params->recepcionRequest));
            $response = Http::post($webhook, $params->recepcionRequest);
            if ($response->ok()) {
                $vcuo = $params->recepcionRequest->vcuo;
                $entidad = env('SGD_ENTIDAD');

                $responseData->return->vcodres = '0000';
                $responseData->return->vdesres = "El documento N° CUO $vcuo se encuentra a disposición para la recepción formal de la entidad destinataria $entidad en los horarios de atención de su Mesa de Partes.";

                $this->sendMail($params->recepcionRequest);

                return $responseData;
            }

            return $responseData;
        } catch (\Throwable $th) {
            logger($th);
            return $responseData;
        }
    }

    private function sendMail($data)
    {
        Mail::to(env('RECEPCIONAR_TRAMITE_RECIPIENT'))
            ->send(new RecepcionarTramiteMail($data));
    }
}
