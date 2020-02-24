<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHistoryDailyRevenueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('history_daily_revenue', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->nullable()->index('USR_ID');
			$table->integer('asset_id')->nullable()->index('AST_ID');
			$table->decimal('revenue', 20, 8)->nullable();
			$table->decimal('amount', 20, 8)->nullable();
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
		Schema::drop('history_daily_revenue');
	}

}
