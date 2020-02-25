<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePoolTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pool', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->string('name', 128)->nullable();
			//$table->integer('asset_id')->nullable();
			$table->integer('miners_count')->nullable();
			$table->bigInteger('total_hashrate')->nullable();
			$table->bigInteger('used_power')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pool');
	}

}
