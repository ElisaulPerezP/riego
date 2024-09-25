<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id',
        'cantidad_en_stock',
        'dias_para_vencimiento',
    ];

    /**
     * Relación uno a uno inversa con Producto.
     * Un stock pertenece a un producto.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
