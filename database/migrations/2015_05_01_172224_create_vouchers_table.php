<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVouchersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('number', 10);
            $table->string('type');
            $table->decimal('amount', 5, 2);
            $table->string('recipient_email');
            $table->integer('redeemed_by')->nullable();
            $table->dateTime('redeemed_at');
            $table->timestamps();
            $table->softDeletes();
            // user_id
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('vouchers');
    }
}
