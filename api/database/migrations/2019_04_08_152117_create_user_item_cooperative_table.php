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
        Schema::create('user_item_cooperative', function (Blueprint $table) {
            $table->foreign('item_id')->references('id')->on('item');
            $table->foreign('user_id')->references('id')->on('user');
            $table->foreign('user_id')->references('id')->on('cooperative');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_item_cooperative');
    }
}
