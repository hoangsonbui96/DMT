<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('Type');
            $table->text('Question')->nullable();
            $table->date('SDate')->nullable();
            $table->date('EDate')->nullable();
            $table->boolean('Status')->default(1);
            $table->unsignedInteger('CreateUID')->nullable();
            $table->string('Name')->nullable();
            $table->text('QLink')->nullable();
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
        Schema::dropIfExists('questions');
    }
}
