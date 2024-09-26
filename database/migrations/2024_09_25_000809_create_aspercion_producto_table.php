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
        Schema::create('aspercion_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aspercion_id')->constrained('asperciones')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->integer('cantidad_de_producto'); // Agregar este campo
            $table->timestamps(); // Opcional, pero recomendado para seguimiento
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aspercion_producto');
    }
};
