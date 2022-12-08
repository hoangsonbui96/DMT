<?php

namespace App\Http\Controllers\Admin;

use App\Equipment;
use App\EquipmentType;
use App\EquipmentUsingHistory;
use App\MasterData;
use App\Room;
use App\User;
use App\RoleScreenDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EqExportHistories;
/**
 * Class EquipmentHistoryController
 * @package App\Http\Controllers\Admin
 * Controller screen Equipment History
 */
class EquipmentHistoryController extends AdminController
{
    protected $add;
    protected $addR;
    protected $edit;
    protected $delete;
    protected $export;
    protected $view;
    const KEYMENU= array(
        "add" => "EquipmentRegistrationsAdd",
        "view" => "EquipmentRotation",
        "edit" => "EquipmentRegistrationsEdit",
        "delete" => "EquipmentRegistrationsDelete",
        "addR" => "EquipmentRotationCan",
        "export" => "EquipmentRotationExport",
    );
    /**
     * EquipmentHistoryController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        // parent::__construct($request);
        // $this->middleware('auth');
        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
        $array = $this->RoleView(null,['EquipmentRotation']);
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if($value == $row->alias)
                    $this->$key = $row;
            } 
        }
    }

    /**
     * @param Request $request
     * @return View (equipment-using-histories)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * Get data EquipmentUsingHistory and return view
     */
    public function show(Request $request, $orderBy = 'id', $sortBy = 'desc'){

        $recordPerPage = $this->getRecordPage();
        $this->authorize('view', $this->menu);
        //list
        if(Schema::hasColumn('equipment_using_histories',$orderBy)){
            $list = EquipmentUsingHistory::orderBy('equipment_using_histories.'.$orderBy, $sortBy);
                // ->whereColumn('user_owner', '<>', 'old_user_owner');
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
            ->join('equipment', 'equipment.room_id', 'rooms.id')
            ->get();

        $this->data['owners'] = User::query()
            ->select('users.id', 'users.FullName')
            ->join('equipment_using_histories', 'equipment_using_histories.user_owner', 'users.id')
            ->groupBy('users.id')
            ->get();

        $this->data['created_users'] = DB::table('users')
            ->select('users.id', 'users.FullName')
            ->join('equipment_using_histories', 'equipment_using_histories.created_user', 'users.id')
            ->groupBy('users.id')
            ->get();

        //tim kiem theo column
        $this->data['request'] = $request->query();
        // dd($this->data['request']);
        // die();
        $one = EquipmentUsingHistory::query()->select('equipment.code','equipment.name','equipment.info',
            'equipment_types.type_name','us1.FullName as CreatedUser','us2.FullName as OldUserOwner','us3.FullName as FullName')
        ->leftJoin('equipment','equipment.code','=','equipment_using_histories.code')
        ->leftJoin('users as us2','us2.id','=','equipment_using_histories.user_owner')
        ->leftJoin('users as us1','us1.id','=','equipment_using_histories.created_user')
        ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
        ->leftJoin('users as us3','us3.id','=','equipment_using_histories.old_user_owner')
        ->first();
        if($one){
            $one = $one->toArray();
            if(array_key_exists('search', $request->input())){
                if(null !== $request->input('search')){
                $list = $list->select('equipment_using_histories.id as idhq','equipment_using_histories.code','equipment_using_histories.user_owner','equipment_using_histories.created_user','equipment_using_histories.old_user_owner','equipment_using_histories.status_id','equipment.code','equipment.name','equipment.type_id','equipment_using_histories.deal_date','equipment_using_histories.old_status_id')
                            ->leftJoin('equipment','equipment.code','=','equipment_using_histories.code')
                            ->leftJoin('users as us2','us2.id','=','equipment_using_histories.user_owner')
                            ->leftJoin('users as us1','us1.id','=','equipment_using_histories.created_user')
                            ->leftJoin('users as us3','us3.id','=','equipment_using_histories.old_user_owner')
                            ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
                ->where(function ($query) use ($one, $request){
                    foreach($one as $key=>$value){
                        if($key == 'FullName') {
                            $query
                            ->orWhere('us3.'.$key, 'like', '%'.$request->input('search').'%');
                        }
                        else if($key == 'type_name') {
                            $query->orWhere('equipment_types.'.$key, 'like', '%'.$request->input('search').'%');
                        }else if($key == 'OldUserOwner'){
                            $query->orWhere('us2.FullName', 'like', '%'.$request->input('search').'%');
                        }
                        else if($key == 'CreatedUser'){
                            $query->orWhere('us1.FullName', 'like', '%'.$request->input('search').'%');
                        }
                        else{
                            if(in_array($key, ['deal_date' ])){
                            $query->orWhereRaw('(DATE_FORMAT(equipment_using_histories.deal_date,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );

                            }else{
                                $query->orWhere('equipment.'.$key, 'like', '%'.$request->input('search').'%');
                            }
                        }
                        $query->orWhereRaw('(DATE_FORMAT(equipment_using_histories.deal_date,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                    }
                });
                }
            }
        }

        //tim kiếm
        foreach($this->data['request'] as $key => $value){
            if(!is_array($value)){
                if($key == 'type_id'){
                    if(Schema::hasColumn('equipment', $key) && $value !== null){
                        if(null === $request->input('search')){
                            $list = $list->join('equipment','equipment.code','=','equipment_using_histories.code')
                                ->join('equipment_types','equipment_types.type_id','=','equipment.type_id');
                        }
                        $list = $list->where('equipment.type_id',$value);
                    }
                }
                if($key == 'created_user' || $key == 'user_owner'){
                    if(Schema::hasColumn('equipment_using_histories', $key) && $value !== null){
                        $list = $list->where('equipment_using_histories.'.$key, $value);
                    }
                }else if(Schema::hasColumn('equipment_using_histories', $key) && $value !== null){
                    $list = $list->where('equipment_using_histories.'.$key, 'like', '%'.$value.'%');
                }
            }else{
                if($value[0] !== null && $value[1] !== null && \DateTime::createFromFormat('d/m/Y', $value[0]) !== FALSE && \DateTime::createFromFormat('d/m/Y', $value[1]) !== FALSE){
                       $list = $list->where('equipment_using_histories.deal_date', '>=', $this->fncDateTimeConvertFomat($value[0], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))
                        ->where('equipment_using_histories.deal_date', '<=', $this->fncDateTimeConvertFomat($value[1], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                }
                else if($value[0] !== null && $value[1] == null && \DateTime::createFromFormat('d/m/Y', $value[0]) !== FALSE){
                    $list = $list->where('equipment_using_histories.deal_date', '>=', $this->fncDateTimeConvertFomat($value[0], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                }
                 else if($value[0] == null && $value[1] !== null && \DateTime::createFromFormat('d/m/Y', $value[1]) !== FALSE){
                    $list = $list->where('equipment_using_histories.deal_date', '<=', $this->fncDateTimeConvertFomat($value[1], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                }
                 else if(($value[0] !== null || $value[1] !== null)&& \DateTime::createFromFormat('d/m/Y', $value[0]) === FALSE && \DateTime::createFromFormat('d/m/Y', $value[1]) === FALSE){
                    // $list = $list->where('equipment_using_histories.deal_date', '=', $this->fncDateTimeConvertFomat('30/02/2020', self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                    return Redirect::back()->withErrors(['Ngày tìm kiếm không hợp lệ']);
                }
            }

        }

        //phan trang
        $list = $list->paginate($recordPerPage);
        foreach($list as $item){
            if(!empty($item->status_id)){
                $item->status = MasterData::find($item->status_id)->Name;
            }
            
            if($item->user_owner){
                $ownerName = User::query()->withTrashed()
                    ->where('id', $item->user_owner)          
                    ->first();
                if(!empty($ownerName)){
                    $item->ownerName = $ownerName->FullName;
                }
            }else{
                $item->ownerName = "Kho";
            }
            if($item->old_user_owner){
                $oldOwnerNames = User::find($item->old_user_owner);
                if(!empty($oldOwnerNames)){
                    $item->oldOwnerName = $oldOwnerNames->FullName;
                }
            }else{
                $item->oldOwnerName = "Kho";
            }
            if($item->room_id){
                $item->room = Room::find($item->room_id)->Name;
            }
            if($item->created_user){
                $created_user_name = User::query()->withTrashed()
                    ->where('id', $item->created_user)          
                    ->first();
                if(!empty($created_user_name)){
                    $item->created_user_name = $created_user_name->FullName;
                }
            }
            $eq = Equipment::query()->withTrashed()
                ->where('code', $item->code)
                ->first();
            if($eq){
                $item->eqName = $eq->name;
                if(!empty($eq->type_id)){
                    $eqTypeName = EquipmentType::query()->withTrashed()
                    ->where('type_id', $eq->type_id)
                    ->first();
                    if(!empty($eqTypeName)){
                        $item->eqTypeName = $eqTypeName->type_name;
                    }
                }
            }
            if(!empty($item->status_id)){
                $item->current_status = MasterData::find($item->status_id)->Name;
            }
            if(!empty($item->old_status_id)){
                $item->old_status = MasterData::find($item->old_status_id)->Name;
            }
        }
        $this->data['list'] = $list;
        $query_string = str_replace($request->url(), '', $request->fullUrl());

        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
        parse_str(str_replace('?', '', $query_string), $query_array);
        $this->data['query_array'] = $query_array;

        $this->data['sort_link'] = $sort_link;
        $this->data['add'] = $this->add;
        $this->data['addR'] = $this->addR;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;
        $this->data['export'] = $this->export;
        $this->data['recordPerPage'] = $recordPerPage;
        return $this->viewAdminLayout('equipment-using-histories', $this->data);
    }

    /**
     * Process insert multiple records,update one records
     * @param Request $request
     * @param null $id
     * @return string|void
     */
    public function store(Request $request, $id=null){
        try{
            if(count($request->input()) >0){
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
                            'status_id' =>  'integer|min:1|nullable',
                            'user_owner' =>  'integer|min:1|nullable',
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
                            'buy_date' =>  'date_format:d/m/Y h:i|nullable',
                            'period_date' =>  'numeric|min:0|nullable',
                            'deal_date' =>  'date_format:d/m/Y|nullable',
                            'unit_price' =>  'numeric|nullable',
                            'note' =>  'string|nullable',
                            'updated_user' =>  'integer|min:1|nullable',
                            'status_id' =>  'integer|min:1|nullable',
                            'user_owner' =>  'integer|min:1|nullable',
                            'register_id' =>  'integer|min:1|nullable',
                            'room_id' =>  'integer|min:1|nullable',
                        ]);
                }

                if ($validator->fails())
                {
                    return response()->json(['errors'=>$validator->errors()->all()]);
                }

                $validated = $validator->validate();

                if(array_key_exists('id', $validated)){
                    $one = Equipment::find($validated['id']);
                }else{
                    $one = new Equipment();
                }
                foreach($validated as $key => $value){
                    if(Schema::hasColumn('equipment', $key))
                        $one->$key = $value;
                }

                if(isset($validated['period_date'])){
                    $one->period_date = Carbon::parse($validated['buy_date'])->addMonth($validated['period_date']);
                }
                if(isset($validated['deal_date'])){
                    $one->deal_date = $this->fncDateTimeConvertFomat($validated['deal_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                } 

                $equipmentSame = Equipment::query()
                    ->where('type_id', $validated['type_id'])
                    ->orderBy('id', 'desc')
                    ->first();
                if($equipmentSame){
                    $one->code = $this->getEquipmentCode($equipmentSame->code);
                }else{
                    $one->code = $validated['type_id']."0001";
                }
                $one->updated_user = Auth::user()->id;
                $one->save();


                return $this->jsonSuccessWithRouter('admin.Equipment');

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
     * @param null $oneId
     * @param null $del
     * @return View (equipment-history-detail)
     */
    public function showDetail(Request $request, $oneId=null, $del=null){
        if($request->exists('copy')){
            $this->data['copy'] = 1;
        }
        $this->data['types'] = EquipmentType::all();
        $this->data['status_list'] = MasterData::query()
            ->where('datakey', 'TB')
            ->get();
        $this->data['owners'] = DB::table('users')
            ->join('equipment', 'equipment.user_owner', 'users.id')
            ->select('users.*')
            ->groupBy('users.id')
            //Tien 1/4/2020
            ->where('equipment.deleted_at', null)
            //Tien 1/4/2020
            ->get();
        $this->data['receive_owners'] = User::query()
            ->where('Active', 1)
            // ->where('id','!=', 1)
            ->get();
        if($oneId!=null){
            if($del == 'del'){
                $one = Equipment::find($oneId);
                if($one) $one->delete();
                return 1;
            }

            $this->data['itemInfo'] = Equipment::find($oneId);
            $this->data['registered_room'] = DB::table('equipment')
                ->join('users', 'users.id', 'equipment.user_owner')
                ->join('rooms', 'users.RoomId', 'rooms.id')
                ->select('rooms.*')
                ->where('equipment.user_owner', $this->data['itemInfo']->user_owner)
                ->first();
//            print_r($this->data['registered_room']);
            if($this->data['registered_room']){

                $this->data['list_users'] = User::query()
                    ->where('RoomId', $this->data['registered_room']->id)
                    ->get();
            }else{
                $this->data['list_users'] = new \stdClass();
            }

//            print_r($this->data['registered_room']);
            if($this->data['itemInfo']){
                return $this->viewAdminIncludes('equipment-history-detail', $this->data);
            }else{
                return "";
            }
        }else{
            return $this->viewAdminIncludes('equipment-history-detail', $this->data);
        }
    }
    /**
     * Export excel
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportHistories(Request $request){
        $array =array();
        //loai thiet bi
        $this->data['eqTypes'] = EquipmentType::query()
            ->select('type_id', 'type_name')
            ->get();

        $this->data['eqStatus'] = MasterData::query()
            ->where('DataKey', 'TB')
            ->get();

        $this->data['rooms'] = DB::table('rooms')
            ->join('equipment', 'equipment.room_id', 'rooms.id')
            ->get();

        $this->data['owners'] = User::query()
            ->select('users.id', 'users.FullName')
            ->join('equipment_using_histories', 'equipment_using_histories.user_owner', 'users.id')
            ->groupBy('users.id')
            ->get();

        $this->data['created_users'] = DB::table('users')
            ->select('users.id', 'users.FullName')
            ->join('equipment_using_histories', 'equipment_using_histories.created_user', 'users.id')
            ->groupBy('users.id')
            ->get();
        $this->data['request'] = $request->query();
        // echo $request->input('search');
        for ($i=0; $i < count(explode(",",$request->input('search'))) ; $i++) {
            $list = EquipmentUsingHistory::query();
            $search= $request->input('search')!=''?explode(",",$request->input('search'))[$i]:'';
            $type_id= $request->input('type_id')!=''?explode(",",$request->input('type_id'))[$i]:'';
            $status_id= $request->input('status_id')!=''?explode(",",$request->input('status_id'))[$i]:'';
            $created_user= $request->input('created_user')!=''?$request->input('created_user'):'';
            $user_owner= $request->input('user_owner')!=''?explode(",",$request->input('user_owner'))[$i]:'';
            $DealDate= $request->input('DealDate')!=''?explode(",",$request->input('DealDate')):[];
            // die();
            # code...
            $one = EquipmentUsingHistory::query()->select('equipment.code','equipment.name','equipment.info',
                'equipment_types.type_name','us1.FullName as CreatedUser','us2.FullName as OldUserOwner','us3.FullName as FullName')
            ->leftJoin('equipment','equipment.code','=','equipment_using_histories.code')
            ->leftJoin('users as us2','us2.id','=','equipment_using_histories.user_owner')
            ->leftJoin('users as us1','us1.id','=','equipment_using_histories.created_user')
            ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
            ->leftJoin('users as us3','us3.id','=','equipment_using_histories.old_user_owner')
            ->first();
            if($one){
                $one = $one->toArray();
                if(array_key_exists('search', $request->input())){
                    if(null !== $request->input('search')){
                    $list = $list->select('equipment_using_histories.id as idhq','equipment_using_histories.code','equipment_using_histories.user_owner','equipment_using_histories.created_user','equipment_using_histories.old_user_owner','equipment_using_histories.status_id','equipment.code','equipment.name','equipment.type_id','equipment_using_histories.deal_date','equipment_using_histories.old_status_id')
                                ->leftJoin('equipment','equipment.code','=','equipment_using_histories.code')
                                ->leftJoin('users as us2','us2.id','=','equipment_using_histories.user_owner')
                                ->leftJoin('users as us1','us1.id','=','equipment_using_histories.created_user')
                                ->leftJoin('users as us3','us3.id','=','equipment_using_histories.old_user_owner')
                                ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
                    ->where(function ($query) use ($one, $search){
                        foreach($one as $key=>$value){
                            if($key == 'FullName') {
                                $query
                                ->orWhere('us3.'.$key, 'like', '%'.$search.'%');
                            }
                            else if($key == 'type_name') {
                                $query->orWhere('equipment_types.'.$key, 'like', '%'.$search.'%');
                            }else if($key == 'OldUserOwner'){
                                $query->orWhere('us2.FullName', 'like', '%'.$search.'%');
                            }
                            else if($key == 'CreatedUser'){
                                $query->orWhere('us1.FullName', 'like', '%'.$search.'%');
                            }
                            else{
                                if(in_array($key, ['deal_date' ])){
                                $query->orWhereRaw('(DATE_FORMAT(equipment_using_histories.deal_date,"%d/%m/%Y")) like ?', '%'.$search.'%' );

                                }else{
                                    $query->orWhere('equipment.'.$key, 'like', '%'.$search.'%');
                                }
                            }
                            $query->orWhereRaw('(DATE_FORMAT(equipment_using_histories.deal_date,"%d/%m/%Y")) like ?', '%'.$search.'%' );
                        }
                    });
                    }
                }
            }
            // for($j=0; $j < 6 ; $j++){
            // if(!is_array($value)){
            if($type_id != ''){
                // if(Schema::hasColumn('equipment', $key) && $value !== null){
                if(null === $request->input('search')){
                    $list = $list->join('equipment','equipment.code','=','equipment_using_histories.code')
                        ->join('equipment_types','equipment_types.type_id','=','equipment.type_id');
                }
                $list = $list->where('equipment.type_id',$type_id);
                // }
            }
            if($created_user != ''){
                // if(Schema::hasColumn('equipment_using_histories', $key) && $value !== null){
                $list = $list->where('equipment_using_histories.created_user', $created_user);
                // }
            }
            if($user_owner != ''){
                // if(Schema::hasColumn('equipment_using_histories', $key) && $value !== null){
                $list = $list->where('equipment_using_histories.user_owner', $user_owner);
                // }
            }
            if($status_id != ''){
                // if(Schema::hasColumn('equipment_using_histories', $key) && $value !== null){
                $list = $list->where('equipment_using_histories.status_id',$status_id);
                // }
            }
            // }else{
            if(!empty($DealDate)){
                if($DealDate[$i*2] !== '' && $DealDate[($i*2+1)] !== '' && \DateTime::createFromFormat('d/m/Y', $DealDate[$i*2]) !== FALSE && \DateTime::createFromFormat('d/m/Y', $DealDate[($i*2+1)]) !== FALSE){
                       $list = $list->where('equipment_using_histories.deal_date', '>=', $this->fncDateTimeConvertFomat($DealDate[$i*2], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))
                        ->where('equipment_using_histories.deal_date', '<=', $this->fncDateTimeConvertFomat($DealDate[($i*2+1)], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                }
                else if($DealDate[$i*2] !== null && $DealDate[($i*2+1)] == null && \DateTime::createFromFormat('d/m/Y', $DealDate[$i*2]) !== FALSE){
                    $list = $list->where('equipment_using_histories.deal_date', '>=', $this->fncDateTimeConvertFomat($DealDate[$i*2], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                }
                 else if($DealDate[$i*2] == null && $DealDate[($i*2+1)] !== null && \DateTime::createFromFormat('d/m/Y', $DealDate[($i*2+1)]) !== FALSE){
                    $list = $list->where('equipment_using_histories.deal_date', '<=', $this->fncDateTimeConvertFomat($DealDate[($i*2+1)], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                }
                 else if(($DealDate[$i*2] !== null || $DealDate[($i*2+1)] !== null)&& \DateTime::createFromFormat('d/m/Y', $DealDate[$i*2]) === FALSE && \DateTime::createFromFormat('d/m/Y', $DealDate[($i*2+1)]) === FALSE){
                    return Redirect::back()->withErrors(['Ngày tìm kiếm không hợp lệ']);
                }
            }
            $list = $list->get();
            foreach($list as $item){
                if(!empty($item->status_id)){
                    $item->status = MasterData::find($item->status_id)->Name;
                }
                
                if($item->user_owner){
                    $ownerName = User::query()->withTrashed()
                        ->where('id', $item->user_owner)          
                        ->first();
                    if(!empty($ownerName)){
                        $item->ownerName = $ownerName->FullName;
                    }
                }else{
                    $item->ownerName = "Kho";
                }
                if($item->old_user_owner){
                    $oldOwnerNames = User::find($item->old_user_owner);
                    if(!empty($oldOwnerNames)){
                        $item->oldOwnerName = $oldOwnerNames->FullName;
                    }
                }else{
                    $item->oldOwnerName = "Kho";
                }
                if($item->room_id){
                    $item->room = Room::find($item->room_id)->Name;
                }
                if($item->created_user){
                    $created_user_name = User::query()->withTrashed()
                        ->where('id', $item->created_user)          
                        ->first();
                    if(!empty($created_user_name)){
                        $item->created_user_name = $created_user_name->FullName;
                    }
                }
                $eq = Equipment::query()->withTrashed()
                    ->where('code', $item->code)
                    ->first();
                if($eq){
                    $item->eqName = $eq->name;
                    if(!empty($eq->type_id)){
                        $eqTypeName = EquipmentType::query()->withTrashed()
                        ->where('type_id', $eq->type_id)
                        ->first();
                        if(!empty($eqTypeName)){
                            $item->eqTypeName = $eqTypeName->type_name;
                        }
                    }
                }
                if(!empty($item->status_id)){
                    $item->current_status = MasterData::find($item->status_id)->Name;
                }
                if(!empty($item->old_status_id)){
                    $item->old_status = MasterData::find($item->old_status_id)->Name;
                }
            }
            array_push($array, $list);
        }
        $arraydata=array();
        foreach ($array as $key => $value) {
            foreach ($value as $row) {
                array_push($arraydata, $row);
            }
        }
        $this->data['list'] = $arraydata;
        if(count($this->data['list']) > 0){
            return Excel::download(new EqExportHistories($this->data), 'Ban_giao_thiet_bi.xlsx');
        }else{
            return $this->jsonErrors(['Không có dữ liệu!']);
        }
    }

    //API
    /**
     * @param Request $request
     * @return View (daily-report)
     * @throws AuthorizationException
     * Get data Equipment and return view
     */
    public function indexApi(Request $request,$orderBy = 'id', $sortBy = 'desc') {
        // $this->authorize('view', $this->menu);
        $recordPerPage = $this->getRecordPage();
        //list
        if(Schema::hasColumn('equipment_using_histories',$orderBy)){
            $list = EquipmentUsingHistory::orderBy('equipment_using_histories.'.$orderBy, $sortBy);
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
            ->join('equipment', 'equipment.room_id', 'rooms.id')
            ->get();

        $this->data['owners'] = User::query()
            ->select('users.id', 'users.FullName')
            ->join('equipment_using_histories', 'equipment_using_histories.user_owner', 'users.id')
            ->groupBy('users.id')
            ->orderBy('username')
            ->get();

        $this->data['created_users'] = DB::table('users')
            ->select('users.id', 'users.FullName')
            ->join('equipment_using_histories', 'equipment_using_histories.created_user', 'users.id')
            ->groupBy('users.id')
            ->orderBy('username')
            ->get();

        //tim kiem theo column
        $this->data['request'] = $request->query();
        $one = EquipmentUsingHistory::query()->select('equipment.code','equipment.name','equipment.info',
            'equipment_types.type_name','us1.FullName as CreatedUser','us2.FullName as OldUserOwner','us3.FullName as FullName')
        ->leftJoin('equipment','equipment.code','=','equipment_using_histories.code')
        ->leftJoin('users as us2','us2.id','=','equipment_using_histories.user_owner')
        ->leftJoin('users as us1','us1.id','=','equipment_using_histories.created_user')
        ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
        ->leftJoin('users as us3','us3.id','=','equipment_using_histories.old_user_owner')
        ->first();
        if($one){
            $one = $one->toArray();
            if(array_key_exists('search', $request->input())){
                if(null !== $request->input('search')){
                $list = $list->select('equipment_using_histories.id as idhq','equipment_using_histories.code','equipment_using_histories.user_owner','equipment_using_histories.created_user','equipment_using_histories.old_user_owner','equipment_using_histories.status_id','equipment.code','equipment.name','equipment.type_id','equipment_using_histories.deal_date','equipment_using_histories.old_status_id')
                            ->leftJoin('equipment','equipment.code','=','equipment_using_histories.code')
                            ->leftJoin('users as us2','us2.id','=','equipment_using_histories.user_owner')
                            ->leftJoin('users as us1','us1.id','=','equipment_using_histories.created_user')
                            ->leftJoin('users as us3','us3.id','=','equipment_using_histories.old_user_owner')
                            ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
                ->where(function ($query) use ($one, $request){
                    foreach($one as $key=>$value){
                        if($key == 'FullName') {
                            $query
                            ->orWhere('us3.'.$key, 'like', '%'.$request->input('search').'%');
                        }
                        else if($key == 'type_name') {
                            $query->orWhere('equipment_types.'.$key, 'like', '%'.$request->input('search').'%');
                        }else if($key == 'OldUserOwner'){
                            $query->orWhere('us2.FullName', 'like', '%'.$request->input('search').'%');
                        }
                        else if($key == 'CreatedUser'){
                            $query->orWhere('us1.FullName', 'like', '%'.$request->input('search').'%');
                        }
                        else{
                            if(in_array($key, ['deal_date' ])){
                            $query->orWhereRaw('(DATE_FORMAT(equipment_using_histories.deal_date,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );

                            }else{
                                $query->orWhere('equipment.'.$key, 'like', '%'.$request->input('search').'%');
                            }
                        }
                        $query->orWhereRaw('(DATE_FORMAT(equipment_using_histories.deal_date,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                    }
                });
                }
            }
        }

        //tim kiếm
        foreach($this->data['request'] as $key => $value){
            if(!is_array($value)){
                if($key == 'type_id'){
                    if(Schema::hasColumn('equipment', $key) && $value !== null){
                        if(null === $request->input('search')){
                            $list = $list->join('equipment','equipment.code','=','equipment_using_histories.code')
                                ->join('equipment_types','equipment_types.type_id','=','equipment.type_id');
                        }
                        $list = $list->where('equipment.type_id',$value);
                    }
                }
                if($key == 'created_user' || $key == 'user_owner'){
                    if(Schema::hasColumn('equipment_using_histories', $key) && $value !== null){
                        $list = $list->where('equipment_using_histories.'.$key, $value);
                    }
                }else if(Schema::hasColumn('equipment_using_histories', $key) && $value !== null){
                    $list = $list->where('equipment_using_histories.'.$key, 'like', '%'.$value.'%');
                }
            }else{
                if($value[0] !== null && $value[1] !== null && \DateTime::createFromFormat('d/m/Y', $value[0]) !== FALSE && \DateTime::createFromFormat('d/m/Y', $value[1]) !== FALSE){
                       $list = $list->where('equipment_using_histories.deal_date', '>=', $this->fncDateTimeConvertFomat($value[0], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))
                        ->where('equipment_using_histories.deal_date', '<=', $this->fncDateTimeConvertFomat($value[1], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                }
                else if($value[0] !== null && $value[1] == null && \DateTime::createFromFormat('d/m/Y', $value[0]) !== FALSE){
                    $list = $list->where('equipment_using_histories.deal_date', '>=', $this->fncDateTimeConvertFomat($value[0], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                }
                 else if($value[0] == null && $value[1] !== null && \DateTime::createFromFormat('d/m/Y', $value[1]) !== FALSE){
                    $list = $list->where('equipment_using_histories.deal_date', '<=', $this->fncDateTimeConvertFomat($value[1], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                }
                 else if(($value[0] !== null || $value[1] !== null)&& \DateTime::createFromFormat('d/m/Y', $value[0]) === FALSE && \DateTime::createFromFormat('d/m/Y', $value[1]) === FALSE){
                    // $list = $list->where('equipment_using_histories.deal_date', '=', $this->fncDateTimeConvertFomat('30/02/2020', self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                    return Redirect::back()->withErrors(['Ngày tìm kiếm không hợp lệ']);
                }
            }

        }

        //phan trang
        $list = $list->paginate($recordPerPage);
        foreach($list as $item){
            if(!empty($item->status_id)){
                $item->status = MasterData::find($item->status_id)->Name;
            }
            
            if($item->user_owner){
                $ownerName = User::query()->withTrashed()
                    ->where('id', $item->user_owner)          
                    ->first();
                if(!empty($ownerName)){
                    $item->ownerName = $ownerName->FullName;
                }
            }else{
                $item->ownerName = "Kho";
            }
            if($item->old_user_owner){
                $oldOwnerNames = User::find($item->old_user_owner);
                if(!empty($oldOwnerNames)){
                    $item->oldOwnerName = $oldOwnerNames->FullName;
                }
            }else{
                $item->oldOwnerName = "Kho";
            }
            if($item->room_id){
                $item->room = Room::find($item->room_id)->Name;
            }
            if($item->created_user){
                $created_user_name = User::query()->withTrashed()
                    ->where('id', $item->created_user)          
                    ->first();
                if(!empty($created_user_name)){
                    $item->created_user_name = $created_user_name->FullName;
                }
            }
            $eq = Equipment::query()->withTrashed()
                ->where('code', $item->code)
                ->first();
            if($eq){
                $item->eqName = $eq->name;
                if(!empty($eq->type_id)){
                    $eqTypeName = EquipmentType::query()->withTrashed()
                    ->where('type_id', $eq->type_id)
                    ->first();
                    if(!empty($eqTypeName)){
                        $item->eqTypeName = $eqTypeName->type_name;
                    }
                }
            }
            if(!empty($item->status_id)){
                $item->current_status = MasterData::find($item->status_id)->Name;
            }
            if(!empty($item->old_status_id)){
                $item->old_status = MasterData::find($item->old_status_id)->Name;
            }
        }
        $this->data['list'] = $list;
        $query_string = str_replace($request->url(), '', $request->fullUrl());

        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
        parse_str(str_replace('?', '', $query_string), $query_array);
        $this->data['query_array'] = $query_array;

        $this->data['sort_link'] = $sort_link;
        $data = $this->data;
        $data['role_key'] = 'EquipmentRotation';
        return AdminController::responseApi(200, null, null, $data);    
    }
    /**
     * Process insert multiple records
     * @param Request $request
     * @return string|void
     */
    public function storeApi(Request $request){
         try{
            if(count($request->input()) >0){
                $validator = Validator::make($request->all(),
                    [
                        'type_id'   =>  'string|required',
                        'name' =>  'string|required',
                        'serial_number' =>  'string|nullable',
                        'info' =>  'string|nullable',
                        'provider' =>  'string|nullable',
                        'buy_date' =>  'date_format:d/m/Y h:i|nullable',
                        'period_date' =>  'numeric|min:0|nullable',
                        'deal_date' =>  'date_format:d/m/Y|nullable',
                        'unit_price' =>  'numeric|nullable',
                        'note' =>  'string|nullable',
                        'updated_user' =>  'integer|min:1|nullable',
                        'status_id' =>  'integer|min:1|nullable',
                        'user_owner' =>  'integer|min:1|nullable',
                        'register_id' =>  'integer|min:1|nullable',
                        'room_id' =>  'integer|min:1|nullable',
                    ]);
                if ($validator->fails())
                {
                    return AdminController::responseApi(422, $validator->errors()->first());
                }

                $validated = $validator->validate();

                $one = new Equipment();
                foreach($validated as $key => $value){
                    if(Schema::hasColumn('equipment', $key))
                        $one->$key = $value;
                }

                if(isset($validated['period_date'])){
                    $one->period_date = Carbon::parse($validated['buy_date'])->addMonth($validated['period_date']);
                }
                if(isset($validated['deal_date'])){
                    $one->deal_date = $this->fncDateTimeConvertFomat($validated['deal_date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                } 

                $equipmentSame = Equipment::query()
                    ->where('type_id', $validated['type_id'])
                    ->orderBy('id', 'desc')
                    ->first();
                if($equipmentSame){
                    $one->code = $this->getEquipmentCode($equipmentSame->code);
                }else{
                    $one->code = $validated['type_id']."0001";
                }
                $one->updated_user = Auth::user()->id;
                $save =$one->save();
                if (!$save) {
                    return AdminController::responseApi(403, __('admin.error.save'));
                } else {
                    return AdminController::responseApi(200, null, __('admin.success.save'));
                }

            }else{
                return AdminController::responseApi(422, __('admin.error.data'));
            }
        }
        catch (\Exception $e){
             return AdminController::responseApi(422, $e->getMessage());
        }
    }
    /**
     * Show popup insert,update can insert multiple records
     * @param Request $request
     * @param null $oneId
     * @param null $del
     * @return View (equipment-history-detail)
     */
    public function showDetailApi(Request $request, $oneId=null){
        if($request->exists('copy')){
            $this->data['copy'] = 1;
        }
        $this->data['types'] = EquipmentType::all();
        $this->data['status_list'] = MasterData::query()
            ->where('datakey', 'TB')
            ->get();
        $this->data['owners'] = DB::table('users')
            ->join('equipment', 'equipment.user_owner', 'users.id')
            ->select('users.*')
            ->groupBy('users.id')
            //Tien 1/4/2020
            ->where('equipment.deleted_at', null)
            //Tien 1/4/2020
            ->orderBy('username')
            ->get();
        $this->data['receive_owners'] = User::query()
            ->where('Active', 1)
            ->where('id','!=', 1)
            ->orderBy('username')
            ->get();
        if($oneId!=null){
            $this->data['itemInfo'] = Equipment::find($oneId);
            $this->data['registered_room'] = DB::table('equipment')
                ->join('users', 'users.id', 'equipment.user_owner')
                ->join('rooms', 'users.RoomId', 'rooms.id')
                ->select('rooms.*')
                ->where('equipment.user_owner', $this->data['itemInfo']->user_owner)
                ->first();
//            print_r($this->data['registered_room']);
            if($this->data['registered_room']){

                $this->data['list_users'] = User::query()
                    ->where('RoomId', $this->data['registered_room']->id)
                    ->get();
            }else{
                $this->data['list_users'] = new \stdClass();
            }
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

    public function getEquipmentList(Request $request) {
        $validator = Validator::make($request->input(), [
            'eqType' => 'array|nullable',
            'eqOwner' => 'integer|required'
        ]);

        if($validator->fails())
           return AdminController::responseApi(422, $validator->errors()->first());
        $validated = $validator->validated();

        $eqList = Equipment::query();
        if(isset($validated['eqType']) && count($validated['eqType'])) {
            $eqList = $eqList->where(function($query) use ($validated) {
                foreach ($validated['eqType'] as $item) {
                    $query->orWhere('type_id', $item);
                }
            });
        }

        $eqList = $eqList->where('user_owner', $validated['eqOwner']);

        $data = $eqList->get()->toArray();
        return AdminController::responseApi(200, null, null, $data);
    }
    
}
