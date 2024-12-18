<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aspercion;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use App\Http\Actions\RestarProductoAStock;
use App\Http\Actions\AjustarStockProducto;
use App\Models\Stock;


class AspercionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    
    /** @test */
/** @test */
public function it_displays_a_list_of_asperciones()
{
    // Arrange: Create some asperciones with related products and users
    $user = User::factory()->create();
    $productos = Producto::factory()->count(3)->create();
    $this->actingAs($user);

    $aspercion = Aspercion::factory()
        ->for($user)
        ->create();

    $aspercion->productos()->attach($productos->pluck('id'), ['cantidad_de_producto' => 10]);

    // Act: Make a GET request to the index route
    $response = $this->get(route('aspercion.index'));

    // Assert: Check if the response is successful and contains the asperciones
    $response->assertStatus(200);
    $response->assertViewIs('asperciones.index');
    $response->assertViewHas('asperciones', function ($asperciones) use ($aspercion) {
        return $asperciones->contains($aspercion);
    });
}

/** @test */
public function it_displays_the_create_form()
{
    // Arrange: Create some users and products
    $usuarios = User::factory()->count(2)->create();
    $productos = Producto::factory()->count(3)->create();
    $user = User::factory()->create();
    $this->actingAs($user);
    // Act: Make a GET request to the create route
    $response = $this->get(route('aspercion.create'));

    // Assert: Check if the response is successful and contains the required data
    $response->assertStatus(200);
    $response->assertViewIs('asperciones.create');
    $response->assertViewHasAll(['usuarios', 'productos']);
}
/** @test */
public function it_stores_a_new_aspercion_successfully()
{
    // Arrange: Create a user and products
    $user = User::factory()->create();
    $productos = Producto::factory()->count(2)->create();

    // Crear stock para cada producto
    foreach ($productos as $producto) {
        Stock::create([
            'producto_id' => $producto->id,
            'dias_para_vencimiento' => 1,
            'cantidad_en_stock' => 100,
        ]);
    }

    $this->actingAs($user);

    $data = [
        'fecha' => now()->toDateString(),
        'hora' => now()->format('H:i'),
        'volumen' => 100.5,
        'tipo_aspercion' => 'Tipo A',
        'responsable' => 'Juan Pérez',
        'user_id' => $user->id,
        'productos' => $productos->pluck('id')->toArray(),
        'cantidades' => [30, 20], // Cantidades usadas
    ];

    // Act: Make a POST request to store the aspercion
    $response = $this->post(route('aspercion.store'), $data);

    // Assert: Check if the aspercion was created and redirected correctly
    $response->assertRedirect(route('aspercion.index'));
    $this->assertDatabaseHas('asperciones', [
        'volumen' => 100.5,
        'responsable' => 'Juan Pérez',
    ]);

    $aspercion = Aspercion::first();
    $this->assertEquals($user->id, $aspercion->user_id);
    $this->assertCount(2, $aspercion->productos);

    // Verificar que las cantidades en la tabla pivote son correctas
    foreach ($productos as $index => $producto) {
        $this->assertDatabaseHas('aspercion_producto', [
            'aspercion_id' => $aspercion->id,
            'producto_id' => $producto->id,
            'cantidad_de_producto' => $data['cantidades'][$index],
        ]);
    }

    // Verificar que el stock se redujo correctamente
    foreach ($productos as $index => $producto) {
        $stock = Stock::where('producto_id', $producto->id)->first();
        $expectedStock = 100 - $data['cantidades'][$index];
        $this->assertEquals($expectedStock, $stock->cantidad_en_stock);
    }
}

/** @test */
/** @test */
/** @test */
public function it_fails_to_store_aspercion_due_to_validation_errors()
{
    // Arrange: Provide incomplete data
    $data = [
        // 'fecha' is missing
        'hora' => '25:61', // Invalid time
        'volumen' => 'invalid', // Should be numeric
        // 'tipo_aspercion' is missing
        'responsable' => '', // Required field
        'user_id' => 9999, // Non-existent user
        'productos' => 'not_an_array', // Should be an array
        'cantidades' => 'invalid', // Should be an array
    ];
    $user = User::factory()->create();
    $this->actingAs($user);

    // Act: Make a POST request to store the aspercion
    $response = $this->from(route('aspercion.create'))->post(route('aspercion.store'), $data);

    // Assert: Check if the validation errors are returned
    $response->assertRedirect(route('aspercion.create'));
    $response->assertSessionHasErrors([
        'fecha',
        'hora',
        'volumen',
        'tipo_aspercion',
        'responsable',
        'user_id',
        'productos',
        'cantidades',
    ]);
    $this->assertDatabaseCount('asperciones', 0);
}


