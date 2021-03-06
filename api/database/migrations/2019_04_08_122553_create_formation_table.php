<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::disableForeignKeyConstraints();
        Schema::create('formation', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('estimated_duration');
            $table->string('level');
            $table->string('local_uri');
            $table->integer('cooperative_id')->unsigned();
            $table->foreign('cooperative_id')->references('id')->on('cooperative')->onDelete('cascade');
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
