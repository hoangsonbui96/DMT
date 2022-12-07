<?php

namespace Modules\ProjectManager\Http\Services;

use App\DailyReport;
use App\Exports\DailyReportExport;
use App\Http\Controllers\Admin\AdminController;
use App\Members;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use GuzzleHttp\Psr7\Message;
use Illuminate\Support\Facades\DB;
use Modules\ProjectManager\Entities\Job;
use Modules\ProjectManager\Entities\Phase;
use Modules\ProjectManager\Entities\Project;
use Modules\ProjectManager\Entities\Task;
use Modules\ProjectManager\Entities\TaskIssue;
use Modules\ProjectManager\Http\Repositories\CalendarRepo;
use Modules\ProjectManager\Http\Repositories\TaskRepo;
use Modules\ProjectManager\Http\Repositories\UserRepo;

class TaskService extends AdminController
{
    public $userRepo;
    public $taskRepo;
    public $commonService;
    public $calendarRepo;
    public $permissions = [
        'create' => false,
        'edit' => false,
        'reschedule' => false,
        'createChildTask' => false,
        'delete' => false,
        'review' => false
    ];

    public function __construct(
        TaskRepo $taskRepo,
        CommonService $commonService,
        UserRepo $userRepo,
        CalendarRepo $calendarRepo,
        DailyReportService $dailyReportService
    )
    {
        $this->taskRepo = $taskRepo;
        $this->commonService = $commonService;
        $this->dailyReportService = $dailyReportService;
        $this->userRepo = $userRepo;
        $this->calendarRepo = $calendarRepo;
    }

    public function setDataQuery($request)
    {
        $currentUser = auth()->user();
        $permissions = $this->getPermissions($request);
        $data = $request->all();
        $data['projectId'] = $data['projectId'] ?? null;
        $data['taskUserIds'] = $request->taskUserIds ?? null;
        if (!$permissions['create']) {
            $data['taskUserIds'][] = $currentUser->id;
        }
        $data['taskId'] = $request->taskId ?? null;
        $data['status'] = $request->Status ?? null;
        $data['keyword'] = $request->Keywords ?? null;
        $data['phaseId'] = $request->phaseId > 0 ? $request->phaseId : null;
        $data['jobId'] = $request->jobId > 0 ? $request->jobId : null;
        $data['startTime'] = $this->commonService->formatDatetime($request->startTime) ?? null;
        $data['endTime'] = $this->commonService->formatDatetime($request->endTime) ?? null;
        $data['orderBy'] = $request->OrderBy ?? 'PhaseId';
        return $data;
    }

    public function getPermissions($request)
    {
        $project = Project::with(['phases', 'leaders'])->find($request->projectId);
        if(!$project) abort(403);
        $currentUser = auth()->user();
        if($request->phaseId && $request->phaseId !=''){
            $phase = Phase::find($request->phaseId);
            if (!$phase) {
                return abort('404');
            }
            $phaseUserIds = $phase->members->pluck('id')->toArray();
            array_push($phaseUserIds,$phase->leaders ? $phase->leaders->id : null);
            if (!in_array($currentUser->id, $phaseUserIds) && $currentUser->role_group !== 2) {
                abort(403);
            }
        }
        if($request->jobId && $request->jobId !=''){
            $job = Job::find($request->jobId);
            if (!$job) {
                return abort('404');
            }
            if (!in_array($currentUser->id, $job->members->pluck('id')->toArray()) && $currentUser->role_group !== 2) {
                abort(403);
            }
        }
        $projectLeaderIds = array_map(function ($item) {
            return $item['id'];
        }, $project->leaders->toArray());
        if (in_array($currentUser->id, $projectLeaderIds) || $currentUser->role_group == 2) {
            $this->permissions['create'] = true;
            $this->permissions['edit'] = true;
            $this->permissions['reschedule'] = true;
            $this->permissions['createChildTask'] = true;
            $this->permissions['review'] = true;
            $this->permissions['delete'] = true;
        }
        return $this->permissions;
    }

    public function getTasks($request)
    {
        $data = $this->setDataQuery($request);
        $data['projectId'] = $data['projectId'] ?? null;
        $tasks = $this->taskRepo->getTasks($data);
        return $tasks;
    }

    public function getById($request)
    {
        $data = $this->setDataQuery($request);
        $task = $this->taskRepo->getById($data);
        return $task;
    }

