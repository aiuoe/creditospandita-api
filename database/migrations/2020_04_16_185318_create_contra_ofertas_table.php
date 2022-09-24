<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContraOfertasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contra_ofertas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('montoSolicitado');
            $table->string('montoAprobado');
            $table->string('plazo');
            $table->string('tasaInteres');
            $table->string('subtotal');
            $table->string('plataforma');
            $table->string('aprobacionRapida');
            $table->string('iva');
            $table->string('totalPagar');
            $table->string('tipoCredito');
            $table->integer('idUserFk');
            $table->integer('idCalculadoraFk');
            $table->integer('loandisk')->nullable();
            $table->string('estatus')->nullable();
            $table->string('numero_credito')->nullable();
            $table->float('puntaje_total', 8, 2)->default(0.00);
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
        Schema::dropIfExists('contra_ofertas');
    }
}
