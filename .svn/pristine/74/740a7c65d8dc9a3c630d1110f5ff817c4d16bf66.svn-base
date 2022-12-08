<?php

namespace Modules\ProjectManager\Http\Services;

use App\Http\Controllers\Admin\AdminController;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\ProjectManager\Entities\Job;
use Modules\ProjectManager\Http\Repositories\DetailRepo;
use Modules\ProjectManager\Http\Repositories\jobRepo;
use Modules\ProjectManager\Http\Repositories\ProjectUserRepo;

class JobService extends AdminController
{
    public $jobRepo;
    public $detailRepo;
    public $projectUserRepo;
    public $commonService;

    public function __construct(
        JobRepo $jobRepo,
        DetailRepo $detailRepo,
        ProjectUserRepo $projectUserRepo,
        CommonService $commonService
    ) {
        $this->jobRepo = $jobRepo;
        $this->detailRepo = $detailRepo;
        $this->projectUserRepo = $projectUserRepo;
        $this->commonService = $commonService;
    }

    public function getById($request)
    {
        return $this->jobRepo->getById($request->jobId);
    }

    public function delete($id)
    {
        return $this->jobRepo->delete($id);
    }

    public function store($request)
    {
        $today = Carbon::now()->toDateString();
        $projectId = $request['projectId'];
        $data = [
            'id' => $request['id'],
            'project_id' => $projectId,
            'name' => $request['Name'],
            'description' => $request['Description'],
            'active' => $request['Active'],
            'color' => $request['color'] ?? '#000000',
            'start_date' => $request['StartDate'] ?? null,
            'end_date' => $request['EndDate'] ?? null,
        ];
        if ($request['StartDate'] !== null && $request['StartDate'] !== '') {
            $data['start_date'] = $this->fncDateTimeConvertFomat($request['StartDate'], 'd/m/Y', self::FOMAT_DB_YMD);
        }
        if ($request['EndDate'] !== null && $request['EndDate'] !== '') {
            $data['end_date'] = $this->fncDateTimeConvertFomat($request['EndDate'], 'd/m/Y', self::FOMAT_DB_YMD);
        }

        DB::beginTransaction();
        try {
            $job = $this->jobRepo->store($data);
            $this->reorder($job->project_id);
            DB::commit();
            return $job;
        } catch (Exception $e) {
            DB::rollBack();
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    public function reorder($projectId){
        $jobs = Job::   query()
                        ->where('project_id',$projectId)
                        ->select('order','id')
                        ->orderByRaw('  CASE
                                        WHEN start_date IS NULL THEN 1 ELSE 0 END, start_date')
                        ->orderBy('created_at')                
                        ->get();
        foreach($jobs as $key =>$job){
            $job->update(['order'=>$key +1 ]);
        }                    
    }

    public function getMembers($request){
        $job = $this->jobRepo->getById($request->jobId);
        $totalDuration = $job->tasks->sum('Duration');
        $totalTasks = count($job->tasks);
        $generalProgress = $this->commonService->calculateProgress($job->tasks,$totalDuration);
        $generalTodoTasks = 0;
        $generalDoingTasks = 0;
        $generalReviewTasks = 0;
        $generalDoneTasks = 0;
        foreach ($job->members as $key => $member) {
            $memberTotalDuration = $member->tasks->sum('Duration');
            $member->personalProgress = $this->commonService->calculateProgress($member->tasks,$memberTotalDuration);
            $member->generalProgress = $this->commonService->calculateProgress($member->tasks,$totalDuration);
            $generalTodoTasks += count($member->todoTasks);
            $generalDoingTasks += count($member->doingTasks);
            $generalReviewTasks += count($member->reviewTasks);
            $generalDoneTasks += count($member->doneTasks);
        }

        return [
            'project' => $job->project_id,
            'members' => $job->members,
            'totalTasks' => $totalTasks,
            'genaralProgress' => $generalProgress,
            'generalTodoTasks' => $generalTodoTasks,
            'generalDoingTasks' => $generalDoingTasks,
            'generalReviewTasks' => $generalReviewTasks,
            'generalDoneTasks' => $generalDoneTasks,
            'workType' => 'job',
            'workName' => $job->name
        ];
    }
}
