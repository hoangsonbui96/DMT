<?php

namespace App\Http\Controllers\Admin;

use App\GroupMenuRoleRelationship;
use App\Http\Requests\AdminUserGroup;
use App\Menu;
use App\Role;
use App\RoleScreenDetail;
use App\UserGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Policies\MenuPolicy;

class UserGroupController extends AdminController
{
    
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');
        $array = $this->RoleView('UserGroups',[]);
        $this->data['menu'] = $array['menu'];
        
    }

    public function show($id=null, $del=null)
    {
        $this->authorize('view', $this->menu);
        if(!is_null($del)){
            $this->authorize('delete', $this->menu);
            $group = UserGroup::find($id);
            GroupMenuRoleRelationship::where('GroupId', $group->id)->delete();
            $group->delete();
            return 1;
        }
        if(is_null($id)){
            $this->data['roles'] = Role::all();
            $this->data['menus'] = Menu::query()
                ->whereNull('ParentId')

                ->where('active', 1)
                ->get();
            foreach($this->data['menus'] as $item){
                $item->childMenus = Menu::query()
                    ->where('ParentId', $item->id)
                    ->get();
            }
            $this->data['userGroups'] = UserGroup::all();
            $this->data['add'] = $this->add;
            $this->data['edit'] = $this->edit;
            $this->data['delete'] = $this->delete;
            return view('admin.layouts.'.config('settings.template').'.user-groups', $this->data);
        }else {
            try{
                $this->data['group'] = UserGroup::find($id);
                if ($this->data['group']) {
                    $this->data['roles'] = Role::all();
                    $this->data['menus'] = Menu::query()
                        ->whereNull('ParentId')
                        ->get();
                    $arrParrent = [];
                    $arrParrent[] = $this->data['group']->Name;
                    $arr = [];
                    foreach ($this->data['menus'] as $item) {
//                        $arr[$item->id] = [];
                        $item->childMenus = Menu::where('ParentId', $item->id)->get();
                        if($item->childMenus->count() > 0){
                            foreach ($item->childMenus as $childMenu) {
//                                $arr[$childMenu->id] = [];
                                foreach ($this->data['roles'] as $role) {
                                    $check = GroupMenuRoleRelationship::where('GroupId', $id)
                                        ->where('MenuId', $childMenu->id)
                                        ->where('RoleId', $role->id)
                                        ->first();


                                    if ($check) {
                                        $arr[$childMenu->id][$role->id] = true;

                                    }
                                }
                            }
                        }
                        else{
                            foreach ($this->data['roles'] as $role) {
                                $check = GroupMenuRoleRelationship::where('GroupId', $id)
                                    ->where('MenuId', $item->id)
                                    ->where('RoleId', $role->id)
                                    ->first();


                                if ($check) {
                                    $arr[$item->id][$role->id] = true;

                                }
                            }
                        }


                    }
                    $arrParrent[] = $arr;
                    $arrParrent[] = $this->data['group']->Manager;
                    $this->data['arr'] = $arrParrent;
                    return $arrParrent;
                }
            }catch (\Exception $e)
            {
                return $e->getMessage();
            }

        }

    }

    public function store(AdminUserGroup $request, $id=null)
    {

        try{
            $validated = $request->validated();
//             print_r($validated);
            if($id == null){
                $this->authorize('create', $this->menu);
                $group = new UserGroup();
            }else{
                $this->authorize('update', $this->menu);
                $group = UserGroup::find($id);

            }
            foreach($validated as $key => $value){
                if(Schema::hasColumn('user_groups', $key)){
                    $group->$key = $value;
                }
            }
            if(isset($validated['Manager'])) $group->Manager = 1;
            else $group->Manager = 0;
            $group->updated_at = Carbon::now();
            $group->save();
            //xu ly phan quyen
            GroupMenuRoleRelationship::where('GroupId', $group->id)->delete();
            if(isset($validated['menu'])){
                foreach($validated['menu'] as $keymenu => $value){
                    foreach($value as $keyrole => $value2){
                        $menu = Menu::find($keymenu);
                        $role = Role::find($keyrole);
                        if($menu && $role){
                            $check = GroupMenuRoleRelationship::where('GroupId', $group->id)
                                ->where('MenuId', $keymenu)
                                ->where('RoleId', $keyrole)
                                ->first();
                            if(!$check){
//                                echo $group->id."+".$keymenu.'+'.$keyrole.'\n';
                                GroupMenuRoleRelationship::create(['GroupId' => $group->id, 'MenuId' => $keymenu, 'RoleId' => $keyrole]);
                            }
                        }
                    }
                }
            }
        }
        catch (\Exception $exception){
            return $exception->getMessage();
        }

       // return redirect()->route('admin.UserGroups', [$group->id]);
    }
    public function delete(Request $request, $id=null){
        return "test";
    }
}
