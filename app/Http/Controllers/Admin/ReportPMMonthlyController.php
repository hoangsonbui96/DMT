<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PMReportRequest;
use App\Http\Requests\ProjectMngtRequest;
use App\Jobs\SendEmail;
use App\Model\ListPosition;
use App\model\ListPositionUser;
use App\Model\TDetailReport;
use App\Model\TMeetingWeek;
use App\Model\TReportPM;
use App\Project;
use App\Room;
use App\User;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReportPMMonthlyController extends AdminController
{
    const KEY_APP = "QLBCDA";
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $export;
    protected $dailyExport;
    protected $user;
    protected $working_days = [1, 2, 3, 4, 5];

    const KEYMENU = array(
        "add" => "MonthlyReportsAdd",
        "view" => "MonthlyReports",
        "edit" => "MonthlyReportsEdit",
        "delete" => "MonthlyReportsDelete",
        // "dailyExport" => "MonthlyReportsExport",
    );

    const MENUKEY = array(
        "add" => "MeetingWeeksAdd",
        "view" => "MeetingWeeks",
        "edit" => "MeetingWeeksEdit",
        "delete" => "MeetingWeeksDelete",
        // "dailyExport" => "MonthlyReportsExport",
    );

    /**
     * DailyReportController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {

        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }


        $array = $this->RoleView('MonthlyReports', ['MonthlyReports', 'MonthlyReports']);
        $this->menu = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if ($value == $row->alias)
                    $this->$key = $row;
            }
        }

        $array_data = $this->RoleView('MeetingWeeks', ['MeetingWeeks', 'MeetingWeeks']);
        $this->menu = $array_data['menu'];
        foreach (self::MENUKEY as $key => $value) {
            foreach ($array_data['role'] as $row) {
                if ($value == $row->alias)
                    $this->$key = $row;
            }
        }
        // $this->export = RoleScreenDetail::query()
        //     ->where('alias', 'TotalReportExport')
        //     ->first();
        // $this->user = $this->GetListUser(self::USER_ACTIVE_FLAG);
    }

    public function main(Request $request, $sortBy = 'asc')
    {
        $this->data['request'] = $request;
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;
        $this->data['selectUser'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $recordPerPage = $this->getRecordPage();
        $user = auth()->user();
        $user_find = $request->get("UserID");
        $search = $request->get("search");

        $startOfMonth = isset($request->StartTime)
            ? Carbon::createFromFormat(self::FOMAT_DISPLAY_DMY, $request->StartTime)->format(self::FOMAT_DB_YMD)
//            : Carbon::now()->startOfMonth()->format(self::FOMAT_DB_YMD);
//            : Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);
		: Carbon::now()->subMonth()->startOfMonth();
        $endOfMonth = isset($request->EndTime)
            ? Carbon::createFromFormat(self::FOMAT_DISPLAY_DMY, $request->EndTime)->format(self::FOMAT_DB_YMD)
            : Carbon::now()->endOfMonth()->format(self::FOMAT_DB_YMD);

        $ones = TMeetingWeek::with("TReportPms")
            ->where(function ($query) use ($user) {
                $query->where('Secret', '0')
                    ->orWhere(function ($query_sub) use ($user) {
                        $query_sub->where("Secret", '1')->where("Participant", "like", "%,$user->id,%");
                    })
                    ->orWhere("RegisterId", $user->id);
            })
            ->when($user_find, function ($q) use ($user_find) {
                return $q->where("Participant", "like", "%,$user_find,%");
            })
            ->when($search, function ($q) use ($search) {
                return $q->where("MeetingName", "like", "%$search%");
            })
            ->when(($startOfMonth && $endOfMonth), function ($q) use ($startOfMonth, $endOfMonth) {
                return $q->whereDate("MeetingTimeFrom", ">=", $startOfMonth)
                    ->whereDate("MeetingTimeFrom", "<=", $endOfMonth);
            })
//            ->orderBy(DB::raw('DayCreate is not null, DayCreate'), 'DESC');
            ->orderByDesc("created_at");
        $ones = $ones->paginate($recordPerPage);
        foreach ($ones as &$one) {
            $one->can_join = true;
            $one->IsColor = false;
            $participants = $one->Participant;
            $participants = explode(",", $participants);
            if ($one->Secret == 0) {
                $one->can_join = in_array($user->id, $participants);
            }
            if ($one->can_join) {
                $is_report = $one->TReportPms->where("UserId", $user->id)->first();
                if (!$is_report && Carbon::now()->lt(Carbon::parse($one->TimeEnd))) {
                    $one->IsColor = true;
                }
            }
            $one->Members = implode("", array_filter(array_map(function ($id) {
                $user = User::withTrashed()->where("id", $id)->first();
                if (!$user) {
                    return null;
                }
                return "<span class='td-user'>$user->FullName</span>";
            }, $participants)));
            $one->IsComment = $one->TReportPms()->where("IsPublic", 1)->whereNotNull("Note_Report")->exists() || !is_null($one->Evaluation);
        }
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);
        /*
        //redirect to the last page if current page has no record
        if ($ones->count() == 0) {
            if (array_key_exists('page', $ones)) {
                if ($ones['page'] > 1) {
                    $query_array['page'] = $ones->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }
            }
        }
        */
        $this->data['query_array'] = $query_array;
        $this->data['t_meetings'] = $ones;
        $this->data['sort_link'] = $sort_link;
        return $this->viewAdminLayout("meeting-weekly", $this->data);
    }

    public function index(Request $request, $id)
    {
        try {
            $this->authorize('view', $this->menu);
        } catch (AuthorizationException $e) {
            abort(403);
        }
        $this->data['request'] = $request;
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;
        $this->data['idRequest'] = $id;
        $one = TMeetingWeek::findOrFail($id);
        $this->data['t_meeting_week'] = $one;
        $this->data['selectUser'] = User::withTrashed()->where('deleted', '!=', self::DELETE_FLAG)
            ->where('role_group', '!=', self::USER_ROOT_GROUP)
            ->when($one->Secret == 1, function ($q) use ($one) {
                return $q->whereIn('id', explode(',', $one->Participant));
            })
            ->orderBy('username')
            ->get();
        $this->data['Evaluation'] = $one->Evaluation;
        $this->data['ChairID'] = $one->ChairID;
        $this->data['idReviewer'] = $one->RegisterId;
        $this->data['NameProject'] = $one->MeetingName;

        $this->data['user'] = auth()->user();
        $user_id = $request->get("UserID");

        $this->data['t_reports'] = TReportPM::query()->where('IdMeeting', $id)->when($user_id, function ($q) use ($user_id) {
            return $q->where("UserId", $user_id);
        })->orderByDesc("created_at")->paginate($this->getRecordPage());

        return $this->viewAdminLayout("monthly-report", $this->data);
    }

    public function updateDetail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|int',
            'Comments' => 'required|string',
            'SendMail' => 'required|boolean'
        ], [
            'Comments.required' => 'Vui lòng điền nhận xét'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }
        $validate = $validator->validate();
        try {
            $t_pm_report = TReportPM::find($validate["id"]);
            $t_pm_report->Note_Report = $validate["Comments"];
            $t_pm_report->save();
            if ($validate["SendMail"]) {
                $this->_sendEmailAndNotification(2, "action", ["one" => $t_pm_report]);
            }
            return response()->json("Lưu thành công.");
        } catch (ModelNotFoundException $exception) {
            return response()->json("Không tìm thấy báo cáo.", 404);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }
    }

    public function store(PMReportRequest $request)
    {
        $_attr = [
            "UserId" => \auth()->id(),
            "Content" => $request->get("Content"),
            "StartDate" => $request->filled("StartDate")
                ? Carbon::createFromFormat(self::FOMAT_DISPLAY_DMY, $request->get('StartDate'))->toDateTimeString()
                : null,
            "EndDate" => $request->filled('EndDate')
                ? Carbon::createFromFormat(self::FOMAT_DISPLAY_DMY, $request->get('EndDate'))->toDateTimeString()
                : null,
            "IdReviewer" => $request->get("IdReviewer"),
            "IdMeeting" => $request->get("idMeeting"),
        ];
        if (!$request->filled("id")) {
            $t_pm_report = TReportPM::create($_attr);
            $tMeeting = TMeetingWeek::query()->where("id", $t_pm_report->IdMeeting)->first();
            DB::beginTransaction();
            try {
                foreach ($request->get("NameProject") as $key => $value) {
                    TDetailReport::create([
                        "NameProject" => $value,
                        "Note" => $request->get('dataNote')[$key],
                        "report_id" => $t_pm_report->id
                    ]);
                }
                DB::commit();
                $mess_back = "Thêm mới báo cáo thành công";
            } catch (\Exception $exception) {
                DB::rollBack();
            }
        } else {
            $t_pm_report = TReportPM::find($request->get("id"));
            $t_pm_report->update($_attr);
            $tMeeting = TMeetingWeek::query()->where("id", $t_pm_report->IdMeeting)->first();
            $listProject = TDetailReport::where('report_id', $t_pm_report->id)->get();
            foreach ($listProject as $key => $element) {
                $element->delete();
            }
            DB::beginTransaction();
            try {
                foreach ($request->get("NameProject") as $key => $value) {
                    TDetailReport::create([
                        "NameProject" => $value,
                        "Note" => $request->get('dataNote')[$key],
                        "report_id" => $t_pm_report->id
                    ]);
                }
                DB::commit();
                $mess_back = "Chỉnh sửa báo cáo thành công";
            } catch (\Exception $exception) {
                DB::rollBack();
            }
        }
        try {
            $this->_sendEmailAndNotification(1, "update", ["t_pm_report" => $t_pm_report, "t_meeting" => $tMeeting]);
        } catch (\Exception $exception) {
            return response()->json(['errors' => $exception->getMessage()], 500);
        }
        return response()->json($mess_back);
    }


    public function storeComment(Request $request, $id)
    {
        if (!$request->filled("Comment")) {
            return response()->json('Vui lòng điền nhận xét cuộc họp.', 400);
        }
        $t_meeting_weekly = TMeetingWeek::find($id);
        if (!$t_meeting_weekly) {
            return response()->json('Wrong id.', 400);
        }
        $t_meeting_weekly->Evaluation = $request->get('Comment');
        $t_meeting_weekly->save();
        return response()->json('Lưu nhận xét cuộc họp thành công.', 200);
    }

    public function save(ProjectMngtRequest $request): JsonResponse
    {
        $arr_participant = $request->get("AssignID");
        array_push($arr_participant, $request->get("ChairID"));
        $arr_participant = "," . implode(",", array_unique($arr_participant)) . ",";

        // Array attribute create or update
        $_attr = [
            "MeetingName" => $request->get("MeetingName"),
            "RegisterId" => \auth()->id(),
            "ChairID" => $request->get("ChairID"),
            "ProjectId" => $request->filled("ProjectID") ? $request->get("ProjectID") : null,
            "MeetingTimeFrom" => Carbon::createFromFormat(self::FOMAT_DISPLAY_DMY, $request->get("MeetingTimeFrom")),
            "MeetingTimeTo" => $request->filled("MeetingTimeTo") ? Carbon::createFromFormat(self::FOMAT_DISPLAY_DMY, $request->get("MeetingTimeTo")) : null,
            "Participant" => $arr_participant,
            "Secret" => $request->get("isPrivate"),
            "TimeEnd" => $request->filled("TimeEnd") ? Carbon::createFromFormat(FOMAT_DISPLAY_DATE_TIME, $request->get("TimeEnd")) : null
        ];
        // Update action
        if ($request->filled("id")) {
            $action = "update";
            $t_meeting_weekly = TMeetingWeek::query()->where("id", $request->get("id"))->first();
            $t_meeting_weekly->update($_attr);
            TReportPM::query()->where("IdMeeting", $request->get("id"))->update(['IdReviewer' => $request->get("ChairID")]);
            $mess_back = "Cập nhật thành công";
        } else {
            $action = "insert";
            $t_meeting_weekly = TMeetingWeek::create($_attr);
            $mess_back = "Luư mới thành công";
        }
        // Send Email and Noti
        $this->_sendEmailAndNotification(0, $action, ["one" => $t_meeting_weekly]);
        return response()->json($mess_back);
    }

    public function showDetail(Request $request, $id = null, $del = null)
    {
        $this->data["time_from"] = null;
        $this->data["time_to"] = null;
        if ($id != null) {
            $t_pm_report = TReportPM::with("detail_reports")->where("id", $id)->first();
            $this->data['weeklyInfo'] = $t_pm_report;
            if ($del != null) {
                $t_pm_report->delete();
                TDetailReport::where("report_id", $t_pm_report->id)->delete();
                $tMeeting = TMeetingWeek::query()->where("id", $t_pm_report->IdMeeting)->first();
                $this->_sendEmailAndNotification(1, "delete", ["t_pm_report" => $t_pm_report, "t_meeting" => $tMeeting]);
                return 1;
            }
            if ($this->data['weeklyInfo']) {
                return $this->viewAdminIncludes('monthly-report-modal', $this->data);
            } else {
                return "";
            }
        } else {
            $t_meeting = TMeetingWeek::find($request->get("t_meeting"));
            $this->data['contentNote'] = 'Báo cáo ';
            $this->data["time_from"] = Carbon::parse($t_meeting->MeetingTimeFrom)->format(self::FOMAT_DISPLAY_DMY);
            $this->data["time_to"] = $t_meeting->MeetingTimeTo
                ? Carbon::parse($t_meeting->MeetingTimeTo)->format(self::FOMAT_DISPLAY_DMY)
                : null;
            return $this->viewAdminIncludes('monthly-report-modal', $this->data);
        }
    }

    public function openMeeting(Request $request, $id = null, $del = null)
    {
        $now = date(self::FOMAT_DB_YMD);
        $now_string = \Illuminate\Support\Carbon::parse($now)->toDateString();
        $this->data['groupDataKey'] = ListPosition::query()
            ->select('DataValue', 'Name')
            ->orderByDesc('DataValue')
            ->get();

        foreach ($this->data['groupDataKey'] as $k => &$item) {
            $existUser = ListPositionUser::query()->where('DataValue', $item->DataValue)
                ->pluck("UserId")->toArray();
            $users = User::query()->where("Active", self::USER_ACTIVE_FLAG)
                ->whereIn("id", $existUser)->pluck("id")->toArray();
            if (count($users) != 0) {
                $item->PositionUser = implode(',', $users);
            } else {
                unset($this->data['groupDataKey'][$k]);
            }
        }

        $this->data['selectUser'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['projects'] = Project::query()
            ->where('Active', 1)
            ->where(function ($query) use ($now_string) {
                $query->where('StartDate', '<=', $now_string)->where('EndDate', '>=', $now_string)->orWhereNull('EndDate');
            })
            ->where(function ($query) use ($request) {
                if ($request['reqId'] != '') {
                    $query->where('Member', 'like', '%' . $request['reqId'] . '%')
                        ->orWhere('Leader', 'like', '%' . $request['reqId'] . '%');
                } else {
                    $query->where('Member', 'like', '%' . Auth::user()->id . '%')
                        ->orWhere('Leader', 'like', '%' . Auth::user()->id . '%');
                }
            })->get();
        //Update modal
        if ($id != null) {
            $t_meeting_report = TMeetingWeek::where("id", $id)->first();
            $arrIdUser = explode(",", $t_meeting_report->Participant);
            $arrayListPositionUser = [];
            foreach (($this->data['groupDataKey']) as $item) {
                $ListPositionUser = ListPositionUser::query()->where('DataValue', '=', $item->DataValue)->select('UserId')->groupBy('UserId')->get();
                $arrayListPositionUserEachValue = [];
                foreach ($ListPositionUser as $key) {
                    $check = User::query()->where('id', '=', $key->UserId)->where('Active', '=', 1)->first();
                    if ($check) {
                        array_push($arrayListPositionUserEachValue, $key->UserId);
                    }
                }
                if ($arrayListPositionUserEachValue && $arrayListPositionUserEachValue[0] != null) {
                    array_push($arrayListPositionUser, ['DataValue' => $item->DataValue, 'item' => $arrayListPositionUserEachValue]);
                }
            };
            $arrayListPositionUserEachValue = [];
            $arrayIdUserCheck = $arrIdUser;
            $this->data['listPosition'] = implode(',', $arrayListPositionUserEachValue);
            $this->data['AssignID'] = implode(',', $arrayIdUserCheck) != "" ? implode(',', $arrayIdUserCheck) : null;
            // $this->data['user_assign'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
            $this->data['user_assign'] = User::withTrashed()->where("isAdmin", "!=", self::USER_ROOT_GROUP)->get();
            $this->data['meetingInfo'] = $t_meeting_report;
            $this->data['meetingUser'] = explode(",", $t_meeting_report->Participant);
            $this->data['all_project'] = Project::query()
                ->where('Member', 'like', '%' . $t_meeting_report->RegisterId . '%')
                ->orWhere('Leader', 'like', '%' . $t_meeting_report->RegisterId . '%')->get();

            if ($del != null) {
                $meeting_id = $t_meeting_report->id;
                $reports = TReportPM::query()->where("IdMeeting", $meeting_id)->get();
                foreach ($reports as $report) {
                    $report_id = $report->id;
                    TDetailReport::where("report_id", $report_id)->delete();
                    $report->delete();
                }
                $t_meeting_report->delete();
                $this->_sendEmailAndNotification(0, "delete", ["one" => $t_meeting_report]);
                return 1;
            }
            if ($this->data['meetingInfo']) {
                return $this->viewAdminIncludes('weekly-meeting-modal', $this->data);
            } else {
                return "";
            }
        } else {
            //Create modal
            $this->data['user_assign'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
            $this->data['contentNote'] = 'Báo cáo dự án ';
            $this->data['meetingUser'] = null;
            return $this->viewAdminIncludes('weekly-meeting-modal', $this->data);
        }
    }

    public function reviewDetail(Request $request, $id = null, $del = null)
    {
        if ($id != null) {
            $t_pm_report = TReportPM::with("detail_reports")->where("id", $id)->first();
            $this->data['weeklyInfo'] = $t_pm_report;
            if ($del != null) {
                $t_pm_report->delete();
                return 1;
            }

            if ($this->data['weeklyInfo']) {
                return $this->viewAdminIncludes('monthly-detail-modal', $this->data);
            } else {
                return "";
            }
        } else {
            $this->data['contentNote'] = 'Báo cáo hàng tuần ';
            return $this->viewAdminIncludes('monthly-detail-modal', $this->data);
        }
    }

    // Function open modal comment
    public function openModalComment(Request $request, $id)
    {
        $t_meeting_week = TMeetingWeek::find($id);
        if (!$t_meeting_week) {
            return response()->json("Not found", 400);
        }
        $arr_participant = explode(",", $t_meeting_week->Participant);
        if (!in_array(auth()->id(), $arr_participant)) {
            return response()->json("Bạn không đủ quyền thực hiện tác vụ này", 403);
        }
        $this->data["t_meeting_week"] = $t_meeting_week;
        $this->data["meeting_name"] = $t_meeting_week->MeetingName;
        $this->data["content_public"] = TReportPM::query()
            ->select("t_report_pms.id as TReportPmId", "UserId", "FullName", "Content", "Note_report", "IdReviewer")
            ->where("IsPublic", 1)
            ->where("IdMeeting", $t_meeting_week->id)
            ->join("users", "users.id", "=", "UserId")
            ->get();
        return $this->viewAdminIncludes('monthly-report.project-comment-modal', $this->data);
    }

    public function openModalCommentSpecific(Request $request, $id)
    {
        $this->data["content_public"] = TReportPM::query()
            ->select("t_report_pms.id as TReportPmId", "UserId", "FullName", "Content", "Note_report", "IdReviewer", "IdMeeting")
//            ->where("IsPublic", 1)
            ->where(function ($q) {
                $q->where("IsPublic", 1)->orwhere(function ($q1) {
                    $q1->where("IsPublic", 0)->where("UserId", \auth()->id())->orWhere("IdReviewer", \auth()->id());
                });
            })
            ->where("t_report_pms.id", $id)
            ->join("users", "users.id", "=", "UserId")
            ->get();

        if (count($this->data["content_public"]) != 0) {
            $this->data["meeting_name"] = TMeetingWeek::query()
                ->where("id", $this->data["content_public"]->first()->IdMeeting)
                ->first()->MeetingName;
            return $this->viewAdminIncludes('monthly-report.project-comment-modal', $this->data);
        }
        return response()->json("Không tìm thấy nhận xét", 400);
    }

    // Function export Comment base of meeting to file PDF
    public function pdf(Request $request, $id)
    {
        $t_meeting_week = TMeetingWeek::find($id);
        $arr_participant = explode(",", $t_meeting_week->Participant);
        array_pop($arr_participant);
        array_shift($arr_participant);

        // Convert array id user to array full name user
        $arr_participant = array_map(function ($id) {
            return User::find($id)->FullName;
        }, $arr_participant);

        // Prepare value
        $data['title'] = "BÁO CÁO";
        $data['meeting_name'] = $t_meeting_week->MeetingName;
        $data['chair'] = User::find($t_meeting_week->ChairID)->FullName;
        $data['participant'] = implode(", ", $arr_participant);
        $data['evaluation'] = $t_meeting_week->Evaluation;
        $data["content_public"] = TReportPM::query()
            ->select("t_report_pms.id as TReportPmId", "UserId", "FullName", "Content", "Note_report")
            ->where("IsPublic", 1)
            ->where("IdMeeting", $t_meeting_week->id)
            ->join("users", "users.id", "=", "UserId")
            ->get();
        $data['time'] = $t_meeting_week->MeetingTimeFrom ? " từ " . Carbon::parse($t_meeting_week->MeetingTimeFrom)->format(self::FOMAT_DISPLAY_DMY) : "";
        $data['time'] .= $t_meeting_week->MeetingTimeTo ? " tới " . Carbon::parse($t_meeting_week->MeetingTimeTo)->format(self::FOMAT_DISPLAY_DMY) : "";
        $pdf = PDF::loadView("admin.pdf.comment", $data);
        $file_name = strtolower(str_replace(' ', '_', $this->convert_vi_to_en($data['meeting_name'])));
        return $pdf->download($file_name . ".pdf");
    }

    //Function return list user by position
    public function getUserByPosition(Request $request): JsonResponse
    {
        $validator = Validator::make($request->only("positions"), [
            'positions' => 'array',
            'positions.*' => 'string'
        ], [
            'positions.array' => "Sai định dạng Nhóm chức vụ",
            'positions.*.string' => "Giá trị Nhóm chức vụ phải là một chuỗi"
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 400);
        }
        $positions = $request->filled("positions") ? $validator->validated()['positions'] : null;
        if (!$positions) {
            return self::responseApi(200, null, true, []);
        }
        $arr_user_id = ListPositionUser::query()
            ->whereIn("DataValue", $positions)
            ->pluck("UserId")->toArray();

        $users = User::query()
            ->select([
                "users.id",
                "FullName",
                "list_position_user.DataValue",
                "rooms.Name as Room"
            ])
            ->join("list_position_user", "list_position_user.UserId", "=", "users.id")
            ->join("rooms", "rooms.id", "=", "users.RoomId")
            ->whereIn("users.id", $arr_user_id)
            ->orderBy("users.id")
            ->get();

        return self::responseApi(200, null, true, ['users' => $users]);
    }

    public function getUserDetail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->only("users"), [
            'users' => 'array',
            'users.*' => 'integer'
        ], [
            'users.array' => "Sai định dạng Người tham gia",
            'users.*.integer' => "Giá trị Người tham gia phải là một số nguyên"
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 400);
        }
        $users = $request->filled("users") ? $validator->validated()['users'] : null;
        if (!$users) {
            return self::responseApi(200, null, true, ["positions" => []]);
        }
        $detail_users = User::query()
            ->select([
                "users.id",
                "FullName",
                "list_position_user.DataValue",
                "rooms.Name as Room"
            ])
            ->join("list_position_user", "list_position_user.UserId", "=", "users.id")
            ->join("rooms", "rooms.id", "=", "users.RoomId")
            ->whereIn("users.id", $users)
            ->orderBy("users.id")
            ->get();

        $t = clone $detail_users;
        $detail_users = $detail_users->map(function ($item, $k) use ($t) {
            $d = $item->DataValue;
            $c = ListPositionUser::query()->where("DataValue", $d)->count();
            $item->isFull = $c == $t->filter(function ($value, $key) use ($d) {
                    return $value->DataValue == $d;
                })->count();
            return $item;
        });
        return self::responseApi(200, null, true, ["users" => $detail_users]);
    }

    public function updateComment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|min:1',
            'value' => 'required|string',
            'key' => 'required|in:t_meeting,t_report'
        ], [
            'id.required' => 'Thiếu id',
            'value.required' => 'Nội dung nhận xét không được để trống',
            'key.required' => 'Từ khóa không được để trống',
            'key.in' => 'Sai định dạng từ khóa'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }
        try {
            $id = $request->get('id');
            $value = $request->get('value');
            $key = $request->get('key');
            if ($key == 't_meeting') {
                $t_meeting = TMeetingWeek::find($id);
                if ($t_meeting->ChairID != \auth()->id()) {
                    return response()->json("Bạn không có quyền thay đổi nhận xét", 403);
                }
                $t_meeting->Evaluation = $value;
                $t_meeting->save();
            }
            if ($key == 't_report') {
                $t_report = TReportPM::find($id);
                if ($t_report->IdReviewer != \auth()->id()) {
                    return response()->json("Bạn không có quyền thay đổi nhận xét", 403);
                }
                $t_report->Note_Report = $value;
                $t_report->save();
            }
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }
        return response()->json("Cập nhật thành công");
    }

    // Gửi email và thông báo cho app
    private function _sendEmailAndNotification($type = 0, $action = "delete", $attr = [], $isSendEmail = true, $isSendNotification = true)
    {
        // Mail
        $subjectMail = "";
        $contentMail = "";
        $mailNameFrom = "";
        $arrMailAddressTo = [];
        $arrMailAddressCC = [];

        // Notification
        $headMess = "";
        $bodyNoti = "";
        $sendData = [];
        $arrToken = [];
        switch ($type) {
            case 0:
                $one = $attr['one'];
                $offer_id = \auth()->id();
                $offerUser = \auth()->user();
                $room = Room::find($offerUser->RoomId);
                $arrIdTo = $arrIdCC = [];
                foreach (array_unique(explode(",", $one->Participant)) as $id) {
                    if ($id == $offer_id) {
                        $arrIdCC[] = $id;
                    } else {
                        $arrIdTo[] = $id;
                    }
                }
                $arrMailAddressTo = User::query()->whereIn("id", $arrIdTo)->pluck("email")->toArray();
                $arrMailAddressCC = User::query()->whereIn("id", $arrIdCC)->pluck("email")->toArray();
                $name = User::query()->whereIn("id", $arrIdTo)->pluck("FullName");
                $to_name = count($name) > 1 ? null : (count($name) == 0 ? null : $name[0]);
                $arrToken = DB::table('push_token')
                    ->whereIn('UserID', explode(',', $one->Participant))
                    ->whereNull('deleted_at')->where('allow_push', 1)
                    ->pluck('token_push')
                    ->toArray();

                $arrMailAddressTo = array_filter($arrMailAddressTo);
                $arrMailAddressCC = array_filter($arrMailAddressCC);
                // Can send email
                if (count($arrMailAddressTo) != 0) {
                    $mailNameFrom = $offerUser->FullName . ' - ' . $room->Name;
                    switch ($action) {
                        case "insert":
                            $subjectMail = "TB thêm mới danh sách báo cáo quản lý dự án";
                            $contentMail = $this->_genContentMail($one->MeetingName, $one->MeetingTimeFrom, $one->MeetingTimeTo, $offerUser->FullName . " vừa thêm mới dự án", $to_name);
                            break;
                        case "update":
                            $subjectMail = "TB chỉnh sửa danh sách báo cáo quản lý dự án";
                            $contentMail = $this->_genContentMail($one->MeetingName, $one->MeetingTimeFrom, $one->MeetingTimeTo, $offerUser->FullName . " vừa thay đổi dự án", $to_name);
                            break;
                        case "delete":
                            $subjectMail = "TB xóa danh sách báo cáo quản lý dự án";
                            $contentMail = $this->_genContentMail($one->MeetingName, $one->MeetingTimeFrom, $one->MeetingTimeTo, $offerUser->FullName . "vừa xóa dự án", $to_name);
                            break;
                    }
                }
                // Can send noti
                if (count($arrToken)) {
                    $sendData = [
                        "id" => $one->id,
                        "data" => self::KEY_APP
                    ];
                    $bodyNoti = "Báo cáo quản lý dự án: " . $one->MeetingName;
                    $bodyNoti .= " [";
                    $bodyNoti .= Carbon::parse($one->MeetingTimeFrom)->format(self::FOMAT_DISPLAY_DMY);
                    $bodyNoti .= $one->MeetingTimeTo ? " - " . Carbon::parse($one->MeetingTimeTo)->format(self::FOMAT_DISPLAY_DMY) . "]" : "]";
                    switch ($action) {
                        case "insert":
                            $headMess = "Bạn có 1 báo cáo quản lý dự án mới";
                            break;
                        case "update":
                            $headMess = "Có 1 báo cáo quản lý dự án vừa được cập nhật";
                            break;
                        case "delete":
                            $headMess = "Có 1 báo cáo quản lý dự án vừa được xóa";
                            break;
                    }
                }
                break;
            case 1:
                $t_pm_report = $attr['t_pm_report'];
                $t_meeting = $attr["t_meeting"];
                $offerUser = User::find($t_pm_report->UserId);
                $room = Room::find($offerUser->RoomId);
                $reviewUser = User::find($t_pm_report->IdReviewer);

                $arrMailAddressTo = [($reviewUser && $reviewUser->email) ? $reviewUser->email : null];
                $arrMailAddressCC = [($offerUser && $offerUser->email) ? $offerUser->email : null];

                $to_name = $reviewUser ? $reviewUser->FullName : null;

                $arrMailAddressTo = array_filter($arrMailAddressTo);
                $arrMailAddressCC = array_filter($arrMailAddressCC);

                $arrToken = DB::table('push_token')
                    ->where('UserID', $t_pm_report->IdReviewer)
                    ->whereNull('deleted_at')
                    ->where('allow_push', 1)
                    ->pluck('token_push')->toArray();

                if (count($arrMailAddressTo) != 0) {
                    $mailNameFrom = $offerUser->FullName . " - " . $room->Name;
                    $t_detail = TDetailReport::query()->where("report_id", $t_pm_report->id)->get();
                    $more = $t_detail->map(function ($item) {
                        $name = $item->NameProject;
                        return "<li>$name</li>";
                    });
                    $more = implode("", $more->toArray());
                    switch ($action) {
                        case "update":
                            $subjectMail = 'TB báo cáo dự án [' . $t_meeting->MeetingName . ']';
                            $contentMail = $this->_genContentMail($t_meeting->MeetingName, $t_meeting->MeetingTimeFrom, $t_meeting->MeetingTimeTo,
                                $offerUser->FullName . ' vừa gửi báo cáo tới dự án', $to_name, "<p>Đầu mục công việc:</p><ul>$more</ul>");
                            break;
                        case "delete":
                            $subjectMail = 'TB xóa báo cáo dự án [' . $t_meeting->MeetingName . ']';
                            $contentMail = $this->_genContentMail($t_meeting->MeetingName, $t_meeting->MeetingTimeFrom, $t_meeting->MeetingTimeTo,
                                $offerUser->FullName . ' vừa xóa báo cáo tại dự án ', $to_name, "<p>Xóa đầu mục công việc:</p><ul>$more</ul>");
                            break;
                        default:
                            break;
                    }
                }
                if (count($arrToken) != 0) {
                    $sendData = [
                        'id' => $t_pm_report->id,
                        'data' => self::KEY_APP
                    ];
                    switch ($action) {
                        case "update":
                            $headMess = "Có 1 báo cáo vừa được gửi";
                            $bodyNoti = $offerUser->FullName . ' vừa gửi báo cáo tới dự án ' . $t_meeting->MeetingName;
                            break;
                        case "delete":
                            $headMess = "Có 1 báo cáo vừa được xóa";
                            $bodyNoti = $offerUser->FullName . ' vừa xóa báo cáo dự án ' . $t_meeting->MeetingName;
                            break;
                        default:
                            break;
                    }
                }
                break;
            case 2:
                $one = $attr['one'];
                $offerUser = User::find($one->IdReviewer);
                $workingUser = User::find($one->UserId);
                $t_meeting = TMeetingWeek::find($one->IdMeeting);
                $room = Room::find($offerUser['RoomId']);

                $arrMailAddressTo = [($offerUser && $offerUser->email) ? $offerUser->email : null];
                $arrMailAddressCC = [($workingUser && $workingUser->email) ? $workingUser->email : null];

                $to_name = $workingUser ? $workingUser->FullName : null;

                // Clean mail
                $arrMailAddressTo = array_filter($arrMailAddressTo);
                $arrMailAddressCC = array_filter($arrMailAddressCC);

                $arrToken = DB::table('push_token')
                    ->where('UserID', $one->UserId)
                    ->whereNull('deleted_at')
                    ->where('allow_push', 1)
                    ->pluck('token_push')->toArray();

                if (count($arrMailAddressTo) != 0) {
                    $subjectMail = "TB nhận xét danh sách báo cáo quản lý dự án";
                    $mailNameFrom = $offerUser->FullName . " - " . $room->Name;
                    $t_pm_report = TReportPM::query()->where("IdMeeting", $one->IdMeeting)->orderByDesc("updated_at")->first();
                    $content = "<p>Nội dung nhận xét:</p>";
                    $content .= $t_pm_report->Note_Report;
                    $contentMail = $this->_genContentMail($t_meeting->MeetingName, $t_meeting->MeetingTimeFrom, $t_meeting->MeetingTimeTo,
                        $offerUser->FullName . " vừa nhận xét báo cáo của bạn tại dự án ", $to_name, $content);
                }
                if (count($arrToken) != 0) {
                    $sendData = [
                        "id" => $t_meeting->id,
                        "data" => self::KEY_APP
                    ];
                    $headMess = "Bạn nhận được 1 nhận xét";
                    $bodyNoti = $offerUser->FullName . ' đã nhận xét báo cáo trong dự án: ' . $t_meeting->MeetingName;
                }
                break;
            default:
                break;
        }
        $isSendEmail = $isSendEmail ? count($arrMailAddressTo) != 0 : $isSendEmail;
        $isSendNotification = $isSendNotification ? count($arrToken) != 0 : $isSendEmail;

        if ($isSendNotification) {
            NotificationController::sendCloudMessaseNoti($headMess, $arrToken, $bodyNoti, $sendData);
        }

        if ($isSendEmail) {
            // Clean mail
            foreach ($arrMailAddressTo as $mail) {
                if (($key = array_search($mail, $arrMailAddressCC)) !== false) {
                    unset($arrMailAddressCC[$key]);
                }
            }
            $this->attr_mail_html["subjectMail"] = $subjectMail;
            $this->attr_mail_html["contentMail"] = $contentMail;
            $this->attr_mail_html["arrMailAddressFrom"] = config('mail.from.address');
            $this->attr_mail_html["mailNameFrom"] = $mailNameFrom;
            $this->attr_mail_html["arrMailAddressTo"] = $arrMailAddressTo;
            $this->attr_mail_html["arrMailAddressCC"] = $arrMailAddressCC;

            SendEmail::dispatch("send_html", $this->attr_mail_html)->delay(now()->addMinute());
        }
    }

    private function _genContentMail($meeting_name, $meeting_time_from, $meeting_time_to, $modify, $to_name = null, $more = null): string
    {
        $contentMail = $to_name == null ? "<p>Kính gửi Ông/Bà</p>" : "Kính gửi " . $to_name;
        $contentMail .= "<p>" . $modify;
        $contentMail .= " với nội dung sau.";
        $contentMail .= "<p>Tiêu đề: " . $meeting_name . ".</p>";
        $contentMail .= "<p>Thời gian: từ ngày " . Carbon::parse($meeting_time_from)->format(self::FOMAT_DISPLAY_DMY);
        $contentMail .= !is_null($meeting_time_to) ? " đến ngày " . Carbon::parse($meeting_time_to)->format(self::FOMAT_DISPLAY_DMY) : "";
        $contentMail .= ".</p>";
        $contentMail .= $more != null ? $more : "";
        $contentMail .= "<p>Xin chân thành cảm ơn.</p>";
        $contentMail .= "<p>__</p>";
        $contentMail .= "<small>Ông/bà vui lòng truy cập vào hệ thống để biết thêm chi tiết.</small>";
        return $contentMail;
    }

    public function changePublic(Request $request): JsonResponse
    {
        if (!$request->filled("id"))
            return response()->json(["errors" => "Thiếu id báo cáo."], 400);
        $t_report = TReportPM::find($request->get("id"));
        if (!$t_report)
            return response()->json(["errors" => "Không tìm thấy báo cáo."], 400);
        $is_public = $t_report->IsPublic == 0 ? 1 : 0;
        $t_report->IsPublic = $is_public;
        $t_report->save();
        return response()->json("Thay đổi trạng thái thành công.");
    }
}
