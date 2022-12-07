<?php

namespace Modules\ProjectManager\Http\Services;

use App\Http\Controllers\Admin\AdminController;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\ProjectManager\Entities\Phase;
use Modules\ProjectManager\Http\Repositories\DetailRepo;
use Modules\ProjectManager\Http\Repositories\PhaseRepo;
use Modules\ProjectManager\Http\Repositories\ProjectUserRepo;

class PhaseService extends AdminController
{
    public $phaseRepo;
    public $detailRepo;
    public $projectUserRepo;
    public $commonService;

    public function __construct(
        PhaseRepo $phaseRepo,
        DetailRepo $detailRepo,
        ProjectUserRepo $projectUserRepo,
        CommonService $commonService
    ) {
        $this->phaseRepo = $phaseRepo;
        $this->detailRepo = $detailRepo;
        $this->projectUserRepo = $projectUserRepo;
        $this->commonService = $commonService;
    }

    public function getById($request)
    {
        return $this->phaseRepo->getById($request->phaseId);
    }

    public function delete($id)
    {
        return $this->phaseRepo->delete($id);
    }

    public function store($request)
    {
        $today = Carbon::now()->toDateString();
        $projectId = $request['projectId'];
        $data = [
            'id' => $request['id'],
            'project_id' => $projectId,
            'name' => $request['Name'],
            'leader_id' => $request['leader'],
            'type' => $request['type'],
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
            $phase = $this->phaseRepo->store($data);
            $this->reorder($phase->project_id);
            DB::commit();
            return $phase;
        } catch (Exception $e) {
            DB::rollBack();
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    public function reorder($projectId){
        $phases = Phase::   query()
                            ->where('project_id', $projectId)
                            ->select('order','id')
                            ->orderByRaw('  CASE
                                            WHEN start_date IS NULL THEN 1 ELSE 0 END, start_date')
                            ->orderBy('created_at')                
                            ->get();
        foreach($phases as $key=>$phase){
            $phase->update(['order'=>$key + 1]);
        }                    
    }
    
    public function getMembers($request){
        $phase = $this->phaseRepo->getById($request->phaseId);
        $totalDuration = $phase->tasks->sum('Duration');
        $totalTasks = count($phase->tasks);
        $generalProgress = $this->commonService->calculateProgress($phase->tasks,$totalDuration);
        $generalTodoTasks = 0;
        $generalDoingTasks = 0;
        $generalReviewTasks = 0;
        $generalDoneTasks = 0;
        foreach ($phase->members as $key => $member) {
            if($phase->leader_id === $member->id){
                $member->is_leader = true;
            }
            $memberTotalDuration = $member->tasks->sum('Duration');
            $member->personalProgress = $this->commonService->calculateProgress($member->tasks,$memberTotalDuration);
            $member->generalProgress = $this->commonService->calculateProgress($member->tasks,$totalDuration);
            $generalTodoTasks += count($member->todoTasks);
            $generalDoingTasks += count($member->doingTasks);
            $generalReviewTasks += count($member->reviewTasks);
            $generalDoneTasks += count($member->doneTasks);
        }

        return [
            'project' => $phase->project_id,
            'members' => $phase->members,
            'totalTasks' => $totalTasks,
            'genaralProgress' => $generalProgress,
            'generalTodoTasks' => $generalTodoTasks,
            'generalDoingTasks' => $generalDoingTasks,
            'generalReviewTasks' => $generalReviewTasks,
            'generalDoneTasks' => $generalDoneTasks,
            'workType' => 'phase',
            'workName' => $phase->name
        ];
    }
}
