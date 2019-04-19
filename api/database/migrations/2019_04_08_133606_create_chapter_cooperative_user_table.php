<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChapterCooperativeUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::disableForeignKeyConstraints();
        Schema::create('chapter_cooperative_user', function (Blueprint $table) {
            $table->integer('chapter_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->integer('cooperative_id')->unsigned();
            $table->foreign('chapter_id')->references('id')->on('chapter')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('cooperative_id')->references('id')->on('cooperative')->onDelete('cascade');
            $table->tinyInteger('is_achieved')->nullable();
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
