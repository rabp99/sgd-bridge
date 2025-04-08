<?php

namespace App\Http\Controllers;

use App\Http\Services\CuoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CuoController extends Controller
{
    protected CuoService $cuoService;

    public function __construct(CuoService $cuoService)
    {
        $this->cuoService = $cuoService;
    }

    public function getCuoTest(Request $request)
    {
        try {
            $cuo = $this->cuoService->getCuoTest($request->ruc, $request->servicio);
            return response()->json(['cuo' => $cuo]);
        } catch (\Throwable $th) {
            logger($th);
            return response()->json([
                'error' => true,
                'message' => 'No se pudo obtener el CUO.'
            ], 400);
        }
    }

    public function getCuoEntidad(Request $request)
    {
        try {
            $cuo = $this->cuoService->getCuoEntidad($request->ruc, $request->servicio);
            return response()->json(['cuo' => $cuo]);
        } catch (\Throwable $th) {
            logger($th);
            return response()->json([
                'error' => true,
                'message' => 'No se pudo obtener el CUO.'
            ], 400);
        }
    }
}
