<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EntidadController extends Controller
{
    public function getList(Request $request)
    {
        $url = "https://ws2.pide.gob.pe/Rest/Pcm/ListaEntidad?out=json";

        try {
            $payload = [
                "PIDE" => [
                    "sidcatent" => $request->sidcatent
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8'
            ])
                ->post($url, $payload);
            if ($response->successful()) {
                $data = $response->json();
                if ($data['getListaEntidadResponse']) {
                    return response()->json($data['getListaEntidadResponse']['return']);
                }
                return response()->json([
                    'error' => true,
                    'message' => 'No se encontraron entidades.'
                ], 400);
            }
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }

    public function validate(Request $request)
    {
        $url = 'https://ws2.pide.gob.pe/Rest/Pcm/ValidarEntidad?out=json';

        try {
            $payload = [
                "PIDE" => [
                    "vrucent" => $request->vrucent
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8'
            ])
                ->post($url, $payload);
            if ($response->successful()) {
                $data = $response->json();
                if ($data['validarEntidadResponse']['return'] === '0000') {
                    return response()->json([
                        'response' => true
                    ]);
                }
                return response()->json([
                    'response' => false
                ]);
            }
        } catch (\Throwable $th) {
            logger($th);
            throw $th;
        }
    }
}
