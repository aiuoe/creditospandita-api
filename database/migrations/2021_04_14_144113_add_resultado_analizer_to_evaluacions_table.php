<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResultadoAnalizerToEvaluacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evaluacions', function (Blueprint $table) {
            $table->text("analizer")->nullable();
            $table->text("resultadoAnalizer")->nullable();
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
            $table->dropColumn('analizer');
            $table->dropColumn('resultadoAnalizer');
        });
    }
}
