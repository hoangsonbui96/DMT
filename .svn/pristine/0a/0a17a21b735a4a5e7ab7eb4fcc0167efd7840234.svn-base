<?php

namespace App\Http\Controllers\Api;

use App\CalendarEvent;
use App\DailyReport;
use App\ErrorReview;
use App\Exports\WorkingTaskExport;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\TaskWork\TraitTaskWorkHistory;
use App\Http\Controllers\Api\WorkTask\TraitNotificationTask;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\WorkTask\AddErrorReviewRequest;
use App\Http\Requests\WorkTask\ReportErrorReviewRequest;
use App\Http\Requests\WorkTask\ReportTaskWorkRequest;
use App\Http\Requests\WorkTask\SearchTaskRequest;
use App\Http\Requests\WorkTask\WorkTaskSearchRequest;
use App\Http\Requests\WorkTaskRequest;
use App\Members;
use App\Project;
use App\User;
use App\WorkTask;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ApiWorkTaskController extends ApiBaseController
{
    private const KEY_STATUS = ['TaskNotFinish' => 1, 'TaskWorking' => 2, 'TaskReview' => 3, 'TaskFinish' => 4];
    private const KEY_MESS = [1 => "Chưa thực hiện", 2 => "Đang thực hiện", 3 => "Đang duyệt", 4 => "Hoàn thành"];
    private $query_work_task;
    private $query_members;
    private $perPage = 10;
    private const SCREEN_NAME = 'TaskWorking';

    use TraitTaskWorkHistory {
        TraitTaskWorkHistory::__construct as private __historyConstruct;
    }
    use TraitNotificationTask;

    public function __construct()
    {
        $this->query_work_task = WorkTask::query();
        $this->query_members = Members::query();
        $this->detailRoleScreen(self::SCREEN_NAME);
    }

    // Calculate total hours working of current project
    private function _calculateHoursProject($id_project): float
    {
        return round((float)DailyReport::query()->selectRaw("sum(WorkingTime) as TotalHours")
            ->join("work_tasks", "daily_reports.TaskID", "=", "work_tasks.id")
            ->where("work_tasks.ProjectID", $id_project)->pluck("TotalHours")->first(), 2);
    }

    // Calculate progress of current project
    private function _calculateProgressProject($id_project): float
    {
        $tasks = WorkTask::query()->select("id", "StartDate", "EndDate")
            ->where('ProjectID', $id_project)->get();
        $time_do_project = $tasks->sum(function ($task) {
            $task->ReportLast = DailyReport::query()->select("Progressing", "DateCreate")
                ->where("TaskID", $task->id)->latest("DateCreate")->first();
            $task->TimeWorking = $this->_calculateDayWorking($task);
            return $task->TimeWorking;
        });
        $progress = $tasks->sum(function ($task) use ($time_do_project) {
            if ($time_do_project == 0) return 0;
            $weight = (float)$task->TimeWorking / $time_do_project;
            $progress = empty($task->ReportLast->Progressing) ? 0 : (float)$task->ReportLast->Progressing / 100;
            return $weight * $progress;
        });
        return round($progress * 100, 2);
    }

    // Calculate day working of current task (depending report)
    private function _calculateDayWorking($task): int
    {
        $start = $task->StartDate;
        if ($start == 0) {
            return 0;
        }
        if (!$task->EndDate && !$task->reportLast) {
            $end = Carbon::now();
        } else if (!$task->EndDate && $task->reportLast) {
            $end = $task->report_last->DateCreate;
        } else {
            $end = $task->EndDate;
        }
        $day_diff = Carbon::parse($end)->diffInDaysFiltered(function (Carbon $date) {
                return !$date->isSunday() || !$date->isSaturday();
            }, Carbon::parse($start)) + 1;
        $day_off = CalendarEvent::query()->selectRaw("(DATEDIFF(EndDate, StartDate) + 1) as `DateDiff`")
            ->where("Type", 1)->whereDate("StartDate", ">=", $start)->where("EndDate", "<=", $end)
            ->get()->count();
        return $day_diff - $day_off;
    }

    // Append attribute for current project
    private function _addAttrProject($projects)
    {
        foreach ($projects as &$value) {
            $id_leaders = array_slice(explode(",", $value['Leader']), 1, -1);
            $id_members = array_slice(explode(',', $value['Member']), 1, -1);
            $value['Members'] = count(array_unique(array_merge($id_leaders, $id_members)));
            $value['TotalHours'] = $this->_calculateHoursProject($value['id']);;
            $value['Progress'] = $this->_calculateProgressProject($value['id']);
        }
        return $projects;
    }

    //Info list tasks of current project
    private function _getInfoTask($id_project, $keywords = null, $choices = null, $startDate = null, $endDate = null, $status = null, $order_by = "Position", $sort_by = "asc")
    {
        $key = [
            'work_tasks.id',
            'Name',
            'CreatedID',
            'Description',
            'work_tasks.Note',
            'Status',
            'Tags',
            'ProjectID',
            'Important',
            'Position',
            'NumberReturn',
            'StartDate',
            'EndDate',
        ];
        $data = WorkTask::with('documents')->select($key)->join("members", "members.WorkTaskID", "=", "work_tasks.id");
        if (!empty($choices)) {
            $data->whereIn("Status", $choices);
        }
        if (\auth()->user()->cant('viewAll-task', $id_project)) {
            $data->where('members.UserID', \auth()->id());
        }
        if (!is_null($keywords)) {
            $user_id = User::fullName($keywords)->get()->pluck("id");
            $work_task_id = Members::whereIn('UserID', $user_id)->pluck('WorkTaskID');
            $data->where(function ($query) use ($keywords, $user_id, $work_task_id) {
                $query = $query->orWhere("Name", "like", "%" . $keywords . "%")->orWhere("Tags", "like", "%," . $keywords . ",%");
                if (count($user_id) != 0) {
                    $query = $query->orWhereIn("work_tasks.id", $work_task_id->toArray());
                }
            });
        }
        if (!is_null($startDate)) {
            $data->where('StartDate', '>=', \Illuminate\Support\Carbon::createFromFormat(self::FOMAT_DMY, $startDate)->format(self::FOMAT_DB_YMD));
        }
        if (!is_null($endDate)) {
            $data->where('EndDate', '<=', \Illuminate\Support\Carbon::createFromFormat(self::FOMAT_DMY, $endDate)->format(self::FOMAT_DB_YMD));
        }
        if (!is_null($status)) {
            if ($order_by == "Important") {
                $data->where('work_tasks.ProjectID', $id_project)->where('Status', $status);
                $data2 = clone $data;
                $data->where('members.UserID', auth()->id())->orderByDesc('Important');
                $data = $data->get()->merge($data2->get());
            } else {
                $data->where('work_tasks.ProjectID', $id_project)->where('Status', $status)
                    ->orderByRaw('ISNULL(' . $order_by . ') ' . $sort_by . ', ' . $order_by . ' ' . $sort_by);
                $data = $data->get();
            }
        } else {
            $data->where('work_tasks.ProjectID', $id_project)->orderBy($order_by, $sort_by);
            $data = $data->get();
        }
        $members = Members::query()->join('users', 'users.id', '=', 'members.UserID')
            ->whereIn('WorkTaskID', $data->map(function ($item, $key) {
                return $item->id;
            }))->select(["users.id", "FullName", "Email", "username", "WorkTaskID"])->get();
        $daily_reports = DailyReport::query()->whereIn('TaskID', $data->map(function ($item, $key) {
            return $item->id;
        }))->whereDate("DateCreate", '=', Carbon::now()->format(self::FOMAT_DB_YMD))->get();
        return $data->map(function ($item, $key) use ($members, $daily_reports) {
            $item->Member = $members->where("WorkTaskID", $item->id)->values();
            $item->ReportToday = $daily_reports->where("TaskID", $item->id)->count() != 0;
            return $item;
        });
    }

    //Info of current task
    private function _getInfoTaskCurrent($id_work_task)
    {
        $query_key_work = [
            'id', 'Name', 'CreatedID', 'Description', 'work_tasks.Note', 'Status', 'Tags', 'ProjectID', 'NumberReturn',
            'Important', 'Position', 'StartDate', 'EndDate'
        ];
        $data = WorkTask::query()->select($query_key_work)->where('work_tasks.id', $id_work_task)->first();
        if (empty($data)) return null;
        if (\auth()->user()->cant('viewAll-task', $data->ProjectID) && \auth()->user()->cant('viewAny-task')) {
            if (!in_array($id_work_task, (array)Members::query()->where('UserID', \auth()->id())->pluck('WorkTaskID'))) {
                return null;
            }
        }
        $query_user = ['users.id', 'users.FullName', 'users.email', 'users.username'];
        $data['Members'] = User::query()->select($query_user)
            ->join('members', 'members.UserID', '=', 'users.id')
            ->where('members.WorkTaskID', '=', $id_work_task)->get();
        return $data;
    }

    public function showProject(Request $request, $order_by = 'id', $sort_by = 'asc'): JsonResponse
    {
        $project = Project::query()->select('id', 'NameVi')->orderBy($order_by, $sort_by)->get();
        return AdminController::responseApi(200, null, true, $project);
    }

    // List all projects or detail project
    public function show(WorkTaskSearchRequest $request, $id = "all", $order_by = "NameVi", $sort_by = "asc", $excel = false): JsonResponse
    {
        try {
            $this->authorize('action', $this->role_list['View']);
            if (auth()->user()->cant('viewAny-project')) {
                return AdminController::responseApi(403, null, false, ['data_project' => null, 'role_key' => self::SCREEN_NAME]);
            }
            if ($request->filled("Date")) {
                if (($request->get("Date")[0] == null && $request->filled(["errorIndex"]))) {
                    return AdminController::responseApi(422, 'Vui lòng nhập Ngày bắt đầu', true, ['data_project' => null, 'role_key' => self::SCREEN_NAME]);
                }
                if ($request->get("Date")[0] != null && !$request->filled(["errorIndex"])) {
                    return AdminController::responseApi(422, 'Vui lòng nhập Trong khoảng thời gian ', true, ['data_project' => null, 'role_key' => self::SCREEN_NAME]);
                }
            }
            $projects = Project::query();
            $key_select = [
                'projects.id',
                'NameVi',
                'Customer',
                'projects.StartDate',
                'projects.EndDate',
                'Leader',
                'Member',
                'projects.deleted_at'
            ];
            $key_select = array_merge(
                $key_select,
                [
                    DB::raw('count(case when work_tasks.Status = 1 then 1 end) AS TaskNotFinish'),
                    DB::raw('count(case when work_tasks.Status = 2 then 1 end) AS TaskWorking'),
                    DB::raw('count(case when work_tasks.Status = 3 then 1 end) AS TaskReview'),
                    DB::raw('count(case when work_tasks.Status = 4 then 1 end) AS TaskFinish')
                ]
            );
            $check = in_array($order_by, $key_select);
            $projects->leftJoin('work_tasks', 'work_tasks.ProjectID', '=', 'projects.id')
                ->groupBy('projects.id');

            if ($id != "all") {
                array_push($key_select, "projects.Description", "projects.NameShort", "projects.NameJa");
                $projects = $this->_addAttrProject($projects->select($key_select)->where("projects.id", "=", $id)->get());
                return AdminController::responseApi(200, null, true, ['data_project' => $projects, 'role_key' => self::SCREEN_NAME]);
            }
            if (auth()->user()->role_group != 2) {
                $projects->where(function ($query) {
                    $query->Member([\auth()->id()])->orLeader([auth()->id()]);
                });
            }
            if ($request->filled('choices')) {
                $choices = $request->get('choices');
                foreach ($choices as $i => $item) {
                    $temp = explode('~', $item);
                    $value = $temp[0];
                    $type = $temp[1];
                    if ($type == 'Leader' || $type == 'Member') {
                        $projects->where(function ($query) use ($value, $type) {
                            $query->orWhere($type, 'like', "%," . $value . ",%");
                        });
                    } else {
                        $projects->where(function ($query) use ($value, $type) {
                            $query->where($type, 'like', $value);
                        });
                    }
                }
            }
            if ($request->filled(["errorIndex"])) {
                $start = empty($request->get("Date")[0])
                    ? Carbon::now()->format(self::FOMAT_DB_YMD)
                    : Carbon::createFromFormat(self::FOMAT_DMY, $request->get('Date')[0])->format(self::FOMAT_DB_YMD);
                $errorIndex = $request->get('errorIndex');
                switch ($request->get('errorType')) {
                    case 'day':
                        $projects->whereBetween('projects.StartDate', [Carbon::parse($start)->subDays($errorIndex), Carbon::parse($start)->addDays($errorIndex)]);
                        break;
                    case 'month':
                        $projects->whereBetween('projects.StartDate', [Carbon::parse($start)->subMonths($errorIndex), Carbon::parse($start)->addMonths($errorIndex)]);
                        break;
                    case 'year':
                        $projects->whereBetween('projects.StartDate', [Carbon::parse($start)->subYears($errorIndex), Carbon::parse($start)->addYears($errorIndex)]);
                        break;
                    case 'option':
                        break;
                    default:
                        return AdminController::responseApi(200, 'Sai loại thời gian tìm kiếm', false);
                }
            }
            $query_raw = '(count(case when work_tasks.Status = 2 then 1 end) + ' .
                'count(case when work_tasks.Status = 3 then 1 end) + ' .
                'count(case when work_tasks.Status = 4 then 1 end)) ';
            if ($request->filled('status') && $request->get('status') == 'on') {
//                $projects->havingRaw($query_raw . ' <> 0')->where("Active", 1)
//                    ->where(function ($query) {
//                        $query->where("projects.EndDate", ">=", Carbon::now()->format(self::FOMAT_DB_YMD))
//                            ->orWhereNull("projects.EndDate");
//                    });
                $projects->where("Active", 1)
                    ->where(function ($query) {
                        $query->where("projects.EndDate", ">=", Carbon::now()->format(self::FOMAT_DB_YMD))
                            ->orWhereNull("projects.EndDate");
                    });
            } else {
                $projects->where(function ($query) {
                    $query->where("projects.EndDate", "<", Carbon::now()->format(self::FOMAT_DB_YMD))
                        ->whereNotNull("projects.EndDate");
                });
            }
            if ($check)
                $projects->orderBy($order_by, $sort_by);
            $projects = $excel ? $projects->select($key_select)->get() : $projects->select($key_select)->paginate($this->perPage);
            $value = $excel ? $this->_addAttrProject($projects) : $this->_addAttrProject($projects)->items();
            if (!$check)
                $value = $sort_by == 'asc' ? collect($value)->sortBy($order_by)->values() : collect($value)->sortByDesc($order_by)->values();
            $data_return = $excel ? ['data_project' => $value] : ['data_project' => $value, 'current' => $projects->currentPage(), 'last' => $projects->lastPage()];
            $data_return['role_key'] = self::SCREEN_NAME;
            return AdminController::responseApi(200, null, true, $data_return);
        } catch (AuthorizationException $e) {
            return AdminController::responseApi(403, $e->getMessage(), false, ['role_key' => self::SCREEN_NAME]);
        } catch (\Exception $exception) {
            return AdminController::responseApi(500, $exception->getMessage(), false);
        }
    }

    // Add or update of current work task
    public function addTask(WorkTaskRequest $request, $id = null): JsonResponse
    {
        if (auth()->user()->cant('action', $this->role_list['Add'])) {
            return AdminController::responseApi(403, "Not authorize", false, ['role' => self::SCREEN_NAME]);
        }
        $arr_send = [];
        foreach ($request->get("Name") as $index => $name) {
            $is_exist_name = WorkTask::where("ProjectID", $request->input("ProjectID"))
                ->whereRaw('Name  =  convert(? using binary)', $name);
            if ($id == null && $is_exist_name->exists()) {
                return AdminController::responseApi(422, 'Tên công việc đã tồn tại', false);
            }
            if ($id != null && $is_exist_name->where("id", "!=", $id)->exists()) {
                return AdminController::responseApi(422, 'Tên công việc đã tồn tại', false);
            }
            if ($id == null) {
                $work_task = new WorkTask();
                $action = "insert";
            } else {
                $work_task = WorkTask::find($id);
                $action = "update";
            }
            $startDate = $request['StartDate'][$index];
            $endDate = $request['EndDate'][$index];
            $tags = $request['Tags'][$index];
            $status = $request['Status'][$index];
            $note = $request['Note'][$index];
            $description = $request['Description'][$index];
            $create_id = $id == null ? auth()->id() : $work_task->CreateID;
            $project_id = $request['ProjectID'];
            $member = $request['Member'][$index];
            $startDate = $startDate != null ?
                Carbon::createFromFormat(self::FOMAT_DMY, $startDate)->format(self::FOMAT_DB_YMD) : null;
            $endDate = $endDate != null ?
                Carbon::createFromFormat(self::FOMAT_DMY, $endDate)->format(self::FOMAT_DB_YMD) : null;
            if ($tags != null) {
                $a = explode(",", $tags);
                array_shift($a);
                $tags = ',#' . join(',#', $a) . ',';
            }
            $messages = [];
            if ($action == "insert" && $member != null) {
                $action = "assign";
                $messages[] = "giao việc";
            }
            if ($action == "update") {
                if ($work_task->Name != $name) array_push($messages, "tên task");
                if ($work_task->StartDate != $startDate || $work_task->EndDate != $endDate) array_push($messages, "thời gian");
                if ($work_task->Description != $description) array_push($messages, "mô tả");
                if ($work_task->Note != $note) array_push($messages, "ghi chú");
                if ($work_task->Tags != $tags) array_push($messages, "thẻ tag");
                if ($work_task->Status != $status) array_push($messages, "trạng thái");
                if ($work_task->members[0] && $work_task->members[0]->UserID != $member) {
                    $action = "assign";
                    $messages[] = "giao việc";
                }
            }
            if ($action == "update" && count($messages) == 0) {
                return AdminController::responseApi(200, false, true, ['role' => self::SCREEN_NAME]);
            }
            try {
                $work_task->Name = $name;
                $work_task->Status = $status;
                $work_task->StartDate = $startDate;
                $work_task->EndDate = $endDate;
                $work_task->Description = $description;
                $work_task->Note = $note;
                $work_task->Tags = $tags;
                $work_task->CreatedID = $create_id;
                $work_task->ProjectID = $project_id;
                $work_task->save();
                $work_task->members()->delete();
                $work_task->members()->create(['UserID' => $member]);
            } catch (\Exception $exception) {
                return AdminController::responseApi(500, $exception->getMessage(), false);
            }

            $context = ['task' => $work_task, 'action' => $action, 'messages' => $messages];
            $cant_push = function () use ($context) {
                return ($context['action'] == 'update' && count($context['messages']) == 0) ||
                    ($context['action'] == 'assign' && count($context['messages']) == 0);
            };
            if (array_key_exists($member, $arr_send) && !$cant_push()) {
                array_push($arr_send[$member], $context);
            }

            if (!array_key_exists($member, $arr_send) && !$cant_push()) {
                $arr_send[$member] = [$context];
            }
        }
        //Send Email
        foreach ($arr_send as $member_id => $content) {
            if (!$this->send(auth()->user(), $member_id, $request['ProjectID'], $content)) {
                continue;
            }
        }
        return AdminController::responseApi(200, null, 'Task mới đã được thêm vào', ['role_key' => self::SCREEN_NAME]);
    }

    // Post daily report for current task
    public function reportTaskWork(ReportTaskWorkRequest $request): JsonResponse
    {
        $timeRequest = Carbon::createFromFormat(self::FOMAT_DMY, $request->get('Date'))->format(self::FOMAT_DB_YMD);
        $id_task = $request->get('id');
        $task = WorkTask::findOrFail($id_task);
        $id_project = $task->ProjectID;

        if (is_null($id_project) || empty($id_project)) {
            return AdminController::responseApi(422, 'ID task công việc không hợp lệ', false);
        }

        if ($task->members[0]->UserID == null) {
            return AdminController::responseApi(422, 'Không thể báo cáo được do task chưa có người thực hiện', false);
        }
        
        if (!is_null($task->project->EndDate) && Carbon::parse($timeRequest)->gt($task->project->EndDate)) {
            return AdminController::responseApi(422, 'Dự án đã kết thúc, không thể báo cáo được', false);
        }
        if (auth()->user()->role_group != 2) {
            if (Carbon::parse($timeRequest)->gt(Carbon::parse($task->EndDate))) {
                return AdminController::responseApi(422, 'Thời gian báo cáo không hợp lệ', false);
            }
            if (Carbon::now()->subDays(4)->gt(Carbon::parse($timeRequest))) {
                return AdminController::responseApi(422, "Không thể báo cáo được do đã quá hạn 3 ngày", false);
            }
        }
        $queryDailyReport = DailyReport::query()->where('TaskID', '=', $id_task)
            ->where('DateCreate', '=', $timeRequest)->get();
        $key_convert = [
            'TaskID' => 'id',
            'Date' => 'Date',
            'DateCreate' => 'Date',
            'ScreenName' => 'ScreenName',
            'TypeWork' => 'TypeWork',
            'Contents' => 'Contents',
            'Progressing' => 'Progressing',
            'Delay' => 'Timedelay',
            'Soon' => 'Timesoon',
            'WorkingTime' => 'WorkingTime',
            'Note' => 'Note'
        ];
        try {
            $dailyReport = (count($queryDailyReport) == 0) ? new DailyReport() : $queryDailyReport[0];
            foreach ($key_convert as $db_key => $request_key) {
                if ($request_key == 'Date') {
                    $dailyReport->$db_key = $timeRequest;
                } elseif (($request_key == 'Timesoon' && $request->$request_key == null) || ($request_key == 'Timedelay' && $request->$request_key == null)) {
                    $dailyReport->$db_key = 0;
                } else {
                    $dailyReport->$db_key = $request->get($request_key);
                }
            }
            $dailyReport->ProjectID = $id_project;
            $task = WorkTask::find($id_task);
            if ($task->Status = self::KEY_STATUS['TaskNotFinish']) {
                $task->Status = self::KEY_STATUS['TaskWorking'];
                $task->save();
                $dailyReport->UserID = $task->members[0]->UserID;
                $dailyReport->save();
            }
            if ($request->get('Progressing') == 100) {
                $task->Status = self::KEY_STATUS['TaskReview'];
                $task->save();
                $dailyReport->UserID = $task->members[0]->UserID;
                $dailyReport->save();
                $content = [[
                    'action' => 'review',
                    'task' => $task,
                ]];
                $this->send(auth()->user(), $task->members()->first()->UserID, $task->project()->first()->id, $content);
            }
            return AdminController::responseApi(200, null, true, ['role_key' => self::SCREEN_NAME]);
        } catch (\Exception $exception) {
            return AdminController::responseApi(500, $exception->getMessage(), false);
        }
    }

    // Change Important of 1 task (toggle)
    public function changeImportant(Request $request, $id): JsonResponse
    {
        try {
            $task = WorkTask::findOrFail($id);
            $value = $task->Important == 1 ? 0 : 1;
            optional(WorkTask::find($id))->update(["Important" => $value]);
            $status_code = 200;
            $error = null;
            $success = true;
        } catch (\Exception $exception) {
            $status_code = 500;
            $error = $exception->getMessage();
            $success = false;
        } finally {
            return AdminController::responseApi($status_code, $error, $success);
        }
    }

    // Update Status or Important of current task or Position a task's array
    public function changeStatus(Request $request): JsonResponse
    {
        try {
            // Thay đổi trạng thái của task
            if ($request->filled(["Status", "Items"])) {
                $errors = [];
                $arr_id = [];
                $item = $request->get("Items")[0];
                $to_status = $request->get('Status');
                //                foreach ($request->get("Items") as $index => $item) {
                $task = WorkTask::findOrFail($item);
                if (auth()->user()->cant("viewAll-task", $task->project->id) && Members::where('WorkTaskID', '=', $task->id)->pluck("UserID")->first() != auth()->id()) {
                    array_push($errors, $task->Name);
                    array_push($arr_id, $task->id);
                } else {
                    if ($task->members[0]->UserID == null) {
                        array_push($errors, $task->Name . ' vì chưa có người thực hiện');
                        array_push($arr_id, $task->id);
                    } else {
                        switch ($task->Status) {
                            case self::KEY_STATUS['TaskNotFinish']:
                                if ($to_status == self::KEY_STATUS['TaskReview'] || $to_status == self::KEY_STATUS['TaskFinish']) {
                                    array_push($errors, $task->Name);
                                    array_push($arr_id, $task->id);
                                } else {
                                    $task->update(["Status" => $to_status]);
                                }
                                break;
                            case self::KEY_STATUS['TaskWorking']:
                                if ($to_status == self::KEY_STATUS['TaskFinish']) {
                                    array_push($errors, $task->Name);
                                    array_push($arr_id, $task->id);
                                } elseif ($to_status == self::KEY_STATUS['TaskNotFinish']) {
                                    if (DailyReport::query()->where("TaskID", $item)->exists()) {
                                        array_push($errors, $task->Name . ' vì đã có báo cáo');
                                        array_push($arr_id, $task->id);
                                    } else {
                                        $task->update(["Status" => $to_status]);
                                    }
                                } elseif ($to_status == self::KEY_STATUS['TaskReview']) {
                                    if ($to_status == self::KEY_STATUS['TaskNotFinish']) {
                                        array_push($errors, $task->Name);
                                        array_push($arr_id, $task->id);
                                    } else if ($to_status == self::KEY_STATUS['TaskReview']) {
                                        break;
                                    } else {
                                        $task->update(["Status" => $to_status]);
                                    }
                                }
                                break;
                            case self::KEY_STATUS['TaskFinish']:
                                if (
                                    $to_status == self::KEY_STATUS['TaskNotFinish']
                                    || $to_status == self::KEY_STATUS['TaskReview']
                                ) {
                                    array_push($errors, $task->Name);
                                    array_push($arr_id, $task->id);
                                }
                                // ve trang thai dang Thuc hien
                                break;
                            case self::KEY_STATUS['TaskReview']:
                                if (auth()->user()->cant('review-task', $task)) {
                                    return AdminController::responseApi(403, "Bạn không có quyền thực hiện hành động này", false, ['role_key' => self::SCREEN_NAME]);
                                }
                                if ($to_status == self::KEY_STATUS['TaskNotFinish']) {
                                    array_push($errors, $task->Name);
                                    array_push($arr_id, $task->id);
                                } else {
                                    $task->update(["Status" => $to_status]);
                                }
                                break;
                            default:
                                return AdminController::responseApi(400, "Invalide request keys", false, ['role_key' => self::SCREEN_NAME]);
                        }
                    }
                }
                //                }
                if (!empty($errors)) {
                    return AdminController::responseApi(422, "Không thể thay đổi trạng thái task", false, ['not_update' => count($errors), 'messages' => $errors, 'role_key' => self::SCREEN_NAME, 'id' => $arr_id]);
                }
                if ($to_status == self::KEY_STATUS['TaskReview'] || $to_status == self::KEY_STATUS['TaskFinish']) {
                    $content = [[
                        'action' => 'update',
                        'task' => $task,
                        'messages' => ["chuyển sang trạng thái " . self::KEY_MESS[$to_status]]
                    ]];
                    $this->send(auth()->user(), $task->members()->first()->UserID, $task->project()->first()->id, $content);
                }
                return AdminController::responseApi(200, null, "Change status success", ['role_key' => self::SCREEN_NAME]);
            }
            // Sắp xếp các task trong 1 cột
            if ($request->filled("Positions")) {
                foreach ($request->get("Positions") as $index => $item) {
                    optional(WorkTask::find($item[0]))->update(["Position" => $item[1]]);
                }
                return AdminController::responseApi(200, null, true, ['role_key' => self::SCREEN_NAME]);
            }
            return AdminController::responseApi(402, "Your request is not enough keys", false);
        } catch (\Exception $exception) {
            return AdminController::responseApi(500, $exception->getMessage(), false);
        }
    }

    // Info of list task
    public
    function infoTask(SearchTaskRequest $request, $id_project, $id_task = null): JsonResponse
    {
        try {
            $this->authorize('action', $this->role_list['View']);
            if (is_null($id_task)) {
                $data = $request->has(['Status', 'OrderBy', 'SortBy'])
                    ? $this->_getInfoTask(
                        $id_project,
                        $request->get('Keywords'),
                        $request->get('Choices'),
                        null,
                        null,
                        $request->get('Status'),
                        $request->get('OrderBy'),
                        $request->get('SortBy')
                    )
                    : $this->_getInfoTask(
                        $id_project,
                        $request->get('Keywords'),
                        $request->get('Choices'),
                        $request->get('StartDate'),
                        $request->get('EndDate')
                    );

                return AdminController::responseApi(200, null, true, ['data' => $data, 'role_key' => self::SCREEN_NAME]);
            }
            $data = $this->_getInfoTaskCurrent($id_task);
            return AdminController::responseApi(200, null, true, ['data' => $data, 'role_key' => self::SCREEN_NAME]);
        } catch (AuthorizationException $e) {
            return AdminController::responseApi(403, "Bạn không có quyền thực hiện hành động này", false, ['role_key' => self::SCREEN_NAME]);
        } catch (\Exception $e) {
            return AdminController::responseApi(500, $e->getMessage(), false);
        }
    }

    //  API suggest FullName of user, Name of task and tags in 1 project
    public
    function suggestSearch(Request $request, $id): JsonResponse
    {
        $task_name = WorkTask::query()->select('Name')->where('ProjectID', '=', $id)->orderBy('Name')->get()->pluck('Name')->toArray();
        $user = User::query()->select('FullName')->orderBy('FullName')->get()->pluck('FullName')->toArray();
        $tag = WorkTask::query()->select('Tags')->where('ProjectID', '=', $id)->orderBy('Tags')->get();
        $arr_tags = [];
        foreach ($tag as $key => $item) {
            if (is_null($item['Tags'])) {
                continue;
            } else {
                foreach (explode(',', $item['Tags']) as $k => $i) {
                    if (!in_array($i, $arr_tags)) {
                        array_push($arr_tags, $i);
                    }
                }
            }
        }
        array_shift($arr_tags);
        $result = array_merge($task_name, $user);
        $result = array_merge($result, $arr_tags);
        return AdminController::responseApi(200, null, true, $result);
    }

    // Delete task
    public
    function delete(Request $request): JsonResponse
    {
        try {
            $this->authorize('action', $this->role_list['Delete']);
        } catch (AuthorizationException $e) {
            return AdminController::responseApi(403, "Bạn không đủ quyền để thực hiện hành động này", false, ['role_key' => self::SCREEN_NAME]);
        }
        $rules = [
            'Items' => 'required|array',
            'Items.*' => 'string|min:1'
        ];
        $messages = [
            'Items.required' => 'Thiếu task cần xóa',
            'Items.array' => 'Sai định dạng',
        ];
        $validated = Validator::make($request->all(), $rules, $messages);
        if ($validated->fails()) {
            return AdminController::responseApi(400, $validated->messages(), false);
        }
        try {
            $arr_send = [];
            $items = $request->get("Items");
            $tasks = WorkTask::query()->whereIn("id", $items)->get();
            $project_id = $tasks->first()->ProjectID;
            $context = ['task' => null, 'action' => 'delete', 'messages' => ''];
            foreach ($tasks as $task) {
                $member = !is_null($task->members()->first()) && isset($task->members()->first()->UserID) ? $task->members()->first()->UserID : null;
                $context['task'] = $task;
                if (array_key_exists($member, $arr_send)) {
                    array_push($arr_send[$member], $context);
                } else {
                    $arr_send[$member] = [$context];
                }
            }
            DailyReport::query()->whereIn("TaskID", $items)->delete();
            Members::query()->where("WorkTaskID", $items)->delete();
            $count = WorkTask::destroy($items);
            foreach ($arr_send as $member_id => $content) {
                if (!$this->send(auth()->user(), $member_id, $project_id, $content)) {
                    continue;
                }
            }
            return AdminController::responseApi(200, null, "Xóa thành công $count task.");
        } catch (\Exception $exception) {
            return AdminController::responseApi(500, $exception->getMessage(), false);
        }
    }

    // Update task
    public
    function update(Request $request, $id): JsonResponse
    {
        try {
            $this->authorize('action', $this->role_list['Edit']);
            $task = optional(WorkTask::find($id));
            if (!$request->filled("Name")) {
                return AdminController::responseApi(422, "Không được bỏ trống tên task", false);
            }
            if ($request->filled("Name")) {
                $exist = WorkTask::query()->where('id', '!=', $id)
                    ->where('ProjectID', '=', $request->get('ProjectID'))
                    ->whereRaw('Name = convert(? using binary)', $request->get('Name'))
                    ->get();
                if (isset($exist) && count($exist) > 0) {
                    return AdminController::responseApi(422, 'Tên công việc đã tồn tại', false);
                }
            }
            if (!$request->filled("StartDate")) {
                return AdminController::responseApi(422, "Không được bỏ trống", false);
            }
            $request->merge(["StartDate" => Carbon::createFromFormat(self::FOMAT_DMY, $request->get('StartDate'))->format(self::FOMAT_DB_YMD)]);
            if ($request->filled('EndDate')) {
                $request->merge(["EndDate" => Carbon::createFromFormat(self::FOMAT_DMY, $request->get('EndDate'))->format(self::FOMAT_DB_YMD)]);
            }
            $task->update($request->except("_token"));
            return AdminController::responseApi(200, null, true);
        } catch (AuthorizationException $e) {
            return AdminController::responseApi(403, "Bạn không đủ quyền thực hiện hành động này", false, ['role_key' => self::SCREEN_NAME]);
        } catch (\Exception $exception) {
            return AdminController::responseApi(500, $exception->getMessage(), false);
        }
    }

    // Export info of list project
    public function export(WorkTaskSearchRequest $request, $order_by = 'id', $sort_by = 'asc')
    {
        $data = json_decode($this->show($request, 'all', $order_by, $sort_by, true)->getContent());
        if ($data->status_code == 200 && !empty($data->data->data_project)) {
            $totalCol = 13;
            $totalRow = count((array)$data->data->data_project);
            return Excel::download(new WorkingTaskExport($data->data->data_project, $totalCol, $totalRow), 'DanhSachDuAn' . date('d-m-Y') . '.xlsx');
        } else {
            return AdminController::responseApi($data->status_code, $data->error, false);
        }
    }

    // Add error when review a task
    public function addErrorReview(AddErrorReviewRequest $request): JsonResponse
    {

        try {
            $task = WorkTask::find($request->get('id'));
            $this->authorize('review-task', $task);
            $task->NumberReturn += 1;
            $task->Status = self::KEY_STATUS['TaskWorking'];
            $task->StartDate = Carbon::createFromFormat(self::FOMAT_DMY, $request->get('StartDate'))->format(self::FOMAT_DB_YMD);
            $task->EndDate = is_null($request->get('EndDate')) ? null : Carbon::createFromFormat(self::FOMAT_DMY, $request->get('EndDate'))->format(self::FOMAT_DB_YMD);
            $task->save();
            DailyReport::query()->where('TaskID', $task->id)->latest('updated_at')->update(['Progressing' => $request->get('Progressing')]);
            ErrorReview::create(['Note' => $request->get('Note'), 'Descriptions' => $request->get('Descriptions'), 'WorkTaskID' => $task->id, 'AcceptedByID' => \auth()->id()]);
            $content = [[
                'action' => 'error',
                'task' => $task,
            ]];
            $this->send(auth()->user(), $task->members()->first()->UserID, $task->project()->first()->id, $content);
            return AdminController::responseApi(200, null, true, ['task' => $task, 'role_key' => self::SCREEN_NAME]);
        } catch (AuthorizationException $exception) {
            return AdminController::responseApi(403, "Bạn không có quyền thực hiện hành động này", false, ['role_key' => self::SCREEN_NAME]);
        }
    }

    // Report for error task
    public function reportErrorReview(ReportErrorReviewRequest $request): JsonResponse
    {
        try {
            $arr_create = [
                'UserID' => \auth()->id(),
                'Date' => Carbon::now()->format(self::FOMAT_DB_YMD),
                'DateCreate' => Carbon::now()->format(self::FOMAT_DB_YMD),
                'ProjectID' => $request->get('ProjectID'),
                'TaskID' => $request->get('TaskID'),
                'ScreenName' => $request->get('ScreenName'),
                'TypeWork' => 'BC004',
                'Contents' => $request->get('Contents'),
                'WorkingTime' => $request->get('WorkingTime'),
                'Progressing' => $request->get('Progressing'),
                'Note' => $request->get('Note')
            ];
            $task = WorkTask::find($request->get('TaskID'));
            if ($request->get('Progressing') == 100) {
                $task->update(['Status' => 3]);
                DailyReport::create($arr_create);
                $content = [[
                    "action" => "review-again",
                    "task" => $task,
                ]];
                $this->send(auth()->user(), $task->members()->first()->UserID, $task->project()->first()->id, $content);
            }
            return AdminController::responseApi(200, null, true, ['role_key' => self::SCREEN_NAME]);
        } catch (AuthenticationException $exception) {
            return AdminController::responseApi(403, "Bạn không có quyền thực hiện hành động này", false, ['role_key' => self::SCREEN_NAME]);
        } catch (\Exception $exception) {
            return AdminController::responseApi(500, $exception->getMessage(), false);
        }
    }

    // Get user in current project
    public function userDependProject(Request $request, $id): JsonResponse
    {
        if (\auth()->user()->cant('create-task', Project::withTrashed()->where('id', $id)->first())) {
            return AdminController::responseApi(403, "Bạn không có quyền thực hiện hành động này", false, ['role_key' => self::SCREEN_NAME]);
        }
        try {
            if (auth()->user()->cant('viewAll-task', $id)) {
                $members = User::query()->select('id', 'FullName')->whereIn('id', explode(',', Project::withTrashed()->where('id', $id)->first()->Member))->where('id', auth()->id())->get();
            } else {
                $members = explode(',', Project::withTrashed()->where('id', $id)->first()->Member);
                $leader = explode(',', Project::withTrashed()->where('id', $id)->first()->Leader);
                $list_member = array_unique(array_merge($members, $leader));
                $members = User::query()->select('id', 'FullName')->whereIn('id', $list_member)->get();
            }
            return AdminController::responseApi(200, null, true, ['members' => $members, 'role_key' => self::SCREEN_NAME]);
        } catch (\Exception $exception) {
            return AdminController::responseApi(500, $exception->getMessage(), false);
        }
    }

    // Get report of day in task
    public function loadReport(Request $request, $id): JsonResponse
    {
        $date = $request->get("date");
        $report = DailyReport::query()->where("Date", Carbon::createFromFormat(self::FOMAT_DMY, $date)
            ->format(self::FOMAT_DB_YMD))->where('TaskID', $id)
            ->first();
        return AdminController::responseApi(200, null, true, ["report" => $report, 'role_key' => self::SCREEN_NAME]);
    }

    // Suggest search project
    public function suggestSearchAll(Request $request): JsonResponse
    {
        $limit = 3;
        $result = new Collection();
        $q = $request->get('q');
        if (is_null($q)) {
            return AdminController::responseApi(200, null, true, []);
        }
        $q = "%" . $q . "%";
        $query = auth()->user()->can("viewAll-project") ? Project::query() : Project::query()->Member([auth()->id()]);
        $query_vi = clone $query;
        $query_en = clone $query;
        $query_japan = clone $query;
        $query_short = clone $query;
        $query_customer = clone $query;

        $vis = $query_vi->where('NameVi', 'like', $q)->selectRaw('NameVi as value')->take($limit)->get();
        $ens = $query_en->where('NameEn', 'like', $q)->selectRaw('NameEn as value')->take($limit)->get();
        $japans = $query_japan->where('NameJa', 'like', $q)->selectRaw('NameJa as value')->take($limit)->get();
        $shorts = $query_short->where('NameShort', 'like', $q)->selectRaw('NameShort as value')->take($limit)->get();
        $customers = $query_customer->where('Customer', 'like', $q)->selectRaw('Customer as value')->take($limit)->get();
        $users = User::query()->where('FullName', 'like', $q)->select('id', 'FullName')->take($limit)->get();

        foreach ($vis as $vi) {
            $vi->key = 'Tên dự án Tiếng Việt';
            $vi->up = $vi->value . '~' . 'NameVi';
            $result->push(collect($vi));
        }
        foreach ($ens as $en) {
            $en->key = 'Tên dự án Tiếng Anh';
            $en->up = $en->value . '~' . 'NameEn';
            $result->push(collect($en));
        }
        foreach ($japans as $japan) {
            $japan->key = 'Tên dự án Tiếng Nhật';
            $japan->up = $japan->value . '~' . 'NameJa';
            $result->push(collect($japan));
        }
        foreach ($shorts as $short) {
            $short->key = 'Tên dự án viết tắt';
            $short->up = $short->value . '~' . 'NameShort';
            $result->push(collect($short));
        }
        foreach ($customers as $customer) {
            $customer->key = 'Khách hàng';
            $customer->up = $customer->value . '~' . 'Customer';
            $result->push(collect($customer));
        }
        foreach ($users as $user) {
            if (Project::query()->Leader([$user->id])->exists()) {
                $result->push(collect(['value' => $user->FullName, 'key' => 'Quản lý', 'up' => $user->id . '~' . 'Leader']));
            }
            if (Project::query()->Member([$user->id])->exists()) {
                $result->push(collect(['value' => $user->FullName, 'key' => 'Thành viên', 'up' => $user->id . '~' . 'Member']));
            }
        }
        return AdminController::responseApi(200, null, true, ['role_key' => self::SCREEN_NAME, 'data' => $result]);
    }

    // Upload multiple file to task
    public function uploadFile(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required|string',
            'files.*' => 'nullable|max:2048',
        ], [
            'files.*.max' => 'File dung lượng tối đa là 2MB'
        ]);
        $data_return = ['role_key' => self::SCREEN_NAME];
        if ($validator->fails()) {
            return AdminController::responseApi(422, $validator->errors()->first(), false, $data_return);
        }
        $mass = ['Note' => $request->get('note'), 'UserID' => auth()->id()];
        if ($request->hasFile('files')) {
            $number_file = count($request->file('files'));
            if ($number_file > 5) {
                return AdminController::responseApi(422, 'Tối đa upload 5 file mỗi lần', false, $data_return);
            }
            $file_path = "files/shares/";
            $location_store = "Task/" . $id;
            $doc_path = "";
            $doc_name = "";
            $index = 0;
            foreach ($request->file('files') as $file) {
                $file_name = $file->getClientOriginalName();
                $public_path = public_path() . "\storage\app\public\\files\shares\Task\\" . $id . "\\" . $file_name;
                if (file_exists($public_path)) {
                    $file_name = time() . '_' . $file_name;
                }
                $file_store = $file->storeAs($file_path . $location_store, $file_name, "public");
                if (++$index == $number_file) {
                    $doc_path .= $file_store;
                    $doc_name .= $file_name;
                } else {
                    $doc_path .= $file_store . "?";
                    $doc_name .= $file_name . "?";
                }
            }
            $mass['DocPath'] = $doc_path;
            $mass['DocName'] = $doc_name;
        }
        $task = WorkTask::find($id);
        $task->documents()->create($mass);
        $data_return['document'] = $task->documents()->latest()->first();
        $data_return['document']['Username'] = auth()->user()->FullName;
        $capacity_doc = count(Storage::disk('public')->allFiles('files/shares/Task/' . $id));
        $data_return['document']['TotalDoc'] = $capacity_doc > 99 ? $capacity_doc . "+" : $capacity_doc;
        $data_return['document']['DiffHuman'] = Carbon::parse($data_return['document']->created_at)->diffForHumans();
        return AdminController::responseApi(200, '', true, $data_return);
    }
}
