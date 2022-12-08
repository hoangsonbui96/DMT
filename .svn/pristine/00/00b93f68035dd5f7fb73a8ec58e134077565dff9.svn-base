<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\SendEmail;
use App\Model\Absence;
use App\Exports\AbsencesExport;
use App\Exports\AbsencesReportExport;
use App\MasterData;
use App\Menu;
use App\RoleGroupScreenDetailRelationship;
use App\RoleUserScreenDetailRelationship;
use App\Room;
use App\User;
use App\RoleScreenDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\CalendarEvent;
use Carbon\CarbonInterval;

/**
 * Controller screen Absence
 * Class AbsenceController
 * @package App\Http\Controllers\Admin
 */
class AbsenceControllerOld extends AdminController
{
    protected $startTime = '08:30';
    protected $endTime = '17:30';
    protected $timeOutAm = '12:00';
    protected $timeInPm = '13:00';
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $addM;
    protected $editM;
    protected $app;
    protected $export;


    /**
     * AbsenceController constructor.
     * @param Request $request
     * Check role view, insert, update
     */
    public function __construct(Request $request)
    {
        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
        $this->menu = Menu::query()
            ->where('RouteName', 'admin.Absences')
            ->first();
        $this->add = RoleScreenDetail::query()
            ->where('alias', 'AbsenceListAdd')
            ->first();
        $this->view = RoleScreenDetail::query()
            ->where('alias', 'AbsenceList')
            ->first();
        $this->edit = RoleScreenDetail::query()
            ->where('alias', 'AbsenceListEdit')
            ->first();
        $this->delete = RoleScreenDetail::query()
            ->where('alias', 'AbsenceListDelete')
            ->first();
        $this->export = RoleScreenDetail::query()
            ->where('alias', 'AbsenceListExport')
            ->first();

        $this->addM = RoleScreenDetail::query()
            ->where('alias', 'AbsenceManagementAdd')
            ->first();
        $this->editM = RoleScreenDetail::query()
            ->where('alias', 'AbsenceManagementEdit')
            ->first();
        $this->deleteM = RoleScreenDetail::query()
            ->where('alias', 'AbsenceManagementDelete')
            ->first();

        $this->app = RoleScreenDetail::query()
            ->where('alias', 'ListApprove')
            ->first();
    }

