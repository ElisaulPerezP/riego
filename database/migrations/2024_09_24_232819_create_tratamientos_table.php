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
        Schema::create('tratamientos', function (Blueprint $table) {
            $table->id();
            $table->string('agronomo'); 
            $table->decimal('cantidad', 8, 2); 
            $table->string('frecuencia');
            $table->text('diagnostico'); 
            $table->text('notas')->nullable(); 
        
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
            $table->timestamps();
        });
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tratamientos');
    }
};
