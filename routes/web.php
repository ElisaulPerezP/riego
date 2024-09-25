<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ProgramaRiegoController;
use App\Http\Controllers\CosechaController;
use App\Http\Controllers\AspercionController;
use App\Http\Controllers\TratamientoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\QrController;
use App\Http\Actions\QRCodeGenerator;
use Illuminate\Support\Facades\File;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Rutas para Reporte
Route::resource('reporte', ReporteController::class);

// Rutas para Programa de Riego
Route::resource('programa-riego', ProgramaRiegoController::class);

// Rutas para Reportar Cosecha
Route::resource('cosecha', CosechaController::class);

// Rutas para Reportar Aspercion
Route::resource('aspercion', AspercionController::class);

// Rutas para Reportar Tratamiento
Route::resource('tratamiento', TratamientoController::class);

Route::resource('productos', ProductoController::class);

Route::resource('stocks', StockController::class);

Route::get('/qrs', [QrController::class, 'index'])->name('qrs.index');

// routes/web.php

Route::get('qrs/create', [QrController::class, 'create'])->name('qrs.create');
Route::post('qrs', [QrController::class, 'store'])->name('qrs.store');
Route::get('qrs/{qr}', [QrController::class, 'show'])->name('qrs.show');


Route::resource('qrs', QrController::class);

Route::get('/generate-qr', function () {
    // Obtener la URL desde el request
    $url = "https://arandanosdemipueblo.online/a79a6d73-5d23-4d53-93b5-631e55dec808";
    
    // Verificar si se proporcionó la URL
    if (!$url) {
        return response()->json(['error' => 'No se proporcionó una URL'], 400);
    }
    
    // Generar el nombre y la ruta de salida del archivo QR
    $nombreArchivo = 'codigo_qr_' . time() . '.jpg';
    $rutaSalida = public_path('qrcodes/' . $nombreArchivo);

    // Crear la carpeta si no existe
    if (!File::exists(public_path('qrcodes'))) {
        File::makeDirectory(public_path('qrcodes'), 0755, true);
    }

    // Crear una instancia de QRCodeGenerator y generar el QR
    $qrGenerator = new QRCodeGenerator();
    $qrGenerator->generar($url, $rutaSalida);

    // Devolver la imagen generada
    return response()->download($rutaSalida, $nombreArchivo, [
        'Content-Type' => 'image/jpeg'
    ]);
});


});


require __DIR__.'/auth.php';
