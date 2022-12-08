<?php

namespace App\Http\Controllers\Admin;

use App\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class MenuController extends AdminController
{
    //
    public function __construct(Request $request)
    {
        if (strpos($request->getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
    }
    public function menuList()
    {
        $mainMenus = Menu::query()->whereNull('parentId')->orderBy('Order', 'ASC')->get();
        $this->getChildMenus($mainMenus);
        $this->menu = Menu::query()->where('RouteName', Route::currentRouteName())->first();

        $this->data['currentRouteName'] = Route::currentRouteName();
        $this->data['mainMenus'] = $mainMenus;
        $this->data['menu'] = $this->menu;
        return AdminController::responseApi(200, '', '', $this->data);
    }

    public function getAllMenuApi()
    {
        $data = array();
        $list_menu = Menu::orderBy('id')->get();
        foreach ($list_menu as $menu) {
            $menu->name = __('menu.' . $menu['LangKey']);
            unset($menu['RouteName']);
            unset($menu['LangKey']);
            unset($menu['created_at']);
            unset($menu['updated_at']);
        }
        $data['menus'] = $list_menu->toArray();
        return AdminController::responseApi(200, '', '', $data);
    }

    public function getMenuApi() {
        $list_menu = self::getMenuRecord(Auth::user())->values()->all();
        foreach ($list_menu as $menu) {
            $menu['name'] = __('menu.' . $menu['LangKey']);
            unset($menu['RouteName']);
            unset($menu['LangKey']);
            unset($menu['created_at']);
            unset($menu['updated_at']);
        }
        $data = ['menus' => $list_menu];
        return AdminController::responseApi(200, '', '', $data);
    }
}
