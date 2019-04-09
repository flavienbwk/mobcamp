<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubmissionMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::disableForeignKeyConstraints();
        Schema::create('submission_media', function (Blueprint $table) {
            $table->integer('submission_id')->unsigned();
            $table->integer('media_id')->unsigned();
            $table->foreign('submission_id')->references('id')->on('submission');
            $table->foreign('media_id')->references('id')->on('media');
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
