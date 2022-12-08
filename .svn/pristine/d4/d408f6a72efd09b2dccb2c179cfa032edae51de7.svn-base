<?php

namespace Modules\ProjectManager\Http\Controllers;

use App\DailyReport;
use App\Http\Controllers\Admin\AdminController;
use App\Menu;
use App\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\ProjectManager\Entities\Project; 
use Modules\ProjectManager\Http\Services\CommonService;
use Modules\ProjectManager\Http\Services\DailyReportService;
use Modules\ProjectManager\Http\Services\JobService;
use Modules\ProjectManager\Http\Services\PhaseService;
use Modules\ProjectManager\Http\Services\ProjectService;
use Modules\ProjectManager\Http\Services\TaskService;

class TaskController extends AdminController
{
    public $projectService;
    public $taskService;
    public $phaseService;
    public $commonService;
    public $jobService;
    public $dailyReportService;
    const STATUS = ["not_working" => 1, "working" => 2, "review" => 3, "finish" => 4];
    private const KEY_STATUS = ['TodoTask' => 1, 'TaskWorking' => 2, 'TaskReview' => 3, 'TaskFinish' => 4];
    private const KEY_MESS = [1 => "Chưa thực hiện", 2 => "Đang thực hiện", 3 => "Đang duyệt", 4 => "Hoàn thành"];
    private const SCREEN_NAME = 'Tasks';
    private $deadline = 4;
    private $path_download_file = "storage/app/public/files/shares/Task/";
    private $path_store_file = "";


    public function __construct(
        Request $request,
        ProjectService $projectService,
        PhaseService $phaseService,
        JobService $jobService,
        TaskService $taskService,
        CommonService $commonService,
        DailyReportService $dailyReportService
    ) {

        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }

