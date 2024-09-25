<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramaRiego extends Model
{
    use HasFactory;

    protected $table = 'programas_riego';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'veces_por_dia',
        'volumen1', 'volumen2', 'volumen3', 'volumen4', 'volumen5', 'volumen6', 'volumen7',
        'volumen8', 'volumen9', 'volumen10', 'volumen11', 'volumen12', 'volumen13', 'volumen14',
        'fertilizante1_1', 'fertilizante1_2', 'fertilizante1_3', 'fertilizante1_4', 'fertilizante1_5',
        'fertilizante1_6', 'fertilizante1_7', 'fertilizante1_8', 'fertilizante1_9', 'fertilizante1_10',
        'fertilizante1_11', 'fertilizante1_12', 'fertilizante1_13', 'fertilizante1_14',
        'fertilizante2_1', 'fertilizante2_2', 'fertilizante2_3', 'fertilizante2_4', 'fertilizante2_5',
        'fertilizante2_6', 'fertilizante2_7', 'fertilizante2_8', 'fertilizante2_9', 'fertilizante2_10',
        'fertilizante2_11', 'fertilizante2_12', 'fertilizante2_13', 'fertilizante2_14',
    ];

}
