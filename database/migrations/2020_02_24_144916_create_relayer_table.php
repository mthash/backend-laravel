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
			$table->unsignedBigInteger('block_id');//->nullable();
            $table->foreign('block_id')
                ->references('id')
                ->on('block')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->unsignedBigInteger('user_id');//->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->unsignedBigInteger('asset_id');//->nullable();
            $table->foreign('asset_id')
                ->references('id')
                ->on('asset')
                ->onUpdate('cascade')
                ->onDelete('cascade');

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
