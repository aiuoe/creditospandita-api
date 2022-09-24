<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateFilesTable.
 */
class CreateFilesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('files', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name',200)->nullable();
            $table->string('original_name',200)->nullable();
            $table->string('extension',10)->nullable();
            $table->integer('size')->default(0);
            $table->softDeletes();
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
		Schema::drop('files');
	}
}
