<?php

namespace App\Http\Controllers\Admin;

use App\Menu;
use App\Room;
use App\User;
use App\RoleScreenDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class RoomController
 * @package App\Http\Controllers\Admin
 * Controller screen Room
 */
class RoomController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    const KEYMENU= array(
        "add" => "RoomListAdd",
        "view" => "RoomList",
        "edit" => "RoomListEdit",
        "delete" => "RoomListDelete",
    );
    /**
     * RoomController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
        $array = $this->RoleView('Rooms',['RoomList']);
        $this->menu = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if($value == $row->alias)
                    $this->$key = $row;
            } 
        }
    }

    /**
     * @param Request $request
     * @return View (rooms)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * Get data Room and return view
     */
    public function show(Request $request, $orderBy = 'id', $sortBy = 'desc'){
        $this->authorize('view', $this->menu);
        $recordPerPage = $this->getRecordPage();

        //list users
        if(Schema::hasColumn('rooms',$orderBy)){
            $rooms = Room::orderBy($orderBy, $sortBy);
        }
        else
        {
            return redirect()->back();
        }
        //tim kiem theo column
        $this->data['request'] = $request->query();
        $one = Room::query()->select('Name')->first();
        if($one){
            $one = $one->toArray();
            if(array_key_exists('search', $request->input())){
                $rooms = $rooms->where(function ($query) use ($one, $request){
                    foreach($one as $key=>$value){
                        $query->orWhere($key, 'like', '%'.$request->input('search').'%');
                    }
                });

            }
        }


        $user = User::find(Auth::user()->id);
        if ($user->cant('action',$this->edit)) {
            $rooms = $rooms->where('Active',1);
        }
        //phan trang

        //Tien 30/3/2020
        $count = $rooms->count();
        //Tien 30/3/2020

        $rooms = $rooms->paginate($recordPerPage);
        $this->data['rooms'] = $rooms;
        $query_string = str_replace($request->url(), '', $request->fullUrl());

        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
        parse_str(str_replace('?', '', $query_string), $query_array);
        if($rooms->count() == 0){
            if(array_key_exists('page', $query_array)){
                if($query_array['page'] > 1){
                    $query_array['page'] = $rooms->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl  = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }

            }
        }
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        $this->data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['sort'] = $sort;
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;

        if (strpos(\Request::getRequestUri(), 'api') !== false) {
            return $this->data;
        }
        return $this->viewAdminLayout('rooms', $this->data);
    }

    /**
     * Process insert multiple records,update one records
     * @param Request $request
     * @param null $id
     * @return string|void
     */
    public function store(Request $request, $id=null){
        $this->menu = Menu::query()
            ->where('RouteName', 'admin.Rooms')
            ->first();
        try{
            if(count($request->input()) >0){
//                return $request->input();

                if(array_key_exists('id', $request->input())){
                    $room = Room::find($request->input('id'));
                    $validator = Validator::make($request->all(),
                        [
                            'Name'  =>  'required|string|max:100|unique:rooms,Name,' . $room->id,
                            'id'    =>  'integer|min:1|nullable',
                            'Active' =>  'string|nullable',
                            'MeetingRoomFlag'    =>  'string|nullable',
                        ]);
                }else{
                    $validator = Validator::make($request->all(),
                        [
                            'Name'  =>  'required|string|max:100',
                            'Active' =>  'string|nullable',
                            'MeetingRoomFlag'    =>  'string|nullable',
                        ]);
                }

                if ($validator->fails())
                {
                    return response()->json(['errors'=>$validator->errors()->all()]);
                }

                $validated = $validator->validate();

                if(isset($validated['Name'])){
                    $check = preg_match('/[(&|@|!|#|^|$|%|*|+|=)]/', $validated['Name']);
                    if($check == 1){
                        return $this->jsonErrors('Tên phòng không được chưa kí tự đặc biệt!');
                    }
                }
                if(array_key_exists('id', $validated)){
                    $this->authorize('action', $this->edit);
                    $one = Room::find($validated['id']);
                }else{
                    $room1 = Room::query()->select('Name')->where('Name',$validated['Name'])->get();
                    if(null != $room1){
                        foreach ($room1 as $key => $value) {
                            if($value->Name == $validated['Name']){
                                return $this->jsonErrors('Tên phòng ban đã  được sử dụng');
                            }
                        }
                    }
                    $this->authorize('action', $this->add);
                    $one = new Room();
                }
                foreach($validated as $key => $value){
                    if(Schema::hasColumn('rooms', $key))
                        $one->$key = $value;
                }
                if(isset($validated['Active'])){
                    $one->Active = 1;
                }else{
                    $one->Active = 0;
                }

                if(isset($validated['MeetingRoomFlag'])){
                    $one->MeetingRoomFlag = 1;
                }else{
                    $one->MeetingRoomFlag = 0;
                }
                $save = $one->save();
                if(!$save){
                    return $this->jsonErrors('Lưu không thành công');
                }else{
                    if (strpos(\Request::getRequestUri(), 'api') !== false) {
                        return response()->json(['success' => 'Lưu thành công.']);
                    }
                    return $this->jsonSuccessWithRouter('admin.Rooms');
                }

            }else{
                return abort('404');
            }
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Show popup insert,update can insert multiple records
     * @param Request $request
     * @param null $roomId
     * @param null $del
     * @return View (room-detail)
     */
    public function showRoom(Request $request, $roomId=null, $del=null){
        if($del == 'up'){
            $tittle  = substr($roomId,-1);
            $roomId = substr($roomId,0,-1);
            if($tittle == 'A'){
                $actives = Room::query()->select('Active')->find($roomId);
                if(!isset($actives)){
                    return 2;
                }
                $active = $actives->Active;
            }else{
                $actives = Room::query()->select('MeetingRoomFlag')->find($roomId);
                if(!isset($actives)){
                    return 2;
                }
                $active = $actives->MeetingRoomFlag;
            }
        }
        if($roomId!=null){
            if($del == 'del'){
                $one = Room::find($roomId);
                if($one){
                    $one->delete();
                    if (strpos(\Request::getRequestUri(), 'api') !== false) {
                        return response()->json(['success' => 'Xóa thành công.']);
                    }
                }
                return 1;
            }
            if($del == 'up'){
                $one = Room::find($roomId);
                if($active == 0){
                    $active = 1;
                }else{
                    $active = 0;
                }
                if($one){
                    if($tittle == 'A'){
                        $one->Active = $active;
                    }else{
                        $one->MeetingRoomFlag = $active;
                    }
                    $one->save();

                }
                return 1;
            }
            $this->data['itemInfo'] = Room::find($roomId);

            if($this->data['itemInfo']){
                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                    return $this->data;
                }
                return  $this->viewAdminIncludes('room-detail', $this->data);
            }else{
                return "";
            }
        }else{
            if (strpos(\Request::getRequestUri(), 'api') !== false) {
                return $this->data;
            }
            return $this->viewAdminIncludes('room-detail', $this->data);
        }

    }

    //API

    /**
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showApi(Request $request, $orderBy = 'id', $sortBy = 'desc'){
        $this->authorize('action', $this->view);

        $data = array();
        $recordPerPage = $this->getRecordPage();

        $rooms = Room::query();
        if(Schema::hasColumn('rooms',$orderBy)){
            $rooms = $rooms->orderBy($orderBy, $sortBy);
        }

        $data['request'] = $request->query();

        $one = Room::query()->select('Name')->first();
        if($one){
            $one = $one->toArray();
            if(array_key_exists('search', $request->input())){
                $rooms = $rooms->where(function ($query) use ($one, $request){
                    foreach($one as $key=>$value){
                        $query->orWhere($key, 'like', '%'.$request->input('search').'%');
                    }
                });

            }
        }

        if (Auth::user()->can('action', $this->edit)) {
            $rooms = $rooms->where('Active',1);
        }

        $count = $rooms->count();
        $rooms = $rooms->paginate($recordPerPage);

        $data['rooms'] = $rooms;

        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
        parse_str(str_replace('?', '', $query_string), $query_array);
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        $data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $data['query_array'] = $query_array;
        $data['sort_link'] = $sort_link;
        $data['sort'] = $sort;
        $data['role_key'] = 'RoomList';

        return AdminController::responseApi(200, null, null, $data);
    }

    /**
     * @param Request $request
     * @param null $roomId
     * @param null $del
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Support\Facades\View|int|string
     */
    public function showRoomApi(Request $request, $id = null){
        if($id!=null){
            $data['itemInfo'] = Room::find($id);
            return AdminController::responseApi(200, null, null, $data);
        } else {
            return AdminController::responseApi(422, __('admin.error.data'));
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function storeApi(Request $request){
        $this->authorize('action', $this->add);

        $menu = Menu::query()
            ->where('RouteName', 'admin.Rooms')
            ->first();
        try{
            if(count($request->input()) >0){
                $validator = Validator::make($request->all(),
                    [
                        'Name'  =>  'required|string|max:100',
                        'Active' =>  'string|nullable',
                        'MeetingRoomFlag'    =>  'string|nullable',
                    ]);

                if ($validator->fails()) {
                    return AdminController::responseApi(422, $validator->errors()->first());
                }

                $validated = $validator->validate();

                if (isset($validated['Name'])) {
                    $check = preg_match('/[(&|@|!|#|^|$|%|*|+|=)]/', $validated['Name']);
                    if($check == 1){
                        return AdminController::responseApi(422, __('admin.error.room.name'));
                    }
                }

                $room1 = Room::query()->select('Name')->where('Name', $validated['Name'])->get();
                if (null != $room1) {
                    foreach ($room1 as $key => $value) {
                        if($value->Name == $validated['Name']){
                            return AdminController::responseApi(422, __('admin.error.room.unique'));
                        }
                    }
                }

                $one = new Room();

                foreach ($validated as $key => $value) {
                    if(Schema::hasColumn('rooms', $key))
                        $one->$key = $value;
                }
                if (isset($validated['Active'])) {
                    $one->Active = 1;
                } else {
                    $one->Active = 0;
                }

                if (isset($validated['MeetingRoomFlag'])) {
                    $one->MeetingRoomFlag = 1;
                } else {
                    $one->MeetingRoomFlag = 0;
                }
                $save = $one->save();
                if (!$save) {
                    return AdminController::responseApi(403, __('admin.error.save'));
                } else {
                    return AdminController::responseApi(200, null, __('admin.success.save'));
                }

            } else {
                return AdminController::responseApi(422, __('admin.error.data'));
            }
        } catch (\Exception $e){
            return AdminController::responseApi(422, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateApi(Request $request, $id=null){
        $this->authorize('action', $this->edit);

        $menu = Menu::query()
            ->where('RouteName', 'admin.Rooms')
            ->first();
        try{
            if(count($request->input()) >0){

                $one = Room::find($id);
                $validator = Validator::make($request->all(),
                    [
                        'Name'  =>  'required|string|max:100|unique:rooms,Name,' . $one->id,
                        'id'    =>  'integer|min:1|nullable',
                        'Active' =>  'string|nullable',
                        'MeetingRoomFlag'    =>  'string|nullable',
                    ]);

                if ($validator->fails()) {
                    return AdminController::responseApi(422, $validator->errors()->first());
                }

                $validated = $validator->validate();

                if(isset($validated['Name'])){
                    $check = preg_match('/[(&|@|!|#|^|$|%|*|+|=)]/', $validated['Name']);
                    if($check == 1){
                        return AdminController::responseApi(422, __('admin.error.room.name'));
                    }
                }


                foreach($validated as $key => $value) {
                    if(Schema::hasColumn('rooms', $key))
                        $one->$key = $value;
                }

                if (isset($validated['Active'])) {
                    $one->Active = 1;
                } else {
                    $one->Active = 0;
                }

                if (isset($validated['MeetingRoomFlag'])) {
                    $one->MeetingRoomFlag = 1;
                } else {
                    $one->MeetingRoomFlag = 0;
                }

                $save = $one->save();
                if(!$save) {
                    return AdminController::responseApi(403, __('admin.error.save'));
                } else {
                    return AdminController::responseApi(200, null, __('admin.success.save'));
                }

            }else{
                return AdminController::responseApi(422, __('admin.error.data'));
            }
        } catch (\Exception $e) {
            return AdminController::responseApi(422, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteApi(Request $request, $id = null) {
        if($id != null){
            $one = Room::find($id);
            if($one){
                $one->delete();
                return AdminController::responseApi(200, null, __('admin.success.delete'));
            }
        }
        return AdminController::responseApi(422, __('admin.error.data'));
    }
}
