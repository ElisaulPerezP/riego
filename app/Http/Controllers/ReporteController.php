<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use Illuminate\Http\Request;
use App\Models\ReporteRiego;


class ReporteController extends Controller
{

    public function createGraph()
    {
        // Obtener los últimos reportes de riego
        $reportes = ReporteRiego::latest()->take(10)->get()->toJson();

        // Definir la ruta de salida para las imágenes
        $output_path = storage_path('app/public/graphs');
        $image_file = $output_path . '/graficas_reporte_riego.png';


        // Verificar si la imagen existe y eliminarla si es así
        if (file_exists($image_file)) {
            unlink($image_file);
        }
        $python_script = base_path('resources/py/graficator.py');
        // Ejecutar el script de Python, capturando también los errores
        $command = "python3 {$python_script} '{$reportes}' '{$output_path}' 2>&1";
        //dd($command);
        $output = shell_exec($command);
        //dd($output);
        // Ver el resultado de la ejecución para depurar si es necesario (descomenta para pruebas)
        // dd($output);
    
        // Verificar si la imagen se generó correctamente
        if (file_exists($image_file)) {
            // Si la imagen fue creada correctamente, redirigir al dashboard
            return redirect()->route('dashboard')->with('success', 'Gráfica generada exitosamente.');
        } else {
            // Si hubo algún problema, redirigir con un mensaje de error
            return redirect()->route('dashboard')->with('error', 'Error al generar la gráfica.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Reporte $reporte)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reporte $reporte)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reporte $reporte)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reporte $reporte)
    {
        //
    }
}
