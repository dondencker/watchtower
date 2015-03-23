<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
//use Illuminate\Support\Facades\Schema;

class WatchtowerRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('watchtower_roles', function(Blueprint $table)
		{
			$table->increments('id');

            $table->string('code',50)->unique()->index();
            $table->string('name',50);
            $table->boolean('is_super_user');

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('watchtower_roles');
	}

}
