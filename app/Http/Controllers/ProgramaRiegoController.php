<?php

namespace App\Http\Controllers;

use App\Models\ProgramaRiego;
use Illuminate\Http\Request;

class ProgramaRiegoController extends Controller
{
    /**
     * Muestra una lista de todos los programas de riego.
     */
    public function index()
    {
        $programas = ProgramaRiego::all();
        return view('programas.index', compact('programas'));
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
        $request->validate([
            'veces_por_dia' => 'required|integer',
            // Validaciones para los campos de volumen
            'volumen1' => 'required|integer',
            'volumen2' => 'required|integer',
            // ...
            'volumen14' => 'required|integer',
            // Validaciones para los campos de fertilizantes
            'fertilizante1_1' => 'required|integer',
            'fertilizante1_2' => 'required|integer',
            // ...
            'fertilizante2_14' => 'required|integer',
        ]);

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
        $request->validate([
            'veces_por_dia' => 'required|integer',
            // Validaciones para los campos de volumen
            'volumen1' => 'required|integer',
            'volumen2' => 'required|integer',
            // ...
            'volumen14' => 'required|integer',
            // Validaciones para los campos de fertilizantes
            'fertilizante1_1' => 'required|integer',
            'fertilizante1_2' => 'required|integer',
            // ...
            'fertilizante2_14' => 'required|integer',
        ]);

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
}