/** @test */
public function it_displays_an_existing_aspercion()
{
    // Arrange: Create an aspercion
    $user = User::factory()->create();
    $aspercion = Aspercion::factory()->for($user)->create();
    $user = User::factory()->create();
    $this->actingAs($user);
    // Act: Make a GET request to show the aspercion
    $response = $this->get(route('aspercion.show', $aspercion));

    // Assert: Check if the response is successful and contains the aspercion
    $response->assertStatus(200);
    $response->assertViewIs('asperciones.show');
    $response->assertViewHas('aspercion', $aspercion);
}
/** @test */
public function it_returns_404_when_aspercion_not_found()
{
    $user = User::factory()->create();
    $this->actingAs($user);
    // Act: Make a GET request with a non-existent aspercion ID
    $response = $this->get(route('aspercion.show', ['aspercion' => 999]));

    // Assert: Check if a 404 error is returned
    $response->assertStatus(404);
}
/** @test */
public function it_displays_the_edit_form()
{
    // Arrange: Create an aspercion, users, and products
    $aspercion = Aspercion::factory()->create();
    $usuarios = User::factory()->count(2)->create();
    $productos = Producto::factory()->count(3)->create();
    $user = User::factory()->create();
    $this->actingAs($user);
    // Act: Make a GET request to the edit route
    $response = $this->get(route('aspercion.edit', $aspercion));

    // Assert: Check if the response is successful and contains the required data
    $response->assertStatus(200);
    $response->assertViewIs('asperciones.edit');
    $response->assertViewHasAll(['aspercion', 'usuarios', 'productos']);
}
/** @test */
public function it_returns_404_when_editing_non_existent_aspercion()
{
    $user = User::factory()->create();
    $this->actingAs($user);
    // Act: Make a GET request with a non-existent aspercion ID
    $response = $this->get(route('aspercion.edit', ['aspercion' => 999]));

    // Assert: Check if a 404 error is returned
    $response->assertStatus(404);
}
/** @test */
/** @test */
/** @test */
public function it_updates_an_existing_aspercion_successfully()
{
    // Arrange: Create an aspercion, user, and products
    $user = User::factory()->create();
    $productos = Producto::factory()->count(2)->create();

    // Crear stock para cada producto
    foreach ($productos as $producto) {
        Stock::create([
            'producto_id' => $producto->id,
            'dias_para_vencimiento' => 1,
            'cantidad_en_stock' => 100,
        ]);
    }

    $aspercion = Aspercion::factory()->create([
        'user_id' => $user->id,
    ]);

    // Asociar productos con cantidades iniciales
    $aspercion->productos()->attach([
        $productos[0]->id => ['cantidad_de_producto' => 30],
        $productos[1]->id => ['cantidad_de_producto' => 20],
    ]);

    // Reducir el stock inicial
    foreach ($productos as $index => $producto) {
        $stock = Stock::where('producto_id', $producto->id)->first();
        $stock->cantidad_en_stock -= ($index == 0 ? 30 : 20);
        $stock->save();
    }

    $this->actingAs($user);

    $updateData = [
        'fecha' => now()->addDay()->toDateString(),
        'hora' => '10:30',
        'volumen' => 200,
        'tipo_aspercion' => 'Tipo B',
        'responsable' => 'María López',
        'user_id' => $user->id,
        'productos' => $productos->pluck('id')->toArray(),
        'cantidades' => [40, 10], // Nuevas cantidades
    ];

    // Act: Make a PUT request to update the aspercion
    $response = $this->put(route('aspercion.update', $aspercion), $updateData);

    // Assert: Check if the aspercion was updated and redirected correctly
    $response->assertRedirect(route('aspercion.index'));
    $this->assertDatabaseHas('asperciones', [
        'id' => $aspercion->id,
        'volumen' => 200,
        'responsable' => 'María López',
    ]);

    $aspercion->refresh();
    $this->assertEquals($user->id, $aspercion->user_id);

    // Verificar que las cantidades en la tabla pivote son correctas
    foreach ($productos as $index => $producto) {
        $this->assertDatabaseHas('aspercion_producto', [
            'aspercion_id' => $aspercion->id,
            'producto_id' => $producto->id,
            'cantidad_de_producto' => $updateData['cantidades'][$index],
        ]);
    }

    // Verificar que el stock se ajustó correctamente
    foreach ($productos as $index => $producto) {
        $stock = Stock::where('producto_id', $producto->id)->first();

        // Calcular el stock esperado
        $cantidadAnterior = ($index == 0) ? 30 : 20;
        $cantidadNueva = $updateData['cantidades'][$index];
        $stockEsperado = 100 - $cantidadNueva;

        $this->assertEquals($stockEsperado, $stock->cantidad_en_stock);
    }
}

