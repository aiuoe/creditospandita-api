<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referencias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ReferenciaPersonalNombres');
            $table->string('ReferenciaPersonalApellidos');
            $table->string('ReferenciaPersonalCiudadFk');
            $table->string('ReferenciaPersonalTelefono');
            $table->string('ReferenciaFamiliarNombres');
            $table->string('ReferenciaFamiliarApellidos');
            $table->string('ReferenciaFamiliarCiudadFk');
            $table->string('ReferenciaFamiliarTelefono');
            $table->string('QuienRecomendo')->nullable();
            $table->integer('iduserFk');

            $table->string('relacionf');
            $table->string('relacionp');
            $table->string('emailf')->nullable();
            $table->string('emailp')->nullable();
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
        Schema::dropIfExists('referencias');
    }
}
