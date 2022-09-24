<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVariosCamposTableBasicas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('basicas', function (Blueprint $table) {
            //
            $table->text('nroPersonasDependenEconomicamente')->nullable();
            $table->text('cotizasSeguridadSocial')->nullable();
            $table->text('tipoAfiliacion')->nullable();
            $table->text('eps')->nullable();
            $table->text('entidadReportado')->nullable();
            $table->text('cualEntidadReportado')->nullable();
            $table->text('valorMora')->nullable();
            $table->text('tiempoReportado')->nullable();
            $table->text('comoEnterasteNosotros')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('basicas', function (Blueprint $table) {
            //
        });
    }
}
