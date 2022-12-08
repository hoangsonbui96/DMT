<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('FullName');
            $table->string('email')->nullable();
            $table->string('username', 30);
            $table->text('avatar')->nullable();
            $table->boolean('Gender')->nullable();
            $table->string('Tel', 15)->nullable();
            $table->date('Birthday')->nullable();
            $table->text('PerAddress')->nullable();
            $table->text('CurAddress')->nullable();
            $table->boolean('MaritalStt')->default(0);
            $table->time('STimeOfDay')->nullable();
            $table->time('ETimeOfDay')->nullable();
            $table->date('SDate')->nullable();
            $table->date('expirationdate')->nullable();
            $table->date('OfficialDate')->nullable();
            $table->date('DaysOff')->nullable();
            $table->text('Note')->nullable();
            $table->boolean('Active')->default(0);
            $table->boolean('FlagManageDevice')->default(0);
            $table->unsignedMediumInteger('RoomId')->nullable();
            $table->unsignedMediumInteger('DepartmentId')->nullable();
            $table->string('TimeRemain', 50)->nullable();
            $table->boolean('deleted')->default(0);
            $table->string('IDFM', 3)->nullable();
            $table->string('RelativeName', 30)->nullable();
            $table->string('Relationship', 70)->nullable();
            $table->string('TelRelative', 20)->nullable();
            $table->string('Facebook', 100)->nullable();
            $table->string('Zalo', 100)->nullable();
            $table->string('Instagram', 100)->nullable();
            $table->longText('social')->nullable();
            $table->boolean('isAdmin')->default(0);
            // $table->unsignedInteger('GroupId')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('settings')->nullable();
            $table->softDeletes();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
