<?php

namespace Modules\ProjectManager\Http\Services;

use App\Http\Controllers\Admin\AdminController;
use Carbon\Carbon;
use Exception;
use http\Env\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\ProjectManager\Entities\Project;
use Modules\ProjectManager\Entities\User;
use Modules\ProjectManager\Http\Repositories\JobRepo;
use Modules\ProjectManager\Http\Repositories\PhaseRepo;
use Modules\ProjectManager\Http\Repositories\ProjectRepo;
use Modules\ProjectManager\Http\Repositories\ProjectUserRepo;
use Modules\ProjectManager\Http\Repositories\UserRepo;

class ProjectService extends AdminController
{
    public $projectRepo;
    public $projectUserRepo;
    public $userRepo;
    public $phaseRepo;
    public $jobRepo;
    public $commonService;
    public $permissions = [
        'create' => false,
        'edit' => false,
        'delete' => false,
    ];

    public function __construct(
        ProjectRepo $projectRepo,
        ProjectUserRepo $projectUserRepo,
        UserRepo $userRepo,
        PhaseRepo $phaseRepo,
        JobRepo $jobRepo,
        CommonService $commonService
    ) {
        $this->projectRepo = $projectRepo;
        $this->projectUserRepo = $projectUserRepo;
        $this->userRepo = $userRepo;
        $this->phaseRepo = $phaseRepo;
        $this->jobRepo = $jobRepo;
        $this->commonService = $commonService;
    }


    public function getPermissions()
    {
        $currentUser = auth()->user();
        if ($currentUser->role_group == 2) {
            $this->permissions['create'] = true;
            $this->permissions['edit'] = true;
            $this->permissions['delete'] = true;
        }
        return $this->permissions;
    }

    public function get($request)
    {
        return $this->projectRepo->get($request);
    }

    public function getAll($request)
    {
        $currentUser = auth()->user();
        $data = [];
        $data['startDate'] = null;
        $data['endDate'] = null;
        $data['export'] = null;
        if ($request['export']) {
            $data['export'] = true;
        }
        if (isset($request['Date'][0])) {
            $startDate = $request['Date'][0];
            $data['startDate'] = $this->fncDateTimeConvertFomat($startDate, 'd/m/Y', self::FOMAT_DB_YMD);
        }
        if (isset($request['Date'][1])) {
            $endDate = $request['Date'][1];
            $data['endDate'] = $this->fncDateTimeConvertFomat($endDate, 'd/m/Y', self::FOMAT_DB_YMD);
        }
        $data['searchKey'] = $request->search ?? null;
        $data['orderBy'] = $request->orderBy ?? null;
        $data['sortBy'] =  $request->sortBy ?? null;
        $data['userIds'] =  $request->userIds ?? null;
        if ($currentUser->role_group != 2) {
            if ($data['userIds'] == []) {
                $data['userIds'][] = $currentUser->id;
            } else {
                array_push($data['userIds'], $currentUser->id);
            }
        }


        $data['Active'] =  $request->Active ?? 0;

        $projects = $this->projectRepo->getAll($data);
        foreach ($projects as $key => $project) {
            $project->hasUnscheduledTask = false;
            foreach ($project->tasks as $task) {
                if (!$task->Duration || $task->Duration == 0){
                    $project->hasUnscheduledTask = true;
                    break;
                }
            }
            $totalDuration = $project->tasks->sum('Duration');
            $project->estimatedDuration = $totalDuration;
            $project->progress = $this->commonService->calculateProgress($project->tasks, $totalDuration);
            $otDuration = 0;
            foreach ($project->tasks as $task) {
                if ($task->SubType == 'OT') {
                    $otDuration += $task->Duration;
                }
            }
            $project->estimatedDuration = $totalDuration - $otDuration;
            $project->realDuration = $totalDuration;
            $project->workedHours = $project->tasks->sum('WorkedTime');
            $project->OTDuration = round($otDuration, 2);
        }
        return $projects;
    }

