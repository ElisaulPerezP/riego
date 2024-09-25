<?php

namespace Database\Factories;

use App\Models\Tratamiento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TratamientoFactory extends Factory
{
    protected $model = Tratamiento::class;

    public function definition()
    {
        return [
            'agronomo' => $this->faker->name(),
            'cantidad' => $this->faker->randomFloat(2, 1, 100),
            'frecuencia' => $this->faker->word(),
            'diagnostico' => $this->faker->sentence(),
            'notas' => $this->faker->paragraph(),
            'user_id' => User::factory(),
        ];
    }

    public function withProductos($cantidadProductos = 1)
    {
        return $this->afterCreating(function (Tratamiento $tratamiento) use ($cantidadProductos) {
            $productos = \App\Models\Producto::factory()->count($cantidadProductos)->create();
            $tratamiento->productos()->attach($productos->pluck('id'));
        });
    }
}
