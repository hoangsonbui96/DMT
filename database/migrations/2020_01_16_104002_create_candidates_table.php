<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCandidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('JobID')->nullable();
            $table->string('CVpath')->nullable();
            $table->string('FullName')->nullable();
            $table->string('Email')->nullable();
            $table->string('Tel')->nullable();
            $table->date('Birthday')->nullable();
            $table->string('PerAddress')->nullable();
            $table->string('CurAddress')->nullable();
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
        Schema::dropIfExists('candidates');
    }
}
