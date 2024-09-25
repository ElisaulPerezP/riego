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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->decimal('precio', 8, 2);
            $table->integer('cantidad');
            $table->date('fecha_vencimiento');
            $table->string('responsable');
            $table->integer('tiempo_retiro')->nullable();
            $table->integer('tiempo_exclusion')->nullable();
            $table->text('afectacion')->nullable();
            $table->text('tratamiento_intoxicacion')->nullable();
            $table->string('telefono_emergencia')->nullable();
            // Campos adicionales sugeridos
            $table->string('numero_registro')->nullable();
            $table->text('composicion_quimica')->nullable();
            $table->string('clasificacion_toxicidad')->nullable();
            $table->text('instrucciones_almacenamiento')->nullable();
            $table->string('proveedor')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
