<?php

namespace App\Http\Controllers\Web;

use App\Models\ReporteRiego;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReporteRiegoController extends Controller
{
    public function index()
    {
        $reportes = ReporteRiego::all();
        return view('reporteRiego.index', compact('reportes')); // Retorna la vista con los reportes
    }

    public function create()
    {
        return view('reporteRiego.create'); // Retorna el formulario de creación
    }

    public function store(Request $request)
    {
        $request->validate([
            'volumen1' => 'required|numeric',
            'tiempo1' => 'required|date_format:H:i:s',
            'mensaje1' => 'nullable|string',
            // Validación para los 14 surcos...
        ]);

        ReporteRiego::create($request->all());

        return redirect()->route('reportes.index')->with('success', 'Reporte de riego creado.');
    }

    public function show(ReporteRiego $reporteRiego)
    {
        return view('reporteRiego.show', compact('reporteRiego')); // Muestra los detalles del reporte
    }

    public function edit(ReporteRiego $reporteRiego)
    {
        return view('reporteRiego.edit', compact('reporteRiego')); // Retorna el formulario de edición
    }

    public function update(Request $request, ReporteRiego $reporteRiego)
    {

        $reporteRiego->update($request->all());

        return redirect()->route('reportes.index')->with('success', 'Reporte de riego actualizado.');
    }

    public function destroy(ReporteRiego $reporteRiego)
    {
        $reporteRiego->delete();

        return redirect()->route('reportes.index')->with('success', 'Reporte de riego eliminado.');
    }
}
