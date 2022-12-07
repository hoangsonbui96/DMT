<?php

namespace App\Http\Controllers\Admin;


use App\Menu;
use App\Project;
use App\User;
use App\RoleScreenDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class ProjectController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    const KEYMENU= array(
        "add" => "ProjectManagementAdd",
        "view" => "ProjectManagement",
        "edit" => "ProjectManagementEdit",
        "delete" => "ProjectManagementDelete"
    );
    public function __construct(Request $request)
    {
        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
        $array = $this->RoleView('Projects',['ProjectManagement']);
        $this->menu = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if($value == $row->alias)
                    $this->$key = $row;
            } 
        }
    }

    public function show(Request $request, $orderBy = 'id', $sortBy = 'desc'){
        $this->authorize('view', $this->menu);
        $recordPerPage = $this->getRecordPage();

        //list users
        if(Schema::hasColumn('projects',$orderBy)){
            $projects = Project::orderBy($orderBy, $sortBy);
        } else {
            return redirect()->back();
        }

        //tim kiem theo column
        $this->data['request'] = $request->query();
        $one = Project::query()->select('NameVi','NameShort','Customer','StartDate','EndDate')->first();
        if($one){
            $one = $one->toArray();
            if(array_key_exists('search', $request->input())){
                $projects = $projects->where(function ($query) use ($one, $request){
                    foreach($one as $key=>$value){
                        $strSearch = trim($this->convert_vi_to_en($request->input('search')));

                        if(in_array($key, ['StartDate', 'EndDate'])){
                            $query->orWhereRaw('(DATE_FORMAT(projects.'.$key.',"%d/%m/%Y")) LIKE ?', '%'.$strSearch.'%' );
                        }else{
                            $query->orWhere('projects.'.$key, 'LIKE', '%'.$strSearch.'%');
                        }
                    }

                });

            }
        }

        $count = $projects->count();
        //phan trang
        $projects = $projects->paginate($recordPerPage);
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc')."/".$query_string;
        parse_str(str_replace('?', '', $query_string), $query_array);

        //redirect to the last page if current page has no record
        if($projects->count() == 0){
            if(array_key_exists('page', $query_array)){
                if($query_array['page'] > 1){
                    $query_array['page'] = $projects->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl  = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }
            }
        }
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        $this->data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $this->data['projects'] = $projects;
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['sort'] = $sort;
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;

        return view('admin.layouts.'.config('settings.template').'.projects', $this->data);
    }

    public function store(Request $request, $id = null){
        if (count($request->input()) === 0){
            return abort('404');
        }
        try{
            $arrCheck = [
                'NameVi'         =>  'required|string|max:100',
                'NameJa'         =>  'nullable|string|max:100',
                'NameShort'      =>  'required|string|max:100|unique:projects,NameShort,'.$request['id'].',id,deleted_at,NULL',
                'Customer'       =>  'required|string|max:100',
                'StartDate'      =>  'required|date_format:d/m/Y',
                'EndDate'        =>  'nullable|date_format:d/m/Y',
                'Leader'         =>  'required|array',
                'Member'         =>  'required|array',
                'Description'    =>  'nullable|string',
                'Active'         =>  'string|nullable',
            ];
            $modeIsUpdate = array_key_exists('id',$request->input());
            if ($modeIsUpdate){
                $arrCheck['id'] = 'integer|min:1|nullable';
            }

            $validator = Validator::make($request->all(), $arrCheck);
            if ($validator->fails()) {
                return response()->json(['errors'=>$validator->errors()->first()]);
            }

            $validated = $validator->validate();
            // check time Project
            if (!$this->StringIsNullOrEmpty($validated['EndDate'])) {
                if($this->compareDate($validated['StartDate'],$validated['EndDate']) == false){
                    return $this->jsonErrors('Ngày kết thúc dự án không hợp lệ');
                }
            }
            $one = !$modeIsUpdate ? new Project() : Project::find($validated['id']);

            foreach($validated as $key => $value){
                if(Schema::hasColumn('projects', $key)){
                    if ($key == 'StartDate') {
                        $value = $this->fncDateTimeConvertFomat($value, 'd/m/Y', self::FOMAT_DB_YMD);
                    }
                    if ($key == 'EndDate' && $value != '') {
                        $value = $this->fncDateTimeConvertFomat($value, 'd/m/Y', self::FOMAT_DB_YMD);
                    }
                    $one->$key = $value;
                }
            }
            isset($validated['Active']) ? $one->Active = 1 : $one->Active = 0;
            $one->Leader = ','.implode(',', $validated['Leader']).',';
            $one->Member = ','.implode(',', $validated['Member']).',';

            $one->save();
            if(!$one){
                return $this->jsonErrors('Lưu thất bại.');
            }else {
                return response()->json(['success' => route('admin.Projects')]);
            }
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function showDetail($oneId = null, $del = null){
        $this->data['users'] = User::query()
            ->select('id', 'FullName')
            ->where('Active', 1)
            ->where('role_group','!=', 1)
            ->get();
        if($oneId!=null){
            if($del == 'del'){
                $one = Project::find($oneId);
                if($one){
                    $one->delete();
                    if (strpos(\Request::getRequestUri(), 'api') !== false) {
                        return response()->json(['success' => 'Xóa thành công.']);
                    }
                }
                return 1;
            }
            $this->data['itemInfo'] = Project::find($oneId);

            if($this->data['itemInfo']){
                if(!is_null($this->data['itemInfo']->Leader)){
                    $this->data['itemInfo']->Leader = explode(',', $this->data['itemInfo']->Leader);
                    $this->data['itemInfo']->Member = explode(',', $this->data['itemInfo']->Member);
                }
                else{
                    $this->data['absenceInfo']->Leader = [];
                    $this->data['absenceInfo']->Member = [];
                }

                return view('admin.includes.project-detail', $this->data);
            }else{
                return "";
            }
        }else{
            return view('admin.includes.project-detail', $this->data);
        }

    }

    /**
     * change active when change checkbox
     * @param $id
     * @param $active
     */
    public function changeCheckboxActive($id, $active){
        if ($id != ''){
            $project = Project::find($id);
            $project->Active = $active;
            $project->save();
        }
    }

    //API

    /**
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showApi(Request $request, $orderBy = 'id', $sortBy = 'desc'){
        $this->authorize('view', $this->view);
        $recordPerPage = $this->getRecordPage();

        if(Schema::hasColumn('projects',$orderBy)){
            $projects = Project::orderBy($orderBy, $sortBy);
        } else {
            $projects = Project::query();
        }

        $data['request'] = $request->query();
        $one = Project::query()->select('NameVi','NameShort','Customer','StartDate','EndDate')->first();
        if($one){
            $one = $one->toArray();
            if(array_key_exists('search', $request->input())){
                $projects = $projects->where(function ($query) use ($one, $request){
                    foreach($one as $key=>$value){
                        $strSearch = trim($this->convert_vi_to_en($request->input('search')));

                        if(in_array($key, ['StartDate', 'EndDate'])){
                            $query->orWhereRaw('(DATE_FORMAT(projects.'.$key.',"%d/%m/%Y")) LIKE ?', '%'.$strSearch.'%' );
                        }else{
                            $query->orWhere('projects.'.$key, 'LIKE', '%'.$strSearch.'%');
                        }
                    }

                });

            }
        }

        $count = $projects->count();
        //phan trang
        $projects = $projects->paginate($recordPerPage);
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc')."/".$query_string;
        parse_str(str_replace('?', '', $query_string), $query_array);

        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        $data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $data['projects'] = $projects;
        $data['query_array'] = $query_array;
        $data['sort_link'] = $sort_link;
        $data['sort'] = $sort;
        $data['role_key'] = 'ProjectManagement';

        return AdminController::responseApi(200, null, null, $data);
    }

    /**
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showDetailApi($id = null){
        $data['users'] = User::query()
            ->select('id', 'FullName')
            ->where('Active', 1)
            ->where('role_group','!=', 1)
            ->get();
        if($id != null){
            $data['itemInfo'] = Project::find($id);

            if($data['itemInfo']){
                if(!is_null($data['itemInfo']->Leader)){
                    $data['itemInfo']->Leader = explode(',', $data['itemInfo']->Leader);
                    $data['itemInfo']->Member = explode(',', $data['itemInfo']->Member);
                } else {
                    $data['absenceInfo']->Leader = [];
                    $data['absenceInfo']->Member = [];
                }
            }
        }
        return AdminController::responseApi(200, null, null, $data);
    }

    /**
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteApi($id = null){
        $one = Project::find($id);
        if($one){
            $one->delete();
            return AdminController::responseApi(200, null, __('admin.success.delete'));
        } else {
            return AdminController::responseApi(422, __('admin.error.data'));
        }
    }

    public function storeApi(Request $request){
        if (count($request->input()) === 0){
            return AdminController::responseApi(422, __('admin.error.data-missing'));
        }
        try{
            $arrCheck = [
                'NameVi'         =>  'required|string|max:100',
                'NameJa'         =>  'nullable|string|max:100',
                'NameShort'      =>  'required|string|max:100|unique:projects,NameShort,id,deleted_at,NULL',
                'Customer'       =>  'required|string|max:100',
                'StartDate'      =>  'required|date_format:d/m/Y',
                'EndDate'        =>  'nullable|date_format:d/m/Y',
                'Leader'         =>  'required|array',
                'Member'         =>  'required|array',
                'Description'    =>  'nullable|string',
                'Active'         =>  'string|nullable',
            ];

            $validator = Validator::make($request->all(), $arrCheck);
            if ($validator->fails()) {
                return AdminController::responseApi(422, $validator->errors()->first());
            }

            $validated = $validator->validate();
            // check time Project
            if (!$this->StringIsNullOrEmpty($validated['EndDate'])) {
                if($this->compareDate($validated['StartDate'],$validated['EndDate']) == false){
                    return AdminController::responseApi(422, __('admin.error.project.end-date'));
                }
            }

            $one = new Project();

            foreach($validated as $key => $value){
                if(Schema::hasColumn('projects', $key)){
                    if ($key == 'StartDate') {
                        $value = $this->fncDateTimeConvertFomat($value, 'd/m/Y', self::FOMAT_DB_YMD);
                    }
                    if ($key == 'EndDate' && $value != '') {
                        $value = $this->fncDateTimeConvertFomat($value, 'd/m/Y', self::FOMAT_DB_YMD);
                    }
                    $one->$key = $value;
                }
            }
            $one->Active = isset($validated['Active']) ? 1 : 0;
            $one->Leader = ','.implode(',', $validated['Leader']).',';
            $one->Member = ','.implode(',', $validated['Member']).',';

            $one->save();
            if(!$one){
                return AdminController::responseApi(403, __('admin.error.save'));
            }else {
                return AdminController::responseApi(200, null, __('admin.success.save'));
            }
        } catch (\Exception $e){
            return AdminController::responseApi(422, $e->getMessage());
        }
    }

    public function updateApi(Request $request, $id = null){
        if (count($request->input()) === 0){
            return AdminController::responseApi(422, __('admin.error.data-missing'));
        }
        try{
            $arrCheck = [
                'NameVi'         =>  'required|string|max:100',
                'NameJa'         =>  'nullable|string|max:100',
                'NameShort'      =>  'required|string|max:100|unique:projects,NameShort,'.$id.',id,deleted_at,NULL',
                'Customer'       =>  'required|string|max:100',
                'StartDate'      =>  'required|date_format:d/m/Y',
                'EndDate'        =>  'nullable|date_format:d/m/Y',
                'Leader'         =>  'required|array',
                'Member'         =>  'required|array',
                'Description'    =>  'nullable|string',
                'Active'         =>  'string|nullable',
                'id'             =>  'integer|min:1|nullable'
            ];

            $validator = Validator::make($request->all(), $arrCheck);
            if ($validator->fails()) {
                return AdminController::responseApi(422, $validator->errors()->first());
            }

            $validated = $validator->validate();
            // check time Project
            if (!$this->StringIsNullOrEmpty($validated['EndDate'])) {
                if($this->compareDate($validated['StartDate'],$validated['EndDate']) == false){
                    return AdminController::responseApi(422, __('admin.error.project.end-date'));
                }
            }
            $one = Project::find($validated['id']);

            foreach($validated as $key => $value){
                if(Schema::hasColumn('projects', $key)){
                    if ($key == 'StartDate') {
                        $value = $this->fncDateTimeConvertFomat($value, 'd/m/Y', self::FOMAT_DB_YMD);
                    }
                    if ($key == 'EndDate' && $value != '') {
                        $value = $this->fncDateTimeConvertFomat($value, 'd/m/Y', self::FOMAT_DB_YMD);
                    }
                    $one->$key = $value;
                }
            }
            $one->Active = isset($validated['Active']) ? 1 : 0;
            $one->Leader = ','.implode(',', $validated['Leader']).',';
            $one->Member = ','.implode(',', $validated['Member']).',';

            $one->save();
            if (!$one){
                return AdminController::responseApi(403, __('admin.error.save'));
            } else {
                return AdminController::responseApi(200, null, __('admin.success.save'));
            }
        } catch (\Exception $e){
            return AdminController::responseApi(422, $e->getMessage());
        }
    }
}
