<?php

namespace Modules\ProjectManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectUserDetail extends Model
{
    protected $table = 't_project_user_detail';
    use SoftDeletes;
    protected $guarded = [];

    public function projectUser(){
        return $this->hasOne(ProjectUser::class,'id','project_user_id');
    }
}
