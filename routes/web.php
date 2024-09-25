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

});


require __DIR__.'/auth.php';
