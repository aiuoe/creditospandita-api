<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigCalculadorasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_calculadoras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('monto_minimo');
            $table->integer('monto_maximo');
            $table->integer('dias_minimo');
            $table->integer('dias_maximo');
            $table->float('porcentaje_iva', 8, 2);
            $table->float('porcentaje_plataforma', 8, 2);
            $table->float('porcentaje_express', 8, 2);
            $table->float('porcentaje_express_dos', 8, 2);
            $table->float('porcentaje_express_tres', 8, 2);
            $table->integer('monto_restriccion');
            $table->integer('dias_restriccion');
            $table->integer('tipo');
            $table->integer('monto_restriccion_tooltip');
            $table->float('tasa', 10, 8);
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
        Schema::dropIfExists('config_calculadoras');
    }
}
