<?php

namespace Modules\ProjectManager\Http\Repositories;
use App\Http\Controllers\Admin\AdminController;
use Modules\ProjectManager\Entities\User;

class UserRepo extends AdminController
{
    public function getAll(){
        return User::withTrashed()->get();
    }

    public function get(){
        return User::query()
        ->select('id', 'FullName')
        ->where('Active', 1)
        ->where('role_group','!=', 1)
        ->get();
    }

    public function getById($request){
        $user = User::find($request->userId);
        return $user;
    }
}