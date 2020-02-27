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
			$table->unsignedBigInteger('asset_id');//->nullable()->index('AST_ID');
            $table->foreign('asset_id')
                ->references('id')
                ->on('asset')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->integer('tokens_invested')->nullable();
			$table->bigInteger('hashrate')->nullable();
			$table->bigInteger('total_hashrate')->nullable();
			$table->integer('created_at')->nullable();
			$table->boolean('status')->default(1)->index('history_asset_sts_index');
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
