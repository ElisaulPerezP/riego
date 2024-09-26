<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ReporteRiego;
use App\Models\AcuseDeRecibo;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogApiTrafficTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba que una solicitud API no autenticada se registre correctamente.
     *
     * @return void
     */
    public function test_it_logs_unauthenticated_api_request()
    {
        // Arrange: Asegurar que no hay registros previos
        $this->assertCount(0, AcuseDeRecibo::all());

        // Act: Realizar una solicitud GET no autenticada
        $response = $this->getJson('/api/reportes');

        // Assert: Verificar que la respuesta es exitosa
        $response->assertStatus(200);

        // Verificar que se registró la solicitud y la respuesta
        $acuses = AcuseDeRecibo::all();
        $this->assertCount(1, $acuses);

        $acuse = $acuses->first();

        // Verificar los datos del acuse
        $this->assertNull($acuse->usuario_responsable);
        $this->assertEquals($response->getContent(), $acuse->acuse_de_recibo);
        $this->assertEquals('GET', $acuse->modelo_serializado['method']);
        $this->assertEmpty($acuse->modelo_serializado['input']);
        $this->assertEquals(200, $acuse->estado_entrega);
    }

    /**
     * Prueba que una solicitud API autenticada se registre correctamente.
     *
     * @return void
     */
    public function test_it_logs_authenticated_api_request()
    {
        // Arrange: Crear y autenticar un usuario
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Asegurar que no hay registros previos
        $this->assertCount(0, AcuseDeRecibo::all());

        // Act: Realizar una solicitud GET autenticada
        $response = $this->getJson('/api/reportes');

        // Assert: Verificar que la respuesta es exitosa
        $response->assertStatus(200);

        // Verificar que se registró la solicitud y la respuesta
        $acuses = AcuseDeRecibo::all();
        $this->assertCount(1, $acuses);

        $acuse = $acuses->first();

        // Verificar los datos del acuse
        $this->assertEquals($user->id, $acuse->usuario_responsable);
        $this->assertEquals($response->getContent(), $acuse->acuse_de_recibo);
        $this->assertEquals('GET', $acuse->modelo_serializado['method']);
        $this->assertEmpty($acuse->modelo_serializado['input']);
        $this->assertEquals(200, $acuse->estado_entrega);
    }

    /**
     * Prueba que el middleware no interfiere con las respuestas de la API.
     *
     * @return void
     */
    public function test_middleware_does_not_affect_api_response()
    {
        // Arrange: Crear un reporte de riego
        $reporte = ReporteRiego::factory()->create();

        // Act: Realizar una solicitud GET para obtener el reporte
        $response = $this->getJson("/api/reportes/{$reporte->id}");

        // Assert: Verificar que la respuesta contiene los datos esperados
        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $reporte->id]);

        // Verificar que se registró la solicitud y la respuesta
        $acuses = AcuseDeRecibo::all();
        $this->assertCount(1, $acuses);
    }

    /**
     * Prueba que las solicitudes POST se registran correctamente con los datos enviados.
     *
     * @return void
     */
    public function test_it_logs_post_api_request()
    {
        // Arrange: Crear y autenticar un usuario
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
    
        // Datos para la solicitud POST, incluyendo todos los campos requeridos
        $data = [];
    
        for ($i = 1; $i <= 14; $i++) {
            $data["volumen{$i}"] = rand(1, 100) / 10; // Ejemplo: 10.5, 5.0, etc.
            $data["tiempo{$i}"] = '12:30:00'; // Puedes variar los tiempos según sea necesario
            $data["mensaje{$i}"] = "Mensaje {$i}";
        }
    
        // Act: Realizar una solicitud POST
        $response = $this->postJson('/api/reportes', $data);
    
        // Assert: Verificar que la respuesta indica creación exitosa
        $response->assertStatus(201)
                 ->assertJsonFragment(['volumen1' => $data['volumen1']]);
    
        // Verificar que se registró la solicitud y la respuesta
        $acuses = AcuseDeRecibo::all();
        $this->assertCount(1, $acuses);
    
        $acuse = $acuses->first();
    
        // Verificar los datos del acuse
        $this->assertEquals($user->id, $acuse->usuario_responsable);
        $this->assertEquals($response->getContent(), $acuse->acuse_de_recibo);
        $this->assertEquals('POST', $acuse->modelo_serializado['method']);
        $this->assertEquals($data, $acuse->modelo_serializado['input']);
        $this->assertEquals(201, $acuse->estado_entrega);
    }
    

    /**
     * Prueba que las solicitudes con errores también se registran correctamente.
     *
     * @return void
     */
    public function test_it_logs_api_request_with_validation_errors()
    {
        // Arrange: Crear y autenticar un usuario
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Datos inválidos para la solicitud POST
        $data = [
            'volumen1' => 'no es un número', // Supongamos que 'volumen1' debe ser numérico
            'tiempo1' => 'hora inválida',   // Supongamos que 'tiempo1' debe tener formato 'H:i:s'
            // Falta 'mensaje1', si es requerido
        ];

        // Act: Realizar una solicitud POST con datos inválidos
        $response = $this->postJson('/api/reportes', $data);

        // Assert: Verificar que hay errores de validación
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['volumen1', 'tiempo1']);

        // Verificar que se registró la solicitud y la respuesta
        $acuses = AcuseDeRecibo::all();
        $this->assertCount(1, $acuses);

        $acuse = $acuses->first();

        // Verificar los datos del acuse
        $this->assertEquals($user->id, $acuse->usuario_responsable);
        $this->assertEquals($response->getContent(), $acuse->acuse_de_recibo);
        $this->assertEquals('POST', $acuse->modelo_serializado['method']);
        $this->assertEquals($data, $acuse->modelo_serializado['input']);
        $this->assertEquals(422, $acuse->estado_entrega);
    }
}
