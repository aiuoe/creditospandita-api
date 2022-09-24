<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagoreferidorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagoreferidors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('idReferidor');
            $table->integer('idReferido');
            $table->integer('idSolicitud');
            $table->text('comision');
            $table->text('estatus');
            $table->text('referencia');
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
        Schema::dropIfExists('pagoreferidors');
    }
}
