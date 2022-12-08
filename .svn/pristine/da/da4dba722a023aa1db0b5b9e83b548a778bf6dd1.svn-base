<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Menu;
use Route;

class HomeController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');
    }

    public function index(){
        if(Auth::user()->role_group == 1){
            return redirect()->route('admin.RoleSetup');
        }

        $roleData = $this->getMenuRecord(Auth::user())->toArray();

        usort($roleData, function($a, $b) {
            return $a['id'] <=> $b['id'];
        });

        $routerRedirect = 'logout';

        if($roleData && count($roleData) > 0 && Route::has($roleData[0]['RouteName'])){
            $routerRedirect =  $roleData[0]['RouteName'];
        }

        return redirect()->route($routerRedirect);
    }
}
