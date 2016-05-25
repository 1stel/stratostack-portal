<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activities', function(Blueprint $table)
		{
			$table->increments('id');
            // subject_type, subject_id, event, user_id, ip
            $table->string('subject_type', 60);
            $table->bigInteger('subject_id')->unsigned();
            $table->string('event');
            $table->bigInteger('user_id')->unsigned();
            $table->string('ip', 40);
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
		Schema::drop('activities');
	}

}
