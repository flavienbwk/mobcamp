<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormationItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::disableForeignKeyConstraints();
        Schema::create('formation_item', function (Blueprint $table) {
            $table->integer('quantity');
            $table->text('message');
            $table->integer('formation_id')->unsigned();
            $table->integer('cooperative_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->foreign('formation_id')->references('id')->on('formation')->onDelete('cascade');
            $table->foreign('cooperative_id')->references('id')->on('cooperative')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('item');
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
