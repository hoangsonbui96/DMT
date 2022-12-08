<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupMenuRoleRelationshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_menu_role_relationships', function (Blueprint $table) {
//            $table->increments('id');
            $table->unsignedInteger('GroupId')->nullable();
            $table->unsignedInteger('MenuId')->nullable();
            $table->unsignedInteger('RoleId')->nullable();
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
        Schema::dropIfExists('group_menu_role_relationships');
    }
}
