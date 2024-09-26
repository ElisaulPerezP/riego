<?php

namespace App\Http\Actions;

use App\Models\Producto;
use App\Models\Stock;

class AjustarStockProducto
{
    /**
     * Ajusta el stock de productos al actualizar una asperción.
     *
     * @param array $productosAnteriores Cantidades anteriores (producto_id => cantidad)
     * @param array $productosNuevos Cantidades nuevas (producto_id => ['cantidad_de_producto' => cantidad])
     * @return void
     */
    public function ejecutar(array $productosAnteriores, array $productosNuevos)
    {
        // Revertir las cantidades anteriores
        foreach ($productosAnteriores as $productoId => $cantidadAnterior) {
            // Obtener el registro de stock del producto
            $stock = Stock::where('producto_id', $productoId)->first();
            if ($stock) {
                // Devolver al stock la cantidad anterior
                $stock->cantidad_en_stock += $cantidadAnterior;
                $stock->save();
            } else {
                $producto = Producto::find($productoId);
                throw new \Exception("No se encontró el registro de stock para el producto: {$producto->nombre}");
            }
        }

        // Restar las nuevas cantidades
        foreach ($productosNuevos as $productoId => $pivotData) {
            $cantidadNueva = $pivotData['cantidad_de_producto'];

            // Obtener el registro de stock del producto
            $stock = Stock::where('producto_id', $productoId)->first();
            if ($stock) {
                if ($stock->cantidad_en_stock < $cantidadNueva) {
                    $producto = Producto::find($productoId);
                    throw new \Exception("No hay suficiente stock disponible para el producto: {$producto->nombre}");
                }
                $stock->cantidad_en_stock -= $cantidadNueva;
                $stock->save();
            } else {
                $producto = Producto::find($productoId);
                throw new \Exception("No se encontró el registro de stock para el producto: {$producto->nombre}");
            }
        }
    }
}
