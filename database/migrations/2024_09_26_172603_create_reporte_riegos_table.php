<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reporte_riego', function (Blueprint $table) {
            $table->id();

            // Campos de volumen, tiempo y mensaje para los 14 surcos
            for ($i = 1; $i <= 14; $i++) {
                $table->float("volumen{$i}")->nullable();
                $table->time("tiempo{$i}")->nullable();
                $table->string("mensaje{$i}")->nullable();
            }

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte_riego');
    }
};