    public function getById($request)
    {
        $data = [
            'projectId' => $request->projectId,
            'StartDate' => isset($request->StartDate) ? $this->fncDateTimeConvertFomat($request->StartDate, 'd/m/Y', self::FOMAT_DB_YMD) : null,
            'EndDate' => isset($request->EndDate) ?  $this->fncDateTimeConvertFomat($request->EndDate, 'd/m/Y', self::FOMAT_DB_YMD) : null,
            'phaseId' => $request->phaseId ?? null,
            'jobId' => $request->jobId ?? null,
            'taskId' => $request->taskId ?? null,
            'taskUserIds' => $request->taskUserIds ?? null,
            'progressUserIds' => $request->progressUserIds ?? null,
            'keyWord' => $request->Keywords ?? null,
            'taskType' => $request->Choices ?? null,
            'tagSearch' => null,
        ];

        if (substr($data['keyWord'], 0, 1) === '#') {
            $data['tagSearch'] = substr($data['keyWord'], 1);
        }
        return $this->projectRepo->getById($data);
    }

    public function getTasks($request)
    {
        $data = [
            'projectId' => $request->projectId,
            'userId' => $request->userId
        ];
        $project = $this->projectRepo->getTasks($data);
        return $project;
    }

    public function getProgress($request)
    {
        $project = $this->getTasks($request);
        $totalDuration = $project->tasks->sum('Duration');
        $totalTasks = count($project->tasks);
        $progress = $this->commonService->calculateProgress($project->tasks, $totalDuration);
        return [
            'jobs' => $project->jobs,
            'tasks' => $project->tasks,
            'totalTasks' => $totalTasks,
            'progress' => $progress
        ];
    }

