<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\ReporteRiego;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;


class ReporteRiegoApiControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_can_list_all_reportes_via_api()
    {
        // Arrange: Crear algunos reportes
        $reportes = ReporteRiego::factory()->count(3)->create();

        // Act: Hacer una solicitud GET a la ruta index de la API
        $response = $this->getJson('/api/reportes');

        // Assert: Verificar que la respuesta sea exitosa y contenga los reportes
        $response->assertStatus(200)
         ->assertJsonCount(3) // Esto verifica que haya 3 reportes en la respuesta
         ->assertJsonStructure([
             '*' => [
                 'id', 
                 'volumen1', 'volumen2', 'volumen3', 'volumen4', 'volumen5', 'volumen6', 'volumen7', 
                 'volumen8', 'volumen9', 'volumen10', 'volumen11', 'volumen12', 'volumen13', 'volumen14',
                 'tiempo1', 'tiempo2', 'tiempo3', 'tiempo4', 'tiempo5', 'tiempo6', 'tiempo7', 
                 'tiempo8', 'tiempo9', 'tiempo10', 'tiempo11', 'tiempo12', 'tiempo13', 'tiempo14',
                 'mensaje1', 'mensaje2', 'mensaje3', 'mensaje4', 'mensaje5', 'mensaje6', 'mensaje7', 
                 'mensaje8', 'mensaje9', 'mensaje10', 'mensaje11', 'mensaje12', 'mensaje13', 'mensaje14',
                 'created_at', 'updated_at',
             ],
         ]);

    }

    /** @test */
    public function it_can_create_a_new_reporte_via_api()
    {
        // Arrange: Datos del nuevo reporte
        $data = ReporteRiego::factory()->make()->toArray();

        // Act: Hacer una solicitud POST a la ruta store de la API
        $response = $this->postJson('/api/reportes', $data);

        // Assert: Verificar que el reporte fue creado y que la respuesta es correcta
        $response->assertStatus(201)
                 ->assertJsonFragment(['volumen1' => $data['volumen1']]);

        $this->assertDatabaseHas('reporte_riego', $data);
    }

    /** @test */
    public function it_validates_input_when_creating_reporte_via_api()
    {
        // Arrange: Datos inválidos
        $data = [
            'volumen1' => 'no es un número',
            'tiempo1' => 'hora inválida',
            // Falta 'mensaje1', que puede ser nullable según tu validación
        ];

        // Act: Hacer una solicitud POST con datos inválidos
        $response = $this->postJson('/api/reportes', $data);

        // Assert: Verificar que hay errores de validación
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['volumen1', 'tiempo1']);
    }

    /** @test */
    public function it_can_show_a_reporte_via_api()
    {
        // Arrange: Crear un reporte
        $reporte = ReporteRiego::factory()->create();

        // Act: Hacer una solicitud GET a la ruta show de la API
        $response = $this->getJson(route('api.reportes.show', ['reporte' => $reporte->id]));

        // Assert: Verificar que la respuesta contiene el reporte
        $response->assertStatus(200)
                ->assertJsonFragment(['id' => $reporte->id]);
    }


    /** @test */
    public function it_can_update_a_reporte_via_api()
    {
        // Arrange: Crear un reporte existente
        $reporte = ReporteRiego::factory()->create();

        // Nuevos datos para actualizar
        $newData = [
            'volumen1' => 15,
            'tiempo1' => '12:30:00',
            'mensaje1' => 'Nuevo mensaje',
            // Añadir otros campos si es necesario
        ];

        // Act: Hacer una solicitud PUT a la ruta update de la API
        $response = $this->putJson((route('api.reportes.update',  $reporte->id)), $newData);
        // Assert: Verificar que el reporte fue actualizado
        $response->assertStatus(200)
                ->assertJsonFragment(['volumen1' => 15]);

        $this->assertDatabaseHas('reporte_riego', array_merge(['id' => $reporte->id], $newData));
    }


    /** @test */
    public function it_can_delete_a_reporte_via_api()
    {
        // Arrange: Crear un reporte
        $reporte = ReporteRiego::factory()->create();

        // Act: Hacer una solicitud DELETE a la ruta destroy de la API
        $response = $this->delete(route('api.reportes.destroy', ['reporte' => $reporte->id]));

        // Assert: Verificar que la respuesta es 204 (No Content)
        $response->assertStatus(204);

        // Verificar que el reporte fue completamente eliminado de la base de datos
        $this->assertDatabaseMissing('reporte_riego', ['id' => $reporte->id]);
    }

}
