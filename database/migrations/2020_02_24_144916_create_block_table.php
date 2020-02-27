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
			$table->bigIncrements('id');
			$table->unsignedBigInteger('asset_id');//->nullable()->index('AST_ID');
            $table->foreign('asset_id')
                ->references('id')
                ->on('asset')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->unsignedBigInteger('miner_id');//->nullable();
            $table->foreign('miner_id')
                ->references('id')
                ->on('miner')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->unsignedBigInteger('pool_id');//->nullable();
            $table->foreign('pool_id')
                ->references('id')
                ->on('pool')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->string('hash')->nullable()->index('HSH');
			$table->decimal('reward', 20, 8)->nullable();
			$table->integer('created_at')->nullable()->index('CREATED');
			$table->boolean('status')->default(1)->index('block_sts_index');
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
