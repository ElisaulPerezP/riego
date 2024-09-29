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
        // Verificar si la imagen generada existe en el almacenamiento público
        $image_path = storage_path('app/public/graphs/graficas_reporte_riego.png');
        $public_image_url = null;

        if (file_exists($image_path)) {
            // Si la imagen existe, generar la URL pública
            $public_image_url = asset('storage/graphs/graficas_reporte_riego.png');
        }
        // Pasar la URL de la imagen a la vista del dashboard, si existe
        return view('dashboard', compact('programaActual', 'ultimoReporte', 'public_image_url'));
    }

}
