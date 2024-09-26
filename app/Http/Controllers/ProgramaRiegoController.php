<?php

namespace App\Http\Controllers;

use App\Models\ProgramaRiego;
use Illuminate\Http\Request;
use App\Models\ProgramaActual;

class ProgramaRiegoController extends Controller
{
    /**
     * Muestra una lista de todos los programas de riego.
     */
    public function index()
    {
        $programas = ProgramaRiego::all();
        $programaActual = ProgramaActual::first(); // Obtenemos el programa actual (si existe)
    
        return view('programas.index', compact('programas', 'programaActual'));
    }

    /**
     * Muestra el formulario para crear un nuevo programa de riego.
     */
    public function create()
    {
        return view('programas.create');
    }

    /**
     * Almacena un nuevo programa de riego en la base de datos.
     */
    public function store(Request $request)
    {

        ProgramaRiego::create($request->all());

        return redirect()->route('programa-riego.index')->with('success', 'Programa de riego creado con éxito.');
    }

    /**
     * Muestra los detalles de un programa de riego específico.
     */
    public function show(ProgramaRiego $programaRiego)
    {
        return view('programas.show', compact('programaRiego'));
    }

    /**
     * Muestra el formulario para editar un programa de riego específico.
     */
    public function edit(ProgramaRiego $programaRiego)
    {
        return view('programas.edit', compact('programaRiego'));
    }

    /**
     * Actualiza un programa de riego en la base de datos.
     */
    public function update(Request $request, ProgramaRiego $programa_riego)
    {
        $programa_riego->update($request->all());

        return redirect()->route('programa-riego.index')->with('success', 'Programa de riego actualizado con éxito.');
    }

    /**
     * Elimina un programa de riego de la base de datos.
     */
    public function destroy(ProgramaRiego $programa_riego)
    {
        $programa_riego->delete();

        return redirect()->route('programa-riego.index')->with('success', 'Programa de riego eliminado con éxito.');
    }

    public function setCurrent(Request $request)
    {
        $programaRiegoId = $request->input('programa_riego_id');

        // Validamos que el programa de riego existe
        $exists = ProgramaRiego::where('id', $programaRiegoId)->exists();

        if ($exists) {
            // Actualizamos o creamos el registro en programa_actual
            ProgramaActual::updateOrCreate(
                ['id' => 1], // Asumimos que siempre es el registro con ID 1
                ['programa_riego_id' => $programaRiegoId]
            );

            return redirect()->route('programa-riego.index')->with('success', 'Programa de Riego actualizado correctamente.');
        } else {
            return redirect()->route('programa-riego.index')->with('error', 'El Programa de Riego seleccionado no existe.');
        }
    }
}
