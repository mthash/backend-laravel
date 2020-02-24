<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAssetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('asset', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('algo_id')->nullable();
			$table->integer('cmc_id')->nullable();
			$table->string('logo_url')->nullable();
			$table->char('symbol', 8)->nullable();
			$table->string('name', 32)->nullable();
			$table->boolean('mineable')->nullable()->default(1);
			$table->boolean('can_mine')->nullable()->default(0);
			$table->bigInteger('total_hashrate')->nullable();
			$table->integer('hash_invested')->nullable()->default(0);
			$table->integer('shares')->nullable();
			$table->decimal('price_usd', 10, 4)->nullable()->default(0.0000);
			$table->integer('last_block_id')->nullable();
			$table->integer('block_generation_time')->nullable();
			$table->float('block_reward_amount', 10, 0)->nullable();
			$table->integer('created_at')->nullable();
			$table->integer('updated_at')->nullable();
			$table->integer('deleted_at')->nullable();
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
		Schema::drop('asset');
	}

}
