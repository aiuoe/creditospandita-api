<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCannonMensualAlojamientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cannon_mensual_alojamientos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('estrato');
            $table->text('alojamiento');
            $table->integer('monto');
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
        Schema::dropIfExists('cannon_mensual_alojamientos');
    }
}
