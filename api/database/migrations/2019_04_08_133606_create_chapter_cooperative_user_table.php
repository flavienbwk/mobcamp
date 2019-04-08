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
        Schema::create('chapter_cooperative_user', function (Blueprint $table) {
            $table->foreign('formation_id')->references('id')->on('formation');
            $table->foreign('user_id')->references('id')->on('user');
            $table->foreign('cooperative_id')->references('id')->on('cooperative');
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
        Schema::dropIfExists('chapter_cooperative_user');
    }
}
