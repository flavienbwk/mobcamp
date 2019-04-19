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
        
        Schema::disableForeignKeyConstraints();
        Schema::create('submission_cooperative_user', function (Blueprint $table) {
            $table->integer('submission_id')->unsigned();
            $table->integer('formation_id')->unsigned();
            $table->integer('activity_id')->unsigned();
            $table->integer('cooperative_id')->unsigned();
            $table->bigInteger('corrector_user_id')->unsigned()->nullable();
            $table->foreign('submission_id')->references('id')->on('submission')->onDelete('cascade');
            $table->foreign('formation_id')->references('id')->on('formation')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('activity')->onDelete('cascade');
            $table->foreign('cooperative_id')->references('id')->on('cooperative')->onDelete('cascade');
            $table->foreign('corrector_user_id')->references('id')->on('user')->onDelete('set null');
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
        
    }
}
