<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_demo')->nullable()->default(0);
            $table->tinyInteger('is_admin')->nullable()->default(0);
            $table->tinyInteger('status')->nullable()->default(1);
            $table->string('tag', 64)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_demo');
            $table->dropColumn('is_admin');
            $table->dropColumn('status');
            $table->dropColumn('tag');
        });
    }
}
