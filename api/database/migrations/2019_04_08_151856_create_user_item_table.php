<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::disableForeignKeyConstraints();
        Schema::create('user_item', function (Blueprint $table) {
            $table->increments('id');
            $table->float('price');
            $table->text('message');
            $table->integer('quantity');
            $table->integer('item_id')->unsigned();
            $table->integer('cooperative_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('item_id')->references('id')->on('item');
            $table->foreign('user_id')->references('id')->on('user');
            $table->foreign('cooperative_id')->references('id')->on('cooperative');
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
