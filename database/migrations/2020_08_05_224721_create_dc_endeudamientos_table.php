<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDcEndeudamientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dc_endeudamientos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('idEvaluacion');
            $table->text("mes")->nullable();
            $table->text("mora")->nullable();
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
        Schema::dropIfExists('dc_endeudamientos');
    }
}
