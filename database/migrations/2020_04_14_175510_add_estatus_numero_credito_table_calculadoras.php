<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEstatusNumeroCreditoTableCalculadoras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calculadoras', function (Blueprint $table) {
            //
            $table->string('estatus')->default('incompleto');
            $table->string('numero_credito');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calculadoras', function (Blueprint $table) {
            //
        });
    }
}
