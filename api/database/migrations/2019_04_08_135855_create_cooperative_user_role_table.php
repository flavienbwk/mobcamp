<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCooperativeUserRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::disableForeignKeyConstraints();
        Schema::create('cooperative_user_role', function (Blueprint $table) {
            $table->integer('role_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->integer('cooperative_id')->unsigned();
            $table->foreign('role_id')->references('id')->on('role')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
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
