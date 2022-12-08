<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('Name');
            $table->unsignedInteger('CreatedID');
            $table->text('Description');
            $table->text('Note')->nullable();
            $table->integer('Status');
            $table->text('Tags')->nullable();
            $table->date('StartDate')->nullable();
            $table->date('EndDate')->nullable();
            $table->unsignedInteger('ProjectID');
            $table->unsignedInteger("NumberReturn")->default(0);
            $table->integer('Important')->default(0);
            $table->integer('Position')->default(0);
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
        Schema::dropIfExists('work_tasks');
    }
}
