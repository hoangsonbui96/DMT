<?php

namespace App\Http\Controllers\Admin\ReportManager;

use App\DailyReport;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\NotificationController;
use App\MasterData;
use App\Project;
use App\User;
use Carbon\Carbon;
use Hamcrest\Core\IsNull;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NeedApproveReportController extends AdminController
{
    // protected $add;
    // protected $edit;
    // protected $delete;
    // protected $view;
    // protected $export;
    // protected $dailyExport;
    // protected $user;
    // protected $currentUser;

    protected $working_days = [1, 2, 3, 4, 5];
    const KEYMENU = array(
        "view" => "NeedApproveReports"
    );

    /**
     * NeedApproveReportsController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        if (strpos($request->getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }

        $array = $this->RoleView('NeedApproveReports', ['NeedApproveReports']);
        $this->menu = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if ($value == $row->alias)
                    $this->$key = $row;
            }
        }
        $this->user = $this->GetListUser(self::USER_ACTIVE_FLAG);
    }
    public function index(Request $request)

    {
        $this->authorize('view', $this->menu);
        $this->data['request'] = $request->query();

        $currentUserId = auth()->id();
        $selectedUserId = isset($request->UserID) ? $request->UserID : [];
        $selectedProjectId = isset($request->ProjectID) ? $request->ProjectID : [];
        $employees = [];
        $listproject = [];

        $currentUserPositions = [];
        $currentUserPositions = $this->getUserPosition($currentUserId);

        $listproject = $this->getProjectsByLeaderPosition($currentUserId, $currentUserPositions);
        // $employees = $this->getUsersByPosition($listproject,$currentUserPositions);
        $employees = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $today = Carbon::now()->toDateString();
        $date = $request['time'] ? Carbon::createFromFormat('m/Y', $request['time']) : Carbon::now();
        $date = $date->format(self::FOMAT_DB_YMD);
        $month = Carbon::parse($date)->format('m');
        $year = Carbon::parse($date)->format('Y');

        $Master1 = MasterData::where('DataValue', 'WT001')->first();
        $Master2 = MasterData::where('DataValue', 'WT002')->first();

        $TimeInAM = $Master1 ? $Master1->Name : '08:30';
        $TimeOutPM = $Master1 ? $Master1->DataDescription : '17:30';
        $TimeOutAM = $Master2 ? $Master2->Name : '12:00';
        $TimeInPM = $Master2 ? $Master2->DataDescription : '13:00';

        $status = $request->ReportStatus;
        $conditionData = [
            'positions' => $currentUserPositions,
            'today' => $today,
            'month' => $month,
            'year' => $year,
            'selectedUserId' => $selectedUserId,
            'selectedProjectId' => $selectedProjectId,
            'status' => $status,
            'currentUserId' => $currentUserId
        ];
        $dailyReportsWithUserPosition = $this->getDailyReportsWithUserPosition($conditionData);
        $dailyReportsWithUserAbsence = $this->getDailyReportsWithUserAbsence($conditionData);
        $dailyReports = $dailyReportsWithUserPosition;
        foreach ($dailyReportsWithUserPosition as $key => $dailyReport) {
            foreach ($dailyReportsWithUserAbsence as $item) {
                if ($item->DailyReportId == $dailyReport->DailyReportId) {
                    $dailyReports[$key]->AbsentName = $item->AbsentName;
                    $dailyReports[$key]->AbsentSDate = $item->AbsentSDate;
                    $dailyReports[$key]->AbsentEDate = $item->AbsentEDate;
                    $dailyReports[$key]->absencesDuration = $item->absencesDuration;
                }
            }
        }

        $dailyReportsByLeader = clone $dailyReports;
        foreach ($dailyReports as $key => $item) {
            
            if (isset($item->Leader)) {
                if (
                        (isset($item->Position)
                        && (str_contains($item->Position, 'CL002') || str_contains($item->Position, 'TL002'))
                        || (!str_contains($item->Leader, ',' . $currentUserId . ',')))
                ) {
                    unset($dailyReportsByLeader[$key]);
                }
            } else {
                unset($dailyReportsByLeader[$key]);
            }
        }
        $dailyReportsByLeaderComtor = clone $dailyReports;
        foreach ($dailyReports as $key => $item) {
            if (isset($item->Position)) {
                if (!str_contains($item->Position, 'CL002')) {
                    unset($dailyReportsByLeaderComtor[$key]);
                }
            } else {
                unset($dailyReportsByLeaderComtor[$key]);
            }
        }

        $dailyReportsByLeaderTester = clone $dailyReports;
        foreach ($dailyReports as $key => $item) {
            if (isset($item->Position)) {
                if (!str_contains($item->Position, 'TL002')) {
                    unset($dailyReportsByLeaderTester[$key]);
                }
            } else {
                unset($dailyReportsByLeaderTester[$key]);
            }
        }
        if (!in_array('CL001', $currentUserPositions) && !in_array('TL001', $currentUserPositions)) {
            $dailyReports = $dailyReportsByLeader;
        } elseif (in_array('CL001', $currentUserPositions) && in_array('TL001', $currentUserPositions)) {
            $dailyReports = $dailyReportsByLeader;
            $dailyReports = $dailyReports->concat($dailyReportsByLeaderComtor);
            $dailyReports = $dailyReports->concat($dailyReportsByLeaderTester);
            $dailyReports = $dailyReports->unique('DailyReportId');
        } elseif (in_array('CL001', $currentUserPositions)) {
            $dailyReports = $dailyReportsByLeader->concat($dailyReportsByLeaderComtor);
            $dailyReports = $dailyReports->unique('DailyReportId');
        } elseif (in_array('TL001', $currentUserPositions)) {
            $dailyReports = $dailyReportsByLeader->concat($dailyReportsByLeaderTester);
            $dailyReports = $dailyReports->unique('DailyReportId');
        }
        //Pagination
        $recordPerPage = $this->getRecordPage();
        $sortBy = 'desc';
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $dailyReports = $dailyReports->paginate($recordPerPage);
        
        foreach ($dailyReports as $item) {
            $item->AbsentName = explode(',', $item->AbsentName);
            $item->AbsentSDate = explode(',', $item->AbsentSDate);
            $item->AbsentEDate = explode(',', $item->AbsentEDate);
            $Absent = [];
            for ($i = 0; $i < count($item->AbsentName); $i++) {
                array_push($Absent, ['Reason' => $item->AbsentName[$i], 'SDate' => $item->AbsentSDate[$i], 'EDate' => $item->AbsentEDate[$i]]);
            }
            $item->Absent = array_reverse($Absent);
        }

        $dailyReports = $this->getAbsent($dailyReports, $TimeInAM, $TimeOutPM, $TimeOutAM, $TimeInPM);
        $count = $dailyReports->count();
        parse_str(str_replace('?', '', $query_string), $query_array);
        if ($count == 0) {
            if (array_key_exists('page', $query_array)) {
                if ($query_array['page'] > 1) {
                    $query_array['page'] = $dailyReports->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl  = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }
            }
        }

        foreach ($dailyReports as $item) {

            if (isset($item->AbsenceSDate)) {
                if (Carbon::parse($item->AbsenceSDate)->day == Carbon::parse($item->Date)->day) {
                    $item->AbsenceSTime = Carbon::parse($item->AbsenceSDate)->format('H:i');
                    if ($item->AbsenceSTime < $TimeInAM) {
                        $item->AbsenceSTime = $TimeInAM;
                    }
                } else {
                    $item->AbsenceSTime = $TimeInAM;
                }
                if (Carbon::parse($item->AbsenceEDate)->day == Carbon::parse($item->Date)->day) {
                    $item->AbsenceETime = Carbon::parse($item->AbsenceEDate)->format('H:i');
                } else {
                    $item->AbsenceETime = $TimeOutPM;
                }
            } else {
                $item->AbsenceSTime = '';
                $item->AbsenceETime = '';
            }
        }
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        $this->data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['sort'] = $sort;

        $this->data['user'] = $request->UserID != '' ? User::find($request->UserID) : $this->user;
        $this->data['dailyReports'] = $dailyReports;

        $this->data['selectUser'] = $employees;
        $this->data['selectProject'] = $listproject;

        if ($status == 1) {
            $this->data['reportStatus'] = 1;
        }

        return $this->viewAdminLayout('report/need-approve-report', $this->data);
    }

    public function getDailyReportsWithUserAbsence($conditionData)
    {
        $today = $conditionData['today'];
        $month = $conditionData['month'];
        $year = $conditionData['year'];
        $selectedUserId = $conditionData['selectedUserId'];
        $selectedProjectId = $conditionData['selectedProjectId'];
        $status = $conditionData['status'];
        $currentUserId = $conditionData['currentUserId'];

        $dailyReports = DailyReport::query()
            ->select(
                'master_data.Name',
                'master_data.DataValue as TypeWork',
                'daily_reports.id as DailyReportId',
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
                'users.FullName as Reporter',
                'projects.Leader',

            )
            ->selectRaw("GROUP_CONCAT(`am`.`Name` SEPARATOR ',') AS AbsentName")
            ->selectRaw("GROUP_CONCAT(`absences`.`SDate` SEPARATOR ',') AS AbsentSDate")
            ->selectRaw("GROUP_CONCAT(`absences`.`EDate` SEPARATOR ',') AS AbsentEDate")
            ->selectRaw("SUM(absences.TotalTimeOff) as absencesDuration")
            ->selectRaw('\'\' AS Position')
            ->join('projects', 'projects.id', '=', 'daily_reports.ProjectID')
            ->Join('users', 'users.id', '=', 'daily_reports.UserID')
            ->leftJoin('master_data', 'master_data.DataValue', '=', 'daily_reports.TypeWork')

            ->leftjoin('absences', function ($join) {
                $join->on('absences.UID', '=', 'daily_reports.UserID');
                $join->on(DB::raw('cast(absences.SDate as date)'), '<=', 'daily_reports.Date');
                $join->on(DB::raw('cast(absences.EDate as date)'), '>=', 'daily_reports.Date');
            })
            ->leftjoin('master_data as am', 'am.DataValue', 'absences.MasterDataValue')
            ->whereNull('projects.deleted_at')
            ->where(function ($query) use ($today) {
                $query->where('projects.EndDate', '>=', $today)
                    ->orWhereNull('projects.EndDate');
            })
            ->whereMonth('daily_reports.Date', $month)
            ->whereYear('daily_reports.Date', $year)
            ->whereNull('users.deleted_at');


        if (count($selectedUserId) > 0) {
            $selectedUser = User::whereIn('id', $selectedUserId)->get();
            $tempArr = [];
            foreach ($selectedUser as $item) {
                $isActiveUser = ($item->Active == 1) ? true : false;
                if (!$isActiveUser) {
                    array_push($tempArr, $item);
                }
            }
            $dailyReports->whereIn('daily_reports.UserID', $selectedUserId);
        }
        if (count($selectedProjectId) > 0) {
            $selectedProject = Project::whereIn('id', $selectedProjectId)->get();
            $tempArr = [];
            foreach ($selectedProject as $item) {
                $isActiveProject = ($item->Active == 1 && $item->EndDate > $today) ? true : false;
                if (!$isActiveProject) {
                    array_push($tempArr, $item);
                }
            }
            $dailyReports->whereIn('projects.id', $selectedProjectId);
        }
        $this->data['reportStatus'] = 0;
        if ($status == 1) {
            $this->data['reportStatus'] = 1;
            $dailyReports->where('daily_reports.status', 1)
                ->where('daily_reports.ApprovedBy', $currentUserId);
        } else {
            $dailyReports->where('daily_reports.status', 0);
        }
        $dailyReports->groupBy('daily_reports.id');

        $dailyReports
            ->orderBy('daily_reports.UserID')
            ->orderBy('daily_reports.Date', 'DESC');
        return $dailyReports->get();
    }

    public function getAbsent($dailyReports, $TimeInAM, $TimeOutPM, $TimeOutAM, $TimeInPM)
    {
        foreach ($dailyReports as $item) {
            $Absent = [];
            foreach ($item->Absent as $absence) {
                $absence['STime'] = '';
                $absence['ETime'] = '';
                if ($absence['SDate'] != '') {
                    if (Carbon::parse($absence['SDate'])->day == Carbon::parse($item->Date)->day) {
                        $absence['STime'] = Carbon::parse($absence['SDate'])->format('H:i');
                        if ($absence['STime'] < $TimeInAM) {
                            $absence['STime'] = $TimeInAM;
                        }
                    } else {
                        $absence['STime'] = $TimeInAM;
                    }
                    if (Carbon::parse($absence['EDate'])->day == Carbon::parse($item->Date)->day) {
                        $absence['ETime'] = Carbon::parse($absence['EDate'])->format('H:i');
                    } else {
                        $absence['ETime'] = $TimeOutPM;
                    }
                }
                array_push($Absent, $absence);
            }
            $item->Absent = $Absent;
        }
        return $dailyReports;
    }

    public function openDenyReport(Request $request)
    {
        $data['id'] = $request->reqId;
        $data['issue'] = DailyReport::query()->where('id', $request->reqId)->pluck('Issue')->first();
        return $this->viewAdminIncludes('report/deny-report-modal', $data);
    }

    public function aprroveReport(Request $request)
    {
        $currentUserId = auth()->id();

        $approver = User::query()
            ->select(
                'users.id',
                'users.FullName',
                'users.email',
                'users.Gender',
                'users.MaritalStt',
                'rooms.id as roomId',
                'rooms.Name as roomName',
            )
            ->join('rooms', 'rooms.id', '=', 'RoomId')
            ->where('users.id', '=', $currentUserId)
            ->first();


        $report = DailyReport::query()
            ->select(
                'daily_reports.id',
                'daily_reports.ProjectID',
                'daily_reports.Issue',
                'daily_reports.Status',
                'daily_reports.DateCreate',
                'daily_reports.ApprovedBy',
                'daily_reports.ApprovedAt',
                'projects.id as projectId',
                'projects.NameVi as projectName',
                'users.id as reporterId',
                'users.FullName as reporterName',
                'users.email as reporterEmail',
                'users.Gender as reporterGender',
                'users.MaritalStt as reporterMaritalStt',
            )
            ->join('users', 'users.id', '=', 'daily_reports.UserID')
            ->join('projects', 'projects.id', '=', 'daily_reports.ProjectID')
            ->where('daily_reports.id', '=', $request->id)
            ->first();

        try {
            DB::beginTransaction();

            if ($request->status === '1') {
                $report->Issue = trim($request->issue) == '' ? null : trim($request->issue);
            }

            $ok = DailyReport::where('id', $request->id)
                ->update(['Issue' => $report->Issue, 'ApprovedBy' => $approver->id, 'ApprovedAt' => Carbon::now(), 'Status' => $request->status]);

            DB::commit();

            if ($ok == 1 && $request->status == 1) {
                $this->sendMail($report, $approver);
                $this->pushNotification($report, $approver);
                return $this->jsonSuccess('Đã yêu cầu viết lại báo cáo!');
            } else {
                return $this->jsonSuccess('Duyệt thành công!');
            }
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->jsonSuccess('Duyệt thất bại!');
        }
    }

    public function pushNotification($report, $approver)
    {
        $arrToken = DB::table('push_token')
            ->whereIn('UserID', [$approver->id, $report->reporterId])
            ->where('allow_push', 1)
            ->whereNull('deleted_at')
            ->pluck('token_push')
            ->toArray();

        $arrToken == array_unique($arrToken);

        if (count($arrToken) > 0) {
            try {
                $sendData = [];
                $sendData['id'] = $report->id;
                $sendData['data'] = "TCBCHN";
                $headrmess = $this->getUserTittle($approver->Gender, $approver->MaritalStt) . $approver->FullName . " Yêu cầu "
                    . $this->getUserTittle($report->reporterGender, $report->reporterMaritalStt) . $report->reporterFullName . " viết lại báo cáo";
                $bodyNoti = "Ngày báo cáo :"
                    . $report->DateCreate
                    . ", thuộc dự án : " . $report->projectName;

                NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
    }

    public function sendMail($report, $approver)
    {
        $projectName = $report->projectName;
        $MailNameTo = $this->getUserTittle($report->reporterGender, $report->reporterMaritalStt) . $report->reporterName;
        $subjectMail = 'Yêu cầu viết lại báo cáo ngày ' . date("d/m/Y", strtotime($report->DateCreate));

        $contentMail = 'Kính gửi ' . $MailNameTo . ' <br/><br/>';
        $contentMail .= $MailNameTo . ' vui lòng viết lại báo cáo' . ' <br/>';
        $contentMail .= 'Thời gian báo cáo: ' . date("d/m/Y", strtotime($report->DateCreate)) . ' <br/>';
        $contentMail .= 'Thuộc dự án : ' .  $projectName . ' <br/>';
        $contentMail .= 'Lý do phải viết lại: ' . $report->Issue . ' <br/><br/>';
        $contentMail .= 'Chân thành cảm ơn!';

        $MailNameFrom = $this->getUserTittle($approver->Gender, $approver->MaritalStt) . $approver->FullName;
        $MailAddressTO = $report->reporterEmail;
        $MailAddressCC = $approver->email;
        $this->SendMailHtml($subjectMail, $contentMail, config('mail.from.address'), $MailNameFrom . ' - ' . $approver->roomName, $MailAddressTO, $MailAddressCC);
    }

    public function getUserTittle($userGender, $userMaritalStt)
    {
        if ($userGender == 1) {
            if ($userMaritalStt == 0) {
                $tittle = 'Chị ';
            } else {
                $tittle = 'Bà ';
            }
        } else {
            if ($userMaritalStt == 0) {
                $tittle = 'Anh ';
            } else {
                $tittle = 'Ông ';
            }
        }
        return $tittle;
    }
}
