<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubmissionCooperativeUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submission_cooperative_user', function (Blueprint $table) {
            $table->integer('submission_id')->unsigned();
            $table->integer('formation_id')->unsigned();
            $table->integer('activity_id')->unsigned();
            $table->integer('cooperative_id')->unsigned();
            $table->bigInteger('corrector_user_id')->unsigned();
            $table->foreign('submission_id')->references('id')->on('submission');
            $table->foreign('formation_id')->references('id')->on('formation');
            $table->foreign('activity_id')->references('id')->on('activity');
            $table->foreign('cooperative_id')->references('id')->on('cooperative');
            $table->foreign('corrector_user_id')->references('id')->on('user');
            $table->tinyInteger('is_validated');
            $table->text('message');
            $table->integer('grade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('submission_cooperative_user');
    }
}
