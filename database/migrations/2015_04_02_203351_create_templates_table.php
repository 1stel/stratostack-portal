<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('templates', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('template_group_id')->unsigned();
            $table->string('template_id');
            $table->mediumInteger('size')->unsigned(); // IaaS: Size in Gb; SaaS: Number of users supported
            $table->decimal('price', 6, 2);
			$table->timestamps();
            $table->softDeletes();

            $table->foreign('template_group_id')->references('id')->on('template_groups')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('templates');
	}

}
