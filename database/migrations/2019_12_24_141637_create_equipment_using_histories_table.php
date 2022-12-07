<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentUsingHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_using_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->unsignedInteger('user_owner')->nullable();
            $table->timestamp('deal_date')->nullable();
            $table->unsignedInteger('register_id')->nullable();
            $table->string('note')->nullable();
            $table->unsignedInteger('created_user');
            $table->unsignedTinyInteger('deal_flag');
            $table->unsignedInteger('old_user_owner')->nullable();
//            $table->unsignedInteger('old_user_owner');
            $table->unsignedInteger('status_id')->nullable();
            $table->unsignedInteger('old_status_id')->nullable();
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
        Schema::dropIfExists('equipment_using_histories');
    }
}
