<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaChapterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::disableForeignKeyConstraints();
        Schema::create('media_chapter', function (Blueprint $table) {
            $table->integer('media_id')->unsigned();
            $table->integer('chapter_id')->unsigned();
            $table->foreign('media_id')->references('id')->on('media');
            $table->foreign('chapter_id')->references('id')->on('chapter');
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
