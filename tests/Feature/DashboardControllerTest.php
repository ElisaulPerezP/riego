<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\ProgramaActual;
use App\Models\ReporteRiego;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use phpmock\phpunit\PHPMock;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase, PHPMock;

    /** @test */
    public function it_displays_dashboard_with_programa_actual_and_ultimo_reporte_and_image()
    {
        // Crear datos de prueba
        $programaRiego = \App\Models\ProgramaRiego::factory()->create();
        $programaActual = ProgramaActual::factory()->create([
            'programa_riego_id' => $programaRiego->id,
        ]);

        $ultimoReporte = ReporteRiego::factory()->create([
            'created_at' => now(),
        ]);

        // Mockear la función file_exists para que siempre retorne true
        $fileExistsMock = $this->getFunctionMock('App\Http\Controllers', "file_exists");
        $fileExistsMock->expects($this->once())->with($this->anything())->willReturn(true);
        $user = User::factory()->create();
        $this->actingAs($user);
        // Hacer una solicitud GET a la ruta del dashboard
        $response = $this->get(route('dashboard'));

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);

        // Verificar que la vista tenga las variables esperadas
        $response->assertViewHas('programaActual', function($viewProgramaActual) use ($programaActual) {
            return $viewProgramaActual->id === $programaActual->id;
        });

        $response->assertViewHas('ultimoReporte', function($viewUltimoReporte) use ($ultimoReporte) {
            return $viewUltimoReporte->id === $ultimoReporte->id;
        });

        $response->assertViewHas('public_image_url', asset('storage/graphs/graficas_reporte_riego.png'));
    }
}
