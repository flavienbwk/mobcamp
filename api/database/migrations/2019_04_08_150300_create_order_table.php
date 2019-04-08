<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->increments('id');
            $table->foreign('item_id')->references('id')->on('item');
            $table->foreign('buyer_user_id')->references('id')->on('user');
            $table->foreign('seller_user_id')->references('id')->on('user');
            $table->foreign('cooperative_id')->references('id')->on('cooperative');
            $table->integer('quantity');
            $table->dateTime('confirmed_at');
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
        Schema::dropIfExists('order');
    }
}
