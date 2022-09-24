<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('idSolicitudFk');
            $table->integer('idUsuarioFk');
            $table->string('fechaPago')->nullable();
            $table->string('estatusPago')->default('pendiente');
            $table->float('montoPagar',8, 2)->default(0);
            $table->float('montoPagado',8, 2)->default(0);
            $table->string('medioPago')->nullable();
            $table->string('fechaPagado')->nullable();
            $table->string('nroReferencia')->nullable();
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
        Schema::dropIfExists('pagos');
    }
}
