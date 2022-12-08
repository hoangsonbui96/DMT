<?php

namespace App\Http\Controllers\Admin;

use App\Menu;
use App\RoleGroupScreenDetailRelationship;
use App\RoleScreen;
use App\RoleScreenDetail;
use App\RoleUserGroup;
use App\RoleUserScreenDetailRelationship;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use ZipArchive;

class RoleGroupController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $listGroupUser;
    protected $listUserActive;
    const KEYMENU= array(
        "add" => "RoleGroupsAdd",
        "view" => "RoleGroups",
        "edit" => "RoleGroupsEdit",
        "delete" => "RoleGroupsDelete",
    );
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');
        $array = $this->RoleView(null,['RoleGroups']);
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if($value == $row->alias)
                    $this->$key = $row;
            } 
        }
        $this->listGroupUser = RoleUserGroup::query()
            ->where('alias', '!=', 'root')
            ->get();

        $this->listUserActive = $this->GetListUser(self::USER_ACTIVE_FLAG);
    }

    public function index(){
        $this->authorize('view', $this->menu);
        $this->data['userGroups'] = $this->listGroupUser;
        return view('admin.layouts.'.config('settings.template').'.role.role-groups', $this->data);
    }

    public function showDetail($groupId, Request $request, $orderBy = 'name', $sortBy = 'asc'){
        $query = RoleScreen::query()
            ->select(
                'role_screens.name as ScreenName',
                'role_screens.alias as ScreenAlias',
                'role_screen_details.name as ScreenDetailName',
                'role_screen_details.alias as ScreenDetailAlias'

            )
            ->join(
                'role_screen_details',
                'role_screen_details.role_screen_alias',
                'role_screens.alias'
            )
            ->orderBy('role_screen_details.role_screen_alias', 'asc');


        $this->data['list'] = $query->get();
        //kiểm tra xem role có được check hay không?
        foreach($this->data['list'] as $item){
            $one = RoleGroupScreenDetailRelationship::query()
                ->where('role_group_id', $groupId)
                ->where('screen_detail_alias', $item->ScreenDetailAlias)
                ->first();
            if($one){
                $item->checked = true;
            }else{
                $item->checked = false;
            }
        }
        $this->data['group'] = RoleUserGroup::find($groupId);
        return view('admin.includes.role-group-detail', $this->data);
    }

    public function store($id, Request $request) {

    }

    public function AjaxRoleScreen($id, $groupId = null, $userId = null){
        if(is_null($userId)){
            //get list detail
            $screenDetailList = RoleScreenDetail::query()
                ->where('alias', '!=', $id)
                ->where('role_screen_alias', $id)
                ->get();
            foreach($screenDetailList as $item){
                $check = RoleGroupScreenDetailRelationship::query()
                    ->where('role_group_id', $groupId)
                    ->where('screen_detail_alias', $item->alias)
                    ->first();
                if($check) $item->checked = 1;
                else $item->checked = 0;
            }
            return $screenDetailList;
        }else{
            //get list detail
            $screenDetailList = RoleScreenDetail::query()
                ->where('alias', '!=', $id)
                ->where('role_screen_alias', $id)
                ->get();
            foreach($screenDetailList as $item){
                $check = RoleUserScreenDetailRelationship::query()
                    ->where('user_id', $userId)
                    ->where('screen_detail_alias', $item->alias)
                    ->where('permission', '=', 1)
                    ->first();
                if($check) $item->checked = 1;
                else $item->checked = 0;
            }
            return $screenDetailList;
        }

    }

    public function AjaxRoleScreenDetailInput($groupId, $checked, $id, $userId = null){
//        DB::enableQueryLog();
        if(is_null($userId)){
            //update quyền cho nhóm
            if($checked == 'true'){
                $data = RoleGroupScreenDetailRelationship::query()
                    ->where('role_group_id', $groupId)
                    ->where('screen_detail_alias', $id)
                    ->first();
                if(!$data){
                    $new = new RoleGroupScreenDetailRelationship();
                    $new->role_group_id = $groupId;
                    $new->screen_detail_alias = $id;
                    $new->save();
                }
            }else{
                RoleGroupScreenDetailRelationship::query()
                    ->where('role_group_id', $groupId)
                    ->where('screen_detail_alias', $id)
                    ->delete();
            }
            return $checked;
        }else{
            //kiểm tra xem user đã được gán quyền chưa
//            $checkUserHasRole = RoleUserScreenDetailRelationship::query()
//            ->where('user_id', $userId)
//            ->first();
//            if(!$checkUserHasRole){
//                //user chưa được gán quyền riêng, chuyển quyền từ nhóm sang trước khi thực hiện thay đổi
//                $listGroupRole = RoleGroupScreenDetailRelationship::query()
//                    ->join(
//                        'users',
//                        'users.role_group',
//                        'role_group_screen_detail_relationships.role_group_id'
//                    )
//                    ->where('users.id', $userId)
//                    ->select('users.id', 'role_group_screen_detail_relationships.screen_detail_alias')
//                    ->get();
//                //insert role cho user
//                foreach($listGroupRole as $item){
//                    $userRole = new RoleUserScreenDetailRelationship();
//                    $userRole->user_id = $item->id;
//                    $userRole->screen_detail_alias = $item->screen_detail_alias;
//                    $userRole->save();
//                }
//            }
            //update quyền cho user
            $data = RoleUserScreenDetailRelationship::query()
                ->where('user_id', $userId)
                ->where('screen_detail_alias', $id)
                ->first();
            if(!$data){
                $new = new RoleUserScreenDetailRelationship();
                $new->user_id = $userId;
                $new->screen_detail_alias = $id;
                $new->permission = 1;
                $new->save();
            }
            if($checked == 'true'){
                RoleUserScreenDetailRelationship::query()
                    ->where('user_id', $userId)
                    ->where('screen_detail_alias', $id)
                    ->update(['permission' => 1]);
            }else{
                RoleUserScreenDetailRelationship::query()
                    ->where('user_id', $userId)
                    ->where('screen_detail_alias', $id)
                    ->update(['permission' => 0]);
            }
            return $checked;
        }
    }

    public function AjaxRoleScreenDetailInputAll(Request $request){
        DB::beginTransaction();
        try{
            $listRole = explode(',', $request->input('data'));
            if($request->has('groupId')){
                //phan quyen cho nhom
                foreach($listRole as $item){
                    if($request->input('checked') == 'true'){
                        $data = RoleGroupScreenDetailRelationship::query()
                            ->where('role_group_id', $request->input('groupId'))
                            ->where('screen_detail_alias', $item)
                            ->first();
                        if(!$data){
                            $new = new RoleGroupScreenDetailRelationship();
                            $new->role_group_id = $request->input('groupId');
                            $new->screen_detail_alias = $item;
                            $new->save();
                        }
                    }else{
                        RoleGroupScreenDetailRelationship::query()
                        ->where('role_group_id', $request->input('groupId'))
                        ->where('screen_detail_alias', $item)
                        ->delete();
                    }
                }
            }elseif($request->has('userId')){
                //phan quyen cho user

//                //kiểm tra xem user đã được gán quyền chưa
//                $checkUserHasRole = RoleUserScreenDetailRelationship::query()
//                    ->where('user_id', $request->input('userId'))
//                    ->first();
//                if(!$checkUserHasRole){
//                    //user chưa được gán quyền riêng, chuyển quyền từ nhóm sang trước khi thực hiện thay đổi
//                    $listGroupRole = RoleGroupScreenDetailRelationship::query()
//                        ->join(
//                            'users',
//                            'users.role_group',
//                            'role_group_screen_detail_relationships.role_group_id'
//                        )
//                        ->where('users.id', $request->input('userId'))
//                        ->select('users.id', 'role_group_screen_detail_relationships.screen_detail_alias')
//                        ->get();
//                    //insert role cho user
//                    foreach($listGroupRole as $item){
//                        $userRole = new RoleUserScreenDetailRelationship();
//                        $userRole->user_id = $item->id;
//                        $userRole->screen_detail_alias = $item->screen_detail_alias;
//                        $userRole->save();
//                    }
//                }
                foreach($listRole as $item){
                    $data = RoleUserScreenDetailRelationship::query()
                        ->where('user_id', $request->input('userId'))
                        ->where('screen_detail_alias', 'like', $item)
                        ->first();
                    if(!$data){
                        $data = new RoleUserScreenDetailRelationship();
                        $data->user_id = $request->input('userId');
                        $data->screen_detail_alias = $item;
                        $data->permission = 1;
                        $data->save();
                    }
                    if($request->input('checked') == 'true'){
                        RoleUserScreenDetailRelationship::query()
                            ->where('user_id', $request->input('userId'))
                            ->where('screen_detail_alias', 'like', $item)
                            ->update(['permission' => 1]);
                    }else{
                        RoleUserScreenDetailRelationship::query()
                            ->where('user_id', $request->input('userId'))
                            ->where('screen_detail_alias', 'like', $item)
                            ->update(['permission' => 0]);
                    }
                }
            }
            DB::commit();
            return 1;
        }
        catch(\Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    /**
     * xem quyền được phân
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ViewRole(Request $request) {

        // role group
//        if($request['type'] == 2){
//            $screen = RoleScreen::query()
//                ->distinct()
//                ->select("role_screens.name", "role_screens.alias")
//                ->selectRaw("GROUP_CONCAT( DISTINCT c.name SEPARATOR ', ' ) AS FullName, GROUP_CONCAT( DISTINCT c.id SEPARATOR ',' ) AS RoleGroupId")
//                ->join('role_screen_details AS r' ,'role_screens.alias','=', 'r.role_screen_alias')
//                ->leftJoin(DB::raw("(SELECT DISTINCT a.screen_detail_alias, role_user_groups.name, role_user_groups.id
//	                            FROM role_group_screen_detail_relationships a
//	                            LEFT JOIN role_user_groups ON role_user_groups.id = a.role_group_id)
//	                            AS c "),
//                    function($join)
//                    {
//                        $join->on('r.role_screen_alias', '=', 'c.screen_detail_alias');
//                    })
//                ->groupBy('role_screens.alias' )
//                ->orderBy('role_screens.alias')
//                ->orderBy('c.name')
//                ->get();
//
//            foreach ($screen as $key => $value){
//
//                // convert userId string to array
//                $arrUserScreen = explode(',', $value['RoleGroupId']);
//
//                //get FullName convert to array
//                $nameUserNotRole = RoleUserGroup::query()->select('name')
//                    ->where('id', '!=', 1)
//                    ->whereNotIn('id', $arrUserScreen)->get()->toArray();
//
//                //convert array to string
//                $convertArrayToString = implode(', ', array_column($nameUserNotRole, 'name'));
//
//                //add column to obj return
//                $screen[$key]['NameUserNotRole'] = $convertArrayToString;
//                $this->data['screen'] = $screen;
//            }
//
//        }else{
//
//            $screen = RoleScreen::query()
//                ->selectRaw("role_screens.id, role_screens.name,role_screens.alias, GROUP_CONCAT(role_screen_details.name) as role, GROUP_CONCAT(role_screen_details.alias) as nameRole")
//                ->join('role_screen_details', 'role_screens.alias','=','role_screen_details.role_screen_alias')
//                ->groupBy('role_screens.name')->get();
//
//            $listUser  = RoleUserScreenDetailRelationship::query()->select('user_id')
//                ->where('user_id','!=',220)->groupBy('user_id')->get();
//            $number = 0;
//            $this->data['screen'] = $screen;
//            $this->data['screen'][$number]['coQuyen'] = '';
//            $this->data['screen'][$number]['khongQuen'] = '';
//            $this->data['screen'][$number]['uid'] = '';
//            $this->data['screen'][$number]['idNotRole'] = '';
//            foreach ($listUser as $userId){
//                $user = User::find($userId->user_id);
//                $khongQuyen = RoleGroupScreenDetailRelationship::query()->select('screen_detail_alias')
//                    ->where('role_group_id', $user['role_group'])
//                    ->whereRaw('screen_detail_alias NOT IN ( SELECT screen_detail_alias FROM role_user_screen_detail_relationships WHERE user_id = '.$user['id'].')')
//                    ->get()->toArray();
//                $khongQuyen = array_column($khongQuyen, 'screen_detail_alias');
//
//                $coQuyen = RoleUserScreenDetailRelationship::query()->select('screen_detail_alias')
//                    ->where('user_id', $user['id'])
//                    ->whereRaw('screen_detail_alias NOT IN ( SELECT screen_detail_alias FROM role_group_screen_detail_relationships WHERE role_group_id = '.$user['role_group'].')')
//                    ->get()->toArray();
//
//                $coQuyen = array_column($coQuyen, 'screen_detail_alias');
//
//
//                foreach ($screen as $key => $scr){
//                    $arrayNameRole = explode(',',$scr->nameRole);
//                    if (count(array_intersect($arrayNameRole,$coQuyen)) > 0){
//                        $this->data['screen'][$key]['coQuyen'] .= $user['FullName'].', ';
//                        $this->data['screen'][$key]['uid'] .= ','.$user['id'];
//                    }
//                    if (count(array_intersect($khongQuyen,$arrayNameRole)) > 0){
//                        $this->data['screen'][$key]['khongQuen'] .= $user['FullName'].', ';
//                        $this->data['screen'][$key]['idNotRole'] .= ','.$user['id'];
//                    }
//                }
//
//            }
//        }

        $this->data['alias'] = RoleScreen::all();

        if (isset($request['alias']) && $request['alias'] != '') {
            $screen = RoleScreen::query()
                ->selectRaw("role_screens.id, role_screens.name,role_screens.alias, GROUP_CONCAT(role_screen_details.name) as role, GROUP_CONCAT(role_screen_details.alias) as nameRole")
                ->join('role_screen_details', 'role_screens.alias','=','role_screen_details.role_screen_alias')
                ->where('role_screens.alias', '=', $request['alias'])
                ->groupBy('role_screens.name')->get();
        } else {
            $screen = RoleScreen::query()
                ->selectRaw("role_screens.id, role_screens.name,role_screens.alias, GROUP_CONCAT(role_screen_details.name) as role, GROUP_CONCAT(role_screen_details.alias) as nameRole")
                ->join('role_screen_details', 'role_screens.alias','=','role_screen_details.role_screen_alias')
                ->groupBy('role_screens.name')->get();
        }

        if ($screen) {
            foreach ($screen as $key => $scr) {
                $scr['coQuyen'] = '';
                $scr['khongQuyen'] = '';

                $coQuyen = array();
                $khongQuyen = array();
                $idNhomCoQuyen = array();

                $listGroup = RoleGroupScreenDetailRelationship::where('screen_detail_alias', '=', $scr->alias)->get();
                if ($listGroup) {
                    foreach ($this->listGroupUser as $_listGroupUser) {
                        $listGroupID = array_column($listGroup->toArray(), 'role_group_id');
                        if (in_array($_listGroupUser->id, $listGroupID)) {
                            $coQuyen[] = $_listGroupUser->name;
                            $idNhomCoQuyen[] = $_listGroupUser->id;
                        } else {
                            $khongQuyen[] = $_listGroupUser->name;
                        }
                    }
                }

                $listUser = RoleUserScreenDetailRelationship::where('screen_detail_alias', '=', $scr->alias)->get();
                if ($listUser) {
                    foreach ($listUser as $_listUser) {
                        foreach ($this->listUserActive as $_listUserActive) {
                            if ($_listUser->user_id == $_listUserActive->id) {
                                if ($_listUser->permission == 1 && !in_array($_listUserActive->role_group, $idNhomCoQuyen)) {
                                    $coQuyen[] = $_listUserActive->FullName;
                                } elseif ($_listUser->permission == 0) {
                                    $khongQuyen[] = $_listUserActive->FullName;
                                }
                            }
                        }
                    }
                }
                $scr['coQuyen'] = implode(', ', $coQuyen);
                $scr['khongQuyen'] = implode(', ', $khongQuyen);
            }
        }

        $this->data['screen'] = $screen;
        $this->data['request'] = $request;
        return view('admin.layouts.'.config('settings.template').'.role.view-role', $this->data);
    }

    public function refreshRole(Request $request) {
//        if ($request['type'] == 2){
//            $arrayActionScreen = RoleScreenDetail::query()
//                ->selectRaw("GROUP_CONCAT(alias) as array, role_screen_alias")
//                ->where('role_screen_alias', $request['nameScreen'])
//                ->groupBy("role_screen_alias")
//                ->get()->toArray();
//
//            $convertToArray = explode(',', $arrayActionScreen[0]['array']);
//            $delete = DB::table('role_group_screen_detail_relationships')
//                ->whereIn('screen_detail_alias', $convertToArray)
//                ->delete();
//
//            if ($delete == 0){
//                return $this->jsonErrors('Gặp lỗi trong quá trình xóa!');
//            }
//            return 1;
//        }else{
//            $arrayActionScreen = RoleScreenDetail::query()
//                ->selectRaw("GROUP_CONCAT(alias) as array, role_screen_alias")
//                ->where('role_screen_alias', $request['nameScreen'])
//                ->groupBy("role_screen_alias")
//                ->get()->toArray();
//
//            $convertUidToArray = explode(',', $request['uId']);
//            $convertToArray = explode(',', $arrayActionScreen[0]['array']);
//            $delete = DB::table('role_user_screen_detail_relationships')
//                ->whereIn('user_id', $convertUidToArray)
//                ->whereIn('screen_detail_alias', $convertToArray)
//                ->delete();
//
//            if ($delete == 0){
//                return $this->jsonErrors('Gặp lỗi trong quá trình xóa!');
//            }
//            return 1;
//        }
        if (isset($request['alias']) && $request['alias'] != '') {
            $roleScreenDetails = RoleScreenDetail::where('role_screen_alias', '=', $request['alias'])->get();
            if ($roleScreenDetails) {
                foreach ($roleScreenDetails as $roleScreenDetail) {
                    $deleteGroup = RoleGroupScreenDetailRelationship::where('screen_detail_alias', '=', $roleScreenDetail->alias)->delete();
                    $deleteUser = RoleUserScreenDetailRelationship::where('screen_detail_alias', '=', $roleScreenDetail->alias)->delete();
                    if ($deleteUser == 0 && $deleteGroup == 0){
                        return $this->jsonErrors(__('admin.error.delete'));
                    }
                }
            }
        } else {
            return $this->jsonErrors(__('admin.error.delete'));
        }
        return 1;
    }

    public static function jsonErrors($errors) {
        return response()->json(['errors' => [$errors]]);
    }

    /**
     * Lấy thông tin cho popup theo user
     *
     * @param Request $request
     * @return false|string
     */
    public function getRoleUserScreen(Request $request){

        $toArrayUserId = explode(',', $request['uid']);
        $user = User::query()->select('id','FullName')
            ->whereIn('id',$toArrayUserId )->get();

        return json_encode( $user);
    }

    public function getRoleGroup(Request $request){
        $screen = RoleScreen::query()
            ->distinct()
            ->select("role_screens.name", "role_screens.alias")
            ->selectRaw("GROUP_CONCAT( DISTINCT c.id SEPARATOR ',' ) AS RoleGroupId")
            ->Join('role_screen_details AS r' ,'role_screens.alias','=', 'r.role_screen_alias')
            ->leftJoin(DB::raw("(SELECT DISTINCT a.screen_detail_alias, role_user_groups.name, role_user_groups.id
	                            FROM role_group_screen_detail_relationships a
	                            LEFT JOIN role_user_groups ON role_user_groups.id = a.role_group_id)
	                            AS c "),
                function($join)
                {
                    $join->on('r.role_screen_alias', '=', 'c.screen_detail_alias');
                })
            ->where('role_screens.alias', $request['alias'])
            ->groupBy('role_screens.alias')
            ->orderBy('role_screens.alias')
            ->orderBy('c.name')
            ->get();


        // convert userId string to array
        $arrUserScreen = explode(',', $screen[0]['RoleGroupId']);

        $nameGroup = RoleUserGroup::query()->select('name')
            ->whereIn('id', $arrUserScreen)->get()->toArray();

        $screen[0]['NameGroup'] = array_column($nameGroup, 'name');

        //get FullName convert to array
        $nameUserNotRole = RoleUserGroup::query()->select('name')
            ->where('id', '!=', 1)
            ->whereNotIn('id', $arrUserScreen)->get()->toArray();

        //add column to obj return
        $screen[0]['NameGroupNotRole'] = array_column($nameUserNotRole, 'name');

        return json_encode($screen);
    }

    /**
     * xóa quyền của nhân viên trong từng màn
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|int
     */
    public function deleteOne(Request $request){
        $arrayActionScreen = RoleScreenDetail::query()
            ->selectRaw("GROUP_CONCAT(alias) as array, role_screen_alias")
            ->where('role_screen_alias', $request['nameScreen'])
            ->groupBy("role_screen_alias")
            ->get()->toArray();

        $convertToArray = explode(',', $arrayActionScreen[0]['array']);
        if ($request->has('type') && $request['type'] == 2) {
            $delete = DB::table('role_group_screen_detail_relationships')
                ->whereIn('screen_detail_alias', $convertToArray)
                ->where('role_group_id', $request['id'])
                ->delete();
        }else{
            $delete = DB::table('role_user_screen_detail_relationships')
                ->whereIn('screen_detail_alias', $convertToArray)
                ->where('user_id', $request['id'])
                ->delete();
        }

        if ($delete == 0){
            return $this->jsonErrors('Gặp lỗi trong quá trình xóa!');
        }
        return 1;
    }

    //get list user return view
    public function listMenus(){
        $listNameMenu = Menu::query()->select()->whereNull('ParentId')->orderBy('Order')->get();
        $array = [];

        if (count($listNameMenu) > 0){
            foreach ($listNameMenu as $item){
                $ArrChillMenu = Menu::query()->select('id','LangKey','Order')->where('ParentId' , $item['id'])->get();
                $array[$item->LangKey]['parent'] = $item;
                $array[$item->LangKey]['chill'] = [];
                if (count($ArrChillMenu) > 0){
                    foreach ($ArrChillMenu as $chillMenu){
                        $array[$item->LangKey]['chill'][] = $chillMenu;
                    }
                }
            }
        }
        $this->data['array'] = $array;
        return view('admin.layouts.'.config('settings.template').'.list-menu', $this->data);
    }

    public function listLanguage(){
        $listNameMenu = Menu::query()->select()->whereNull('ParentId')->orderBy('Order')->get();
        $array = [];

        if (count($listNameMenu) > 0){
            foreach ($listNameMenu as $item){
                $ArrChillMenu = Menu::query()->select('id','LangKey','Order')->where('ParentId' , $item['id'])->get();
                $array[$item->LangKey]['parent'] = $item;
                $array[$item->LangKey]['chill'] = [];
                if (count($ArrChillMenu) > 0){
                    foreach ($ArrChillMenu as $chillMenu){
                        $array[$item->LangKey]['chill'][] = $chillMenu;
                    }
                }
            }
        }
        $this->data['array'] = $array;
        return view('admin.layouts.'.config('settings.template').'.list-language', $this->data);
    }

    //return modal edit menu
    public function editMenu($id = null, $del = null){

        if ($id != null){
            $record = Menu::find($id);
            if ($del ==' del'){
                $record->delete();
            }
            if (isset($record)){
                $this->data['record'] = $record;
                $this->data['menuParent'] = Menu::query()->select('id','LangKey')->whereNull('RouteName')->get();
                return view('admin.includes.' . 'edit-menu-detail', $this->data);
            }
        }
        return view('admin.includes.' . 'edit-menu-detail', $this->data);
    }

    //mục đích là để lưu icon cho menu
    public function saveMenu(Request $request){
        if (count($request->input()) == 0 ){
            return abort('404');
        }
        try{
            $arrCheck = [
                'id'                         =>  'required',
                'FontAwesome'                =>  'nullable|string',
                'Order'                      =>  'nullable|string',
                'ParentId'                   =>  'nullable',
            ];
//            $mes = [
//                'Order.required' => 'Vị trí không được để trống'
//            ];

            $validator = Validator::make($request->all(), $arrCheck);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()->first()]);
            }
            $validated = $validator->validate();
            $one = Menu::find($validated['id']);

            foreach ($validated as $key => $value){
                if (Schema::hasColumn('menus',$key)){
                    $one->$key = $value;
                }
            }
            $one->save();
            return $one;
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function saveNewLang(Request $request){
        if($request->hasFile('files') || $request->hasFile('image')) {
            $allowedfileExtension = ['json'];
            $imgExtension = ['jpg','png','jpeg','JPG','PNG','JPEG'];

            // tạo thư mục ngôn ngữ mới
            $files = File::directories($this->resources_path('lang/'));
            $nameFolder = [];
            foreach ($files as $file){
                $nameFolder[] = basename($file);
            }
            if (in_array($request['nameShort'], $nameFolder)){
                return $this->jsonErrors(['Viết tắt ngôn ngữ đã tồn tại.']);
            }else{
                $path = $this->resources_path('lang/').$request['nameShort'];
                File::makeDirectory($path);
            }

            //kiểm tra và lưu ảnh
            $checkImg = in_array($request['image']->getClientOriginalExtension(), $imgExtension);
            if(!$checkImg) {
                return $this->jsonErrors(['File ảnh tải lên không hợp lệ!']);
            }else{
                $image_resize = Image::make($request['image']->getRealPath());
                $image_resize->resize(64, 64);
                $image_resize->save(public_path('imgs/' . $request['nameShort'].'.'.$request['image']->getClientOriginalExtension()));
            }

            // Lưu file vào thư mục
            foreach ($request->file('files') as $file){
                $extension = $file->getClientOriginalExtension();
                $check = in_array($extension, $allowedfileExtension);
                if(!$check) {
                    // nếu có file nào không đúng đuôi mở rộng thì đổi flag thành false
                    return $this->jsonErrors(['File tải lên không hợp lệ!']);
                }else{
                    $file->move($this->resources_path('lang/').$request['nameShort'], $file->getClientOriginalName());
                }
            }
        }
    }

    public function resources_path($path = '')
    {
        return app()->make('path.resources').($path ? DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR) : $path);
    }

    public function exportFileJson(){
        $files = File::files($this->resources_path('lang\vi'));

        $public_dir = public_path().'/storage/';
        $zipFileName = 'file.zip';
        $zip = new ZipArchive;

        if ($zip->open($public_dir . '/' . $zipFileName, ZipArchive::CREATE) === TRUE) {
            foreach ($files as $file){
                $data = require $file;
                header('Content-Type: application/json;charset=utf-8');
                $dataJson = json_encode($data,JSON_UNESCAPED_UNICODE);
                $fileJson = str_replace("php","json", basename($file));
                $destinationPath = public_path()."/storage/";

                if (!is_dir($destinationPath))
                { mkdir($destinationPath,0777,true);}
                File::put($destinationPath.$fileJson,$dataJson);

                $zip->addFile($destinationPath.$fileJson,$fileJson);
            }

            $zip->close();
        }
        $headers = array(
            'Content-Type' => 'application/octet-stream',
        );
        $filetopath = $public_dir.'/'.$zipFileName;
        if(file_exists($filetopath)){
            return response()->download($filetopath,$zipFileName,$headers);
        }
        return ['status'=>'file does not exist'];
    }
}
