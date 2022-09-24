<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtractosBancariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extractos_bancarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("idEvaluacion");
            $table->integer("saldoAnterior")->default(0);
            $table->integer("totalAbonos")->default(0);
            $table->integer("totalCargos")->default(0);
            $table->integer("saldoActual")->default(0);
            $table->integer("saldoPromedio")->default(0);
            $table->integer("salario")->default(0);
            $table->text("diasPago")->nullable();
            $table->text("nombreEmpresa")->nullable();
            $table->text("tipoContrato")->nullable();
            $table->text("antiguedadLaboral")->nullable();
            $table->text("nombreCargo")->nullable();
            $table->text("valorTotalMensualCreditosActuales")->nullable();
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
        Schema::dropIfExists('extractos_bancarios');
    }
}
