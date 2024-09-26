<?php

namespace Database\Factories;

use App\Models\ProgramaRiego;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramaRiegoFactory extends Factory
{
    protected $model = ProgramaRiego::class;

    public function definition()
    {
        $data = [];

        // Generar datos ficticios para volumen, fertilizante1 y fertilizante2 para los 14 surcos
        for ($i = 1; $i <= 14; $i++) {
            $data["volumen{$i}"] = $this->faker->numberBetween(0, 25);
            $data["fertilizante1_{$i}"] = $this->faker->numberBetween(0, 10);
            $data["fertilizante2_{$i}"] = $this->faker->numberBetween(0, 10);
        }

        return $data;
    }
}
