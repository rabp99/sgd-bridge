<?php

namespace App\Http\Controllers\REST;

use Illuminate\Http\Request;
use App\Http\Services\EntidadService;
use App\Http\Controllers\Controller;

class EntidadController extends Controller
{
    protected EntidadService $entidadService;

    public function __construct(EntidadService $entidadService)
    {
        $this->entidadService = $entidadService;
    }

    public function getList(Request $request)
    {
        try {
            $list = $this->entidadService->getList($request->sidcatent);
            return response()->json(compact('list'));
        } catch (\Throwable $th) {
            logger($th);
            return response()->json([
                'error' => true,
                'message' => 'No se pudo obtener la lista.'
            ], 400);
        }
    }

    public function validate(Request $request)
    {
        try {
            $result = $this->entidadService->validate($request->vrucent);
            return response()->json(compact('result'));
        } catch (\Throwable $th) {
            logger($th);
            return response()->json([
                'error' => true,
                'message' => 'No se pudo validar el RUC.'
            ], 400);
        }
    }
}
