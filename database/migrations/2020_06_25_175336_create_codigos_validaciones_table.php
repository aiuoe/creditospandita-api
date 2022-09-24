<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCodigosValidacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('codigos_validaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('codigo');
            $table->integer('idUserFk');
            $table->integer('idSolicitudFk');
            $table->integer('valido')->default(1);
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
        Schema::dropIfExists('codigos_validaciones');
    }
}
