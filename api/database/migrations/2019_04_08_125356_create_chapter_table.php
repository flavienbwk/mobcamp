<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChapterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::disableForeignKeyConstraints();
        Schema::create('chapter', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('content')->nullable();
            $table->tinyInteger('order');
            $table->integer('lesson_id')->unsigned();
            $table->integer('quizz_id')->unsigned();
            $table->integer('activity_id')->unsigned();
            $table->foreign('lesson_id')->references('id')->on('lesson')->nullable();
            $table->foreign('quizz_id')->references('id')->on('quizz')->nullable();
            $table->foreign('activity_id')->references('id')->on('activity')->nullable();
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
