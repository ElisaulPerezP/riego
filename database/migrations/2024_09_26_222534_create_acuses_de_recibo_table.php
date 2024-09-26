<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcusesDeReciboTable extends Migration
{
    public function up()
    {
        Schema::create('acuses_de_recibo', function (Blueprint $table) {
            $table->id();

            $table->string('entregado_a')->nullable();
            $table->longText('acuse_de_recibo')->nullable();
            $table->string('recibido_de')->nullable();
            $table->json('modelo_serializado')->nullable();
            $table->timestamp('fecha_entrega')->nullable();
            $table->timestamp('fecha_acuse')->nullable();
            $table->string('estado_entrega')->nullable();
            $table->unsignedBigInteger('usuario_responsable')->nullable();
            $table->string('firma_recibo')->nullable();

            $table->timestamps();

            // Si tienes una tabla 'users' y quieres establecer la relación:
            $table->foreign('usuario_responsable')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('acuses_de_recibo');
    }
}
