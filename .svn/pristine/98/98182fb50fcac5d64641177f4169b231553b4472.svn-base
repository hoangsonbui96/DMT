<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCapicityProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('capicity_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('UserID');
            $table->string('LevelEN')->nullable();
            $table->string('LevelJA')->nullable();
            $table->decimal('YearExperience', 4, 2)->nullable();
            $table->decimal('YearInJA', 4, 2)->nullable();
            $table->string('CVFile')->nullable();
            $table->string('CapacityOther')->nullable();
            $table->string('Favorite')->nullable();
            $table->string('Note')->nullable();
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
        Schema::dropIfExists('capicity_profiles');
    }
}
