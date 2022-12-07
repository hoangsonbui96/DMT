<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    public function workTasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WorkTask::class, 'ProjectID');
    }

    public function scopeNameVi($query, $value){
        if (!$value) return $query;
        return $query->where('NameVi', 'like', '%'. $value .'%');
    }

    public function scopeNameEn($query, $value){
        if (!$value) return $query;
        return $query->where('NameEn', 'like', '%'. $value .'%');
    }

    public function scopeNameJa($query, $value){
        if (!$value) return $query;
        return $query->where('NameJa', 'like', '%'. $value .'%');
    }

    public function scopeNameShort($query, $value){
        if (!$value) return $query;
        return $query->where('NameShort', 'like', '%'. $value .'%');
    }
    public function scopeLeader($query,  $arr){
        if (empty($arr)) return $query;
        foreach ($arr as $id){
            $query->where('Leader', 'like', '%,'.$id.',%');
        }
        return $query;
    }

    public function scopeMember($query,  $arr){
        if (empty($arr)) return $query;
        foreach ($arr as $id){
            $query->where('Member', 'like', '%,'.$id.',%');
        }
        return $query;
    }


    public function scopeOrLeader($query,  $arr){
        if (empty($arr)) return $query;
        foreach ($arr as $id){
            $query->orWhere('Leader', 'like', '%,'.$id.',%');
        }
        return $query;
    }

    public function scopeOrMember($query,  $arr){
        if (empty($arr)) return $query;
        foreach ($arr as $id){
            $query->orWhere('Member', 'like', '%,'.$id.',%');
        }
        return $query;
    }


    public function scopeOrNameVi($query, $value){
        if (!$value) return $query;
        return $query->orWhere('NameVi', 'like', '%'. $value .'%');
    }

    public function scopeOrNameEn($query, $value){
        if (!$value) return $query;
        return $query->orWhere('NameEn', 'like', '%'. $value .'%');
    }

    public function scopeOrNameJa($query, $value){
        if (!$value) return $query;
        return $query->orWhere('NameJa', 'like', '%'. $value .'%');
    }

    public function scopeOrNameShort($query, $value){
        if (!$value) return $query;
        return $query->orWhere('NameShort', 'like', '%'. $value .'%');
    }

    public function scopeOrCustomer($query, $value){
        if (!$value) return $query;
        return $query->orWhere('Customer', 'like', '%' . $value . '%');
    }

    public function scopeInLeaderOrMember($query, $value){
        if (!$value) return $query;
        return $query->where("Leader", "like", "%,". $value .",%")->orWhere("Member", "like", "%,". $value .",%");
    }
}
