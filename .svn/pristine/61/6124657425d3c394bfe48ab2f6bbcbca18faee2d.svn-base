<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckinHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkin_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('UserID');
            $table->unsignedInteger('QRCodeID');
            $table->string('InfoDevice');
            $table->string('Type');
            $table->timestamp('CheckinTime');
            $table->timestamp('RequestTime');
            $table->string('MacAddress');
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
        Schema::dropIfExists('checkin_history');
    }
}
