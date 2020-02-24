<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRelayerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('relayer', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('block_id')->nullable();
			$table->integer('user_id')->nullable();
			$table->integer('asset_id')->nullable();
			$table->bigInteger('hashrate')->nullable();
			$table->integer('created_at')->nullable();
			$table->integer('status')->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('relayer');
	}

}
