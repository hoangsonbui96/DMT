<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    //

    public function scopeRouteName($query, $value){
        if (!$value) return $query;
        else return $query->where('RouteName', $value);
    }
}
