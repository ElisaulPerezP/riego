<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQrsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('qrs', function (Blueprint $table) {
            $table->id();

            // Relación con la cosecha
            $table->foreignId('cosecha_id')->constrained('cosechas')->onDelete('cascade');

            // Campos para las imágenes de los QR
            $table->string('qr125')->nullable();
            $table->string('qr250')->nullable();
            $table->string('qr500')->nullable();

            // Campos para los UUIDs (almacenados como JSON)
            $table->json('uuid125')->nullable();
            $table->json('uuid250')->nullable();
            $table->json('uuid500')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('qrs');
    }
}
