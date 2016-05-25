<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainRecordsTable extends Migration {

    /**
	 * Run the migrations.
     *
	 * @return void
	 */
	public function up()
	{
		Schema::create('domain_records', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('domain_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('type', 10);
            $table->string('target', 40)->nullable();
			$table->smallInteger('priority')->unsigned()->nullable();
			$table->smallInteger('port')->unsigned()->nullable();
			$table->smallInteger('weight')->unsigned()->nullable();
            $table->integer('user_id')->unsigned();
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
		Schema::drop('domain_records');
	}

}
