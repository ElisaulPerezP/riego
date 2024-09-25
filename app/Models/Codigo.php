<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Codigo extends Model
{
    use HasFactory;

    protected $table = 'codigos';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'codigo',
        'cosecha_id',
    ];

    /**
     * Relación inversa uno a muchos con Cosecha.
     */
    public function cosecha()
    {
        return $this->belongsTo(Cosecha::class);
    }
}
