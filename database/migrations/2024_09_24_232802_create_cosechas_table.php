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
        Schema::create('cosechas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->decimal('cantidad', 5, 2);
            $table->decimal('porcentaje', 5, 2);

            // Los nuevos campos para empaquetado
            $table->integer('cajas125')->default(0);  // Cantidad de cajas de 125
            $table->integer('cajas250')->default(0);  // Cantidad de cajas de 250
            $table->integer('cajas500')->default(0);  // Cantidad de cajas de 500

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cosechas');
    }
};
