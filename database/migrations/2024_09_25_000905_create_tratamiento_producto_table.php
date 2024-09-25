<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tratamiento_producto', function (Blueprint $table) {
            $table->id();  // Identificador único de la tabla pivote
            $table->foreignId('tratamiento_id')->constrained('tratamientos')->onDelete('cascade');  // Relación con tratamientos
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');  // Relación con productos
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tratamiento_producto');
    }
};
