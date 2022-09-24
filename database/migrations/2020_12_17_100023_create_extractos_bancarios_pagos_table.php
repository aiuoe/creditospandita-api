<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtractosBancariosPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extractos_bancarios_pagos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("idEvaluacion");
            $table->text("fecha")->nullable();
            $table->text("concepto")->nullable();
            $table->integer("valorIngreso")->default(0);
            $table->integer("totalMensual")->default(0);
            $table->integer("promedio")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extractos_bancarios_pagos');
    }
}
