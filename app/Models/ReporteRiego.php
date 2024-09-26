<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteRiego extends Model
{
    use HasFactory;

    protected $table = 'reporte_riego';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        // Campos de volumen para los 14 surcos
        'volumen1', 'volumen2', 'volumen3', 'volumen4', 'volumen5', 'volumen6', 'volumen7',
        'volumen8', 'volumen9', 'volumen10', 'volumen11', 'volumen12', 'volumen13', 'volumen14',

        // Campos de tiempo para los 14 surcos
        'tiempo1', 'tiempo2', 'tiempo3', 'tiempo4', 'tiempo5', 'tiempo6', 'tiempo7',
        'tiempo8', 'tiempo9', 'tiempo10', 'tiempo11', 'tiempo12', 'tiempo13', 'tiempo14',

        // Campos de mensaje para los 14 surcos
        'mensaje1', 'mensaje2', 'mensaje3', 'mensaje4', 'mensaje5', 'mensaje6', 'mensaje7',
        'mensaje8', 'mensaje9', 'mensaje10', 'mensaje11', 'mensaje12', 'mensaje13', 'mensaje14',
    ];
}
