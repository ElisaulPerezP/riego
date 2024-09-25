<?php

namespace App\Http\Controllers;

use App\Models\Aspercion;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\Request;

class AspercionController extends Controller
{
    /**
     * Mostrar una lista de las asperciones.
     */
    public function index()
    {
        $asperciones = Aspercion::with('productos', 'user')->get();
        return view('asperciones.index', compact('asperciones'));
    }

    /**
     * Mostrar el formulario para crear una nueva asperción.
     */
    public function create()
    {
        $productos = Producto::all();
        $usuarios = User::all(); // Para seleccionar el usuario responsable
        return view('asperciones.create', compact('productos', 'usuarios'));
    }

    /**
     * Almacenar una nueva asperción en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'volumen' => 'required|numeric',
            'tipo_aspercion' => 'required|string|max:255',
            'responsable' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'productos' => 'required|array',
        ]);

        $aspercion = Aspercion::create($request->except('productos'));

        $aspercion->productos()->attach($request->productos);

        return redirect()->route('aspercion.index')->with('success', 'Asperción creada correctamente.');
    }

    /**
     * Mostrar el detalle de una asperción.
     */
    public function show(Aspercion $aspercion)
    {
        return view('asperciones.show', compact('aspercion'));
    }

    /**
     * Mostrar el formulario para editar una asperción.
     */
    public function edit(Aspercion $aspercion)
    {
        $productos = Producto::all();
        $usuarios = User::all();
        return view('asperciones.edit', compact('aspercion', 'productos', 'usuarios'));
    }

    /**
     * Actualizar una asperción en la base de datos.
     */
    public function update(Request $request, Aspercion $aspercion)
    {
        $request->validate([
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'volumen' => 'required|numeric',
            'tipo_aspercion' => 'required|string|max:255',
            'responsable' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'productos' => 'required|array',
        ]);

        $aspercion->update($request->except('productos'));
        $aspercion->productos()->sync($request->productos);

        return redirect()->route('aspercion.index')->with('success', 'Asperción actualizada correctamente.');
    }

    /**
     * Eliminar una asperción de la base de datos.
     */
    public function destroy(Aspercion $aspercion)
    {
        $aspercion->productos()->detach();
        $aspercion->delete();

        return redirect()->route('aspercion.index')->with('success', 'Asperción eliminada correctamente.');
    }
}
