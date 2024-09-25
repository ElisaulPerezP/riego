<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qr extends Model
{
    use HasFactory;

    protected $table = 'qrs';

    protected $fillable = [
        'cosecha_id',
        'qr125',
        'qr250',
        'qr500',
        'uuid125',
        'uuid250',
        'uuid500',
    ];

    protected $casts = [
        'uuid125' => 'array',
        'uuid250' => 'array',
        'uuid500' => 'array',
    ];

    public function cosecha()
    {
        return $this->belongsTo(Cosecha::class);
    }
}
