<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDesembolsosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('desembolsos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('nombres');
            $table->text('ncedula');
            $table->text('email');
            $table->text('nombreBanco');
            $table->text('tipoCuenta');
            $table->text('ncuenta');
            $table->text('monto');
            $table->text('metodo');
            $table->integer('idUserFk');
            $table->text('registrador');
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
        Schema::dropIfExists('desembolsos');
    }
}
