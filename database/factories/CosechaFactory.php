<?php

namespace Database\Factories;

use App\Models\Cosecha;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CosechaFactory extends Factory
{
    protected $model = Cosecha::class;

    public function definition()
    {
        return [
            'fecha' => $this->faker->date(),
            'cantidad' => $this->faker->randomFloat(2, 0, 999.99),
            'porcentaje' => $this->faker->randomFloat(2, 0, 100),
            'cajas125' => $this->faker->numberBetween(0, 100),
            'cajas250' => $this->faker->numberBetween(0, 100),
            'cajas500' => $this->faker->numberBetween(0, 100),
            'user_id' => User::factory(),
        ];
    }
}
