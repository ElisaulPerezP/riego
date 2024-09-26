<?php

namespace Database\Factories;

use App\Models\Aspercion;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AspercionFactory extends Factory
{
    /**
     * El nombre del modelo asociado.
     *
     * @var string
     */
    protected $model = Aspercion::class;

    /**
     * Define el estado por defecto del factory.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'fecha' => $this->faker->date(),
            'hora' => $this->faker->time('H:i'),
            'volumen' => $this->faker->randomFloat(2, 10, 500), // Entre 10 y 500 litros
            'tipo_aspercion' => $this->faker->word(),
            'responsable' => $this->faker->name(),
            'user_id' => User::factory(),
            'cantidad_de_producto' => $this->faker->numberBetween(1, 100), // Nuevo campo
        ];
    }


    /**
     * Estado personalizado para asociar productos.
     *
     * @param int $cantidadProductos
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withProductos($cantidadProductos = 1)
    {
        return $this->afterCreating(function (Aspercion $aspercion) use ($cantidadProductos) {
            $productos = Producto::factory()->count($cantidadProductos)->create();
            $aspercion->productos()->attach($productos->pluck('id'));
        });
    }
}
