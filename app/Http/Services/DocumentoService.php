<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class DocumentoService
{
    public function getTipos() {
        $url = env('TRAMITE_WS');

        ini_set('default_socket_timeout', 600);
        $client = new \SoapClient($url, [
            'trace' => 1,
            'exceptions' => true,
            'connection_timeout' => 600
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