    public function getDoingTask($request)
    {
        $data = $this->setDataQuery($request);
        $tasks = $this->taskRepo->getInvolvedTasks($data);
        foreach($tasks as $task){
            $task->StartDate = $this->commonService->formatDatetime($task->StartDate);
            $task->EndDate = $this->commonService->formatDatetime($task->EndDate);
        }
        return $tasks;
    }

    public function store($request)
    {
        $today = Carbon::now()->format('Y-m-d');
        $currentUser = auth()->user();
        $projectId = $request['projectId'];
        if ($request->taskId && $request->issue == 'reschedule') {
            $oldTask = Task::find($request->taskId);
        }
        DB::beginTransaction();
        try {
            foreach ($request->name as $key => $item) {
                if(isset($oldTask)){
                    $task = $oldTask->replicate();
                    $task->SubType = 'RS';
                    $task->Name = 'RS '.$oldTask->Name;
                    $task->ParentId = $oldTask->id;
                    $task->Duration = $request['duration'][$key] ?? null;
                    if ($request['startDate'][$key] !== null && $request['startDate'][$key] !== '') {
                        $task->StartDate = $this->commonService->formatDatetime($request['startDate'][$key]);
                    }else{
                        $task->StartDate = null;
                    }
                    if ($request['endDate'][$key] !== null && $request['endDate'][$key] !== '') {
                        $task->EndDate = $this->commonService->formatDatetime($request['endDate'][$key]);
                    }else{
                        $task->EndDate = null;
                    }
                    $task->Description = $request['description'][$key];
                    $task->Note = $request['note'][$key];
                    $task->Status = 1;
                    $task->save();
                    $oldTask->Status = 4;
                    $oldTask->save();
                    $dailyReport = new DailyReport();
                    $dailyReport->ProjectID = $oldTask->ProjectId;
                    $dailyReport->TaskID = $oldTask->id;
                    $dailyReport->UserID = $oldTask->UserId;
                    $dailyReport->DateCreate = $today;
                    $dailyReport->Progressing = 100;
                    $dailyReport->Contents = 'Gia hạn Task';
                    $dailyReport->TypeWork = 'BC009';
                    $dailyReport->save();
                    DB::commit();
                    return $task;
                }
                $data = [
                    'id'=> $request['taskId']?? null,
                    'ProjectId' => $projectId,
                    'Name' => $item,
                    'GiverId' => $currentUser->id,
                    'UserId' => $request['members'][$key],
                    'Duration' => $request['duration'] ? str_replace(',', '.', $request['duration'][$key]) : null,
                    'Description' => $request['description'][$key],
                    'Note' => $request['note'][$key],
                    'ParentId' => $request['parentTaskId'][$key] ?? null,
                    'SubType' => $request['subType'][$key] ?? null,
                ];
                $data['Type'] = $request['type'][$key] ?? null;
                $data['PhaseId'] = $request['phaseId'][$key];
                $data['JobId'] = $request['jobId'][$key];
                if ($request->Tags) {
                    $data['Tags'] = $request['Tags'][$key];
                }
                if ($request['startDate'][$key] !== null && $request['startDate'][$key] !== '') {
                    $data['StartDate'] = $this->commonService->formatDatetime($request['startDate'][$key]);
                }else{
                    $data['StartDate'] = null;
                }
                if ($request['endDate'][$key] !== null && $request['endDate'][$key] !== '') {
                    $data['EndDate'] = $this->commonService->formatDatetime($request['endDate'][$key]);
                }else{
                    $data['EndDate'] = null;
                }
                $task = $this->taskRepo->store($data);
            }
            DB::commit();
            return $task;
        } catch (Exception $e) {
            DB::rollBack();
            echo 'Có gì đó không đúng khi lưu Tasks: ', $e->getMessage(), "\n";
        }
    }

