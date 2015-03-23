<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WatchtowerPermissionRolePivot extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('watchtower_roles_permissions', function(Blueprint $table)
		{
                $table->unsignedInteger('role_id');
                $table->unsignedInteger('permission_id');

                $table->foreign('role_id')->references('id')->on('watchtower_roles')->onDelete('cascade');
                $table->foreign('permission_id')->references('id')->on('watchtower_permissions')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('watchtower_roles_permissions');
	}

}