    /**
     * Get data absence
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return View screen (absence)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Request $request, $orderBy = 'SDate', $sortBy = 'desc')
    {
        $this->authorize('view', $this->menu);
        $recordPerPage = $this->getRecordPage();
        $checkRequestManager = Absence::query()->where('RequestManager','like', '%'.Auth::user()->id.'%')->get();
        $absence = $this->getUserWithRequest($request, $orderBy, $sortBy);
        $count = $absence->count();

        //Pagination
        $absence = $absence->paginate($recordPerPage);
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc')."/".$query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);

        //redirect to the last page if current page has no record
        if($absence->count() == 0)
        {
            if(array_key_exists('page', $query_array))
            {
                if($query_array['page'] > 1)
                {
                    $query_array['page'] = $absence->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl  = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }
            }
        }

        $this->data['master_datas'] = $this->getReasonAbsence();
        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $this->data['absence'] = $absence;
        $this->data['checkRequestManager'] = $checkRequestManager;
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['sort'] = $sort;
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;
        $this->data['export'] = $this->export;

        if (strpos(\Request::getRequestUri(), 'api') !== false) {
           return response()->json(['data' => $this->data ]);
        }
        return $this->viewAdminLayout('absence', $this->data);
    }

    /**
     * @param $request
     * @param $orderBy
     * @param $sortBy
     * @param null $export
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder|\Illuminate\Database\Query\Builder[]|\Illuminate\Http\RedirectResponse|\Illuminate\Support\Collection
     * @throws \Exception
     */
    public function getUserWithRequest($request, $orderBy, $sortBy, $export = null){
        //Get list absence
        if(Schema::hasColumn('absences',$orderBy))
        {
            $absence = Absence::query()
                ->select('absences.*','tb1.FullName','master_data.Name','tb2.username as NameUpdateBy')
                ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
                ->leftJoin('users as tb1', 'absences.UID', '=', 'tb1.id')
                ->leftJoin('users as tb2', 'absences.UpdateBy', '=', 'tb2.id')
                ->orderBy($orderBy, $sortBy);
        }else{
            return redirect()->back();
        }

        $this->data['request'] = $request->query();
        // Search in columns
        $one = Absence::query()->select('absences.Reason','absences.Remark','absences.AbsentDate','absences.SDate',
            'absences.EDate','absences.ApprovedDate','users.FullName','master_data.Name','users.username')
            ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
            ->leftJoin('users', 'absences.UID', '=', 'users.id')->first();

        if($one){
            $one = $one->toArray();
            if(array_key_exists('search', $request->input()))
            {
                $absence = $absence->where(function ($query) use ($one, $request){

                    foreach($one as $key=>$value){
                        if($key == 'FullName'){
                            $query->orWhere('tb1.'.$key, 'like', '%'.$request->input('search').'%');
                        }elseif ($key == 'Name'){
                            $query->orWhere('master_data.'.$key, 'like', '%'.$request->input('search').'%');
                        }elseif ($key == 'username'){
                            $query->orWhere('tb1.'.$key, 'like', '%'.$request->input('search').'%');
                        }else{
                            $strSearch = trim($this->convert_vi_to_en($request->input('search')));

                            if(in_array($key, ['SDate','EDate','AbsentDate','ApprovedDate'])){
                                $query->orWhereRaw('(DATE_FORMAT(absences.'.$key.',"%d/%m/%Y")) LIKE ?', '%'.$strSearch.'%' );
                            }else{
                                $query->orWhere('absences.'.$key, 'LIKE', '%'.$strSearch.'%');
                            }
                        }
                    }
                });
            }
        }

        //check value request search
        if ($request->has('Date')) {
            if (\DateTime::createFromFormat('d/m/Y', $request['Date'][0]) === FALSE && $request['Date'][0] != '' ||
                \DateTime::createFromFormat('d/m/Y', $request['Date'][1]) === FALSE && $request['Date'][1] != '') {
                return Redirect::back();
            }
        }
        //Search with condition
        if (!isset($request['UID']) && !isset($request['MasterDataValue']) && !isset($request['Date'])){
            $absence = $absence->where('absences.SDate','>=', Carbon::now()->startOfMonth())
                ->orWhere('absences.EDate','>=', Carbon::now()->startOfMonth());
        }
        if ($request['UID'] != ''){
            $absence = $absence->where('absences.UID', $request['UID']);
        }
        if ($request['MasterDataValue'] != ''){
            $absence = $absence->where('absences.MasterDataValue', 'like', '%'.$request['MasterDataValue'].'%');
        }

        foreach($this->data['request'] as $key => $value)
        {
            if(is_array($value)){
                $value[0] != '' ? $value[0] = $this->fncDateTimeConvertFomat($value[0],
                    self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : $value[0];
                $value[1] != '' ? $value[1] = $this->fncDateTimeConvertFomat($value[1],
                    self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : $value[1];

                $absence->where(function ($query) use ($value){

                    if($value[0] != '' && $value[1] != '' && $value[0] !== $value[1]){
                        $query = $query->whereBetween('absences.SDate', array(Carbon::parse($value[0])->startOfDay(),Carbon::parse($value[1])->endOfDay()))
                        ->orWhereBetween('absences.EDate', array(Carbon::parse($value[0])->startOfDay(),Carbon::parse($value[1])->endOfDay()))
                            ->orWhere(function ($query1) use ($value){
                                $query1->where('absences.SDate','<=', Carbon::parse($value[0])->startOfDay())
                                    ->where('absences.EDate','>=', Carbon::parse($value[1])->endOfDay());
                            });
                    }
                    if($value[0] === $value[1] && $value[0] != ''){
                        $query = $query->whereRaw("CAST(absences.SDate AS DATE) = '$value[0]'");
                    }
                    if ($value[0] != '' && $value[1] == ''){
                        $query = $query->where('absences.SDate','>=', Carbon::parse($value[0])->startOfDay())
                            ->orWhere('absences.EDate','>=', Carbon::parse($value[0])->startOfDay());
                    }
                    if ($value[0] == '' && $value[1] != ''){
                        $query = $query->where('absences.SDate','<=', Carbon::parse($value[1])->startOfDay())
                            ->orWhere('absences.EDate','<=', Carbon::parse($value[1])->startOfDay());
                    }
                });
            }
        }
        if(isset($request['approve']) && $request['approve'] != 'null'){
            $absence = $absence->where('Approved', '!=', $request['approve']);
        }
        if ($export != '' || $export != null){
            return $absence->get();
        }
        return $absence;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Exception
     */
    public function export(Request $request){

        $records = $this->getUserWithRequest($request, 'id', 'desc', 'export');
        if($records->count() > 0){
            return Excel::download(new AbsencesExport($records), 'Danh_sách_nghỉ.xlsx');
        }else{
            return response()->json(['errors' => ['Không có dữ liệu.']]);
        }
    }

    /**
     * Get data absence one user
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return View screen (absence-management)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showAbsenceManagement(Request $request, $orderBy = 'id', $sortBy = 'desc')
    {
        $this->authorize('view', $this->menu);
        $recordPerPage = $this->getRecordPage();

        //Get list absence of users
        if(Schema::hasColumn('absences',$orderBy))
        {
            $absences = Absence::query()->select('absences.*','master_data.Name','tb1.FullName','tb2.username as NameUpdateBy')
                ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
                ->leftJoin('users as tb1', 'absences.UID', '=', 'tb1.id')
                ->leftJoin('users as tb2', 'absences.UpdateBy', '=', 'tb2.id')
                ->where('UID', Auth::user()->id)->orderBy($orderBy, $sortBy);
        }else{
            return redirect()->back();
        }

        //Search in column
        $this->data['request'] = $request->query();
        $one = Absence::query()->select('absences.SDate','absences.EDate','absences.TotalTimeOff','absences.Reason','absences.Remark','master_data.Name')
            ->Join('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
            ->leftJoin('users', 'absences.UID', '=', 'users.id')->first();

        if($one)
        {
            $one = $one->toArray();
            if(array_key_exists('search', $request->input()))
            {
                $absences = $absences->where(function ($query) use ($one, $request){

                    foreach($one as $key=>$value){
                        if ($key == 'Name'){
                            $query->orWhere('master_data.'.$key, 'like', '%'.$request->input('search').'%');
                        }else{
                            if(in_array($key, ['SDate', 'EDate', 'ApprovedDate'])){
                                $query->orWhereRaw('(DATE_FORMAT(absences.'.$key.',"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                            }else{
                                $query->orWhere('absences.'.$key, 'like', '%'.$request->input('search').'%');
                            }
                        }
                    }
                });
            }
        }

        if (!isset($request['search']) && !isset($request['Date']) || strpos(\Request::getRequestUri(), 'api') !== false){
            $absences = $absences->where(function ($queryOne) {
                $queryOne->where('absences.SDate','>=', Carbon::now()->startOfMonth())
                ->orWhere('absences.EDate','>=', Carbon::now()->startOfMonth());
            });
        }

//        dd($absences->toSql());
        foreach($this->data['request'] as $key => $value)
        {
            if(is_array($value)){
                $value[0] != '' ? $value[0] = $this->fncDateTimeConvertFomat($value[0],
                    self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : $value[0];
                $value[1] != '' ? $value[1] = $this->fncDateTimeConvertFomat($value[1],
                    self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : $value[1];

                $absences->where(function ($query) use ($value){

                    if($value[0] != '' && $value[1] != '' && $value[0] !== $value[1]){
                        $query = $query->whereBetween('absences.SDate', array(Carbon::parse($value[0])->startOfDay(),Carbon::parse($value[1])->endOfDay()))
                            ->orWhereBetween('absences.EDate', array(Carbon::parse($value[0])->startOfDay(),Carbon::parse($value[1])->endOfDay()))
                            ->orWhere(function ($query1) use ($value){
                                $query1->where('absences.SDate','<=', Carbon::parse($value[0])->startOfDay())
                                    ->where('absences.EDate','>=', Carbon::parse($value[1])->endOfDay());
                            });
                    }
                    if($value[0] === $value[1] && $value[0] != ''){
                        $query = $query->whereRaw("CAST(absences.SDate AS DATE) = '$value[0]'");
                    }
                    if ($value[0] != '' && $value[1] == ''){
                        $query = $query->where('absences.SDate','>=', Carbon::parse($value[0])->startOfDay())
                            ->orWhere('absences.EDate','>=', Carbon::parse($value[0])->startOfDay());
                    }
                    if ($value[0] == '' && $value[1] != ''){
                        $query = $query->where('absences.SDate','<=', Carbon::parse($value[1])->startOfDay())
                            ->orWhere('absences.EDate','<=', Carbon::parse($value[1])->startOfDay());
                    }
                });
            }
        }

        $count = $absences->count();
        //Pagination
        $absences = $absences->paginate($recordPerPage);
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
        $sort = ($sortBy =='asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);

        //redirect to the last page if current page has no record
        if($absences->count() == 0){
            if(array_key_exists('page', $query_array)){
                if($query_array['page'] > 1){
                    $query_array['page'] = $absences->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl  = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }
            }
        }

        $this->data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $this->data['absences'] = $absences;
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['sort'] = $sort;
        $this->data['add'] = $this->addM;
        $this->data['edit'] = $this->editM;
        $this->data['delete'] = $this->deleteM;

        $absenceTypes = MasterData::query()->where('DataKey','VM')
            ->get();
        $totalReport = [];
        $absences->filter(function($value, $key) use (&$totalReport){
            if(array_key_exists($value->MasterDataValue, $totalReport)){
                $totalReport[$value->MasterDataValue] += $value->TotalTimeOff;
            }else{
                $totalReport[$value->MasterDataValue] = $value->TotalTimeOff;
            }
        });
        // print_r($totalReport);
        $this->data['totalReport'] = $totalReport;
        $this->data['absenceTypes'] = $absenceTypes;

        if (strpos(\Request::getRequestUri(), 'api') !== false) {
            return $this->data;
        }
        return $this->viewAdminLayout('absence-management', $this->data);
    }

    /**
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return View screen (absence-reports)
     */
    public function showReport(Request $request)
    {

        $this->getListAbsencesReport($request);

        $this->data['export'] = RoleScreenDetail::query()->where('alias', 'AbsenceReportsExport')->first();
        return $this->viewAdminLayout('absence-reports', $this->data);
    }

    public function getListAbsencesReport($request){

        $query = Absence::query()->select('absences.*', 'users.FullName')
            ->join('users', 'absences.UID', '=', 'users.id')
            ->groupBy('absences.UID')
            ->orderBy('absences.UID', 'desc');

        $this->data['request'] = $request->query();

        if(!$request->has('UID') && !$request->has('date')){
            $query = $query->where(function($query1){
                $query1->whereBetween('absences.SDate',
                        array(
                            Carbon::now()->startOfMonth(),
                            Carbon::now()->endOfMonth()
                        )
                    )->orWhereBetween('absences.EDate',
                        array(
                            Carbon::now()->startOfMonth(),
                            Carbon::now()->endOfMonth()
                        )
                    );
            });
        }

        if ($request['UID'] != ''){
            $query = $query->where('absences.UID', $request['UID']);
        }
        foreach($this->data['request'] as $key => $value)
        {
            if(is_array($value))
            {
                $value[0] != '' ? $value[0] = $this->fncDateTimeConvertFomat($value[0],
                    self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : '';
                $value[1] != '' ? $value[1] = $this->fncDateTimeConvertFomat($value[1],
                    self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : '';

                $query->where(function ($queryFirst) use ($value){

                    if($value[0] != '' && $value[1] != '' && $value[0] !== $value[1]){
                        $queryFirst = $queryFirst->whereBetween(
                                'absences.SDate',
                                array(
                                    Carbon::parse($value[0])->startOfDay(),
                                    Carbon::parse($value[1])->startOfDay()
                                )
                            )
                            ->orWhereBetween(
                                'absences.EDate',
                                array(
                                    Carbon::parse($value[0])->startOfDay(),
                                    Carbon::parse($value[1])->startOfDay()
                                )
                            );
                    }
                    if($value[0] === $value[1] && $value[0] != ''){
                        $queryFirst = $queryFirst->whereRaw("CAST(absences.SDate AS DATE) = '$value[0]'");
                    }
                    if ($value[0] != '' && $value[1] == ''){
                        $queryFirst = $queryFirst->where('absences.SDate','>=', Carbon::parse($value[0])->startOfDay())
                            ->orWhere('absences.EDate','>=', Carbon::parse($value[0])->startOfDay());
                    }
                    if ($value[0] == '' && $value[1] != ''){
                        $queryFirst = $queryFirst->where('absences.SDate','<=', Carbon::parse($value[1])->startOfDay())
                            ->orWhere('absences.EDate','<=', Carbon::parse($value[1])->startOfDay());
                    }
                });
            }
        }

        $query = $query->get();
        $this->data['master_datas'] = $this->getReasonAbsence();

        foreach($query as $item){
            $hours = array();
            $times = array();
            foreach($this->data['master_datas'] as $value)
            {
                $getDataReport = Absence::query()->where('UID', $item->UID)
                    ->where('MasterDataValue', $value->DataValue)->where('absences.Approved', '!=',2);
                if(!$request->has('UID') && !$request->has('date')){
                    $getDataReport = $getDataReport->where(function($query1){
                        $query1->whereBetween('absences.SDate',
                                array(
                                    Carbon::now()->startOfMonth(),
                                    Carbon::now()->endOfMonth()
                                )
                            )->orWhereBetween('absences.EDate',
                                array(
                                    Carbon::now()->startOfMonth(),
                                    Carbon::now()->endOfMonth()
                                )
                            );
                    });
                }

                if ($request['UID'] != ''){
                    $getDataReport = $getDataReport->where('absences.UID', $request['UID']);
                }

                foreach($this->data['request'] as $key => $valueR)
                {
                    if(is_array($valueR))
                    {
                        $valueR[0] != '' ? $valueR[0] = $this->fncDateTimeConvertFomat($valueR[0],
                            self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : '';
                        $valueR[1] != '' ? $valueR[1] = $this->fncDateTimeConvertFomat($valueR[1],
                            self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : '';

                        $getDataReport->where(function ($queryFirst) use ($valueR){

                            if($valueR[0] != '' && $valueR[1] != '' && $valueR[0] !== $valueR[1]){
                                $queryFirst = $queryFirst->whereBetween('absences.SDate', array($valueR[0],$valueR[1]))
                                    ->orWhereBetween('absences.EDate', array($valueR[0],$valueR[1]));
                            }
                            if($valueR[0] === $valueR[1] && $valueR[0] != ''){
                                $queryFirst = $queryFirst->whereRaw("CAST(absences.SDate AS DATE) = '$valueR[0]'");
                            }
                            if ($valueR[0] != '' && $valueR[1] == ''){
                                $queryFirst = $queryFirst->where('absences.SDate','>=', Carbon::parse($valueR[0])->startOfDay())
                                    ->orWhere('absences.EDate','>=', Carbon::parse($valueR[0])->startOfDay());
                            }
                            if ($valueR[0] == '' && $valueR[1] != ''){
                                $queryFirst = $queryFirst->where('absences.SDate','<=', Carbon::parse($valueR[1])->startOfDay())
                                    ->orWhere('absences.EDate','<=', Carbon::parse($valueR[1])->startOfDay());
                            }
                        });
                    }
                }

                //tính tổng giờ nghỉ
                $hours[] = $getDataReport->sum('TotalTimeOff');
                $item->hours = $hours;
                //đếm số lần nghỉ
                $times[] = $getDataReport->get()->count();
                $item->times = $times;
            }
        }
        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['excel'] = RoleScreenDetail::query()->where('alias', 'AbsenceListExport')->first();
        $this->data['absence_report'] = $query;
        return $this->data;
    }

    /**
     * export excel of Absences Report
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function absencesReportExport(Request $request){

        $records = $this->getListAbsencesReport($request);

        if(isset($records['absence_report'][0])){
            return Excel::download(new AbsencesReportExport($records), 'BaoCaoTongHopVangMat.xlsx');
        }else{
            return response()->json(['errors' => ['Không có dữ liệu.']]);
        }
    }

    /**
     * @param null $id
     * @param null $del
     * @return View popup (absence-detail)
     */
    public function showDetail($id = null, $del = null)
    {
        $role_group_user = RoleGroupScreenDetailRelationship::query()
            ->select('role_group_id')->where('screen_detail_alias','ListApprove')->get();
        $role_group_user = $role_group_user->toArray();

        $request_manager = RoleUserScreenDetailRelationship::query()
            ->select('user_id','FullName')
            ->join('users','users.id','=','role_user_screen_detail_relationships.user_id')
            ->where('screen_detail_alias', '=','ListApprove');

        foreach ($role_group_user as $key => $value){
            $group_user = User::query()->select('id as user_id', 'FullName')->where('role_group', $value['role_group_id']);
//            $request_manager = $request_manager->union($group_user);
        }

        $this->data['request_manager'] = $request_manager->get();
        $this->data['rooms'] = Room::query()->select('id','Name')
            ->where('MeetingRoomFlag','!=',1)->where('Active',1)->get();
        $this->data['master_datas'] = $this->getReasonAbsence();
        $this->data['boolean'] = 1;
        $this->data['userLogged'] = User::find(Auth::user()->id);
        $this->data['roomUser'] = Room::find(Auth::user()->RoomId);
        $this->data['add'] = $this->add;

        if($id != null){
            $this->data['absenceInfo'] = Absence::find($id);
            if($del == 'del')
            {
                $one = Absence::find($id);
                if($one != null){
                    $one->delete();
                    if (strpos(\Request::getRequestUri(), 'api') !== false) {
                        return response()->json(['success' => 'Xóa thành công.']);
                    }
                }
                return 1;
            }
            if($this->data['absenceInfo'])
            {
                if(!is_null($this->data['absenceInfo']->RequestManager))
                {
                    $this->data['absenceInfo']->RequestManager = explode(',', $this->data['absenceInfo']->RequestManager);
                    $this->data['boolean'] = 2;
                }else{
                    $this->data['absenceInfo']->RequestManager = [];
                }
                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                    return $this->data;
                }
                return $this->viewAdminIncludes('absence-detail', $this->data);
            }else{
                return "";
            }
        }else{
            if (strpos(\Request::getRequestUri(), 'api') !== false) {
                return $this->data;
            }
            return $this->viewAdminIncludes('absence-detail', $this->data);
        }
    }

    /**
     * @param Request $request
     * @param null $id
     * @return string|void
     */
    public function store(Request $request, $id = null){
        if (count($request->input()) === 0){
            return abort('404');
        }
        try{
            $arrCheck = [
                'RoomID'               =>  'required|integer|min:1',
                'UID'                  =>  'required|integer|min:1',
                'MasterDataValue'      =>  'required|string',
                'SDate'                =>  'required|date_format:d/m/Y H:i',
                'EDate'                =>  'required|date_format:d/m/Y H:i',
                'Reason'               =>  'required|string',
                'Remark'               =>  'string|nullable',
                'RequestManager'       =>  'required|array',
                'AbsentDate'           =>  'nullable',
                'TotalTimeOff'         =>  'nullable|integer',
            ];
            $modeIsUpdate = array_key_exists('id', $request->input());

            if ($modeIsUpdate){
                $arrCheck['id'] = 'integer|min:1';
            }

            $validator = Validator::make($request->all(), $arrCheck);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()->first()]);
            }

            $validated = $validator->validate();

            $one = !$modeIsUpdate ? new Absence() : Absence::find($validated['id']);

            foreach($validated as $key => $value){
                if(Schema::hasColumn('absences', $key))
                {
                    if ($key == 'SDate'){
                        $value = $this->fncDateTimeConvertFomat($value, 'd/m/Y H:i', 'Y-m-d H:i');
                    }
                    if ($key == 'EDate'){
                        $value = $this->fncDateTimeConvertFomat($value, 'd/m/Y H:i', 'Y-m-d H:i');
                    }
                    $one->$key = $value;
                }
            }

            // Check valid vacation time
            $S = $this->fncDateTimeConvertFomat($validated['SDate'], 'd/m/Y H:i', 'Y-m-d H:i');
            $E = $this->fncDateTimeConvertFomat($validated['EDate'], 'd/m/Y H:i', 'Y-m-d H:i');
            if (Carbon::parse($S)->gt(Carbon::parse($E))){
                return $this->jsonErrors('Ngày nghỉ không hợp lệ');
            }

            $one->TotalTimeOff = $this->getDiffHours(Carbon::parse($one->SDate), Carbon::parse($one->EDate), $validated['UID'])*60;

            // nếu xin nghỉ vào cuối tuần
            if ($this->checkHoliday(Carbon::parse($S)) && $this->checkHoliday(Carbon::parse($E))){
                return $this->jsonErrors('Không xin nghỉ vào ngày nghỉ.');
            }
            if ($one->TotalTimeOff < 5){
                return $this->jsonErrors('Thời gian nghỉ tối thiểu là 5 phút');
            }

            $one->RequestManager = ','.implode(',', $validated['RequestManager']).',';
            $one->AbsentDate = Carbon::now()->format('Y-m-d');
            $one->save();

            if(!$one){
                return $this->jsonErrors('Lưu không thành công');
            }else{
                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                    return response()->json(['success' => 'Lưu thành công.']);
                }
                $header = 'Kính gửi ban Giám đốc';
                $this->sendMail($validated,$header);

                return $this->jsonSuccessWithRouter('admin.AbsenceManagement');
            }

        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Send data mail to serve
     * @param $array
     * @param $header
     * @param null $comment
     * @return bool
     */
    public function sendMail($array,$header,$comment = null){
        //get data
        $rooms = Room::find($array['RoomID']);
        $users = User::find($array['UID']);

        //kiểm tra ngày trong quá khứ
        if (!isset($array['Approved'])){
            $End = $this->fncDateTimeConvertFomat($array['EDate'], 'd/m/Y H:i', 'Y-m-d');
            if (Carbon::parse($End)->lt(Carbon::now()->format('Y-m-d'))){
                return false;
            }
        }

        //sửa lich nghỉ
        if (isset($array['Approved'])){
            if ( Carbon::parse($array['EDate'])->lt(Carbon::now()->format('Y-m-d'))){
                return false;
            }
            //format date for subjectMail
            $array['SDate'] = $this->fncDateTimeConvertFomat($array['SDate'], 'Y-m-d H:i:s', 'd/m/Y H:i');
            $array['EDate'] = $this->fncDateTimeConvertFomat($array['EDate'], 'Y-m-d H:i:s', 'd/m/Y H:i');
        }

        $arrMail = [];
        $arrMailCc = [];
        $mailOfUser = $users['email'];

        if (!is_array($array['RequestManager'])){
            $array['RequestManager'] = array_filter(explode(',', $array['RequestManager']));
        }

        $arrayMailCc = MasterData::query()->where('DataValue', 'EM001')->get();
        $mailCc = array_filter(explode(',', $arrayMailCc[0]['DataDescription']));

        foreach ($array['RequestManager'] as $value){
            $mailUser = User::find($value);
            $arrMailAddressTo = $mailUser->email;

            $arrMail[] = $arrMailAddressTo;
        }

        $arrMail[] = $mailOfUser;
        $arrMailAddressTo = array_unique($arrMail);

        foreach ($mailCc as $value){
            $arrMailCc[] = $value;
        }

        $addressMailCc = array_diff($arrMailCc,$arrMailAddressTo);
        $viewBladeMail = 'template_mail.absences-mail';
        $apr = isset($array['Approved']) ? $array['Approved'] : '';

        if ($array['MasterDataValue'] == 'VM007'){
            $master_data_name = 'vắng mặt';
        }else{
            $master_data = MasterData::query()->where('DataValue', $array['MasterDataValue'])->first()->toArray();
            $master_data_name = $master_data['Name'];
        }

        //cách gọi Mr,Ms,Mrs
        if ($users['Gender'] == 0){
            $users['Gender'] = 'Mr';
        }
        if ($users['Gender'] == 1 && $users['MaritalStt'] == 1){
            $users['Gender'] = 'Mrs';
        }
        if ($users['Gender'] == 1 && $users['MaritalStt'] == 0){
            $users['Gender'] = 'Ms';
        }

        //gộp thời gian nếu cùng ngày
        $dateStart = $this->fncDateTimeConvertFomat($array['SDate'], 'd/m/Y H:i', 'Y-m-d H:i:s');
        $dateEnd = $this->fncDateTimeConvertFomat($array['EDate'], 'd/m/Y H:i', 'Y-m-d H:i:s');
        $diffDay = Carbon::parse($dateStart)->diffInDays(Carbon::parse($dateEnd));

        $viewDay = $array['SDate'].' - '.$array['EDate'];
        if ($diffDay == 0){
            $viewDay = Carbon::parse($dateStart)->format('d/m/Y').' '.Carbon::parse($dateStart)->format('H:i').'-'.Carbon::parse($dateEnd)->format('H:i');
        }

        //chuyển sang viết thường
        $master_data_name = mb_strtolower($master_data_name, 'UTF-8');

        $dataBinding = [
            'Header'                => $header,
            'MasterDataValue'       => $master_data_name,
            'FullName'              => $users['FullName'],
            'Room'                  => $rooms['Name'],
            'viewDay'               => $viewDay,
            'Reason'                => $array['Reason'],
            'Remark'                => $array['Remark'],
            'Gender'                => $users['Gender'],
            'Approved'              => $apr,
            'Comment'               => $comment,
        ];

        //tiêu đề mail (subjectMail)
        if (isset($array['Approved'])){
            $nameFrom = 'AKB Văn Phòng';
            if ($array['Approved'] == 1){
                $subjectMail = 'TB '.$users['FullName'].' '.mb_strtolower($master_data_name, 'UTF-8').' ('.$viewDay.')';
            }else{
                $subjectMail = 'TB từ chối '.mb_strtolower($master_data_name, 'UTF-8').' ('.$viewDay.')';
            }
        }else{
            $nameFrom = $users['FullName'] . ' - Phòng ' . $rooms['Name'];
            $subjectMail = 'TB xin '.mb_strtolower($master_data_name, 'UTF-8').' ('.$viewDay.')';
        }

//        $this->SendMailWithView([
//            self::KEY_SUBJECT_MAIL      => $subjectMail,
//            self::KEY_VIEW_MAIL         => $viewBladeMail,
//            self::KEY_DATA_BINDING      => $dataBinding,
//            self::KEY_MAIL_NAME_FROM    => $nameFrom,
//            self::KEY_MAIL_ADDRESS_TO   => $arrMailAddressTo,
//            self::KEY_MAIL_ADDRESS_CC   => $addressMailCc,
//        ]);
        $this->attr_mail_view[self::KEY_SUBJECT_MAIL] = $subjectMail;
        $this->attr_mail_view[self::KEY_VIEW_MAIL] = $viewBladeMail;
        $this->attr_mail_view[self::KEY_DATA_BINDING] = $dataBinding;
        $this->attr_mail_view[self::KEY_MAIL_NAME_FROM] = $nameFrom;
        $this->attr_mail_view[self::KEY_MAIL_ADDRESS_TO] = $arrMailAddressTo;
        $this->attr_mail_view[self::KEY_MAIL_ADDRESS_CC] = $addressMailCc;
        SendEmail::dispatch("send_view", $this->attr_mail_view)->delay(now()->addMinute());
    }

    /**
     * Danh sách duyệt vắng mặt
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return View screen (absence-list-approve)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showListApprove(Request $request, $orderBy = 'id', $sortBy = 'desc')
    {
        $recordPerPage = $this->getRecordPage();
        $this->authorize('view', $this->menu);

        //Get list absences approve = 0
        if(Schema::hasColumn('absences',$orderBy)){
            $absence = Absence::query()->select('absences.*','tb1.FullName','master_data.Name','tb2.FullName as NameUpdateBy','rooms.Name as RoomName')
                ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
                ->leftJoin('users as tb1', 'absences.UID', '=', 'tb1.id')
                ->leftJoin('users as tb2', 'absences.UpdateBy', '=', 'tb2.id')
                ->leftJoin('rooms', 'absences.RoomID', '=', 'rooms.id')
                ->where('Approved',0)->where('RequestManager','like', '%'.Auth::user()->id.'%')
                ->orderBy($orderBy, $sortBy);

            //Get user has role approve
            $checkRequestManager = Absence::query()->where('RequestManager','like', '%'.Auth::user()->id.'%')->get();
        }else{
            return redirect()->back();
        }

        //Search in columns
        $this->data['request'] = $request->query();
        $one = Absence::query()
            ->select('absences.Reason','absences.Remark','users.FullName','master_data.Name','rooms.Name')
            ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
            ->leftJoin('users', 'absences.UID', '=', 'users.id')
            ->leftJoin('rooms', 'absences.RoomID', '=', 'rooms.id')->first();

        if($one) {
            $one = $one->toArray();
            if(array_key_exists('search', $request->input())){
                $absence = $absence->where(function ($query) use ($one, $request){
                    foreach($one as $key => $value){
                        if($key == 'FullName'){
                            $query->orWhere('tb1.'.$key, 'like', '%'.$request->input('search').'%');
                        }elseif ($key == 'Name'){
                            $query->orWhere('master_data.'.$key, 'like', '%'.$request->input('search').'%')
                            ->orWhere('rooms.'.$key, 'like', '%'.$request->input('search').'%');
                        }else{
                            if (in_array($key, ['SDate', 'EDate', 'AbsentDate'])){
                                $query->orWhereRaw('(DATE_FORMAT(absences.'.$key.',"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                            }else{
                                $query->orWhere('absences.'.$key, 'like', '%'.$request->input('search').'%');
                            }
                        }
                    }
                });
            }
        }

        $count = $absence->count();

        //Pagination
        $absence = $absence->paginate($recordPerPage);
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
        $sort = ($sortBy=='asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);

        //redirect to the last page if current page has no record
        if($absence->count() == 0)
        {
            if(array_key_exists('page', $query_array))
            {
                if($query_array['page'] > 1)
                {
                    $query_array['page'] = $absence->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl  = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }
            }
        }

        $this->data['master_datas'] = $this->getReasonAbsence();
        $this->data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $this->data['absence'] = $absence;
        $this->data['checkRequestManager'] = $checkRequestManager;
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['sort'] = $sort;
        $this->data['app'] = $this->app;

        if (strpos(\Request::getRequestUri(), 'api') !== false) {
            return $this->data;
        }
        return $this->viewAdminLayout('absence-list-approve', $this->data);
    }

    /**
     * Duyệt/từ chối lịch nghỉ
     * @param Request $request
     * @param null $id
     * @param null $del
     * @return int|string
     */
    public function AprAbsence(Request $request, $id = null, $del = null){
        $this->data['request'] = $request->query();
        if($id != null){
            $one = Absence::find($id);
            //hủy lịch nghỉ
            if($del == 'del'){
                if($one){
                    $one->Approved = 2;
                    $one->UpdateBy = Auth::user()->id;
                    $one->ApprovedDate = Carbon::now();
                    $one->Comment = $this->data['request']['Comment'];
                    if ($this->data['request']['Comment'] == ''){
                        return $this->jsonErrors('Vui lòng điền lý do');
                    }
                    $one->save();
                    $header = '';
                    $this->sendMail($one,$header,$this->data['request']['Comment']);
                    if (strpos(\Request::getRequestUri(), 'api') !== false) {
                        return response()->json(['success' => 'Đã hủy lịch vắng mặt.']);
                    }
                }
                return 1;
            }
            //duyệt lịch nghỉ
            if($one){
                $one->Approved = 1;
                $one->UpdateBy = Auth::user()->id;
                $one->ApprovedDate = Carbon::now();
                $one->save();

                $header = 'Gửi anh/chị/em trong công ty';
                $this->sendMail($one,$header);
                return $this->jsonSuccess('Duyệt thành công');
            }else{
                return $this->jsonErrors('Duyệt thất bại');
            }
        }else{
            return $this->viewAdminIncludes('absence-detail', $this->data);
        }
    }

    /**
     * @return View (unapprove-detail)
     */
    public function showDetailUnapprove(){
        return $this->viewAdminIncludes('unapprove-detail', $this->data);
    }

    /**
     * @param $date
     * @return bool
     */
    public function checkHoliday($date) {
        //check weekend
        if($date->isWeekend()){
            //kiem tra xem co phải ngày làm bù ko
            $queryOne = CalendarEvent::query()
                ->where('StartDate', '<=', $date->toDateString())
                ->where('EndDate', '>=', $date->toDateString())
                ->where('Type', 0)
                ->where('CalendarID', 1)
                ->first();
            return $queryOne ? false : true;

        }else{
            //kiểm tra xem có phải ngày nghỉ lễ ko
            $queryOne = CalendarEvent::query()
                ->where('StartDate', '<=', $date->toDateString())
                ->where('EndDate', '>=', $date->toDateString())
                ->where('Type', '!=', 0)
                ->where('CalendarID', 1)
                ->first();
            return $queryOne ? true : false;
        }
    }

    public function getDiffHours($from,$to, $userId) {
        $user = User::find($userId);
        // $startTime = Carbon::parse($this->startTime);
        // $endTime = Carbon::parse($this->endTime);
        $startTime = !is_null($user->STimeOfDay) ? Carbon::parse($user->STimeOfDay) : Carbon::parse($this->startTime);
        $endTime = !is_null($user->ETimeOfDay) ? Carbon::parse($user->ETimeOfDay) : Carbon::parse($this->endTime);
        if(!isset($to))
        {
            $to = Carbon::now('UTC');
        }
        if($to->format('H:i') <= $startTime->format('H:i')){
            $to->addDays(-1);
            $to->hour = $endTime->hour;
            $to->minute = $endTime->minute;
            $to->second = 0;
        }
        if($to->format('H:i') > $endTime->format('H:i')){
            $to->hour = $endTime->hour;
            $to->minute = $endTime->minute;
            $to->second = 0;
        }
        if($from->format('H:i') >= $endTime->format('H:i')){
            $from->addDays(1);
            $from->hour = $startTime->hour;
            $from->minute = $startTime->minute;
            $from->second = 0;
        }
        if($from->format('H:i') < $startTime->format('H:i')) {
            $from->hour = $startTime->hour;
            $from->minute = $startTime->minute;
            $from->second = 0;
        }
        if($from->format('H:i') >= $this->timeOutAm && $from->format('H:i') < $this->timeInPm){
            $from->hour = Carbon::parse($this->timeInPm)->hour;
            $from->minute = Carbon::parse($this->timeInPm)->minute;
            $from->second = 0;
        }
        if($to->format('H:i') > $this->timeOutAm && $to->format('H:i') <= $this->timeInPm){
            $to->hour = Carbon::parse($this->timeOutAm)->hour;
            $to->minute = Carbon::parse($this->timeOutAm)->minute;
            $to->second = 0;
        }
        //nếu ngày bắt đầu rơi vào ngày nghỉ
        $nextWorkingDay = $this->getNextWorkingDays(1, $from->toDateString())[0];
        if($from->toDateString() != $nextWorkingDay){
            $from = Carbon::parse($nextWorkingDay);
            $from->hour = $startTime->hour;
            $from->minute = $startTime->minute;
            $from->second = 0;
        }
        //Nếu ngày kết thúc rơi vào ngày nghỉ
        $prevWorkingDay = $this->getNextWorkingDays(1, $to->toDateString(), -1)[0];
        if($to->toDateString() != $prevWorkingDay){
            $to = Carbon::parse($prevWorkingDay);
            $to->hour = $endTime->hour;
            $to->minute = $endTime->minute;
            $to->second = 0;
        }


        // if(
        //    ($from->format('H:i') < $this->timeOutAm && $to->format('H:i') >= $this->timeInPm)
        //    || ($from->format('H:i') < $this->timeOutAm && $from->day != $to->day)
        //    || ($to->format('H:i') > $this->timeInPm && $from->day != $to->day)
        //    || ($from->hour == $to->hour && $from->day == $to->day && $from->minute == $to->minute)
        // ){
        //    $minus = Carbon::parse($this->timeInPm)->diffInHours($this->timeOutAm);
        // }else{
        //    $minus = 0;
        // }
        $minus = Carbon::parse($this->timeInPm)->diffInHours($this->timeOutAm);
        if($from->gte($to))
            return 0;

        $diffDays = $to->diffInDaysFiltered(function($date){return !$this->checkHoliday($date); }, $from) - 1;
        if($diffDays < 0) $diffDays == 0;

        $weekends = $to->diffInDaysFiltered(function($date){ return $this->checkHoliday($date); }, $from);//Weekends or holidays
        $finalDiff = $diffDays * ($endTime->diffInHours($startTime) - $minus);
        if($diffDays > 0)
        $from->addDays($diffDays+$weekends);

        $diffHours = $to->diffFiltered(CarbonInterval::hour(), function(Carbon $date) use ($startTime, $endTime) {
            // print_r($date->hour.'/');
            if($this->checkHoliday($date)){
                return false;
            }

            if($date->hour > $startTime->hour && $date->hour <= $endTime->hour)
            return true;
            else return false;

        }, $from, true);

        if($from->hour != $startTime->hour){
            $diffHours -= 1;
        }
        if(($from->hour > $to->hour || ($from->hour == $to->hour && $from->minute >= $to->minute)) && ($from->hour < 12 || $to->hour >= 13)){
            $diffHours -= 1;
        }
        $finalDiff += $diffHours;
        if($to->minute > $from->minute){
            $correct = ($to->minute - $from->minute)/60;
        }else{
            $correct = ($to->minute + 60 - $from->minute)/60;
        }

        if(
        (
            $from->format('H:i') < $this->timeOutAm && $to->format('H:i') > $this->timeInPm)
        ){
            $minus = Carbon::parse($this->timeInPm)->diffInHours($this->timeOutAm);
        }else{
            $minus = 0;
        }

        $finalDiff += $correct - $minus;
        // echo $finalDiff;
        return $finalDiff;

    }
    protected $working_days = [1,2,3,4,5];

    //lấy danh sách các ngày làm việc tiếp theo
    public function getNextWorkingDays($intNum, $datetime, $sub = 1){

        $intCount = 0;

        $arrDates = [];
        while($intCount < $intNum){
            $weekDay = Carbon::parse($datetime)->dayOfWeek;
            //Truong hop neu la ngay nghi cuoi tuan
            if(!in_array($weekDay, $this->working_days)){
                //kiem tra xem co phai ngay lam bu khong
                $queryOne = CalendarEvent::query()
                    ->where('StartDate', '<=', Carbon::parse($datetime)->toDateString())
                    ->where('EndDate', '>=', Carbon::parse($datetime)->toDateString())
                    ->where('Type', 0)
                    ->where('CalendarID', 1)
                    ->first();
                if($queryOne){
                    $arrDates[] = Carbon::parse($datetime)->toDateString();
                    $intCount++;
                }
            }else{
                //kiem tra xem ngay hien tai co phai ngay le hay ngay nghi khong
                $queryOne = CalendarEvent::query()
                    ->where('StartDate', '<=', Carbon::parse($datetime)->toDateString())
                    ->where('EndDate', '>=', Carbon::parse($datetime)->toDateString())
                    ->where('Type', '!=', 0)
                    ->where('CalendarID', 1)
                    ->first();
                if(!$queryOne){
                    $arrDates[] = Carbon::parse($datetime)->toDateString();
                    $intCount++;
                }
            }
            $datetime = Carbon::parse($datetime)->addDays($sub);
        }
        return $arrDates;
    }
}
