<?php

namespace Database\Factories;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    /**
     * El nombre del modelo asociado.
     *
     * @var string
     */
    protected $model = Producto::class;

    /**
     * Define el estado por defecto del factory.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombre' => $this->faker->word(),
            'descripcion' => $this->faker->sentence(),
            'precio' => $this->faker->randomFloat(2, 1, 1000), // Precio entre 1 y 1000
            'cantidad' => $this->faker->numberBetween(1, 100), // Cantidad entre 1 y 100
            'fecha_vencimiento' => $this->faker->date(),
            'responsable' => $this->faker->name(),
            'tiempo_retiro' => $this->faker->numberBetween(1, 30),
            'tiempo_exclusion' => $this->faker->numberBetween(1, 30),
            'afectacion' => $this->faker->sentence(),
            'tratamiento_intoxicacion' => $this->faker->sentence(),
            'telefono_emergencia' => $this->faker->phoneNumber(),
            'numero_registro' => $this->faker->word(),
            'composicion_quimica' => $this->faker->word(),
            'clasificacion_toxicidad' => $this->faker->word(),
            'instrucciones_almacenamiento' => $this->faker->sentence(),
            'proveedor' => $this->faker->company(),
        ];
    }
}
