<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErrorReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('error_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Descriptions');
            $table->longText('Note')->nullable();
            $table->unsignedBigInteger("WorkTaskID");
            $table->unsignedInteger('AcceptedByID');
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
        Schema::dropIfExists('error_reviews');
    }
}
