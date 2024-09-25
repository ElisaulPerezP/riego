<?php

namespace App\Http\Controllers;

use App\Models\Tratamiento;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\Request;

class TratamientoController extends Controller
{
    /**
     * Mostrar una lista de los tratamientos.
     */
    public function index()
    {
        $tratamientos = Tratamiento::with('productos', 'user')->get();
        return view('tratamientos.index', compact('tratamientos'));
    }

    
    /**
     * Mostrar el formulario para crear un nuevo tratamiento.
     */
    public function create()
    {
        $productos = Producto::all();
        $usuarios = User::all(); // Para seleccionar el agrónomo responsable
        return view('tratamientos.create', compact('productos', 'usuarios'));
    }

    /**
     * Almacenar un nuevo tratamiento en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'agronomo' => 'required|string|max:255',
            'cantidad' => 'required|numeric|min:0',
            'frecuencia' => 'required|string|max:255',
            'diagnostico' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'productos' => 'required|array',
        ]);

        $tratamiento = Tratamiento::create($request->except('productos'));

        // Asociar los productos seleccionados
        $tratamiento->productos()->attach($request->productos);

        return redirect()->route('tratamiento.index')->with('success', 'Tratamiento creado correctamente.');
    }

    /**
     * Mostrar el detalle de un tratamiento.
     */
    public function show(Tratamiento $tratamiento)
    {
        return view('tratamientos.show', compact('tratamiento'));
    }

    /**
     * Mostrar el formulario para editar un tratamiento.
     */
    public function edit(Tratamiento $tratamiento)
    {
        $productos = Producto::all();
        $usuarios = User::all(); // Obtener usuarios para seleccionar el responsable
        return view('tratamientos.edit', compact('tratamiento', 'productos', 'usuarios'));
    }

    /**
     * Actualizar un tratamiento en la base de datos.
     */
    public function update(Request $request, Tratamiento $tratamiento)
    {
        $request->validate([
            'agronomo' => 'required|string|max:255',
            'cantidad' => 'required|numeric|min:0',
            'frecuencia' => 'required|string|max:255',
            'diagnostico' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'productos' => 'required|array',
        ]);

        $tratamiento->update($request->except('productos'));
        $tratamiento->productos()->sync($request->productos);

        return redirect()->route('tratamiento.index')->with('success', 'Tratamiento actualizado correctamente.');
    }

    /**
     * Eliminar un tratamiento de la base de datos.
     */
    public function destroy(Tratamiento $tratamiento)
    {
        $tratamiento->productos()->detach();
        $tratamiento->delete();

        return redirect()->route('tratamiento.index')->with('success', 'Tratamiento eliminado correctamente.');
    }
}
