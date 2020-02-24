<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOverviewTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('overview', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('asset_id')->nullable()->index('asset_id');
			$table->decimal('daily_revenue', 16, 3)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('overview');
	}

}
