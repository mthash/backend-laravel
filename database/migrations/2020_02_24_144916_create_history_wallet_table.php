<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHistoryWalletTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('history_wallet', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->nullable()->index('USR_ID');
			$table->integer('wallet_id')->nullable()->index('WLT_ID');
			$table->decimal('balance', 20, 8)->nullable();
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
		Schema::drop('history_wallet');
	}

}
