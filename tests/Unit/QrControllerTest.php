<?php

namespace Tests\Feature;

use App\Http\Actions\QRCodeGenerator;
use App\Models\Cosecha;
use App\Models\Qr;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class QrControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_the_qr_index_page()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear una cosecha
        $cosecha = Cosecha::factory()->create();

        // Crear algunos QRs asociados a la cosecha
        $qrs = Qr::factory()->count(3)->create([
            'cosecha_id' => $cosecha->id,
        ]);

        // Act: Hacer una solicitud GET a la ruta de índice, pasando el ID de la cosecha
        $response = $this->get(route('qrs.index', ['cosecha_id' => $cosecha->id]));

        // Assert: Verificar que la respuesta sea exitosa y contenga los datos
        $response->assertStatus(200);
        $response->assertViewIs('cosechas.qrs.index');
        $response->assertViewHas('qrs', function ($collection) use ($qrs) {
            return $collection->count() === 3;
        });
    }

    /** @test */
    public function it_displays_the_create_qr_form()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear una cosecha
        $cosecha = Cosecha::factory()->create();

        // Act: Hacer una solicitud GET a la ruta de creación, pasando el ID de la cosecha
        $response = $this->get(route('qrs.create', ['cosecha_id' => $cosecha->id]));

        // Assert: Verificar que la respuesta sea exitosa y contiene la vista correcta
        $response->assertStatus(200);
        $response->assertViewIs('cosechas.qrs.create');
        $response->assertViewHas('cosecha', $cosecha);
    }

    /** @test */
    public function it_stores_a_new_qr()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Mockear el QRCodeGenerator para evitar ejecutar el script de Python
        $this->mock(QRCodeGenerator::class, function ($mock) {
            $mock->shouldReceive('generarImagenQR')->andReturnUsing(function ($uuidList, $baseUrl, $outputFile) {
                // Simular la generación de un archivo con el nombre esperado
                return 'qrcodes/' . $outputFile;
            });
        });

        // Crear una cosecha con cantidades específicas
        $cosecha = Cosecha::factory()->create([
            'cajas125' => 2,
            'cajas250' => 0,
            'cajas500' => 3,
        ]);

        // Act: Hacer una solicitud POST para almacenar el QR
        $response = $this->post(route('qrs.store'), [
            'cosecha_id' => $cosecha->id,
        ]);

        // Assert: Verificar que el QR fue creado y redirige correctamente
        $response->assertRedirect();
        
        // Verificar que el QR fue almacenado correctamente en la base de datos
        $this->assertDatabaseHas('qrs', [
            'cosecha_id' => $cosecha->id,
            'qr250' => null, // qr250 no debería tener valor
        ]);

        // Verificar que los campos qr125 y qr500 comienzan con 'qrcodes/'
        $qr = Qr::where('cosecha_id', $cosecha->id)->first();
        $this->assertStringStartsWith('qrcodes/', $qr->qr125);
        $this->assertStringStartsWith('qrcodes/', $qr->qr500);
    }

    /** @test */
    public function it_shows_a_qr_detail_page()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear un QR
        $qr = Qr::factory()->create();

        // Act: Hacer una solicitud GET a la ruta show
        $response = $this->get(route('qrs.show', $qr->id));

        // Assert: Verificar que la respuesta es exitosa y muestra la vista correcta
        $response->assertStatus(200);
        $response->assertViewIs('cosechas.qrs.show');
        $response->assertViewHas('qr', $qr);
    }

    /** @test */
    public function it_deletes_a_qr()
    {
        // Arrange: Crear un usuario y autenticarse
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear un QR
        $qr = Qr::factory()->create();

        // Act: Hacer una solicitud DELETE para eliminar el QR
        $response = $this->delete(route('qrs.destroy', $qr->id));

        // Assert: Verificar que el QR fue eliminado
        $response->assertRedirect(route('cosecha.index'));
        $this->assertDatabaseMissing('qrs', ['id' => $qr->id]);
    }
}
