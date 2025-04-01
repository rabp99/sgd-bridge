<?php

use Artisaninweb\SoapWrapper\SoapWrapper;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;


Route::get('/', function () {
    return view('welcome');
});


Route::get('test', function () {
    $url = "https://ws2.pide.gob.pe/Rest/Pcm/ListaEntidad?sidcatent=01&out=json";

    try {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json; charset=UTF-8'
        ])
            ->timeout(300000)
            ->get($url);
        dd($response->body());
    } catch (\Throwable $e) {
        dd($e);
    }
});
/*
Route::get('test', function () {
    $wsdl = "https://ws2.pide.gob.pe/services/PcmIMgdEntidad?wsdl";

    try {
        $options = [
            'trace'    => 1, // Para obtener más detalles en la respuesta
            'exceptions' => true,
            'stream_context' => stream_context_create([
                'http' => [
                    'timeout' => 180 // Tiempo de espera en segundos
                ]
            ])
        ];
        $client = new \SoapClient($wsdl, $options);

        $response = $client->getListaEntidad(['sidcatent' => '01']);
        dd($response);
    } catch (\SoapFault $e) {
        dd($e);
    }
});
*/
/*
Route::get('test', function (SoapWrapper $soapWrapper) {
    try {
        $soapWrapper->add('Entidad', function ($service) {
            $service
                ->wsdl('http://mpv-iotd.gobiernodigital.gob.pe/wsentidad/Entidad?wsdl')
                ->trace(true)
                ->options([
                    'soap_version' => SOAP_1_2,
                    'exceptions' => true,
                    'cache_wsdl' => WSDL_CACHE_NONE,
                    'connection_timeout' => 30, // Asegurar tiempo de espera adecuado
                    'stream_context' => stream_context_create([
                        'http' => [
                            'user_agent' => 'PHPSoapClient',
                            'timeout' => 30
                        ]
                    ])
                ]);
        });

        $response = $soapWrapper->call('Entidad.getListaEntidad', ['sidcatent' => '1']);
        dd($response);
    } catch (\Throwable $th) {
        dd($th);
    }
});
*/
/*
Route::get('test', function (SoapWrapper $soapWrapper) {
    try {
        $client = new \SoapClient('http://mpv-iotd.gobiernodigital.gob.pe/wsentidad/Entidad?wsdl');
        dd($client->__getFunctions()); // Esto lista los métodos disponibles
    } catch (\SoapFault $e) {
        dd($e->getMessage());
    }
});
*/