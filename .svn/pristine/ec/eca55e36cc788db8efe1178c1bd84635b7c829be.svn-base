<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->string('type_id')->nullable();
            $table->string('serial_number')->nullable();
            $table->text('info')->nullable();
            $table->string('provider')->nullable();
            $table->timestamp('buy_date')->nullable();
            $table->timestamp('period_date')->nullable();
            $table->timestamp('deal_date')->nullable();
//            $table->timestamp('updated_date')->nullable();
            $table->decimal('unit_price', 14, 2)->nullable();
            $table->string('note')->nullable();
            $table->unsignedInteger('updated_user')->nullable();
            $table->unsignedSmallInteger('status_id')->nullable();
            $table->unsignedInteger('user_owner')->nullable();
            $table->unsignedInteger('register_id')->nullable();
            $table->unsignedInteger('room_id')->nullable();
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
        Schema::dropIfExists('equipment');
    }
}
