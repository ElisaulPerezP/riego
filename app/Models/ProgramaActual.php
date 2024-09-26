<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramaActual extends Model
{
    use HasFactory;
    
    protected $table = 'programa_actual'; // Especifica el nombre de la tabla

    protected $fillable = ['programa_riego_id'];

    // Relación con ProgramaRiego
    public function programaRiego()
    {
        return $this->belongsTo(ProgramaRiego::class, 'programa_riego_id');
    }
}
