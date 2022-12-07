<?php

namespace Modules\ProjectManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Calendar extends Model
{
    protected $table = 'calendar_events';
    protected $guarded = [];
}
