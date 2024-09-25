<?php

namespace App\Http\Controllers;

use App\Models\Cosecha;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\Request;

class CosechaController extends Controller
{
    /**
     * Mostrar la lista de cosechas.
     */
    public function index()
    {
        $cosechas = Cosecha::with('productos', 'user')->get();
        return view('cosechas.index', compact('cosechas'));
    }

    /**
     * Mostrar el formulario para crear una nueva cosecha.
     */
    public function create()
    {
        $usuarios = User::all(); // Para seleccionar el usuario responsable
        return view('cosechas.create', compact('usuarios'));
    }

    /**
     * Almacenar una nueva cosecha en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'cantidad' => 'required|numeric|min:0',
            'porcentaje' => 'required|numeric|min:0|max:100',
            'cajas125' => 'required|integer|min:0',
            'cajas250' => 'required|integer|min:0',
            'cajas500' => 'required|integer|min:0',
            'user_id' => 'required|exists:users,id',
        ]);

        $cosecha = Cosecha::create($request->except(['cajas125', 'cajas250', 'cajas500']));

        // Asignar los valores de las cajas
        $cosecha->cajas125 = $request->input('cajas125');
        $cosecha->cajas250 = $request->input('cajas250');
        $cosecha->cajas500 = $request->input('cajas500');
        $cosecha->save();

        // Aquí irían los cálculos para productos y códigos

        return redirect()->route('cosecha.index')->with('success', 'Cosecha creada correctamente.');
    }

    /**
     * Mostrar el detalle de una cosecha.
     */
    public function show(Cosecha $cosecha)
    {
        return view('cosechas.show', compact('cosecha'));
    }

    /**
     * Mostrar el formulario para editar una cosecha.
     */
    public function edit(Cosecha $cosecha)
    {
        $usuarios = User::all(); // Obtener usuarios para seleccionar el responsable
        return view('cosechas.edit', compact('cosecha', 'usuarios'));
    }

    /**
     * Actualizar una cosecha en la base de datos.
     */
    public function update(Request $request, Cosecha $cosecha)
    {
        $request->validate([
            'fecha' => 'required|date',
            'cantidad' => 'required|numeric|min:0',
            'porcentaje' => 'required|numeric|min:0|max:100',
            'cajas125' => 'required|integer|min:0',
            'cajas250' => 'required|integer|min:0',
            'cajas500' => 'required|integer|min:0',
            'user_id' => 'required|exists:users,id',
        ]);

        $cosecha->update($request->except(['cajas125', 'cajas250', 'cajas500']));

        // Actualizar los valores de las cajas
        $cosecha->cajas125 = $request->input('cajas125');
        $cosecha->cajas250 = $request->input('cajas250');
        $cosecha->cajas500 = $request->input('cajas500');
        $cosecha->save();

        // Recalcular productos y códigos si es necesario

        return redirect()->route('cosecha.index')->with('success', 'Cosecha actualizada correctamente.');
    }

    /**
     * Eliminar una cosecha de la base de datos.
     */
    public function destroy(Cosecha $cosecha)
    {
        $cosecha->productos()->detach();
        $cosecha->delete();

        return redirect()->route('cosecha.index')->with('success', 'Cosecha eliminada correctamente.');
    }
}
