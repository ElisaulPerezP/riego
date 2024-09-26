<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\ReporteRiego;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ReporteRiegoControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_displays_a_list_of_reportes()
    {
        // Arrange: Crear un usuario y algunos reportes
        $user = User::factory()->create();
        $this->actingAs($user);

        $reportes = ReporteRiego::factory()->count(3)->create();

        // Act: Hacer una solicitud GET a la ruta index
        $response = $this->get(route('reportes.index'));

        // Assert: Verificar que la vista se carga correctamente
        $response->assertStatus(200)
                 ->assertViewIs('reporteRiego.index')
                 ->assertViewHas('reportes', function ($collection) use ($reportes) {
                     return $collection->count() === 3;
                 });
    }

    /** @test */
    public function it_displays_the_create_form()
    {
        // Arrange: Crear un usuario
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: Hacer una solicitud GET a la ruta create
        $response = $this->get(route('reportes.create'));

        // Assert: Verificar que la vista se carga correctamente
        $response->assertStatus(200)
                 ->assertViewIs('reporteRiego.create');
    }

    /** @test */
    public function it_stores_a_new_reporte_successfully()
    {
        // Arrange: Crear un usuario y datos del reporte
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = ReporteRiego::factory()->make()->toArray();

        // Act: Hacer una solicitud POST a la ruta store
        $response = $this->post(route('reportes.store'), $data);

        // Assert: Verificar que el reporte fue creado
        $response->assertRedirect(route('reportes.index'));
        $this->assertDatabaseHas('reporte_riego', $data);
    }

    /** @test */
    public function it_shows_validation_errors_when_storing_invalid_data()
    {
        // Arrange: Crear un usuario y datos inválidos
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'volumen1' => 'no es un número',
            'tiempo1' => 'hora inválida',
            // Otros campos pueden estar ausentes o inválidos
        ];

        // Act: Hacer una solicitud POST con datos inválidos
        $response = $this->from(route('reportes.create'))->post(route('reportes.store'), $data);

        // Assert: Verificar que hay errores de validación
        $response->assertRedirect(route('reportes.create'));
        $response->assertSessionHasErrors(['volumen1', 'tiempo1']);
    }

    /** @test */
    public function it_displays_a_specific_reporte()
    {
        // Arrange: Crear un usuario y un reporte
        $user = User::factory()->create();
        $this->actingAs($user);

        $reporte = ReporteRiego::factory()->create();

        // Act: Hacer una solicitud GET a la ruta show
        $response = $this->get(route('reportes.show', $reporte));

        // Assert: Verificar que la vista se carga correctamente
        $response->assertStatus(200)
                 ->assertViewIs('reporteRiego.show')
                 ->assertViewHas('reporteRiego', $reporte);
    }

    /** @test */
    public function it_displays_the_edit_form()
    {
        // Arrange: Crear un usuario y un reporte
        $user = User::factory()->create();
        $this->actingAs($user);

        $reporte = ReporteRiego::factory()->create();

        // Act: Hacer una solicitud GET a la ruta edit
        $response = $this->get(route('reportes.edit', $reporte));

        // Assert: Verificar que la vista se carga correctamente
        $response->assertStatus(200)
                 ->assertViewIs('reporteRiego.edit')
                 ->assertViewHas('reporteRiego', $reporte);
    }

    /** @test */
    public function it_updates_a_reporte_successfully()
    {
        // Arrange: Crear un usuario y un reporte existente
        $user = User::factory()->create();
        $this->actingAs($user);

        $reporte = ReporteRiego::factory()->create();

        $newData = [
            'volumen1' => 20.5,
            'tiempo1' => '14:45:00',
            'mensaje1' => 'Mensaje actualizado',
            // Añadir otros campos si es necesario
        ];

        // Act: Hacer una solicitud PUT a la ruta update
        $response = $this->put(route('reportes.update', $reporte), $newData);

        // Assert: Verificar que el reporte fue actualizado
        $response->assertRedirect(route('reportes.index'));
        $this->assertDatabaseHas('reporte_riego', array_merge(['id' => $reporte->id], $newData));
    }

    /** @test */
    public function it_deletes_a_reporte_successfully()
    {
        // Arrange: Crear un usuario y un reporte
        $user = User::factory()->create();
        $this->actingAs($user);

        $reporte = ReporteRiego::factory()->create();

        // Act: Hacer una solicitud DELETE a la ruta destroy
        $response = $this->delete(route('reportes.destroy', $reporte));

        // Assert: Verificar que el reporte fue eliminado
        $response->assertRedirect(route('reportes.index'));
        $this->assertDatabaseMissing('reporte_riego', ['id' => $reporte->id]);
    }
}
