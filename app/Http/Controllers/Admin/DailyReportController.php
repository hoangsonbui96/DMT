<?php

namespace App\Http\Controllers\Admin;

use App\CalendarEvent;
use App\DailyReport;
use App\Exports\DailyReportByMonth;
use App\Exports\DailyReportExport;
use App\Exports\YearlyReportExport;
use App\Http\Requests\DailyReportOneRequest;
use App\Http\Requests\DailyReportRequest;
use App\Http\Services\DailyReport\DailyReportService;
use App\MasterData;
use App\Menu;
use App\Model\Absence;
use App\model\ListPosition;
use App\OvertimeWork;
use App\Project;
use App\RoleScreenDetail;
use App\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


/**
 * Class DailyReportController
 * @package App\Http\Controllers\Admin
 * Controller screen Daily Report
 */
class DailyReportController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $export;
    protected $dailyExport;
    protected $user;

    protected $working_days = [1, 2, 3, 4, 5];
    const KEYMENU = array(
        "add" => "DailyReportsAdd",
        "view" => "DailyReports",
        "edit" => "DailyReportsEdit",
        "delete" => "DailyReportsDelete",
        "dailyExport" => "DailyReportsExport",
        "export" => "TotalReportExport"
    );

    /**
     * DailyReportController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {

        if (strpos($request->getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }

        $array = $this->RoleView('DailyReports', ['DailyReports', 'TotalReport']);
        $this->menu = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if ($value == $row->alias)
                    $this->$key = $row;
            }
        }
        $this->export = RoleScreenDetail::query()
            ->where('alias', 'TotalReportExport')
            ->first();
        $this->user = $this->GetListUser(self::USER_ACTIVE_FLAG);
    }

    /**
     * @param Request $request
     * @return View (daily-report)
     * @throws AuthorizationException
     * Get data Daily Report and return view
     */
    public function index(Request $request)
    {
        $this->authorize('view', $this->menu);
        // total time
        $this->totalDailyReport($request);

        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;
        $this->data['dailyExport'] = $this->dailyExport;
        return $this->viewAdminLayout('daily-report', $this->data);
    }

    /**
     * @param $request
     * @param null $month
     * @param null $year
     * Calculate time total project
     */
    public function totalDailyReport($request)
    {
        $currentUserId = auth()->id();
        $this->data['request'] = $request->query();
        $masterData = null;
        $masterData = MasterData::query()->where('DataKey', 'BC')->select('Name', 'DataValue');

        // test project
        $this->data['user'] = $request->has('UserID') ? User::find($request['UserID']) : \auth()->user();
        $selectedProjectId = $request->has('ProjectID') ? $request['ProjectID'] : null;
        $date = $request['time'] ? Carbon::createFromFormat('m/Y', $request['time']) : Carbon::now();
        $date = $date->format(self::FOMAT_DB_YMD);
        $month = Carbon::parse($date)->format('m');
        $year = Carbon::parse($date)->format('Y');
        $firstOfMonth = Carbon::parse($date)->firstOfMonth();
        $endOfMonth = Carbon::parse($date)->endOfMonth();
        $dailyReports = DailyReport::query()
            ->select(
                'daily_reports.id',
                'daily_reports.UserID',
                'projects.NameVi',
                'projects.NameShort',
                'daily_reports.Date',
                'daily_reports.ScreenName',
                'daily_reports.DateCreate',
                'daily_reports.WorkingTime',
                'daily_reports.Progressing',
                'daily_reports.Delay',
                'daily_reports.Note',
                'daily_reports.Status',
                'daily_reports.Issue',
                'daily_reports.Contents',
                'daily_reports.ProjectID',
                'daily_reports.created_at',
                'master_data.Name',
                'master_data.DataValue as TypeWork',
                'projects.Leader'
            )
            ->selectRaw('1 AS TypeReport')
            ->join('projects', 'projects.id', '=', 'daily_reports.ProjectID')
            ->leftJoin('master_data', 'master_data.DataValue', '=', 'daily_reports.TypeWork')
            ->leftJoin('users', 'users.id', '=', 'daily_reports.UserID')
            ->whereMonth('Date', $month)
            ->whereYear('Date', $year)
            ->where('UserID', '=', $this->data['user']->id);
        if ($selectedProjectId) {
            $dailyReports = $dailyReports->where('projects.id', $selectedProjectId);
        }
        // test in here
        $OT = OvertimeWork::query()
            ->select('overtime_works.id', 'overtime_works.UserID', 'projects.NameVi', 'projects.NameShort')
            ->selectRaw('DATE_FORMAT(overtime_works.STime, \'%Y-%m-%d\') AS Date')
            ->selectRaw('\'\' AS ScreenName')
            ->selectRaw('DATE_FORMAT(overtime_works.STime, \'%Y-%m-%d\') as DateCreate')
            ->selectRaw('FORMAT((TIMESTAMPDIFF(MINUTE, overtime_works.STime, overtime_works.ETime) - overtime_works.BreakTime*60)/60, 2) AS WorkingTime')
            ->selectRaw('100 AS Progressing')
            ->selectRaw('\'0\'AS Delay')
            ->selectRaw('\'\' AS Note')
            ->selectRaw('\'\' AS Status')
            ->selectRaw('\'\' AS Issue')
            ->selectRaw('overtime_works.Content AS Contents')
            ->selectRaw('\'\' AS ProjectID')
            ->selectRaw('overtime_works.created_at AS created_at')
            ->selectRaw('\'OT\' AS Name')
            ->selectRaw('\'\' AS TypeWork')
            ->selectRaw('2 AS TypeReport')
            ->selectRaw('Leader')
            ->leftJoin('projects', 'projects.id', '=', 'overtime_works.ProjectID')
            ->leftJoin('users', 'users.id', '=', 'overtime_works.UserID')
            ->where('UserID', $this->data['user']->id)
            ->where('overtime_works.STime', '>=', $firstOfMonth)
            ->where('overtime_works.ETime', '<=', $endOfMonth)
            ->where('overtime_works.Approved', '!=', 2);
        if ($selectedProjectId) {
            $dailyReports = $dailyReports->where('projects.id', $selectedProjectId);
        }

        $dailyReports = $dailyReports->union($OT)->orderBy('Date', 'ASC')->orderBy('id', 'ASC');
        $dailyReports = $dailyReports->get();

        $this->data['dailyReports'] = $dailyReports;
        $OT = OvertimeWork::query()
            ->select('overtime_works.ProjectID', 'projects.NameVi')
            ->selectRaw('FORMAT(SUM((TIMESTAMPDIFF( MINUTE, overtime_works.STime, overtime_works.ETime ) - overtime_works.BreakTime * 60 ) / 60), 2) AS WorkingTime')
            ->leftJoin('users', 'users.id', '=', 'overtime_works.UserID')
            ->leftJoin('projects', 'projects.id', '=', 'overtime_works.ProjectID')
            ->where('UserID', $this->data['user']->id)
            ->where('overtime_works.STime', '>=', $firstOfMonth)
            ->where('overtime_works.ETime', '<=', $endOfMonth)
            ->where('overtime_works.Approved', '!=', 2)->groupBy('overtime_works.ProjectID')->get();
        if (count($OT) > 0) {
            $temp = MasterData::query()->selectRaw("'OT' as Name, 'BC999' as DataValue")->take(1);
            $masterData = $masterData->union($temp);
        }

        $masterData = $masterData->get();
        // end test project

        $total = DailyReport::query()
            ->select('daily_reports.ProjectID', 'projects.NameVi', 'projects.Leader')
            ->join('projects', 'projects.id', '=', 'daily_reports.ProjectID')
            ->whereMonth('Date', $month)
            ->whereYear('Date', $year)
            ->where('UserID', $this->data['user']->id)
            ->groupBy('daily_reports.ProjectID');

        $OT_temp = OvertimeWork::query()
            ->select('overtime_works.ProjectID', 'projects.NameVi', 'projects.Leader')
            ->leftJoin('users', 'users.id', '=', 'overtime_works.UserID')
            ->leftJoin('projects', 'projects.id', '=', 'overtime_works.ProjectID')
            ->where('UserID', $this->data['user']->id)
            ->where('overtime_works.STime', '>=', $firstOfMonth)
            ->where('overtime_works.ETime', '<=', $endOfMonth)
            ->where('overtime_works.Approved', '!=', 2)->groupBy('overtime_works.ProjectID');

        $total = $total->union($OT_temp)->get();
        $total->totalHours = 0;

        foreach ($masterData as $data) {
            $key = $data->DataValue;
            $total->$key = 0;
        }
        foreach ($total as $item) {
            $item->totalHours = 0;
            $item->Leader = array_values(array_filter(explode(',', $item->Leader)));
            foreach ($masterData as $data) {
                $key = $data->DataValue;
                $item->$key = DailyReport::query()
                    ->where('ProjectID', $item->ProjectID)
                    ->where('TypeWork', $data->DataValue)
                    ->whereMonth('Date', $month)
                    ->whereYear('Date', $year)
                    ->where('UserID', $this->data['user']->id)
                    ->sum('WorkingTime');
                $item->totalHours += $item->$key;
                $total->$key += $item->$key;

                if (count($OT) > 0) {
                    foreach ($OT as $record) {
                        if ($item->ProjectID == $record->ProjectID && $data->DataValue == 'BC999') {
                            $item->$key = $record->WorkingTime;
                            $item->totalHours += $item->$key;
                            $total->$key += $item->$key;
                            continue 2;
                        }
                    }
                }
            }
            $total->totalHours += $item->totalHours;
        }

        $currentUserPositions = [];
        $currentUserPositions = $this->getUserPosition($currentUserId);

        $listproject = $this->getProjectsByLeaderPosition($currentUserId, $currentUserPositions);
        if ($request->ProjectID != '') {
            $selectedProject = Project::where('id', $request->ProjectID)->get();
            $isActiveProject = ($selectedProject[0]->Active == 1 && $selectedProject[0]->EndDate < Carbon::now()->toDateString()) ? true : false;
            if (!$isActiveProject) {
                $listproject = $listproject->concat($selectedProject);
            }
        }
        $this->data['selectProject'] = $listproject;


        $this->data['isManager'] = $this->checkManager($currentUserId);
        $this->data['total'] = $total;
        $this->data['masterData'] = $masterData;
        $this->data['selectUser'] = $this->user;
    }

    /**
     * trả dữ liệu về view (màn báo cảo tổng hợp)
     *
     * @param Request $request
     * @param DailyReportService $dailyReportService
     * @param string $order
     * @param string $type
     * @param null $t
     * @return RedirectResponse (daily-general-report)
     */
    public function generalReports(Request $request, DailyReportService $dailyReportService, $order = "full-name", $type = "asc", $t = null)
    {
        try {
            $this->menu = Menu::query()
                ->where('RouteName', 'admin.GeneralReports')
                ->first();
            $this->authorize('view', $this->menu);
        } catch (AuthorizationException $exception) {
            abort(403);
        }
        if ($t != null && $t != "project" && $t != "work") {
            return redirect()->route('admin.GeneralReports');
        }
        $this->data["order"] = $order;
        $validated = $request->validate([
            "Project" => "nullable|array",
            "Project.*" => "required|integer|min:1",
            "WorkType" => "nullable|array",
            "WorkType.*" => "required",
            "User" => "nullable|array",
            "User.*" => "required|integer|min:1",
            "StartDate" => "nullable|date_format:d/m/Y",
            "EndDate" => "nullable|date_format:d/m/Y|after_or_equal:StartDate"
        ]);
        $this->data["selectUsers"] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $arr_user_active_id = $this->data["selectUsers"]->pluck("id")->toArray();
        if ($t == "project" || $t == null) {
            $this->data["group_project_user"] = $dailyReportService->getDataGeneralReportProject($validated, $arr_user_active_id, $order, $type);
            $this->data["total_hours"] = $this->data["group_project_user"]->sum(function ($item) {
                return $item->pivot->TotalHours;
            });
            if (empty($validated["User"])) {
                $projects = Project::query()->select("id", "NameVi");
                if (empty($validated["Project"])) {
                    $this->data["chooseProjects"] = $projects->orMember($arr_user_active_id)->get();
                } else {
                    $this->data["chooseProjects"] = $projects
                        ->whereIn("id", $validated["Project"])
                        ->get();
                }
            } else {
                $projects = Project::query()->select("id", "NameVi");
                if (empty($validated["Project"])) {
                    $this->data["chooseProjects"] = $projects->orMember($validated["User"])->get();
                } else {
                    $this->data["chooseProjects"] = Project::query()->whereIn("id", $validated["Project"])->get();
                }
            }
        }
        if ($t == "work") {
            $this->data["group_work_user"] = $dailyReportService->getDataGeneralReportWork($validated, $arr_user_active_id, $order, $type);
            $this->data["total_hours"] = $this->data["group_work_user"]->sum(function ($item) {
                return $item->pivot->TotalHours;
            });
            $this->data["chooseWorks"] = MasterData::query()
                ->select("id", "DataValue", "Name")
                ->when(!empty($validated["WorkType"]), function ($query) use ($validated) {
                    $query->whereIn("DataValue", $validated["WorkType"]);
                })
                ->where("DataKey", "BC")
                ->get();
        }

        $this->data["t"] = $t;
        $this->data["type_reverse"] = $type == "asc" ? "desc" : "asc";
        $this->data["selectProjects"] = Project::query()->get();
        $this->data["selectWorks"] = MasterData::query()->where("DataKey", "BC")->get();
        //        $this->data["selectUsers"] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['excel'] = RoleScreenDetail::where('alias', 'DailyReportSummariesExport')->first();

        if (strpos($request->getRequestUri(), 'api') !== false) {
            $this->getListDailyReport($request);
            return $this->data;
        }
        return $this->viewAdminLayout('daily-general-report', $this->data);
    }

    /**
     * lấy dữ liệu cho màn báo cáo tổng hợp
     *
     * @param $request
     * @return RedirectResponse
     */
    public function getListDailyReport($request)
    {
        if ($request->has('Date')) {
            if (
                DateTime::createFromFormat('d/m/Y', $request['Date'][0]) === FALSE && $request['Date'][0] != '' ||
                DateTime::createFromFormat('d/m/Y', $request['Date'][1]) === FALSE && $request['Date'][1] != ''
            ) {
                return Redirect::back();
            }
        }

        //Get list dailyReport
        $userList = DailyReport::query()
            ->select('daily_reports.*', 'users.FullName')
            ->join('users', 'users.id', '=', 'daily_reports.UserID')
            ->groupBy('UserID')
            ->orderBy('UserID');

        $this->queryWithCondition($request, $userList);
        $this->data['request'] = $request->query();
        $userList = $userList->get();

        if ($request->has('Project')) {
            $projects = Project::query()->whereIn('id', $request['Project'])->get();
        } else {
            $projects = DailyReport::query()
                ->select('projects.*')
                ->join('projects', 'projects.id', 'daily_reports.ProjectID')
                ->groupBy('ProjectID')
                ->orderBy('ProjectID', 'asc');
            $this->queryWithCondition($request, $projects);
            $projects = $projects->get();
        }
        $totalTimeOnProject = [];

        foreach ($projects as $project) {
            $totalTimeOnProject[] = 0;
        }

        foreach ($userList as $user) {
            $tempData = [];
            $key = 0;
            foreach ($projects as $project) {
                $works = DailyReport::query()->where('ProjectID', $project->id)->where('UserID', $user->UserID);
                $this->queryWithCondition($request, $works);
                $works = $works->get();
                $totalHours = 0;

                foreach ($works as $work) {
                    $totalHours += floatval($work->WorkingTime);
                }

                $tempData[] = number_format($totalHours, 2);
                $user->workOnProject = $tempData;
                $user->totalOvertime += floatval($totalHours);

                if ($tempData[$key] != 0) {
                    $totalTimeOnProject[$key] += floatval($totalHours);
                }
                $key++;
            }
        }

        $this->data['userList'] = $userList;
        $this->data['projects'] = $projects;
        $this->data['totalOvertimeOnProject'] = $totalTimeOnProject;
        return $this->data;
    }

    /**
     * xuất excel của màn báo cáo tổng hợp
     *
     * @param Request $request
     * @return RedirectResponse|BinaryFileResponse
     */
    public function export(Request $request)
    {
        $record = $this->getListDailyReport($request);

        if (isset($record['userList'][0])) {
            return Excel::download(new DailyReportExport($record), 'DailyReport.xlsx');
        }
        return $this->jsonErrors('Không có dữ liệu!');
    }

    /**
     * xuất excel của màn báo cáo hàng ngày
     *
     * @param Request $request
     * @return RedirectResponse|BinaryFileResponse
     */
    public function exportReport(Request $request)
    {
        $record = $this->getListDailyReport($request);
        $time = explode('/', $record['request']['time']);
        $this->totalDailyReport($request);
        $data = $this->data;
        if (count($data['dailyReports']) > 0) {
            return Excel::download(new DailyReportByMonth($data), $this->data['user']->FullName . '_' . $time[0] . '_' . $time[1] . '.xlsx');
        }
        return $this->jsonErrors('Không có dữ liệu!');
    }

    public function queryWithCondition($request, $query)
    {
        if (@$request->query()['Date'][0] == null && @$request->query()['Date'][1] == null) {
            $query = $query->where('Date', '>=', Carbon::now()->startOfMonth())
                ->where('Date', '<=', Carbon::now());
        } else {
            foreach ($request->query() as $key1 => $value) {

                if ($key1 == 'Date') {
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

                    if ($value[0] != '' && $value[1] != '' && $value[0] !== $value[1]) {
                        $query = $query->where('Date', '>=', Carbon::parse($value[0])->startOfDay())
                            ->where('Date', '<=', Carbon::parse($value[1])->endOfDay());
                    }
                    if ($value[0] != '' && $value[1] != '' && $value[1] == $value[0]) {
                        $query = $query->whereRaw("CAST(daily_reports.Date AS DATE) = '$value[0]'");
                    }
                    if ($value[0] != '' && $value[1] == '') {
                        $query = $query->where('Date', '<=', Carbon::now())
                            ->where('Date', '>=', Carbon::parse($value[0])->startOfDay());
                    }
                    if ($value[0] == '' && $value[1] != '') {
                        $query = $query->where('Date', '>=', Carbon::parse()->startOfYear())
                            ->where('Date', '<=', Carbon::parse($value[1])->endOfDay());
                    }
                }
            }
        }
    }

    /**
     * Show popup insert,update can insert multiple records
     * @param Request $request
     * @param null $id
     * @param null $del
     * @return View (daily-detail)
     */
    public function showDetail(Request $request, $id = null, $del = null)
    {
        $currentUserId = auth()->id();
        $isManager = $this->checkManager($currentUserId);
        $this->data['isManager'] = $isManager;

        $now = date(self::FOMAT_DB_YMD);
        $now_string = \Illuminate\Support\Carbon::parse($now)->toDateString();
        $this->data['projects'] = Project::query()
            ->where('Active', 1)
            ->where(function ($query) use ($now_string) {
                $query->where('StartDate', '<=', $now_string)->where('EndDate', '>=', $now_string)->orWhereNull('EndDate');
            })
            ->Where(function ($query) use ($request) {
                if ($request['reqId'] != '') {
                    $query->where('Member', 'like', '%' . $request['reqId'] . '%')
                        ->orWhere('Leader', 'like', '%' . $request['reqId'] . '%');
                } else {
                    $query->where('Member', 'like', '%' . Auth::user()->id . '%')
                        ->orWhere('Leader', 'like', '%' . Auth::user()->id . '%');
                }
            })->get();
        $this->data['masterDatas'] = MasterData::query()
            ->select('Name', 'DataValue')
            ->where('DataKey', 'BC')->orderBy('DataDisplayOrder', 'asc')->get();



        if ($id != null) {

            $userID = DailyReport::find($id)->UserID;
            $this->data['dailyInfo'] = DailyReport::find($id);

            $this->data['isLeader'] = false;
            $project = Project::find($this->data['dailyInfo']->ProjectID);
            $this->data['projecto'] = $project;
            if (in_array($currentUserId, explode(',', $project->Leader))) {
                $this->data['isLeader'] = true;
            }

            $this->data['all_project'] = Project::query()
                ->where('Member', 'like', '%' . $userID . '%')
                ->orWhere('Leader', 'like', '%' . $userID . '%')->get();

            if ($del == 'del') {
                $one = DailyReport::find($id);
                if ($one != null) {
                    $one->delete();
                }
                return 1;
            }

            if ($this->data['dailyInfo']) {
                $this->data['isOwner'] = false;
                if ($this->data['dailyInfo']->UserID == $currentUserId) {
                    $this->data['isOwner'] = true;
                }
                return $this->viewAdminIncludes('daily-detail', $this->data);
            } else {
                return "";
            }
        } else {
            return $this->viewAdminIncludes('daily-detail', $this->data);
        }
    }

    /**
     * Show popup insert,update, can insert one records same content
     * @param Request $request
     * @param null $id
     * @return View (daily-detail-one)
     */
    //    public function showDetailOne(Request $request, $id = null)
    //    {
    ////        $now = date(self::FOMAT_DB_YMD);
    ////        $now_string = \Illuminate\Support\Carbon::parse($now)->toDateString();
    //        $this->data['projects'] = Project::query()
    //            ->where('Active', 1)
    ////            ->whereDate('EndDate', '>=', $now)
    //            ->Where(function ($query) use ($request) {
    //                if (isset($request['reqId']) && $request['reqId'] != '') {
    //                    $query->where('Member', 'like', '%' . $request['reqId'] . '%')
    //                        ->orWhere('Leader', 'like', '%' . $request['reqId'] . '%');
    //                } else {
    //                    $query->where('Member', 'like', '%' . Auth::user()->id . '%')
    //                        ->orWhere('Leader', 'like', '%' . Auth::user()->id . '%');
    //                }
    //            })->get();
    //        $this->data['masterDatas'] = MasterData::query()
    //            ->select('Name', 'DataValue')
    //            ->where('DataKey', 'BC')->get();
    //        $this->data['all_project'] = Project::query()
    //            ->where('Member', 'like', '%' . $request['reqId'] . '%')
    //            ->orWhere('Leader', 'like', '%' . $request['reqId'] . '%')->get();
    //        if (strpos(\Request::getRequestUri(), 'api') !== false) {
    //            return $this->data;
    //        }
    //        if ($id != null) {
    //            $this->data['dailyInfo'] = DailyReport::find($id);
    //            if ($this->data['dailyInfo']) {
    //                return $this->viewAdminIncludes('daily-detail-one', $this->data);
    //            } else {
    //                return "";
    //            }
    //        } else {
    //            return $this->viewAdminIncludes('daily-detail-one', $this->data);
    //        }
    //    }

    /**
     * Process insert multiple records,update one records
     * @param Request $request
     * @param null $id
     * @return string|void
     */

    public function store(DailyReportRequest $request)
    {
        $currentUserId = auth()->id();
        if (count($request->input()) === 0) {
            return abort('404');
        }

        $isManager = $this->checkManager($currentUserId);

        try {
            if (isset($request->id) && count($request->id) > 0) {
                $one = DailyReport::find($request->id[0]);
                $one->Status = 0;
                foreach ($request->all() as $key => $value) {
                    if (Schema::hasColumn('daily_reports', $key)) {
                        if ($key == 'Date') {
                            $value[0] = $this->fncDateTimeConvertFomat(!is_null($value) && count($value) > 0 ? $value[0] : date("d/m/Y"), self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                        } elseif ($key == 'WorkingTime' || $key == 'Progressing') {
                            $value[0] = number_format($value[0], 2, '.', '');
                        }
                        $one->$key = ($key !== 'UserID' && !is_null($value) && count($value) > 0) ? $value[0] : null;
                    }
                }
                if ($request->has('reqID')) {
                    $one->UserID = !is_null($request['reqID']) && count($request['reqID']) > 0 ? $request['reqID'][0] : null;
                }
                if ($request->has('UserID')) {
                    $one->UserID = $request['UserID'];
                }
                if ($this->StringIsNullOrEmpty($one->UserID)) {
                    $one->UserID = Auth::user()->id;
                }
                $one->save();
            } else {
                if (count($request->input()) > 0) {
                    //                    return $this->jsonErrors($request->all());
                    foreach ($request->Date as $key => $value) {
                        $arr = explode(',', $value);
                        foreach ($arr as $key1 => $value1) {
                            $one = new DailyReport();
                            $one->Date = $this->fncDateTimeConvertFomat($value1, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                            $one->DateCreate = date(now());
                            $one->ProjectID = $request->ProjectID[$key];
                            $one->ScreenName = $request->ScreenName[$key];
                            $one->TypeWork = $request->TypeWork[$key];
                            $one->Contents = $request->Contents[$key];
                            $one->WorkingTime = number_format($request->WorkingTime[$key], 2, '.', '');
                            $one->Progressing = number_format($request->Progressing[$key], 2, '.', '');

                            $project = Project::find($one->ProjectID);
                            $isLeader = false;
                            if (in_array($currentUserId, explode(',', $project->Leader))) {
                                $isLeader = true;
                            }
                            if ($isLeader || $isManager) {
                                $one->Status = 2;
                                $one->ApprovedBy = $currentUserId;
                                $one->ApprovedAt = date('Y-m-d H:i:s');
                            } else {
                                $one->Status = 0;
                            }
                            $one->Note = $request->Note[$key];
                            if (isset($request['reqID'])) {
                                $one->UserID = $request['reqID'];
                            }

                            if ($this->StringIsNullOrEmpty($one->UserID)) {

                                $one->UserID = Auth::user()->id;
                            }

                            $one->save();
                        }
                    }
                }
            }

            return $this->jsonSuccessWithRouter('admin.DailyReports');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function storeStatic(DailyReportRequest $request)
    {
        if (count($request->input()) === 0) {
            return abort('404');
        }
        try {
            // dump(isset($request->id) && count($request->id) > 0);
            if (isset($request->id) && count($request->id) > 0) {
                $one = DailyReport::find($request->id[0]);
                $date = \DateTime::createFromFormat(self::FOMAT_DISPLAY_DMY, $request->Date[0]);
                $one->Date = $date->format(self::FOMAT_DB_YMD);
                $one->DateCreate = date(now());
                $one->ProjectID = $request->ProjectID[0];
                $one->ScreenName = $request->ScreenName[0];
                $one->TypeWork = $request->TypeWork[0];
                $one->Contents = $request->Contents[0];
                $one->WorkingTime = number_format($request->WorkingTime[0], 2, '.', '');
                $one->Progressing = number_format($request->Progressing[0], 2, '.', '');
                $one->Note = $request->Note[0];
                // $one->IdWS = $request->IdWS;
                // $one->IdWS = $request->IdWS;
                $one->save();
            } else {
                if (count($request->input()) > 0) {
                    $one = new DailyReport();
                    $date = \DateTime::createFromFormat(self::FOMAT_DISPLAY_DMY, $request->Date[0]);
                    $one->Date = $date->format(self::FOMAT_DB_YMD);
                    $one->DateCreate = date(now());
                    $one->ProjectID = $request->ProjectID[0];
                    $one->ScreenName = $request->ScreenName[0];
                    $one->TypeWork = $request->TypeWork[0];
                    $one->Contents = $request->Contents[0];
                    $one->WorkingTime = number_format($request->WorkingTime[0], 2, '.', '');
                    $one->Progressing = number_format($request->Progressing[0], 2, '.', '');

                    $one->Note = $request->Note[0];
                    $one->IdWS = $request->IdWS;
                    if ($request['reqID']) {
                        $one->UserID = $request['reqID'];
                        $one->save();
                    }
                }
            }
            return response()->json(['success' => route('admin.DailyReports')]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Process insert records same content
     * @param Request $request
     * @return string|void
     */
    //    public function storeOne(Request $request)
    //    {
    //        try {
    //            if (count($request->input()) > 0) {
    //                $validator = Validator::make($request->all(),
    //                    [
    //                        'Date'          => 'required|array',
    //                        'Date.*'        => 'required|string',
    //                        'ProjectID'     => 'required|array',
    //                        'ProjectID.*'   => 'required|integer',
    //                        'ScreenName'    => 'nullable|array',
    //                        'ScreenName.*'  => 'nullable|string',
    //                        'TypeWork'      => 'required|array',
    //                        'TypeWork.*'    => 'required|string',
    //                        'Contents'      => 'required|array',
    //                        'Contents.*'    => 'required|string',
    //                        'WorkingTime'   => 'required|array',
    //                        'WorkingTime.*' => 'numeric|between:0,24',
    //                        'Progressing'   => 'required|array',
    //                        'Progressing.*' => 'numeric|between:0,100',
    //                        'Note'          => 'required|array',
    //                        'Note.*'        => 'nullable|string',
    //                    ]
    //                );
    //
    //                if ($validator->fails()) {
    //                    return $this->jsonArrErrors($validator->errors()->first());
    //                }
    //
    //                $validated = $validator->validate();
    //                foreach ($validated['Date'] as $key => $value) {
    //                    $arr = explode(',', $value);
    //                    foreach ($arr as $key1 => $value1) {
    //                        $one = new DailyReport();
    //                        $one->Date = $this->fncDateTimeConvertFomat($value1, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
    //                        $one->DateCreate = date(now());
    //                        $one->ProjectID = $validated['ProjectID'][$key];
    //                        $one->ScreenName = $validated['ScreenName'][$key];
    //                        $one->TypeWork = $validated['TypeWork'][$key];
    //                        $one->Contents = $validated['Contents'][$key];
    //                        $one->WorkingTime = $validated['WorkingTime'][$key];
    //                        $one->Progressing = $validated['Progressing'][$key];
    //
    //                        $one->Note = $validated['Note'][$key];
    //                        if (isset($request['reqID'])) {
    //                            $one->UserID = $request['reqID'];
    //                        }
    //                        if ($this->StringIsNullOrEmpty($one->UserID)) {
    //                            $one->UserID = Auth::user()->id;
    //                        }
    //                        $one->save();
    //                    }
    //                }
    //                if (strpos(\Request::getRequestUri(), 'api') !== false) {
    //                    return response()->json(['success' => 'Lưu thành công.']);
    //                }
    //
    //                return $this->jsonSuccessWithRouter('admin.DailyReports');
    //            } else {
    //                return abort('404');
    //            }
    //        } catch (Exception $e) {
    //            return $e->getMessage();
    //        }
    //    }

    /**
     * Calculate total year daily report
     *
     * @param Request $request
     * @return View (yearly-reports)
     */
    public function yearlyReports(Request $request)
    {
        $this->menu = Menu::query()
            ->where('RouteName', 'admin.YearlyReports')
            ->first();
        $this->authorize('view', $this->menu);
        $this->data['request'] = $request;
        $this->getDataYearlyReport($request);
        $this->data['export'] = RoleScreenDetail::query()->where('alias', 'YearlyReportsExport')->first();
        return view('admin.layouts.' . config('settings.template') . '.yearly-reports', $this->data);
    }

    //Export bao cao hang nam
    public function exportYearlyReport(Request $request)
    {
        $userName = User::find($request->get('UID'))->FullName;
        $arr = $this->getDataYearlyReport($request);
        if (isset($arr) && count($arr[0]) > 0) {
            $name_file = 'BCTHN' . $request['year'] . '_' . $this->convert_vi_to_en($userName) . '_' . Carbon::now()->format("d_m_Y") . '.xlsx';
            return Excel::download(new YearlyReportExport($arr[0], $request, $arr[1]), $name_file);
        }
        abort(503, "Không có dữ liệu");
    }

    //lấy dữ liệu báo cáo theo năm
    public function getDataYearlyReport($request)
    {
        $minYear = DailyReport::query()
            ->select('Date')
            ->where('Date', '!=', '0000-00-00')
            ->orderBy('Date', 'ASC')
            ->first();
        if ($minYear) {
            $this->data['minYear'] = Carbon::parse($minYear->Date)->year;
            $maxYear = DailyReport::query()
                ->select('Date')
                ->where('Date', '!=', '0000-00-00')
                ->orderBy('Date', 'DESC')
                ->first();
            $this->data['maxYear'] = Carbon::parse($maxYear->Date)->year;
        }

        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['user'] = Auth::user()->id;
        $this->data['year'] = Carbon::now()->year;

        //nếu tồn tại request kiểm tra user có phải là admin
        $userId = Auth::user()->id;
        if ($request->has('UID')) {
            $user = User::find(Auth::user()->id);
            if ($user->can('admin', $this->menu)) {
                $userId = $request['UID'];
            }
        }
        $year = Carbon::now()->year;
        if ($request->has('year')) {
            $year = $request['year'];
        }

        $projects = Project::query()
            ->select('projects.id', 'NameVi', 'NameEn', 'NameJa', 'NameShort')
            ->leftJoin('daily_reports', 'daily_reports.ProjectID', 'projects.id')
            ->whereYear('daily_reports.Date', $year)
            ->where('daily_reports.UserID', $userId)
            ->groupBy('projects.id')
            ->get();

        $masterData = MasterData::query()->where('DataKey', 'BC')
            ->select('Name', 'DataValue')->get();
        $projects_id = clone $projects;
        $daily_reports = DailyReport::query()
            ->whereIn("ProjectID", $projects_id->pluck("id")->toArray())
            ->where('UserID', $userId)
            ->whereYear('Date', $year)
            ->get();
        $projects->transform(function ($project) use ($daily_reports, $masterData) {
            $project_daily = $daily_reports->where("ProjectID", $project->id);
            $month = 1;
            while ($month <= 12) {
                $key = 'T' . $month;
                $project->$key = collect(["TotalHours" => 0]);
                $current_month = $project->$key;
                $masterData->each(function ($single_master) use ($project_daily, $current_month, $month) {
                    $data_value = $single_master->DataValue;
                    $total_work_this_month = $project_daily->sum(function ($daily) use ($month, $data_value) {
                        if (Carbon::parse($daily->Date)->month == $month && $daily->TypeWork == $data_value) {
                            return $daily->WorkingTime;
                        }
                        return 0;
                    });
                    $current_month->put($data_value, $total_work_this_month);
                    $current_month['TotalHours'] += $total_work_this_month;
                });
                $month++;
            }
            $project->TotalYear = collect(["TotalHours" => 0]);
            $current_year = $project->TotalYear;
            $masterData->each(function ($single_master) use ($project_daily, $current_year) {
                $data_value = $single_master->DataValue;
                $total_work_this_year = $project_daily->sum(function ($daily) use ($data_value) {
                    if ($daily->TypeWork == $data_value) {
                        return $daily->WorkingTime;
                    }
                    return 0;
                });
                $current_year->put($data_value, $total_work_this_year);
                $current_year['TotalHours'] += $total_work_this_year;
            });
            return $project;
        });
        $statistic_year = collect();
        for ($i = 1; $i <= 12; $i++) {
            $month = 'T' . $i;
            $statistic_year->put($month, collect());
        }
        $statistic_year->transform(function ($value, $month) use ($projects, $masterData) {
            $value->put('TotalHours', $projects->sum(function ($item) use ($month) {
                return $item->$month['TotalHours'];
            }));
            $masterData->each(function ($master) use ($month, $projects, $value) {
                $data_value = $master->DataValue;
                $value->put($data_value, $projects->sum(function ($item) use ($month, $data_value) {
                    return $item->$month[$data_value];
                }));
            });
            return $value;
        });
        $statistic_year->put("TotalYear", collect());
        $statistic_year->first()->keys()->each(function ($item) use ($projects, $statistic_year) {
            $statistic_year['TotalYear']->put($item, $projects->sum(function ($project) use ($item) {
                return $project->TotalYear[$item];
            }));
        });
        $this->data['projects'] = $projects;
        $this->data['statistic_year'] = $statistic_year;
        $this->data['masterData'] = $masterData;
        return [$projects, $statistic_year];
    }

    //thống kê tình trạng báo cáo
    public function DailyReportStatus(Request $request)
    {
        $this->menu = Menu::query()
            ->where('RouteName', 'admin.TotalReport')
            ->first();
        $this->authorize('view', $this->menu);
        $this->data['request'] = $request->query();

        $this->data['export'] = $this->export;
        $excludeUsers = MasterData::query()
            ->where('DataKey', 'NWD')
            ->first();
        if ($excludeUsers) {
            $excludeArr = explode(',', $excludeUsers->DataDescription);
        } else {
            $excludeArr = [];
        }
        $users = User::query()
            ->whereNotIn('id', $excludeArr)
            ->where('role_group', '!=', 1)
            ->where('active', 1)
            ->pluck('users.id');
        $this->data['users'] = $users;

        return $this->viewAdminLayout('total-report', $this->data);
    }

    public function showTotalDetail()
    {
        $this->data['userList'] = User::query()->select('id', 'FullName')
            ->where('Active', 1)
            ->where('role_group', '!=', 1)->get();

        $userChecked = MasterData::query()->where('DataKey', 'NWD')
            ->where('DataValue', 'NWD001')
            ->get()->first();

        $this->data['userChecked'] = $userChecked;
        $this->data['arrUserChecked'] = explode(',', $userChecked->DataDescription);

        return $this->viewAdminIncludes('total-report-detail', $this->data);
    }

    //function
    public function getUserReportStatus(Request $request)
    {
        $userId = $request->has('userId') ? $request->input('userId') : Auth::user()->id;
        $month = $request->has('month') ? $request->input('month') : Carbon::now()->format('m/Y');
        $user = User::query()
            ->select('FullName', 'STimeOfDay', 'ETimeOfDay', 'id')
            ->where('id', $userId)
            ->first();
        $user->data = $this->getReportStatus($month, $user);
        return $user;
    }

    //lấy danh sách ngày làm việc của một nhân viên trong tháng
    public function getReportStatus($month, $user)
    {
        $tmpArr = explode('/', $month);
        $userId = $user->id;
        $startDate = $tmpArr[1] . '-' . $tmpArr[0] . '-01';
        $arrDates = [];
        $arrDates1 = [];
        $master = MasterData::where('DataValue', 'WT001')->first();
        $sTimeOfDay = $user->STimeOfDay ? $user->STimeOfDay : ($master->Name ? $master->Name : '08:30');
        $eTimeOfDay = $user->ETimeOfDay ? $user->ETimeOfDay : ($master->DataDescription ? $master->DataDescription : '17:30');
        //bao cao trong thang cua nhan vien
        $userReports = DailyReport::query()
            ->select('Date', 'id')
            ->whereMonth('Date', Carbon::parse($startDate)->month)
            ->whereYear('Date', Carbon::parse($startDate)->year)
            ->where('UserID', $userId)
            ->get();
        // print_r($userReports);
        while (Carbon::parse($startDate)->format('m/Y') == $month && $startDate <= Carbon::now()->toDateString()) {
            $sTime = $startDate . ' ' . $sTimeOfDay;
            $eTime = $startDate . ' ' . $eTimeOfDay;
            $weekDay = Carbon::parse($startDate)->dayOfWeek;
            //Truong hop neu la ngay nghi cuoi tuan
            if (!in_array($weekDay, $this->working_days)) {
                //kiem tra xem co phai ngay lam bu khong
                $queryOne = CalendarEvent::query()
                    ->select('id')
                    ->where('CalendarID', 1)
                    ->where('StartDate', '<=', Carbon::parse($startDate)->toDateString())
                    ->where('EndDate', '>=', Carbon::parse($startDate)->toDateString())
                    ->where('Type', 0)
                    ->first();
                if ($queryOne) {
                    //kiểm tra xem nhân viên có xin nghỉ cả ngày ko
                    $checkAbsence = Absence::query()
                        ->select('id')
                        ->where('UID', $userId)
                        ->where('SDate', '<=', $sTime)
                        ->where('EDate', '>=', $eTime)
                        ->first();
                    if (!$checkAbsence) {
                        //Lấy danh sách 3 ngày làm việc tiếp theo + ngày hiện tại
                        // $absenceController = app()->make('App\Http\Controllers\Admin\AbsenceController');
                        $tmpArrDate = $this->getNextWorkingDays(4, $startDate);
                        $lastDate = $tmpArrDate[3];

                        //chưa viết báo cáo
                        $checkReport = $userReports->filter(function ($value, $key) use ($startDate) {
                            return $value->Date == $startDate;
                        });
                        if ($checkReport->isEmpty()) {
                            $arrDates[] = Carbon::parse($startDate)->day;
                        } else {
                            //viết báo cáo muộn
                            $checkLateReport = DailyReport::query()
                                ->select('id')
                                ->where('Date', $startDate)
                                ->where('UserID', $userId)
                                ->where('DateCreate', '>', Carbon::parse($lastDate)->toDateString())
                                ->first();
                            if ($checkLateReport) {
                                $arrDates1[] = Carbon::parse($startDate)->day;
                            }
                        }
                    }
                }
            } else {
                //kiem tra xem ngay hien tai co phai ngay le hay ngay nghi khong
                $queryOne = CalendarEvent::query()
                    ->select('id')
                    ->where('CalendarID', 1)
                    ->where('StartDate', '<=', Carbon::parse($startDate)->toDateString())
                    ->where('EndDate', '>=', Carbon::parse($startDate)->toDateString())
                    ->where('Type', '!=', 0)
                    ->first();
                if (!$queryOne) {
                    //kiểm tra xem nhân viên có xin nghỉ cả ngày ko
                    $checkAbsence = Absence::query()
                        ->select('id')
                        ->where('UID', $userId)
                        ->where('SDate', '<=', $sTime)
                        ->where('EDate', '>=', $eTime)
                        ->first();
                    if (!$checkAbsence) {
                        //Lấy danh sách 3 ngày làm việc tiếp theo + ngày hiện tại
                        // $absenceController = app()->make('App\Http\Controllers\Admin\AbsenceController');
                        $tmpArrDate = $this->getNextWorkingDays(4, $startDate);
                        $lastDate = $tmpArrDate[3];

                        //chưa viết báo cáo
                        $checkReport = $userReports->filter(function ($value, $key) use ($startDate) {
                            return $value->Date == $startDate;
                        });
                        if ($checkReport->isEmpty()) {
                            $arrDates[] = Carbon::parse($startDate)->day;
                        } else {
                            //viết báo cáo muộn
                            $checkLateReport = DailyReport::query()
                                ->select('id')
                                ->where('Date', $startDate)
                                ->where('UserID', $userId)
                                ->where('DateCreate', '>', Carbon::parse($lastDate)->toDateString())
                                ->first();

                            if ($checkLateReport) {
                                $arrDates1[] = Carbon::parse($startDate)->day;
                            }
                        }
                    }
                }
            }
            $startDate = Carbon::parse($startDate)->addDays(1)->format(self::FOMAT_DB_YMD);
        }
        return [$arrDates, $arrDates1];
    }

    //lưu danh sách không phải viết báo cáo
    public function saveArrayNWD(Request $request)
    {
        $one = MasterData::query()->where('DataKey', 'NWD')
            ->where('DataValue', 'NWD001')
            ->get()->first();
        if (isset($one)) {
            $one->DataDescription = ',' . $request['arrayID'] . ',';
            $one->save();
            return 'true';
        }
        return $this->jsonErrors('Gặp lỗi trong quá trình xóa!');
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
                    ->select('id')
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
                    ->select('id')
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
     * @return View (daily-report)
     * @throws AuthorizationException
     * Get data Daily Report and return view
     */
    public function indexApi(Request $request)
    {
        $this->authorize('action', $this->view);
        $this->totalDailyReport($request);
        $data = $this->data;
        $data['role_key'] = 'DailyReports';
        $data['projects'] = Project::query()
            ->where('Active', 1)
            ->Where(function ($query) use ($request) {
                if (isset($request['reqId']) && $request['reqId'] != '') {
                    $query->where('Member', 'like', '%' . $request['reqId'] . '%')
                        ->orWhere('Leader', 'like', '%' . $request['reqId'] . '%');
                } else {
                    $query->where('Member', 'like', '%' . Auth::user()->id . '%')
                        ->orWhere('Leader', 'like', '%' . Auth::user()->id . '%');
                }
            })->get();
        $data['all_project'] = Project::query()
            ->where('Member', 'like', '%' . $request['reqId'] . '%')
            ->orWhere('Leader', 'like', '%' . $request['reqId'] . '%')->get();
        return AdminController::responseApi(200, null, null, $data);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showDetailOneApi(Request $request, $id = null)
    {
        $data = array();
        $data['projects'] = Project::query()
            ->where('Active', 1)
            ->Where(function ($query) use ($request) {
                if (isset($request['reqId']) && $request['reqId'] != '') {
                    $query->where('Member', 'like', '%' . $request['reqId'] . '%')
                        ->orWhere('Leader', 'like', '%' . $request['reqId'] . '%');
                } else {
                    $query->where('Member', 'like', '%' . Auth::user()->id . '%')
                        ->orWhere('Leader', 'like', '%' . Auth::user()->id . '%');
                }
            })->get();
        $data['masterDatas'] = MasterData::query()
            ->select('Name', 'DataValue')
            ->where('DataKey', 'BC')->get();
        $data['all_project'] = Project::query()
            ->where('Member', 'like', '%' . $request['reqId'] . '%')
            ->orWhere('Leader', 'like', '%' . $request['reqId'] . '%')->get();
        return AdminController::responseApi(200, null, null, $data);
    }

    /**
     * @param DailyReportOneRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException
     */
    public function storeApi(DailyReportOneRequest $request)
    {
        $this->authorize('action', $this->add);

        if (count($request->input()) === 0) {
            return AdminController::responseApi(422, __('admin.error.data'));
        }

        try {
            if (count($request->input()) > 0) {
                $one = new DailyReport();
                $one->Date = $this->fncDateTimeConvertFomat($request->Date, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                $one->DateCreate = date(now());
                $one->ProjectID = $request->ProjectID;
                $one->ScreenName = $request->ScreenName;
                $one->TypeWork = $request->TypeWork;
                $one->Contents = $request->Contents;
                $one->WorkingTime = number_format($request->WorkingTime, 2, '.', '');
                $one->Progressing = number_format($request->Progressing, 2, '.', '');

                $one->Note = $request->Note;
                if (isset($request['reqID'])) {
                    $one->UserID = $request['reqID'];
                }
                if ($this->StringIsNullOrEmpty($one->UserID)) {
                    $one->UserID = Auth::user()->id;
                }
                $one->save();
            }
            return AdminController::responseApi(200, null, __('admin.success.save'));
        } catch (Exception $e) {
            return AdminController::responseApi(422, $e->getMessage());
        }
    }

    public function storeApiWorkingShecdule(DailyReportOneRequest $request)
    {

        $arayUID = $request->input();
        $this->authorize('action', $this->add);
        if (count($request->input()) === 0) {
            return AdminController::responseApi(422, __('admin.error.data'));
        }

        try {
            foreach ($arayUID['RequestUID'] as $keyUID => $valueUID) {
                if (count($request->input()) > 0) {
                    $one = new DailyReport();
                    $one->Date = $this->fncDateTimeConvertFomat($request->Date, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                    $one->DateCreate = date(now());
                    $one->ProjectID = $request->ProjectID;
                    $one->ScreenName = $request->ScreenName;
                    $one->TypeWork = $request->TypeWork;
                    $one->Contents = $request->Contents;
                    $one->WorkingTime = $request['WorkingTime'];
                    $one->Progressing = 100;

                    $one->Note = $request->Note;
                    if (isset($valueUID)) {
                        $one->UserID = $valueUID;
                    }
                    if ($this->StringIsNullOrEmpty($one->UserID)) {
                        $one->UserID = Auth::user()->id;
                    }
                    $one->save();
                    // print($one);

                }
            }
            return AdminController::responseApi(200, null, __('admin.success.save'));
        } catch (Exception $e) {
            return AdminController::responseApi(422, $e->getMessage());
        }
    }

    /**
     * @param DailyReportOneRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException
     */
    public function updateApi(DailyReportOneRequest $request, $id)
    {
        $this->authorize('action', $this->edit);

        if (count($request->input()) === 0) {
            return AdminController::responseApi(422, __('admin.error.data'));
        }

        try {
            $one = DailyReport::find($id);
            foreach ($request->all() as $key => $value) {
                if (Schema::hasColumn('daily_reports', $key)) {
                    if ($key == 'Date') {
                        $value = $this->fncDateTimeConvertFomat(!is_null($value) ? $value : date("d/m/Y"), self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                    } elseif ($key == 'WorkingTime' || $key == 'Progressing') {
                        $value = number_format($value, 2, '.', '');
                    }
                    $one->$key = ($key !== 'UserID' && !is_null($value)) ? $value : null;
                }
            }
            if ($request->has('reqID')) {
                $one->UserID = !is_null($request['reqID']) && count($request['reqID']) > 0 ? $request['reqID'] : null;
            }
            if ($request->has('UserID')) {
                $one->UserID = $request['UserID'];
            }
            if ($this->StringIsNullOrEmpty($one->UserID)) {
                $one->UserID = Auth::user()->id;
            }
            $one->save();

            return AdminController::responseApi(200, null, __('admin.success.save'));
        } catch (Exception $e) {
            return AdminController::responseApi(422, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param null $id
     * @param null $del
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException
     */
    public function deleteApi(Request $request, $id = null, $del = null)
    {
        $this->authorize('action', $this->delete);
        if ($id != null) {
            $one = DailyReport::find($id);
            if ($one != null) {
                $one->delete();
                return AdminController::responseApi(200, null, __('admin.success.delete'));
            }
        }
        return AdminController::responseApi(422, __('admin.error.data'));
    }

    public function notificationPersonal()
    {
        return $this->viewAdminIncludes("notification");
    }
}
