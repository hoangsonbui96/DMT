<?php

namespace App\Http\Controllers\Admin;

use App\Equipment;
use App\EquipmentRegistration;
use App\EquipmentType;
use App\EquipmentUsingHistory;
use App\MasterData;
use App\Room;
use App\User;
use App\RoleScreenDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Menu;
use App\Exports\EqExport;
use App\Exports\EqExportQR;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

/**
 * Class EquipmentController
 * @package App\Http\Controllers\Admin
 * Controller screen Equipment
 */

class EquipmentController  extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $export;
    protected $exportQR;
    const KEYMENU= array(
        "add" => "EquipmentListAdd",
        "view" => "EquipmentList",
        "edit" => "EquipmentListEdit",
        "delete" => "EquipmentListDelete",
        "export" => "EquipmentListExport",
        "exportQR" => "EquipmentListExportQR",
    );
    /**
     * EquipmentController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        // parent::__construct($request);
        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
        $array = $this->RoleView('Equipment',['EquipmentList']);
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
     * @return View (equipment)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * Get data Equipment and return view
     */
    public function show(Request $request, $orderBy = 'id', $sortBy = 'desc'){
        $this->authorize('view', $this->menu);
        $recordPerPage = $this->getRecordPage();

        //list
        if(Schema::hasColumn('equipment',$orderBy)){
            $list = Equipment::orderBy('equipment.'.$orderBy, $sortBy);
        }
        else
        {
            return redirect()->back();
        }
        //loai thiet bi

        $this->data['eqTypes'] = EquipmentType::query()
            ->select('type_id', 'type_name')
            ->get();
        $this->data['eqStatus'] = MasterData::query()
            ->where('DataKey', 'TB')
            ->get();

        $this->data['rooms'] = DB::table('rooms')
            ->select('rooms.id', 'rooms.Name')
            ->join('equipment', 'equipment.room_id', 'rooms.id')
            ->groupBy('rooms.id')
            ->get();

        $this->data['owners'] = DB::table('users')
            ->select('users.id', 'users.FullName')
            ->join('equipment', 'equipment.user_owner', 'users.id')
            ->groupBy('users.id')
            ->get();


        //tim kiem theo column
        $this->data['request'] = $request->query();
        $one = Equipment::query()->select('equipment.id','equipment.code','equipment.name','equipment.period_date','equipment.serial_number','equipment.provider','equipment.info','equipment_types.type_name','rooms.Name','users.FullName')
            ->leftJoin('rooms','rooms.id','=','equipment.room_id')
            ->leftJoin('users','users.id','=','equipment.user_owner')
            ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
            ->first();
        if($one){
            $one = $one->toArray();
            if(array_key_exists('search', $request->input())){
                if(null !== $request->input('search')){
                $list = $list->select('equipment.*')
                            ->leftJoin('rooms','rooms.id','=','equipment.room_id')
                            ->leftJoin('users','users.id','=','equipment.user_owner')
                            // ->join('master_data','master_data.id','=','equipment.status_id')
                            ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
                    ->where(function ($query) use ($one, $request){
                    foreach($one as $key=>$value){
                        if($key == 'Name') {
                            $query->orWhere('rooms.'.$key, 'like', '%'.$request->input('search').'%');
                        }
                        else if($key == 'FullName') {
                            $query->orWhere('users.'.$key, 'like', '%'.$request->input('search').'%');
                        }
                        else if($key == 'type_name') {
                            $query->orWhere('equipment_types.'.$key, 'like', '%'.$request->input('search').'%');
                        }else{
                            if(in_array($key, ['provider', 'deal_date'])){
                                $query->orWhereRaw('(DATE_FORMAT(equipment.'.$key.',"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                            }else{
                                if(in_array($key, ['code', 'name','serial_number','info'])){
                                $query->orWhere('equipment.'.$key, 'like', '%'.$request->input('search').'%');
                                }
                            }
                        }
                        $query->orWhereRaw('(DATE_FORMAT(equipment.provider,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                        $query->orWhereRaw('(DATE_FORMAT(equipment.deal_date,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                    }
                    
                });
            }
            }
        }

        //tim kiếm
        foreach($this->data['request'] as $key => $value){ 
            if(!is_array($value)){
                if(Schema::hasColumn('equipment', $key) && $value !== null){
                   $list = $list->where('equipment.'.$key, '=', $value);
                }
            }else{
                if($value[0] !== null && $value[1] !== null && \DateTime::createFromFormat('d/m/Y', $value[0]) !== FALSE && \DateTime::createFromFormat('d/m/Y', $value[1]) !== FALSE){
                    $list = $list->where('equipment.deal_date', '>=', $this->fncDateTimeConvertFomat($value[0], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))
                    ->where('equipment.deal_date', '<=', $this->fncDateTimeConvertFomat($value[1], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                }
                else if($value[0] !== null && $value[1] == null && \DateTime::createFromFormat('d/m/Y', $value[0]) !== FALSE){
                    $list = $list->where('equipment.deal_date', '>=', $this->fncDateTimeConvertFomat($value[0], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                }
                else if($value[0] == null && $value[1] !== null && \DateTime::createFromFormat('d/m/Y', $value[1]) !== FALSE){
                    $list = $list->where('equipment.deal_date', '<=', $this->fncDateTimeConvertFomat($value[1], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                }
                else if(($value[0] !== null || $value[1] !== null) && (\DateTime::createFromFormat('d/m/Y', $value[0]) === FALSE || \DateTime::createFromFormat('d/m/Y', $value[1]) === FALSE)){
                // $list = $list->where('equipment.deal_date', '=', $this->fncDateTimeConvertFomat('30/02/2020', self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                    return Redirect::back()->withErrors(['Ngày tìm kiếm không hợp lệ']);
                }
            }
            if($key == 'warranty'){
                if($value == 1){
                    $list = $list->where('period_date', '>=', Carbon::now()->startOfDay());
                }
                if($value == 2){
                    $list = $list->where('period_date', '<', Carbon::now()->startOfDay());
                }
            }

        }
        $user = User::find(Auth::user()->id);
        if ($user->cant('admin', $this->menu)) {
            $list = $list->where('user_owner', Auth::user()->id);
        }
        //phan trang
        $list = $list->paginate($recordPerPage);
        foreach($list as $item){
            $status = MasterData::find($item->status_id);
            if($status) $item->status = $status->Name;
            if($item->user_owner){
                // $item->ownerName = User::find($item->user_owner)->FullName; 

                //Tien 1/4/2020
                if(!empty($item->user_owner)){
                    $ownerName = User::query()->withTrashed()
                    ->where('id', $item->user_owner)          
                    ->first();
                    if(!empty($ownerName)){
                        $item->ownerName = $ownerName->FullName;
                    }
                }
                //Tien 1/4/2020
            }
            if($item->room_id){
                $item->room = Room::find($item->room_id)->Name;
            }
        }
        $this->data['list'] = $list;

        $query_string = str_replace($request->url(), '', $request->fullUrl());

        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
        parse_str(str_replace('?', '', $query_string), $query_array);
        //redirect to the last page if current page has no record
        if($list->count() == 0){
            if(array_key_exists('page', $query_array)){
                if($query_array['page'] > 1){
                    $query_array['page'] = $list->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl  = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }

            }
        }
        $this->data['query_array'] = $query_array;

        $this->data['sort_link'] = $sort_link;
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;
        $this->data['export'] = $this->export;
        $this->data['exportQR'] = $this->exportQR;
        $this->data['recordPerPage'] = $recordPerPage;
        return $this->viewAdminLayout('equipment', $this->data);
    }

    /**
     * Process insert multiple records,update one records
     * @param Request $request
     * @param null $id
     * @return string|void
     */
    public function store(Request $request, $id=null){

        if(count($request->input()) === 0){
            return abort('404');
        }

        try{

//                return $request->input();
            if(array_key_exists('id', $request->input())){
                $validator = Validator::make($request->all(),
                    [

                        'id'    =>  'integer|min:1|nullable',
                        'type_id'   =>  'string|required',
                        'name' =>  'string|required',
                        'serial_number' =>  'string|nullable',
                        'info' =>  'string|nullable',
                        'provider' =>  'string|nullable',
                        'buy_date' =>  'date_format:d/m/Y|nullable',
                        'period_date' =>  'numeric|min:0|nullable',
                        'deal_date' =>  'date_format:d/m/Y|nullable',
                        'unit_price' =>  'numeric|nullable',
                        'note' =>  'string|nullable',
                        'updated_user' =>  'integer|min:1|nullable',
                        'status_id' =>  'integer|min:1|required',
                        'user_owner' =>  'integer|min:0|nullable',
                        'register_id' =>  'integer|min:1|nullable',
                        'room_id' =>  'integer|min:1|nullable',

                    ]);
            }else{
                $validator = Validator::make($request->all(),
                    [
                        'type_id'   =>  'string|required',
                        'name' =>  'string|required',
                        'serial_number' =>  'string|nullable',
                        'info' =>  'string|nullable',
                        'provider' =>  'string|nullable',
                        'buy_date' =>  'date_format:d/m/Y|nullable',
                        'period_date' =>  'numeric|min:0|nullable',
                        'deal_date' =>  'date_format:d/m/Y|nullable',
                        'unit_price' =>  'numeric|nullable',
                        'note' =>  'string|nullable',
                        'updated_user' =>  'integer|min:1|nullable',
                        'status_id' =>  'integer|min:1|nullable',
                        'user_owner' =>  'integer|min:0|nullable',
                        'register_id' =>  'integer|min:1|nullable',
                        'room_id' =>  'integer|min:1|nullable',
                    ]);
            }

            if ($validator->fails())
            {
                return response()->json(['errors'=>$validator->errors()->all()]);
            }
            $validated = $validator->validate();
            if(isset($validated['buy_date'])){
                $eqbuy_date = strtotime($this->fncDateTimeConvertFomat($validated['buy_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                $eqdeal_date = strtotime($this->fncDateTimeConvertFomat($validated['deal_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                if( $eqbuy_date > $eqdeal_date ){
                    return $this->jsonErrors('Ngày mua không thể lớn hơn ngày bàn giao!');
                } 
            }
            if(isset($validated['period_date'])){
                if(!isset($validated['buy_date'])){
                     return $this->jsonErrors('Bạn chưa nhập ngày mua !');
                }
            }

            if(isset($validated['serial_number'])){
                $check = preg_match('/^[A-Z0-9]+$/',$validated['serial_number']);
                if(!$check){
                    return $this->jsonErrors('Serial_number chỉ được nhập kí tự in hoa và số!');
                }
            }
            if(isset($validated['name'])){
                $check1 = preg_match('/[(&|@|!|#|^|$|%|*|+|=)]/', $validated['name']);
                $check3 = is_numeric($validated['name']);
                if($check1 == 1 || $check3){
                    return $this->jsonErrors('Vui lòng nhập lại tên thiết bị!');
                }
            }
            if(isset($validated['provider'])){
                $check2 = preg_match('/[(&|@|!|#|^|$|%|*|+|=)]/', $validated['provider']);
                if($check2 == 1){
                    return $this->jsonErrors('Nhà cung cấp không thể có ký tự đặc biệt!');
                }
            }
            if(array_key_exists('id', $validated)){
                $eqcheck= Equipment::query()
                    ->select('id','serial_number')
                    ->where('id','!=',$validated['id'])
                    ->get();
            }else{
                $eqcheck = Equipment::query()
                    ->select('id','serial_number')
                    ->get();
            }
            
            foreach($eqcheck as $row){
                if($row['serial_number'] == $validated['serial_number'] && isset($validated['serial_number']))
                {
                    return $this->jsonErrors('Serial Number đã tồn tại !');
                }
                
            }
            if(array_key_exists('id', $validated)){
                // $this->authorize('update', $this->menu);
                $one = Equipment::find($validated['id']);
                if($one->status_id != $validated['status_id']){
                    $newHistory = new EquipmentUsingHistory();
                    $newHistory->code = $one->code;
                    $newHistory->created_user = Auth::user()->id;
                    $newHistory->status_id = $validated['status_id'];
                    $newHistory->old_status_id = $one->status_id;
                    $newHistory->deal_date = Carbon::now();
                    if($validated['user_owner'] == null){
                        $one->user_owner = 0;
                    }
                    $newHistory->user_owner = $one->user_owner;
                    $newHistory->old_user_owner = $one->user_owner;
                    $newHistory->note = "Cập nhật trạng thái thiết bị";
                    $newHistory->save();
                }
                
            }else{
                // $this->authorize('create', $this->menu);
                $one = new Equipment();
                $equipmentSame = Equipment::query()->withTrashed()
                    ->where('type_id', $validated['type_id'])
                    ->orderBy('id', 'desc')
                    ->first();

                if($equipmentSame){
                    $one->code = $this->getEquipmentCode($equipmentSame->code);
                }else{
                    $one->code = $validated['type_id']."0001";
                }
                if($one->code != ''){
                    $newHistory = new EquipmentUsingHistory();
                    $newHistory->code = $one->code;
                    $newHistory->created_user = Auth::user()->id;
                    $newHistory->status_id = $validated['status_id'];
                    $newHistory->old_status_id = $validated['status_id'];
                    if($validated['user_owner'] == null){
                        $one->user_owner = 0;
                    }
                    $newHistory->user_owner = $one->user_owner;
                    $newHistory->old_user_owner = $one->user_owner;
                    if(isset($validated['deal_date'])){
                        $newHistory->deal_date = $this->fncDateTimeConvertFomat($validated['deal_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                    }
                    $newHistory->save();
                }
                
            }
            

            foreach($validated as $key => $value){
                if(Schema::hasColumn('equipment', $key))
                    $one->$key = $value;
            }
            if($validated['user_owner'] == null || !array_key_exists('id', $validated)){
                $one->user_owner = 0;
            }
            if(isset($validated['period_date'])){
               $one->period_date = Carbon::parse($this->fncDateTimeConvertFomat($validated['buy_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))->addMonth($validated['period_date']);
            }
            if(isset($validated['buy_date'])){
                $one->buy_date = $this->fncDateTimeConvertFomat($validated['buy_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
            }

            if(isset($validated['deal_date'])){
                $one->deal_date = $this->fncDateTimeConvertFomat($validated['deal_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
            }

            $one->updated_user = Auth::user()->id;
            $one->save();

            return $this->jsonSuccessWithRouter('admin.Equipment');
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Show popup insert,update can insert multiple records
     * @param Request $request
     * @param null $oneId
     * @param null $del
     * @return View (equipment-detail)
     */
    public function showDetail(Request $request, $oneId=null, $del=null){
        if($request->exists('copy')){
            $this->data['copy'] = 1;
        }
        $this->data['types'] = EquipmentType::all();
        $this->data['status_list'] = MasterData::query()
            ->where('datakey', 'TB')
            ->get();
        $this->data['register_rooms'] = DB::table('rooms')
            ->join('users', 'users.RoomId', 'rooms.id')
            ->select('rooms.*')
            ->get();

        //vi tri dat thiet bi
        $this->data['put_rooms'] = Room::query()
            ->where('Active', 1)
            ->get();

        // Tien 23/3/2020
            $this->data['Equipment'] = Equipment::query()
            ->select('id','serial_number')
            ->get();
        // Tien 23/3/2020
        if($oneId!=null){
            if($del == 'del'){
                $one = Equipment::find($oneId);
                if($one){
                    $one->delete();
                }
                return 1;
            }

            $this->data['itemInfo'] = Equipment::find($oneId);
            $this->data['registered_room'] = DB::table('equipment')
                ->join('users', 'users.id', 'equipment.user_owner')
                ->join('rooms', 'users.RoomId', 'rooms.id')
                ->select('rooms.*')
                ->where('equipment.user_owner', $this->data['itemInfo']->user_owner)
                ->first();
            if($this->data['registered_room']){

                $this->data['list_users'] = User::query()
                    ->where('RoomId', $this->data['registered_room']->id)
                    ->get();
            }else{
                $this->data['list_users'] = new \stdClass();

            }

            print_r($this->data['itemInfo']);
            if($this->data['itemInfo']){
                return $this->viewAdminIncludes('equipment-detail', $this->data);
            }else{
                return "";
            }
        }else{
            return $this->viewAdminIncludes('equipment-detail', $this->data);
        }

    }

    /**
     * show view History Equipment
     * @param $oneId
     * @return View (equipment-status-histories)
     */
    public function showStatusHistory($oneId){
        $eq = Equipment::find($oneId);
        if($eq){
            $this->data['eq'] = $eq;
            $this->data['histories'] = EquipmentUsingHistory::query()
                ->where('code', $eq->code)
                // ->whereColumn('status_id', '<>', 'old_status_id')
                ->get();
            foreach($this->data['histories'] as $item){
                if($item->user_owner > 0){
                    $array= User::find($item->user_owner);
                    if($array!= null){
                        $item->user_owner = $array->FullName;
                    }else{
                        $item->user_owner = 'Không biết người sử dụng';
                    }
                }else{
                    $item->user_owner = __("admin.equipment.store");
                }
                if($item->old_user_owner > 0){
                    $item->old_user_owner = User::find($item->old_user_owner)->FullName;
                }else{
                    $item->old_user_owner = __("admin.equipment.store");
                }
                if(!empty($item->status_id)){
                    $item->status_id = MasterData::find($item->status_id)->Name;
                }
                if(!empty($item->old_status_id)){
                    $item->old_status_id = MasterData::find($item->old_status_id)->Name;
                }
                $item->created_user = User::find($item->created_user)->FullName;
            }
            return $this->viewAdminIncludes('equipment-status-histories', $this->data);
        }

    }

     /**
     * Export excel
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request){
        $type_id= $request->input('type_id');
        $status_id= $request->input('status_id');
        $room_id= $request->input('room_id');
        $warranty= $request->input('warranty');
        $user_owner= $request->input('user_owner');
        $sDate= $request->input('sDate');
        $eDate= $request->input('eDate');
        $search= $request->input('search');

        $list = Equipment::query()->orderBy('id', 'desc');

        if(isset($search)){
            $one = Equipment::query()->select('equipment.id','equipment.code','equipment.name','equipment.period_date','equipment.serial_number','equipment.provider','equipment.info','equipment_types.type_name','rooms.Name','users.FullName')
                ->leftJoin('rooms','rooms.id','=','equipment.room_id')
                ->leftJoin('users','users.id','=','equipment.user_owner')
                ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
                ->first();
            if($one){
                $one = $one->toArray();
                if(array_key_exists('search', $request->input())){
                    if(null !== $request->input('search')){
                    $list = $list->select('equipment.*')
                                ->leftJoin('rooms','rooms.id','=','equipment.room_id')
                                ->leftJoin('users','users.id','=','equipment.user_owner')
                                // ->join('master_data','master_data.id','=','equipment.status_id')
                                ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
                        ->where(function ($query) use ($one, $request){
                        foreach($one as $key=>$value){
                            if($key == 'Name') {
                                $query->orWhere('rooms.'.$key, 'like', '%'.$request->input('search').'%');
                            }
                            else if($key == 'FullName') {
                                $query->orWhere('users.'.$key, 'like', '%'.$request->input('search').'%');
                            }
                            else if($key == 'type_name') {
                                $query->orWhere('equipment_types.'.$key, 'like', '%'.$request->input('search').'%');
                            }else{
                                if(in_array($key, ['provider', 'deal_date'])){
                                    $query->orWhereRaw('(DATE_FORMAT(equipment.'.$key.',"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                                }else{
                                    if(in_array($key, ['code', 'name','serial_number','info'])){
                                    $query->orWhere('equipment.'.$key, 'like', '%'.$request->input('search').'%');
                                    }
                                }
                            }
                            $query->orWhereRaw('(DATE_FORMAT(equipment.provider,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                            $query->orWhereRaw('(DATE_FORMAT(equipment.deal_date,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                        }
                        
                    });
                }
                }
            }
        }

        $list = $list->get();
        if(isset($type_id)&& $type_id!=''){
            $list = $list->where('type_id',$type_id);
        }
        if(isset($status_id)&& $status_id!=''){
            $list = $list->where('status_id',$status_id);
        }
        if(isset($room_id)&& $room_id!=''){
            $list = $list->where('room_id',$room_id);
        }
        
        if(isset($user_owner)&& $user_owner!= 0){
            $list = $list->where('user_owner',$user_owner);
        }else if(isset($user_owner) && $user_owner == 0 && $user_owner !='undefined'){
            $list = $list->where('user_owner',$user_owner);
        }

        if(isset($sDate)&& $sDate!=''||isset($eDate)&& $eDate!=''){
            if($sDate !== null && $eDate !== null)
                $list = $list->where('deal_date', '>=', $this->fncDateTimeConvertFomat($sDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))
                    ->where('deal_date', '<=', $this->fncDateTimeConvertFomat($eDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
            else if($sDate !== null && $eDate == null)
            $list = $list->where('deal_date', '>=', $this->fncDateTimeConvertFomat($sDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
            else if($sDate == null && $eDate !== null)
            $list = $list->where('deal_date', '<=', $this->fncDateTimeConvertFomat($eDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
        }

        if(isset($warranty)&& $warranty!=''){
            if($warranty == 1){
                $list = $list->where('period_date', '>=', Carbon::now()->startOfDay());
            }
            if($warranty == 2){
                $list = $list->where('period_date', '<', Carbon::now()->startOfDay());
            }
        }

        if($list->count() > 0){
            return Excel::download(new EqExport($request), 'ds_chi_tiet_thiet_bi.xlsx');
        }else{
            return $this->jsonErrors(['Không có dữ liệu!']);
        }
    }
    /**
     * @param Request $request
     * @return View (equipment-maintenance)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * Get data Equipment and return view
     */
    public function showMaintenance(Request $request, $id = null){
        $this->data['maintenance'] = Equipment::query()
                    ->where('id', $id)
                    ->first();

        return $this->viewAdminIncludes('equipment-maintenance', $this->data);
    }
     /**
     * Show popup insert,update can insert multiple records
     * @param Request $request
     * @param null $oneId
     * @return View (equipment-maintenance-detail)
     */
    public function showMaintenanceDetail(Request $request, $oneId=null){
        $this->data['maintenance'] = Equipment::query()
                    ->where('id', $oneId)
                    ->first();
        return $this->viewAdminIncludes('equipment-maintenance-detail', $this->data);
    }
    /**
     * Export excel QR code
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportQR(Request $request){
        $type_id= $request['type_id'];
        $status_id= $request['status_id'];
        $room_id= $request['room_id'];
        $warranty= $request['warranty'];
        $user_owner= $request['user_owner'];
        $sDate= $request['sDate'];
        $eDate= $request['eDate'];
        $search= $request['search'];
        $list = Equipment::query()->orderBy('id', 'desc');
        if(isset($search)){
            $one = Equipment::query()->select('equipment.id','equipment.code','equipment.name','equipment.period_date','equipment.serial_number','equipment.provider','equipment.info','equipment_types.type_name','rooms.Name','users.FullName')
                ->leftJoin('rooms','rooms.id','=','equipment.room_id')
                ->leftJoin('users','users.id','=','equipment.user_owner')
                ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
                ->first();
            if($one){
                $one = $one->toArray();
                if(array_key_exists('search', $request->input())){
                    if(null !== $request->input('search')){
                    $list = $list->select('equipment.*')
                                ->leftJoin('rooms','rooms.id','=','equipment.room_id')
                                ->leftJoin('users','users.id','=','equipment.user_owner')
                                ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
                        ->where(function ($query) use ($one, $request){
                        foreach($one as $key=>$value){
                            if($key == 'Name') {
                                $query->orWhere('rooms.'.$key, 'like', '%'.$request->input('search').'%');
                            }
                            else if($key == 'FullName') {
                                $query->orWhere('users.'.$key, 'like', '%'.$request->input('search').'%');
                            }
                            else if($key == 'type_name') {
                                $query->orWhere('equipment_types.'.$key, 'like', '%'.$request->input('search').'%');
                            }else{
                                if(in_array($key, ['provider', 'deal_date'])){
                                    $query->orWhereRaw('(DATE_FORMAT(equipment.'.$key.',"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                                }else{
                                    if(in_array($key, ['code', 'name','serial_number','info'])){
                                    $query->orWhere('equipment.'.$key, 'like', '%'.$request->input('search').'%');
                                    }
                                }
                            }
                            $query->orWhereRaw('(DATE_FORMAT(equipment.provider,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                            $query->orWhereRaw('(DATE_FORMAT(equipment.deal_date,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                        }
                        
                    });
                }
                }
            }
        }
        $list = $list->get();
        
        $this->data['rooms'] = Room::query()
            ->select('rooms.id', 'rooms.Name')
            ->join('equipment', 'equipment.room_id', 'rooms.id')
            ->groupBy('rooms.id')
            ->get();
        $this->data['owners'] = User::query()
            ->select('users.id', 'users.FullName')
            ->join('equipment', 'equipment.user_owner', 'users.id')
            ->groupBy('users.id')
            ->get();
        $this->data['eqTypes'] = EquipmentType::query()
            ->select('type_id', 'type_name')
            ->get();
        $this->data['eqStatus'] = MasterData::query()
            ->where('DataKey', 'TB')
            ->get();

        //sreach    
        if(isset($type_id)&& $type_id!=''){
            $list = $list->where('type_id',$type_id);
        }
        if(isset($status_id)&& $status_id!=''){
            $list = $list->where('status_id',$status_id);
        }
        if(isset($room_id)&& $room_id!=''){
            $list = $list->where('room_id',$room_id);
        }
        
        if(isset($user_owner)&& $user_owner!= 0){
            $list = $list->where('user_owner',$user_owner);
        }else if(isset($user_owner) && $user_owner == 0 && $user_owner !='undefined'){
            $list = $list->where('user_owner',$user_owner);
        }

        if(isset($sDate)&& $sDate!=''||isset($eDate)&& $eDate!=''){
            if($sDate !== null && $eDate !== null)
                $list = $list->where('deal_date', '>=', $this->fncDateTimeConvertFomat($sDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))
                    ->where('deal_date', '<=', $this->fncDateTimeConvertFomat($eDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
            else if($sDate !== null && $eDate == null)
            $list = $list->where('deal_date', '>=', $this->fncDateTimeConvertFomat($sDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
            else if($sDate == null && $eDate !== null)
            $list = $list->where('deal_date', '<=', $this->fncDateTimeConvertFomat($eDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
        }

        if(isset($warranty)&& $warranty!=''){
            if($warranty == 1){
                $list = $list->where('period_date', '>=', Carbon::now()->startOfDay());
            }
            if($warranty == 2){
                $list = $list->where('period_date', '<', Carbon::now()->startOfDay());
            }
        }

        if($list->count() > 0){
            return Excel::download(new EqExportQR($request), 'ds_ma_thiet_bi.xlsx');
        }else{
            return $this->jsonErrors(['Không có dữ liệu!']);
        }
    }
     /**
     * Show popup insert,update can insert multiple records
     * @param Request $id
     * @param null $oneId
     * @return View ()
     */
    public function exportQRView(Request $request){
        $id = $request->input('device');
        $list = Equipment::query()
        ->leftJoin('rooms','rooms.id','=','equipment.room_id')
        ->leftJoin('users','users.id','=','equipment.user_owner')
        ->leftJoin('master_data','master_data.id','=','equipment.status_id')
        ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
        ->where('equipment.code',$id);
        $this->data['list'] = $list->get();
        if (strpos(\Request::getRequestUri(), 'api') !== false) {
            return $this->data;
        }
        return $this->viewAdminLayout('equipment-QRcode', $this->data);
    }
    //API
    /**
     * @param Request $request
     * @return View (daily-report)
     * @throws AuthorizationException
     * Get data Equipment and return view
     */
    public function indexApi(Request $request,$orderBy = 'id', $sortBy = 'desc') {
        $this->authorize('action', $this->view);
        $recordPerPage = $this->getRecordPage();

        //list
        if(Schema::hasColumn('equipment',$orderBy)){
            $list = Equipment::orderBy('equipment.'.$orderBy, $sortBy);
        }
        else
        {
            return redirect()->back();
        }
        //loai thiet bi

        $this->data['eqTypes'] = EquipmentType::query()
            ->select('type_id', 'type_name')
            ->get();
        $this->data['eqStatus'] = MasterData::query()
            ->where('DataKey', 'TB')
            ->get();

        $this->data['rooms'] = DB::table('rooms')
            ->select('rooms.id', 'rooms.Name')
            ->join('equipment', 'equipment.room_id', 'rooms.id')
            ->groupBy('rooms.id')
            ->get();

        $this->data['owners'] = DB::table('users')
            ->select('users.id', 'users.FullName')
            ->join('equipment', 'equipment.user_owner', 'users.id')
            ->groupBy('users.id')
            ->orderBy('username')
            ->get();


        //tim kiem theo column
        $this->data['request'] = $request->query();
        $one = Equipment::query()->select('equipment.id','equipment.code','equipment.name','equipment.period_date','equipment.serial_number','equipment.provider','equipment.info','equipment_types.type_name','rooms.Name','users.FullName')
            ->leftJoin('rooms','rooms.id','=','equipment.room_id')
            ->leftJoin('users','users.id','=','equipment.user_owner')
            ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
            ->first();
        if($one){
            $one = $one->toArray();
            if(array_key_exists('search', $request->input())){
                if(null !== $request->input('search')){
                $list = $list->select('equipment.*')
                            ->leftJoin('rooms','rooms.id','=','equipment.room_id')
                            ->leftJoin('users','users.id','=','equipment.user_owner')
                            // ->join('master_data','master_data.id','=','equipment.status_id')
                            ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
                    ->where(function ($query) use ($one, $request){
                    foreach($one as $key=>$value){
                        if($key == 'Name') {
                            $query->orWhere('rooms.'.$key, 'like', '%'.$request->input('search').'%');
                        }
                        else if($key == 'FullName') {
                            $query->orWhere('users.'.$key, 'like', '%'.$request->input('search').'%');
                        }
                        else if($key == 'type_name') {
                            $query->orWhere('equipment_types.'.$key, 'like', '%'.$request->input('search').'%');
                        }else{
                            if(in_array($key, ['provider', 'deal_date'])){
                                $query->orWhereRaw('(DATE_FORMAT(equipment.'.$key.',"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                            }else{
                                if(in_array($key, ['code', 'name','serial_number','info'])){
                                $query->orWhere('equipment.'.$key, 'like', '%'.$request->input('search').'%');
                                }
                            }
                        }
                        $query->orWhereRaw('(DATE_FORMAT(equipment.provider,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                        $query->orWhereRaw('(DATE_FORMAT(equipment.deal_date,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                    }
                    
                });
            }
            }
        }

        //tim kiếm
        foreach($this->data['request'] as $key => $value){ 
            if(!is_array($value)){
                if(Schema::hasColumn('equipment', $key) && $value !== null){
                   $list = $list->where('equipment.'.$key, '=', $value);
                }
            }else{
                if($value[0] !== null && $value[1] !== null && \DateTime::createFromFormat('d/m/Y', $value[0]) !== FALSE && \DateTime::createFromFormat('d/m/Y', $value[1]) !== FALSE){
                    $list = $list->where('equipment.deal_date', '>=', $this->fncDateTimeConvertFomat($value[0], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))
                    ->where('equipment.deal_date', '<=', $this->fncDateTimeConvertFomat($value[1], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                }
                else if($value[0] !== null && $value[1] == null && \DateTime::createFromFormat('d/m/Y', $value[0]) !== FALSE){
                    $list = $list->where('equipment.deal_date', '>=', $this->fncDateTimeConvertFomat($value[0], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                }
                else if($value[0] == null && $value[1] !== null && \DateTime::createFromFormat('d/m/Y', $value[1]) !== FALSE){
                    $list = $list->where('equipment.deal_date', '<=', $this->fncDateTimeConvertFomat($value[1], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                }
                else if(($value[0] !== null || $value[1] !== null) && (\DateTime::createFromFormat('d/m/Y', $value[0]) === FALSE || \DateTime::createFromFormat('d/m/Y', $value[1]) === FALSE)){
                // $list = $list->where('equipment.deal_date', '=', $this->fncDateTimeConvertFomat('30/02/2020', self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                    return Redirect::back()->withErrors(['Ngày tìm kiếm không hợp lệ']);
                }
            }
            if($key == 'warranty'){
                if($value == 1){
                    $list = $list->where('period_date', '>=', Carbon::now()->startOfDay());
                }
                if($value == 2){
                    $list = $list->where('period_date', '<', Carbon::now()->startOfDay());
                }
            }

        }
        $user = User::find(Auth::user()->id);
        if ($user->cant('admin', $this->menu)) {
            $list = $list->where('user_owner', Auth::user()->id);
        }
        //phan trang
        $list = $list->paginate($recordPerPage);
        foreach($list as $item){
            $status = MasterData::find($item->status_id);
            if($status) $item->status = $status->Name;
            if($item->user_owner){
                // $item->ownerName = User::find($item->user_owner)->FullName; 

                //Tien 1/4/2020
                if(!empty($item->user_owner)){
                    $ownerName = User::query()->withTrashed()
                    ->where('id', $item->user_owner)          
                    ->first();
                    if(!empty($ownerName)){
                        $item->ownerName = $ownerName->FullName;
                    }
                }
                //Tien 1/4/2020
            }
            if($item->room_id){
                $item->room = Room::find($item->room_id)->Name;
            }
        }
        $this->data['list'] = $list;

        $query_string = str_replace($request->url(), '', $request->fullUrl());

        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
        parse_str(str_replace('?', '', $query_string), $query_array);

        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;

        $data = $this->data;
        $data['role_key'] = 'EquipmentList';
        return AdminController::responseApi(200, null, null, $data);
    }
    /**
     * Process insert multiple records
     * @param Request $request
     * @return string|void
     */
    public function storeApi(Request $request){
        $this->authorize('action', $this->add);
        if (count($request->input()) === 0) {
            return AdminController::responseApi(422, __('admin.error.data'));
        }
        try{
            $validator = Validator::make($request->all(),
                [
                    'type_id'   =>  'string|required',
                    'name' =>  'string|required',
                    'serial_number' =>  'string|nullable',
                    'info' =>  'string|nullable',
                    'provider' =>  'string|nullable',
                    'buy_date' =>  'date_format:d/m/Y|nullable',
                    'period_date' =>  'numeric|min:0|nullable',
                    'deal_date' =>  'date_format:d/m/Y|nullable',
                    'unit_price' =>  'numeric|nullable',
                    'note' =>  'string|nullable',
                    'updated_user' =>  'integer|min:1|nullable',
                    'status_id' =>  'integer|min:1|nullable',
                    'user_owner' =>  'integer|min:0|nullable',
                    'register_id' =>  'integer|min:1|nullable',
                    'room_id' =>  'integer|min:1|nullable',
                ]);

            if ($validator->fails())
            {
                return AdminController::responseApi(422, $validator->errors()->first());
            }
            $validated = $validator->validate();
            if(isset($validated['buy_date'])){
                $eqbuy_date = strtotime($this->fncDateTimeConvertFomat($validated['buy_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                $eqdeal_date = strtotime($this->fncDateTimeConvertFomat($validated['deal_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                if( $eqbuy_date > $eqdeal_date ){
                    return AdminController::responseApi(422, __('admin.error.equipment.date'));
                } 
            }
            if(isset($validated['period_date'])){
                if(!isset($validated['buy_date'])){
                    return AdminController::responseApi(422, __('admin.error.equipment.buydate'));
                }
            }

            if(isset($validated['serial_number'])){
                $check = preg_match('/^[A-Z0-9]+$/',$validated['serial_number']);
                if(!$check){
                    return AdminController::responseApi(422, __('admin.error.equipment.number'));
                }
            }
            if(isset($validated['name'])){
                $check1 = preg_match('/[(&|@|!|#|^|$|%|*|+|=)]/', $validated['name']);
                $check3 = is_numeric($validated['name']);
                if($check1 == 1 || $check3){
                    return AdminController::responseApi(422, __('admin.error.equipment.name'));
                }
            }
            if(isset($validated['provider'])){
                $check2 = preg_match('/[(&|@|!|#|^|$|%|*|+|=)]/', $validated['provider']);
                if($check2 == 1){
                    return AdminController::responseApi(422, __('admin.error.equipment.provider'));
                }
            }
            $eqcheck = Equipment::query()
                ->select('id','serial_number')
                ->get();
            
            foreach($eqcheck as $row){
                if($row['serial_number'] == $validated['serial_number'] && isset($validated['serial_number']))
                {
                    return AdminController::responseApi(422, __('admin.error.equipment.serialnumber'));
                }
                
            }
            $one = new Equipment();
            $equipmentSame = Equipment::query()->withTrashed()
                ->where('type_id', $validated['type_id'])
                ->orderBy('id', 'desc')
                ->first();

            if($equipmentSame){
                $one->code = $this->getEquipmentCode($equipmentSame->code);
            }else{
                $one->code = $validated['type_id']."0001";
            }
            if($one->code != ''){
                $newHistory = new EquipmentUsingHistory();
                $newHistory->code = $one->code;
                $newHistory->created_user = Auth::user()->id;
                $newHistory->status_id = $validated['status_id'];
                $newHistory->old_status_id = $validated['status_id'];
                if($validated['user_owner'] == null){
                    $one->user_owner = 0;
                }
                $newHistory->user_owner = $one->user_owner;
                $newHistory->old_user_owner = $one->user_owner;
                if(isset($validated['deal_date'])){
                    $newHistory->deal_date = $this->fncDateTimeConvertFomat($validated['deal_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                }
                $newHistory->save();
            }

            foreach($validated as $key => $value){
                if(Schema::hasColumn('equipment', $key))
                    $one->$key = $value;
            }
            if($validated['user_owner'] == null){
                $one->user_owner = 0;
            }
            if(isset($validated['period_date'])){
               $one->period_date = Carbon::parse($this->fncDateTimeConvertFomat($validated['buy_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))->addMonth($validated['period_date']);
            }
            if(isset($validated['buy_date'])){
                $one->buy_date = $this->fncDateTimeConvertFomat($validated['buy_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
            }

            if(isset($validated['deal_date'])){
                $one->deal_date = $this->fncDateTimeConvertFomat($validated['deal_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
            }

            $one->updated_user = Auth::user()->id;
            $save =$one->save();
            if (!$save) {
                return AdminController::responseApi(403, __('admin.error.save'));
            } else {
                return AdminController::responseApi(200, null, __('admin.success.save'));
            }
        }
        catch (\Exception $e){
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
        if (count($request->input()) === 0) {
            return AdminController::responseApi(422, __('admin.error.data'));
        }
        try{
            $validator = Validator::make($request->all(),
                [

                    'id'    =>  'integer|min:1|nullable',
                    'type_id'   =>  'string|required',
                    'name' =>  'string|required',
                    'serial_number' =>  'string|nullable',
                    'info' =>  'string|nullable',
                    'provider' =>  'string|nullable',
                    'buy_date' =>  'date_format:d/m/Y|nullable',
                    'period_date' =>  'numeric|min:0|nullable',
                    'deal_date' =>  'date_format:d/m/Y|nullable',
                    'unit_price' =>  'numeric|nullable',
                    'note' =>  'string|nullable',
                    'updated_user' =>  'integer|min:1|nullable',
                    'status_id' =>  'integer|min:1|required',
                    'user_owner' =>  'integer|min:0|nullable',
                    'register_id' =>  'integer|min:1|nullable',
                    'room_id' =>  'integer|min:1|nullable',

                ]);
            
            if ($validator->fails())
            {
                return AdminController::responseApi(422, $validator->errors()->first());
            }
            $validated = $validator->validate();
            if(isset($validated['buy_date'])){
                $eqbuy_date = strtotime($this->fncDateTimeConvertFomat($validated['buy_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                $eqdeal_date = strtotime($this->fncDateTimeConvertFomat($validated['deal_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                if( $eqbuy_date > $eqdeal_date ){
                    return AdminController::responseApi(422, __('admin.error.equipment.date'));
                } 
            }
            if(isset($validated['period_date'])){
                if(!isset($validated['buy_date'])){
                    return AdminController::responseApi(422, __('admin.error.equipment.buydate'));
                }
            }

            if(isset($validated['serial_number'])){
                $check = preg_match('/^[A-Z0-9]+$/',$validated['serial_number']);
                if(!$check){
                    return AdminController::responseApi(422, __('admin.error.equipment.number'));
                }
            }
            if(isset($validated['name'])){
                $check1 = preg_match('/[(&|@|!|#|^|$|%|*|+|=)]/', $validated['name']);
                $check3 = is_numeric($validated['name']);
                if($check1 == 1 || $check3){
                    return AdminController::responseApi(422, __('admin.error.equipment.name'));
                }
            }
            if(isset($validated['provider'])){
                $check2 = preg_match('/[(&|@|!|#|^|$|%|*|+|=)]/', $validated['provider']);
                if($check2 == 1){
                    return AdminController::responseApi(422, __('admin.error.equipment.provider'));
                }
            }
            $eqcheck= Equipment::query()
                ->select('id','serial_number')
                ->where('id','!=',$validated['id'])
                ->get();
            
            
            foreach($eqcheck as $row){
                if($row['serial_number'] == $validated['serial_number'] && isset($validated['serial_number']))
                {
                    return AdminController::responseApi(422, __('admin.error.equipment.serialnumber'));
                }
                
            }
            $one = Equipment::find($validated['id']);
            if($one->status_id != $validated['status_id']){
                $newHistory = new EquipmentUsingHistory();
                $newHistory->code = $one->code;
                $newHistory->created_user = Auth::user()->id;
                $newHistory->status_id = $validated['status_id'];
                $newHistory->old_status_id = $one->status_id;
                $newHistory->deal_date = Carbon::now();
                if($validated['user_owner'] == null){
                    $one->user_owner = 0;
                }
                $newHistory->user_owner = $one->user_owner;
                $newHistory->old_user_owner = $one->user_owner;
                $newHistory->note = "Cập nhật trạng thái thiết bị";
                $newHistory->save();
            }

            foreach($validated as $key => $value){
                if(Schema::hasColumn('equipment', $key))
                    $one->$key = $value;
            }
            if($validated['user_owner'] == null || !array_key_exists('id', $validated)){
                $one->user_owner = 0;
            }
            if(isset($validated['period_date'])){
               $one->period_date = Carbon::parse($this->fncDateTimeConvertFomat($validated['buy_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))->addMonth($validated['period_date']);
            }
            if(isset($validated['buy_date'])){
                $one->buy_date = $this->fncDateTimeConvertFomat($validated['buy_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
            }

            if(isset($validated['deal_date'])){
                $one->deal_date = $this->fncDateTimeConvertFomat($validated['deal_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
            }

            $one->updated_user = Auth::user()->id;
            $save =$one->save();
            if (!$save) {
                return AdminController::responseApi(403, __('admin.error.save'));
            } else {
                return AdminController::responseApi(200, null, __('admin.success.save'));
            }
        }
        catch (\Exception $e){
            return AdminController::responseApi(422, $e->getMessage());
        }   
    }
    /**
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteApi(Request $request, $id = null) {
        $this->authorize('action', $this->delete);
        if($id!=null){
            $one = Equipment::find($id);
            if($one){
                $one->delete();
            }
            return AdminController::responseApi(200, null, __('admin.success.delete'));
        }
        return AdminController::responseApi(422, __('admin.error.data'));
    }
    /**
     * Show popup insert,update can insert multiple records
     * @param Request $request
     * @param null $oneId
     * @return View (equipment-detail)
     */
    public function showDetailApi(Request $request, $oneId=null){
        if($request->exists('copy')){
            $this->data['copy'] = 1;
        }
        $this->data['types'] = EquipmentType::all();
        $this->data['status_list'] = MasterData::query()
            ->where('datakey', 'TB')
            ->get();
        $this->data['register_rooms'] = DB::table('rooms')
            ->join('users', 'users.RoomId', 'rooms.id')
            ->select('rooms.*')
            ->get();

        //vi tri dat thiet bi
        $this->data['put_rooms'] = Room::query()
            ->where('Active', 1)
            ->get();

        // Tien 23/3/2020
            $this->data['Equipment'] = Equipment::query()
            ->select('id','serial_number')
            ->get();
        // Tien 23/3/2020
        if($oneId!=null){
            $this->data['itemInfo'] = Equipment::find($oneId);
            $this->data['registered_room'] = DB::table('equipment')
                ->join('users', 'users.id', 'equipment.user_owner')
                ->join('rooms', 'users.RoomId', 'rooms.id')
                ->select('rooms.*')
                ->where('equipment.user_owner', $this->data['itemInfo']->user_owner)
                ->first();
            if($this->data['registered_room']){

                $this->data['list_users'] = User::query()
                    ->where('RoomId', $this->data['registered_room']->id)
                    ->get();
            }else{
                $this->data['list_users'] = new \stdClass();

            }
            //print_r($this->data['registered_room']);
            $data = $this->data;
            if($this->data['itemInfo']){
                return AdminController::responseApi(200, null, null, $data);
            }else{
                return AdminController::responseApi(422, __('admin.error.data'));
            }
        }else{
            $data = $this->data;
            return AdminController::responseApi(200, null, null, $data);
        }

    }

    /**
     * show view History Equipment
     * @param $oneId
     * @return View (equipment-status-histories)
     */
    public function showStatusHistoryApi($oneId){
        $eq = Equipment::find($oneId);
        if($eq){
            $this->data['eq'] = $eq;
            $this->data['histories'] = EquipmentUsingHistory::query()
                ->where('code', $eq->code)
                ->get();
            foreach($this->data['histories'] as $item){
                if($item->user_owner > 0){
                    $array= User::find($item->user_owner);
                    if($array!= null){
                        $item->user_owner = $array->FullName;
                    }else{
                        $item->user_owner = 'Không biết người sử dụng';
                    }
                }else{
                    $item->user_owner = __("admin.equipment.store");
                }
                if($item->old_user_owner > 0){
                    $item->old_user_owner = User::find($item->old_user_owner)->FullName;
                }else{
                    $item->old_user_owner = __("admin.equipment.store");
                }
                if(!empty($item->status_id)){
                    $item->status_id = MasterData::find($item->status_id)->Name;
                }
                if(!empty($item->old_status_id)){
                    $item->old_status_id = MasterData::find($item->old_status_id)->Name;
                }
                $item->created_user = User::find($item->created_user)->FullName;
            }
            $data = $this->data;
            return AdminController::responseApi(200, null, null, $data);
        }

    }
}
