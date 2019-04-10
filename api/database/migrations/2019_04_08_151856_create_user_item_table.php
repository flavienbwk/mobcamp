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
            $table->float('price');
            $table->integer('quantity');
            $table->integer('item_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('item_id')->references('id')->on('item');
            $table->foreign('user_id')->references('id')->on('user');
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