    public function changeStatus($request, $KEY_STATUS, $SCREEN_NAME)
    {
        try {
            // Thay đổi trạng thái của task
            if ($request->filled(["Status", "Items"])) {
                $errors = [];
                $arr_id = [];
                $item = $request->get("Items")[0];
                $to_status = $request->get('Status');
                //                foreach ($request->get("Items") as $index => $item) {
                $taskId = $request->Items;
                $data['taskId'] = $taskId[0];
                $task = $this->taskRepo->getById($data);
                // if (auth()->user()->cant("viewAll-task", $task->project->id) && Members::where('WorkTaskID', '=', $task->id)->pluck("UserID")->first() != auth()->id()) {
                //     array_push($errors, $task->Name);
                //     array_push($arr_id, $task->id);
                // } else {
                if (!$task->UserId) {
                    array_push($errors, $task->Name . ' vì chưa có người thực hiện');
                    array_push($arr_id, $task->id);
                } else {
                    switch ($task->Status) {
                        case $KEY_STATUS['TodoTask']:

                            if ($to_status == $KEY_STATUS['TaskReview'] || $to_status == $KEY_STATUS['TaskFinish']) {
                                array_push($errors, $task->Name);
                                array_push($arr_id, $task->id);
                            } else {

                                $task->update(["Status" => $to_status]);
                            }
                            break;
                        case $KEY_STATUS['TaskWorking']:
                            if ($to_status == $KEY_STATUS['TaskFinish']) {
                                array_push($errors, $task->Name);
                                array_push($arr_id, $task->id);
                            } elseif ($to_status == $KEY_STATUS['TodoTask']) {
                                // if (DailyReport::query()->where("TaskID", $item)->exists()) {
                                //     array_push($errors, $task->Name . ' vì đã có báo cáo');
                                //     array_push($arr_id, $task->id);
                                // } else {
                                $task->update(["Status" => $to_status]);
                                // }
                            } elseif ($to_status == $KEY_STATUS['TaskReview']) {
                                $task->update(["Status" => $to_status]);
                            }
                            break;
                        case $KEY_STATUS['TaskFinish']:
                            if (
                                $to_status == $KEY_STATUS['TodoTask']
                                || $to_status == $KEY_STATUS['TaskReview']
                            ) {
                                array_push($errors, $task->Name);
                                array_push($arr_id, $task->id);
                            }
                            // ve trang thai dang Thuc hien
                            break;
                        case $KEY_STATUS['TaskReview']:
                            // if (auth()->user()->cant('review-task', $task)) {
                            //     return [
                            //         'statusCode' => 403,
                            //         'errorMes' => "Bạn không có quyền thực hiện hành động này",
                            //         'success' => false,
                            //         'data' => ['role_key' => $SCREEN_NAME]
                            //     ];
                            // }
                            if ($to_status == $KEY_STATUS['TodoTask']) {
                                array_push($errors, $task->Name);
                                array_push($arr_id, $task->id);

                            } elseif ($to_status == $KEY_STATUS['TaskFinish']) {
                                $permissions = $this->getPermissions($request);
                                if (!$permissions['review']){
                                    array_push($errors, $task->Name. ': Không có quyền duyệt Task');
                                    array_push($arr_id, $task->id);
                                }else{
                                    $task->update([
                                        "Status" => $to_status,
                                    ]);
                                }
                            } else {
                                $task->update([
                                    "Status" => $to_status,
                                    "NumberReturn" => DB::raw('NumberReturn + 1')
                                ]);
                            }
                            break;
                        default:
                            return [
                                'statusCode' => 400,
                                'errorMes' => "Invalide request keys",
                                'success' => false,
                                'data' => ['role_key' => $SCREEN_NAME]
                            ];
                    }
                }
                // }
                //                }
                if (!empty($errors)) {
                    return [
                        'statusCode' => 422,
                        'errorMes' => "Không thể thay đổi trạng thái task",
                        'success' => false,
                        'data' => ['not_update' => count($errors), 'messages' => $errors, 'role_key' => $SCREEN_NAME, 'id' => $arr_id]
                    ];
                }

                // send email if task finished
                // if ($to_status == $KEY_STATUS['TaskReview'] || $to_status == $KEY_STATUS['TaskFinish']) {
                //     $content = [[
                //         'action' => 'update',
                //         'task' => $task,
                //         'messages' => ["chuyển sang trạng thái " . self::KEY_MESS[$to_status]]
                //     ]];
                //     $this->send(auth()->user(), $task->members()->first()->UserID, $task->project()->first()->id, $content);
                // }
                return [
                    'statusCode' => 200,
                    'errorMes' => null,
                    'success' => true,
                    'data' => ['role_key' => $SCREEN_NAME]
                ];
            }
            // Sắp xếp các task trong 1 cột
            // if ($request->filled("Positions")) {
            //     foreach ($request->get("Positions") as $index => $item) {
            //         optional(Task::find($item[0]))->update(["position" => $item[1]]);
            //     }
            //     return [
            //         'statusCode' => 200,
            //         'errorMes' => null,
            //         'success' => true,
            //         'data' => ['role_key' => $SCREEN_NAME]
            //     ];
            // }
            return [
                'statusCode' => 402,
                'errorMes' => "Your request is not enough keys",
                'success' => false,
                'data' => null
            ];
        } catch (\Exception $exception) {
            return [
                'statusCode' => 500,
                'errorMes' => $exception->getMessage(),
                'success' => false,
                'data' => null
            ];
        }
    }

