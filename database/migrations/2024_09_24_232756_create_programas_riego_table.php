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
        Schema::create('programas_riego', function (Blueprint $table) {
            $table->id();

            // Campo veces_por_dia
            $table->integer('veces_por_dia')->default(0);

            // Campos de volumen1 a volumen14
            for ($i = 1; $i <= 14; $i++) {
                $table->integer("volumen{$i}")->default(0); // valor por defecto: 0
            }

            // Campos de fertilizante1_1 a fertilizante1_14 y fertilizante2_1 a fertilizante2_14
            for ($i = 1; $i <= 14; $i++) {
                $table->integer("fertilizante1_{$i}")->default(0); // valor por defecto: 0
                $table->integer("fertilizante2_{$i}")->default(0); // valor por defecto: 0
            }

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programas_riego');
    }
};
