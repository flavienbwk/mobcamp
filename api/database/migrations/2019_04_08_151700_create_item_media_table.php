<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::disableForeignKeyConstraints();
        Schema::create('item_media', function (Blueprint $table) {
            $table->integer('item_id')->unsigned();
            $table->integer('media_id')->unsigned();
            $table->foreign('item_id')->references('id')->on('item');
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
