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
			$table->unsignedBigInteger('user_id');//->nullable()->index('USR_ID');
            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->unsignedBigInteger('asset_id');//->nullable()->index('AST_ID');
            $table->foreign('asset_id')
                ->references('id')
                ->on('asset')
                ->onUpdate('cascade')
                ->onDelete('cascade');

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
