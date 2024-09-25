<?php

namespace Tests\Feature;

use App\Models\Tratamiento;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TratamientoControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_the_tratamientos_index_page()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $tratamientos = Tratamiento::factory()->count(3)->create();

        // Act
        $response = $this->get(route('tratamiento.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('tratamientos.index');
        $response->assertViewHas('tratamientos');
    }

    /** @test */
    public function it_displays_the_create_tratamiento_form()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act
        $response = $this->get(route('tratamiento.create'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('tratamientos.create');
        $response->assertViewHasAll(['productos', 'usuarios']);
    }

    /** @test */
    public function it_stores_a_new_tratamiento_successfully()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $productos = Producto::factory()->count(2)->create();

        $data = [
            'agronomo' => 'Ing. Juan Pérez',
            'cantidad' => 50.5,
            'frecuencia' => 'Semanal',
            'diagnostico' => 'Diagnóstico de prueba',
            'notas' => 'Notas adicionales',
            'user_id' => $user->id,
            'productos' => $productos->pluck('id')->toArray(),
        ];

        // Act
        $response = $this->post(route('tratamiento.store'), $data);

        // Assert
        $response->assertRedirect(route('tratamiento.index'));
        $this->assertDatabaseHas('tratamientos', [
            'agronomo' => 'Ing. Juan Pérez',
            'cantidad' => 50.5,
        ]);

        $tratamiento = Tratamiento::first();
        $this->assertEquals($user->id, $tratamiento->user_id);
        $this->assertCount(2, $tratamiento->productos);
    }

    /** @test */
    public function it_fails_to_store_tratamiento_due_to_validation_errors()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            // 'agronomo' => 'Ing. Juan Pérez', // Missing agronomo
            'cantidad' => -10, // Invalid quantity
            'frecuencia' => '', // Required field
            'diagnostico' => '', // Required field
            'user_id' => 9999, // Non-existent user
            'productos' => 'not_an_array', // Should be an array
        ];

        // Act
        $response = $this->from(route('tratamiento.create'))->post(route('tratamiento.store'), $data);

        // Assert
        $response->assertRedirect(route('tratamiento.create'));
        $response->assertSessionHasErrors([
            'agronomo',
            'cantidad',
            'frecuencia',
            'diagnostico',
            'user_id',
            'productos',
        ]);
        $this->assertDatabaseCount('tratamientos', 0);
    }

    /** @test */
    public function it_displays_an_existing_tratamiento()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $tratamiento = Tratamiento::factory()->create();

        // Act
        $response = $this->get(route('tratamiento.show', $tratamiento));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('tratamientos.show');
        $response->assertViewHas('tratamiento', $tratamiento);
    }

    /** @test */
    public function it_displays_the_edit_form()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $tratamiento = Tratamiento::factory()->create(['user_id' => $user->id]);
        $productos = Producto::factory()->count(3)->create();

        // Act
        $response = $this->get(route('tratamiento.edit', $tratamiento));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('tratamientos.edit');
        $response->assertViewHasAll(['tratamiento', 'productos', 'usuarios']);
    }

    /** @test */
    public function it_updates_an_existing_tratamiento_successfully()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $tratamiento = Tratamiento::factory()->create(['user_id' => $user->id]);
        $productos = Producto::factory()->count(2)->create();

        $updateData = [
            'agronomo' => 'Ing. María López',
            'cantidad' => 75.0,
            'frecuencia' => 'Mensual',
            'diagnostico' => 'Nuevo diagnóstico',
            'notas' => 'Actualización de notas',
            'user_id' => $user->id,
            'productos' => $productos->pluck('id')->toArray(),
        ];

        // Act
        $response = $this->put(route('tratamiento.update', $tratamiento), $updateData);

        // Assert
        $response->assertRedirect(route('tratamiento.index'));
        $this->assertDatabaseHas('tratamientos', [
            'id' => $tratamiento->id,
            'agronomo' => 'Ing. María López',
            'cantidad' => 75.0,
        ]);

        $tratamiento->refresh();
        $this->assertCount(2, $tratamiento->productos);
    }

    /** @test */
    public function it_fails_to_update_tratamiento_due_to_validation_errors()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $tratamiento = Tratamiento::factory()->create(['user_id' => $user->id]);

        $updateData = [
            'agronomo' => '', // Required
            'cantidad' => 'invalid', // Should be numeric
            'frecuencia' => '', // Required
            'diagnostico' => '', // Required
            'user_id' => 9999, // Non-existent user
            'productos' => 'not_an_array', // Should be an array
        ];

        // Act
        $response = $this->from(route('tratamiento.edit', $tratamiento))->put(route('tratamiento.update', $tratamiento), $updateData);

        // Assert
        $response->assertRedirect(route('tratamiento.edit', $tratamiento));
        $response->assertSessionHasErrors([
            'agronomo',
            'cantidad',
            'frecuencia',
            'diagnostico',
            'user_id',
            'productos',
        ]);
    }

    /** @test */
    public function it_deletes_an_existing_tratamiento()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $tratamiento = Tratamiento::factory()->withProductos(2)->create(['user_id' => $user->id]);

        // Act
        $response = $this->delete(route('tratamiento.destroy', $tratamiento));

        // Assert
        $response->assertRedirect(route('tratamiento.index'));
        $this->assertDatabaseMissing('tratamientos', ['id' => $tratamiento->id]);
    }

    /** @test */
    public function it_denies_access_to_unauthenticated_users()
    {
        // Arrange
        $tratamiento = Tratamiento::factory()->create();

        // Act
        $response = $this->get(route('tratamiento.index'));

        // Assert
        $response->assertRedirect(route('login'));
    }
}
