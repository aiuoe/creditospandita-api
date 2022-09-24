<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTelefonoFiltroToEvaluacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evaluacions', function (Blueprint $table) {
            $table->text('telefono')->nullable();
            $table->text('filtro')->nullable();
            $table->text('resultadoFiltro')->nullable();
            $table->text('resultadoEmail')->nullable();
            $table->text('email')->nullable();
            $table->text('scrapping')->nullable();
            $table->text('resultadoScrapping')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('evaluacions', function (Blueprint $table) {
            //
        });
    }
}
