<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('RegisterID');
            $table->date('MeetingDate')->nullable();
            $table->time('MeetingTimeFrom')->nullable();
            $table->time('MeetingTimeTo')->nullable();
            $table->unsignedMediumInteger('RoomID')->nullable();
            $table->string('Description')->nullable();
            $table->unsignedInteger('MeetingHostID')->nullable();
            $table->text('Participant')->nullable();
            $table->text('Purpose')->nullable();
            $table->boolean('Delflag')->default(1);
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
        Schema::dropIfExists('meeting_schedules');
    }
}
