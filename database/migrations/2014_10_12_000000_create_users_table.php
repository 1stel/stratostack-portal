<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password', 60);
            $table->enum('access', ['User', 'Agent', 'Admin'])->default('User');
            $table->enum('paymentTypeOverride', ['PostPay', 'PrePay']);
            $table->string('acs_id', 40);
            $table->integer('agent_id')->unsigned()->nullable();
            $table->integer('authnet_cid')->unsigned()->nullable();
            $table->string('apiKey', 40);
            $table->decimal('credit', 8, 2);
            $table->date('bill_date');
            $table->boolean('verified')->default(false);
            $table->string('email_token')->nullable();
            $table->rememberToken();
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
        Schema::drop('users');
    }
}
