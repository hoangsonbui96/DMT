<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarsViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("
            CREATE OR REPLACE VIEW  calendars_views
            AS
            SELECT meeting_schedules.`id` as id,'H' as DataKey,DATE_FORMAT(`MeetingDate`,'%Y-%m-%d') as StartDate,DATE_FORMAT(`MeetingDate`,'%Y-%m-%d') as EndDate,MeetingTimeFrom, MeetingTimeTo,Purpose as Content,Participant,MeetingHostID FROM meeting_schedules
            UNION ALL
            (SELECT absences.`id` as `id`,'VM' as DataKey,DATE_FORMAT(`SDate`,'%Y-%m-%d') as StartDate,DATE_FORMAT(`EDate`,'%Y-%m-%d') as EndDate,DATE_FORMAT(`SDate`,'%T')  as MeetingTimeFrom,DATE_FORMAT(`EDate`,'%T') as MeetingTimeTo,Reason as Content,UID as Participant,'' as MeetingHostID FROM absences WHERE  MasterDataValue = 'VM006')
            UNION ALL
            SELECT calendar_events.`id` as `id`,CONVERT(CalendarID, CHAR(50)) AS DataKey,DATE_FORMAT(`StartDate`,'%Y-%m-%d') as StartDate,DATE_FORMAT(`EndDate`,'%Y-%m-%d') as EndDate,DATE_FORMAT('', '%T') as MeetingTimeFrom,DATE_FORMAT('', '%T') as MeetingTimeTo,Content,'' as Participant,'' as MeetingHostID FROM calendar_events
            UNION ALL
            SELECT questions.`id` as `id`,'' as DataKey,DATE_FORMAT(`SDate`,'%Y-%m-%d') as StartDate,DATE_FORMAT(`EDate`,'%Y-%m-%d') as EndDate,DATE_FORMAT('', '%T') as MeetingTimeFrom,DATE_FORMAT('', '%T') as MeetingTimeTo,questions.`Name` as Content,'' as Participant,'' as MeetingHostID FROM questions

        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendars_views');
    }
}
