<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalculadorasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calculadoras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('montoSolicitado');
            $table->string('plazo');
            $table->string('tasaInteres');
            $table->string('subtotal');
            $table->string('plataforma');
            $table->string('aprobacionRapida');
            $table->string('iva');
            $table->string('totalPagar');
            $table->string('tipoCredito');
            $table->integer('idUserFk');
            $table->integer('loandisk')->nullable();
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
        Schema::dropIfExists('calculadoras');
    }
}
