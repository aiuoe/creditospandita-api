<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTiempoDatacreditoToFinancierasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financieras', function (Blueprint $table) {
            $table->integer('tiempoDatacredito')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financieras', function (Blueprint $table) {
            $table->dropColumn('tiempoDatacredito');
        });
    }
}
