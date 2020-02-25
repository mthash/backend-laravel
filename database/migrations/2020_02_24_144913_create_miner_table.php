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
			$table->bigIncrements('id');
			$table->unsignedBigInteger('pool_id');//->nullable();
            $table->foreign('pool_id')
                ->references('id')
                ->on('pool')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->unsignedBigInteger('algo_id');//->nullable();
            $table->foreign('algo_id')
                ->references('id')
                ->on('algo')
                ->onUpdate('cascade')
                ->onDelete('cascade');

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
