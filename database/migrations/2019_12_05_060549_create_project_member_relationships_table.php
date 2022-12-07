<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectMemberRelationshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_member_relationships', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ProjectId')->nullable();
            $table->unsignedInteger('UserId')->nullable();
            $table->boolean('Leader')->default(0);
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
        Schema::dropIfExists('project_member_relationships');
    }
}
