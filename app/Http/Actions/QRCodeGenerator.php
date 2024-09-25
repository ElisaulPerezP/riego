<?php

namespace App\Http\Actions;

use Illuminate\Support\Facades\File;

class QRCodeGenerator
{
    /**
     * Ejecuta el script de Python para generar la imagen de QR.
     *
     * @param array $uuids Lista de UUIDs.
     * @param string $baseUrl URL base para formar los enlaces en los QR.
     * @param string $outputFile Nombre del archivo de imagen de salida.
     * @return string Ruta relativa al archivo de imagen generado.
     * @throws \Exception Si la imagen no se genera correctamente.
     */
    public function generarImagenQR(array $uuids, $baseUrl, $outputFile)
    {
        // Ruta al script de Python
        $scriptPath = base_path('app/Http/Actions/QrGenerator.py');

        // Convertir la lista de UUIDs en una cadena separada por comas
        $uuidString = implode(',', $uuids);

        // Asegurarse de que la carpeta 'public/qrcodes' existe
        $outputDir = public_path('qrcodes');
        if (!File::exists($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
        }

        // Ruta completa al archivo de imagen de salida
        $outputImagePath = $outputDir . '/' . $outputFile;

        // Comando para ejecutar el script de Python
        $command = escapeshellcmd("python3 $scriptPath '$uuidString' '$baseUrl' '$outputImagePath'");

        // Ejecutar el comando
        $output = shell_exec($command);

        // Verificar si la imagen fue generada
        if (File::exists($outputImagePath)) {
            // Devolver la ruta relativa para acceder al archivo desde la web
            return 'qrcodes/' . $outputFile;
        } else {
            throw new \Exception('No se pudo generar la imagen QR.');
        }
    }
}
