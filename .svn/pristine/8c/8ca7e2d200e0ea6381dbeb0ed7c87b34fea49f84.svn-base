<?php

namespace Modules\ProjectManager\Http\Controllers;

use Illuminate\Http\Request;

trait TraitCommonController
{
    public function goToLastPage($projects, $query_array, $request)
    {
        if (array_key_exists('page', $query_array)) {
            if ($query_array['page'] > 1) {
                $query_array['page'] = $projects->lastPage();
                $query_string = http_build_query($query_array);
                $fullUrl  = $request->url() . '?' . $query_string;
                return redirect($fullUrl);
            }
        }
    }

    public function showDetail(Request $request)
    {
        $this->data['users'] = $this->commonService->getUsers();
        $this->data['phaseTypes'] = $this->commonService->getPhaseTypes();
        $this->data['priorities'] = $this->commonService->getPriorities();
        $projectId = $request->projectId ?? null;

        $action = $request->action ?? null;

        //show form create Phase/Job
        if ($action) {
            return $this->showCreateForm($action, $projectId, $this->data);
        }

        $phaseId = $request->phaseId ?? null;
        $jobId = $request->jobId ?? null;

        if ($jobId) {
            $objId = $jobId;
            $service = $this->jobService;
            $objName = 'job';
            $view = 'projectmanager::job-detail';
        } else if ($phaseId) {
            $objId = $phaseId;
            $service = $this->phaseService;
            $objName = 'phase';
            $view = 'projectmanager::phase-detail';
        } else {
            $objId = $projectId;
            $service = $this->projectService;
            $objName = 'project';
            $view = 'projectmanager::project-detail';
        }

        if ($objName != 'project') {
            $project = $this->projectService->getById($projectId);
            $this->data['project'] = $project;
        }

        //show form create Project
        if (!$objId) return view($view, $this->data);

        //show form edit 
        if ($request->del == 'del') {
            $service->delete($objId);
            if (strpos(\Request::getRequestUri(), 'api') !== false) {
                return response()->json(['success' => 'Xóa thành công.']);
            }
            return 1;
        }

        $obj = $service->getById($objId);
        if (!$obj) return "";

        $obj->leaderIds = array_map(function ($e) {
            return $e->id;
        }, $obj->leaders);

        $obj->memberIds = array_map(function ($e) {
            return $e->id;
        }, $obj->members);
        $this->data[$objName] = $obj;
        return view($view, $this->data);
    }

    public function showCreateForm($action, $projectId, $data)
    {
        $project = $this->projectService->getById($projectId);
        if ($project) {
            $data['project'] = $project;
            $view = $action === "createPhase"
                ? 'projectmanager::phase-detail'
                : 'projectmanager::job-detail';
            return view($view, $data);
        } else {
            return view('projectmanager::project-detail', $data);
        }
    }
}
