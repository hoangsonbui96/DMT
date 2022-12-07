<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailyReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('UserID')->nullable();
            $table->date('Date')->nullable();
            $table->date('DateCreate')->nullable();
            $table->integer('ProjectID')->nullable();
            $table->string('ScreenName')->nullable();
            $table->string('TypeWork')->nullable();
            $table->string('Contents')->nullable();
            $table->decimal('WorkingTime',6,2)->default(0);
            $table->decimal('Progressing',5,2)->default(0);
            $table->decimal('Delay',4,2)->default(0)->nullable();
            $table->string('Note')->nullable();
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
        Schema::dropIfExists('daily_reports');
    }
}
