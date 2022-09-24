<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDcSintesisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dc_sintesis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('idEvaluacion');
            $table->integer("estadoDocumento")->default(0);
            $table->integer("nroCedula")->default(0);
            $table->integer("fechaExpedicion")->default(0);
            $table->integer("genero")->default(0);
            $table->integer("rangoEdad")->default(0);
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
        Schema::dropIfExists('dc_sinteses');
    }
}
