<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinancierasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financieras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('banco');
            $table->string('tipoCuenta');
            $table->string('nCuenta');
            $table->string('ingresoTotalMensual');
            $table->string('egresoTotalMensual');
            $table->string('ingresoTotalMensualHogar')->nullable();
            $table->string('egresoTotalMensualHogar')->nullable();
            $table->string('comoTePagan');
            $table->string('situacionLaboral');
            $table->string('actividad');
            $table->string('antiguedadLaboral')->nullable();
            $table->string('nombreEmpresa')->nullable();
            $table->string('telefonoEmpresa')->nullable();
            $table->string('usoCredito');
            $table->string('otroIngreso')->nullable();
            $table->string('proviene')->nullable();
            $table->string('total_otro_ingr_mensual')->nullable();
            $table->string('idUserFk');
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
        Schema::dropIfExists('financieras');
    }
}
