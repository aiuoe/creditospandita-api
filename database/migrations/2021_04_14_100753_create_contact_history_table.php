<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('idSolicitudFk');
            $table->integer('idUserFk');
            $table->date('fechaContacto');
            $table->integer('colaborador_id')->comment('Id de la persona que realizo el contacto');
            $table->string('proposito')->comment('proposito del contacto');
            $table->string('metodoContacto')->comment('Metodo que se utilizo para realizar el contacto');
            $table->string('resultado')->comment('resultado del contacto');
            $table->text('comentario')->nullable()->comment('comentario del colaborador sobre el contacto');
            $table->date('fechaPtp')->comment('promise to pay, fecha acordada para el pago');
            $table->double('montoPtp', 20, 2)->comment('promise to pay, monto acordada para el pago');
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
        Schema::dropIfExists('contact_history');
    }
}
