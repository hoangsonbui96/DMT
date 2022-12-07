<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_registrations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('form_id');
            $table->unsignedInteger('user_id');
            $table->string('type_id');
            $table->string('note');
            $table->timestamp('approved_date')->nullable();
            $table->unsignedInteger('approved_user')->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->string('code')->nullable();
//            $table->unsignedInteger('status_id')->nullable();
            $table->unsignedInteger('change_id')->nullable();
            $table->unsignedInteger('total')->nullable();
            $table->text('arr_code')->nullable();
            $table->text('reject_note')->nullable();
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
        Schema::dropIfExists('equipment_registrations');
    }
}
