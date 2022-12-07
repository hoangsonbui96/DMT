<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentOfferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_offer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('OfferDate');
            $table->unsignedInteger('OfferUserID');
            $table->unsignedInteger('ApprovedUserID');
            $table->tinyInteger('Approved');
            $table->dateTime('ApprovedDate')->nullable();
            $table->text('Note')->nullable();
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
        Schema::dropIfExists('equipment_offer');
    }
}
