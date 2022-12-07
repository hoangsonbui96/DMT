<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkTaskHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_task_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('UserID');
            $table->unsignedInteger('WorkTaskID');
            $table->unsignedInteger('TypeActionID');
            $table->text('Content')->nullable();
            $table->string('Old')->nullable();
            $table->string('New')->nullable();
            $table->string('FieldsName')->nullable();
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
        Schema::dropIfExists('work_task_histories');
    }
}
