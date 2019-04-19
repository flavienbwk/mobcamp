<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::disableForeignKeyConstraints();
        Schema::create('order_schedule', function (Blueprint $table) {
            $table->integer('order_id')->unsigned();
            $table->integer('schedule_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('order');
            $table->foreign('schedule_id')->references('id')->on('schedule');
            $table->tinyInteger('is_validated')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
