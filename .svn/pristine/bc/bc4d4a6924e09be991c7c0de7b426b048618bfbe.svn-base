<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbsencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('UID');
            $table->integer('RoomID');
            $table->timestamp('SDate');
            $table->timestamp('EDate');
            $table->string('MasterDataValue');
            $table->string('Reason');
            $table->string('Remark')->nullable();
            $table->string('RequestManager');
            $table->tinyInteger('Approved');
            $table->string('Comment')->nullable();
            $table->integer('UpdateBy')->nullable();
            $table->float('TotalTimeOff');
            $table->dateTime('AbsentDate')->nullable();
            $table->dateTime('ApprovedDate')->nullable();
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
        Schema::dropIfExists('absences');
    }
}
