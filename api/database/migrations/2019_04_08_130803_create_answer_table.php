<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::disableForeignKeyConstraints();
        Schema::create('answer', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value');
            $table->tinyInteger('is_correct');
            $table->integer('quizz_id')->unsigned();
            $table->integer('question_id')->unsigned();
            $table->foreign('quizz_id')->references('id')->on('quizz')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('question')->onDelete('cascade');
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
