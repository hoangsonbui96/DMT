<?php

namespace Modules\ProjectManager\Entities;

use App\DailyReport;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;
    protected $table = 't_tasks';
    protected $guarded = [];

    public function member()
    {
        return $this->belongsTo(User::class, 'UserId', 'id');
    }

    public function giver()
    {
        return $this->belongsTo(User::class, 'GiverId', 'id');
    }

    public function typeName()
    {
        return $this->hasOne(TaskType::class, 'id', 'Type');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'ProjectId', 'id');
    }

    public function phase()
    {
        return $this->belongsTo(Phase::class, 'PhaseId', 'id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'JobId', 'id');
    }

    public function parentTask(){
        return $this->hasOne(Task::class,'id','ParentId');
    }

    public function dailyReports(){
        return $this->hasMany(DailyRerport::class,'TaskID','id')->where('TypeWork','BC009');
    }

    public function lastReport()
    {
        return $this->hasOne(DailyRerport::class,'TaskID','id')->where('TypeWork','BC009')->latest();
    }

    public function lastReportWithIssue(){
        return $this->hasOne(DailyRerport::class,'TaskID','id')->whereNotNull('Issue')->latest();
    }

    public function OT(){
        return $this->hasMany(Task::class,'ParentId','id')->where('SubType','OT');
    }

    public function issues(){
        return $this->hasMany(TaskIssue::class,'task_id','id');
    }
}
