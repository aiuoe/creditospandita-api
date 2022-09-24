<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBasicasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('basicas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('direccion');
            $table->string('ciudad');
            $table->string('tipoVivienda');
            $table->string('tienmpoVivienda');
            $table->string('conquienVives');
            $table->string('estrato');
            $table->string('genero');
            $table->string('fechaNacimiento');
            $table->string('estadoCivil');
            $table->string('personasaCargo');
            $table->string('nCedula');
            $table->string('fechaExpedicionCedula');
            $table->string('anversoCedula')->nullable();
            $table->string('reversoCedula')->nullable();
            $table->string('selfi')->nullable();
            $table->string('nHijos');
            $table->string('tipoPlanMovil');
            $table->string('nivelEstudio');
            $table->string('estadoEstudio');
            $table->string('vehiculo');
            $table->string('placa')->nullable();
            $table->string('centralRiesgo');
            $table->string('estadoReportado')->nullable();
            $table->integer('idUserFk');
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
        Schema::dropIfExists('basicas');
    }
}
