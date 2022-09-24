<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDcAlertasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dc_alertas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("idEvaluacion");
            $table->text("fuente")->nullable();
            $table->text("fecha")->nullable();
            $table->text("novedad")->nullable();
            $table->text("descripcion")->nullable();
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
        Schema::dropIfExists('dc_alertas');
    }
}
