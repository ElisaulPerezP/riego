<?php

namespace App\Http\Controllers\Api;

use App\Models\ReporteRiego;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReporteRiegoApiController extends Controller
{
    /**
     * Muestra una lista de todos los reportes de riego.
     */
    public function index()
    {
        $reportes = ReporteRiego::all();
        return response()->json($reportes, 200); // Retorna JSON con los reportes
    }

    /**
     * Almacena un nuevo reporte de riego en la base de datos.
     */
    public function store(Request $request)
    {
    // Validamos los datos recibidos
    $request->validate([
        // Validación para los 14 campos de volumen
        'volumen1' => 'required|numeric',
        'volumen2' => 'required|numeric',
        'volumen3' => 'required|numeric',
        'volumen4' => 'required|numeric',
        'volumen5' => 'required|numeric',
        'volumen6' => 'required|numeric',
        'volumen7' => 'required|numeric',
        'volumen8' => 'required|numeric',
        'volumen9' => 'required|numeric',
        'volumen10' => 'required|numeric',
        'volumen11' => 'required|numeric',
        'volumen12' => 'required|numeric',
        'volumen13' => 'required|numeric',
        'volumen14' => 'required|numeric',

        // Validación para los 14 campos de tiempo (con formato HH:mm)
        'tiempo1' => 'required|date_format:H:i:s',
        'tiempo2' => 'required|date_format:H:i:s',
        'tiempo3' => 'required|date_format:H:i:s',
        'tiempo4' => 'required|date_format:H:i:s',
        'tiempo5' => 'required|date_format:H:i:s',
        'tiempo6' => 'required|date_format:H:i:s',
        'tiempo7' => 'required|date_format:H:i:s',
        'tiempo8' => 'required|date_format:H:i:s',
        'tiempo9' => 'required|date_format:H:i:s',
        'tiempo10' => 'required|date_format:H:i:s',
        'tiempo11' => 'required|date_format:H:i:s',
        'tiempo12' => 'required|date_format:H:i:s',
        'tiempo13' => 'required|date_format:H:i:s',
        'tiempo14' => 'required|date_format:H:i:s',

        // Validación para los 14 campos de mensaje (puede ser nulo y debe ser una cadena si está presente)
        'mensaje1' => 'nullable|string',
        'mensaje2' => 'nullable|string',
        'mensaje3' => 'nullable|string',
        'mensaje4' => 'nullable|string',
        'mensaje5' => 'nullable|string',
        'mensaje6' => 'nullable|string',
        'mensaje7' => 'nullable|string',
        'mensaje8' => 'nullable|string',
        'mensaje9' => 'nullable|string',
        'mensaje10' => 'nullable|string',
        'mensaje11' => 'nullable|string',
        'mensaje12' => 'nullable|string',
        'mensaje13' => 'nullable|string',
        'mensaje14' => 'nullable|string',
    ]);


        // Creamos el reporte de riego
        $reporte = ReporteRiego::create($request->all());

        return response()->json($reporte, 201); // Retorna el reporte creado con código 201
    }

    /**
     * Muestra un reporte de riego específico.
     */
    public function show(ReporteRiego $reporte)
    {
        return response()->json($reporte, 200); // Retorna el reporte en formato JSON
    }

    /**
     * Actualiza un reporte de riego específico.
     */
    public function update(Request $request, ReporteRiego $reporte)
    {

    // Validamos los datos recibidos
    $request->validate([
        // Validación para los 14 campos de volumen
        'volumen1' => 'nullable|numeric',
        'volumen2' => 'nullable|numeric',
        'volumen3' => 'nullable|numeric',
        'volumen4' => 'nullable|numeric',
        'volumen5' => 'nullable|numeric',
        'volumen6' => 'nullable|numeric',
        'volumen7' => 'nullable|numeric',
        'volumen8' => 'nullable|numeric',
        'volumen9' => 'nullable|numeric',
        'volumen10' => 'nullable|numeric',
        'volumen11' => 'nullable|numeric',
        'volumen12' => 'nullable|numeric',
        'volumen13' => 'nullable|numeric',
        'volumen14' => 'nullable|numeric',

        // Validación para los 14 campos de tiempo (con formato HH:mm)
        'tiempo1' => 'nullable|date_format:H:i:s',
        'tiempo2' => 'nullable|date_format:H:i:s',
        'tiempo3' => 'nullable|date_format:H:i:s',
        'tiempo4' => 'nullable|date_format:H:i:s',
        'tiempo5' => 'nullable|date_format:H:i:s',
        'tiempo6' => 'nullable|date_format:H:i:s',
        'tiempo7' => 'nullable|date_format:H:i:s',
        'tiempo8' => 'nullable|date_format:H:i:s',
        'tiempo9' => 'nullable|date_format:H:i:s',
        'tiempo10' => 'nullable|date_format:H:i:s',
        'tiempo11' => 'nullable|date_format:H:i:s',
        'tiempo12' => 'nullable|date_format:H:i:s',
        'tiempo13' => 'nullable|date_format:H:i:s',
        'tiempo14' => 'nullable|date_format:H:i:s',

        // Validación para los 14 campos de mensaje (puede ser nulo y debe ser una cadena si está presente)
        'mensaje1' => 'nullable|string',
        'mensaje2' => 'nullable|string',
        'mensaje3' => 'nullable|string',
        'mensaje4' => 'nullable|string',
        'mensaje5' => 'nullable|string',
        'mensaje6' => 'nullable|string',
        'mensaje7' => 'nullable|string',
        'mensaje8' => 'nullable|string',
        'mensaje9' => 'nullable|string',
        'mensaje10' => 'nullable|string',
        'mensaje11' => 'nullable|string',
        'mensaje12' => 'nullable|string',
        'mensaje13' => 'nullable|string',
        'mensaje14' => 'nullable|string',
    ]);
        // Actualizamos el reporte de riego
        $reporte->update($request->only([
            'volumen1', 'volumen2', 'volumen3', 'volumen4', 'volumen5', 'volumen6', 'volumen7',
            'volumen8', 'volumen9', 'volumen10', 'volumen11', 'volumen12', 'volumen13', 'volumen14',
            'tiempo1', 'tiempo2', 'tiempo3', 'tiempo4', 'tiempo5', 'tiempo6', 'tiempo7',
            'tiempo8', 'tiempo9', 'tiempo10', 'tiempo11', 'tiempo12', 'tiempo13', 'tiempo14',
            'mensaje1', 'mensaje2', 'mensaje3', 'mensaje4', 'mensaje5', 'mensaje6', 'mensaje7',
            'mensaje8', 'mensaje9', 'mensaje10', 'mensaje11', 'mensaje12', 'mensaje13', 'mensaje14',
        ]));

        return response()->json($reporte, 200); // Retorna el reporte actualizado
    }

    /**
     * Elimina un reporte de riego específico.
     */
    public function destroy(ReporteRiego $reporte)
    {
        $reporte->delete();

        return response()->json(null, 204); // Retorna un código 204 (sin contenido)
    }
}
