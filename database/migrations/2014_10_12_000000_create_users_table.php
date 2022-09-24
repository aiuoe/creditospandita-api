<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email',100);
            $table->string('first_name',50);
            $table->string('second_name',50)->nullable();
            $table->string('last_name',50);
            $table->string('second_last_name',50)->nullable();
            $table->text('password');
            $table->string('n_document');
            $table->string('phone_number');
            $table->rememberToken();
            $table->unique(['email','deleted_at']);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('avatar')->nullable();
            $table->text('token_password')->nullable();
            $table->integer('borrower_id_Fk')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
