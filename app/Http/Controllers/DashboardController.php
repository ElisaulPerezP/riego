<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgramaActual;
use App\Models\ReporteRiego; // Asegúrate de importar el modelo ReporteRiego

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard con el programa actual de riego y el último reporte.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtener el programa actual con su relación ProgramaRiego
        $programaActual = ProgramaActual::with('programaRiego')->first();

        // Obtener el último reporte de riego, asumiendo que existe una columna 'created_at'
        $ultimoReporte = ReporteRiego::latest()->first();

        return view('dashboard', compact('programaActual', 'ultimoReporte'));
    }
}
