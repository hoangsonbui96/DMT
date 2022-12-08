<?php

namespace App\Policies;

use App\RoleGroupScreenDetailRelationship;
use App\RoleScreenDetail;
use App\RoleUserScreenDetailRelationship;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function before($user, $ability)
    {
        if ($user->role_group == 1) {
            return true;
        }
    }
    public function action(?User $user, RoleScreenDetail $action){
        $checkUserRole = RoleUserScreenDetailRelationship::query()
            ->where('screen_detail_alias', $action->alias)
            ->where('user_id', $user->id)
            ->first();

        if(!$checkUserRole){
            $checkGroupRole = RoleGroupScreenDetailRelationship::query()
                ->where('screen_detail_alias', $action->alias)
                ->where('role_group_id', $user->role_group)
                ->first();
            if($checkGroupRole) return true;
            else return false;
        }else{
            if($checkUserRole->permission == 1) return true;
            else return false;
        }
    }
}
