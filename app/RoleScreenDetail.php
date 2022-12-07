<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleScreenDetail extends Model
{
    public $timestamps=false;

    public function scopeAlias($query, $value){
        if (!$value) return $query;
        return $query->where("alias", "like", $value);
    }
}
