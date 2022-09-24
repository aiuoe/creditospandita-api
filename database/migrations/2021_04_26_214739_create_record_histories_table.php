<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;

class CreateRecordHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('record_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid', 50);
            $table->integer('idSolicitudFk')->nullable();
            $table->integer('idUserFk')->nullable();
            $table->date('fecha_registro')->default(date('Y-m-d'));
            $table->string('estatus')->default('visita')->comment('estados registrado, visita y verificado');
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
        Schema::dropIfExists('record_histories');
    }
}
