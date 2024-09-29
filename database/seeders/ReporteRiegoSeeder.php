<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReporteRiego;
use Carbon\Carbon;

class ReporteRiegoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Tiempo inicial (timestamp actual)
        $currentTime = Carbon::now();

        // Generar 50 reportes de riego
        for ($i = 0; $i < 50; $i++) {
            // Crear un nuevo reporte de riego con el factory
            ReporteRiego::factory()->create([
                'created_at' => $currentTime,
            ]);

            // Sumar entre 8 y 12 horas al timestamp actual para el siguiente reporte
            $hoursToAdd = rand(8, 12);
            $currentTime = $currentTime->addHours($hoursToAdd);
        }
    }
}
