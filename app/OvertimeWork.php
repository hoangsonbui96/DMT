<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimeWork extends Model
{
    protected $table = 'overtime_works';
    use SoftDeletes;
}
