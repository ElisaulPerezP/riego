<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cosecha extends Model
{
    use HasFactory;

    protected $table = 'cosechas';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'fecha',
        'cantidad',
        'porcentaje',
        'empaquetado',
        'user_id',  // Añadido para relacionar con el usuario responsable
    ];

    /**
     * Relación muchos a muchos con Producto.
     */
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'cosecha_producto');
    }

    /**
     * Relación uno a muchos con Codigo.
     */
    public function codigos()
    {
        return $this->hasMany(Codigo::class);
    }

    /**
     * Relación uno a muchos (inversa) con User.
     * Una cosecha pertenece a un usuario (responsable).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}