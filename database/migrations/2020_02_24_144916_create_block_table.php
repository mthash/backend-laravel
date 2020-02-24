<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlockTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('block', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('asset_id')->nullable()->index('AST_ID');
			$table->integer('miner_id')->nullable();
			$table->integer('pool_id')->nullable();
			$table->string('hash')->nullable()->index('HSH');
			$table->decimal('reward', 20, 8)->nullable();
			$table->integer('created_at')->nullable()->index('CREATED');
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
		Schema::drop('block');
	}

}
