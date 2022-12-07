<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimekeepingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timekeepings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('UserID');
            $table->date('Date')->nullable();
            $table->time('TimeIn')->nullable();
            $table->time('TimeOut')->nullable();
            $table->time('STimeOfDay')->nullable();
            $table->time('ETimeOfDay')->nullable();
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
        Schema::dropIfExists('timekeepings');
    }
}
