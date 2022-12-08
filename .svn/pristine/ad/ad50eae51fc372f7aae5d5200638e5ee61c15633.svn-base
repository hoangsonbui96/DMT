<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('RoomID')->nullable();
            $table->timestamp('SDate')->nullable();
            $table->timestamp('EDate')->nullable();
            $table->date('DateUpdate')->nullable();
            $table->text('Contents')->nullable();
            $table->text('week_work')->nullable();
            $table->text('unfinished_work')->nullable();
            $table->text('noted')->nullable();
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
        Schema::dropIfExists('room_reports');
    }
}
