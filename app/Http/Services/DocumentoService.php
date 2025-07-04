<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class DocumentoService
{
    public function getTipos() {
        $url = env('TRAMITE_WS');

        $client = new \SoapClient($url, [
            'trace' => 1,
            'exceptions' => true
        ]);

        try {
            $response = $client->getTipoDocumento();
            $tipos = $response->return;
            if (count($tipos)) {
                return $tipos;
            }

            throw new \Exception('No se pudo obtener la lista de Entidades.');
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }

    /*
    public function getTipo(string $ccodtipdoc)
    {
        $url = "https://ws2.pide.gob.pe/Rest/Pcm/TipoDocumento?out=json";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8'
            ])
                ->post($url, null);
            if ($response->successful()) {
                $data = $response->json();
                $tipos = $data['getTipoDocumentoResponse']['return'];

                foreach ($tipos as $tipo) {
                    if (isset($tipo['ccodtipdoctra']) && $tipo['ccodtipdoctra'] === $ccodtipdoc) {
                        return $tipo['vnomtipdoctra'];
                    }
                }

                return null;
            }
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }
    */

    public function getTipo(string $ccodtipdoc) {
        try {
            $tipos = $this->getTipos();

            foreach ($tipos as $tipo) {
                if (isset($tipo->ccodtipdoctra) && $tipo->ccodtipdoctra === $ccodtipdoc) {
                    return $tipo->vnomtipdoctra;
                }
            }

            return null;
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }
}
