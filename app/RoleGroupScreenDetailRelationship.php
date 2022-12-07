<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleGroupScreenDetailRelationship extends Model
{
    //

    public function scopeRoleGroup($query, $value){
        if (!$value) return $query;
        return $query->select('role_group_id')->where('screen_detail_alias', $value);
    }
}
