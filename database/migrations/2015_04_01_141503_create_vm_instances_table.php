<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVmInstancesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vm_instances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('agent_id');
            $table->string('vm_instance_id', 40);
            $table->tinyInteger('cpu_number');
            $table->integer('cpu_speed');
            $table->integer('memory');
            $table->integer('disk_size');
            $table->enum('disk_type', ['HD', 'SSD']);
            $table->decimal('rate', 7, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('vm_instances');
    }
}
