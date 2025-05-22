<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\REST\SignatureController;
use App\Http\Controllers\REST\EntidadController;
use App\Http\Controllers\REST\CuoController;
use App\Http\Controllers\REST\DocumentoController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/sign', [SignatureController::class, 'sign'])->name('sign');
Route::post('/sign-cargo', [SignatureController::class, 'signCargo'])->name('sign-cargo');

Route::get('/entidad/get-list/{sidcatent}', [EntidadController::class, 'getList'])->name('entidad.get-list');

Route::get('/entidad/validate/{vrucent}', [EntidadController::class, 'validate'])->name('entidad.get-list');

Route::get('/cuo/get-cuo-test/{ruc}/{servicio}', [CuoController::class, 'getCuoTest'])->name('cuo.get-cuo-test');

Route::get('/cuo/get-cuo-entidad/{ruc}/{servicio}', [CuoController::class, 'getCuoEntidad'])->name('cuo.get-cuo-entidad');

Route::post('/documentos/cargo-tramite', [DocumentoController::class, 'cargoTramite'])->name('documento.cargo-tramite');

Route::post('/documentos/consultar-tramite', [DocumentoController::class, 'consultarTramite'])->name('cuo.get-cuo-entidad');

Route::get('/documento/tipo-documentos', [DocumentoController::class, 'getTipos'])->name('documento.get-tipos');
Route::get('/documento/tipo-documento/{ccodtipdoc}', [DocumentoController::class, 'getTipo'])->name('documento.get-tipo');

Route::post('/documentos/recepcionar-tramite', [DocumentoController::class, 'recepcionarTramite'])->name('documento.recepcionar-tramite');
