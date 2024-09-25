<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $table = 'reportes';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'documento',
        'fecha_solicitud',
        'fecha_procesamiento',
        'fecha_descarga',
        'user_id',
    ];

    /**
     * Relación muchos a muchos con Producto.
     */
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_reporte');
    }
    // Añadir al modelo Reporte
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
