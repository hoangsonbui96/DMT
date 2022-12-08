<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('DataKey', 10);
            $table->string('Name', 30);
            $table->string('TypeName', 30);
            $table->string('DataValue', 30); 
            $table->string('DataDescription')->nullable();
            $table->unsignedSmallInteger('DataDisplayOrder');
            $table->boolean('PermissionEdit')->default(1);
            $table->boolean('PermissionDelete')->default(1);
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
        Schema::dropIfExists('master_data');
    }
}
