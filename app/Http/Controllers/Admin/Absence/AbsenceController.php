<?php

namespace App\Http\Controllers\Admin\Absence;

use App\Model\Absence;
use App\Http\Requests\AbsenceRequest;
use App\Exports\AbsencesExport;
use App\Exports\AbsencesReportExport;
use App\Exports\AbsencesTimekeepingExport;
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
use Maatwebsite\Excel\Facades\Excel;
use App\CalendarEvent;
use Carbon\CarbonInterval;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\NotificationController;
use Illuminate\Support\Facades\DB;
use App\Timekeeping;
use App\TimekeepingNew;
use Carbon\CarbonPeriod;

/**
 * Controller screen Absence
 * Class AbsenceController
 * @package App\Http\Controllers\Admin\Absence
 */
class AbsenceController extends AdminController
{
    protected $startTime = '08:30';
    protected $endTime = '17:30';
    protected $timeOutAm = '12:00';
    protected $timeInPm = '13:00';
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $viewM;
    protected $addM;
    protected $editM;
    protected $deleteM;
    protected $viewAppr;
    protected $app;
    protected $export;
    protected $user;
    protected $working_days = [1, 2, 3, 4, 5];
    const KEYMENU = array(
        "add" => "AbsenceListAdd",
        "view" => "AbsenceList",
        "edit" => "AbsenceListEdit",
        "delete" => "AbsenceListDelete",
        "export" => "AbsenceListExport",
        "addM" => "AbsenceManagementAdd",
        "viewM" => "AbsenceManagement",
        "editM" => "AbsenceManagementEdit",
        "deleteM" => "AbsenceManagementDelete",
        "viewAppr" => "AbsenceListApprove",
        "app" => "ListApprove"
    );

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
        $array = $this->RoleView('Absences', ['AbsenceList', 'AbsenceManagement', 'AbsenceListApprove']);
        $this->menu = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if ($value == $row->alias)
                    $this->$key = $row;
            }
        }
        $arrayM = $this->RoleView('AbsenceManagement', []);
        $this->menuM = $arrayM['menu'];
        $this->user = $this->GetListUser(self::USER_ACTIVE_FLAG);
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
        $checkRequestManager = Absence::query()->where('RequestManager', 'like', '%' . Auth::user()->id . '%')->get();
        $absence = $this->getUserWithRequest($request, $orderBy, $sortBy);
        $count = $absence->count();

        //Pagination
        $absence = $absence->paginate($recordPerPage);
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);

        //redirect to the last page if current page has no record
        if ($absence->count() == 0) {
            if (array_key_exists('page', $query_array)) {
                if ($query_array['page'] > 1) {
                    $query_array['page'] = $absence->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl = $request->url() . '?' . $query_string;
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
        $this->data['appr'] = $this->app;

        return $this->viewAdminLayout('absence.absence', $this->data);
    }

    /**
     * @param $request
     * @param $orderBy
     * @param $sortBy
     * @param null $export
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder|\Illuminate\Database\Query\Builder[]|\Illuminate\Http\RedirectResponse|\Illuminate\Support\Collection
     * @throws \Exception
     */
    public function getUserWithRequest($request, $orderBy, $sortBy, $export = null)
    {
        //Get list absence
        if (Schema::hasColumn('absences', $orderBy)) {
            $absence = Absence::query()
                ->select('absences.*', 'tb1.FullName', 'master_data.Name', 'tb2.username as NameUpdateBy')
                ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
                ->leftJoin('users as tb1', 'absences.UID', '=', 'tb1.id')
                ->leftJoin('users as tb2', 'absences.UpdateBy', '=', 'tb2.id')
                ->orderBy($orderBy, $sortBy);
        } else {
            return redirect()->back();
        }

        $this->data['request'] = $request->query();
        // Search in columns
        $one = Absence::query()->select(
            'absences.Reason',
            'absences.Remark',
            'absences.AbsentDate',
            'absences.SDate',
            'absences.EDate',
            'absences.ApprovedDate',
            'users.FullName',
            'master_data.Name',
            'users.username'
        )
            ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
            ->leftJoin('users', 'absences.UID', '=', 'users.id')->first();

        if ($one) {
            $one = $one->toArray();
            if (array_key_exists('search', $request->input())) {
                $absence = $absence->where(function ($query) use ($one, $request) {

                    foreach ($one as $key => $value) {
                        if ($key == 'FullName') {
                            $query->orWhere('tb1.' . $key, 'like', '%' . $request->input('search') . '%');
                        } elseif ($key == 'Name') {
                            $query->orWhere('master_data.' . $key, 'like', '%' . $request->input('search') . '%');
                        } elseif ($key == 'username') {
                            $query->orWhere('tb1.' . $key, 'like', '%' . $request->input('search') . '%');
                        } else {
                            $strSearch = trim($this->convert_vi_to_en($request->input('search')));

                            if (in_array($key, ['SDate', 'EDate', 'AbsentDate', 'ApprovedDate'])) {
                                $query->orWhereRaw('(DATE_FORMAT(absences.' . $key . ',"%d/%m/%Y")) LIKE ?', '%' . $strSearch . '%');
                            } else {
                                $query->orWhere('absences.' . $key, 'LIKE', '%' . $strSearch . '%');
                            }
                        }
                    }
                });
            }
        }

        //check value request search
        if ($request->has('Date')) {
            if (
                \DateTime::createFromFormat('d/m/Y', $request['Date'][0]) === FALSE && $request['Date'][0] != '' ||
                \DateTime::createFromFormat('d/m/Y', $request['Date'][1]) === FALSE && $request['Date'][1] != ''
            ) {
                return Redirect::back();
            }
        }
        //Search with condition
        if (!isset($request['UID']) && !isset($request['MasterDataValue']) && !isset($request['Date'])) {
            $absence = $absence->where('absences.SDate', '>=', Carbon::now()->startOfMonth())
                ->orWhere('absences.EDate', '>=', Carbon::now()->startOfMonth());
        }
        if ($request['UID'] != '') {
            $absence = $absence->where('absences.UID', $request['UID']);
        }
        if ($request['MasterDataValue'] != '') {
            $absence = $absence->where('absences.MasterDataValue', 'like', '%' . $request['MasterDataValue'] . '%');
        }

        foreach ($this->data['request'] as $key => $value) {
            if (is_array($value)) {
                $value[0] != '' ? $value[0] = $this->fncDateTimeConvertFomat(
                    $value[0],
                    self::FOMAT_DISPLAY_DMY,
                    self::FOMAT_DB_YMD
                ) : $value[0];
                $value[1] != '' ? $value[1] = $this->fncDateTimeConvertFomat(
                    $value[1],
                    self::FOMAT_DISPLAY_DMY,
                    self::FOMAT_DB_YMD
                ) : $value[1];

                $absence->where(function ($query) use ($value) {

                    if ($value[0] != '' && $value[1] != '' && $value[0] !== $value[1]) {
                        $query = $query->whereBetween('absences.SDate', array(Carbon::parse($value[0])->startOfDay(), Carbon::parse($value[1])->endOfDay()))
                            ->orWhereBetween('absences.EDate', array(Carbon::parse($value[0])->startOfDay(), Carbon::parse($value[1])->endOfDay()))
                            ->orWhere(function ($query1) use ($value) {
                                $query1->where('absences.SDate', '<=', Carbon::parse($value[0])->startOfDay())
                                    ->where('absences.EDate', '>=', Carbon::parse($value[1])->endOfDay());
                            });
                    }
                    if ($value[0] === $value[1] && $value[0] != '') {
                        $query = $query->whereRaw("CAST(absences.SDate AS DATE) = '$value[0]'");
                    }
                    if ($value[0] != '' && $value[1] == '') {
                        $query = $query->where('absences.SDate', '>=', Carbon::parse($value[0])->startOfDay())
                            ->orWhere('absences.EDate', '>=', Carbon::parse($value[0])->startOfDay());
                    }
                    if ($value[0] == '' && $value[1] != '') {
                        $query = $query->where('absences.SDate', '<=', Carbon::parse($value[1])->startOfDay())
                            ->orWhere('absences.EDate', '<=', Carbon::parse($value[1])->startOfDay());
                    }
                });
            }
        }
        if (isset($request['approve']) && $request['approve'] != 'null') {
            $absence = $absence->where('Approved', '!=', $request['approve']);
        }
        if ($export != '' || $export != null) {
            return $absence->get();
        }
        return $absence;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Exception
     */
    public function export(Request $request)
    {

        $records = $this->getUserWithRequest($request, 'id', 'desc', 'export');
        if ($records->count() > 0) {
            return Excel::download(new AbsencesExport($records), 'Danh_sách_nghỉ.xlsx');
        } else {
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
    public function showAbsenceManagement(Request $request, $orderBy = 'SDate', $sortBy = 'desc')
    {
        $this->authorize('view', $this->menuM);
        $recordPerPage = $this->getRecordPage();

        //Get list absence of users
        $absences = Absence::query()->select('absences.*', 'master_data.Name', 'tb1.FullName', 'tb2.username as NameUpdateBy')
            ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
            ->leftJoin('users as tb1', 'absences.UID', '=', 'tb1.id')
            ->leftJoin('users as tb2', 'absences.UpdateBy', '=', 'tb2.id');
        if (Schema::hasColumn('absences', $orderBy)) {
            $absences = $absences->where('UID', Auth::user()->id)->orderBy($orderBy, $sortBy);
        } else {
            return redirect()->back();
        }

        //Search in column
        $this->data['request'] = $request->query();
        $one = Absence::query()->select('absences.SDate', 'absences.EDate', 'absences.TotalTimeOff', 'absences.Reason', 'absences.Remark', 'master_data.Name')
            ->Join('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
            ->leftJoin('users', 'absences.UID', '=', 'users.id')->first();

        if ($one) {
            $one = $one->toArray();
            if (array_key_exists('search', $request->input())) {
                $absences = $absences->where(function ($query) use ($one, $request) {

                    foreach ($one as $key => $value) {
                        if ($key == 'Name') {
                            $query->orWhere('master_data.' . $key, 'like', '%' . $request->input('search') . '%');
                        } else {
                            if (in_array($key, ['SDate', 'EDate', 'ApprovedDate'])) {
                                $query->orWhereRaw('(DATE_FORMAT(absences.' . $key . ',"%d/%m/%Y")) like ?', '%' . $request->input('search') . '%');
                            } else {
                                $query->orWhere('absences.' . $key, 'like', '%' . $request->input('search') . '%');
                            }
                        }
                    }
                });
            }
        }

        if (!isset($request['search']) && !isset($request['Date']) || strpos(\Request::getRequestUri(), 'api') !== false) {
            $absences = $absences->where(function ($queryOne) {
                $queryOne->where('absences.SDate', '>=', Carbon::now()->startOfMonth())
                    ->orWhere('absences.EDate', '>=', Carbon::now()->startOfMonth());
            });
        }

        foreach ($this->data['request'] as $key => $value) {
            if (is_array($value)) {
                $value[0] != '' ? $value[0] = $this->fncDateTimeConvertFomat(
                    $value[0],
                    self::FOMAT_DISPLAY_DMY,
                    self::FOMAT_DB_YMD
                ) : $value[0];
                $value[1] != '' ? $value[1] = $this->fncDateTimeConvertFomat(
                    $value[1],
                    self::FOMAT_DISPLAY_DMY,
                    self::FOMAT_DB_YMD
                ) : $value[1];

                $absences->where(function ($query) use ($value) {

                    if ($value[0] != '' && $value[1] != '' && $value[0] !== $value[1]) {
                        $query = $query->whereBetween('absences.SDate', array(Carbon::parse($value[0])->startOfDay(), Carbon::parse($value[1])->endOfDay()))
                            ->orWhereBetween('absences.EDate', array(Carbon::parse($value[0])->startOfDay(), Carbon::parse($value[1])->endOfDay()))
                            ->orWhere(function ($query1) use ($value) {
                                $query1->where('absences.SDate', '<=', Carbon::parse($value[0])->startOfDay())
                                    ->where('absences.EDate', '>=', Carbon::parse($value[1])->endOfDay());
                            });
                    }
                    if ($value[0] === $value[1] && $value[0] != '') {
                        $query = $query->whereRaw("CAST(absences.SDate AS DATE) = '$value[0]'");
                    }
                    if ($value[0] != '' && $value[1] == '') {
                        $query = $query->where('absences.SDate', '>=', Carbon::parse($value[0])->startOfDay())
                            ->orWhere('absences.EDate', '>=', Carbon::parse($value[0])->startOfDay());
                    }
                    if ($value[0] == '' && $value[1] != '') {
                        $query = $query->where('absences.SDate', '<=', Carbon::parse($value[1])->startOfDay())
                            ->orWhere('absences.EDate', '<=', Carbon::parse($value[1])->startOfDay());
                    }
                });
            }
        }

        $absences_list = $absences->get()->toArray();

        $list_id = array_column($absences_list, 'id');

        $absences_paginate = Absence::query()->select('absences.*', 'master_data.Name', 'tb1.FullName', 'tb2.username as NameUpdateBy')
            ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
            ->leftJoin('users as tb1', 'absences.UID', '=', 'tb1.id')
            ->leftJoin('users as tb2', 'absences.UpdateBy', '=', 'tb2.id')
            ->whereIn('absences.id', $list_id)->orderBy($orderBy, $sortBy);

        $count = $absences_paginate->count();
        //Pagination
        $absences = $absences_paginate->paginate($recordPerPage);
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);

        //redirect to the last page if current page has no record
        if ($absences->count() == 0) {
            if (array_key_exists('page', $query_array)) {
                if ($query_array['page'] > 1) {
                    $query_array['page'] = $absences->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl = $request->url() . '?' . $query_string;
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
        $this->data['appr'] = $this->app;
        $this->data['delete'] = $this->deleteM;

        $absenceTypes = MasterData::query()->where('DataKey', 'VM')
            ->get();
        $totalReport = [];
        $absences_total = Absence::query()->select('absences.*', 'master_data.Name', 'tb1.FullName', 'tb2.username as NameUpdateBy')
            ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
            ->leftJoin('users as tb1', 'absences.UID', '=', 'tb1.id')
            ->leftJoin('users as tb2', 'absences.UpdateBy', '=', 'tb2.id')
            ->whereIn('absences.id', $list_id)->get();
        $absences_total->filter(function ($value, $key) use (&$totalReport) {
            if (array_key_exists($value->MasterDataValue, $totalReport)) {
                $totalReport[$value->MasterDataValue] += $value->TotalTimeOff;
            } else {
                $totalReport[$value->MasterDataValue] = $value->TotalTimeOff;
            }
        });
        // print_r($totalReport);
        $this->data['totalReport'] = $totalReport;
        $this->data['absenceTypes'] = $absenceTypes;

        return $this->viewAdminLayout('absence.absence-management', $this->data);
    }

    /**
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return View screen (absence-reports)
     */
    public function showReport(Request $request, $orderBy = 'id', $sortBy = 'desc')
    {
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        // if ($request->species == 'system')
        //     $this->getListAbsencesReport($request, $orderBy, $sortBy);
        // else
        //     $this->getReportListAbsentTimekeeper($request, $orderBy, $sortBy);
        if ($request->species == 'timekeeper') {
            // $this->getReportListAbsentTimekeeper($request, $orderBy, $sortBy);
            $this->getAbsenceByTimekeeper($request, $orderBy, $sortBy);
        } else {
            $this->getListAbsencesReport($request, $orderBy, $sortBy);
        }

        $this->data['sort_link'] = $sort_link;
        $this->data['species'] = isset($request->species) ? $request->species : 'system';
        $this->data['export'] = RoleScreenDetail::query()->where('alias', 'AbsenceReportsExport')->first();
        return $this->viewAdminLayout('absence.absence-reports', $this->data);
    }

    public function getReportListAbsentTimekeeper($request, $orderBy = 'id', $sortBy = 'desc')
    {
        if ($request['UID'] != '') {
            $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
            $listIdUserActive = array($request['UID']);
        } else {
            $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
            $listIdUserActive = array_column($this->data['users']->toArray(), 'id');
        }
        $TimeInPMLate = "13:30";
        $Master1 = MasterData::where('DataValue', 'WT001')->first();
        $Master2 = MasterData::where('DataValue', 'WT002')->first();
        $reason = MasterData::where('Name', 'Ra ngoài')->first()->DataValue;
        $TimeOutAM = $Master2->Name;
        $TimeInPM = $Master2->DataDescription;
        $User = [];
        $arrayEarlyHours = [];
        $arrayLateHours = [];
        $arrayEarlyTimes = [];
        $arrayLateTimes = [];
        $arrayOffWorkHours = [];
        $arrayOffWorkTimes = [];
        $arrayOutHours = [];
        $arrayOutTimes = [];
        $totalHours = [];
        $totalTimes = [];
        foreach ($listIdUserActive as $row) {
            // $query = Timekeeping::query();
            $query = TimekeepingNew::query();

            $this->data['request'] = $request->query();
            if (!$request->has('UID') && !$request->has('date')) {
                $query = $query->where('UserID', $row)->whereBetween(
                    'Date',
                    array(
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth()
                    )
                );
            }
            if ($request['UID'] != '') {
                $query = $query->where('UserID', $row);
            }
            foreach ($this->data['request'] as $key => $value) {
                if (is_array($value)) {
                    if ($value[0] != '') $value[0] = Carbon::parse($this->fncDateTimeConvertFomat(
                        $value[0],
                        self::FOMAT_DISPLAY_DMY,
                        self::FOMAT_DB_YMD
                    ));
                    if ($value[1] != '') $value[1] = Carbon::parse($this->fncDateTimeConvertFomat(
                        $value[1],
                        self::FOMAT_DISPLAY_DMY,
                        self::FOMAT_DB_YMD
                    ));
                    $query->where(function ($queryFirst) use ($value) {
                        if ($value[0] != '' && $value[1] != '' && $value[0] !== $value[1]) {
                            $queryFirst = $queryFirst->whereBetween(
                                'Date',
                                array(
                                    $value[0],
                                    $value[1]
                                )
                            );
                        }
                        if ($value[0] === $value[1] && $value[0] != '') {
                            $queryFirst = $queryFirst->whereRaw("Date", $value[0]);
                        }
                        if ($value[0] != '' && $value[1] == '') {
                            $queryFirst = $queryFirst->where('Date', '>=', $value[0]);
                        }
                        if ($value[0] == '' && $value[1] != '') {
                            $queryFirst = $queryFirst->where('Date', '<=', $value[1]);
                        }
                    });
                }
            }
            $query = $query->orderBy('Date', 'asc')->get();
            $calendarEvent = CalendarEvent::query()
                ->select('StartDate', 'EndDate', 'Content', 'Type')
                ->where('StartDate', '>=', isset($this->data['request']['date'][0]) ? Carbon::parse($this->fncDateTimeConvertFomat($this->data['request']['date'][0], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD)) : Carbon::now()->startOfMonth())
                ->where('EndDate', '<=', isset($this->data['request']['date'][1]) ? Carbon::parse($this->fncDateTimeConvertFomat($this->data['request']['date'][1], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD)) : Carbon::now()->endOfMonth())
                ->where('CalendarID', 1)
                ->where('Content', 'NOT LIKE', '%Du lịch%')
                ->get();
            $userSelect = User::find($row);
            $absences = Absence::query()
                ->select('absences.SDate', 'absences.EDate')
                ->join('master_data', 'master_data.DataValue', 'absences.MasterDataValue')
                ->where('UID', isset($row) ? $row : '')
                ->where('SDate', '>=', isset($this->data['request']['date'][0]) ? Carbon::parse($this->fncDateTimeConvertFomat($this->data['request']['date'][0], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD)) : Carbon::now()->startOfMonth())
                ->where('EDate', '<=', isset($this->data['request']['date'][1]) ? Carbon::parse($this->fncDateTimeConvertFomat($this->data['request']['date'][1], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD)) : Carbon::now()->endOfMonth())
                ->where('MasterDataValue', $reason)
                ->get();
            $lateTimes = 0;
            $lateHours = 0;
            $soonTimes = 0;
            $soonHours = 0;
            $offWorkTimes = 0;
            $offWorkHours = 0;
            $outTimes = 0;
            $outHours = 0;
            $dem = 0;

            $userOffDays = array_map(function ($absenceInfo) {
                return $absenceInfo['Date'];
            }, $query->toArray());

            $query->offDays = $userOffDays;
            foreach ($query as $item) {
                if ($item->UserID == $row) {
                    $dayOfTheWeek = Carbon::parse($item->Date)->dayOfWeek;
                    $weekday = self::WEEK_MAP[$dayOfTheWeek];
                    if ($item->TimeIn != null && ((Carbon::parse($userSelect->STimeOfDay)->diffInMinutes(Carbon::parse($item->TimeIn), false) > 0) && (Carbon::parse($item->TimeIn) < Carbon::parse($TimeOutAM)) && (Carbon::parse($userSelect->STimeOfDay)->diffInMinutes($TimeOutAM, false) > Carbon::parse($userSelect->STimeOfDay)->diffInMinutes(Carbon::parse($item->TimeIn), false)))) {
                        $rangelate = (Carbon::parse($userSelect->STimeOfDay)->diffInMinutes(Carbon::parse($item->TimeIn), false));
                        $lateTimes += 1;
                        $lateHours += $rangelate / 60;
                    } elseif ($item->TimeIn != null && ((Carbon::parse($TimeInPMLate)->diffInMinutes(Carbon::parse($item->TimeIn), false) > 0) && (Carbon::parse($item->TimeIn) < Carbon::parse($userSelect->ETimeOfDay)) && (Carbon::parse($TimeInPMLate)->diffInMinutes($userSelect->ETimeOfDay, false) > Carbon::parse($TimeInPMLate)->diffInMinutes(Carbon::parse($item->TimeIn), false)))) {
                        $rangelate = (Carbon::parse($TimeInPMLate)->diffInMinutes(Carbon::parse($item->TimeIn), false));
                        $lateTimes += 1;
                        $lateHours += $rangelate / 60;
                    } else $rangelate = 0;
                    if ($item->TimeOut != null && ((Carbon::parse($TimeInPM)->diffInMinutes($userSelect->ETimeOfDay, false)) > (Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($userSelect->ETimeOfDay), false))) && ((Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($userSelect->ETimeOfDay), false)) > 0) && ((Carbon::parse($TimeInPM) < Carbon::parse($item->TimeOut))) && (Carbon::parse($item->TimeOut) < Carbon::parse($userSelect->ETimeOfDay))) {
                        $rangeearly = (Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($userSelect->ETimeOfDay), false));
                        $soonTimes += 1;
                        $soonHours += $rangeearly / 60;
                    } elseif ($item->TimeOut != null && ((Carbon::parse($userSelect->STimeOfDay)->diffInMinutes($TimeOutAM, false)) > (Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($TimeOutAM), false))) && ((Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($TimeOutAM), false)) > 0) && ((Carbon::parse($userSelect->STimeOfDay) < Carbon::parse($item->TimeOut))) && (Carbon::parse($item->TimeOut) < Carbon::parse($TimeOutAM))) {
                        $rangeearly = Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($TimeOutAM), false);
                        $soonTimes += 1;
                        $soonHours += $rangeearly / 60;
                    } else {
                        $rangeearly = 0;
                    }

                    $compensate = 0;
                    $holiday = 0;
                    foreach ($calendarEvent as $calendar) {
                        if ($calendar->Type == 1 && Carbon::parse($calendar->StartDate) <= Carbon::parse($item->Date) && Carbon::parse($calendar->EndDate) >= Carbon::parse($item->Date)) $holiday = 1;
                        elseif ($calendar->Type == 0 && Carbon::parse($calendar->StartDate) <= Carbon::parse($item->Date) && Carbon::parse($calendar->EndDate) >= Carbon::parse($item->Date)) $compensate = 1;
                    }
                    if (($weekday == 'T7' || $weekday == 'CN') && $compensate == 0) {
                        $rangeoffWork = 0;
                    } else {
                        if (($item->TimeOut == null && $item->TimeIn == null) || ($compensate == 1 && $item->TimeOut == null && $item->TimeIn == null)) {
                            $rangeoffWork = Carbon::parse($userSelect->STimeOfDay)->diffInMinutes(Carbon::parse($TimeOutAM), false) + Carbon::parse($TimeInPM)->diffInMinutes(Carbon::parse($userSelect->ETimeOfDay), false);
                            $offWorkTimes += 1;
                            $offWorkHours += $rangeoffWork / 60;
                        } else if (($item->TimeIn != null && $item->TimeOut != null && Carbon::parse($TimeInPM) >= Carbon::parse($item->TimeOut))) {
                            $rangeoffWork = Carbon::parse($TimeInPM)->diffInMinutes(Carbon::parse($userSelect->ETimeOfDay), false);
                            $offWorkTimes += 1;
                            $offWorkHours += $rangeoffWork / 60;
                        } else if (($item->TimeIn != null && $item->TimeOut != null && Carbon::parse($TimeOutAM) <= Carbon::parse($item->TimeIn))) {
                            $rangeoffWork = Carbon::parse($userSelect->STimeOfDay)->diffInMinutes(Carbon::parse($TimeOutAM), false);
                            $offWorkTimes += 1;
                            $offWorkHours += $rangeoffWork / 60;
                        } else {
                            $rangeoffWork = 0;
                        }
                    }
                    $out = 0;
                    foreach ($absences as $absence) {
                        if (Carbon::parse($absence->SDate)->format('Y-m-d') == Carbon::parse($item->Date)->format('Y-m-d')) {
                            $out += Carbon::parse($absence->SDate)->diffInMinutes(Carbon::parse($absence->EDate), false);
                            $outTimes += 1;
                        }
                    }
                    if ($out != 0) {
                        $outHours += $out / 60;
                    }
                }
            }
            $totalH = $lateHours + $soonHours + $offWorkHours + $outHours;
            $totalT = $lateTimes + $soonTimes + $offWorkTimes + $outTimes;
            array_push($arrayLateHours, $lateHours);
            array_push($arrayLateTimes, $lateTimes);
            array_push($arrayEarlyHours, $soonHours);
            array_push($arrayEarlyTimes, $soonTimes);
            array_push($arrayOffWorkHours, $offWorkHours);
            array_push($arrayOffWorkTimes, $offWorkTimes);
            array_push($arrayOutHours, $outHours);
            array_push($arrayOutTimes, $outTimes);
            array_push($totalHours, $totalH);
            array_push($totalTimes, $totalT);
            array_push($User, $userSelect);
        }
        $this->data['arrayLateHours'] = $arrayLateHours;
        $this->data['arrayLateTimes'] = $arrayLateTimes;
        $this->data['arrayEarlyHours'] = $arrayEarlyHours;
        $this->data['arrayEarlyTimes'] = $arrayEarlyTimes;
        $this->data['arrayOffWorkHours'] = $arrayOffWorkHours;
        $this->data['arrayOffWorkTimes'] = $arrayOffWorkTimes;
        $this->data['arrayOutHours'] = $arrayOutHours;
        $this->data['arrayOutTimes'] = $arrayOutTimes;
        $this->data['User'] = $User;
        $this->data['totalHours'] = $totalHours;
        $this->data['totalTimes'] = $totalTimes;
        $this->data['dataquery'] = $query;
        return $this->data;
    }

    public function getAbsenceByTimekeeper($request, $orderBy = 'id', $sortBy = 'desc')
    {
        $this->data['request'] = $request->all();
        $defaultimes = MasterData::query()
            ->select('Name', 'DataDescription')
            ->where('DataKey', 'WT')
            ->get()->toArray();

        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
        $today = Carbon::now()->format('Y-m-d');
        if ($request->date) {
            $startDate = date("Y-m-d", strtotime(str_replace('/', '-', $request->date[0])));
            $endDate = date("Y-m-d", strtotime(str_replace('/', '-', $request->date[1])));
        }else{
            $startDate = $startOfMonth;
            $endDate = $today;
            $this->data['request']['date'][0] = Carbon::now()->startOfMonth()->format('d/m/Y');
            $this->data['request']['date'][1] = Carbon::now()->format('d/m/Y');
            // $endDate = $endOfMonth;
        }
        $dateRange = CarbonPeriod::create($startDate, $endDate);
        if ($request['UID'] != '') {
            $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
            $listIdUserActive = array($request['UID']);
        } else {
            $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
            $listIdUserActive = array_column($this->data['users']->toArray(), 'id');
        }
        $users = User::with([
            'timekeepings_new' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('Date', [$startDate, $endDate]);
            },
            'absences'  => function ($query) use ($startDate, $endDate) {
                $query->where('SDate', '>=', "$startDate" . ' 00:00:00')
                    ->where('EDate', '<=', "$endDate" . " 23:59:59");
            }
        ])
            ->where('Active', 1)
            ->orderBy($orderBy, $sortBy);
        if ($request->UID) {
            $users = $users->where('id', $request->UID);
        }
        $users = $users->get();
        $eventDateList = $this->getEventDateList($startDate, $endDate);
        foreach ($users as $user) {
            $userWorkStartTime = $user->STimeOfDay ?? $defaultimes[0]['Name'];
            $userWorkEndTime = $user->ETimeOfDay ?? $defaultimes[0]['DataDescription'];
            $userWorkStartTime = Carbon::parse($userWorkStartTime);
            $userWorkEndTime = Carbon::parse($userWorkEndTime);

            $userBreakStart = $user->SBreakOfDay ?? $defaultimes[1]['Name'];
            $userBreakEnd = $user->EBreakOfDay ?? $defaultimes[1]['DataDescription'];
            $userBreakStart = Carbon::parse($userBreakStart);
            $userBreakEnd = Carbon::parse($userBreakEnd);

            $userBreakHours = Carbon::parse($userBreakEnd)->diffInHours($userBreakStart);
            $userWorkHours = Carbon::parse($userWorkEndTime)->diffInHours($userWorkStartTime) - $userBreakHours;

            $noCheckinTimes = 0;
            $noCheckoutTimes = 0;
            $checkinLateTimes = 0;
            $checkinLateHours = 0;
            $checkoutSoonTimes = 0;
            $checkoutSoonHours = 0;
            $noReasonAbsentTimes = 0;
            $noReasonAbsentHours = 0;
            $noReasonAbsentDays = 0;

            $noReasonAbsentDays = $this->getNoReasonAbsentDays($user, $dateRange, $eventDateList);
            $noReasonAbsentTimes = count($noReasonAbsentDays);
            $noReasonAbsentHours =  $noReasonAbsentTimes * $userWorkHours;

            foreach ($user->timekeepings_new as $timekeeping) {
                if ($timekeeping->TimeIn) {
                    $timeIn = Carbon::parse($timekeeping->TimeIn);
                    $lateHours = $timeIn->diffInMinutes($userWorkStartTime);
                    if ($timeIn > $userWorkStartTime) {
                        if($timeIn > $userBreakStart){
                            if($timeIn > $userBreakEnd){
                                $lateHours -= $userBreakHours;
                            }else{
                                $lateHours = $lateHours - ($timeIn->diffInMinutes($userBreakStart));
                            }
                        }
                        $checkinLateHours += $lateHours;
                        $checkinLateTimes += 1;
                    }
                } else {
                    $noCheckinTimes += 1;
                }

                if ($timekeeping->TimeOut) {
                    $timeOut = Carbon::parse($timekeeping->TimeOut);
                    $soonHours = $timeOut->diffInMinutes($userWorkEndTime);
                    if ($timeOut < $userWorkEndTime) {
                        if($timeOut < $userBreakEnd){
                            if($timeOut < $userBreakStart){
                                $soonHours -= $userBreakHours;
                            }else{
                                $soonHours -= ($userWorkEndTime->diffInMinutes($timeOut));
                            }
                        }
                        $checkoutSoonTimes += 1;
                        $checkoutSoonHours += $soonHours;
                    }
                } else {
                    $noCheckoutTimes += 1;
                }
            }
            $user->hasReasonAbsentHours = ($user->absences()
                    ->where('SDate', '>=', "$startDate" . ' 00:00:00')
                    ->where('EDate', '<=', "$endDate" . " 23:59:59")
                    ->sum('TotalTimeOff') / 60);
            $user->hasReasonAbsentTimes = count($user->absences);
            $user->noReasonAbsentHours = $noReasonAbsentHours;
            $user->noReasonAbsentTimes = $noReasonAbsentTimes;
            $user->checkinLateTimes = $checkinLateTimes;
            $user->checkinLateHours = $checkinLateHours / 60;
            $user->checkoutSoonTimes = $checkoutSoonTimes;
            $user->checkoutSoonHours = $checkoutSoonHours / 60;
            $user->noCheckinTimes = $noCheckinTimes;
            $user->noCheckoutTimes = $noCheckoutTimes;
        }
        $this->data['listUsers'] = $users;
        $this->data['dateRange'] = $dateRange;
        return $this->data;
    }

    public function getNoReasonAbsentDays($user, $dateRange, $eventDateList)
    {
        $userStartDate = isset($user->SDate) ? Carbon::createFromFormat('Y-m-d', $user->SDate) : null;
        $userQuitDate = isset($user->DaysOff) ? Carbon::createFromFormat('Y-m-d', $user->DaysOff) : null;
        if ($userStartDate && $userStartDate->between($dateRange->startDate, $dateRange->endDate)) {
            $dateRange->setStartDate($userStartDate);
        }
        if ($userQuitDate && $userQuitDate->between($dateRange->startDate, $dateRange->endDate)) {
            $dateRange->setEndDate($userQuitDate);
        }
        $dailyCheckin = [];
        foreach ($user->timekeepings_new as $timekeeping) {
            array_push($dailyCheckin,$timekeeping->Date);
        }

        // foreach($user->absences as $item){
        //     $absencedateRange = CarbonPeriod::create($item->SDate, $item->EDate);
        //     foreach($absencedateRange->toArray() as $date){
        //         $hasReasonAbsences[] = $date->toDateString('Y-m-d');
        //     }
        // }

        // $hasReasonAbsences = array_filter(array_merge($hasReasonAbsences,$eventDateList));

        $workingDays = array_map(function ($date) {
            if ($date->isWeekday()) return $date->toDateString('Y-m-d');
        }, $dateRange->toArray());

        $workingDays = array_diff(array_filter($workingDays), $eventDateList);

        $absentDays = array_diff($workingDays, $dailyCheckin);
        return $absentDays;
    }
    public function getEventDateList($startDate, $endDate)
    {
        $akbEvent = CalendarEvent::query()
            ->select('StartDate', 'EndDate', 'Content', 'Type')
            ->where('StartDate', '>=', $startDate)
            ->where('EndDate', '<=', $endDate)
            ->where('CalendarID', 1)
            ->get();
        foreach ($akbEvent as $event) {
            $eventDateRange = CarbonPeriod::create($event->StartDate, $event->EndDate);
            foreach ($eventDateRange as $day) {
                $dayoffList[] = $day->toDateString();
            }
        }
        if (isset($dayoffList)) {
            return $dayoffList;
        }
        return [];
    }
    public function getListAbsencesReport($request, $orderBy = 'id', $sortBy = 'desc')
    {
        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['today'] = Carbon::now()->format('d/m/Y');
        $listIdUserActive = array_column($this->data['users']->toArray(), 'id');
        $query = Absence::query()->select('absences.*', 'users.FullName')
            ->join('users', 'absences.UID', '=', 'users.id')
            ->whereIn('absences.UID', $listIdUserActive)
            ->groupBy('absences.UID')
            ->orderBy('absences.UID', 'desc');

        $this->data['request'] = $request->query();

        if (!$request->has('UID') && !$request->has('date')) {
            $query = $query->where(function ($query1) {
                $query1->whereBetween(
                    'absences.SDate',
                    array(
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth()
                    )
                )->orWhereBetween(
                    'absences.EDate',
                    array(
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth()
                    )
                );
            });
        }

        if ($request['UID'] != '') {
            $query = $query->where('absences.UID', $request['UID']);
        }
        foreach ($this->data['request'] as $key => $value) {
            if (is_array($value)) {
                $value[0] != '' ? $value[0] = $this->fncDateTimeConvertFomat(
                    $value[0],
                    self::FOMAT_DISPLAY_DMY,
                    self::FOMAT_DB_YMD
                ) : '';
                $value[1] != '' ? $value[1] = $this->fncDateTimeConvertFomat(
                    $value[1],
                    self::FOMAT_DISPLAY_DMY,
                    self::FOMAT_DB_YMD
                ) : '';

                $query->where(function ($queryFirst) use ($value) {

                    if ($value[0] != '' && $value[1] != '' && $value[0] !== $value[1]) {
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
                    if ($value[0] === $value[1] && $value[0] != '') {
                        $queryFirst = $queryFirst->whereRaw("CAST(absences.SDate AS DATE) = '$value[0]'");
                    }
                    if ($value[0] != '' && $value[1] == '') {
                        $queryFirst = $queryFirst->where('absences.SDate', '>=', Carbon::parse($value[0])->startOfDay())
                            ->orWhere('absences.EDate', '>=', Carbon::parse($value[0])->startOfDay());
                    }
                    if ($value[0] == '' && $value[1] != '') {
                        $queryFirst = $queryFirst->where('absences.SDate', '<=', Carbon::parse($value[1])->startOfDay())
                            ->orWhere('absences.EDate', '<=', Carbon::parse($value[1])->startOfDay());
                    }
                });
            }
        }

        $query = $query->get();
        $this->data['master_datas'] = $this->getReasonAbsence();

        foreach ($query as $item) {
            $hours = array();
            $times = array();
            foreach ($this->data['master_datas'] as $value) {
                $getDataReport = Absence::query()->where('UID', $item->UID)
                    ->where('MasterDataValue', $value->DataValue)->where('absences.Approved', '!=', 2);
                if (!$request->has('UID') && !$request->has('date')) {
                    $getDataReport = $getDataReport->where(function ($query1) {
                        $query1->whereBetween(
                            'absences.SDate',
                            array(
                                Carbon::now()->startOfMonth(),
                                Carbon::now()->endOfMonth()
                            )
                        )->orWhereBetween(
                            'absences.EDate',
                            array(
                                Carbon::now()->startOfMonth(),
                                Carbon::now()->endOfMonth()
                            )
                        );
                    });
                }

                if ($request['UID'] != '') {
                    $getDataReport = $getDataReport->where('absences.UID', $request['UID']);
                }

                foreach ($this->data['request'] as $key => $valueR) {
                    if (is_array($valueR)) {
                        $valueR[0] != '' ? $valueR[0] = $this->fncDateTimeConvertFomat(
                            $valueR[0],
                            self::FOMAT_DISPLAY_DMY,
                            self::FOMAT_DB_YMD
                        ) : '';
                        $valueR[1] != '' ? $valueR[1] = $this->fncDateTimeConvertFomat(
                            $valueR[1],
                            self::FOMAT_DISPLAY_DMY,
                            self::FOMAT_DB_YMD
                        ) : '';

                        $getDataReport->where(function ($queryFirst) use ($valueR) {

                            if ($valueR[0] != '' && $valueR[1] != '' && $valueR[0] !== $valueR[1]) {
                                $queryFirst = $queryFirst->whereBetween('absences.SDate', array($valueR[0], $valueR[1]))
                                    ->orWhereBetween('absences.EDate', array($valueR[0], $valueR[1]));
                            }
                            if ($valueR[0] === $valueR[1] && $valueR[0] != '') {
                                $queryFirst = $queryFirst->whereRaw("CAST(absences.SDate AS DATE) = '$valueR[0]'");
                            }
                            if ($valueR[0] != '' && $valueR[1] == '') {
                                $queryFirst = $queryFirst->where('absences.SDate', '>=', Carbon::parse($valueR[0])->startOfDay())
                                    ->orWhere('absences.EDate', '>=', Carbon::parse($valueR[0])->startOfDay());
                            }
                            if ($valueR[0] == '' && $valueR[1] != '') {
                                $queryFirst = $queryFirst->where('absences.SDate', '<=', Carbon::parse($valueR[1])->startOfDay())
                                    ->orWhere('absences.EDate', '<=', Carbon::parse($valueR[1])->startOfDay());
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

        if ($orderBy == 'sum' && $sortBy == 'asc') {
            $query = $query->sortBy(function ($item, $key) {
                return number_format(array_sum($item->hours) / 60, 2);
            });
        } else {
            $query = $query->sortByDesc(function ($item, $key) {
                return number_format(array_sum($item->hours) / 60, 2);
            });
        }
        $nummaster_datas = -1;
        foreach ($this->data['master_datas'] as $keys => $value) {
            $nummaster_datas++;
            if ($orderBy == $value['DataValue'] && $sortBy == 'asc') {
                $query = $query->sortBy(function ($item, $key) use ($nummaster_datas) {
                    return number_format($item->hours[$nummaster_datas] / 60, 1);
                });
            } else if ($orderBy == $value['DataValue'] && $sortBy == 'desc') {
                $query = $query->sortByDesc(function ($item, $key) use ($nummaster_datas) {
                    return number_format($item->hours[$nummaster_datas] / 60, 1);
                });
            }
        }
        $this->data['excel'] = RoleScreenDetail::query()->where('alias', 'AbsenceListExport')->first();
        $this->data['absence_report'] = $query;
        return $this->data;
    }

    /**
     * export excel of Absences Report
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function absencesReportExport(Request $request)
    {
        if ($request->species == 'system')
            $records = $this->getListAbsencesReport($request);
        else
            $records =$this->getAbsenceByTimekeeper($request);
            // $records = $this->getReportListAbsentTimekeeper($request);
        if (isset($records['absence_report'][0]) && $request->species == 'system') {
            return Excel::download(new AbsencesReportExport($records), 'BaoCaoTongHopVangMat.xlsx');
        } elseif (isset($records['users'][0]) && $request->species == 'timekeeper') {
            return Excel::download(new AbsencesTimekeepingExport($records), 'BaoCaoTongHopVangMat.xlsx');
        } else {
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
            ->select('role_group_id')->where('screen_detail_alias', 'ListApprove')->get();
        $role_group_user = $role_group_user->toArray();

        $request_manager = RoleUserScreenDetailRelationship::query()
            ->select('user_id', 'FullName')
            ->join('users', 'users.id', '=', 'role_user_screen_detail_relationships.user_id')
            ->where('role_user_screen_detail_relationships.screen_detail_alias', 'like', 'ListApprove')
            ->where('role_user_screen_detail_relationships.permission', '=', 1);

        foreach ($role_group_user as $key => $value) {
            $group_user = User::query()->select('id as user_id', 'FullName')->where('role_group', $value['role_group_id']);
            //            $request_manager = $request_manager->union($group_user);
        }

        $this->data['request_manager'] = $request_manager->get();

        $this->data['rooms'] = Room::query()->select('id', 'Name')
            ->where('MeetingRoomFlag', '!=', 1)
            ->where('Active', 1)
            ->get();
        $this->data['master_datas'] = $this->getReasonAbsence();
        $this->data['boolean'] = 1;
        $userLogged = auth()->user();
        $this->data['userLogged'] = $userLogged;
        $this->data['roomUser'] = $userLogged->RoomId;
        // $this->data['userLogged'] = User::find(Auth::user()->id);
        // $this->data['roomUser'] = Room::find(Auth::user()->RoomId);
        $this->data['isAdmin'] = true;
        if ($userLogged->role_group != 2) {
            $this->data['isAdmin'] = false;
        }
        $this->data['add'] = $this->add;

        if ($id != null) {
            $one = Absence::find($id);
            if ($one) {
                if ($del == 'del') {
                    $one->delete();
                    return 1;
                }
                $this->data['absenceInfo'] = $one;
                if (!is_null($this->data['absenceInfo']->RequestManager)) {
                    $this->data['absenceInfo']->RequestManager = explode(',', $this->data['absenceInfo']->RequestManager);
                    $this->data['boolean'] = 2;
                } else {
                    $this->data['absenceInfo']->RequestManager = [];
                }
                return $this->viewAdminIncludes('absence-detail', $this->data);
            } else {
                return "";
            }
        } else {
            return $this->viewAdminIncludes('absence-detail', $this->data);
        }
    }

    /**
     * @param Request $request
     * @param null $id
     * @return string|void
     */
    public function store(AbsenceRequest $request, $id = null)
    {
        if (count($request->input()) === 0) {
            return abort('404');
        }
        try {
            $modeIsUpdate = array_key_exists('id', $request->input());
            $one = !$modeIsUpdate ? new Absence() : Absence::find($request->id);

            foreach ($request->all() as $key => $value) {
                if (Schema::hasColumn('absences', $key)) {
                    if ($key == 'SDate') {
                        $value = $this->fncDateTimeConvertFomat($value, 'd/m/Y H:i', 'Y-m-d H:i');
                    }
                    if ($key == 'EDate') {
                        $value = $this->fncDateTimeConvertFomat($value, 'd/m/Y H:i', 'Y-m-d H:i');
                    }
                    $one->$key = $value;
                }
            }

            if (Carbon::parse($one->SDate)->gt(Carbon::parse($one->EDate))) {
                return $this->jsonErrors('Ngày nghỉ không hợp lệ');
            }

            $user = User::find($request->UID);
            $startTimeOfUser = Carbon::parse($user->STimeOfDay);
            $startBreakTimeOfUser = Carbon::parse($user->SBreakOfDay);
            $endTimeOfUser = Carbon::parse($user->ETimeOfDay);
            $endBreakTimeOfUser = Carbon::parse($user->EBreakOfDay);
            $totalTimeUser = ($startTimeOfUser->diffInMinutes($startBreakTimeOfUser) + $endBreakTimeOfUser->diffInMinutes($endTimeOfUser));
            $one->TotalTimeOff = $this->getDiffHours(Carbon::parse($one->SDate), Carbon::parse($one->EDate), $request->UID) * 60;

            if ($one->MasterDataValue == 'VM003' && (Carbon::parse($one->EDate)->format('Y-m-d H:i') == Carbon::parse($one->EDate)->format('Y-m-d') . " 12:00" || $one->TotalTimeOff >= ($totalTimeUser / 2))) {
                return $this->jsonErrors('Thời gian xin đi muộn không được vượt quá ' . ($totalTimeUser / 2 / 60) . ' giờ.');
            }
            if ($one->MasterDataValue == 'VM003' && Carbon::now()->format('Y-m-d') > Carbon::parse($one->EDate)->format('Y-m-d')) {
                return $this->jsonErrors('Thời gian kết thúc xin đi muộn không được trước ngày hôm nay.');
            }

            // if ($one->MasterDataValue == 'VM001' && (Carbon::parse($one->EDate)->format('Y-m-d H:i') != Carbon::parse($one->EDate)->format('Y-m-d') . " 12:00" && $one->TotalTimeOff < ($totalTimeUser / 2))) {
            //    return $this->jsonErrors('Thời gian xin nghỉ phép tối thiểu là ' . $totalTimeUser / 2 / 60 . ' giờ.');
            // }
            if (
                $one->MasterDataValue == 'VM005'
                // && (Carbon::parse($one->EDate)->format('Y-m-d H:i') == Carbon::parse($one->EDate)->format('Y-m-d') . " 12:00"
                && $one->TotalTimeOff >= ($totalTimeUser / 2)
            ) {

                return $this->jsonErrors('Thời gian xin ra ngoài không được vượt quá ' . ($totalTimeUser / 2 / 60) . ' giờ.');
            }


            // nếu xin nghỉ vào cuối tuần
            // if ($this->checkHoliday(Carbon::parse($one->SDate)) && $this->checkHoliday(Carbon::parse($one->EDate))) {
            //    return $this->jsonErrors('Không xin nghỉ vào ngày nghỉ.');
            //}
            //if ($one->TotalTimeOff < 5) {
            //    return $this->jsonErrors('Thời gian nghỉ tối thiểu là 5 phút');
            //}
            $check = DB::table('absences')
                ->where('UID', $request->UID)
                ->where(function ($query) use ($one) {
                    $query->orWhere(function ($query) use ($one) {
                        $query->orWhereBetween('SDate', array($one->SDate, $one->EDate));
                        $query->orWhereBetween('EDate', array($one->SDate, $one->EDate));
                    });
                    $query->orWhere(function ($query) use ($one) {
                        $query->where('SDate', '<=', $one->SDate);
                        $query->where('EDate', '>=', $one->EDate);
                    });
                })
                ->whereNull('deleted_at');
            if ($modeIsUpdate) {
                $check = $check->where('id', '!=', $request->id);
            }
            $check = $check->first();
            if ($check) {
                return $this->jsonErrors('Đơn vắng mặt đã tồn tại, vui lòng chọn giờ khác!');
            }
            $one->RequestManager = ',' . implode(',', $request->RequestManager) . ',';
            $one->AbsentDate = Carbon::now()->format('Y-m-d');
            $one->save();
            if (!$one) {
                return $this->jsonErrors('Lưu không thành công');
            } else {
                $arrTokenAd = collect(DB::table('push_token')->where('role_group', 2)->where('allow_push', 1)->whereNull('deleted_at')->pluck('token_push')->toArray());
                $arrToken = ($arrTokenAd->merge(DB::table('push_token')->where('UserID', $one->UID)->whereNull('deleted_at')->pluck('token_push')->toArray()))->all();
                $arrToken = array_unique($arrToken);
                // $arrToken = DB::table('push_token')->where('role_group', 2)->where('allow_push', 1)->whereNull('deleted_at')->pluck('token_push')->toArray();
                if (count($arrToken) > 0) {
                    $sendData = [];
                    $sendData['id'] = $one->id;
                    $sendData['data'] = DB::table('master_data')->where('DataValue', $request['MasterDataValue'])->pluck('DataKey')->first();
                    $messNotification = DB::table('master_data')->where('DataValue', $request['MasterDataValue'])->pluck('Name')->toArray();
                    $master_data_name = mb_strtolower($messNotification[0], 'UTF-8');
                    $FullName = DB::table('users')->where('id', $request['UID'])->pluck('FullName')->first();
                    $headrmess = $FullName . " xin phép " . $master_data_name;

                    $bodyNoti = "Từ " . $request['SDate'] . ' đến ' . $request['EDate'];

                    NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
                }

                $header = 'Kính gửi Ban giám đốc!';
                if ($request->Mail === null) {
                    $this->sendMail($request->all(), $header, null, 2);
                }
                return $this->jsonSuccessWithRouter('admin.AbsenceManagement');
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public static function storeStatic(AbsenceRequest $request, $id = null)
    {
        if (count($request->input()) === 0) {
            return abort('404');
        }
        try {
            $modeIsUpdate = array_key_exists('id', $request->input()) && ($request->input())['id'];
            $one = !$modeIsUpdate ? new Absence() : Absence::find($request->id);
            foreach ($request->all() as $key => $value) {
                if (Schema::hasColumn('absences', $key)) {
                    if ($key == 'SDate') {
                        $date = \DateTime::createFromFormat('d/m/Y H:i', $value);
                        $value =  $date->format('Y-m-d H:i');
                    }
                    if ($key == 'EDate') {
                        $date = \DateTime::createFromFormat('d/m/Y H:i', $value);
                        $value = $date->format('Y-m-d H:i');
                    }
                    $one->$key = $value;
                }
            }
            $user = User::find($request->UID);
            $one->RequestManager = ',' . implode(',', $request->RequestManager) . ',';
            $one->AbsentDate = Carbon::now()->format('Y-m-d');
            $one->RoomID = strval($user['RoomId']);
            $startTime = Carbon::parse($one->SDate);
            $endTime = Carbon::parse($one->EDate);
            $one->TotalTimeOff = (strtotime($endTime) - strtotime($startTime)) / 60;

            $one->save();
            if (!$one) {
                return false;
            } else {
                return response()->json(['success' => route('admin.AbsenceManagement')]);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
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
        if (Schema::hasColumn('absences', $orderBy)) {
            $absence = Absence::query()->select('absences.*', 'tb1.FullName', 'master_data.Name', 'tb2.FullName as NameUpdateBy', 'rooms.Name as RoomName')
                ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
                ->leftJoin('users as tb1', 'absences.UID', '=', 'tb1.id')
                ->leftJoin('users as tb2', 'absences.UpdateBy', '=', 'tb2.id')
                ->leftJoin('rooms', 'absences.RoomID', '=', 'rooms.id')
                ->where('Approved', 0)->where('RequestManager', 'like', '%,' . Auth::user()->id . ',%')
                ->orderBy($orderBy, $sortBy);

            //Get user has role approve
            $checkRequestManager = Absence::query()->where('RequestManager', 'like', '%' . Auth::user()->id . '%')->get();
        } else {
            return redirect()->back();
        }

        //Search in columns
        $this->data['request'] = $request->query();
        $one = Absence::query()
            ->select('absences.SDate', 'absences.EDate', 'absences.AbsentDate', 'absences.Reason', 'absences.Remark', 'users.FullName', 'master_data.Name', 'rooms.Name')
            ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
            ->leftJoin('users', 'absences.UID', '=', 'users.id')
            ->leftJoin('rooms', 'absences.RoomID', '=', 'rooms.id')->first();


        if ($one) {
            $one = $one->toArray();
            if (array_key_exists('search', $request->input())) {
                $absence = $absence->where(function ($query) use ($one, $request) {
                    foreach ($one as $key => $value) {
                        if ($key == 'FullName') {
                            $query->orWhere('tb1.' . $key, 'like', '%' . $request->input('search') . '%');
                        } elseif ($key == 'Name') {
                            $query->orWhere('master_data.' . $key, 'like', '%' . $request->input('search') . '%')
                                ->orWhere('rooms.' . $key, 'like', '%' . $request->input('search') . '%');
                        } else {
                            if (in_array($key, ['SDate', 'EDate', 'AbsentDate'])) {
                                $query->orWhereRaw('(DATE_FORMAT(absences.' . $key . ',"%d/%m/%Y")) like ?', '%' . $request->input('search') . '%');
                            } else {
                                $query->orWhere('absences.' . $key, 'like', '%' . $request->input('search') . '%');
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
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);

        //redirect to the last page if current page has no record
        if ($absence->count() == 0) {
            if (array_key_exists('page', $query_array)) {
                if ($query_array['page'] > 1) {
                    $query_array['page'] = $absence->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl = $request->url() . '?' . $query_string;
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

        return $this->viewAdminLayout('absence.absence-list-approve', $this->data);
    }

    /**
     * Duyệt/từ chối lịch nghỉ
     * @param Request $request
     * @param null $id
     * @param null $del
     * @return int|string
     */
    public function AprAbsence(Request $request, $id = null, $del = null)
    {
        $this->data['request'] = $request->query();

        if ($id != null) {
            $one = Absence::find($id);
            $MasterDataValue = DB::table('absences')->where('id', $id)->pluck('MasterDataValue')[0];
            //hủy lịch nghỉ
            if ($del == 'del') {
                if ($this->data['request']['Comment'] == '') {
                    return $this->jsonErrors('Vui lòng điền lý do');
                }
                if ($one) {
                    $one->Approved = 2;
                    $one->UpdateBy = Auth::user()->id;
                    $one->ApprovedDate = Carbon::now();
                    $one->Comment = $this->data['request']['Comment'];
                    $one->save();

                    // firebase notification
                    $UidValue = DB::table('absences')->where('id', $id)->pluck('UID')->toArray();
                    $arrTokenAd = collect(DB::table('push_token')->where('role_group', 2)->where('allow_push', 1)->whereNull('deleted_at')->pluck('token_push')->toArray());
                    $arrToken = ($arrTokenAd->merge(DB::table('push_token')->where('UserID', $UidValue)->whereNull('deleted_at')->pluck('token_push')->toArray()))->all();
                    $arrToken = array_unique($arrToken);
                    if (count($arrToken) > 0) {
                        $sendData = [];
                        $sendData['id'] = $one->id;
                        $sendData['data'] = DB::table('master_data')->where('DataValue', $MasterDataValue)->pluck('DataKey')->first();

                        $messNotification = DB::table('master_data')->where('DataValue', $MasterDataValue)->pluck('Name')->toArray();
                        $master_data_name = mb_strtolower($messNotification[0], 'UTF-8');
                        $headrmess = DB::table('users')->where('id', $one->UpdateBy)->pluck('FullName')->first() . " đã từ chối lịch " . $master_data_name;

                        $bodyNoti = "Lý do: " . $request['Comment'];

                        NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData, Carbon::createFromFormat('Y-m-d H:i:s', $one['EDate']));
                    }


                    $this->sendMail($one, '', $this->data['request']['Comment'], null, "1");
                }
                return 1;
            }
            //duyệt lịch nghỉ
            if ($one) {
                $one->Approved = 1;
                $one->UpdateBy = Auth::user()->id;
                $one->ApprovedDate = Carbon::now();
                $one->save();

                // firebase notification
                $UidValue = DB::table('absences')->where('id', $id)->pluck('UID')->toArray();
                $arrTokenAd = collect(DB::table('push_token')->where('role_group', 2)->where('allow_push', 1)->whereNull('deleted_at')->pluck('token_push')->toArray());
                $arrToken = ($arrTokenAd->merge(DB::table('push_token')->where('UserID', $UidValue)->whereNull('deleted_at')->pluck('token_push')->toArray()))->all();
                $arrToken = array_unique($arrToken);
                if (count($arrToken) > 0) {
                    $sendData = [];
                    $sendData['id'] = $one->id;
                    $sendData['data'] = DB::table('master_data')->where('DataValue', $one['MasterDataValue'])->pluck('DataKey')->first();

                    $messNotification = DB::table('master_data')->where('DataValue', $one['MasterDataValue'])->pluck('Name')->toArray();
                    $master_data_name = mb_strtolower($messNotification[0], 'UTF-8');
                    $headrmess = DB::table('users')->where('id', $one->UpdateBy)->pluck('FullName')->first() . " đã duyệt lịch " . $master_data_name;

                    $bodyNoti = "Từ " . Carbon::parse($one['SDate'])->format('d/m/Y H:i:s') . ' đến ' . Carbon::parse($one['EDate'])->format('d/m/Y H:i:s');

                    NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData, Carbon::createFromFormat('Y-m-d H:i:s', $one['EDate']));
                }

                $header = 'Gửi anh/chị/em trong công ty!';
                $this->sendMail($one, $header, null, "1", null);
                return $this->jsonSuccess('Duyệt thành công');
            } else {
                return $this->jsonErrors('Duyệt thất bại');
            }
        } else {
            return $this->viewAdminIncludes('absence-detail', $this->data);
        }
    }

    /**
     * @return View (unapprove-detail)
     */
    public function showDetailUnapprove(Request $request)
    {
        $this->data['request'] = $request;
        return $this->viewAdminIncludes('unapprove-detail', $this->data);
    }

    /**
     * @param $date
     * @return bool
     */
    public function checkHoliday($date)
    {
        //check weekend
        if ($date->isWeekend()) {
            //kiem tra xem co phải ngày làm bù ko
            $queryOne = CalendarEvent::query()
                ->where('StartDate', '<=', $date->toDateString())
                ->where('EndDate', '>=', $date->toDateString())
                ->where('Type', 0)
                ->where('CalendarID', 1)
                ->first();
            return $queryOne ? false : true;
        } else {
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

    public function getDiffHours($from, $to, $userId)
    {
        $user = User::find($userId);
        // $startTime = Carbon::parse($this->startTime);
        // $endTime = Carbon::parse($this->endTime);
        $startTime = !is_null($user->STimeOfDay) ? Carbon::parse($user->STimeOfDay) : Carbon::parse($this->startTime);
        $endTime = !is_null($user->ETimeOfDay) ? Carbon::parse($user->ETimeOfDay) : Carbon::parse($this->endTime);
        if (!isset($to)) {
            $to = Carbon::now('UTC');
        }
        if ($to->format('H:i') <= $startTime->format('H:i')) {
            $to->addDays(-1);
            $to->hour = $endTime->hour;
            $to->minute = $endTime->minute;
            $to->second = 0;
        }
        if ($to->format('H:i') > $endTime->format('H:i')) {
            $to->hour = $endTime->hour;
            $to->minute = $endTime->minute;
            $to->second = 0;
        }
        if ($from->format('H:i') >= $endTime->format('H:i')) {
            $from->addDays(1);
            $from->hour = $startTime->hour;
            $from->minute = $startTime->minute;
            $from->second = 0;
        }
        if ($from->format('H:i') < $startTime->format('H:i')) {
            $from->hour = $startTime->hour;
            $from->minute = $startTime->minute;
            $from->second = 0;
        }
        if ($from->format('H:i') >= $this->timeOutAm && $from->format('H:i') < $this->timeInPm) {
            $from->hour = Carbon::parse($this->timeInPm)->hour;
            $from->minute = Carbon::parse($this->timeInPm)->minute;
            $from->second = 0;
        }
        if ($to->format('H:i') > $this->timeOutAm && $to->format('H:i') <= $this->timeInPm) {
            $to->hour = Carbon::parse($this->timeOutAm)->hour;
            $to->minute = Carbon::parse($this->timeOutAm)->minute;
            $to->second = 0;
        }
        //nếu ngày bắt đầu rơi vào ngày nghỉ
        $nextWorkingDay = $this->getNextWorkingDays(1, $from->toDateString())[0];
        if ($from->toDateString() != $nextWorkingDay) {
            $from = Carbon::parse($nextWorkingDay);
            $from->hour = $startTime->hour;
            $from->minute = $startTime->minute;
            $from->second = 0;
        }
        //Nếu ngày kết thúc rơi vào ngày nghỉ
        $prevWorkingDay = $this->getNextWorkingDays(1, $to->toDateString(), -1)[0];
        if ($to->toDateString() != $prevWorkingDay) {
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
        if ($from->gte($to)) {
            return 0;
        }
        $diffDays = $to->diffInDaysFiltered(function ($date) {
            return !$this->checkHoliday($date);
        }, $from) - 1;
        if ($diffDays < 0) {
            $diffDays == 0;
        }

        $weekends = $to->diffInDaysFiltered(function ($date) {
            return $this->checkHoliday($date);
        }, $from); //Weekends or holidays

        $finalDiff = $diffDays * ($endTime->diffInMinutes($startTime) / 60 - $minus);
        if ($diffDays > 0) {
            $from->addDays($diffDays + $weekends);
        }

        $diffHours = $to->diffFiltered(CarbonInterval::hour(), function (Carbon $date) use ($startTime, $endTime) {
            // print_r($date->hour.'/');
            if ($this->checkHoliday($date)) {
                return false;
            }

            if ($date->hour > $startTime->hour && $date->hour <= $endTime->hour) {
                return true;
            } else {
                return false;
            }
        }, $from, true);

        if ($from->hour != $startTime->hour) {
            $diffHours -= 1;
        }
        if (($from->hour > $to->hour || ($from->hour == $to->hour && $from->minute >= $to->minute)) && ($from->hour < 12 || $to->hour >= 13)) {
            $diffHours -= 1;
        }
        $finalDiff += $diffHours;
        if ($to->minute > $from->minute) {
            $correct = ($to->minute - $from->minute) / 60;
        } else {
            $correct = ($to->minute + 60 - $from->minute) / 60;
        }

        if (($from->format('H:i') < $this->timeOutAm && $to->format('H:i') > $this->timeInPm)) {
            $minus = $minus;
        } else {
            $minus = 0;
        }
        $finalDiff += $correct - $minus;
        // echo $finalDiff;
        return $finalDiff;
    }

    //lấy danh sách các ngày làm việc tiếp theo
    public function getNextWorkingDays($intNum, $datetime, $sub = 1)
    {
        $intCount = 0;
        $arrDates = [];
        while ($intCount < $intNum) {
            $weekDay = Carbon::parse($datetime)->dayOfWeek;
            //Truong hop neu la ngay nghi cuoi tuan
            if (!in_array($weekDay, $this->working_days)) {
                //kiem tra xem co phai ngay lam bu khong
                $queryOne = CalendarEvent::query()
                    ->where('StartDate', '<=', Carbon::parse($datetime)->toDateString())
                    ->where('EndDate', '>=', Carbon::parse($datetime)->toDateString())
                    ->where('Type', 0)
                    ->where('CalendarID', 1)
                    ->first();
                if ($queryOne) {
                    $arrDates[] = Carbon::parse($datetime)->toDateString();
                    $intCount++;
                }
            } else {
                //kiem tra xem ngay hien tai co phai ngay le hay ngay nghi khong
                $queryOne = CalendarEvent::query()
                    ->where('StartDate', '<=', Carbon::parse($datetime)->toDateString())
                    ->where('EndDate', '>=', Carbon::parse($datetime)->toDateString())
                    ->where('Type', '!=', 0)
                    ->where('CalendarID', 1)
                    ->first();
                if (!$queryOne) {
                    $arrDates[] = Carbon::parse($datetime)->toDateString();
                    $intCount++;
                }
            }
            $datetime = Carbon::parse($datetime)->addDays($sub);
        }
        return $arrDates;
    }

    //API

    /**
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\Support\Facades\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showApi(Request $request, $orderBy = 'SDate', $sortBy = 'desc')
    {
        $this->authorize('action', $this->view);
        $data = array();
        $recordPerPage = $this->getRecordPage();
        $absence = $this->getUserWithRequest($request, $orderBy, $sortBy);
        $count = $absence->count();

        //Pagination
        $absence = $absence->paginate($recordPerPage);
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);

        $data['master_datas'] = $this->getReasonAbsence();
        $data['users'] = $this->user;
        $data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $data['absence'] = $absence;
        $data['query_array'] = $query_array;
        $data['sort_link'] = $sort_link;
        $data['sort'] = $sort;
        $data['role_key'] = 'AbsenceList';

        return AdminController::responseApi(200, null, null, $data);
    }

    /**
     * @param null $id
     * @param null $del
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Support\Facades\View|int|string
     */
    public function showDetailApi($id = null, $del = null)
    {
        $data = array();
        $role_group_user = RoleGroupScreenDetailRelationship::query()
            ->select('role_group_id')
            ->where('screen_detail_alias', 'ListApprove')
            ->get();
        $role_group_user = $role_group_user->toArray();

        $request_manager = RoleUserScreenDetailRelationship::query()
            ->select('user_id', 'FullName')
            ->join('users', 'users.id', '=', 'role_user_screen_detail_relationships.user_id')
            ->where('role_user_screen_detail_relationships.screen_detail_alias', '=', 'ListApprove')
            ->where('role_user_screen_detail_relationships.permission', '=', 1);

        $data['request_manager'] = $request_manager->get();
        $data['rooms'] = Room::query()->select('id', 'Name')
            ->where('MeetingRoomFlag', '!=', 1)
            ->where('Active', 1)
            ->get();
        $data['master_datas'] = $this->getReasonAbsence();
        $data['boolean'] = 1;
        $data['userLogged'] = Auth::user();
        $data['roomUser'] = Room::find(Auth::user()->RoomId);

        if ($id != null) {
            $one = Absence::find($id);
            if ($del == 'del' && $one != null) {
                $one->delete();
                return AdminController::responseApi(200, null, __('admin.success.delete'));
            }
            $data['absenceInfo'] = $one;
            if ($data['absenceInfo']) {
                if (!is_null($data['absenceInfo']->RequestManager)) {
                    $data['absenceInfo']->RequestManager = explode(',', $data['absenceInfo']->RequestManager);
                    $data['boolean'] = 2;
                } else {
                    $data['absenceInfo']->RequestManager = [];
                }
            }
        }
        return AdminController::responseApi(200, null, null, $data);
    }

    /**
     * @param AbsenceRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function storeApi(AbsenceRequest $request)
    {
        $this->authorize('action', $this->add);
        $result = $this->insertOrUpdate($request);
        if (array_key_exists('success', $result)) {
            return AdminController::responseApi($result['status'], null, $result['success']);
        } else {
            return AdminController::responseApi($result['status'], $result['error']);
        }
    }

    /**
     * @param AbsenceRequest $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateApi(AbsenceRequest $request, $id = null)
    {
        $this->authorize('action', $this->edit);
        $result = $this->insertOrUpdate($request, $id);

        if (array_key_exists('success', $result)) {
            return AdminController::responseApi($result['status'], null, $result['success']);
        } else {
            return AdminController::responseApi($result['status'], $result['error']);
        }
    }

    /**
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function deleteApi($id = null)
    {
        $this->authorize('action', $this->delete);
        if ($id != null) {
            $one = Absence::find($id);
            if ($one) {
                $one->delete();
                return AdminController::responseApi(200, null, __('admin.success.save'));
            }
        }
        return AdminController::responseApi(422, __('admin.error.data'));
    }

    /**
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showListApproveApi(Request $request, $orderBy = 'id', $sortBy = 'desc')
    {
        $this->authorize('action', $this->viewAppr);

        $data = array();
        $recordPerPage = $this->getRecordPage();
        //Get list absences approve = 0
        $absence = Absence::query()->select('absences.*', 'tb1.FullName', 'master_data.Name', 'tb2.FullName as NameUpdateBy', 'rooms.Name as RoomName')
            ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
            ->leftJoin('users as tb1', 'absences.UID', '=', 'tb1.id')
            ->leftJoin('users as tb2', 'absences.UpdateBy', '=', 'tb2.id')
            ->leftJoin('rooms', 'absences.RoomID', '=', 'rooms.id')
            ->where('Approved', 0)->where('RequestManager', 'like', '%' . Auth::user()->id . '%');
        if (Schema::hasColumn('absences', $orderBy)) {
            $absence = $absence->orderBy($orderBy, $sortBy);
        }

        //Search in columns
        $data['request'] = $request->query();
        $one = Absence::query()
            ->select('absences.SDate', 'absences.EDate', 'absences.AbsentDate', 'absences.Reason', 'absences.Remark', 'users.FullName', 'master_data.Name', 'rooms.Name')
            ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
            ->leftJoin('users', 'absences.UID', '=', 'users.id')
            ->leftJoin('rooms', 'absences.RoomID', '=', 'rooms.id')->first();

        if ($one) {
            $one = $one->toArray();
            if (array_key_exists('search', $request->input())) {
                $absence = $absence->where(function ($query) use ($one, $request) {
                    foreach ($one as $key => $value) {
                        if ($key == 'FullName') {
                            $query->orWhere('tb1.' . $key, 'like', '%' . $request->input('search') . '%');
                        } elseif ($key == 'Name') {
                            $query->orWhere('master_data.' . $key, 'like', '%' . $request->input('search') . '%')
                                ->orWhere('rooms.' . $key, 'like', '%' . $request->input('search') . '%');
                        } else {
                            if (in_array($key, ['SDate', 'EDate', 'AbsentDate'])) {
                                $query->orWhereRaw('(DATE_FORMAT(absences.' . $key . ',"%d/%m/%Y")) like ?', '%' . $request->input('search') . '%');
                            } else {
                                $query->orWhere('absences.' . $key, 'like', '%' . $request->input('search') . '%');
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
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);

        $data['master_datas'] = $this->getReasonAbsence();
        $data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $data['absence'] = $absence;
        $data['query_array'] = $query_array;
        $data['sort_link'] = $sort_link;
        $data['sort'] = $sort;
        $data['role_key'] = 'AbsenceListApprove';

        return AdminController::responseApi(200, null, null, $data);
    }

    public function countListApproveApi(Request $request)
    {
        $this->authorize('action', $this->viewAppr);

        $data = array();
        $data['countListApprove'] = Absence::query()
            ->where('Approved', 0)
            ->where('RequestManager', 'like', '%,' . Auth::user()->id . ',%')
            ->count();
        $data['role_key'] = 'AbsenceListApprove';
        return AdminController::responseApi(200, null, null, $data);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function apprAbsenceApi(Request $request, $id = null)
    {
        $this->authorize('action', $this->app);
        if ($id != null) {
            $one = Absence::find($id);
            $MasterDataValue = DB::table('absences')->where('id', $id)->pluck('MasterDataValue')[0];
            $UidValue = DB::table('absences')->where('id', $id)->pluck('UID')->toArray();
            if ($one) {
                $one->UpdateBy = Auth::user()->id;
                $one->ApprovedDate = Carbon::now();
                $one->Approved = 1;
                $one->save();
                // firebase notification
                $arrTokenAd = collect(DB::table('push_token')->where('role_group', 2)->where('allow_push', 1)->whereNull('deleted_at')->pluck('token_push')->toArray());
                $arrToken = ($arrTokenAd->merge(DB::table('push_token')->where('UserID', $UidValue)->whereNull('deleted_at')->pluck('token_push')->toArray()))->all();
                $arrToken = array_unique($arrToken);
                if (count($arrToken) > 0) {
                    $sendData = [];
                    $sendData['id'] = $one->id;
                    $sendData['data'] = DB::table('master_data')->where('DataValue', $MasterDataValue)->pluck('DataKey')->first();

                    $messNotification = DB::table('master_data')->where('DataValue', $MasterDataValue)->pluck('Name')->toArray();
                    $master_data_name = mb_strtolower($messNotification[0], 'UTF-8');
                    $headrmess = DB::table('users')->where('id', $one->UpdateBy)->pluck('FullName')->first() . " đã duyệt lịch " . $master_data_name;

                    $bodyNoti = "Từ " . Carbon::parse($one['SDate'])->format('d/m/Y H:i:s') . ' đến ' . Carbon::parse($one['EDate'])->format('d/m/Y H:i:s');

                    NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData, Carbon::createFromFormat('Y-m-d H:i:s', $one['EDate']));
                }

                $this->sendMail($one, 'Gửi anh/chị/em trong công ty');

                return AdminController::responseApi(200, null, __('admin.success.approve'));
            }
        }
        return AdminController::responseApi(422, __('admin.error.data'));
    }

    /**
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function unApprAbsenceApi(Request $request, $id = null)
    {
        $this->authorize('action', $this->app);
        if (!isset($request['Comment']) || $request['Comment'] == '') {
            return AdminController::responseApi(422, __('admin.error.comment-missing'));
        }

        if ($id != null) {
            $one = Absence::find($id);
            if ($one) {
                $one->UpdateBy = Auth::user()->id;
                $one->ApprovedDate = Carbon::now();
                $one->Approved = 2;
                $one->Comment = $request['Comment'];
                $one->save();

                // firebase notification
                $MasterDataValue = DB::table('absences')->where('id', $id)->pluck('MasterDataValue')[0];
                $UidValue = DB::table('absences')->where('id', $id)->pluck('UID')->toArray();
                $arrTokenAd = collect(DB::table('push_token')->where('role_group', 2)->where('allow_push', 1)->whereNull('deleted_at')->pluck('token_push')->toArray());
                $arrToken = ($arrTokenAd->merge(DB::table('push_token')->where('UserID', $UidValue)->whereNull('deleted_at')->pluck('token_push')->toArray()))->all();
                $arrToken = array_unique($arrToken);
                if (count($arrToken) > 0) {
                    $sendData = [];
                    $sendData['id'] = $one->id;
                    $sendData['data'] = DB::table('master_data')->where('DataValue', $MasterDataValue)->pluck('DataKey')->first();

                    $messNotification = DB::table('master_data')->where('DataValue', $MasterDataValue)->pluck('Name')->toArray();
                    $master_data_name = mb_strtolower($messNotification[0], 'UTF-8');
                    $headrmess = DB::table('users')->where('id', $one->UpdateBy)->pluck('FullName')->first() . " đã từ chối lịch " . $master_data_name;

                    $bodyNoti = "Lý do: " . $request['Comment'];

                    NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData, Carbon::createFromFormat('Y-m-d H:i:s', $one['EDate']));
                }

                $this->sendMail($one, '', $request['Comment']);

                return AdminController::responseApi(200, null, __('admin.success.un-approve'));
            }
        }
        return AdminController::responseApi(422, __('admin.error.data'));
    }

    /**
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showAbsenceManagementApi(Request $request, $orderBy = 'id', $sortBy = 'desc')
    {
        $this->authorize('action', $this->viewM);
        $recordPerPage = $this->getRecordPage();

        $data = array();
        $absences = Absence::query()->select('absences.*', 'master_data.Name', 'tb1.FullName', 'tb2.username as NameUpdateBy')
            ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
            ->leftJoin('users as tb1', 'absences.UID', '=', 'tb1.id')
            ->leftJoin('users as tb2', 'absences.UpdateBy', '=', 'tb2.id')
            ->where('UID', Auth::user()->id);
        if (Schema::hasColumn('absences', $orderBy)) {
            $absences->orderBy($orderBy, $sortBy);
        }

        $data['request'] = $request->query();
        $one = Absence::query()->select('absences.SDate', 'absences.EDate', 'absences.TotalTimeOff', 'absences.Reason', 'absences.Remark', 'master_data.Name')
            ->Join('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
            ->leftJoin('users', 'absences.UID', '=', 'users.id')->first();

        if ($one) {
            $one = $one->toArray();
            if (array_key_exists('search', $request->input())) {
                $absences = $absences->where(function ($query) use ($one, $request) {
                    foreach ($one as $key => $value) {
                        if ($key == 'Name') {
                            $query->orWhere('master_data.' . $key, 'like', '%' . $request->input('search') . '%');
                        } else {
                            if (in_array($key, ['SDate', 'EDate', 'ApprovedDate'])) {
                                $query->orWhereRaw('(DATE_FORMAT(absences.' . $key . ',"%d/%m/%Y")) like ?', '%' . $request->input('search') . '%');
                            } else {
                                $query->orWhere('absences.' . $key, 'like', '%' . $request->input('search') . '%');
                            }
                        }
                    }
                });
            }
        }

        if (!isset($request['search']) && !isset($request['Date'])) {
            $absences = $absences->where(function ($queryOne) {
                $queryOne->where('absences.SDate', '>=', Carbon::now()->startOfMonth())
                    ->orWhere('absences.EDate', '>=', Carbon::now()->startOfMonth());
            });
        }

        foreach ($data['request'] as $key => $value) {
            if (is_array($value)) {
                $value[0] != '' ? $value[0] = $this->fncDateTimeConvertFomat($value[0], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : $value[0];
                $value[1] != '' ? $value[1] = $this->fncDateTimeConvertFomat($value[1], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : $value[1];

                $absences->where(function ($query) use ($value) {
                    if ($value[0] != '' && $value[1] != '' && $value[0] !== $value[1]) {
                        $query = $query->whereBetween('absences.SDate', array(Carbon::parse($value[0])->startOfDay(), Carbon::parse($value[1])->endOfDay()))
                            ->orWhereBetween('absences.EDate', array(Carbon::parse($value[0])->startOfDay(), Carbon::parse($value[1])->endOfDay()))
                            ->orWhere(function ($query1) use ($value) {
                                $query1->where('absences.SDate', '<=', Carbon::parse($value[0])->startOfDay())
                                    ->where('absences.EDate', '>=', Carbon::parse($value[1])->endOfDay());
                            });
                    }
                    if ($value[0] === $value[1] && $value[0] != '') {
                        $query = $query->whereRaw("CAST(absences.SDate AS DATE) = '$value[0]'");
                    }
                    if ($value[0] != '' && $value[1] == '') {
                        $query = $query->where('absences.SDate', '>=', Carbon::parse($value[0])->startOfDay())
                            ->orWhere('absences.EDate', '>=', Carbon::parse($value[0])->startOfDay());
                    }
                    if ($value[0] == '' && $value[1] != '') {
                        $query = $query->where('absences.SDate', '<=', Carbon::parse($value[1])->startOfDay())
                            ->orWhere('absences.EDate', '<=', Carbon::parse($value[1])->startOfDay());
                    }
                });
            }
        }

        $count = $absences->count();

        $absences = $absences->paginate($recordPerPage);
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);

        $data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $data['absences'] = $absences;
        $data['query_array'] = $query_array;
        $data['sort_link'] = $sort_link;
        $data['sort'] = $sort;

        $absenceTypes = MasterData::query()->where('DataKey', 'VM')->get();
        $totalReport = [];
        $absences->filter(function ($value, $key) use (&$totalReport) {
            if (array_key_exists($value->MasterDataValue, $totalReport)) {
                $totalReport[$value->MasterDataValue] += $value->TotalTimeOff;
            } else {
                $totalReport[$value->MasterDataValue] = $value->TotalTimeOff;
            }
        });

        $data['totalReport'] = $totalReport;
        $data['absenceTypes'] = $absenceTypes;
        $data['role_key'] = 'AbsenceManagement';

        return AdminController::responseApi(200, null, null, $data);
    }

    /**
     * @param AbsenceRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function storeManagementApi(AbsenceRequest $request)
    {
        $this->authorize('action', $this->addM);
        $result = $this->insertOrUpdate($request);
        if (array_key_exists('success', $result)) {
            return AdminController::responseApi($result['status'], null, $result['success']);
        } else {
            return AdminController::responseApi($result['status'], $result['error']);
        }
    }

    /**
     * @param AbsenceRequest $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateManagementApi(AbsenceRequest $request, $id = null)
    {
        $this->authorize('action', $this->editM);
        $result = $this->insertOrUpdate($request, $id);
        if (array_key_exists('success', $result)) {
            return AdminController::responseApi($result['status'], null, $result['success']);
        } else {
            return AdminController::responseApi($result['status'], $result['error']);
        }
    }

    /**
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function deleteManagementApi($id = null)
    {
        $this->authorize('action', $this->deleteM);
        if ($id != null) {
            $one = Absence::find($id);
            if ($one) {
                $one->delete();
                return AdminController::responseApi(200, null, __('admin.success.delete'));
            }
        }
        return AdminController::responseApi(422, __('admin.error.data'));
    }


    // AbsenceController

    /**
     * @param $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function insertOrUpdate($request, $id = null)
    {
        if (count($request->input()) === 0) {
            return ['error' => __('admin.error.data'), 'status' => 422];
        }
        try {
            $one = isset($id) && $id != null ? Absence::find($id) : new Absence();
            foreach ($request->all() as $key => $value) {
                if (Schema::hasColumn('absences', $key)) {
                    if ($key == 'SDate') {
                        $value = $this->fncDateTimeConvertFomat($value, 'd/m/Y H:i', 'Y-m-d H:i');
                    }
                    if ($key == 'EDate') {
                        $value = $this->fncDateTimeConvertFomat($value, 'd/m/Y H:i', 'Y-m-d H:i');
                    }
                    $one->$key = $value;
                }
            }

            if (Carbon::parse($one->SDate)->gt(Carbon::parse($one->EDate))) {
                return ['error' => __('admin.error.absence.date'), 'status' => 422];
            }

            $one->TotalTimeOff = $this->getDiffHours(Carbon::parse($one->SDate), Carbon::parse($one->EDate), $request->UID) * 60;

            // nếu xin nghỉ vào cuối tuần
            if ($this->checkHoliday(Carbon::parse($one->SDate)) && $this->checkHoliday(Carbon::parse($one->EDate))) {
                return ['error' => __('admin.error.date-holiday'), 'status' => 422];
            }
            if ($one->TotalTimeOff < 5) {
                return ['error' => __('admin.error.date-range'), 'status' => 422];
            }
            $check = DB::table('absences')
                ->where('UID', $request->UID)
                ->where(function ($query) use ($one) {
                    $query->orWhere(function ($query) use ($one) {
                        $query->orWhereBetween('SDate', array($one->SDate, $one->EDate));
                        $query->orWhereBetween('EDate', array($one->SDate, $one->EDate));
                    });
                    $query->orWhere(function ($query) use ($one) {
                        $query->where('SDate', '<=', $one->SDate);
                        $query->where('EDate', '>=', $one->EDate);
                    });
                })
                ->whereNull('deleted_at');
            if (array_key_exists('id', $request->input())) {
                $check = $check->where('id', '!=', $request->id);
            }
            $check = $check->first();
            if ($check) {
                return ['error' => __('admin.error.absence.isset'), 'status' => 422];
            }
            $one->RequestManager = ',' . implode(',', $request->RequestManager) . ',';
            $one->AbsentDate = Carbon::now()->format('Y-m-d');
            $one->save();

            if (!$one) {
                return ['error' => __('admin.error.save'), 'status' => 403];
            } else {
                // $this->sendMail($request->all(), 'Kính gửi Ban giám đốc');
                $arrTokenAd = collect(DB::table('push_token')->where('role_group', 2)->where('allow_push', 1)->whereNull('deleted_at')->pluck('token_push')->toArray());
                $arrToken = ($arrTokenAd->merge(DB::table('push_token')->where('UserID', $one->UID)->whereNull('deleted_at')->pluck('token_push')->toArray()))->all();
                $arrToken = array_unique($arrToken);
                if (count($arrToken) > 0) {
                    $sendData = [];
                    $sendData['id'] = $one->id;
                    $sendData['data'] = DB::table('master_data')->where('DataValue', $request['MasterDataValue'])->pluck('DataKey')->first();
                    $messNotification = DB::table('master_data')->where('DataValue', $request['MasterDataValue'])->pluck('Name')->toArray();
                    $master_data_name = mb_strtolower($messNotification[0], 'UTF-8');
                    $FullName = DB::table('users')->where('id', $request['UID'])->pluck('FullName')->first();
                    $headrmess = $FullName . " xin phép " . $master_data_name;

                    $bodyNoti = "Từ " . $request['SDate'] . ' đến ' . $request['EDate'];

                    NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
                }

                $this->sendMail($request->all(), 'Kính gửi Ban giám đốc');
                return ['success' => __('admin.success.save'), 'status' => 200];
            }
        } catch (\Exception $e) {
            return ['error' => $e->getMessage(), 'status' => 422];
        }
    }

    /**
     * @param $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function insertToWorkingShecdule(AbsenceRequest $request, $id = null)
    {
        $arayUID = $request->input();
        if (count($request->input()) === 0) {
            return ['error' => __('admin.error.data'), 'status' => 422];
        }
        try {
            foreach ($arayUID['RequestUID'] as $keyUID => $valueUID) {
                # code...
                $roomIdToWorking = DB::table('users')->where('id', $valueUID)->value('RoomId');
                $one = isset($id) && $id != null ? Absence::find($id) : new Absence();
                foreach ($request->all() as $key => $value) {
                    if (Schema::hasColumn('absences', $key)) {
                        if ($key == 'SDate') {
                            $value = $this->fncDateTimeConvertFomat($value, 'd/m/Y H:i', 'Y-m-d H:i');
                        }
                        if ($key == 'EDate') {
                            $value = $this->fncDateTimeConvertFomat($value, 'd/m/Y H:i', 'Y-m-d H:i');
                        }
                        $one->$key = $value;
                    }
                }

                if (Carbon::parse($one->SDate)->gt(Carbon::parse($one->EDate))) {
                    return ['error' => __('admin.error.absence.date'), 'status' => 422];
                }

                $one->TotalTimeOff = $this->getDiffHours(Carbon::parse($one->SDate), Carbon::parse($one->EDate), $request->UID) * 60;

                // nếu xin nghỉ vào cuối tuần
                if ($this->checkHoliday(Carbon::parse($one->SDate)) && $this->checkHoliday(Carbon::parse($one->EDate))) {
                    return ['error' => __('admin.error.date-holiday'), 'status' => 422];
                }
                if ($one->TotalTimeOff < 5) {
                    return ['error' => __('admin.error.date-range'), 'status' => 422];
                }
                $check = DB::table('absences')
                    ->where('UID', $valueUID)
                    ->where(function ($query) use ($one) {
                        $query->orWhere(function ($query) use ($one) {
                            $query->orWhereBetween('SDate', array($one->SDate, $one->EDate));
                            $query->orWhereBetween('EDate', array($one->SDate, $one->EDate));
                        });
                        $query->orWhere(function ($query) use ($one) {
                            $query->where('SDate', '<=', $one->SDate);
                            $query->where('EDate', '>=', $one->EDate);
                        });
                    })
                    ->whereNull('deleted_at');
                if (array_key_exists('id', $request->input())) {
                    $check = $check->where('id', '!=', $request->id);
                }
                $check = $check->first();
                if ($check) {
                    return ['error' => __('admin.error.absence.isset'), 'status' => 422];
                }
                $one->RoomID = $roomIdToWorking;
                $one->UID = $valueUID;
                $one->RequestManager = ',' . implode(',', $request->RequestManager) . ',';
                $one->AbsentDate = Carbon::now()->format('Y-m-d');
                // print($one);
                $one->save();
            }
            if (!$one) {
                return ['error' => __('admin.error.save'), 'status' => 403];
            } else {
                foreach ($arayUID['RequestUID'] as $keyUID => $valueUID) {
                    $roomIdToWorking = DB::table('users')->where('id', $valueUID)->value('RoomId');
                    $sendMail = array();
                    $sendMail['RoomID'] = $roomIdToWorking;
                    $sendMail['UID'] = $valueUID;
                    $sendMail['MasterDataValue'] = $request->MasterDataValue;
                    $sendMail['SDate'] = $request->SDate;
                    $sendMail['EDate'] = $request->EDate;
                    $sendMail['Reason'] = $request->Reason;
                    $sendMail['Remark'] = $request->Remark;
                    $sendMail['RequestManager'] = $request->RequestManager;
                    $this->sendMail($sendMail, 'Kính gửi Ban giám đốc');
                }
                return ['success' => __('admin.success.save'), 'status' => 200];
            }
        } catch (\Exception $e) {
            return ['error' => $e->getMessage(), 'status' => 422];
        }
    }


    /**
     * Send data mail to serve
     * @param $array
     * @param $header
     * @param null $comment
     * @return bool
     */
    public function sendMail($array, $header, $comment = null, $int = null, $email = null)
    {
        $rooms = Room::find($array['RoomID']);
        $users = User::find($array['UID']);
        $name_user = '';

        if ($int == 2) {
            $Arraylist_user = User::query()->whereIn("id", is_array($array['RequestManager']) ? $array['RequestManager'] : explode(',', $array['RequestManager']))->pluck("FullName");
        } else {
            if (isset($int)) {
                $Arraylist_user = User::query()->whereIn("id", explode(',', $array['UpdateBy']))->pluck("FullName");
                $name_user = $Arraylist_user[0];
            } else {
                $Arraylist_user = User::query()->whereIn("id", is_array($array['RequestManager']) ? $array['RequestManager'] : explode(',', $array['RequestManager']))->pluck("FullName");
                if (isset($array['UpdateBy'])) {
                    $arrayname_user = User::query()->whereIn("id", explode(',', $array['UpdateBy']))->pluck("FullName");
                    $name_user = $arrayname_user[0];
                }
            }
        }

        $list_user = '';
        for ($i = 0; $i < count($Arraylist_user); $i++) {
            if ($i + 1 == count($Arraylist_user))
                $list_user .= $Arraylist_user[$i];
            else
                $list_user .= $Arraylist_user[$i] . ', ';
        }
        //kiểm tra ngày trong quá khứ
        if (!isset($array['Approved'])) {
            $End = $this->fncDateTimeConvertFomat($array['EDate'], 'd/m/Y H:i', 'Y-m-d');
            // if (Carbon::parse($End)->lt(Carbon::now()->format('Y-m-d'))) {
            //    return false;
            // }
        }

        //sửa lich nghỉ
        if (isset($array['Approved'])) {
            if (Carbon::parse($array['EDate'])->lt(Carbon::now()->format('Y-m-d'))) {
                return false;
            }
            //format date for subjectMail
            $array['SDate'] = $this->fncDateTimeConvertFomat($array['SDate'], 'Y-m-d H:i:s', 'd/m/Y H:i');
            $array['EDate'] = $this->fncDateTimeConvertFomat($array['EDate'], 'Y-m-d H:i:s', 'd/m/Y H:i');
        }

        $arrMail = [];
        $arrMailCc = [];


        if (!is_array($array['RequestManager'])) {
            $array['RequestManager'] = array_filter(explode(',', $array['RequestManager']));
        }

        $arrayMailCc = MasterData::query()->where('DataValue', 'EM001')->get();
        $mailCc = array_filter(explode(',', $arrayMailCc[0]['DataDescription']));

        foreach ($array['RequestManager'] as $value) {
            $mailUser = User::find($value);
            if ($mailUser->email != null) {
                $arrMailAddressTo = $mailUser->email;
                $arrMail[] = $arrMailAddressTo;
            }
        }

        $arrMailAddressTo = array_unique($arrMail);

        foreach ($mailCc as $value) {
            $arrMailCc[] = $value;
        }

        $viewBladeMail = 'template_mail.absences-mail';
        $apr = isset($array['Approved']) ? $array['Approved'] : '';

        if ($array['MasterDataValue'] == 'VM007') {
            $master_data_name = 'vắng mặt';
        } else {
            $master_data = MasterData::query()->where('DataValue', $array['MasterDataValue'])->first()->toArray();
            $master_data_name = $master_data['Name'];
        }

        //cách gọi Mr,Ms,Mrs
        if ($users['Gender'] == 0) {
            $users['Gender'] = 'Mr';
        }
        if ($users['Gender'] == 1 && $users['MaritalStt'] == 1) {
            $users['Gender'] = 'Mrs';
        }
        if ($users['Gender'] == 1 && $users['MaritalStt'] == 0) {
            $users['Gender'] = 'Ms';
        }

        //gộp thời gian nếu cùng ngày
        $dateStart = $this->fncDateTimeConvertFomat($array['SDate'], 'd/m/Y H:i', 'Y-m-d H:i:s');
        $dateEnd = $this->fncDateTimeConvertFomat($array['EDate'], 'd/m/Y H:i', 'Y-m-d H:i:s');
        $diffDay = Carbon::parse(Carbon::parse($dateStart)->format('Y-m-d'))->diffInDays(Carbon::parse(Carbon::parse($dateEnd)->format('Y-m-d')));

        $viewDay = $array['SDate'] . ' - ' . $array['EDate'];
        if ($diffDay == 0) {
            $viewDay = Carbon::parse($dateStart)->format('d/m/Y') . ' ' . Carbon::parse($dateStart)->format('H:i') . '-' . Carbon::parse($dateEnd)->format('H:i');
        }
        //chuyển sang viết thường
        $master_data_name = mb_strtolower($master_data_name, 'UTF-8');


        $dataBinding = [
            'Header' => $header,
            'MasterDataValue' => $master_data_name,
            'FullName' => $users['FullName'],
            'Room' => $rooms['Name'],
            'viewDay' => $viewDay,
            'Reason' => $array['Reason'],
            'Remark' => $array['Remark'],
            'Gender' => $users['Gender'],
            'Approved' => $apr,
            'Comment' => $comment,
            'Management' => $list_user,
            'UpdateBy' => $name_user,
        ];


        //tiêu đề mail (subjectMail)
        if (isset($array['Approved'])) {
            $nameFrom = 'AKB Văn Phòng';
            if ($array['Approved'] == 1) {
                $subjectMail = 'TB ' . $users['FullName'] . ' ' . mb_strtolower($master_data_name, 'UTF-8') . ' (' . $viewDay . ')';
            } else {
                $Arraylist_user = User::query()->whereIn("id", is_array($array['RequestManager']) ? $array['RequestManager'] : explode(',', $array['RequestManager']))->pluck("FullName");
                $subjectMail = 'TB ' . $users['FullName'] . ' ' . mb_strtolower($master_data_name, 'UTF-8') . ' (' . $viewDay . ')';
            }

            $arrMailAddressTo = [];
            if ($users['email'] != null) {
                array_push($arrMailAddressTo, $users['email']);
            } else {
                $replace_mailTO = MasterData::query()->where('DataValue', '=', 'EM006')->first();
                $arrMailAddressTo = explode(',', $replace_mailTO->DataDescription);
            }
        } else {
            $nameFrom = $users['FullName'] . ' - ' . $rooms['Name'];
            $subjectMail = 'TB xin ' . mb_strtolower($master_data_name, 'UTF-8') . ' (' . $viewDay . ')';
            if ($users['email'] != null) {
                $arrMailCc[] = $users['email'];
            }
        }

        $addressMailCc = array_diff($arrMailCc, $arrMailAddressTo);

        $this->SendMailWithView([
            self::KEY_SUBJECT_MAIL => $subjectMail,
            self::KEY_VIEW_MAIL => $viewBladeMail,
            self::KEY_DATA_BINDING => $dataBinding,
            self::KEY_MAIL_NAME_FROM => $nameFrom,
            self::KEY_MAIL_ADDRESS_TO => $arrMailAddressTo,
            self::KEY_MAIL_ADDRESS_CC => $addressMailCc,
        ]);
    }
}
