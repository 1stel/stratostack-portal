<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsageGeneralsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usage_generals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('zoneId', 40);
            $table->string('accountId', 40);
            $table->enum('type', ['LB', 'PF', 'VPN', 'Network Sent', 'Network Received']);
            $table->double('usage');
            $table->string('vmInstanceId', 40)->nullable();
            $table->string('templateId', 40)->nullable();
            $table->dateTime('startDate');
            $table->dateTime('endDate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('usage_generals');
    }
}
