<?php

namespace Database\Factories;

use App\Models\ProgramaActual;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramaActualFactory extends Factory
{
    protected $model = ProgramaActual::class;

    public function definition()
    {
        return [
            'programa_riego_id' => \App\Models\ProgramaRiego::factory(),
        ];
    }
}
