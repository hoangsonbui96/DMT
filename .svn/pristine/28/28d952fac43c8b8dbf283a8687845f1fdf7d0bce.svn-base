<?php

namespace App\Http\Controllers\Admin;

use App\MasterData;
use App\RoleScreenDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use DB;

/**
 * Class MasterDataController 
 * @package App\Http\Controllers\Admin
 * Controller screen Master
 */
class MasterDataController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    const KEYMENU= array(
        "add" => "MasterDataAdd",
        "view" => "MasterData",
        "edit" => "MasterDataEdit",
        "delete" => "MasterDataDelete",
    );
    /**
     * MasterDataController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');
        $array = $this->RoleView(null,['MasterData']);
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if($value == $row->alias)
                    $this->$key = $row;
            } 
        }
    }
    /**
     * @param Request $request
     * @return View (master-data)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * Get data MasterData and return view
     */
    public function show(Request $request, $orderBy = 'id', $sortBy = 'asc'){
        $this->authorize('view', $this->menu);
        $recordPerPage = config('settings.records_per_page');
        //list users
        if($orderBy =='id'){
            $orderBy = 'DataDisplayOrder';
        }
        if(Schema::hasColumn('master_data',$orderBy)){ 
            $masterDatas = MasterData::orderBy($orderBy, $sortBy);
        }
        else
        {
            return redirect()->back();

        }
        $this->data['groupDatakey'] = MasterData::query()->withTrashed()
            ->select('DataKey','TypeName')
            ->groupBy('DataKey','TypeName')
            ->orderBy('DataKey','desc')
            ->get();

        //tim kiem theo column
        $this->data['request'] = $request->query();
        $id = null !== $request->input('datakey')?$request->input('datakey'):'';
        if($id==''){
            $onedatakey = MasterData::query()
                        ->select('DataKey')
                        ->limit(1)
                        ->get();
            foreach ($onedatakey as $rows) {
               $this->data['request'] =  array('datakey'=>$rows->DataKey);
            }
        }

        $one = MasterData::query()->select('DataKey','Name','TypeName','DataValue','DataDescription')->first();
        if($one){
            $one = $one->toArray();
            if(array_key_exists('search', $request->input())){
                $masterDatas = $masterDatas->where(function ($query) use ($one, $request){
                    foreach($one as $key=>$value){
                        $query->orWhere($key, 'like', '%'.$request->input('search').'%');
                    }
                });

            }
        }

        //tim kiếm
        foreach($this->data['request'] as $key => $value){
            if(!is_array($value)){
                if(Schema::hasColumn('master_data', $key) && $value !== null){
                    $masterDatas = $masterDatas->where($key, 'like', '%'.$value.'%');
                }
            }
        }

        //phan trang
        $masterDatas = $masterDatas->paginate($recordPerPage);

        $this->data['masterDatas'] = $masterDatas;
        $query_string = str_replace($request->url(), '', $request->fullUrl());

        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
        parse_str(str_replace('?', '', $query_string), $query_array);
        if($masterDatas->count() == 0){
            if(array_key_exists('page', $query_array)){
                if($query_array['page'] > 1){
                    $query_array['page'] = $masterDatas->lastPage();
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
        return $this->viewAdminLayout('master-data', $this->data);
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
                            'Name'  =>  'required|string|max:1000',
                            'TypeName'  =>  'required|string|max:1000',
                            'DataKey'  =>  'required|string|max:1000',
                            // 'DataValue'  =>  'required|string|max:1000',
                            'DataDescription'  =>  'string|nullable',
                            'DataDisplayOrder'  =>  'required|integer',
                            'id'    =>  'integer|min:1|nullable',
                        ]);
                }else{
                    $validator = Validator::make($request->all(),
                        [
                            'Name'  =>  'required|string|max:1000',
                            'TypeName'  =>  'required|string|max:1000',
                            'DataKey'  =>  'required|string|max:1000',
                            // 'DataValue'  =>  'required|string|max:1000',
                            'DataDescription'  =>  'string|nullable',
                            'DataDisplayOrder'  =>  'required|integer',
                        ]);
                }

                if ($validator->fails())
                {
                    return response()->json(['errors'=>$validator->errors()->all()]);
                }

                $validated = $validator->validate();
                if($validated['DataKey'] != 'EM' && null == $validated['DataDescription']){
                    return $this->jsonErrors('Chưa điền mô tả');
                }
                if(array_key_exists('id', $validated)){
                    $one = MasterData::find($validated['id']);
                }else{

                    $one = new MasterData();
                    $masters = MasterData::query()
                    ->where('DataKey', $validated['DataKey'])
                    ->orderBy('id', 'desc')
                    ->first();
                    if($masters){
                        $stt = substr($masters->DataValue, 3);
                        $key = substr($masters->DataValue, 0, 3);
                        $newStt = $stt + 1;
                        $one->DataValue = $key.substr("0000{$newStt}", -2);
                    }else{
                        $one->DataValue = $validated['DataKey']."001";
                    }
                    $one->PermissionEdit = 0;
                    $one->PermissionDelete = 0;
                }

                //change Display Order
                $masterOrders = MasterData::query()
                    ->select('DataDisplayOrder')
                    ->where('DataKey', $validated['DataKey'])
                    ->orderBy('DataDisplayOrder', 'desc')
                    ->get();

                if(array_key_exists('id', $validated)){
                    $masterDisplayOrdersold = MasterData::find($validated['id'])
                    ->DataDisplayOrder;
                    if($validated['DataDisplayOrder'] < $masterDisplayOrdersold){
                       $one->where('DataKey', $validated['DataKey'])
                        ->where('DataDisplayOrder','>=',$validated['DataDisplayOrder'])
                        ->where('DataDisplayOrder','<', $masterDisplayOrdersold)
                        ->increment('DataDisplayOrder',1);
                        $check = false;
                    }else{
                        $check = true;
                    }
                    
                }else{
                    $check = true;
                }

                if($check){
                  foreach ($masterOrders as $row) {
                        if($validated['DataDisplayOrder'] == $row->DataDisplayOrder){
                            $one->where('DataKey', $validated['DataKey'])
                            ->where('DataDisplayOrder','>=', $validated['DataDisplayOrder'])
                            ->increment('DataDisplayOrder',1);
                        }
                    }  
                }

                foreach($validated as $key => $value){
                    if(Schema::hasColumn('master_data', $key))
                        $one->$key = $value;
                }

                $save = $one->save(); 
                if(!$save){
                    return $this->jsonErrors('Lưu không thành công'); 
                }else{
                    return $this->jsonSuccessWithRouter('admin.MasterData');
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
     * @param null $oneId
     * @param null $del
     * @return View (master-data-detail)
     */
    public function showDetail(Request $request, $oneId = null, $del = null){
        if($oneId!=null){
            if($del == 'del'){
                $one = MasterData::find($oneId);
                if($one){
                    if($one->PermissionDelete == 1){
                        return $this->jsonErrors('Bạn không có quyền xóa ');
                    }else{
                        $save = $one->delete();
                    }
                } 
                if(!$save){
                    return $this->jsonErrors('Xóa không thành công');
                }else{
                     return 1;
                }
            }
            $this->data['itemInfo'] = MasterData::find($oneId);
            if($this->data['itemInfo']){
                $PermissionEdit = MasterData::find($oneId)->PermissionEdit;
                if($PermissionEdit == 1){
                    return $this->jsonErrors('Bạn không có quyền sửa ');
                }else{
                    return $this->viewAdminIncludes('master-data-detail', $this->data);
                }
            }else{
                return "";
            }
        }else{
            return $this->viewAdminIncludes('master-data-detail', $this->data);
        }

    }
}
