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
use \App\Http\Controllers\Web\ReporteRiegoController;
use App\Http\Controllers\DashboardController;


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


// Ruta para el dashboard
Route::get('/graph', [ReporteController::class, 'createGraph'])
    ->name('graph');

Route::get('/', function () {
    return view('welcome');
});

// Ruta para el dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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

Route::post('set/current-program', [ProgramaRiegoController::class, 'setCurrent'])->name('set.current-program');


Route::get('reportes', [ReporteRiegoController::class, 'index'])->name('reportes.index'); // Mostrar la lista de reportes
Route::get('reportes/create', [ReporteRiegoController::class, 'create'])->name('reportes.create'); // Mostrar el formulario para crear un nuevo reporte
Route::post('reportes', [ReporteRiegoController::class, 'store'])->name('reportes.store'); // Guardar un nuevo reporte
Route::get('reportes/{reporteRiego}', [ReporteRiegoController::class, 'show'])->name('reportes.show'); // Mostrar un reporte específico
Route::get('reportes/{reporteRiego}/edit', [ReporteRiegoController::class, 'edit'])->name('reportes.edit'); // Mostrar el formulario para editar un reporte
Route::put('reportes/{reporteRiego}', [ReporteRiegoController::class, 'update'])->name('reportes.update'); // Actualizar un reporte específico
Route::delete('reportes/{reporteRiego}', [ReporteRiegoController::class, 'destroy'])->name('reportes.destroy'); // Eliminar un reporte específico
});

require __DIR__.'/auth.php';
