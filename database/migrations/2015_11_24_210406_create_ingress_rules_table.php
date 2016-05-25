<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIngressRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingress_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('security_group_id')->unsigned();
            $table->string('cidr', 45);
            $table->enum('protocol', ['TCP', 'UDP', 'ICMP']);
            $table->tinyInteger('icmp_type')->unsigned()->nullable();
            $table->tinyInteger('icmp_code')->unsigned()->nullable();
            $table->smallInteger('start_port')->unsigned()->nullable();
            $table->smallInteger('end_port')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('security_group_id')->references('id')->on('security_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ingress_rules');
    }
}