    public function delete($projectId)
    {
        DB::beginTransaction();
        try {
            $project = Project::find($projectId);
            $project->users()->detach();
            $this->projectRepo->delete($project);
            DB::commit();
            return ['deleted'=>true,'mes'=>'Đã xóa Dự án!'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['deleted'=>false,'mes'=>'Đã có lỗi, vui lòng kiểm tra và thử lại!'];
        }
    }

    public function store($request)
    {
        $today = Carbon::now()->toDateString();
        $dataUpdate = $request->only([
            'id',
            'NameVi',
            'NameJa',
            'NameEn',
            'NameShort',
            'Customer',
            'StartDate',
            'EndDate',
            'Description',
        ]);
        $dataUpdate['StartDate'] = $dataUpdate['StartDate']
            ? $this->fncDateTimeConvertFomat($dataUpdate['StartDate'], 'd/m/Y', self::FOMAT_DB_YMD)
            : null;
        $dataUpdate['EndDate'] = $dataUpdate['EndDate']
            ? $this->fncDateTimeConvertFomat($dataUpdate['EndDate'], 'd/m/Y', self::FOMAT_DB_YMD)
            : null;
        $dataUpdate['Active'] = $request['Active'] ? 1 : 0;
        DB::beginTransaction();
        try {
			$leaders = $request['Leader'] ?? [];
            $members = $request['Member'] ?? [];
            if($request->id){
                $project = Project::find($request->id);
                $inProjectUsers = array_column($project->users->toArray(), 'id');
                $inProjecLeaders = array_column($project->leaders->toArray(), 'id');
                $removedUsers = array_values(array_diff($inProjectUsers, array_merge($leaders, $members)));
                $doingTaskUsers = array_map(function ($item) use ($project) {
                    if ($item['Status'] != 4) {
                        return $item['UserId'];
                    }
                }, $project->tasks->toArray());
                $cantRemoveUsers = array_intersect($removedUsers,$doingTaskUsers);
                if(isset($cantRemoveUsers) && $cantRemoveUsers != []){
                    $names = User::whereIn('id',$cantRemoveUsers)->withTrashed()->pluck('FullName');
                    $names = $names->implode(',');
                    $response['mes'] = mb_convert_encoding("Không thể xóa thành viên: ".$names." ra khỏi dự án vì có Tasks đang thực hiện", 'UTF-8', 'UTF-8');
                    $response['success'] = false;
                    return $response;
                }
                foreach($cantRemoveUsers as $user){
                    if(in_array($user,$inProjecLeaders)){
                        array_push($leaders,$user);
                    }else{
                        array_push($members,$user);
                    }
                }
            }
            $leaderString = ','. implode(',',$leaders) .',';
            $memberString = ','. implode(',',$members) .',';
            $dataUpdate['Member'] = $memberString;
            $dataUpdate['Leader'] = $leaderString;
                
            $project = $this->projectRepo->store($dataUpdate);

            $syncUsers = [];
            foreach ($leaders as $leader) {
                $syncUsers[$leader] = ['is_leader' => 1, 'active' => 1, 'join_date' => $today];
            }
            if ($members) {
                foreach ($members as $member) {
                    if (!in_array($member, $leaders)) {
                        $syncUsers[$member] = ['is_leader' => null,'active' => 1, 'join_date' => $today];
                    }
                }
            }
            $project->users()->sync($syncUsers);
            DB::commit();

            if($project->wasRecentlyCreated){
                $response['mes'] = "<strong>Đã thêm một Dự án mới!</strong>";
            }else{
                $response['mes'] = "<strong>Đã cập nhật Dự án thành công!</strong>";
            }
            $response['success'] = true;
            return $response;
        } catch (Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['mes'] = "Đã có lỗi sảy ra, vui lòng thử lại sau!";
        }
    }

    public function getPhases($request)
    {
        $data['projectId'] = $request['projectId'];
        $data['userIds'] = $request['userIds'] ?? null;
        $data['phaseSearch'] = $request['phaseSearch'] ?? null;
        $data['phaseStartDate'] = (isset($request['phaseStartDate']) && $request['phaseStartDate'] != null) ? $this->fncDateTimeConvertFomat($request['phaseStartDate'], 'd/m/Y', self::FOMAT_DB_YMD) : null;
        $data['phaseEndDate'] = (isset($request['phaseEndDate']) &&  $request['phaseEndDate'] != null) ? $this->fncDateTimeConvertFomat($request['phaseEndDate'], 'd/m/Y', self::FOMAT_DB_YMD) : null;
        $phases = $this->phaseRepo->getAll($data);
        foreach ($phases as $phase) {
            $phaseDuration = $phase->tasks->sum('Duration');
            $projectDuration = $phase->project->tasks->sum('Duration');
            $phase->progress = $this->commonService->calculateProgress($phase->tasks, $phaseDuration);
            $phase->generalProgress = $this->commonService->calculateProgress($phase->tasks, $projectDuration);
            $phase->percentInProject = $projectDuration > 0 ? number_format($phaseDuration / $projectDuration * 100, 2) : 0;
        }
        return $phases;
    }

    public function getJobs($request)
    {
        $data['projectId'] = $request['projectId'];
        $data['userIds'] = $request['userIds'] ?? null;
        $data['jobSearch'] = $request['jobSearch'] ?? null;
        $data['jobStartDate'] = (isset($request['jobStartDate']) && $request['jobStartDate'] != null) ? $this->fncDateTimeConvertFomat($request['jobStartDate'], 'd/m/Y', self::FOMAT_DB_YMD) : null;
        $data['jobEndDate'] = (isset($request['jobEndDate']) &&  $request['jobEndDate'] != null) ? $this->fncDateTimeConvertFomat($request['jobEndDate'], 'd/m/Y', self::FOMAT_DB_YMD) : null;
        $jobs = $this->jobRepo->getAll($data);
        foreach ($jobs as $job) {
            $projectDuration = $job->project->tasks->sum('Duration');
            $jobDuration = $job->tasks->sum('Duration');
            $job->progress = $this->commonService->calculateProgress($job->tasks, $jobDuration);
            $job->generalProgress = $this->commonService->calculateProgress($job->tasks, $projectDuration);
            $job->percentInProject = $projectDuration > 0 ? number_format($jobDuration / $projectDuration * 100, 2) : 0;

        }
        return $jobs;
    }

    public function getMembers($request)
    {
        $project = $this->getById($request);
        $totalDuration = $project->tasks->sum('Duration');
        $totalTasks = count($project->tasks);
        $generalProgress = $this->commonService->calculateProgress($project->tasks, $totalDuration);

        foreach ($project->users as $key => $member) {
            $memberTotalDuration = $member->tasks->sum('Duration');
            $member->personalProgress = $this->commonService->calculateProgress($member->tasks, $memberTotalDuration);
            $member->generalProgress = $this->commonService->calculateProgress($member->tasks, $totalDuration);
        }
        return [
            'members' => $project->users,
            'totalTasks' => $totalTasks,
            'genaralProgress' => $generalProgress,
            'generalTodoTasks' => count($project->todoTasks),
            'generalDoingTasks' => count($project->doingTasks),
            'generalReviewTasks' => count($project->reviewTasks),
            'generalDoneTasks' => count($project->doneTasks),
            'workType' => 'Project',
            'workName' => $project->NameVi
        ];
    }
}
