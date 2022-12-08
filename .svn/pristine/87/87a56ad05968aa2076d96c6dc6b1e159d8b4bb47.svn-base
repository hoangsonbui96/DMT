<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ParentId')->nullable();
            $table->string('FontAwesome')->nullable();
            $table->text('CssClass')->nullable();
            $table->string('RouteName')->nullable();
            $table->string('LangKey')->nullable();
            $table->unsignedSmallInteger('Order')->nullable();
            $table->boolean('active')->default(1);
            $table->string('alias', 30)->nullable();
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
        Schema::dropIfExists('menus');
    }
}
