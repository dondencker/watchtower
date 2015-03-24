<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class WatchtowerActorsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('watchtower_actors', function(Blueprint $table){
            $table->unsignedInteger('role_id');
            $table->string('actor_id');
            $table->string('actor_type');

            $table->unique(['role_id','actor_id','actor_type']);

            $table->foreign('role_id')->references('id')->on('watchtower_roles')->onDelete('cascade');

        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('watchtower_actors');
	}

}
