<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCooperativeUserFormationAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cooperative_user_formation_answer', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->integer('formation_id')->unsigned();
            $table->integer('answer_id')->unsigned();
            $table->integer('question_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('formation_id')->references('id')->on('formation')->onDelete('cascade');
            $table->foreign('answer_id')->references('id')->on('answer')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('question')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cooperative_user_formation_answer');
    }
}
