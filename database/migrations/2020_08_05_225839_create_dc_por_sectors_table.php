<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDcPorSectorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dc_por_sectors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("idEvaluacion");
            $table->integer("cupoInicial")->default(0);
            $table->integer("saldoActual")->default(0);
            $table->integer("cuotaMensual")->default(0);
            $table->integer("gastosFamiliares")->default(0);
            $table->integer("saldoMora")->default(0);
            $table->integer("disponibleMensual")->default(0);
            $table->integer("disponibleEndeudamiento")->default(0);
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
        Schema::dropIfExists('dc_por_sectors');
    }
}