/** @test */
public function it_fails_to_update_aspercion_due_to_validation_errors()
{
    // Arrange: Create an aspercion
    $aspercion = Aspercion::factory()->create();

    $updateData = [
        'fecha' => 'invalid-date',
        'hora' => '25:61',
        'volumen' => 'non-numeric',
        'tipo_aspercion' => '', // Required field
        'responsable' => '', // Required field
        'user_id' => 9999, // Non-existent user
        'productos' => 'not-an-array', // Should be an array
    ];
    $user = User::factory()->create();
    $this->actingAs($user);
    // Act: Make a PUT request to update the aspercion
    $response = $this->from(route('aspercion.edit', $aspercion))->put(route('aspercion.update', $aspercion), $updateData);

    // Assert: Check if validation errors are returned
    $response->assertRedirect(route('aspercion.edit', $aspercion));
    $response->assertSessionHasErrors([
        'fecha',
        'hora',
        'volumen',
        'tipo_aspercion',
        'responsable',
        'user_id',
        'productos',
    ]);
}
/** @test */
public function it_deletes_an_existing_aspercion()
{
    $aspercion = Aspercion::factory()->create();
    $productos = Producto::factory()->count(2)->create();
    
    // Proporcionar el valor para 'cantidad_de_producto' en la tabla pivote
    $syncData = [];
    foreach ($productos as $producto) {
        $syncData[$producto->id] = ['cantidad_de_producto' => 10]; // Cantidad ficticia para el test
    }
    
    $aspercion->productos()->attach($syncData);
    
    $user = User::factory()->create();
    $this->actingAs($user);
    // Act: Make a DELETE request to destroy the aspercion
    $response = $this->delete(route('aspercion.destroy', $aspercion));

    // Assert: Check if the aspercion was deleted
    $response->assertRedirect(route('aspercion.index'));
    $this->assertDatabaseMissing('asperciones', ['id' => $aspercion->id]);
    $this->assertDatabaseMissing('aspercion_producto', ['aspercion_id' => $aspercion->id]);
}
/** @test */
public function it_returns_404_when_deleting_non_existent_aspercion()
{
    $user = User::factory()->create();
    $this->actingAs($user);
    // Act: Make a DELETE request with a non-existent aspercion ID
    $response = $this->delete(route('aspercion.destroy', ['aspercion' => 999]));

    // Assert: Check if a 404 error is returned
    $response->assertStatus(404);
}
/** @test */
public function it_denies_access_to_unauthenticated_users()
{
    // Arrange: Create an aspercion
    $aspercion = Aspercion::factory()->create();

    // Act: Make a GET request without authentication
    $response = $this->get(route('aspercion.edit', $aspercion));

    // Assert: Should redirect to login
    $response->assertRedirect(route('login'));
}

/** @test */
public function it_reduces_stock_correctly_using_restar_producto_a_stock()
{
    // Arrange
    $producto = Producto::factory()->create();
    Stock::create([
        'producto_id' => $producto->id,
        'dias_para_vencimiento' => 1,
        'cantidad_en_stock' => 100,
    ]);

    $syncData = [
        $producto->id => ['cantidad_de_producto' => 30],
    ];

    // Act
    $action = new RestarProductoAStock();
    $action->ejecutar($syncData);

    // Assert
    $stock = Stock::where('producto_id', $producto->id)->first();
    $this->assertEquals(70, $stock->cantidad_en_stock);
}

/** @test */
public function it_throws_exception_when_not_enough_stock_in_restar_producto_a_stock()
{
    // Arrange
    $producto = Producto::factory()->create();
    Stock::create([
        'producto_id' => $producto->id,
        'dias_para_vencimiento' => 1,
        'cantidad_en_stock' => 50,
    ]);

    $syncData = [
        $producto->id => ['cantidad_de_producto' => 60], // Más de lo disponible
    ];

    // Act & Assert
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage("No hay suficiente stock disponible para el producto: {$producto->nombre}");

    $action = new RestarProductoAStock();
    $action->ejecutar($syncData);
}


}