    public function delete($request, $KEY_STATUS, $SCREEN_NAME)
    {
        // try {
        //     $this->authorize('action', $this->role_list['Delete']);
        // } catch (AuthorizationException $e) {
        //     return AdminController::responseApi(403, "Bạn không đủ quyền để thực hiện hành động này", false, ['role_key' => self::SCREEN_NAME]);
        // }
        // $rules = [
        //     'Items' => 'required|array',
        //     'Items.*' => 'string|min:1'
        // ];
        // $messages = [
        //     'Items.required' => 'Thiếu task cần xóa',
        //     'Items.array' => 'Sai định dạng',
        // ];
        // $validated = Validator::make($request->all(), $rules, $messages);
        // if ($validated->fails()) {
        //     return AdminController::responseApi(400, $validated->messages(), false);
        // }
        try {
            $arr_send = [];
            $items = $request->get("Items");
            $tasks = Task::query()->whereIn("id", $items)->get();
            $context = ['task' => null, 'action' => 'delete', 'messages' => ''];
            foreach ($tasks as $task) {
                $member = !is_null($task->UserId) ? $task->UserId : null;
                $context['task'] = $task;
                if (array_key_exists($member, $arr_send)) {
                    array_push($arr_send[$member], $context);
                } else {
                    $arr_send[$member] = [$context];
                }
            }
            // DailyReport::query()->whereIn("TaskID", $items)->delete();
            // Members::query()->where("WorkTaskID", $items)->delete();
            $count = Task::destroy($items);
            // foreach ($arr_send as $member_id => $content) {
            //     if (!$this->send(auth()->user(), $member_id, $ProjectId, $content)) {
            //         continue;
            //     }
            // }
            return [
                'statusCode' => 200,
                'errorMes' => null,
                'success' => "Xóa thành công $count task.",
                'data' => null
            ];
        } catch (\Exception $exception) {
            return [
                'statusCode' => 500,
                'errorMes' => $exception->getMessage(),
                'success' => false,
                'data' => null
            ];
        }
    }

