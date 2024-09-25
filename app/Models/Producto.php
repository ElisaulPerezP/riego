<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'cantidad',
        'fecha_vencimiento',
        'responsable',
        'tiempo_retiro',
        'tiempo_exclusion',
        'afectacion',
        'tratamiento_intoxicacion',
        'telefono_emergencia',
        'numero_registro',
        'composicion_quimica',
        'clasificacion_toxicidad',
        'instrucciones_almacenamiento',
        'proveedor',
    ];

    /**
     * Relación muchos a muchos con Aspercion.
     */
    public function asperciones()
    {
        return $this->belongsToMany(Aspercion::class, 'aspercion_producto');
    }

    /**
     * Relación muchos a muchos con Cosecha.
     */
    public function cosechas()
    {
        return $this->belongsToMany(Cosecha::class, 'cosecha_producto');
    }

    /**
     * Relación muchos a muchos con Tratamiento.
     */
    public function tratamientos()
    {
        return $this->belongsToMany(Tratamiento::class, 'producto_tratamiento');
    }

    /**
     * Relación muchos a muchos con Reporte.
     */
    public function reportes()
    {
        return $this->belongsToMany(Reporte::class, 'producto_reporte');
    }
}
