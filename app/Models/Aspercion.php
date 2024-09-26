<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aspercion extends Model
{
    use HasFactory;

    protected $table = 'asperciones';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'fecha',
        'hora',
        'volumen',
        'tipo_aspercion',
        'responsable',
        'anotaciones',
        'user_id',  
    ];

    /**
     * Relación muchos a muchos con Producto.
     */
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'aspercion_producto')
                    ->withPivot('cantidad_de_producto')
                    ->withTimestamps();
    }
    /**
     * Relación uno a muchos (inversa) con User.
     * Un usuario (responsable) puede estar relacionado con muchas asperciones.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
