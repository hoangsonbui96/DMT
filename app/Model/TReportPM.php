<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TReportPM extends Model
{
    //
    protected $table = 't_report_pms';

    protected $fillable = [
        "UserId", "Content", "StartDate",
        "EndDate", "IdReviewer", "IdMeeting", "Note_Report"
    ];

    public function detail_reports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TDetailReport::class, "report_id");
    }

    public function delete()
    {
        $temp = $this->detail_reports();
        foreach ($temp as $t) {
            $t->delete();
        }
        parent::delete();
    }
}
