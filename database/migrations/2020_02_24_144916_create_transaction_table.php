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
			$table->unsignedInteger('type_id');//->nullable()->index('TYPE');
            $table->foreign('type_id')
                ->references('id')
                ->on('transaction_type')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->unsignedBigInteger('from_user_id');//->nullable()->index('FROM_USER');
            $table->foreign('from_user_id')
                ->references('id')
                ->on('user')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->unsignedBigInteger('wallet_from_id');//->nullable();
            $table->foreign('wallet_from_id')
                ->references('id')
                ->on('wallet')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->unsignedBigInteger('to_user_id');//->nullable();
            $table->foreign('to_user_id')
                ->references('id')
                ->on('user')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->unsignedBigInteger('wallet_to_id');//->nullable();
            $table->foreign('wallet_to_id')
                ->references('id')
                ->on('wallet')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unsignedBigInteger('block_id')->nullable();
            $table->foreign('block_id')
                ->references('id')
                ->on('block')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->decimal('amount', 20, 8)->nullable();
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
