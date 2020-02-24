<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMinerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('miner', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('pool_id')->nullable();
			$table->integer('algo_id')->nullable();
			$table->unsignedBigInteger('max_hashrate')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('miner');
	}

}
