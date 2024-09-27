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
            // Generar tiempo entre 0 y 20 minutos (0 a 1200 segundos)
            $seconds = $this->faker->numberBetween(0, 1200);
            $data["tiempo{$i}"] = gmdate('H:i:s', $seconds);
            $data["mensaje{$i}"] = $this->faker->randomElement([
                'Riego exitoso',
                'Necesita revisión',
                'Volumen adecuado',
                'Tiempo insuficiente',
                'Fertilizante aplicado correctamente',
                'Alerta de sistema',
                'Sin problemas detectados',
                'Error en el surco',
                'Mantenimiento requerido',
                'Operación normal'
            ]);
        }

        return $data;
    }
}