    public function getEndTime($request)
    {
        $user = $this->userRepo->getById($request);
        $workStartTime = $user->STimeOfDay;
        $workEndTime = $user->ETimeOfDay;
        $lunchStartTime = $user->SBreakOfDay;
        $lunchEndTime = $user->EBreakOfDay;
        $taskStartTime = $request->taskStartTime;
        $taskDuration = $request->taskDuration;
        $taskStartTime = $this->setTaskStartTime($taskStartTime, $workStartTime, $lunchStartTime, $lunchEndTime, $workEndTime);
        $tempTaskStartTime = clone $taskStartTime;
        $startDate = $taskStartTime->format('Y-m-d');
        $holidays = $this->getHolidays($startDate, null);
        $isRestDay = 1;
        while ($isRestDay == 1) {
            if (in_array($startDate, $holidays) || $taskStartTime->isWeekend()) {
                $taskStartTime->addDay(1);
                $startDate = $taskStartTime->format('Y-m-d');
            } else {
                $isRestDay = 0;
            }
        }
        $workHours = $this->getWorkHours($workStartTime, $workEndTime, $lunchStartTime, $lunchEndTime);
        $taskWorkDays = floor($taskDuration / $workHours); // days to finish Task
        $taskWorkHours = fmod($taskDuration, $workHours);    // above days plus hours to finish Task
        if ($taskWorkHours == 0 & $taskWorkDays > 0) {
            $taskWorkHours = $workHours;
            $taskWorkDays = $taskWorkDays - 1;
        }
        $taskWorkMinutes = $taskWorkHours * 60;
        $taskWorkSecond = $taskWorkMinutes * 60;
        while ($taskWorkDays > 0) {
            $taskStartTime->addDay(1);
            $startDate = $taskStartTime->format('Y-m-d');
            if (!in_array($startDate, $holidays) && !$taskStartTime->isWeekend()) {
                $taskWorkDays = $taskWorkDays - 1;
            }
        }
        $tempWorkStartTime = clone $taskStartTime;
        $this->setDateTime($tempWorkStartTime, $workStartTime);
        $tempWorkEndTime = clone $taskStartTime;
        $this->setDateTime($tempWorkEndTime, $workEndTime);
        $tempLunchStartTime = clone $taskStartTime;
        $this->setDateTime($tempLunchStartTime, $lunchStartTime);
        $tempLunchEndTime = clone $taskStartTime;
        $this->setDateTime($tempLunchEndTime, $lunchEndTime);

        $lunchTime = $tempLunchEndTime->timestamp - $tempLunchStartTime->timestamp;
        if ($taskStartTime <= $tempLunchStartTime && $tempLunchStartTime->timestamp - $taskStartTime->timestamp <= $taskWorkSecond) {
            $workTimeLeft = ($tempWorkEndTime->timestamp - $taskStartTime->timestamp) - $lunchTime; //in Second
        } else {
            $workTimeLeft = ($tempWorkEndTime->timestamp - $taskStartTime->timestamp);
        }
        if ($workTimeLeft < $taskWorkSecond) {
            $tempEndTime = clone $tempWorkStartTime;
            $taskWorkSecond = $taskWorkSecond - $workTimeLeft;
            $tempEndTime->addSecond($taskWorkSecond);
            if ($taskStartTime < $tempLunchStartTime && $tempEndTime > $tempLunchStartTime) {
                $tempEndTime->addSecond($lunchTime);
            }
            $tempEndTime->addDay(1);
            $tempLunchStartTime->addDay(1);
            if ($tempEndTime > $tempLunchStartTime) {
                $tempEndTime->addHour(1);
            }
        } else if ($workTimeLeft > $taskWorkSecond) {
            $tempEndTime = clone $taskStartTime;
            $tempEndTime->addSecond($taskWorkSecond);
            if ($taskStartTime < $tempLunchStartTime && $tempEndTime > $tempLunchStartTime) {
                $tempEndTime->addSecond($lunchTime);
            }
        } else {
            $tempEndTime = clone $tempWorkEndTime;
        }

        $isRestDay = 1;
        while ($isRestDay == 1) {
            $endDate = $tempEndTime->format('Y-m-d');
            if ($tempEndTime->isWeekend() || in_array($endDate, $holidays)) {
                $tempEndTime->addDay(1);
            } else {
                $isRestDay = 0;
            }
        }
        return [
            'startTime' => $tempTaskStartTime,
            'endTime' => $tempEndTime
        ];
    }

    public function getHolidays($startDate, $endDate)
    {
        $dates = [];
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        $holidays = $this->calendarRepo->get($data);
        foreach ($holidays as $holiday) {
            $period = CarbonPeriod::create($holiday->StartDate, $holiday->EndDate);
            foreach ($period as $date) {
                $date = $date->format('Y-m-d');
                array_push($dates, $date);
            }
        }
        return $dates;
    }

    function setDateTime($tempTime, $hours)
    {
        $hours = explode(':', $hours);
        $tempTime->setHour($hours[0]);
        $tempTime->setMinute($hours[1]);
    }

