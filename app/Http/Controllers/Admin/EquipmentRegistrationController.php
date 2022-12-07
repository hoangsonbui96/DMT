<?php

namespace App\Http\Controllers\Admin;

use App\Equipment;
use App\EquipmentRegistration;
use App\EquipmentRegistrationForm;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

/**
 * Class EquipmentRegistrationController
 * @package App\Http\Controllers\Admin
 * Controller screen Equipment Registration
 */
class EquipmentRegistrationController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $approve;
    const KEYMENU= array(
        "add" => "EquipmentRegistrationsAdd",
        "view" => "EquipmentRegistrations",
        "edit" => "EquipmentRegistrationsEdit",
        "delete" => "EquipmentRegistrationsDelete",
        "approve" => "EquipmentRegistrationsApprove",
    );
    /**
     * EquipmentRegistrationController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
        $array = $this->RoleView('EquipmentRegistrations',['EquipmentRegistrations']);
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
     * @return View (equipment-registrations)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * Get data EquipmentRegistrationForm and return view
     */
    public function show(Request $request, $orderBy = 'status', $sortBy = 'asc'){

        $recordPerPage = $this->getRecordPage();
        $this->authorize('view', $this->menu);
        //list
        if(Schema::hasColumn('equipment_registration_forms',$orderBy)){
            $list = EquipmentRegistrationForm::orderBy($orderBy, $sortBy)
            ->orderBy('updated_at', 'desc');
        }
        else
        {
            return redirect()->back();
        }
        //loai thiet bi

        $this->data['registerUsers'] = DB::table('users')
            ->join('equipment_registration_forms', 'equipment_registration_forms.user_id', 'users.id')
            ->select('users.*')
            ->groupBy('equipment_registration_forms.user_id')
            ->get();

        // $list = $list->select('equipment_registration_forms.id as idq','equipment_registration_forms.requests','equipment_registration_forms.rejected_requests','equipment_registration_forms.processed_requests','users.FullName','equipment_registration_forms.updated_at','equipment_registration_forms.created_at','equipment_registration_forms.user_id','equipment_registration_forms.status','users.id')->join('users','users.id','=','equipment_registration_forms.user_id');
        
        //tim kiem theo column
        $this->data['request'] = $request->query();
        $one = EquipmentRegistrationForm::query()->select('equipment_registration_forms.id','equipment_registration_forms.requests','equipment_registration_forms.rejected_requests','equipment_registration_forms.processed_requests','users.FullName')
            ->leftJoin('users','users.id','=','equipment_registration_forms.user_id')->first();

        if($one){
            $one = $one->toArray();
            if(array_key_exists('search', $request->input())){
                if(null !== $request->input('search')){
                $list = $list->select('equipment_registration_forms.id as idq','equipment_registration_forms.requests','equipment_registration_forms.rejected_requests','equipment_registration_forms.processed_requests','users.FullName','equipment_registration_forms.updated_at','equipment_registration_forms.created_at','equipment_registration_forms.user_id','equipment_registration_forms.status','users.id')
                    ->join('users','users.id','=','equipment_registration_forms.user_id')
                    ->where(function ($query) use ($one, $request){
                    foreach($one as $key=>$value){
                        if($key == 'FullName') {
                            $query->orWhere('users.'.$key, 'like', '%'.$request->input('search').'%');
                        }else if($key == 'id') {
                            $query->orWhere('equipment_registration_forms.'.$key, 'like', '%'.$request->input('search').'%');
                        }
                        else{
                            if(in_array($key,['created_at', 'updated_at'])){
                                 $query->orWhereRaw('(DATE_FORMAT(equipment_registration_forms.'.$key.',"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                            }else{

                            $query->orWhere('equipment_registration_forms.'.$key, 'like', '%'.$request->input('search').'%');
                            }
                        }
                    }
                    $query->orWhereRaw('(DATE_FORMAT(equipment_registration_forms.created_at,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                    $query->orWhereRaw('(DATE_FORMAT(equipment_registration_forms.updated_at,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                });
                }
            }
        }

        //tim kiếm
        foreach($this->data['request'] as $key => $value){
            if(!is_array($value)){
                if($key == 'form_status') $key = 'status';
                if(Schema::hasColumn('equipment_registration_forms', $key) && $value !== null){
                    $list = $list->where($key, 'like', '%'.$value.'%');
                }
            }else{
                if($value[0] !== null && $value[1] !== null)
                    $list = $list->where('deal_date', '>=', Carbon::parse($value[0])->startOfDay())
                        ->where('deal_date', '<=', Carbon::parse($value[1])->endOfDay());
            }

        }

        $user = User::find(Auth::user()->id);
        if ($user->cant('admin', $this->menu)) {
            $list = $list->where('user_id', Auth::user()->id);
        }
        
        //phan trang
        $list = $list->paginate($recordPerPage);
        foreach($list as $item){
            //Tien 1/4/2020
            $item->user_id = User::query()->withTrashed()
                    ->where('id', $item->user_id)          
                    ->first()->FullName;
            //Tien 1/4/2020
        }
        $this->data['list'] = $list;
        $query_string = str_replace($request->url(), '', $request->fullUrl());

        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
        parse_str(str_replace('?', '', $query_string), $query_array);
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
        $this->data['approve'] = $this->approve;
        return $this->viewAdminLayout('equipment-registrations', $this->data);
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
                if(array_key_exists('form_id', $request->input())){
                    $validator = Validator::make($request->all(),
                        [
                            'form_id'   =>  'required|integer',
                            'changeType'   =>  'array|required',
                            'changeType.*'   =>  'required|integer|min:1|max:2',
                            'eqType'   =>  'array|required',
                            'eqType.*'   =>  'required|string|min:3|max:3',
                            'eq'    =>  'array|required',
                            'eq.*'  =>  'string|nullable',
                            'total'    =>  'array|required',
                            'total.*'  =>  'integer|min:1',
                            'note'    =>  'array|required',
                            'note.*'  =>  'required|string',
                            'id'    =>  'required|array',
                            'id.*'    =>  'integer|nullable',


                        ]);
                }else{
                    $validator = Validator::make($request->all(),
                        [
                            'changeType'   =>  'array|required',
                            'changeType.*'   =>  'required|integer|min:1|max:2',
                            'eqType'   =>  'array|required',
                            'eqType.*'   =>  'required|string|min:3|max:3',
                            'eq'    =>  'array|required',
                            'eq.*'  =>  'string|nullable',
                            'total'    =>  'array|required',
                            'total.*'  =>  'integer|min:1',
                            'note'    =>  'array|required',
                            'note.*'  =>  'required|string',
                            'id'    =>  'required|array',
                            'id.*'    =>  'integer|nullable',

                        ]);
                }

                if ($validator->fails())
                {
                    return response()->json(['errors'=>$validator->errors()->all()]);
                }

                $validated = $validator->validate();
                if(array_key_exists('form_id', $validated)){
                    // $this->authorize('update', $this->menu);
                    //cập nhật danh sách đăng ký
                    $form = EquipmentRegistrationForm::find($validated['form_id']);
                    //xoa đơn
                    $registrationId = EquipmentRegistration::query()
                        ->where('form_id', $form->id)
                        ->select('id')
                        ->get();
                    foreach($registrationId as $value){

                        if(!in_array($value->id, $validated['id'])){

                            EquipmentRegistration::find($value->id)->delete();
                        }
                    }
                    if(!$form)
                        exit();


                }else{
                    // $this->authorize('create', $this->menu);
                    $form = new EquipmentRegistrationForm();


                }
                // $form->requests = count($validated['id']);
                $total=0;
                foreach($validated['id'] as $key => $value){
                    $total=$total+$validated['total'][$key];

                }
                $form->requests = $total;
                $form->user_id = Auth::user()->id;
                $form->updated_at = Carbon::now();
                $ckecknumenote = 0;
                foreach($validated['note'] as $key => $value){
                     $check1 = preg_match('/[(&|@|!|#|^|$|%|*|+|=)]/', $validated['note'][$key]);
                    if($check1 == 1){
                        $ckecknumenote = $ckecknumenote+1;
                    }
                }
                if($ckecknumenote > 0){
                    return $this->jsonErrors('Lý do không thể có kí tự đặc biệt!');
                }
                $form->save();

                // Tien 6/4/2020
                $array = array();
                // Tien 6/4/2020

                foreach($validated['id'] as $key => $value){

                    //check type exist

                    $checkType = EquipmentType::query()
                        ->where('type_id', $validated['eqType'][$key])
                        ->first();
                    if(empty($checkType)){
                        exit();
                    }

                    if(is_null($value)){
                        $registration = new EquipmentRegistration();
                        $statusold = 0;
                    }else{
                        $registration = EquipmentRegistration::find($value);
                        if(empty($registration)){
                            exit();
                        }
                        $statusold = $registration->status;
                    }
                    if(isset($validated['note'][$key])){
                        $check1 = preg_match('/[(&|@|!|#|^|$|%|*|+|=)]/', $validated['note'][$key]);
                        if($check1 == 1){
                            return $this->jsonErrors('Lý do không thể có kí tự đặc biệt!');
                        }
                    }
                    // $registration->register_id = $register_id;
                    $registration->user_id = Auth::user()->id;
                    $registration->form_id = $form->id;
                    $registration->type_id = $validated['eqType'][$key];
                    $registration->change_id = $validated['changeType'][$key];
                    $registration->status = 0;
                    $registration->note = $validated['note'][$key];
                    $registration->total = $validated['total'][$key];
                    $registration->status = $statusold;
                    if($validated['changeType'][$key] == 2){
                        //doi thiet bi
                        $check = Equipment::query()
                            ->where('code', $validated['eq'][$key])
                            ->first();
                        if($check){
                            $registration->type_id = $check->type_id;
                            $registration->code = $validated['eq'][$key];//thiet bi can doi
                            //check don dang ky cho thiet bi can doi da ton tai chua
                            $query = EquipmentRegistration::query()
                                ->where('code', $validated['eq'][$key])
                                ->where('status', 0)
                                ->first();
                            if(!empty($query)){
                                exit();
                            }
                        }
                        else{
                            exit();
                        }



                    }
                    // Tien 6/4/2020
                    if($validated['changeType'][$key] == 1){
                        $changeTypeE = 'Thêm thiết bị';
                        $codeE ='';
                    }else{
                        $changeTypeE = 'Đổi thiết bị';
                        $codeE = $check->name;
                    }
                    if(isset($validated['eqType'][$key])){
                        $eqTypeE = $checkType->type_name;
                    }
                    $array[$key] = array(
                                    'changeType' => $changeTypeE,
                                   'eqType' => $eqTypeE,
                                   'code' => $codeE,
                                   'total' => $validated['total'][$key],
                                   'note' => $validated['note'][$key],
                                  );
                    // Tien 6/4/2020

                    $registration->save();

                }
                // Tien 6/4/2020
                $this->sendMail(
                    $form->id,
                    Auth::user()->id,
                    $array,
                    'add'
                );
                // Tien 6/4/2020
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
     * get Register
     */
    public function getRegisterId(){
        $code = EquipmentRegistration::query()
            ->orderBy('id', 'desc')
            ->first();
        if(!$code){
            return "#1";
        }else{
            $old_id = $code->register_id;
            $code = str_replace('#', '', $old_id);
            $code++;
            return '#'.$code;
        }
    }

    /**
     * Show popup insert,update can insert multiple records
     * @param Request $request
     * @param null $oneId
     * @param null $del
     * @return View (equipment-registration)
     */
    public function showDetail(Request $request, $oneId=null, $del=null){

        $this->data['types'] = EquipmentType::all();
        $this->data['status_list'] = MasterData::query()
            ->where('datakey', 'TB')
            ->get();


        if($oneId!=null){
            if($del == 'del'){
                $one = EquipmentRegistrationForm::find($oneId);
                if($one){
                    EquipmentRegistration::query()
                        ->where('form_id', $oneId)
                        ->delete();
                    $one->delete();
                }   
                return 1;
            }

            $this->data['itemInfo'] = EquipmentRegistrationForm::find($oneId);

//            print_r($this->data['registered_room']);
            if($this->data['itemInfo']){
                $this->data['registrations'] = EquipmentRegistration::query()
                    ->where('form_id', $this->data['itemInfo']->id)
                    ->get();
                foreach($this->data['registrations'] as $item){
                    $type_name = EquipmentType::query()
                        ->withTrashed()
                        ->where('type_id', $item->type_id)
                        ->first();
                    if(!empty($type_name)){
                        $item->type_name=$type_name->type_name;
                    }
                    $item->eq_name = is_null($item->code) ? '' : Equipment::query()->where('code', $item->code)
                                                                            //Tien 1/4/2020
                                                                            ->withTrashed()
                                                                            //Tien 1/4/2020
                                                                            ->first()->name;
                }
                return $this->viewAdminIncludes('equipment-registration', $this->data);
            }else{
                return "";
            }
        }else{
            $this->data['registrations'] = new \stdClass();
            return $this->viewAdminIncludes('equipment-registration', $this->data);
        }

    }

    /**
     * Show screen  Approve Equipment
     * @param Request $request
     * @param $oneId
     * @return View (equipment-registration-approve)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function regApprove(Request $request, $oneId){
        $this->data['types'] = EquipmentType::all();
        $this->data['status_list'] = MasterData::query()
            ->where('datakey', 'TB')
            ->get();


        if($oneId!=null){
//            $this->data['id'] = $oneId;


            $this->data['itemInfo'] = EquipmentRegistrationForm::find($oneId);


//            print_r($this->data['registered_room']);
            if($this->data['itemInfo']){
                $this->data['registrations'] = EquipmentRegistration::query()
                    ->where('form_id', $this->data['itemInfo']->id)
                    ->where('change_id', 1)
                    ->get();

                foreach($this->data['registrations'] as $item){
                    $type_name = EquipmentType::query()->withTrashed()
                        ->where('type_id', $item->type_id)
                        ->first();
                    if(!empty($type_name)){
                        $item->type_name=$type_name->type_name;
                    }
                    $item->eq_name = is_null($item->code) ? '' : Equipment::query()->where('code', $item->code)->first()->name;
                    $item->approved = count(explode(',', $item->arr_code)) == 1 ? 0 : count(explode(',', $item->arr_code))-2;
                }

                $this->data['changeRegistrations'] = EquipmentRegistration::query()
                    ->where('form_id', $this->data['itemInfo']->id)
                    ->where('change_id', 2)
                    ->get();
                foreach($this->data['changeRegistrations'] as $item){
                    $item->type_name = EquipmentType::query()
                        //Tien 1/4/2020
                        ->withTrashed()
                        //Tien 1/4/2020
                        ->where('type_id', $item->type_id)
                        ->first()->type_name;

                    //Tien 1/4/2020
                    $item->eq_name = is_null($item->code) ? '' : Equipment::query()->withTrashed()->where('code', $item->code)->first()->name;
                     //Tien 1/4/2020

                    // $item->eq_name = is_null($item->code) ? '' : Equipment::query()->where('code', $item->code)->first()->name;
                    if(!is_null($item->arr_code)){
                        $newEquipment = Equipment::query()
                            ->where('code', explode(',', $item->arr_code)[1])
                            ->first();
                        if($newEquipment)
                        $item->newEq = $newEquipment;
                    }

                }
                return $this->viewAdminIncludes('equipment-registration-approve', $this->data);
            }else{
                return "";
            }
        }else{
            $this->data['registrations'] = new \stdClass();
            return$this->viewAdminIncludes('equipment-registration-approve', $this->data);
        }

//        return view('admin.includes.equipment-registration-approve', $this->data);
    }

    /**
     * Show screen  Approve Equipment Detail
     * @param Request $request
     * @param $oneId = 0
     * @param $reject = 0
     * @return View (equipment-approve-detail)
     */
    public function regApproveDetail(Request $request, $oneId = 0, $reject = 0){

            $this->data['itemInfo'] = EquipmentRegistration::find($oneId);
            if($this->data['itemInfo']){

                $one = EquipmentType::query()
                    //Tien 1/4/2020
                    ->withTrashed()
                    //Tien 1/4/2020
                    ->where('type_id', $this->data['itemInfo']->type_id)
                    ->first();
                if(!empty($one->type_name)){
                    $this->data['itemInfo']->type_name = $one->type_name;
                }
                
                //Tien 1/4/2020
                $oneUser = User::withTrashed()->find($this->data['itemInfo']->user_id);
                //Tien 1/4/2020

                $this->data['itemInfo']->user_id = $oneUser->FullName;
                if(!empty($one->type_id)){
                $this->data['itemSources'] = DB::table('users')
                    ->join('equipment', 'equipment.user_owner', 'users.id')
                    ->select('users.FullName', 'users.id')
                    ->where('equipment.type_id', $one->type_id)
                    ->where('equipment.user_owner', '<>', $oneUser->id)
                    ->groupBy('users.id')
                    ->get();
                $this->data['store'] = Equipment::query()
                    ->where('type_id', $one->type_id)
                    ->where('user_owner', 0)
                    ->get();
                }else{
                     $this->data['itemSources'] =[];
                     $this->data['store'] = [];
                }
                if(!is_null($this->data['itemInfo']->code)){
                    //Tien 1/4/2020 
                    $this->data['itemInfo']->eq = Equipment::query()->withTrashed()
                        ->where('code', $this->data['itemInfo']->code)
                        ->first();
                    //Tien 1/4/2020
                    $this->data['itemInfo']->eq_status = MasterData::find($this->data['itemInfo']->eq->status_id);
                    $this->data['itemInfo']->newOwners = User::query()
                        ->where('id', '<>', $this->data['itemInfo']->eq->user_owner)
                        ->where('Active', 1)
                        ->get();

                    $newSource = EquipmentUsingHistory::query()
                        ->where('register_id', $oneId)
                        ->where('code', $this->data['itemInfo']->code)
                        ->first();
                    if($newSource){
                        $this->data['itemInfo']->newSource = $newSource->user_owner;
                    }

                }
                $arr = explode(",", $this->data['itemInfo']->arr_code);
                $query = Equipment::query();
                foreach($arr as $item){
                    $query = $query->orWhere('code', $item);
                }

                $this->data['processedEq'] = $query->get();
                foreach($this->data['processedEq'] as $item){
                    $owner = User::find($item->user_owner);
                    if($owner){
                        $item->source = $owner->FullName;
                    }
                    else{
                        $item->source = 'Kho';
                    }
                    $oldOwner = EquipmentUsingHistory::query()->select('users.FullName')
                    ->leftjoin('users', 'equipment_using_histories.old_user_owner', 'users.id')
                    ->where('equipment_using_histories.user_owner', $item->user_owner)
                    ->where('equipment_using_histories.updated_at', $item->updated_at)
                    ->get();
                    if($oldOwner){
                        $item->oldOwner = null==$oldOwner[0]['FullName']?'Kho':$oldOwner[0]['FullName'];
                    }
                }
                $reg = EquipmentRegistration::query()
                    ->leftjoin('equipment_registration_forms', 'equipment_registration_forms.id', 'equipment_registrations.form_id')
                    ->where('equipment_registrations.id', $oneId)
                    ->where('equipment_registrations.status', 0)
                    ->first();
                if(!$reg || $reg->processing_requests == 1){
                    if(isset($item->user_owner)){
                        if(isset($reg->processing_requests)){
                            $this->data['reg'] = 0; 
                        }else{
                            $this->data['reg'] = 1; 
                        }
                    }else{
                        $this->data['reg'] = 0; 
                    }
                }else{
                    $this->data['reg'] = 0;
                }
                return $this->viewAdminIncludes('equipment-approve-detail', $this->data);
            }else{
                return abort('404');
            }

    }

    /**
     * Process insert multiple records,update one records
     * @param Request $request
     * @return string|void
     */
    public function regApproveDetailStore(Request $request){
        // return $request->input();
        //
        //cap nhat chi tiet dang ky
        //
        $reg = EquipmentRegistration::query()
            ->where('id', $request->input('reg'))
            ->where('status', 0)
            ->first();
        if(!$reg)
        return "Đơn không tồn tại hoặc đã được duyệt!";
        $eqApprovedArr = explode(',', $reg->arr_code);
        // return count($eqApprovedArr);
        //check equipment co hop le hay khong
        $check = true;
        $count = 0;
        if(null == $request->input('eq1')){
             return  $this->jsonErrors('Bạn chưa chọn thiết bị!');
        }
        foreach($request->input('eq1') as $item){
            if(!in_array($item, $eqApprovedArr)){
                $count++;
                $eq = Equipment::query()
                    ->where('code', $item)
                    ->where('type_id', $reg->type_id)
                    ->where('status_id', 15)
                    ->where('user_owner', '<>', $reg->user_id)
                    ->first();
                if(!$eq){
                    $check = false;
                    // break;
                }
            }

        }
        if($count > $reg->total - (count($eqApprovedArr) == 1 ? count($eqApprovedArr) - 1 : count($eqApprovedArr) - 2 ))
            return "Số lượng thiết bị nhập vào lớn hơn yêu cầu!";
        if(!$check) exit();
        //check so luong equipment
        // if(count($request->input('eq1')) > $reg->total)
        // exit();
        $reg->approved_user = Auth::user()->id;
        $reg->approved_date = Carbon::now();
        $reg->arr_code = ",".implode(',', $request->input('eq1')).",";
        if(count($request->input('eq1')) == $reg->total){
            $reg->status = 1;
        }
        $reg->save();

        //luan chuyen thiet bi cu
        if($reg->change_id == 2){

            $oldEquipment = Equipment::query()
                ->where('code', $reg->code)
                ->first();
            $newOwner = User::find($request->input('new_source'));
            if($newOwner){
                $newOwnerId = $newOwner->id;

            }else{
                $newOwnerId = 0;
            }

            //lich su luan chuyen

            $oldEquipHistory = new EquipmentUsingHistory();
            $oldEquipHistory->user_owner = $newOwnerId;
            $oldEquipHistory->old_user_owner = $oldEquipment->user_owner;
            $oldEquipHistory->deal_date = Carbon::now();
            $oldEquipHistory->created_user = Auth::user()->id;
            $oldEquipHistory->status_id = $oldEquipment->status_id;
            $oldEquipHistory->old_status_id = $oldEquipment->status_id;
            $oldEquipHistory->register_id = $reg->id;
            $oldEquipHistory->code = $oldEquipment->code;
            $oldEquipHistory->save();

            $oldEquipment->user_owner = $newOwnerId;
            $oldEquipment->save();
        }
        //
        //Cập nhật đơn (nhiều đăng ký)
        //
        $this->updateRegFormStatus($reg->form_id,'Approve',$reg->id);
        //
        //Luân chuyển thiết bị
        //

        foreach($request->input('eq1') as $item){
            if(!in_array($item, $eqApprovedArr)){
                $eq = Equipment::query()
                    ->where('code', $item)
                    ->where('type_id', $reg->type_id)
                    ->where('status_id', 15)
                    ->first();

                //cap nhat lich su thiet bi
                $eqHistory = new EquipmentUsingHistory();
                $eqHistory->code = $eq->code;
                $eqHistory->user_owner = $reg->user_id;
                $eqHistory->old_user_owner = $eq->user_owner;
                $eqHistory->created_user = Auth::user()->id;
                $eqHistory->register_id = $reg->id;
                $eqHistory->status_id = $eq->status_id;
                $eqHistory->old_status_id = $eq->status_id;
                $eqHistory->deal_date = Carbon::now();
                $eqHistory->save();

                //cap nhat thiet bi
                $eq->user_owner = $reg->user_id;
                $eq->save();
            }

        }
        // Tien 6/4/2020
        $checkType = EquipmentType::query()
                    ->where('type_id', $reg->type_id)
                    ->first();
        $check = Equipment::query()
                ->where('code', $reg->code)
                ->first();
        $a = explode(',', $reg->arr_code);
        $arr_codeN ='';
        foreach ($a as $value) {
            if($value){
                $check1 = Equipment::query()
                ->where('code', $value)
                ->first();
            $arr_codeN = $arr_codeN .','. $check1->name;
            }
        }
        if($reg->change_id == 1){
            $changeTypeE = 'Thêm thiết bị';
            $codeE ='';
        }else{
            $changeTypeE = 'Đổi thiết bị';
            $codeE = $check->name;
        }
        
        $form_id     = $reg->form_id;
        $changeTypeE = $changeTypeE;
        $eqTypeE     = $checkType->type_name;
        $arr_codeN   = $arr_codeN;
        $totalE      = $reg->total;
        $noteE       = $reg->note;
        $user_idE    = $reg->user_id;
        
        $array[1] = array(
            'changeType' => $changeTypeE,
            'eqType' => $eqTypeE,
            'code' => $codeE,
            'arr_codeN' => $arr_codeN,
            'total' => $totalE,
            'note' => $noteE,
          );
        
        $this->sendMail(
            $form_id,
            $user_idE,
            $array,
            'Approve'
        );
        // Tien 6/4/2020
        return 1;
    }

    /**
     * Process insert multiple records,update one records
     * @param Request $request
     * @return string|void
     */
    public function regApproveReject(Request $request){
        // return $request->input();
        $reg = EquipmentRegistration::find($request->input('id'));
        if(!$reg) return("Đơn không tồn tại");
        if(is_null($reg->arr_code)){
            $reg->status = 2;
        }else{
            $reg->status = 1;
        }

        $reg->reject_note = $request->input('note');
        $reg->approved_user = Auth::user()->id;
        $reg->approved_date = Carbon::now();
        $reg->save();
        $this->updateRegFormStatus($reg->form_id,'Reject',$reg->id);
        // Tien 6/4/2020
        $checkType = EquipmentType::query()
                    ->where('type_id', $reg->type_id)
                    ->first();
        $check = Equipment::query()
                ->where('code', $reg->code)
                ->first();
        if($reg->change_id == 1){
            $changeTypeE = 'Thêm thiết bị';
            $codeE ='';
        }else{
            $changeTypeE = 'Đổi thiết bị';
            $codeE = $check->name;
        }
        $form_id     = $reg->form_id;
        $changeTypeE = $changeTypeE;
        $eqTypeE     = $checkType->type_name;
        $totalE      = $reg->total;
        $noteE       = $reg->note;
        $user_idE    = $reg->user_id;
        
        $array[1] = array(
            'changeType' => $changeTypeE,
            'eqType' => $eqTypeE,
            'code' => $codeE,
            'total' => $totalE,
            'note' => $noteE,
          );
        
        $this->sendMail(
            $form_id,
            $user_idE,
            $array,
            'Reject'
        );
        // Tien 6/4/2020
        return 1;

    }

    //cập nhật đơn đăng ký(số lượng đơn đang xử lý, đã xử lý, đã từ chối, tình trạng đơn đã hoàn thành hay chưa)
    /**
     * Process update one records in registration form
     * @param Request $request
     * @param null $id
     * @return string|void
     */
    public function updateRegFormStatus($regFormId,$requests,$regId)
    {
        $regForm = EquipmentRegistrationForm::find($regFormId);
        $regForm->processing_requests = EquipmentRegistration::query()
            ->where('status', 0)
            ->where('form_id', $regFormId)
            ->whereNotNull('approved_user')
            ->get()
            ->count();

        $regs = EquipmentRegistration::query()->where('form_id', $regFormId)->where('id', $regId)->get();
        // start Tien 2/6/2020
        if($requests == 'Approve'){
            // $regForm->processed_requests = EquipmentRegistration::query()
            // ->where('status', $number)
            // ->where('form_id', $regFormId)
            // ->get()
            // ->count();
            $number = 0;
            foreach ($regs as $key => $value) {
                $number = $number+(count(explode(',', $value->arr_code)) == 1 ? 0 : count(explode(',', $value->arr_code))-2);
            }
            $regForm->processed_requests = $regForm->processed_requests + $number;
        }
        if($requests == 'Reject'){
        // $regForm->rejected_requests = EquipmentRegistration::query()
        //     ->where('status', $number)
        //     ->where('form_id', $regFormId)
        //     ->get()
        //     ->count();
            $number = 0;
            foreach ($regs as $key => $value) {
                $number = $number+($value->total-(count(explode(',', $value->arr_code)) == 1 ? 0 : count(explode(',', $value->arr_code))-2));
            }
            $regForm->rejected_requests = $regForm->rejected_requests + $number;
        }
        // end Tien 2/6/2020
        if($regForm->requests == $regForm->processed_requests + $regForm->rejected_requests){
            $regForm->status = 1;
        }
        $regForm->save();
    }

    /**
     * Send data to mail serve
     * @param $id
     * @param $user_id
     * @param $array
     * @param $check_status
     */
    public function sendMail($id,$user_id,$array,$check_status){
        $mailUser = User::find($user_id);
        if($mailUser->Gender == 0){
            $GENDER = 'Ms.';
        }else if($mailUser->Gender == 1){
            $GENDER = 'Mrs.';
        }   
        if($check_status == 'Approve'){
            $header = '';
            $subjectMail = 'TB đã duyệt đơn đăng ký thêm/thay đổi thiết bị của bạn .';
            $arrMailAddressFrom = env('MAIL_USERNAME');
            $arrMailAddressTo = $mailUser->email;
            $arrMailCC = env('MAIL_USERNAME');
        }else if($check_status == 'Reject'){
            $header = '';
            $subjectMail = 'TB từ chối đơn đăng ký thêm/thay đổi thiết bị của bạn .';
            $arrMailAddressFrom = env('MAIL_USERNAME');
            $arrMailAddressTo = $mailUser->email;
            $arrMailCC = env('MAIL_USERNAME');
        }else{
            $header = 'Kính gửi Văn phòng';
            $subjectMail = 'TB xin đăng kí thêm/ thay đổi thiết bị.';
            $arrMailAddressTo = env('MAIL_USERNAME');
            $arrMailAddressFrom = $mailUser->email;
            $arrMailCC = $mailUser->email;
        }
        $contentMail = '';
        
        $Name = $mailUser->FullName;
        $viewBladeMail = 'template_mail.equipment-mail';
        $dataBinding = [
            'Header' => $header,
            'GENDER' => $GENDER,
            'Name' => $Name,
            'array' => $array,
            'id' => $id,
            'check_status'=>$check_status,
        ];
        $this->SendMailWithView([
            self::KEY_SUBJECT_MAIL => $subjectMail,
            self::KEY_VIEW_MAIL => $viewBladeMail,
            self::KEY_DATA_BINDING => $dataBinding,
            self::KEY_MAIL_NAME_FROM => $arrMailAddressFrom,
            self::KEY_MAIL_ADDRESS_TO => $arrMailAddressTo,
            self::KEY_MAIL_ADDRESS_CC => $arrMailCC,
        ]);
    }
    //API
    /**
     * @param Request $request
     * @return View (daily-report)
     * @throws AuthorizationException
     * Get data Equipment Registration and return view
     */
    public function indexApi(Request $request,$orderBy = 'id', $sortBy = 'desc') {
        $recordPerPage = $this->getRecordPage();
        // $this->authorize('action', $this->menu);
        //list
        if(Schema::hasColumn('equipment_registration_forms',$orderBy)){
            $list = EquipmentRegistrationForm::orderBy($orderBy, $sortBy)
            ->orderBy('updated_at', 'desc');
        }
        else
        {
            return redirect()->back();
        }
        //loai thiet bi

        $this->data['registerUsers'] = DB::table('users')
            ->join('equipment_registration_forms', 'equipment_registration_forms.user_id', 'users.id')
            ->select('users.*')
            ->groupBy('equipment_registration_forms.user_id')
            ->orderBy('username')
            ->get();

        //tim kiem theo column
        $this->data['request'] = $request->query();
        $one = EquipmentRegistrationForm::query()->select('equipment_registration_forms.id','equipment_registration_forms.requests','equipment_registration_forms.rejected_requests','equipment_registration_forms.processed_requests','users.FullName')
            ->leftJoin('users','users.id','=','equipment_registration_forms.user_id')->first();

        if($one){
            $one = $one->toArray();
            if(array_key_exists('search', $request->input())){
                if(null !== $request->input('search')){
                $list = $list->select('equipment_registration_forms.id as idq','equipment_registration_forms.requests','equipment_registration_forms.rejected_requests','equipment_registration_forms.processed_requests','users.FullName','equipment_registration_forms.updated_at','equipment_registration_forms.created_at','equipment_registration_forms.user_id','equipment_registration_forms.status','users.id')
                    ->join('users','users.id','=','equipment_registration_forms.user_id')
                    ->where(function ($query) use ($one, $request){
                    foreach($one as $key=>$value){
                        if($key == 'FullName') {
                            $query->orWhere('users.'.$key, 'like', '%'.$request->input('search').'%');
                        }else if($key == 'id') {
                            $query->orWhere('equipment_registration_forms.'.$key, 'like', '%'.$request->input('search').'%');
                        }
                        else{
                            if(in_array($key,['created_at', 'updated_at'])){
                                 $query->orWhereRaw('(DATE_FORMAT(equipment_registration_forms.'.$key.',"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                            }else{

                            $query->orWhere('equipment_registration_forms.'.$key, 'like', '%'.$request->input('search').'%');
                            }
                        }
                    }
                    $query->orWhereRaw('(DATE_FORMAT(equipment_registration_forms.created_at,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                    $query->orWhereRaw('(DATE_FORMAT(equipment_registration_forms.updated_at,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                });
                }
            }
        }

        //tim kiếm
        foreach($this->data['request'] as $key => $value){
            if(!is_array($value)){
                if($key == 'form_status') $key = 'status';
                if(Schema::hasColumn('equipment_registration_forms', $key) && $value !== null){
                    $list = $list->where($key, 'like', '%'.$value.'%');
                }
            }else{
                if($value[0] !== null && $value[1] !== null)
                    $list = $list->where('deal_date', '>=', Carbon::parse($value[0])->startOfDay())
                        ->where('deal_date', '<=', Carbon::parse($value[1])->endOfDay());
            }

        }

        $user = User::find(Auth::user()->id);
        if ($user->cant('admin', $this->menu)) {
            $list = $list->where('user_id', Auth::user()->id);
        }
        
        //phan trang
        $list = $list->paginate($recordPerPage);
        foreach($list as $item){
            //Tien 1/4/2020
            $item->user_id = User::query()->withTrashed()
                    ->where('id', $item->user_id)          
                    ->first()->FullName;
            //Tien 1/4/2020
        }
        $this->data['list'] = $list;
        $query_string = str_replace($request->url(), '', $request->fullUrl());

        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
        parse_str(str_replace('?', '', $query_string), $query_array);
        
        $this->data['query_array'] = $query_array;

        $this->data['sort_link'] = $sort_link;
        $data = $this->data;
        $data['role_key'] = 'EquipmentRegistrations';
        return AdminController::responseApi(200, null,null, $data);
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
                        'changeType'   =>  'array|required',
                        'changeType.*'   =>  'required|integer|min:1|max:2',
                        'eqType'   =>  'array|required',
                        'eqType.*'   =>  'required|string|min:3|max:3',
                        'eq'    =>  'array|required',
                        'eq.*'  =>  'string|nullable',
                        'total'    =>  'array|required',
                        'total.*'  =>  'integer|min:1',
                        'note'    =>  'array|required',
                        'note.*'  =>  'required|string',
                        'id'    =>  'required|array',
                        'id.*'    =>  'integer|nullable',

                    ]);

                if ($validator->fails())
                {
                    return AdminController::responseApi(422, $validator->errors()->first());
                }

                $validated = $validator->validate();
                $form = new EquipmentRegistrationForm();
                $total=0;
                foreach($validated['id'] as $key => $value){
                    $total=$total+$validated['total'][$key];

                }
                $form->requests = $total;
                $form->user_id = Auth::user()->id;
                $form->updated_at = Carbon::now();
                $ckecknumenote = 0;
                foreach($validated['note'] as $key => $value){
                     $check1 = preg_match('/[(&|@|!|#|^|$|%|*|+|=)]/', $validated['note'][$key]);
                    if($check1 == 1){
                        $ckecknumenote = $ckecknumenote+1;
                    }
                }
                if($ckecknumenote > 0){
                    return AdminController::responseApi(422, __('admin.error.equipment-registration.ckecknumenote'));
                }
                $form->save();

                // Tien 6/4/2020
                $array = array();
                // Tien 6/4/2020
                foreach($validated['id'] as $key => $value){

                    //check type exist

                    $checkType = EquipmentType::query()
                        ->where('type_id', $validated['eqType'][$key])
                        ->first();
                    if(empty($checkType)){
                        exit();
                    }

                    if(is_null($value)){
                        $registration = new EquipmentRegistration();
                        $statusold = 0;
                    }else{
                        $registration = EquipmentRegistration::find($value);
                        if(empty($registration)){
                            exit();
                        }
                        $statusold = $registration->status;
                    }
                    if(isset($validated['note'][$key])){
                        $check1 = preg_match('/[(&|@|!|#|^|$|%|*|+|=)]/', $validated['note'][$key]);
                        if($check1 == 1){
                            return AdminController::responseApi(422, __('admin.error.equipment-registration.ckecknumenote'));
                        }
                    }
                    // $registration->register_id = $register_id;
                    $registration->user_id = Auth::user()->id;
                    $registration->form_id = $form->id;
                    $registration->type_id = $validated['eqType'][$key];
                    $registration->change_id = $validated['changeType'][$key];
                    $registration->status = 0;
                    $registration->note = $validated['note'][$key];
                    $registration->total = $validated['total'][$key];
                    $registration->status = $statusold;
                    if($validated['changeType'][$key] == 2){
                        //doi thiet bi
                        $check = Equipment::query()
                            ->where('code', $validated['eq'][$key])
                            ->first();
                        if($check){
                            $registration->type_id = $check->type_id;
                            $registration->code = $validated['eq'][$key];//thiet bi can doi
                            //check don dang ky cho thiet bi can doi da ton tai chua
                            $query = EquipmentRegistration::query()
                                ->where('code', $validated['eq'][$key])
                                ->where('status', 0)
                                ->first();
                            if(!empty($query)){
                                exit();
                            }
                        }
                        else{
                            exit();
                        }
                    }
                    // Tien 6/4/2020
                    if($validated['changeType'][$key] == 1){
                        $changeTypeE = 'Thêm thiết bị';
                        $codeE ='';
                    }else{
                        $changeTypeE = 'Đổi thiết bị';
                        $codeE = $check->name;
                    }
                    if(isset($validated['eqType'][$key])){
                        $eqTypeE = $checkType->type_name;
                    }
                    $array[$key] = array(
                                    'changeType' => $changeTypeE,
                                   'eqType' => $eqTypeE,
                                   'code' => $codeE,
                                   'total' => $validated['total'][$key],
                                   'note' => $validated['note'][$key],
                                  );
                    // Tien 6/4/2020

                    $registration->save();
                }
                // Tien 6/4/2020
                $this->sendMail(
                    $form->id,
                    Auth::user()->id,
                    $array,
                    'add'
                );
                // Tien 6/4/2020
                return AdminController::responseApi(200, __('admin.success.save'));

            }else{
               return AdminController::responseApi(422, __('admin.error.data'));
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
        try{
            if(count($request->input()) >0){
                    $validator = Validator::make($request->all(),
                    [
                        'form_id'   =>  'required|integer',
                        'changeType'   =>  'array|required',
                        'changeType.*'   =>  'required|integer|min:1|max:2',
                        'eqType'   =>  'array|required',
                        'eqType.*'   =>  'required|string|min:3|max:3',
                        'eq'    =>  'array|required',
                        'eq.*'  =>  'string|nullable',
                        'total'    =>  'array|required',
                        'total.*'  =>  'integer|min:1',
                        'note'    =>  'array|required',
                        'note.*'  =>  'required|string',
                        'id'    =>  'required|array',
                        'id.*'    =>  'integer|nullable',
                    ]);
                
                if ($validator->fails())
                {
                    return AdminController::responseApi(422, $validator->errors()->first());
                }

                $validated = $validator->validate();
                //cập nhật danh sách đăng ký
                $form = EquipmentRegistrationForm::find($validated['form_id']);
                //xoa đơn
                $registrationId = EquipmentRegistration::query()
                    ->where('form_id', $form->id)
                    ->select('id')
                    ->get();
                foreach($registrationId as $value){

                    if(!in_array($value->id, $validated['id'])){

                        EquipmentRegistration::find($value->id)->delete();
                    }
                }
                if(!$form)
                    exit();

                $total=0;
                foreach($validated['id'] as $key => $value){
                    $total=$total+$validated['total'][$key];

                }
                $form->requests = $total;
                $form->user_id = Auth::user()->id;
                $form->updated_at = Carbon::now();
                $ckecknumenote = 0;
                foreach($validated['note'] as $key => $value){
                     $check1 = preg_match('/[(&|@|!|#|^|$|%|*|+|=)]/', $validated['note'][$key]);
                    if($check1 == 1){
                        $ckecknumenote = $ckecknumenote+1;
                    }
                }
                if($ckecknumenote > 0){
                    return AdminController::responseApi(422, __('admin.error.equipment-registration.ckecknumenote'));
                }
                $form->save();

                // Tien 6/4/2020
                $array = array();
                // Tien 6/4/2020

                foreach($validated['id'] as $key => $value){

                    //check type exist

                    $checkType = EquipmentType::query()
                        ->where('type_id', $validated['eqType'][$key])
                        ->first();
                    if(empty($checkType)){
                        exit();
                    }

                    if(is_null($value)){
                        $registration = new EquipmentRegistration();
                        $statusold = 0;
                    }else{
                        $registration = EquipmentRegistration::find($value);
                        if(empty($registration)){
                            exit();
                        }
                        $statusold = $registration->status;
                    }
                    if(isset($validated['note'][$key])){
                        $check1 = preg_match('/[(&|@|!|#|^|$|%|*|+|=)]/', $validated['note'][$key]);
                        if($check1 == 1){
                            return AdminController::responseApi(422, __('admin.error.equipment-registration.ckecknumenote'));
                        }
                    }
                    // $registration->register_id = $register_id;
                    $registration->user_id = Auth::user()->id;
                    $registration->form_id = $form->id;
                    $registration->type_id = $validated['eqType'][$key];
                    $registration->change_id = $validated['changeType'][$key];
                    $registration->status = 0;
                    $registration->note = $validated['note'][$key];
                    $registration->total = $validated['total'][$key];
                    $registration->status = $statusold;
                    if($validated['changeType'][$key] == 2){
                        //doi thiet bi
                        $check = Equipment::query()
                            ->where('code', $validated['eq'][$key])
                            ->first();
                        if($check){
                            $registration->type_id = $check->type_id;
                            $registration->code = $validated['eq'][$key];//thiet bi can doi
                            //check don dang ky cho thiet bi can doi da ton tai chua
                            $query = EquipmentRegistration::query()
                                ->where('code', $validated['eq'][$key])
                                ->where('status', 0)
                                ->first();
                            if(!empty($query)){
                                return AdminController::responseApi(200,null, __('admin.success.save'));
                                exit();
                            }
                        }
                        else{
                            exit();
                        }



                    }
                    // Tien 6/4/2020
                    if($validated['changeType'][$key] == 1){
                        $changeTypeE = 'Thêm thiết bị';
                        $codeE ='';
                    }else{
                        $changeTypeE = 'Đổi thiết bị';
                        $codeE = $check->name;
                    }
                    if(isset($validated['eqType'][$key])){
                        $eqTypeE = $checkType->type_name;
                    }
                    $array[$key] = array(
                                    'changeType' => $changeTypeE,
                                   'eqType' => $eqTypeE,
                                   'code' => $codeE,
                                   'total' => $validated['total'][$key],
                                   'note' => $validated['note'][$key],
                                  );
                    // Tien 6/4/2020

                    $registration->save();

                }
                // Tien 6/4/2020
                $this->sendMail(
                    $form->id,
                    Auth::user()->id,
                    $array,
                    'add'
                );
                // Tien 6/4/2020
                
                return AdminController::responseApi(200,null, __('admin.success.save'));

            }else{
               return AdminController::responseApi(422, __('admin.error.data'));
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
    public function deleteApi(Request $request, $oneId = null) {
        if($oneId!=null){
            $one = EquipmentRegistrationForm::find($oneId);
            if($one){
                EquipmentRegistration::query()
                    ->where('form_id', $oneId)
                    ->delete();
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
     * @param null $del
     * @return View (equipment-history-detail)
     */
     public function showDetailApi(Request $request, $oneId=null){

        $this->data['types'] = EquipmentType::all();
        $this->data['status_list'] = MasterData::query()
            ->where('datakey', 'TB')
            ->get();


        if($oneId!=null){
            $this->data['itemInfo'] = EquipmentRegistrationForm::find($oneId);

            if($this->data['itemInfo']){
                $this->data['registrations'] = EquipmentRegistration::query()
                    ->where('form_id', $this->data['itemInfo']->id)
                    ->get();
                foreach($this->data['registrations'] as $item){
                    $type_name = EquipmentType::query()
                        ->withTrashed()
                        ->where('type_id', $item->type_id)
                        ->first();
                    if(!empty($type_name)){
                        $item->type_name=$type_name->type_name;
                    }
                    $item->eq_name = is_null($item->code) ? '' : Equipment::query()->where('code', $item->code)
                                                                            //Tien 1/4/2020
                                                                            ->withTrashed()
                                                                            //Tien 1/4/2020
                                                                            ->first()->name;
                }
                $data = $this->data;
                return AdminController::responseApi(200, null, $data);
            }else{
                return AdminController::responseApi(422, __('admin.error.data'));
            }
        }else{
            $data = $this->data;
            $this->data['registrations'] = new \stdClass();
            return AdminController::responseApi(200, null, $data);
        }

    }
    //get thong tin cua loai thiet bi theo param change_id ( 1: Them thiet bi , 2: doi thiet bi)
    public function getEquipmentTypeList(Request $request) {
        $validator = Validator::make($request->input(), [
            'change_id' => 'integer|nullable',
        ]);

        if($validator->fails())
           return AdminController::responseApi(422, $validator->errors()->first());
        $validated = $validator->validated();
        // return $validated;
        $eqTypeList = EquipmentType::query();
        if(is_null($validated['change_id'])) {
            return array();
        } elseif ($validated['change_id'] == 2) {
            $eqTypeList = $eqTypeList->join('equipment', 'equipment.type_id', 'equipment_types.type_id')
                ->where('equipment.user_owner', Auth::user()->id)
                ->groupBy('equipment_types.type_id');
        }
        $data = $eqTypeList->get()->toArray();
        return AdminController::responseApi(200, null, $data);
    }
    //get thong tin cua thiet bi duoc lua chon theo param code
    public function getEquipmentStatus(Request $request) {
        $validator = Validator::make($request->input(), [
            'code' => 'string|nullable',
        ]);

        if($validator->fails())
            return AdminController::responseApi(422, $validator->errors()->first());
        $validated = $validator->validated();
        // return $validated;
        $eqStatus = DB::table('master_data');
        if(is_null($validated['code'])) {
            return array();
        } else {
            $eqStatus = $eqStatus->join('equipment', 'equipment.status_id', 'master_data.id')
                ->where('equipment.code', $validated['code']);
        }

        $eqStatus = $eqStatus->select('master_data.*');
        $data = $eqStatus->get()->toArray();

        return AdminController::responseApi(200, null, null, $data);
    }
    //get thong tin cac thiet bi theo param eqType(loai thiet bi) và eqOwner(nguoi dang ki)
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
        return AdminController::responseApi(200, null, $data);
    }
    /**
     * Show screen  Approve Equipment
     * @param Request $request
     * @param $id
     * @return View (equipment-registration-approve)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    //thong tin man duyet don dang ki thay doi thiet bi
    public function regApproveApi(Request $request, $id){
        $this->data['types'] = EquipmentType::all();
        $this->data['status_list'] = MasterData::query()
            ->where('datakey', 'TB')
            ->get();

        if($id!=null){

            $this->data['itemInfo'] = EquipmentRegistrationForm::find($id);

            if($this->data['itemInfo']){
                $this->data['registrations'] = EquipmentRegistration::query()
                    ->where('form_id', $this->data['itemInfo']->id)
                    ->where('change_id', 1)
                    ->get();

                foreach($this->data['registrations'] as $item){
                    $type_name = EquipmentType::query()->withTrashed()
                        ->where('type_id', $item->type_id)
                        ->first();
                    if(!empty($type_name)){
                        $item->type_name=$type_name->type_name;
                    }
                    $item->eq_name = is_null($item->code) ? '' : Equipment::query()->where('code', $item->code)->first()->name;
                    $item->approved = count(explode(',', $item->arr_code)) == 1 ? 0 : count(explode(',', $item->arr_code))-2;
                }

                $this->data['changeRegistrations'] = EquipmentRegistration::query()
                    ->where('form_id', $this->data['itemInfo']->id)
                    ->where('change_id', 2)
                    ->get();
                foreach($this->data['changeRegistrations'] as $item){
                    $item->type_name = EquipmentType::query()
                        //Tien 1/4/2020
                        ->withTrashed()
                        //Tien 1/4/2020
                        ->where('type_id', $item->type_id)
                        ->first()->type_name;

                    //Tien 1/4/2020
                    $item->eq_name = is_null($item->code) ? '' : Equipment::query()->withTrashed()->where('code', $item->code)->first()->name;
                     //Tien 1/4/2020

                    if(!is_null($item->arr_code)){
                        $newEquipment = Equipment::query()
                            ->where('code', explode(',', $item->arr_code)[1])
                            ->first();
                        if($newEquipment)
                        $item->newEq = $newEquipment;
                    }

                }
                $data = $this->data;
                return AdminController::responseApi(200, null, $data);
            }else{
                return AdminController::responseApi(422, __('admin.error.data'));
            }
        }else{
            $this->data['registrations'] = new \stdClass();
            $data = $this->data;
            return AdminController::responseApi(200, null, null, $data);
        }
    }
    /**
     * Show screen  Approve Equipment Detail
     * @param Request $request
     * @param $oneId = 0
     * @param $reject = 0
     * @return View (equipment-approve-detail)
     */
    //thong tin man duyet doi/them thiet bi
    public function regApproveDetailApi(Request $request, $oneId = 0, $reject = 0){

            $this->data['itemInfo'] = EquipmentRegistration::find($oneId);
            if($this->data['itemInfo']){

                $one = EquipmentType::query()
                    //Tien 1/4/2020
                    ->withTrashed()
                    //Tien 1/4/2020
                    ->where('type_id', $this->data['itemInfo']->type_id)
                    ->first();
                if(!empty($one->type_name)){
                    $this->data['itemInfo']->type_name = $one->type_name;
                }
                
                //Tien 1/4/2020
                $oneUser = User::withTrashed()->find($this->data['itemInfo']->user_id);
                //Tien 1/4/2020

                $this->data['itemInfo']->user_id = $oneUser->FullName;
                if(!empty($one->type_id)){
                $this->data['itemSources'] = DB::table('users')
                    ->join('equipment', 'equipment.user_owner', 'users.id')
                    ->select('users.FullName', 'users.id')
                    ->where('equipment.type_id', $one->type_id)
                    ->where('equipment.user_owner', '<>', $oneUser->id)
                    ->groupBy('users.id')
                    ->orderBy('username')
                    ->get();
                $this->data['store'] = Equipment::query()
                    ->where('type_id', $one->type_id)
                    ->where('user_owner', 0)
                    ->get();
                }else{
                     $this->data['itemSources'] =[];
                     $this->data['store'] = [];
                }
                if(!is_null($this->data['itemInfo']->code)){
                    //Tien 1/4/2020 
                    $this->data['itemInfo']->eq = Equipment::query()->withTrashed()
                        ->where('code', $this->data['itemInfo']->code)
                        ->first();
                    //Tien 1/4/2020
                    $this->data['itemInfo']->eq_status = MasterData::find($this->data['itemInfo']->eq->status_id);
                    $this->data['itemInfo']->newOwners = User::query()
                        ->where('id', '<>', $this->data['itemInfo']->eq->user_owner)
                        ->where('Active', 1)
                        ->get();

                    $newSource = EquipmentUsingHistory::query()
                        ->where('register_id', $oneId)
                        ->where('code', $this->data['itemInfo']->code)
                        ->first();
                    if($newSource){
                        $this->data['itemInfo']->newSource = $newSource->user_owner;
                    }

                }
                $arr = explode(",", $this->data['itemInfo']->arr_code);
                $query = Equipment::query();
                foreach($arr as $item){
                    $query = $query->orWhere('code', $item);
                }

                $this->data['processedEq'] = $query->get();
                foreach($this->data['processedEq'] as $item){
                    $owner = User::find($item->user_owner);
                    if($owner){
                        $item->source = $owner->FullName;
                    }
                    else{
                        $item->source = 'Kho';
                    }
                    $oldOwner = EquipmentUsingHistory::query()->select('users.FullName')
                    ->leftjoin('users', 'equipment_using_histories.old_user_owner', 'users.id')
                    ->where('equipment_using_histories.user_owner', $item->user_owner)
                    ->where('equipment_using_histories.updated_at', $item->updated_at)
                    ->get();
                    if($oldOwner){
                        $item->oldOwner = null==$oldOwner[0]['FullName']?'Kho':$oldOwner[0]['FullName'];
                    }
                
                }
                $reg = EquipmentRegistration::query()
                    ->leftjoin('equipment_registration_forms', 'equipment_registration_forms.id', 'equipment_registrations.form_id')
                    ->where('equipment_registrations.id', $oneId)
                    ->where('equipment_registrations.status', 0)
                    ->first();
                if(!$reg || $reg->processing_requests == 1){
                    if(isset($item->user_owner)){
                        if(isset($reg->processing_requests)){
                            $this->data['reg'] = 0; 
                        }else{
                            $this->data['reg'] = 1; 
                        }
                    }else{
                        $this->data['reg'] = 0; 
                    }
                }else{
                    $this->data['reg'] = 0;
                }
                $data = $this->data;
                return AdminController::responseApi(200, null, $data);
            }else{
                return AdminController::responseApi(422, __('admin.error.data'));
            }
    }
    //get danh sach thiet bi theo nguon thiet bi de duyet don dang ki
    public function equipmentApproveList(Request $request) {
        // return $request->input();
        $itemList = Equipment::query()
            ->where('user_owner', $request->input('user_owner'))
            ->where('type_id', $request->input('type_id'))
            ->where('status_id', 15)
            ->get()->toArray();
        return AdminController::responseApi(200, null, null, $itemList);
    }
    /**
     * Process insert multiple records,update one records
     * @param Request $request
     * @return string|void
     */
    //Duyet don dang ky thiet bi
    public function regApproveDetailStoreApi(Request $request){
        //
        //cap nhat chi tiet dang ky
        //
        $reg = EquipmentRegistration::query()
            ->where('id', $request->input('reg'))
            ->where('status', 0)
            ->first();
        if(!$reg)
            return AdminController::responseApi(422, __('admin.error.equipment-registration.reg'));
        $eqApprovedArr = explode(',', $reg->arr_code);

        //check equipment co hop le hay khong
        $check = true;
        $count = 0;
        if(null == $request->input('eq1')){
            return AdminController::responseApi(422, __('admin.error.equipment-registration.type'));
        }
        foreach($request->input('eq1') as $item){
            if(!in_array($item, $eqApprovedArr)){
                $count++;
                $eq = Equipment::query()
                    ->where('code', $item)
                    ->where('type_id', $reg->type_id)
                    ->where('status_id', 15)
                    ->where('user_owner', '<>', $reg->user_id)
                    ->first();
                if(!$eq){
                    $check = false;
                    // break;
                }
            }

        }
        if($count > $reg->total - (count($eqApprovedArr) == 1 ? count($eqApprovedArr) - 1 : count($eqApprovedArr) - 2 ))
           return AdminController::responseApi(422, __('admin.error.equipment-registration.number'));
        if(!$check) exit();
        //check so luong equipment
        // if(count($request->input('eq1')) > $reg->total)
        // exit();
        $reg->approved_user = Auth::user()->id;
        $reg->approved_date = Carbon::now();
        $reg->arr_code = ",".implode(',', $request->input('eq1')).",";
        if(count($request->input('eq1')) == $reg->total){
            $reg->status = 1;
        }
        $reg->save();

        //luan chuyen thiet bi cu
        if($reg->change_id == 2){

            $oldEquipment = Equipment::query()
                ->where('code', $reg->code)
                ->first();
            $newOwner = User::find($request->input('new_source'));
            if($newOwner){
                $newOwnerId = $newOwner->id;

            }else{
                $newOwnerId = 0;
            }

            //lich su luan chuyen

            $oldEquipHistory = new EquipmentUsingHistory();
            $oldEquipHistory->user_owner = $newOwnerId;
            $oldEquipHistory->old_user_owner = $oldEquipment->user_owner;
            $oldEquipHistory->deal_date = Carbon::now();
            $oldEquipHistory->created_user = Auth::user()->id;
            $oldEquipHistory->status_id = $oldEquipment->status_id;
            $oldEquipHistory->old_status_id = $oldEquipment->status_id;
            $oldEquipHistory->register_id = $reg->id;
            $oldEquipHistory->code = $oldEquipment->code;
            $oldEquipHistory->save();

            $oldEquipment->user_owner = $newOwnerId;
            $oldEquipment->save();
        }
        //
        //Cập nhật đơn (nhiều đăng ký)
        //
        $this->updateRegFormStatus($reg->form_id,'Approve',$reg->id);
        //
        //Luân chuyển thiết bị
        //

        foreach($request->input('eq1') as $item){
            if(!in_array($item, $eqApprovedArr)){
                $eq = Equipment::query()
                    ->where('code', $item)
                    ->where('type_id', $reg->type_id)
                    ->where('status_id', 15)
                    ->first();

                //cap nhat lich su thiet bi
                $eqHistory = new EquipmentUsingHistory();
                $eqHistory->code = $eq->code;
                $eqHistory->user_owner = $reg->user_id;
                $eqHistory->old_user_owner = $eq->user_owner;
                $eqHistory->created_user = Auth::user()->id;
                $eqHistory->register_id = $reg->id;
                $eqHistory->status_id = $eq->status_id;
                $eqHistory->old_status_id = $eq->status_id;
                $eqHistory->deal_date = Carbon::now();
                $eqHistory->save();

                //cap nhat thiet bi
                $eq->user_owner = $reg->user_id;
                $eq->save();
            }

        }
        // Tien 6/4/2020
        $checkType = EquipmentType::query()
                    ->where('type_id', $reg->type_id)
                    ->first();
        $check = Equipment::query()
                ->where('code', $reg->code)
                ->first();
        $a = explode(',', $reg->arr_code);
        $arr_codeN ='';
        foreach ($a as $value) {
            if($value){
                $check1 = Equipment::query()
                ->where('code', $value)
                ->first();
            $arr_codeN = $arr_codeN .','. $check1->name;
            }
        }
        if($reg->change_id == 1){
            $changeTypeE = 'Thêm thiết bị';
            $codeE ='';
        }else{
            $changeTypeE = 'Đổi thiết bị';
            $codeE = $check->name;
        }
        
        $form_id     = $reg->form_id;
        $changeTypeE = $changeTypeE;
        $eqTypeE     = $checkType->type_name;
        $arr_codeN   = $arr_codeN;
        $totalE      = $reg->total;
        $noteE       = $reg->note;
        $user_idE    = $reg->user_id;
        
        $array[1] = array(
            'changeType' => $changeTypeE,
            'eqType' => $eqTypeE,
            'code' => $codeE,
            'arr_codeN' => $arr_codeN,
            'total' => $totalE,
            'note' => $noteE,
          );
        
        $this->sendMail(
            $form_id,
            $user_idE,
            $array,
            'Approve'
        );
        // Tien 6/4/2020
        $data = 1;
        return AdminController::responseApi(200, null, $data);
    }
    /**
     * Process insert multiple records,update one records
     * @param Request $request
     * @return string|void
     */
    //Tu choi don dang ki thiet bi
    public function regApproveRejectApi(Request $request){
        $reg = EquipmentRegistration::find($request->input('id'));
        if(!$reg) return("Đơn không tồn tại");
        if(is_null($reg->arr_code)){
            $reg->status = 2;
        }else{
            $reg->status = 1;
        }

        $reg->reject_note = $request->input('note');
        $reg->approved_user = Auth::user()->id;
        $reg->approved_date = Carbon::now();
        $reg->save();
        $this->updateRegFormStatus($reg->form_id,'Reject',$reg->id);
        // Tien 6/4/2020
        $checkType = EquipmentType::query()
                    ->where('type_id', $reg->type_id)
                    ->first();
        $check = Equipment::query()
                ->where('code', $reg->code)
                ->first();
        if($reg->change_id == 1){
            $changeTypeE = 'Thêm thiết bị';
            $codeE ='';
        }else{
            $changeTypeE = 'Đổi thiết bị';
            $codeE = $check->name;
        }
        $form_id     = $reg->form_id;
        $changeTypeE = $changeTypeE;
        $eqTypeE     = $checkType->type_name;
        $totalE      = $reg->total;
        $noteE       = $reg->note;
        $user_idE    = $reg->user_id;
        
        $array[1] = array(
            'changeType' => $changeTypeE,
            'eqType' => $eqTypeE,
            'code' => $codeE,
            'total' => $totalE,
            'note' => $noteE,
          );
        
        $this->sendMail(
            $form_id,
            $user_idE,
            $array,
            'Reject'
        );
        // Tien 6/4/2020
        $data = 1;
        return AdminController::responseApi(200, null, $data);
    }
}