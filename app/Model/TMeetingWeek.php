<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TMeetingWeek extends Model
{

    protected $table = 't_meeting_weeks';

    protected $fillable = [
        "MeetingName", "RegisterId", "ChairID", "ProjectId", "MeetingTimeFrom",
        "MeetingTimeTo", "Participant", "Secret", "Evaluation", "TimeEnd"
    ];

    public function delete()
    {
        $this->TReportPms()->delete();
        parent::delete();
    }

    public function TReportPms()
    {
        return $this->hasMany(TReportPM::class, "IdMeeting");
    }
}
