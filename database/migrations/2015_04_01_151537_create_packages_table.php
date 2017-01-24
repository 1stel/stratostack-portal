<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('cpu_number')->unsigned();
            $table->mediumInteger('ram')->unsigned();
            $table->integer('disk_size')->unsigned();
            $table->integer('disk_type');
            $table->decimal('price', 6, 2);
            $table->string('tic');
            $table->enum('paymentTypeOverride', ['PostPay', 'PrePay']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('packages');
    }
}
