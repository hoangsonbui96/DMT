<?php

namespace Modules\ProjectManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phase extends Model
{
    use SoftDeletes;
    protected $table = 't_phases';
    protected $guarded = [];
    public $leaderIds = [];
    public $memberIds = [];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function phase(){
        return $this->hasOne(Phase::class,'id','id');
    }
    public function phaseType()
    {
        return $this->hasOne(MasterData::class, 'DataValue', 'type');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'PhaseId', 'id');
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

    public function taskTypes()
    {
        return $this->hasMany(TaskType::class, 'phase_type', 'type');
    }

    public function doneTasks()
    {
        return $this->tasks()->where('Status', 4);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 't_tasks', 'PhaseId', 'UserId')->groupBy('users.id')->withTrashed();
    }

    public function leaders()
    {
        return $this->hasOne(User::class, 'id', 'leader_id');
    }
}
