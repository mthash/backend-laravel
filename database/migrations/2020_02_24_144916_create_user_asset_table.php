<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserAssetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_asset', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedBigInteger('asset_id');//->nullable();
            $table->foreign('asset_id')
                ->references('id')
                ->on('asset')
                ->onUpdate('cascade')
                ->onDelete('cascade');
			$table->unsignedBigInteger('user_id');//->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onUpdate('cascade')
                ->onDelete('cascade');

			$table->integer('created_at')->nullable();
			$table->integer('updated_at')->nullable();
			$table->integer('deleted_at')->nullable();
			$table->boolean('is_visible')->nullable()->default(0);
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
		Schema::drop('user_asset');
	}

}
