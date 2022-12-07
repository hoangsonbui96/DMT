<?php

namespace App\Http\Controllers\Admin;

use App\RolepartnerscreenDetailRelationship;
use App\RoleScreen;
use App\RoleScreenDetail;
use App\RolePartnerGroup;
use App\MasterData;
use App\Project;
use Illuminate\Http\Request;
use App\Partner;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Menu;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Exports\PartnerExport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class PartnerController
 * @package App\Http\Controllers\Admin
 * Screen list Partner
 */
class PartnerController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $export;
    const KEYMENU= array(
        "add" => "PartnerListAdd",
        "view" => "PartnerList",
        "edit" => "PartnerListEdit",
        "delete" => "PartnerListDelete",
        "export" => "PartnerListExport",
    );
    /**
     * Check role view,insert,update
     * PartnerController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');
        $array = $this->RoleView(null,['PartnerList']);
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if($value == $row->alias)
                    $this->$key = $row;
            } 
        }
    }

    /**
     * Get list partner
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @param null $id
     * @return View (partner)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Request $request, $orderBy = 'id', $sortBy = 'desc', $id = null){

        $this->authorize('view', $this->menu);
        $recordPerPage = $this->getRecordPage();
        $partners = $this->getDataWithCondition($request, $orderBy, $sortBy);
        $count = $partners->count();
        //Pagination
        $partners = $partners->paginate($recordPerPage);
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
        $sort = ($sortBy=='asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);
        //redirect to the last page if current page has no record
        if($partners->count() == 0)
        {
            if(array_key_exists('page', $query_array))
            {
                if($query_array['page'] > 1)
                {
                    $query_array['page'] = $partners->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl  = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }
            }
        }
        $this->data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $this->data['partners'] = $partners;
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['sort'] = $sort;
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;
        $this->data['export'] = $this->export;

        return $this->viewAdminLayout('partner', $this->data);
    }
    public function getDataWithCondition($array, $orderBy, $sortBy, $export = null){
        //Get list partners left join with master_data
        if(Schema::hasColumn('partners', $orderBy))
        {
            // $partners = Partner::query()->select('partners.*','master_data.Name')
            //     ->leftJoin('master_data','partners.department_id','=','master_data.DataValue')
            //     ->orderBy('full_name', 'asc');

            $partners = Partner::query()->select('partners.*')
                // ->leftJoin('master_data','partners.department_id','=','master_data.DataValue')
                ->orderBy($orderBy,$sortBy);
        }else{

            return redirect()->back();
        }
        //Search in columns
        $this->data['request'] = $array->query();
        // $one = Partner::query()
        //     ->select('partners.full_name','partners.tel','partners.email','partners.address','master_data.Name','partners.InfoRepresentatives')
        //     ->leftJoin('master_data','partners.department_id','=','master_data.DataValue')
        //     ->first();
        $one = Partner::query()
            ->select('partners.full_name','partners.tel','partners.email','partners.address','partners.InfoRepresentatives')
            // ->leftJoin('master_data','partners.department_id','=','master_data.DataValue')
            ->first();

        if ($one)
        {
            $one = $one->toArray();
            if (array_key_exists('search',$array->input()))
            {
                $partners = $partners->where(function ($query) use ($one,$array){
                    foreach($one as $key => $value){

                        // if($key == 'Name') {
                        //     $query->orWhere('master_data.'.$key, 'like', '%'.$array->input('search').'%');
                        // }else{

                            // if(in_array($key, ['birthday' ])){
                            //     $query->orWhereRaw('(DATE_FORMAT(partners.'.$key.',"%d/%m/%Y")) like ?', '%'.$array->input('search').'%' );
                            // }
                            // else{
                                $query->orWhere('partners.'.$key, 'like', '%'.$array->input('search').'%');
                            // }
                        // }
                    }


                });
            }
        }
        if ($export != '' || $export != null){
            return $partners->get();
        }
        return $partners;
    }

    /**
     * @param Request $request
     * @param null $view
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request, $view = null){
        $records = $this->getDataWithCondition($request, 'id', 'desc', 'export');
        if($records->count() > 0){
            return Excel::download(new PartnerExport($records, $view), 'DanhSachDoiTac.xlsx');
        }else{
            return Redirect::back()->withErrors(['Không có dữ liệu!']);
        }
    }
    /**
     * Processing insert, update
     * @param Request $request
     * @param null $id
     * @return string|void
     */
    public function  showDetail(Request $request, $id= null, $del = null){
        // $this->data['master_data'] = MasterData::query()
        // ->select('Name','DataValue')
        // ->where('DataKey','DT')->get();
        if($id != null)
        {
            $this->data['PartnerInfo'] = Partner::find($id);
            if($del == 'del')
            {
                $one = Partner::find($id);
                if($one != null)
                {
                    $one->delete();
                }
                return 1;
            }
            if($this->data['PartnerInfo']){
                return $this->viewAdminIncludes('partner-detail',$this->data);
            }else{
                return "";
            }
        }else{
        return $this->viewAdminIncludes('partner-detail',$this->data);
        }
    }
    /**
     * Processing insert, update
     * @param Request $request
     * @param null $id
     * @return string|void
     */
    public function store(Request $request, $id = null){
        if (count($request->input()) === 0)
        {
            return abort('404');
        }
        try{
            $arrCheck = [
                'full_name'              =>  'required|string|max:100',
                'InfoRepresentatives'    =>  'required',
                // 'sectors'  =>  'string|nullable',
                // 'birthday'  =>  'required|date_format:d/m/Y',
                'tel'                    =>  'required|regex:/^([0-9\s\-\+\(\)]*)$/||min:10|max:12',
                'address'                =>  'string|nullable|max:256',
                'email'                  =>  'nullable|string|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            ];
            $modeIsUpdate = array_key_exists('id', $request->input());
            if ($modeIsUpdate){
                $arrCheck['id'] = 'integer|min:1|nullable';
            }
            $validator = Validator::make($request->all(), $arrCheck);
            if ($validator->fails()) 
            {
                return response()->json(['errors' => $validator->errors()->first()]);
            }
            $validated = $validator->validate();
            // $birthDay =  $this->fncDateTimeConvertFomat($validated['birthday'],self::FOMAT_DISPLAY_DMY,self::FOMAT_DB_YMD);
            // if (Carbon::parse($birthDay)->age < 18){
            //     return response()->json(['errors' => ['Ngày sinh không hợp lệ']]);
            // }
            $one = !$modeIsUpdate ? new Partner() : Partner::find($validated['id']);
            foreach($validated as $key => $value){
                if(Schema::hasColumn('partners', $key))
                {
                    // if ($key == 'birthday'){
                    //     $value = $this->fncDateTimeConvertFomat($value, 'd/m/Y', 'Y-m-d');
                    // }                 
                    $one->$key = strip_tags($value);
                }
            }
            $one->save();        
            return $this->jsonSuccessWithRouter('admin.Partner');
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }
}
