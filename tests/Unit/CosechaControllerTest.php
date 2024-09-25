<?php

namespace Tests\Feature;

use App\Models\Cosecha;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CosechaControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_the_cosecha_index_page()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear algunas cosechas
        $cosechas = Cosecha::factory()->count(3)->create();

        // Act: Hacer una solicitud GET a la ruta de índice
        $response = $this->get(route('cosecha.index'));

        // Assert: Verificar que la respuesta sea exitosa y contenga los datos
        $response->assertStatus(200);
        $response->assertViewIs('cosechas.index');
        $response->assertViewHas('cosechas');
    }

    /** @test */
    public function it_displays_the_create_cosecha_form()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: Hacer una solicitud GET a la ruta de creación
        $response = $this->get(route('cosecha.create'));

        // Assert: Verificar que la respuesta sea exitosa y contenga los datos
        $response->assertStatus(200);
        $response->assertViewIs('cosechas.create');
        $response->assertViewHas('usuarios');
    }

    /** @test */
    public function it_stores_a_new_cosecha_successfully()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Datos para crear una nueva cosecha
        $data = [
            'fecha' => '2023-10-01',
            'cantidad' => 123.45,
            'porcentaje' => 85.5,
            'cajas125' => 10,
            'cajas250' => 5,
            'cajas500' => 2,
            'user_id' => $user->id,
        ];

        // Act: Enviar una solicitud POST a la ruta de almacenamiento
        $response = $this->post(route('cosecha.store'), $data);

        // Assert: Verificar que la cosecha se creó y se redirecciona correctamente
        $response->assertRedirect(route('cosecha.index'));
        $this->assertDatabaseHas('cosechas', [
            'fecha' => '2023-10-01',
            'cantidad' => 123.45,
            'porcentaje' => 85.5,
            'cajas125' => 10,
            'cajas250' => 5,
            'cajas500' => 2,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function it_fails_to_store_cosecha_due_to_validation_errors()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Datos inválidos
        $data = [
            'fecha' => 'invalid-date',
            'cantidad' => -10,
            'porcentaje' => 150,
            'cajas125' => -1,
            'cajas250' => 'not a number',
            'cajas500' => null,
            'user_id' => 9999,
        ];

        // Act: Enviar una solicitud POST con datos inválidos
        $response = $this->from(route('cosecha.create'))->post(route('cosecha.store'), $data);

        // Assert: Verificar que hay errores de validación y no se creó la cosecha
        $response->assertRedirect(route('cosecha.create'));
        $response->assertSessionHasErrors([
            'fecha',
            'cantidad',
            'porcentaje',
            'cajas125',
            'cajas250',
            'cajas500',
            'user_id',
        ]);

        $this->assertDatabaseCount('cosechas', 0);
    }

    /** @test */
    public function it_displays_a_specific_cosecha()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear una cosecha
        $cosecha = Cosecha::factory()->create();

        // Act: Hacer una solicitud GET a la ruta de mostrar
        $response = $this->get(route('cosecha.show', $cosecha));

        // Assert: Verificar que la vista se carga correctamente con la cosecha
        $response->assertStatus(200);
        $response->assertViewIs('cosechas.show');
        $response->assertViewHas('cosecha', $cosecha);
    }

    /** @test */
    public function it_displays_the_edit_cosecha_form()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear una cosecha
        $cosecha = Cosecha::factory()->create();

        // Act: Hacer una solicitud GET a la ruta de edición
        $response = $this->get(route('cosecha.edit', $cosecha));

        // Assert: Verificar que la vista se carga correctamente con la cosecha y los usuarios
        $response->assertStatus(200);
        $response->assertViewIs('cosechas.edit');
        $response->assertViewHas('cosecha', $cosecha);
        $response->assertViewHas('usuarios');
    }

    /** @test */
    public function it_updates_a_cosecha_successfully()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear una cosecha
        $cosecha = Cosecha::factory()->create(['user_id' => $user->id]);

        // Datos para actualizar la cosecha
        $data = [
            'fecha' => '2023-10-02',
            'cantidad' => 200,
            'porcentaje' => 90,
            'cajas125' => 20,
            'cajas250' => 10,
            'cajas500' => 5,
            'user_id' => $user->id,
        ];

        // Act: Enviar una solicitud PUT a la ruta de actualización
        $response = $this->put(route('cosecha.update', $cosecha), $data);

        // Assert: Verificar que la cosecha se actualizó y se redirecciona correctamente
        $response->assertRedirect(route('cosecha.index'));
        $this->assertDatabaseHas('cosechas', [
            'id' => $cosecha->id,
            'fecha' => '2023-10-02',
            'cantidad' => 200,
            'porcentaje' => 90,
            'cajas125' => 20,
            'cajas250' => 10,
            'cajas500' => 5,
        ]);
    }

    /** @test */
    public function it_fails_to_update_cosecha_due_to_validation_errors()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear una cosecha
        $cosecha = Cosecha::factory()->create(['user_id' => $user->id]);

        // Datos inválidos para actualización
        $data = [
            'fecha' => 'invalid-date',
            'cantidad' => 'not a number',
            'porcentaje' => 150,
            'cajas125' => -5,
            'cajas250' => 'invalid',
            'cajas500' => null,
            'user_id' => 9999,
        ];

        // Act: Enviar una solicitud PUT con datos inválidos
        $response = $this->from(route('cosecha.edit', $cosecha))->put(route('cosecha.update', $cosecha), $data);

        // Assert: Verificar que hay errores de validación y la cosecha no se actualizó
        $response->assertRedirect(route('cosecha.edit', $cosecha));
        $response->assertSessionHasErrors([
            'fecha',
            'cantidad',
            'porcentaje',
            'cajas125',
            'cajas250',
            'cajas500',
            'user_id',
        ]);
    }

    /** @test */
    public function it_deletes_a_cosecha_successfully()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear una cosecha
        $cosecha = Cosecha::factory()->create(['user_id' => $user->id]);

        // Act: Enviar una solicitud DELETE a la ruta de eliminación
        $response = $this->delete(route('cosecha.destroy', $cosecha));

        // Assert: Verificar que la cosecha se eliminó y se redirecciona correctamente
        $response->assertRedirect(route('cosecha.index'));
        $this->assertDatabaseMissing('cosechas', [
            'id' => $cosecha->id,
        ]);
    }

    /** @test */
    public function it_denies_access_to_unauthenticated_users()
    {
        // Arrange: No autenticamos a ningún usuario

        // Act: Intentar acceder a una ruta protegida
        $response = $this->get(route('cosecha.index'));

        // Assert: Verificar que se redirecciona al login
        $response->assertRedirect(route('login'));
    }
}
