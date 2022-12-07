<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentOfferDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_offer_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('EquipmentOfferID');
            $table->text('Description');
            $table->integer('Quantity');
            $table->decimal('UnitPrice', 14, 2);
            $table->decimal('FinalUnitPrice', 14, 2);
            $table->decimal('Price', 14, 2);
            $table->string('BuyAddress');
            $table->date('BuyDate');
            $table->unsignedInteger('BuyUserID');
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
        Schema::dropIfExists('equipment_offer_detail');
    }
}
