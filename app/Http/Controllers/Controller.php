<?php

namespace App\Http\Controllers;

use App\Menu;
use App\RoleGroupScreenDetailRelationship;
use App\RoleUserScreenDetailRelationship;
use GuzzleHttp\Client;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Modules\Event\Entities\EventResult;
use Nwidart\Modules\Laravel\Module;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $data;
    protected $menu;

    protected function __construct(Request $request)
    {
        //dd($request->segment());
        if (!in_array($request->segment(1), config('settings.company')) && $request->segment(1) != 'admin') {
            Redirect::to('akb/login')->send();
            exit();
        }
        Config::set('database.default', $request->segment(1) == 'admin' ? config('settings.company')[0] : $request->segment(1));
        $this->data['company'] = $request->segment(1);
        $lang = Cookie::has('lang') ? Crypt::decryptString(Cookie::get('lang')) : 'vi';
        App::setLocale($lang);

        $mainMenus = Menu::query()->whereNull('parentId')->orderBy('Order', 'ASC')->get();

        //dd($mainMenus);
        $mainMenus = $mainMenus->reject(function ($menu) {
            return !Module::isModuleEnabled($menu->Module);
        });
        $this->getChildMenus($mainMenus);

        //dd($this->getChildMenus($mainMenus));

        $this->menu = Menu::query()->where('RouteName', Route::currentRouteName())->first();

        $files = File::directories($this->resources_path('lang/'));
        $nameFolder = [];
        foreach ($files as $file) {
            $nameFolder[] = basename($file);
        }

        $this->data['controller'] = $this;
        $this->data['currentRouteName'] = Route::currentRouteName();
        $this->data['mainMenus'] = $mainMenus;
        $this->data['menu'] = $this->menu;
        $this->data['nameFolder'] = $nameFolder;

    }

    public function getChildMenus($menuData)
    {

        $arrParentId = isset($menuData) ? array_column($menuData->toArray(), 'id') : array();

        if (empty($arrParentId)) {
            return;
        }

        $arrMenuChild = Menu::query()->whereIn('ParentId', $arrParentId)
            ->orderBy('Order', 'ASC')
            ->get();

        foreach ($menuData as $item) {
            $parentId = $item->id;
            $item->childMenus = $arrMenuChild->filter(function ($value, $key) use ($parentId) {
                return $value->ParentId === $parentId;
            });
        }
    }

    public function childMenuCanView($menu, $userId)
    {
        $count = 0;
        $childMenu = Menu::query()->where('ParentId', $menu->id)->get();
//        $checkUserRole = RoleUserScreenDetailRelationship::query()->where('user_id', $userId)->first();
//        $roleData = $checkUserRole
//            ? Menu::query()
//                ->join('role_screens', 'role_screens.alias', 'menus.alias')
//                ->join('role_user_screen_detail_relationships', 'role_user_screen_detail_relationships.screen_detail_alias', 'role_screens.alias')
//                ->where('role_user_screen_detail_relationships.user_id', $userId)
//                ->where('role_user_screen_detail_relationships.permission', '=', 1)
//                ->select('menus.id')
//                ->get()
//            : $roleData = Menu::query()
//                ->join('role_screens', 'role_screens.alias', 'menus.alias')
//                ->join('role_group_screen_detail_relationships', 'role_group_screen_detail_relationships.screen_detail_alias', 'role_screens.alias')
//                ->where('role_group_screen_detail_relationships.role_group_id', $user->role_group)
//                ->select('menus.id')
//                ->get();
        $roleData = $this->getMenuRecord(Auth::user());

        foreach ($childMenu as $item) {
            $itemId = $item->id;
            $result = $roleData->filter(function ($value, $key) use ($itemId) {
                return $value->id === $itemId;
            });

            if ($result->isNotEmpty()) $count++;
        }

        // if($checkUserRole){
        //     foreach($childMenu as $item){
        //         $result = Menu::query()
        //             ->join('role_screens', 'role_screens.alias', 'menus.alias')
        //             ->join('role_user_screen_detail_relationships', 'role_user_screen_detail_relationships.screen_detail_alias', 'role_screens.alias')
        //             ->where('menus.id', $item->id)
        //             ->where('role_user_screen_detail_relationships.user_id', $userId)
        //             ->first();
        //         if($result) $count++;
        //     }
        // }else{
        //     foreach($childMenu as $item){
        //         $result = Menu::query()
        //             ->join('role_screens', 'role_screens.alias', 'menus.alias')
        //             ->join('role_group_screen_detail_relationships', 'role_group_screen_detail_relationships.screen_detail_alias', 'role_screens.alias')
        //             ->where('menus.id', $item->id)
        //             ->where('role_group_screen_detail_relationships.role_group_id', $user->role_group)
        //             ->first();
        //         if($result) $count++;
        //     }
        // }

        return $count;
    }

    public function iff($condition, $a, $b)
    {
        return $condition ? $a : $b;
    }

    public function xssClean($data)
    {
//        $data=str_replace("<","&lt;",$data);
//        $data=str_replace(">","&gt;",$data);
        // Fix &entity\n;
        $data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        } while ($old_data !== $data);

        // we are done...
        return $data;
    }

    public function checkVote($eventId)
    {
        $result = DB::table('answers')
            ->join('event_results', 'answers.id', 'event_results.AID')
            ->join('questions', 'questions.id', 'answers.QID')
            ->where('event_results.UID', Auth::user()->id)
            ->where('questions.id', $eventId)
            ->first();
        if ($result) return 1;
        else return 0;
    }

    public function checkAnswer($answerId)
    {
        $result = EventResult::query()
            ->where('AID', $answerId)
            ->where('UID', Auth::user()->id)
            ->first();
        if ($result) return 1;
        else return 0;
    }

    public function getEquipmentCode($oldCode)
    {
        $stt = substr($oldCode, 3);

        $key = substr($oldCode, 0, 3);

        $newStt = $stt + 1;
        return $key . substr("0000{$newStt}", -4);
    }

    public function getShortName($str)
    {
        $arr = explode(" ", $str);
        if (count($arr) > 1) {
            return strtoupper($arr[0][0] . $arr[count($arr) - 1][0]);
        } else {
            return strtoupper($arr[0][0]);
        }
    }

    //group array by key
    public function group_by($key, $data)
    {
        $result = array();

        foreach ($data as $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
            } else {
                $result[""][] = $val;
            }
        }

        return $result;
    }

    public function convert_vi_to_en($str)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
        $str = preg_replace("/(đ)/", "d", $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
        $str = preg_replace("/(Đ)/", "D", $str);
        //$str = str_replace(" ", "-", str_replace("&*#39;","",$str));
        return $str;
    }

    public function resources_path($path = '')
    {
        return app()->make('path.resources') . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $path);
    }

    public static function getMenuRecord($user)
    {
        $roleData = collect();
        $resultMenuUser = Menu::query()
            ->select('menus.*', 'role_user_screen_detail_relationships.permission')
            ->join('role_screens', 'role_screens.alias', 'menus.alias')
            ->join('role_user_screen_detail_relationships', 'role_user_screen_detail_relationships.screen_detail_alias', 'role_screens.alias')
            ->where('role_user_screen_detail_relationships.user_id', $user->id)
            ->orderBy('role_screens.id')
            ->get();
        if ($resultMenuUser) {
            foreach ($resultMenuUser as $_resultMenuUser) {
                if ($_resultMenuUser->permission == 1) {
                    $roleData = $roleData->push($_resultMenuUser);
                }
            }
        }

        $resultMenuGroup = Menu::query()
            ->select('menus.*')
            ->join('role_screens', 'role_screens.alias', 'menus.alias')
            ->join('role_group_screen_detail_relationships', 'role_group_screen_detail_relationships.screen_detail_alias', 'role_screens.alias')
            ->where('role_group_screen_detail_relationships.role_group_id', $user->role_group)
            ->whereNotIn('menus.id', array_column($resultMenuUser->toArray(), 'id'))
            ->orderBy('role_screens.id')
            ->get();
        if ($resultMenuGroup) {
            $roleData = $roleData->concat($resultMenuGroup);
        }

        if ($roleData) {
            $roleData = $roleData->sortBy('id', SORT_NUMERIC, false);
        }

        return $roleData;
    }

    public static function getRoleUser($user)
    {
        $role = array();
        $listRoleUser = array();

        $roleUser = RoleUserScreenDetailRelationship::query()
            ->where('user_id', '=', $user->id)
            ->get()->toArray();
        if ($roleUser) {
            $listRoleUser = array_column($roleUser, 'screen_detail_alias');
            foreach ($roleUser as $_roleUser) {
                if ($_roleUser['permission'] == 1) {
                    $role[] = $_roleUser['screen_detail_alias'];
                }
            }
        }

        $roleGroup = RoleGroupScreenDetailRelationship::query()
            ->whereNotIn('screen_detail_alias', $listRoleUser)
            ->where('role_group_id', '=', $user->role_group)
            ->get()->toArray();
        if ($roleGroup) {
            foreach ($roleGroup as $_roleGroup) {
                $role[] = $_roleGroup['screen_detail_alias'];
            }
        }

        return $role;
    }

    public static function sendNoticationRealTime($device_token, $title, $body)
    {
        $SERVER_API_KEY = config('app.server_api_key');

        $data = [
            "to" => $device_token,
            'priority' => 'high',
            "notification" => [
                "title" => $title,
                "body" => $body,
            ]
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization' => 'key=' . $SERVER_API_KEY,
            'Content-Type' => 'application/json',
        ];

        $client = new Client();

        //Log::useFiles(storage_path().'/logs/name-of-log.log');
        //Log::info(Carbon::now());

        $response = $client->post('https://fcm.googleapis.com/fcm/send', [
            'headers' => $headers,
            'body' => $dataString
        ]);
        return $response;
    }

//    protected function isModuleEnabled($module): bool
//    {
//        return array_key_exists($module, Module::allEnabled());
//    }
}
