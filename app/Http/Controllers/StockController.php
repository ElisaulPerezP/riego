<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Producto;
use Illuminate\Http\Request;
use Carbon\Carbon;  // Para manejar fechas fácilmente

class StockController extends Controller
{
    /**
     * Mostrar una lista de stocks.
     */
    public function index()
    {
        $stocks = Stock::with('producto')->get();
        return view('stocks.index', compact('stocks'));
    }

    /**
     * Mostrar el formulario para crear un nuevo stock.
     */
    public function create()
    {
        $productos = Producto::doesntHave('stock')->get(); // Mostrar solo productos sin stock
        return view('stocks.create', compact('productos'));
    }

    /**
     * Almacenar un nuevo stock en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id|unique:stocks,producto_id', // Verificar que el producto no tenga stock ya
            'cantidad_en_stock' => 'required|integer|min:0',
        ]);

        // Obtener el producto seleccionado
        $producto = Producto::findOrFail($request->producto_id);

        // Calcular los días para vencimiento restando la fecha de vencimiento y la fecha actual
        $dias_para_vencimiento = Carbon::now()->diffInDays(Carbon::parse($producto->fecha_vencimiento), false);

        // Crear el stock con los datos calculados
        Stock::create([
            'producto_id' => $request->producto_id,
            'cantidad_en_stock' => $request->cantidad_en_stock,
            'dias_para_vencimiento' => $dias_para_vencimiento,
        ]);

        return redirect()->route('stocks.index')->with('success', 'Stock creado correctamente.');
    }

    /**
     * Mostrar los detalles de un stock.
     */
    public function show(Stock $stock)
    {
        return view('stocks.show', compact('stock'));
    }

    /**
     * Mostrar el formulario para editar un stock.
     */
    public function edit(Stock $stock)
    {
        $productos = Producto::all();
        return view('stocks.edit', compact('stock', 'productos'));
    }

    /**
     * Actualizar un stock en la base de datos.
     */
    public function update(Request $request, Stock $stock)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id|unique:stocks,producto_id,' . $stock->id,
            'cantidad_en_stock' => 'required|integer|min:0',
        ]);

        // Obtener el producto actualizado
        $producto = Producto::findOrFail($request->producto_id);

        // Recalcular los días para vencimiento
        $dias_para_vencimiento = Carbon::now()->diffInDays(Carbon::parse($producto->fecha_vencimiento), false);

        // Actualizar el stock
        $stock->update([
            'producto_id' => $request->producto_id,
            'cantidad_en_stock' => $request->cantidad_en_stock,
            'dias_para_vencimiento' => $dias_para_vencimiento,
        ]);

        return redirect()->route('stocks.index')->with('success', 'Stock actualizado correctamente.');
    }

    /**
     * Eliminar un stock de la base de datos.
     */
    public function destroy(Stock $stock)
    {
        $stock->delete();

        return redirect()->route('stocks.index')->with('success', 'Stock eliminado correctamente.');
    }
}
