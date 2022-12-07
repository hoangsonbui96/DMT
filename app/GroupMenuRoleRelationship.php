<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupMenuRoleRelationship extends Model
{
    protected $fillable=['GroupId', 'MenuId', 'RoleId'];
}
