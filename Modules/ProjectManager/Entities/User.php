<?php

namespace Modules\ProjectManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    protected $table = 'users';
    protected $hidden = [
        'password'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'UserId', 'id');
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

    public function projectUsers()
    {
        return $this->hasMany(ProjectUser::class, 'user_id', 'id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 't_project_user', 'project_id', 'user_id');
    }

    public function project()
    {
        return $this->projects()->first();
    }

    public function projectTasks()
    {
        return $this->project()->tasks;
    }
}
