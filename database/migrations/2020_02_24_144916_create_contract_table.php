<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContractTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contract', function(Blueprint $table)
		{
			$table->bigIncrements('id');

			$table->unsignedBigInteger('user_id');//->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->unsignedBigInteger('wallet_id');//->nullable();
            $table->foreign('wallet_id')
                ->references('id')
                ->on('wallet')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->unsignedBigInteger('asset_id');//->nullable();
            $table->foreign('asset_id')
                ->references('id')
                ->on('asset')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->unsignedBigInteger('block_id');//>nullable();
            $table->foreign('block_id')
                ->references('id')
                ->on('block')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->unsignedBigInteger('pool_id');//->nullable();
            $table->foreign('pool_id')
                ->references('id')
                ->on('pool')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->decimal('amount', 20, 8)->nullable();
			$table->bigInteger('hashrate')->nullable();
			$table->integer('created_at')->nullable();
			$table->integer('updated_at')->nullable();
			$table->integer('deleted_at')->nullable();
			$table->integer('revoked_at')->nullable();
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
		Schema::drop('contract');
	}

}
