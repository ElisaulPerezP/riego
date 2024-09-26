<?php

namespace Database\Factories;

use App\Models\ReporteRiego;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReporteRiegoFactory extends Factory
{
    protected $model = ReporteRiego::class;

    public function definition()
    {
        $data = [];

        for ($i = 1; $i <= 14; $i++) {
            $data["volumen{$i}"] = $this->faker->randomFloat(2, 0, 25);
            $data["tiempo{$i}"] = $this->faker->time('H:i:s');
            $data["mensaje{$i}"] = $this->faker->sentence;
        }

        return $data;
    }
}
