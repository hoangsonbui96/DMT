<?php

namespace App\Model;

use App\TReportPM;
use Illuminate\Database\Eloquent\Model;

class TDetailReport extends Model
{
    //
    protected $table = 't_detail_reports';

    protected $fillable = ['NameProject', 'Note','report_id'];

    public function report_pm(){
        return $this->belongsTo(TReportPM::class, "report_id", "id");
    }
}
