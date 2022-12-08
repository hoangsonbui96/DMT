<?php

namespace App\Http\Controllers\Admin\TaskWork;

use App\DailyReport;
use App\ErrorReview;
use App\Http\Controllers\Admin\AdminController;
use App\MasterData;
use App\Members;
use App\Menu;
use App\Model\WorkTask\WorkTaskDocument;
use App\Project;
use App\Task;
use App\User;
use App\WorkTask;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class TaskWorkController extends AdminController
{
    //
    const STATUS = ["not_working" => 1, "working" => 2, "review" => 3, "finish" => 4];
    private $deadline = 4;
    private $path_download_file = "storage/app/public/files/shares/Task/";
    private $path_store_file = "";

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->menu = Menu::routeName('admin.TaskWork')->first();
        $this->detailRoleScreen('TaskWorking');
        $this->path_store_file = public_path() . "/" . $this->path_download_file;
    }

    // Show view all project
    public function show(Request $request)
    {
        try {
            $this->authorize('view', $this->menu);
        } catch (AuthorizationException $e) {
            abort(403);
        } finally {
            return $this->viewAdminLayout('task-work.task-work', $this->data);
        }
    }

    // Show view all task of current project
    public function detail(Request $request, $id)
    {
        $this->data['project'] = Project::withTrashed()->where("id", $id)->firstOrFail();
        $this->data['show'] = is_null($this->data['project']->EndDate) ? true : Carbon::parse(Carbon::now())->lt($this->data['project']->EndDate);
        return $this->viewAdminLayout('task-work.task-work-detail', $this->data);
    }

    // Open modal add or update task
    public function openAddTaskModal(Request $request, $id_pr = null, $id = null)
    {
        if (auth()->user()->cant('create-task', Project::withTrashed()->where('id', $id_pr)->first())) {
            return response()->json(['error' => 'Bạn không có quyền thực hiện hành động này'], 403);
        }
        if (!is_null($id_pr) && Project::withTrashed()->where('id', $id_pr)->first()->trashed()) {
            return response()->json(['error' => 'Dự án đã được đóng, không thể tạo được task'], 422);
        }
        if ($request->filled('Status-id')) {
            $this->data['status_id'] = $request->get('Status-id');
            $title_status = [
                __('admin.task-working.status_unfinished'),
                __('admin.task-working.status_working'),
                __('admin.task-working.status_review'),
                __('admin.task-working.status_finished'),
            ];
            $this->data['status_title'] = $title_status[$request->get('Status-id') - 1];
        }
        $query_user = User::query()
            ->select('id', 'FullName')
            ->where('Active', 1)
            ->where('role_group', '!=', 1);
        $query_project = Project::query()->select('id', 'NameVi', 'Member', 'Leader')
            ->whereNull('EndDate')
            ->orWhereDate('EndDate', '>=', Carbon::now()->format(self::FOMAT_DB_YMD));
        if (auth()->user()->role_group != 2) {
            $query_project->Member([auth()->id()]);
        }
        $this->data['itemInfo'] = is_null($id_pr) ? $query_project->get() : Project::withTrashed()->select('id', 'NameVi', 'Member', 'Leader')
            ->where('id', '=', $id_pr)
            ->get();
        $this->data['users'] = is_null($id_pr) ? $query_user->get() : (auth()->user()->cant('viewAll-task', $id_pr) ?
            $query_user
                ->whereIn('users.id', [auth()->id()])
                ->get() :
            $query_user
                ->whereIn('users.id', explode(',', $this->data['itemInfo']->first()->Member))
                ->get());

        //Neu id task null thì lấy thông tin Member của task đó
        if (!is_null($id)) {
            $this->data['taskInfo'] = WorkTask::find($id);
            $members = $query_user
                ->select('users.id')
                ->where('Active', 1)
                ->where('role_group', '!=', 1)
                ->where('WorkTaskID', '=', $id)
                ->join("members", "members.UserID", "=", "users.id")
                ->get()->toArray();
            $values = [];
            foreach ($members as $member) {
                array_push($values, $member['id']);
            }
            $this->data['taskInfo']->Member = $values;
        }
        return $this->viewAdminIncludes('taskwork.add-task-working', $this->data);
    }

    // Open modal report or feed back error task
    public function openReportTaskModal(Request $request, $id)
    {
        try {
            $this->authorize('action', $this->role_list['View']);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => 403, 'messages' => "Bạn không có quyền thực hiện tác vụ này"], 403);
        }
        $task = WorkTask::findOrFail($id);
        if (auth()->user()->cant("viewAll-task", $task->project->id) && Members::query()->where('WorkTaskID', $id)->pluck('UserID')->first() != auth()->id()) {
            return response()->json(['error' => 403, 'messages' => 'Bạn không có quyền thực hiện tác vụ này'], 403);
        }
        if (is_null($task->members()->first()) || !isset($task->members()->first()->UserID)) {
            return response()->json(['error' => 400, 'messages' => "Không thể báo cáo task này do không có người thực hiện"], 400);
        }
        $this->data['id'] = $id;
        $this->data['is_fast_report'] = $request->get('is_fast_report');
        $this->data['list_works'] = MasterData::query()
            ->select('Name', 'DataValue')
            ->where('DataKey', 'BC')->orderBy('DataDisplayOrder')->get();
        $result = DailyReport::query()->where('TaskID', $id)->orderByDesc('created_at');
        $this->data['task'] = $task;
        if ($task->Status == self::STATUS["not_working"] || $task->Status == self::STATUS["working"]) {
            $error = ErrorReview::where("WorkTaskID", $id)->latest("updated_at")->first();
            if ($error)
                $this->data["error"] = $error;
            else
                $result->where("DateCreate", "=", Carbon::today()->format(self::FOMAT_DB_YMD));
        }
        $result = $result->first();
        if ($result) {
            $this->data['reportLast'] = $result;
        }
        return $this->viewAdminIncludes('taskwork.task-working-detail', $this->data);
    }

    // Open modal member of 1 project
    public function openMemberModal(Request $request)
    {
        return $this->viewAdminIncludes('taskwork.task-member-popup');
    }

    // Open modal error review for leader, admin or user has role 'Review'
    public function openErrorReviewModal(Request $request, $id)
    {
        $task = WorkTask::find($id);
        if (\auth()->user()->cant("review-task", $task)) {
            return response()->json(['error' => 403, 'messages' => "Bạn không có quyền thực hiện tác vụ này"], 403);
        }
        $last_report = DailyReport::query()->where('TaskID', $id)->latest()->first();
        $this->data['task'] = $task;
        $this->data['member'] = User::withTrashed()->where('id', Members::query()->where('WorkTaskID', $id)->first()->UserID)->first()->FullName;
        $this->data['last_report'] = $last_report;
        $error = ErrorReview::query()->where('WorkTaskID', $id);
        if (!$error->exists()) {
            $this->data['error'] = $error->latest('created_at')->first();
        }
        return $this->viewAdminIncludes('taskwork.task-review-popup', $this->data);
    }

    // Open modal main of task
    public function openTaskMainModal(Request $request, $id)
    {
        $task = WorkTask::with("documents")->where("id", $id)->first();
        if (auth()->user()->cant("view-task", $task)){
            return response()->json("Bạn không có quyền xem task này.", 403);
        }
        if ($task->Status != self::STATUS["review"] && $task->Status != self::STATUS["finish"]) {
            $error = ErrorReview::where("WorkTaskID", $id)->latest("updated_at")->first();
            if ($error)
                $this->data["error"] = $error;
        }
        $path = $this->path_store_file . $id;
        if (!File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }
        collect($task->documents)->transform(function ($item, $key) {
            $item->DocPath = explode("?", $item->DocPath);
            $item->DocName = explode("?", $item->DocName);
            $item->User = User::find($item->UserID)->FullName;
            return $item;
        });
        $this->data['task'] = $task;
        $file_list = collect(File::files($path))->transform(function ($file) {
            return $file->getExtension();
        });
        $this->data['count_file'] = count($file_list);
        $this->data['type_file'] = $file_list->unique()->values();
        $this->data['userPost'] = User::query()->select("id", "FullName")->whereIn("id", $task->documents()->pluck("UserID")->unique()->toArray())->get();
        if ($task->EndDate != null && ($task->Status == 1 || $task->Status == 2)) {
            $diff = Carbon::createFromFormat(self::FOMAT_DB_YMD, $task->EndDate)->diffInDays(Carbon::now(), false);
            if ($diff > 0) {
                $text_diff = "Quá hạn ${diff} ngày";
                $text_class = "text-danger";
            } else if ($diff < 0) {
                $diff *= -1;
                $text_diff = "Còn ${diff} ngày";
                if ($diff >= $this->deadline) {
                    $text_class = "text-success";
                } elseif ($diff >= $this->deadline / 2 && $diff < $this->deadline) {
                    $text_class = "text-warning";
                } else {
                    $text_class = "text-danger";
                }
            } else {
                $text_diff = "Hết hạn trong hôm nay";
                $text_class = "text-danger";
            }
            $this->data['text_diff'] = $text_diff;
            $this->data['text_class'] = $text_class;
        }
        return $this->viewAdminIncludes("taskwork.task-working-main", $this->data);
    }

    // Get all document of 1 task
    public function getAllDoc(Request $request, $id): JsonResponse
    {
        $task = WorkTask::find($id);
        $path_store = $this->path_store_file . $id;
        try {
            $this->authorize('view-task', $task);
            $list_doc = collect(File::files($path_store))
                ->sortByDesc(function ($file) {
                    return $file->getCTime();
                })
                ->map(function ($file) use ($id) {
                    $file->downloadLink = asset($this->path_download_file . $id . "/" . $file->getFilename());
                    $file->fileName = $file->getFilename();
                    $file->type = $file->getExtension();
                    $file->size = $file->getSize();
                    $file->lastModify = Carbon::parse($file->getMTime())->diffForHumans(Carbon::now());
                    $file->timeCreate = Carbon::parse($file->getCTime())->format(self::FOMAT_DISPLAY_DMY);
                    $user_id = WorkTaskDocument::where('WorkTaskID', $id)
                        ->where('DocName', 'like', '%' . $file->fileName . '%')->pluck('UserID')->first();
                    $user = User::find($user_id);
                    $file->userPost = $user ? $user->id : null;
                    return $file;
                })
                ->filter(function ($file) use ($request) {
                    if ($request->filled('file')) {
                        if (stristr($file->fileName, $request->get('file'))) {
                            return $file;
                        }
                    } else
                        return $file;
                })
                ->filter(function ($file) use ($request) {
                    if ($request->filled('type')) {
                        if ($file->type == $request->get('type')) {
                            return $file;
                        }
                    } else
                        return $file;
                })
                ->filter(function ($file) use ($request) {
                    if ($request->filled('userPost')) {
                        if ($file->userPost == $request->get('userPost')) {
                            return $file;
                        }
                    } else
                        return $file;
                })
                ->filter(function ($file) use ($request) {
                    if ($request->filled('date')) {
                        $time_create = Carbon::parse($file->getCTime());
                        switch ($request->get('date')) {
                            case "1-week":
                                return Carbon::now()->week() == $time_create->week();
                            case "1-month":
                                return Carbon::now()->format("Y-m") == $time_create->format("Y-m");
                            case "3-month":
                                return Carbon::now()->subMonths(3) <= $time_create;
                            case "1-year":
                                return Carbon::now()->format("Y") == $time_create->format("Y");
                        }
                    } else {
                        return $file;
                    }
                })
                ->values();
            return response()->json($list_doc, 200);
        } catch (AuthorizationException | FileException $exception) {
            return response()->json($exception->getMessage(), $exception instanceof AuthorizationException ? 403 : 400);
        }
    }
}
