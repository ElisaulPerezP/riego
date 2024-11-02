<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\ProgramaActual;

class ProgramaRiegoApiController extends Controller
{
    /**
     * Muestra el programa actual de riego.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showProgramaActual(): JsonResponse
    {
        try {
            // Obtener el programa actual con su relación programaRiego
            $programaActual = ProgramaActual::with('programaRiego')->first();
            
            if (!$programaActual) {
                return response()->json(['error' => 'Programa de riego no encontrado'], 404);
            }
            
            return response()->json($programaActual, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener el programa de riego', 'message' => $e->getMessage()], 500);
        }
    }
}
