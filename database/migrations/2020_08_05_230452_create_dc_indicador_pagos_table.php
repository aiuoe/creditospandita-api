<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDcIndicadorPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dc_indicador_pagos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('idEvaluacion');
            $table->integer('bajo')->default(0);
            $table->integer('medio')->default(0);
            $table->integer('alto')->default(0);
            $table->integer('puntajeBajo')->default(0);
            $table->integer('puntajeMedio')->default(0);
            $table->integer('puntajeAlto')->default(0);
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
        Schema::dropIfExists('dc_indicador_pagos');
    }
}
