<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\ReporteRiego;
use Illuminate\Foundation\Testing\RefreshDatabase;
use phpmock\phpunit\PHPMock;
use Illuminate\Support\Facades\Storage;

class ReporteControllerTest extends TestCase
{
    use RefreshDatabase, PHPMock;

    /** @test */
    public function it_generates_graph_and_redirects_with_success_message()
    {
        // Simular almacenamiento
        Storage::fake('public');

        // Crear reportes de riego de prueba
        ReporteRiego::factory()->count(10)->create();

        // Definir la ruta de la imagen
        $output_path = storage_path('app/public/graphs');
        $image_file = $output_path . '/graficas_reporte_riego.png';

        // Mockear file_exists para que retorne true en la primera llamada (antes de unlink)
        // y true en la segunda llamada (después de shell_exec)
        $fileExistsMock = $this->getFunctionMock('App\Http\Controllers', "file_exists");
        $fileExistsMock->expects($this->exactly(2))
                       ->with($image_file)
                       ->willReturnOnConsecutiveCalls(true, true);

        // Mockear unlink para esperar solo una llamada
        $unlinkMock = $this->getFunctionMock('App\Http\Controllers', "unlink");
        $unlinkMock->expects($this->once())
                   ->with($image_file)
                   ->willReturn(true);

        // Mockear shell_exec para simular la ejecución del script de Python
        $shellExecMock = $this->getFunctionMock('App\Http\Controllers', "shell_exec");
        $shellExecMock->expects($this->once())
                      ->with($this->stringContains('python3'))
                      ->willReturn('Script ejecutado correctamente');

        // Opcional: Simular que el script de Python crea el archivo
        // Al usar Storage::fake, puedes crear el archivo después de shell_exec
        // Pero como estamos mockeando file_exists, esto no es necesario

        // Hacer una solicitud GET al método createGraph
        $response = $this->get(route('graph'));

        // Verificar que se redirige al dashboard con mensaje de éxito
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Gráfica generada exitosamente.');
    }

    /** @test */
    public function it_redirects_with_error_when_graph_generation_fails()
    {
        // Simular almacenamiento
        Storage::fake('public');

        // Crear reportes de riego de prueba
        ReporteRiego::factory()->count(10)->create();

        // Definir la ruta de la imagen
        $output_path = storage_path('app/public/graphs');
        $image_file = $output_path . '/graficas_reporte_riego.png';

        // Mockear file_exists para que retorne true en la primera llamada (antes de unlink)
        // y false en la segunda llamada (después de shell_exec fallido)
        $fileExistsMock = $this->getFunctionMock('App\Http\Controllers', "file_exists");
        $fileExistsMock->expects($this->exactly(2))
                       ->with($image_file)
                       ->willReturnOnConsecutiveCalls(true, false);

        // Mockear unlink para esperar solo una llamada
        $unlinkMock = $this->getFunctionMock('App\Http\Controllers', "unlink");
        $unlinkMock->expects($this->once())
                   ->with($image_file)
                   ->willReturn(true);

        // Mockear shell_exec para simular un error en la ejecución del script de Python
        $shellExecMock = $this->getFunctionMock('App\Http\Controllers', "shell_exec");
        $shellExecMock->expects($this->once())
                      ->with($this->stringContains('python3'))
                      ->willReturn('Error en el script');

        // Hacer una solicitud GET al método createGraph
        $response = $this->get(route('graph'));

        // Verificar que se redirige al dashboard con mensaje de error
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error', 'Error al generar la gráfica.');
    }
}
