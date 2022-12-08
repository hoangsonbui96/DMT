<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDayToTimekeepingsNewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('timekeepings_new', function (Blueprint $table) {
            if (!Schema::hasColumn('timekeepings_new', 'Day')) {
                $table->unsignedInteger('Day')->after('UserID');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('timekeepings_new', function (Blueprint $table) {
            if (Schema::hasColumn('timekeepings_new', 'Day')) {
                $table->dropColumn('Day');
            }
        });
    }
}
