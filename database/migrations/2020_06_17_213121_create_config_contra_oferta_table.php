<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigContraOfertaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_contra_ofertas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('monto_maximo');
            $table->integer('monto_minimo');
            $table->text('tipo_credito');
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
        Schema::dropIfExists('config_contra_ofertas');
    }
}
