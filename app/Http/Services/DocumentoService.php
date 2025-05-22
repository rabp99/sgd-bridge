<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class DocumentoService
{
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
}
