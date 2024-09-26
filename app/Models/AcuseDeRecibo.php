<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcuseDeRecibo extends Model
{
    use HasFactory;

    protected $table = 'acuses_de_recibo';

    protected $fillable = [
        'entregado_a',
        'acuse_de_recibo',
        'recibido_de',
        'modelo_serializado',
        'fecha_entrega',
        'fecha_acuse',
        'estado_entrega',
        'usuario_responsable',
        'firma_recibo',
    ];

    // Si usas campos de tipo JSON o fecha, puedes añadir casts
    protected $casts = [
        'modelo_serializado' => 'array',
        'fecha_entrega'      => 'datetime',
        'fecha_acuse'        => 'datetime',
    ];
}