    public function setTaskStartTime($taskStartTime, $workStartTime, $lunchStartTime, $lunchEndTime, $workEndTime)
    {
        $arrTST = explode(' ', $taskStartTime);
        $tempWorkStartTime = implode(' ', [$arrTST[0], $workStartTime]);
        $tempLunchStartTime = implode(' ', [$arrTST[0], $lunchStartTime]);
        $tempLunchEndTime = implode(' ', [$arrTST[0], $lunchEndTime]);
        $tempWorkEndTime = implode(' ', [$arrTST[0], $workEndTime]);

        $tempWorkStartTime = Carbon::createFromFormat('d/m/Y H:i:s', $tempWorkStartTime);
        $tempWorkEndTime = Carbon::createFromFormat('d/m/Y H:i:s', $tempWorkEndTime);
        $tempLunchStartTime = Carbon::createFromFormat('d/m/Y H:i:s', $tempLunchStartTime);
        $tempLunchEndTime = Carbon::createFromFormat('d/m/Y H:i:s', $tempLunchEndTime);
        $taskStartTime = Carbon::createFromFormat('d/m/Y H:i', $taskStartTime);

        $lunchTimes = $tempLunchEndTime->timestamp - $tempLunchStartTime->timestamp;
        if ($taskStartTime->timestamp - $tempWorkStartTime->timestamp < 0) {
            $taskStartTime = $tempWorkStartTime;
        } elseif ($taskStartTime->timestamp - $tempLunchStartTime->timestamp <= $lunchTimes && $taskStartTime->timestamp - $tempLunchStartTime->timestamp >= 0) {
            $taskStartTime = $tempLunchEndTime;
        } elseif ($taskStartTime->timestamp - $tempWorkEndTime->timestamp >= 0) {
            $taskStartTime = $tempWorkStartTime;
            $taskStartTime->addDays(1);
        }
        return $taskStartTime;
    }

    public
    function getWorkHours($workStartTime, $workEndTime, $lunchStartTime, $lunchEndTime)
    {
        $arrWST = explode(':', $workStartTime);
        $arrWET = explode(':', $workEndTime);
        $arrLST = explode(':', $lunchStartTime);
        $arrLET = explode(':', $lunchEndTime);
        $workHours = round(
            (($arrWET[0] * 60 + $arrWET[1]) - ($arrWST[0] * 60 + $arrWST[1])) / 60
            -
            (($arrLET[0] * 60 + $arrLET[1]) - ($arrLST[0] * 60 + $arrLST[1])) / 60

        );
        return $workHours;
    }

    public function getOrderBy($request,$key){
        $data = $this->setDataQuery($request);
        $tasks = $this->taskRepo->getOrderBy($data,$key);
        if($key == 'pOrder'){
            $taskOrder = 'tpOrder';
        }else{
            $taskOrder = 'tjOrder';
        }
        $index = 0;
        $lastOrder = 1;
        foreach($tasks as $task) {
            if($task->$key != null && $task->$key != $lastOrder) {
                $index = 1;
                $lastOrder = $task->$key;
            } else {
                $index += 1;
            }

            if($task->$key != null) {
                $task->$taskOrder = $index;
            } else {
                $task->$taskOrder = null;
            }
        }
        return $tasks;
    }

    public function report($data,$task){
        DB::beginTransaction();
        try {
			$phase = Phase::find($task->PhaseId);
			$type = "BC007";
			
			if ($phase !== null) {
				switch ($phase->type) {
				  case "PHT001":
					$type = "BC008";
					break;
				  case "PHT002":
					$type = "BC002";
					break;
				  case "PHT003":
					$type = "BC003";
					break;
				  case "PHT005":
					$type = "BC005";
					break;
				  default:
					$type = "BC007";
				}
			}
			$data['TypeWork'] = $type;
	
            $this->dailyReportService->store($data);
            $task->WorkedTime = $task->WorkedTime + $data['WorkingTime'];
            $task->Progress = $data['Progressing'];
            if ($data['Progressing'] == 100) {
                $task->Status = 3;
            }
            $task->save();
            DB::commit();
            return response()->json([
                'success' => true,
                'mes' => 'Báo cáo thành công!'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'mes' => "Đã xảy ra sự cố, vui lòng thử lại sau!"
            ]);
        }
      
    }

    public function reportError($task,$validateData){
        $task->Progress = $validateData['Progressing'];
        $issue = new TaskIssue();
        $issue->task_id = $task->id;
        $issue->content = $validateData['Content'];
        $issue->issued_at = Carbon::createFromFormat('d/m/Y H:i', $validateData['IssuedTime']);;
        $issue->issued_by = auth()->id();
        $issue->note = $validateData['Note'];

        if ($validateData['Progressing'] < 100) {
            $task->Status = 2;
            $task->NumberReturn += 1;
        }
        DB::beginTransaction();
        try {
            $task->save();
            $issue -> save();
            DB::commit();
            return response()->json([
                'success' => true,
                'mes' => 'Báo lỗi thành công!'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'mes' => 'Báo lỗi không thành công!Vui lòng xem lại thông tin báo cáo!'
            ]);
        }

    }
}
