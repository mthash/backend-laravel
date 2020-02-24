<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transaction', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('type_id')->nullable()->index('TYPE');
			$table->integer('from_user_id')->nullable()->index('FROM_USER');
			$table->integer('wallet_from_id')->nullable();
			$table->integer('to_user_id')->nullable();
			$table->integer('wallet_to_id')->nullable();
			$table->decimal('amount', 20, 8)->nullable();
			$table->integer('block_id')->nullable();
			$table->float('percent', 10, 0)->nullable();
			$table->char('currency', 10)->nullable();
			$table->boolean('condition')->nullable();
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
		Schema::drop('transaction');
	}

}
