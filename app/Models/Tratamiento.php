<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tratamiento extends Model
{
    use HasFactory;

    protected $table = 'tratamientos';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'agronomo',
        'cantidad',        // Cantidad del producto utilizado
        'frecuencia',      // Frecuencia de aplicación
        'diagnostico',     // Diagnóstico asociado al tratamiento
        'notas',           // Notas adicionales
        'user_id',         // Relacionar con el usuario responsable (agronomo)
    ];

    /**
     * Relación muchos a muchos con Producto.
     */
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_tratamiento');
    }

    /**
     * Relación inversa uno a muchos con User.
     * Un tratamiento pertenece a un agrónomo o responsable.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
