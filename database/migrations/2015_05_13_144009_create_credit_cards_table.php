<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditCardsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('credit_cards', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('user_id');
            $table->string('number', '4');
            $table->string('exp', '7');
            $table->enum('type', ['Mastercard', 'VISA', 'Discover', 'American Express']);
            $table->tinyInteger('primary')->default('0');
            $table->integer('payment_profile_id');
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
		Schema::drop('credit_cards');
	}

}
