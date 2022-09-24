<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvaluacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluacions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('selfie')->nullable();
            $table->text('comentario_selfie')->nullable();
            $table->text('usuarioResSelfie')->nullable();
            $table->text('fechaComentSelfi')->nullable();
            $table->text('identidad')->nullable();
            $table->text('comentario_identidad')->nullable();
            $table->text('usuarioResIdentidad')->nullable();
            $table->text('fechaComentIdentidad')->nullable();
            $table->text('adicionales')->nullable();
            $table->text('comentario_adicionales')->nullable();
            $table->text('usuarioResAdicional')->nullable();
            $table->text('fechaComentAdicional')->nullable();
            $table->text('llamada')->nullable();
            $table->text('comentario_llamada')->nullable();
            $table->text('usuarioResLlamada')->nullable();
            $table->text('fechaComentLlamada')->nullable();
            $table->integer('idSolicitudFk')->nullable();
            $table->integer('idUserFk')->nullable();
            $table->text('estatus')->nullable();
            $table->text('data_credito')->nullable();
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
        Schema::dropIfExists('evaluacions');
    }
}
