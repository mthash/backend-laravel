<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlgoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('algo', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->string('name', 64)->nullable();
			$table->unsignedBigInteger('pool_id');//->nullable();
            $table->foreign('pool_id')
                ->references('id')
                ->on('pool')
                ->onUpdate('cascade')
                ->onDelete('cascade');
			$table->boolean('status')->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('algo');
	}

}
