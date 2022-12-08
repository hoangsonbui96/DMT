<?php

namespace Modules\ProjectManager\Http\Controllers;

use App\Exports\ProjectsExport;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\ProjectManager\Http\Services\CommonService;
use Modules\ProjectManager\Http\Services\JobService;
use Modules\ProjectManager\Http\Services\PhaseService;
use Modules\ProjectManager\Http\Services\ProjectService;
use Modules\ProjectManager\Http\Services\UserService;
use Modules\ProjectManager\Entities\Project;
use Modules\ProjectManager\Entities\Task;
use stdClass;

class ProjectManagerController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $app;
    const KEYMENU = array(
        "add" => "ProjectAdd",
        "view" => "Project",
        "edit" => "ProjectEdit",
        "delete" => "ProjectDelete",
        "app" => "ListApprove"
    );

    public function __construct(
        Request $request,
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

        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }

        $array = $this->RoleView('ProjectManager', ['ProjectManager', 'ProjectManager']);
        $this->menu = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if ($value == $row->alias)
                    $this->$key = $row;
            }
        }
    }

    public function index(Request $request)
    {
        $projects = $this->projectService->getAll($request);
        $total = count($projects);
        $this->data['permissions'] = $this->projectService->getPermissions();
        $this->data['projects'] = $projects;
        $this->data['request'] = $request;
        $this->data['users'] = $this->userService->getAll();
        if (strpos(\Request::getRequestUri(), 'api') !== false) {
            return response()->json(['data' => $this->data]);
        }
        if ($request->ajax()) {
            if ($total > 0) {
                return response()->json([
                    'view' => view('projectmanager::includes/projects-tbody', $this->data)->render(),
                    'last' => $projects[0]->id,
                    'lastPage' => $projects->lastPage(),
                    'page' => $request->page ?? $projects->currentPage(),
                    'sortBy' => $request->sortBy,
                    'errors' => ''
                ]);
            } else {
                if ($request['page'] > 1) {
                    return $this->getAllProjects($request);
                } else {
                    return response()->json([
                        'errors' => ['code' => '000', 'mes' => 'Không có dữ liệu']
                    ]);
                }
            }
        }
        return view('projectmanager::projects', $this->data);
    }

    public function getAllProjects($request)
    {
        if ($request['action'] == 'del') {
            $request['page'] = $request['page'] - 1;
        } else {
            $request['page'] = 1;
        }

        $projects = $this->projectService->getAll($request);
        $total = count($projects);
        $this->data['permissions'] = $this->projectService->getPermissions();

        $this->data['projects'] = $projects;
        $this->data['request'] = $request;
        $this->data['selectUsers'] = $this->GetListUser();
        if ($total > 0) {
            return response()->json([
                'view' => view('projectmanager::includes/projects-tbody', $this->data)->render(),
                'last' => $projects[0]->id,
                'lastPage' => $projects->lastPage(),
                'page' => $request->page ?? $projects->currentPage(),
                'sortBy' => $request->sortBy,
                'errors' => ''
            ]);
        } else {
            return response()->json([
                'errors' => ['code' => '000', 'mes' => 'Không có dữ liệu']
            ]);
        }
    }

    public function getTasks(Request $request)
    {
        $projects = $this->projectService->getTasks($request);
        return AdminController::responseApi(200, null, true, $projects);
    }

    public function get(Request $request)
    {
        $projects = $this->projectService->get($request);
        return AdminController::responseApi(200, null, true, $projects);
    }

    public function store(Request $request)
    {
        $action = 'new';
        $updateId = null;
        if (count($request->input()) === 0) {
            return abort('404');
        }
        $arrCheck = [
            'NameVi' => 'required|string|max:150|unique:projects,NameVi,' . $request['id'] . ',id,deleted_at,NULL',
            'NameJa' => 'nullable|string|max:100',
            'NameShort' => [
                'required',
                'string',
                'max:100',
                'unique:projects,NameShort,' . $request['id'] . ',id,deleted_at,NULL',
            ],
            'Customer' => 'required|string|max:100',
            'StartDate' => 'required|date_format:d/m/Y',
            'EndDate' => 'nullable|date_format:d/m/Y',
            'Description' => 'nullable|string|max:255',
            'Leader' => 'required|array',
            'Member' => 'nullable|array',
            'Active' => 'string|nullable',
        ];
        $modeIsUpdate = array_key_exists('id', $request->input());

        if ($modeIsUpdate) {
            $action = 'update';
            $updateId = $request->id;
            $arrCheck['id'] = 'integer|min:1|nullable';
        }
        $validator = Validator::make(
            $request->all(),
            $arrCheck,
            [
                'Leader.required' => 'Vui lòng chọn trưởng nhóm'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()]);
        } else {
            $validated = $validator->validate();
            if (!$this->StringIsNullOrEmpty($validated['EndDate'])) {
                if ($this->compareDate($validated['StartDate'], $validated['EndDate']) == false) {
                    return $this->jsonErrors('Ngày kết thúc dự án không hợp lệ');
                }
            }
            $response = $this->projectService->store($request);
            if ($response['success']) {
                return response()->json(['action' => $action, 'updateId' => $updateId, 'mes' => $response['mes']]);
            } else {
                return $this->jsonErrors($response['mes']);
            }
        }
    }

    public function showPhaseJob(Request $request)
    {
        $currentUser = auth()->user();
        $this->data['managePermission'] = false;
        if ($currentUser->role_group == 2) {
            $this->data['managePermission'] = true;
        }
        $project = $this->projectService->getById($request);
        if (!$project) {
            return abort('404');
        }
        $this->data['request'] = $request->all();
        if (in_array($currentUser->id, $project->leaders->pluck('id')->toArray())) {
            $this->data['managePermission'] = true;
        };
        $this->data['project'] = $project;
        return view('projectmanager::phase-job', $this->data);
    }

    public function export(Request $request)
    {
        $records = $this->projectService->getAll($request);
        $totalColumns = $records->count();
        if ($records->count() > 0) {
            return Excel::download(new ProjectsExport($records, 11, $totalColumns, $this->projectService), 'Danh_sách_dự_án.xlsx');
        } else {
            return response()->json(['errors' => ['Không có dữ liệu.']]);
        }
    }

    public function showProgress(Request $request)
    {
        if ($request->projectId) {
            $project = $this->projectService->getById($request);
            if (!$project) {
                return abort(404);
            }
            $this->data['project'] = $project;
            return view('projectmanager::gantt', $this->data);
        }
    }

    public function getProgress(Request $request)
    {
        $data = $this->projectService->getProgress($request);
        $tasks = $data['tasks']->whereNotNull('StartDate');
        $unscheduledTasks = $data['tasks']->whereNull('StartDate');

        foreach ($tasks as $task) {
            $task->text = $task->Name;
            $task->start_date = $task->StartDate;
            $task->unscheduled = false;
            $task->end_date = $task->EndDate ?? null;
            $task->progress = $task->Progress / 100;
            $task->parent = $task->JobId ? 'j_' . $task->JobId : null;
            $task->color = $task->JobId ? $task->job->color : null;
        }

        foreach ($data['jobs'] as $job) {
            $task = new Task();
            $task->id = $job->id;
            $task->text = $job->name;
            $task->isJob = true;
            $task->start_date = $job->start_date ?? null;
            $task->unscheduled = ($task->start_date || count($job->tasks) > 0) ? false : true;
            $task->end_date = $job->end_date ?? null;
            $task->color = $job->color;
            $task->WorkedTime = $job->tasks->sum('WorkedTime');
            $task->Duration = $job->tasks->sum('Duration');
            $task->Progress = $this->commonService->calculateProgress($job->tasks, $task->Duration);
            $task->progress = $task->Progress / 100;
            $tasks->push($task);
        }

        foreach ($unscheduledTasks as $task) {
            $task->text = $task->Name;
            $task->start_date =  null;
            $task->unscheduled = true;
            $task->end_date = $task->EndDate ?? null;
            $task->progress = null;
            $task->parent = $task->JobId ? 'j_' . $task->JobId : null;
            $task->color = $task->JobId ? $task->job->color : null;
            $tasks->push($task);
        }
        return response()->json([
            "tasks" => $tasks,
            // "links" => $links->all()
        ]);
    }

    public function syncMembers()
    {
        $oldProject = Project::all();
        foreach ($oldProject as $project) {
            $members = array_filter(explode(',', $project->Member));
            $leaders = array_filter(explode(',', $project->Leader));
            $members = array_diff($members, $leaders);
            $syncLeaders = [];
            foreach ($leaders as $leader) {
                $project->users()->attach([$leader => ['is_leader' => 1]]);
            }
            foreach ($members as $member) {
                $project->users()->attach([$member]);
            }
        }
    }
}
