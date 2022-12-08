<?php

namespace Modules\ProjectManager\Http\Controllers;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use Modules\ProjectManager\Http\Services\CommonService;
use Modules\ProjectManager\Http\Services\JobService;
use Modules\ProjectManager\Http\Services\PhaseService;
use Modules\ProjectManager\Http\Services\ProjectService;
use Modules\ProjectManager\Http\Services\UserService;

class CommonController extends AdminController
{
    public $projectService;
    public $userService;
    public $phaseService;
    public $commonService;
    public $jobService;
    public $phaseRepo;

    public function __construct(
        ProjectService $projectService,
        PhaseService $phaseService,
        JobService $jobService,
        CommonService $commonService,
        UserService $userService
    ) {
        $this->projectService = $projectService;
        $this->phaseService = $phaseService;
        $this->jobService = $jobService;
        $this->commonService = $commonService;
        $this->userService = $userService;
    }

    public function showDetail(Request $request)
    {
        $this->data['users'] = $this->commonService->getUsers();
        $this->data['phaseTypes'] = $this->commonService->getPhaseTypes();
        $projectId = $request->projectId ?? null;

        $action = $request->action ?? null;

        //show form create Phase/Job
        if ($action) {
            return $this->showCreateForm($action, $request, $this->data);
        }

        $phaseId = $request->phaseId ?? null;
        $jobId = $request->jobId ?? null;

        if ($jobId) {
            $objId = $jobId;
            $service = $this->jobService;
            $objName = 'job';
            $project = $this->projectService->getById($request);
            $this->data['project'] = $project;
            $this->data['phases'] = $project->phases;
            $view = 'projectmanager::modal.job-detail';
        } else if ($phaseId) {
            $objId = $phaseId;
            $service = $this->phaseService;
            $objName = 'phase';
            $project = $this->projectService->getById($request);
            $this->data['project'] = $project;
            $view = 'projectmanager::modal.phase-detail';
        } else {
            $objId = $projectId;
            $service = $this->projectService;
            $objName = 'project';
            $view = 'projectmanager::modal.project-detail';
        }

        //show form create Project
        if (!$objId) return view($view, $this->data);

        if ($request->del == 'del') {
            $response = $service->delete($objId);
            if($response['deleted']){
                if($objName != 'project'){
                    $service->reorder($projectId);
                }
                if ($request->ajax()) {
                    return response()->json([
                        'success' => $response['deleted'],
                        'mes' => $response['mes']
                    ]);
                }
                return 1;
            }else{
                return response()->json([
                    'success' => $response['deleted'],
                    'mes' => $response['mes']
                ]);
            }
        }
        $obj = $service->getById($request);
        if (!$obj) return "";
        $this->data[$objName] = $obj;
        return view($view, $this->data);
    }

    public function showCreateForm($action, $request, $data)
    {
        $project = $this->projectService->getById($request);
        $phases = $project->phases;
        if ($project) {
            $data['project'] = $project;
            $data['phases'] = $phases;
            $view = $action === "createPhase"
                ? 'projectmanager::modal.phase-detail'
                : 'projectmanager::modal.job-detail';
            return view($view, $data);
        } else {
            return view('projectmanager::modal.project-detail', $data);
        }
    }

    public function showMembers(Request $request)
    {
        if ($request->projectId) {
            $this->data = $this->projectService->getMembers($request);
        } else {
            if ($request->phaseId) {
                $this->data = $this->phaseService->getMembers($request);
            } else {
                $this->data = $this->jobService->getMembers($request);
            }
            $request->projectId = $this->data['project'];
            $this->data['project'] = $this->projectService->getProgress($request);
        }
        return view('projectmanager::modal.project-members', $this->data);
    }
}
