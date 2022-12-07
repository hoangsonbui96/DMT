<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTTaskRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_task_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('requestUserID');
            $table->timestamp('requestTime');
            $table->string('sumaryContent');
            $table->string('requestContent');
            $table->boolean('isPrivate');
            $table->unsignedInteger('projectID');
            $table->unsignedInteger('receiveUserID');
            $table->string('responseContent')->nullable();
            $table->timestamp('responseTime')->nullable();
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
        Schema::dropIfExists('t_task_requests');
    }
}
