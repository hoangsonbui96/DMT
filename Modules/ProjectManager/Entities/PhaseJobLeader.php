<?php

namespace Modules\ProjectManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhaseJobLeader extends Model
{
    protected $table = 't_phase_job_leader';
    use SoftDeletes;
    protected $guarded = [];
}
