<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserItemCooperativeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::disableForeignKeyConstraints();
        Schema::create('user_item_cooperative', function (Blueprint $table) {
            $table->integer('item_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->integer('cooperative_id')->unsigned();
            $table->foreign('item_id')->references('id')->on('item');
            $table->foreign('user_id')->references('id')->on('user');
            $table->foreign('cooperative_id')->references('id')->on('cooperative');
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
