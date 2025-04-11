<?php

use Artisaninweb\SoapWrapper\SoapWrapper;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\EntidadController;
use App\Http\Controllers\CuoController;
use App\Http\Controllers\DocumentoController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/sign', [SignatureController::class, 'sign'])->name('sign');

Route::get('/entidad/get-list/{sidcatent}', [EntidadController::class, 'getList'])->name('entidad.get-list');

Route::get('/entidad/validate/{vrucent}', [EntidadController::class, 'validate'])->name('entidad.get-list');

Route::get('/cuo/get-cuo-test/{ruc}/{servicio}', [CuoController::class, 'getCuoTest'])->name('cuo.get-cuo-test');

Route::get('/cuo/get-cuo-entidad/{ruc}/{servicio}', [CuoController::class, 'getCuoEntidad'])->name('cuo.get-cuo-entidad');

// Route::post('/documentos/cargo-tramite', [DocumentoController::class, 'cargoTramite'])->name('documento.cargo-tramite');

// Route::get('/documentos/consultar-tramite', [CuoController::class, 'getCuoEntidad'])->name('cuo.get-cuo-entidad');

Route::get('/documento/tipo-documento', [DocumentoController::class, 'getTipos'])->name('documento.get-tipos');

Route::post('/documentos/recepcionar-tramite', [DocumentoController::class, 'recepcionarTramite'])->name('documento.recepcionar-tramite');

Route::get('/documentos/test-recepcionar-tramite', [DocumentoController::class, 'testRecepcionarTramite'])->name('documento.test-recepcionar-tramite');