        $this->projectService = $projectService;
        $this->phaseService = $phaseService;
        $this->projectService = $projectService;
        $this->taskService = $taskService;
        $this->dailyReportService = $dailyReportService;
        $this->commonService = $commonService;
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
            $project =  $this->projectService->getById($request);
            if (!$project) {
                return abort('404');
            }
            $permissions = $this->taskService->getPermissions($request);
            if ($permissions['create']) {
                $this->data['allUsers'] = $project->users;
            }
            $this->data['permissions'] = $permissions;
            $this->data['project'] = $project;
            $this->data['request'] = $request->all();
            return view('projectmanager::tasks', $this->data);
        }
    }

    public function getAllTasks(Request $request)
    {
        $permissions = $this->taskService->getPermissions($request);
        $tasks = $this->taskService->getTasks($request);
        foreach ($tasks as $key => $task) {
        //     if ((!$task->UserId && $task->Status != 4) || !$task->StartDate) {
        //         $task->Status = 1;
        //         $task->save();
        //     }
            $task->startTime = $this->commonService->formatDatetime($task->StartDate);
            $task->endTime = $this->commonService->formatDatetime($task->EndDate);
        }

        $tasksOrderByPhase = $this->taskService->getOrderBy($request,'pOrder');
        $tasksOrderByJob = $this->taskService->getOrderBy($request,'jOrder');
        foreach ($tasks as $task){
            foreach ($tasksOrderByPhase as $key => $value) {
                if($task->id == $value->id){
                    $task->tpOrder = $value->tpOrder;
                }
            }
            foreach ($tasksOrderByJob as $key => $item) {
                if($task->id == $item->id){
                    $task->tjOrder = $item->tjOrder;
                }
            }
        }
        if($request->Status){
            $tasks = $tasks->where('Status',$request->Status);
        }
        
        return AdminController::responseApi(200, null, true, ['tasks' => $tasks, 'role_key' => 'Tasks', 'permissions' => $permissions]);
    }

    public function getTasks(Request $request)
    {
        $tasks = $this->taskService->getTasks($request);
        return AdminController::responseApi(200, null, true, $tasks);
    }

    public function getDoingTasks(Request $request)
    {
        $tasks = $this->taskService->getDoingTask($request);
        if ($tasks) {
            return AdminController::responseApi(200, null, true, ['tasks' => $tasks, 'role_key' => 'Tasks']);
        }
        return AdminController::responseApi(200, 'Không lấy được dữ liệu', false, ['tasks' => $tasks, 'role_key' => 'Tasks']);
    }

    public function showTaskForm(Request $request)
    {
        $permissions = $this->taskService->getPermissions($request);
        if (!$permissions['create']) {
            return AdminController::responseApi(403, "Bạn không có quyền thực hiện hành động này!", false, null);
        }
        if ($request->taskId) {
            $task = $this->taskService->getById($request);

            if ($task->StartDate) {
                $task->StartDate = date_format(DateTime::createFromFormat('Y-m-d H:i:s', $task->StartDate), 'd/m/Y H:i') ?? null;
            }
            if ($task->EndDate) {
                $task->EndDate = date_format(DateTime::createFromFormat('Y-m-d H:i:s', $task->EndDate), 'd/m/Y H:i') ?? null;
            }
            if ($request->issue == 'reschedule') {
                $task->StartDate = $task->EndDate;
                $task->EndDate = null;
                $task->Duration = null;
            }
            $this->data['taskInfo'] = $task;
            $this->data['project'] = $task->project;
            $this->data['selectTaskTypes'] = isset($task->phase) ? $task->phase->taskTypes : null;
            $this->data['selectMembers'] = $task->project->members;
            $this->data['issue'] = $request->issue;
        } else {
            $project = $this->projectService->getById($request);
            $this->data['project'] = $project;
            $this->data['selectTaskTypes'] = null;
            $this->data['selectMembers'] = $project->members;
            $this->data['searchKeys'] = $request->searchKeys;
            $this->data['issue'] = "create";
        }
        $this->data['request'] = $request->all();
        return view('projectmanager::modal.task-form', $this->data);
    }

    public function showTaskDetail(Request $request)
    {
        $this->data['permissions'] = $this->taskService->getPermissions($request);
        $task = $this->taskService->getById($request);
        $otDuration = 0;
        foreach ($task->OT as $item) {
            $otDuration += Carbon::parse($item->STime)->diffInSeconds(Carbon::parse($item->ETime)) / 3600 - $item->BreakTime;
        }
        $task->workedHours = $task->dailyReports->sum('WorkingTime');
        $task->OTDuration = round($otDuration, 2);
        $today = Carbon::now()->format('Y-m-d H:i:s');
        if ($task->EndDate < $today) {
            $task->outdated = 1;
        }
        $this->data['task'] = $task;
        return view('projectmanager::modal.task-detail', $this->data);
    }

    public function store(Request $request)
    {
        $projectEndDate = Project::where('id', $request->projectId)->pluck('EndDate')->first();
        if ($projectEndDate) {
            $projectEndDate = Carbon::createFromFormat('Y-m-d', $projectEndDate);
        }
        if (count($request->input()) === 0) {
            return abort('404');
        }
        $validateData = $request->all();
        $allTaskValidated = true;
        $arrCheck = [
            'name' => [
                'required',
                'string',
                'max:300',
                Rule::unique('t_tasks', 'name')
                    ->ignore($request->taskId, 'id')
                    ->where('ProjectId', $request->projectId)
                    ->whereNull('deleted_at')
            ],
            'description' => 'nullable|string',
            'startDate' => 'nullable|date_format:"d/m/Y H:i',
            'endDate' => 'nullable|date_format:"d/m/Y H:i',
            'duration' => 'nullable|numeric|gte:0',
            'members' => 'nullable|array',
            'note' => 'nullable|string|max:100',
            'Tags' => 'nullable|string|max:100',
        ];
        if ($request->phaseId) {
            $arrCheck['type'] = 'nullable';
        }
        // if ($request->members[0] != null) {
        //     $arrCheck['startDate'] = 'required|date_format:"d/m/Y H:i"';
        //     $arrCheck['endDate'] = 'required|date_format:"d/m/Y H:i"';
        // }
        $modeIsUpdate = array_key_exists('taskId', $request->input());
        if ($modeIsUpdate) {
            $arrCheck['id'] = 'integer|min:1|nullable';
        }
        foreach ($request->name as $key => $item) {
            if($request->taskId && $request->taskId[$key] && !$request->members[$key] && $request->taskStatus[$key] > 1){
                return $this->jsonErrors('Task đang thực hiện không thể bỏ Nhân viên thực hiện!');
            }
            $validateData['name'] = $item;
            $validateData['description'] = $request->description[$key] ?? null;
            $validateData['duration'] = $request->duration[$key] ?? null;
            $validateData['type'] = $request->type[$key] ?? null;
            $validateData['startDate'] = $request->startDate[$key] ?? null;
            $validateData['endDate'] = $request->endDate[$key] ?? null;
            $validateData['note'] = $request->note[$key] ?? null;
            $validateData['Tags'] = $request->Tags[$key] ?? null;
            $validator = Validator::make($validateData, $arrCheck,[
                'name.required' => 'Vui lòng điền tên Task!',
                // 'startDate.required' => 'Vui lòng điền tên Thời gian bắt đầu Task!',
                // 'endDate.required' => 'Vui lòng điền Thời gian kết thúc Task!'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()->first()
                ]);
                $allTaskValidated = false;
            } else {
                $validated = $validator->validate();
                if (isset($validated['endDate'])) {
                    $taskStartDate = $validated['startDate'] ? Carbon::createFromFormat('d/m/Y H:i', $validated['startDate']) : null;
                    $taskEndDate = Carbon::createFromFormat('d/m/Y H:i', $validated['endDate']);
                    if ($taskStartDate && $taskEndDate < $taskStartDate) {
                        return $this->jsonErrors('Ngày bắt đầu không lớn hơn ngày kết thúc!');
                    } elseif ($projectEndDate && $taskEndDate > $projectEndDate) {
                        return $this->jsonErrors('Ngày kết thúc Task vượt quá thời hạn của Dự án. Ngày kết thúc dự án là: ' . $projectEndDate->format('d/m/Y'));
                    }
                }
            }
        }
        if ($allTaskValidated){
            $task = $this->taskService->store($request);
        }
        if($task->wasRecentlyCreated){
            return AdminController::responseApi('200',null,true, ["mes" => "Đã thêm một Task mới!"]);
        }else{
            return AdminController::responseApi('200',null,true, ["mes" => "Đã cập nhật Task  thành công!"]);
        }
    }

    public function changeStatus(Request $request): JsonResponse
    {
        // if(!$permissions['create']){
            // return AdminController::responseApi(403, "Bạn không có quyền thực hiện hành động này!", false, null);
        // }
        $response = $this->taskService->changeStatus($request, self::KEY_STATUS, self::SCREEN_NAME);
        return response()->json([
            'success' => $response['success'],
            'error' => $response['errorMes'],
            'mes' => isset($response['data']) ? $response['data']['messages'] ?? null : null
        ]);
    }

    public function openReportTaskModal(Request $request)
    {
        $task = $this->taskService->getById($request);
        $this->data['request'] = $request->all();
        $this->data['task'] = $task;
        return view('projectmanager::modal.task-report', $this->data);
    }

    public function report(Request $request)
    {
                
        if (count($request->input()) === 0) {
            return abort('404');
        }
        if (!$request->Date || $request->Date == '') {
            return response()->json([
                'success' => false,
                'mes' => 'Vui lòng chọn Ngày làm việc!'
            ]);
        }

        if (is_null($request->projectId) || empty($request->projectId)) {
            return response()->json([
                'success' => false,
                'mes' => 'ID task công việc không hợp lệ!'
            ]);
        }
        
        $timeRequest = $this->fncDateTimeConvertFomat($request->Date, 'd/m/Y', self::FOMAT_DB_YMD);
        $task = $this->taskService->getById($request);

        if ($task->UserId == null) {
            return response()->json([
                'success' => false,
                'mes' => 'Không thể báo cáo được do task chưa có người thực hiện!'
            ]);
        }


        if (!is_null($task->project->EndDate) && Carbon::parse($timeRequest)->gt($task->project->EndDate)) {
            return response()->json([
                'success' => false,
                'mes' => 'Dự án đã kết thúc, không thể báo cáo được!'
            ]);
        }

        // if (auth()->user()->role_group != 2) {
        //     if (Carbon::parse($timeRequest)->gt(Carbon::parse($task->EndDate))) {
        //         return response()->json([
        //             'success' => false,
        //             'mes' => 'Thời gian báo cáo không hợp lệ!'
        //         ]);
        //     }
        //     if (Carbon::now()->subDays(4)->gt(Carbon::parse($timeRequest))) {
        //         return response()->json([
        //             'success' => false,
        //             'mes' => 'Không thể báo cáo được do đã quá hạn 3 ngày!'
        //         ]);
        //     }
        // }

        $validateData = $request->except('WorkedTime','type');
        $arrCheck = [
            'Date' => 'required|date_format:d/m/Y',
            'ScreenName' => 'required|string|max:100',
            'Contents' => 'required|string|max:255',
            'WorkingTime' => 'required|numeric|max:10|gt:0',
            'Progressing' => 'required|numeric|gt:0',
            'Delay' => 'nullable|numeric|max:100|gte:0',
            'Soon' => 'nullable|numeric|max:100|gte:0',
            'Note' => 'nullable|string|max:100',
        ];

        $modeIsUpdate = array_key_exists('taskId', $request->input());
        if ($modeIsUpdate) {
            $arrCheck['id'] = 'integer|min:1|nullable';
        }
        $validator = Validator::make($validateData, $arrCheck);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'mes' => $validator->errors()->first()
            ]);
        } else {
            $validateData['UserId']= $task->UserId;
            return $this->taskService->report($validateData,$task);
        }
    }

    public function openErrorTaskReport(Request $request)
    {
        $permissions = $this->taskService->getPermissions($request);
        if (!$permissions['review']) {
            return AdminController::responseApi(403, "Bạn không có quyền thực hiện hành động này!", false, null);
        }
        $task = $this->taskService->getById($request);
        $this->data['request'] = $request->all();
        $this->data['task'] = $task;
        $this->data['last_report'] = $task->lastReport;
        return view('projectmanager::modal.task-error', $this->data);
    }

    public function reportErrorTask(Request $request)
    {
        $task = $this->taskService->getById($request);
        if (count($request->input()) === 0) {
            return abort('404');
        }

        $validateData = $request->all();
        $arrCheck = [
            'Progressing'   => 'required',
            'IssuedTime'    => 'required|date_format:d/m/Y H:i',
            'Content'       => 'nullable|string',
            'Note'          => 'nullable|string',
        ];

        $validateData['Progressing'] = $request->Progressing;
        $validateData['IssuedTime'] = $request->IssuedTime;
        $validateData['Content'] = $request->Content;
        $validateData['Note'] = $request->Note;
        $validator = Validator::make($validateData, $arrCheck,[
            'IssuedTime.required' => 'Vui lòng nhập thời gian báo lỗi!',
            'IssuedTime.date_format' => 'Thời gian báo lỗi không đúng định dạng!'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'mes' => $validator->errors()->first()
            ]);
        } else {
            return $this->taskService->reportError($task,$validateData);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        $response = $this->taskService->delete($request, self::KEY_STATUS, self::SCREEN_NAME);
        return AdminController::responseApi(
            $response['statusCode'],
            $response['errorMes'],
            $response['success'],
            $response['data']
        );
    }

    public function getEndTime(Request $request)
    {
        $time = $this->taskService->getEndTime($request);
        $startTime = $time['startTime']->format('d/m/Y H:i');
        $endTime = $time['endTime']->format('d/m/Y H:i');
        return AdminController::responseApi(200, null, true, ['startTime' => $startTime, 'endTime' => $endTime]);
    }
}
