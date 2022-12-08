<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOvertimeWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('overtime_works', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('UserID');
            $table->timestamp('STime')->nullable();
            $table->timestamp('ETime')->nullable();
            $table->decimal('BreakTime', 4 , 2)->default(0);
            $table->unsignedInteger('ProjectID');
            $table->text('Content');
            $table->unsignedInteger('UpdatedBy')->nullable();
            $table->unsignedTinyInteger('Approved')->default(0);
            $table->string('Note')->nullable();
            $table->string('RequestManager')->nullable();
            $table->timestamp('OTDate')->nullable();
            $table->timestamp('ApprovedDate')->nullable();
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
        Schema::dropIfExists('overtime_works');
    }
}
