<?php

namespace App\Policies;

use App\GroupMenuRoleRelationship;
use App\Menu;
use App\Role;
use App\RoleGroupScreenDetailRelationship;
use App\RoleScreenDetail;
use App\RoleUserScreenDetailRelationship;
use App\User;
use App\UserGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class MenuPolicy
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
//    public function before($user, $ability)
//    {
//        if ($user->role_group == 2) {
//            return true;
//        }
//    }
    public function view(?User $user, Menu $menu){
        $role = Menu::query()
            ->join('role_screens', 'role_screens.alias', 'menus.alias')
            ->join('role_user_screen_detail_relationships', 'role_user_screen_detail_relationships.screen_detail_alias', 'menus.alias')
            ->where('menus.id', $menu->id)
            ->where('role_user_screen_detail_relationships.user_id', $user->id)
            ->first();
        if($role && $role->permission == 1) {
            return true;
        } elseif ($role && $role->permission == 0) {
            return false;
        } else {
            $role = Menu::query()
                ->join('role_screens', 'role_screens.alias', 'menus.alias')
                ->join('role_group_screen_detail_relationships', 'role_group_screen_detail_relationships.screen_detail_alias', 'menus.alias')
                ->where('menus.id', $menu->id)
                ->where('role_group_screen_detail_relationships.role_group_id', $user->role_group)
                ->first();
            if($role) {
                return true;
            } else {
                return false;
            }
        }

    }

    //backup view
//    public function view(?User $user, Menu $menu){
//        $role = Role::query()
//            ->where('Action', 'view')
//            ->first();
//        return GroupMenuRoleRelationship::query()
//            ->where('GroupId', $user->GroupId)
//            ->where('MenuId', $menu->id)
//            ->where('RoleId', $role->id)
//            ->first();
//
//    }

    public function admin(?User $user, Menu $menu){
        if($user->role_group == 2 || $user->role_group == 1) return true;
        else return false;
//        $role = Role::query()
//            ->where('Action', 'admin')
//            ->first();
//        $result = GroupMenuRoleRelationship::query()
//            ->where('GroupId', $user->GroupId)
//            ->where('MenuId', $menu->id)
//            ->where('RoleId', $role->id)
//            ->first();
//        if($result) return true;
//        else return 0;
    }

    public function create(?User $user, Menu $menu){
        $role = Role::query()
            ->where('Action', 'create')
            ->first();
        return GroupMenuRoleRelationship::query()
            ->where('GroupId', $user->GroupId)
            ->where('MenuId', $menu->id)
            ->where('RoleId', $role->id)
            ->first();

    }

    public function update(?User $user, Menu $menu){
        $role = Role::query()
            ->where('Action', 'update')
            ->first();
        return GroupMenuRoleRelationship::query()
            ->where('GroupId', $user->GroupId)
            ->where('MenuId', $menu->id)
            ->where('RoleId', $role->id)
            ->first();

    }

    public function delete(?User $user, Menu $menu){
        $role = Role::query()
            ->where('Action', 'delete')
            ->first();
        return GroupMenuRoleRelationship::query()
            ->where('GroupId', $user->GroupId)
            ->where('MenuId', $menu->id)
            ->where('RoleId', $role->id)
            ->first();

    }
}
