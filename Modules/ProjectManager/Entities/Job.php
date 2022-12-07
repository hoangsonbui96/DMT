<?php

namespace Modules\ProjectManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use SoftDeletes;
    protected $table = 't_jobs';
    protected $guarded = [];
    public $leaderIds = [];
    public $memberIds = [];

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function phases(){
        return $this->belongsToMany(Phase::class,'t_tasks','JobId','PhaseId');
    }

    public function jobType(){
        return $this->hasOne(MasterData::class,'id','type');
    }

    public function tasks(){
        return $this->hasMany(Task::class,'JobId','id');
    }
    public function todoTasks()
    {
        return $this->tasks()->where('Status', 1);
    }
    public function doingTasks()
    {
        return $this->tasks()->where('Status', 2);
    }
    public function reviewTasks()
    {
        return $this->tasks()->where('Status', 3);
    }

    public function doneTasks()
    {
        return $this->tasks()->where('Status', 4);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 't_tasks', 'JobId', 'UserId')->groupBy('UserId')->withTrashed();
    }
}
