<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagosParcialesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos_parciales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('idSolicitudFk');
            $table->integer('idUsuarioFk');
            $table->float('capital',8, 2)->default(0);
            $table->float('intereses',8, 2)->default(0);
            $table->float('interesesMora',8, 2)->default(0);
            $table->float('plataforma',8, 2)->default(0);
            $table->float('aprobacionRapida',8, 2)->default(0);
            $table->float('gastosCobranza',8, 2)->default(0);
            $table->float('iva',8, 2)->default(0);
            $table->float('totalNoPago',8, 2)->default(0);
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
        Schema::dropIfExists('pagos_parciales');
    }
}
