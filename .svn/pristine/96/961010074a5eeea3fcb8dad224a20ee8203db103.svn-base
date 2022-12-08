<?php

namespace Modules\ProjectManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectUser extends Model
{
    use SoftDeletes;
    protected $table = 't_project_user';
    protected $guarded = [];

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }

}
