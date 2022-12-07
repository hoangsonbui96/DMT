<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDbLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('db_levels', function (Blueprint $table) {
//            $table->increments('id');
            $table->unsignedInteger('UserID');
            $table->unsignedInteger('DBSkillID');
            $table->decimal('Level',2,1)->nullable();
            $table->decimal('YearExp',3,1)->nullable();
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
        Schema::dropIfExists('db_levels');
    }
}
