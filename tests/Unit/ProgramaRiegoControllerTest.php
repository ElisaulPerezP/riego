<?php

namespace Tests\Feature;

use App\Models\ProgramaActual;
use App\Models\ProgramaRiego;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramaRiegoControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_the_programa_riego_index_page()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear algunos programas de riego
        $programas = ProgramaRiego::factory()->count(3)->create();

        // Crear un programa actual
        $programaActual = ProgramaActual::factory()->create([
            'programa_riego_id' => $programas->first()->id,
        ]);

        // Act: Hacer una solicitud GET a la ruta de índice
        $response = $this->get(route('programa-riego.index'));

        // Assert: Verificar que la respuesta sea exitosa y contenga los programas
        $response->assertStatus(200);
        $response->assertViewIs('programas.index');
        $response->assertViewHas('programas', function ($collection) use ($programas) {
            return $collection->count() === 3;
        });
        $response->assertViewHas('programaActual', function ($actual) use ($programaActual) {
            return $actual->id === $programaActual->id;
        });
    }

    /** @test */
    public function it_displays_the_create_programa_riego_form()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: Hacer una solicitud GET a la ruta de creación
        $response = $this->get(route('programa-riego.create'));

        // Assert: Verificar que la respuesta sea exitosa y contenga el formulario de creación
        $response->assertStatus(200);
        $response->assertViewIs('programas.create');
    }

    /** @test */
    public function it_stores_a_new_programa_riego()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Datos para el nuevo programa de riego
        $data = ProgramaRiego::factory()->make()->toArray();

        // Act: Hacer una solicitud POST para almacenar el programa de riego
        $response = $this->post(route('programa-riego.store'), $data);

        // Assert: Verificar que el programa fue creado en la base de datos
        $response->assertRedirect(route('programa-riego.index'));
        $this->assertDatabaseHas('programas_riego', $data);
    }

    /** @test */
    public function it_displays_the_programa_riego_details()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear un programa de riego
        $programa = ProgramaRiego::factory()->create();

        // Act: Hacer una solicitud GET a la ruta de detalle
        $response = $this->get(route('programa-riego.show', $programa));

        // Assert: Verificar que la respuesta sea exitosa y contenga los detalles
        $response->assertStatus(200);
        $response->assertViewIs('programas.show');
        $response->assertViewHas('programaRiego', $programa);
    }

    /** @test */
    public function it_displays_the_edit_programa_riego_form()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear un programa de riego
        $programa = ProgramaRiego::factory()->create();

        // Act: Hacer una solicitud GET a la ruta de edición
        $response = $this->get(route('programa-riego.edit', $programa));

        // Assert: Verificar que la respuesta sea exitosa y contenga el formulario de edición
        $response->assertStatus(200);
        $response->assertViewIs('programas.edit');
        $response->assertViewHas('programaRiego', $programa);
    }

    /** @test */
    public function it_updates_an_existing_programa_riego()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear un programa de riego existente
        $programa = ProgramaRiego::factory()->create();

        // Nuevos datos para actualizar
        $newData = ProgramaRiego::factory()->make()->toArray();

        // Act: Hacer una solicitud PUT para actualizar el programa de riego
        $response = $this->put(route('programa-riego.update', $programa), $newData);

        // Assert: Verificar que el programa fue actualizado en la base de datos
        $response->assertRedirect(route('programa-riego.index'));
        $this->assertDatabaseHas('programas_riego', array_merge(['id' => $programa->id], $newData));
    }

    /** @test */
    public function it_deletes_an_existing_programa_riego()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear un programa de riego
        $programa = ProgramaRiego::factory()->create();

        // Act: Hacer una solicitud DELETE para eliminar el programa de riego
        $response = $this->delete(route('programa-riego.destroy', $programa));

        // Assert: Verificar que el programa fue eliminado de la base de datos
        $response->assertRedirect(route('programa-riego.index'));
        $this->assertDatabaseMissing('programas_riego', ['id' => $programa->id]);
    }

    /** @test */
    public function it_sets_the_current_programa_riego()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear algunos programas de riego
        $programas = ProgramaRiego::factory()->count(2)->create();

        // Act: Hacer una solicitud POST para establecer el programa actual
        $response = $this->post(route('set.current-program'), [
            'programa_riego_id' => $programas->first()->id,
        ]);
        $programaActual = ProgramaActual::all();
        // Assert: Verificar que el programa actual fue establecido
        $response->assertRedirect(route('programa-riego.index'));
        $this->assertDatabaseCount('programa_actual', 1);
        $this->assertDatabaseHas('programa_actual', [
            'id' => $programaActual->first()->id,
            'programa_riego_id' => $programas->first()->id,
        ]);
    }

    /** @test */
    public function it_returns_error_when_setting_nonexistent_programa_riego()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // ID de programa de riego que no existe
        $nonExistentId = 999;

        // Act: Hacer una solicitud POST para establecer el programa actual con un ID inexistente
        $response = $this->post(route('set.current-program'), [
            'programa_riego_id' => $nonExistentId,
        ]);

        // Assert: Verificar que se redirige con un mensaje de error
        $response->assertRedirect(route('programa-riego.index'));
        $response->assertSessionHas('error', 'El Programa de Riego seleccionado no existe.');
        $this->assertDatabaseMissing('programa_actual', [
            'programa_riego_id' => $nonExistentId,
        ]);
    }
}
