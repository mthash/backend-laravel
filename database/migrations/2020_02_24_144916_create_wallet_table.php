<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWalletTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wallet', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('asset_id')->nullable();
			$table->integer('user_id')->nullable();
			$table->string('address')->nullable();
			$table->string('public_key')->nullable();
			$table->string('private_key')->nullable();
			$table->string('name', 64)->nullable();
			$table->char('currency', 5)->nullable();
			$table->decimal('balance', 20, 8)->nullable()->default(0.00000000);
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
		Schema::drop('wallet');
	}

}
