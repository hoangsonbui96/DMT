<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListPositionUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_position_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('UserId', 30);
            $table->string('DataValue', 30); 
            $table->string('DataDescription')->nullable();
            $table->boolean('Level')->default(1);
            $table->softDeletes();
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
        Schema::dropIfExists('list_position_user');
    }
}
