<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class PushToken extends Model
{
    use SoftDeletes;
    protected $table = 'push_token';
}
