<?php

namespace Tests\Feature;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_the_product_index_page()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear algunos productos
        $productos = Producto::factory()->count(3)->create();

        // Act: Hacer una solicitud GET a la ruta de índice
        $response = $this->get(route('productos.index'));

        // Assert: Verificar que la respuesta sea exitosa y contenga los productos
        $response->assertStatus(200);
        $response->assertViewIs('productos.index');
        $response->assertViewHas('productos', function ($collection) use ($productos) {
            return $collection->count() === 3;
        });
    }

    /** @test */
    public function it_displays_the_create_product_form()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: Hacer una solicitud GET a la ruta de creación
        $response = $this->get(route('productos.create'));

        // Assert: Verificar que la respuesta sea exitosa y contenga el formulario de creación
        $response->assertStatus(200);
        $response->assertViewIs('productos.create');
    }

    /** @test */
    public function it_stores_a_new_product()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: Hacer una solicitud POST para almacenar el producto
        $response = $this->post(route('productos.store'), [
            'nombre' => 'Producto de prueba',
            'descripcion' => 'Descripción del producto de prueba',
            'precio' => 99.99,
            'cantidad' => 10,
            'fecha_vencimiento' => now()->addMonth()->toDateString(),
            'responsable' => 'Responsable de prueba',
            'telefono_emergencia' => '123456789',
        ]);

        // Assert: Verificar que el producto fue creado en la base de datos
        $response->assertRedirect(route('productos.index'));
        $this->assertDatabaseHas('productos', [
            'nombre' => 'Producto de prueba',
            'descripcion' => 'Descripción del producto de prueba',
        ]);
    }

    /** @test */
    public function it_displays_the_product_detail_page()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear un producto
        $producto = Producto::factory()->create();

        // Act: Hacer una solicitud GET a la ruta de detalle del producto
        $response = $this->get(route('productos.show', $producto->id));

        // Assert: Verificar que la respuesta es exitosa y muestra los detalles del producto
        $response->assertStatus(200);
        $response->assertViewIs('productos.show');
        $response->assertViewHas('producto', $producto);
    }

    /** @test */
    public function it_displays_the_edit_product_form()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear un producto
        $producto = Producto::factory()->create();

        // Act: Hacer una solicitud GET a la ruta de edición del producto
        $response = $this->get(route('productos.edit', $producto->id));

        // Assert: Verificar que la respuesta es exitosa y contiene el formulario de edición
        $response->assertStatus(200);
        $response->assertViewIs('productos.edit');
        $response->assertViewHas('producto', $producto);
    }

    /** @test */
    public function it_updates_a_product()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear un producto
        $producto = Producto::factory()->create();

        // Act: Hacer una solicitud PUT para actualizar el producto
        $response = $this->put(route('productos.update', $producto->id), [
            'nombre' => 'Producto actualizado',
            'descripcion' => 'Descripción actualizada',
            'precio' => 120.50,
            'cantidad' => 5,
            'fecha_vencimiento' => now()->addYear()->toDateString(),
            'responsable' => 'Nuevo Responsable',
            'telefono_emergencia' => '987654321',
        ]);

        // Assert: Verificar que el producto fue actualizado en la base de datos
        $response->assertRedirect(route('productos.index'));
        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'nombre' => 'Producto actualizado',
            'descripcion' => 'Descripción actualizada',
        ]);
    }

    /** @test */
    public function it_deletes_a_product()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear un producto
        $producto = Producto::factory()->create();

        // Act: Hacer una solicitud DELETE para eliminar el producto
        $response = $this->delete(route('productos.destroy', $producto->id));

        // Assert: Verificar que el producto fue eliminado de la base de datos
        $response->assertRedirect(route('productos.index'));
        $this->assertDatabaseMissing('productos', ['id' => $producto->id]);
    }
}
