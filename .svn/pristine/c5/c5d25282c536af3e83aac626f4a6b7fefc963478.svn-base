<?php

namespace Modules\ProjectManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskIssue extends Model
{
    use SoftDeletes;
    protected $table = 'task_issues';
    protected $guarded = [];
 
    public function issuer(){
        return $this->hasOne(User::class,'id','issued_by');
    }
}
