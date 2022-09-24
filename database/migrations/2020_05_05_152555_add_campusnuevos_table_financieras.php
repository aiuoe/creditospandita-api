<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCampusnuevosTableFinancieras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financieras', function (Blueprint $table) {
            $table->text('periodoPagoNomina')->nullable();
            $table->text('diasPago')->nullable();
            $table->text('tarjetasCredito')->nullable();
            $table->text('creditosBanco')->nullable();
            $table->text('otrasCuentas')->nullable();
            $table->text('tipoEmpresa')->nullable();
            $table->text('empresaConstituida')->nullable();
            $table->text('nit')->nullable();
            $table->text('rut')->nullable();
            $table->text('nombreCargo')->nullable();
            $table->text('ciudadTrabajas')->nullable();
            $table->text('direccionEmpresa')->nullable();
            $table->text('sectorEconomico')->nullable();
            $table->text('tamanoEmpresa')->nullable();
            $table->text('fondoPension')->nullable();
            $table->text('bancoPension')->nullable();
            $table->text('fuenteIngreso')->nullable();
            $table->text('cual')->nullable();
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
            //
        });
    }
}
