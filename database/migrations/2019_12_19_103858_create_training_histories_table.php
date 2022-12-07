<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainingHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_histories', function (Blueprint $table) {
//            $table->bigIncrements('id');
            $table->unsignedInteger('UserID');
            $table->date('SYear')->nullable();
            $table->date('EYear')->nullable();
            $table->string('Content')->nullable();
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
        Schema::dropIfExists('training_histories');
    }
}
