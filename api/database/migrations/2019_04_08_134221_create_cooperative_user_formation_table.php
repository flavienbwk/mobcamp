<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCooperativeUserFormationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cooperative_user_formation', function (Blueprint $table) {
            $table->integer('formation_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->integer('cooperative_id')->unsigned();
            $table->foreign('formation_id')->references('id')->on('formation')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('cooperative_id')->references('id')->on('cooperative')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cooperative_user_formation');
    }
}
