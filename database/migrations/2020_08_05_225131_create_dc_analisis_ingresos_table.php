<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDcAnalisisIngresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dc_analisis_ingresos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("idEvaluacion");
            $table->text("situacionLaboral")->nullable();
            $table->text("ingresoVsCuota")->nullable();
            $table->text("ingresoEstimado")->nullable();
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
        Schema::dropIfExists('dc_analisis_ingresos');
    }
}
