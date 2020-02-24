<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHistoryAssetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('history_asset', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('asset_id')->nullable()->index('AST_ID');
			$table->integer('tokens_invested')->nullable();
			$table->bigInteger('hashrate')->nullable();
			$table->bigInteger('total_hashrate')->nullable();
			$table->integer('created_at')->nullable();
			$table->boolean('status')->default(1)->index('STS');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('history_asset');
	}

}
