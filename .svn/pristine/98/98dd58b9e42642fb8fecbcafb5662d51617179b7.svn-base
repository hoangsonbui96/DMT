<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkingScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('working_schedule', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('Date')->nullable();
            $table->tinyInteger('TimeWorking')->nullable();
            $table->text('Content');
            $table->text('Address')->nullable();
            $table->string('AssignID');
            $table->string('Note')->nullable();
            $table->unsignedInteger('UserID');
            $table->timestamps();
            $table->unsignedInteger('projectID');
            $table->Integer('in_out');
            $table->unsignedInteger('roomsID');
            $table->Integer('minuteRoom');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('working_schedule');
    }
}
