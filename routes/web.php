<?php

use Artisaninweb\SoapWrapper\SoapWrapper;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('test', function (SoapWrapper $soapWrapper) {
    $soapWrapper->add('EntidadWS', function ($service) {
        $service
            ->wsdl('http://mpv-iotd.gobiernodigital.gob.pe/wsentidad/Entidad?wsdl')
            ->trace(true);
    });

    $response = $soapWrapper->call('EntidadWS.getListaEntidad', ['sidcatent' => '1']);
    dd($response);
});
