<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsageDisksTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usage_disks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('zoneId', 40);
            $table->string('accountId', 40);
            $table->string('volumeId', 40)->nullable();
            $table->bigInteger('size')->unsigned();
            $table->enum('type', ['Volume', 'Root Volume', 'Snapshot']);
            $table->string('tags', 25)->nullable();
            $table->double('usage');
            $table->string('vmInstanceId', 40)->nullable();
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
        Schema::drop('usage_disks');
    }
}
