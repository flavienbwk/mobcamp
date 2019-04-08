<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConnectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('connection');
        Schema::create('connection', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('token');
            $table->string("ip")->nullable();
            $table->timestamp("expires_at");
            $table->timestamp("created_at")->useCurrent();
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('connection');
    }
}
