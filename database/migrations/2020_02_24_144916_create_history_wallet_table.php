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
			$table->unsignedBigInteger('user_id');//->nullable()->index('USR_ID');
            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->unsignedBigInteger('wallet_id');//->nullable()->index('WLT_ID');
            $table->foreign('wallet_id')
                ->references('id')
                ->on('wallet')
                ->onUpdate('cascade')
                ->onDelete('cascade');

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
