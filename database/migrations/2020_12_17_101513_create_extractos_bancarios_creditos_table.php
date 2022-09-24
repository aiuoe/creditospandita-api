<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtractosBancariosCreditosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extractos_bancarios_creditos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("idEvaluacion");
            $table->text("fecha")->nullable();
            $table->text("empresa")->nullable();
            $table->integer("ingresoPrestamo")->default(0);
            $table->integer("cuotaCredito")->default(0);
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
        Schema::dropIfExists('extractos_bancarios_creditos');
    }
}
