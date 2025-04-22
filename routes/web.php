<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SOAP\SoapServerController;
use App\Http\Middleware\RemoveHeaders;


Route::any('/wsiotramite/Tramite', [SoapServerController::class, 'handle']);
