<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RoleUserScreenDetailRelationship extends Model
{
    //

    public function scopePermission($query, $value){
        if (!$value && !Auth::check()) return $query;
        return  RoleUserScreenDetailRelationship::query()
            ->where('screen_detail_alias', $value)
            ->where('user_id', \auth()->id())
            ->where('permission', 1);
    }
}
