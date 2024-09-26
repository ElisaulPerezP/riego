<?php

namespace App\Http\Actions;

use App\Models\Producto;
use App\Models\Stock;

class RestarProductoAStock
{
    /**
     * Resta la cantidad de producto al stock de los productos dados.
     *
     * @param array $productos Cantidad de producto a restar (array con clave de producto ID y valor array con 'cantidad_de_producto').
     * @return void
     */
    public function ejecutar(array $productos)
    {
        foreach ($productos as $productoId => $pivotData) {
            $cantidadUsada = $pivotData['cantidad_de_producto'];

            // Obtener el registro de stock del producto
            $stock = Stock::where('producto_id', $productoId)->first();

            if ($stock) {
                // Verificar que haya suficiente stock disponible
                if ($stock->cantidad_en_stock < $cantidadUsada) {
                    $producto = Producto::find($productoId);
                    throw new \Exception("No hay suficiente stock disponible para el producto: {$producto->nombre}");
                }

                // Restar la cantidad utilizada del stock
                $stock->cantidad_en_stock -= $cantidadUsada;
                $stock->save();
            } else {
                $producto = Producto::find($productoId);
                throw new \Exception("No se encontró el registro de stock para el producto: {$producto->nombre}");
            }
        }
    }
}
