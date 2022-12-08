<?php

namespace App\Http\Controllers\Admin;

use App\EquipmentType;
use App\User;
use App\RoleScreenDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

/**
 * Class EquipmentTypeController
 * @package App\Http\Controllers\Admin
 * Controller screen EquipmentType
 */
class EquipmentTypeController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    const KEYMENU= array(
        "add" => "EquipmentTypeAdd",
        "view" => "EquipmentType",
        "edit" => "EquipmentTypeEdit",
        "delete" => "EquipmentTypeDelete",
    );
    /**
     * EquipmentTypeController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {

        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
        $array = $this->RoleView(null,['EquipmentType']);
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if($value == $row->alias)
                    $this->$key = $row;
            } 
        }
    }

    /**
     * @param Request $request
     * @return View (equipment-types)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * Get data EquipmentType and return view
     */
    public function show(Request $request, $orderBy = 'id', $sortBy = 'desc'){

        $recordPerPage = $this->getRecordPage();
        $this->authorize('view', $this->menu);

        //list users
        if(Schema::hasColumn('equipment_types',$orderBy)){
            $list = EquipmentType::orderBy($orderBy, $sortBy);
        }
        else
        {
            return redirect()->back();

        }

        //tim kiem theo column
        $this->data['request'] = $request->query();
        $one = EquipmentType::query()->select('type_name','type_id','note')->first();
        if($one){
            $one = $one->toArray(); 
            if(array_key_exists('search', $request->input())){
                $list = $list->where(function ($query) use ($one, $request){
                    foreach($one as $key=>$value){
                        $query->orWhere($key, 'like', '%'.$request->input('search').'%');
                    }
                    $query->orWhereRaw('(DATE_FORMAT(equipment_types.created_at,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                });

            }
        }



        //phan trang
        $list = $list->paginate($recordPerPage);

        $this->data['list'] = $list;
        foreach($this->data['list'] as $item){
            // $item->created_user = User::find($item->created_user)->FullName;

            //Tiên 1/4/2020
            $item->created_user = User::query()->withTrashed()
                    ->where('id', $item->created_user)          
                    ->first()->FullName;
            //Tiên 1/4/2020
        }
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
        $this->data['recordPerPage'] = $recordPerPage;
        return $this->viewAdminLayout('equipment-types', $this->data);
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
                            'type_id'  =>  'required|string|max:3|min:3',
                            'type_name' =>  'required|string|max:100',
                            'note'    =>  'string|nullable',

                        ]);
                }else{
                    $validator = Validator::make($request->all(),
                        [
                            'type_id'  =>  'required|string|max:3|min:3',
                            'type_name' =>  'required|string|max:100',
                            'note'    =>  'string|nullable',
                        ]);
                }

                if ($validator->fails())
                {
                    return response()->json(['errors'=>$validator->errors()->all()]);
                }

                $validated = $validator->validate();
                if(substr($validated['type_id'], 0,-2) != '0'){
                }else{
                    if(intval($validated['type_id']) == 0){
                        return $this->jsonErrors('Mã thiết bị không thể là '.$validated['type_id']);
                    }
                }
                
                if(array_key_exists('id', $validated)){
                    $type = EquipmentType::query()
                            ->select('type_id')
                            ->where('id','!=',$validated['id'])
                            ->get();
                    $one = EquipmentType::find($validated['id']);
                }else{
                    $type = EquipmentType::query()
                            ->select('type_id')
                            ->get();
                    $one = new EquipmentType();
                }

                foreach($type as $row){
                    if($row['type_id'] == $validated['type_id'])
                    {
                        return $this->jsonErrors('Mã thiết bị đã tồn tại !');
                    }
                    
                }
                foreach($validated as $key => $value){
                    if(Schema::hasColumn('equipment_types', $key))
                        $one->$key = $value;
                }
                $one->created_user = Auth::user()->id;


                $one->save();
                return $this->jsonSuccessWithRouter('admin.EquipmentType');

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
     * @param null $oneId
     * @param null $del
     * @return View (equipment-type-detail)
     */
    public function showDetail($oneId=null, $del=null){
        $this->data['list'] = EquipmentType::query()
            ->select('type_id')
            ->get();
        if($oneId!=null){
            if($del == 'del'){
                $one = EquipmentType::find($oneId);
                if($one) {
                    $one->delete();
                }
                return 1;
            }
            $this->data['itemInfo'] = EquipmentType::find($oneId);

            if($this->data['itemInfo']){
                return $this->viewAdminIncludes('equipment-type-detail', $this->data);
            }else{
                return "";
            }
        }else{
            return $this->viewAdminIncludes('equipment-type-detail', $this->data);
        }
    }
    //API
    /**
     * @param Request $request
     * @return View (daily-report)
     * @throws AuthorizationException
     * Get data Equipment Type and return view
     */
    public function indexApi(Request $request,$orderBy = 'id', $sortBy = 'desc') {
        $recordPerPage = $this->getRecordPage();
        $this->authorize('action', $this->view);
        //list users
        if(Schema::hasColumn('equipment_types',$orderBy)){
            $list = EquipmentType::orderBy($orderBy, $sortBy);
        }
        else
        {
            return redirect()->back();

        }

        //tim kiem theo column
        $this->data['request'] = $request->query();
        $one = EquipmentType::query()->select('type_name','type_id','note')->first();
        if($one){
            $one = $one->toArray(); 
            if(array_key_exists('search', $request->input())){
                $list = $list->where(function ($query) use ($one, $request){
                    foreach($one as $key=>$value){
                        $query->orWhere($key, 'like', '%'.$request->input('search').'%');
                    }
                    $query->orWhereRaw('(DATE_FORMAT(equipment_types.created_at,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                });

            }
        }

        //phan trang
        $list = $list->paginate($recordPerPage);

        $this->data['list'] = $list;
        foreach($this->data['list'] as $item){
            $item->created_user = User::query()->withTrashed()
                    ->where('id', $item->created_user)          
                    ->first()->FullName;
        }
        $query_string = str_replace($request->url(), '', $request->fullUrl());

        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
        parse_str(str_replace('?', '', $query_string), $query_array);

        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;

        $data = $this->data;
        $data['role_key'] = 'EquipmentType';
        return AdminController::responseApi(200, null, null, $data);
    }
    /**
     * Process insert multiple records
     * @param Request $request
     * @return string|void
     */
    public function storeApi(Request $request){
        $this->authorize('action', $this->add);
        try{
            if(count($request->input()) >0){
                $validator = Validator::make($request->all(),
                    [
                        'type_id'  =>  'required|string|max:3|min:3',
                        'type_name' =>  'required|string|max:100',
                        'note'    =>  'string|nullable',
                    ]);
                if ($validator->fails())
                {
                    return AdminController::responseApi(422, $validator->errors()->first());
                }

                $validated = $validator->validate();
                if(substr($validated['type_id'], 0,-2) != '0'){
                }else{
                    if(intval($validated['type_id']) == 0){
                        return AdminController::responseApi(422, __('admin.error.equipment-type.check-id').$validated['type_id']);
                    }
                }
                
                $type = EquipmentType::query()
                        ->select('type_id')
                        ->get();
                $one = new EquipmentType();

                foreach($type as $row){
                    if($row['type_id'] == $validated['type_id'])
                    {
                        return AdminController::responseApi(422, __('admin.error.equipment-type.type-id'));
                    }
                    
                }
                foreach($validated as $key => $value){
                    if(Schema::hasColumn('equipment_types', $key))
                        $one->$key = $value;
                }
                $one->created_user = Auth::user()->id;

                $save = $one->save();
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
                        'id'    =>  'integer|min:1|nullable',
                        'type_id'  =>  'required|string|max:3|min:3',
                        'type_name' =>  'required|string|max:100',
                        'note'    =>  'string|nullable',

                    ]);

                if ($validator->fails())
                {
                    return AdminController::responseApi(422, $validator->errors()->first());
                }

                $validated = $validator->validate();
                if(substr($validated['type_id'], 0,-2) != '0'){
                }else{
                    if(intval($validated['type_id']) == 0){
                        return AdminController::responseApi(422, __('admin.error.equipment-type.check-id').$validated['type_id']);
                    }
                }
                
                $type = EquipmentType::query()
                        ->select('type_id')
                        ->where('id','!=',$validated['id'])
                        ->get();
                $one = EquipmentType::find($validated['id']);

                foreach($type as $row){
                    if($row['type_id'] == $validated['type_id'])
                    {
                        return AdminController::responseApi(422, __('admin.error.equipment-type.type-id'));
                    }
                    
                }
                foreach($validated as $key => $value){
                    if(Schema::hasColumn('equipment_types', $key))
                        $one->$key = $value;
                }
                $one->created_user = Auth::user()->id;

                $save = $one->save();
                if (!$save) {
                    return AdminController::responseApi(403, __('admin.error.save'));
                } else {
                    return AdminController::responseApi(200, null, __('admin.success.save'));
                }

            }else{
                return AdminController::responseApi(422, $e->getMessage());
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
        if($oneId!=null){
            $one = EquipmentType::find($oneId);
            if($one) {
                $one->delete();
            }
            return AdminController::responseApi(200, null, __('admin.success.delete'));
        }
        return AdminController::responseApi(422, __('admin.error.data'));
    }
    /**
     * Show popup insert,update can insert multiple records
     * @param null $oneId
     * @param null $del
     * @return View (equipment-type-detail)
     */
    public function showDetailApi($oneId=null){
        $this->data['list'] = EquipmentType::query()
            ->select('type_id')
            ->get();
        if($oneId!=null){
            $this->data['itemInfo'] = EquipmentType::find($oneId);
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
}
