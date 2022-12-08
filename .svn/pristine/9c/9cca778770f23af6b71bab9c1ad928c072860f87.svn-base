<?php

namespace Modules\ProjectManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use SoftDeletes;
    // protected $table = 't_projects';
    protected $table = 'projects';
    protected $guarded = [];

    public $leaderIds = [];
    public $memberIds = [];
    public $phaseMembers = [];

    public function phases()
    {
        return $this->hasMany(Phase::class,'project_id','id');
    }

    public function jobs()
    {
        return $this->hasMany(Job::class,'project_id','id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 't_project_user', 'project_id', 'user_id')->withTrashed();
    }

    public function deletedUsers()
    {
        return $this->belongsToMany(User::class, 't_project_user', 'project_id', 'user_id')->onlyTrashed();
    }

    public function activeUsers()
    {
        return $this->users()->where('users.Active',1);
    }

    public function inActiveUsers()
    {
        return $this->users()->where('users.Active',0);
    }

    public function projectUsers()
    {
        return $this->hasMany(ProjectUser::class, 'project_id', 'id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'ProjectId', 'id');
    }
    
    public function todoTasks(){
        return $this->tasks()->where('Status',1);
    }
    public function doingTasks(){
        return $this->tasks()->where('Status',2);
    }
    public function reviewTasks(){
        return $this->tasks()->where('Status',3);
    }
    public function doneTasks(){
        return $this->tasks()->where('Status',4);
    }

    public function getUserIdsAttribute()
    {
        return $this->projectUsers->pluck('user_id')->toArray();
    }

    public function leaders(){
        return $this->users()->where('t_project_user.is_leader',1);
    }

    public function duration(){
        return $this->tasks();
    }
    public function members(){
        return $this->users()->whereNull('t_project_user.is_leader');
    }

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
