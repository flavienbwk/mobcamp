<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTourScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::disableForeignKeyConstraints();
        Schema::create('tour_schedule', function (Blueprint $table) {
            $table->integer('tour_id')->unsigned();
            $table->integer('schedule_id')->unsigned();
            $table->string('place');
            $table->tinyInteger('active')->default(1);
            $table->foreign('tour_id')->references('id')->on('tour');
            $table->foreign('schedule_id')->references('id')->on('schedule');
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
        
    }
}
