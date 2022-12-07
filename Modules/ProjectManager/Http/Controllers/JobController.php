<?php

namespace Modules\ProjectManager\Http\Controllers;

use App\Http\Controllers\Admin\AdminController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\ProjectManager\Entities\Project;
use Modules\ProjectManager\Http\Services\CommonService;
use Modules\ProjectManager\Http\Services\JobService;
use Modules\ProjectManager\Http\Services\ProjectService;

class JobController extends CommonController
{
    public $projectService;
    public $commonService;
    public $jobService;

    public function __construct(
        ProjectService $projectService,
        CommonService $commonService,
        JobService $jobService
    ) {
        $this->projectService = $projectService;
        $this->commonService = $commonService;
        $this->jobService = $jobService;
    }

    public function store(Request $request)
    {
        $projectStartDate = $request->projectStartDate? date('d/m/Y', strtotime($request->projectStartDate)) : null;
        $projectEndDate = $request->projectEndDate? date('d/m/Y', strtotime($request->projectEndDate)) : null;
        $projectStartDate   = $projectStartDate ? Carbon::createFromFormat('d/m/Y', $projectStartDate):null;
        $projectEndDate     = $projectEndDate ? Carbon::createFromFormat('d/m/Y', $projectEndDate):null;
        if (count($request->input()) === 0) {
            return abort('404');
        }

        $arrCheck = [
            'Name'         =>  [
                'required',
                'string', 'max:100',
                Rule::unique('t_jobs', 'name')->ignore($request->id, 'id')->where('project_id', $request->projectId)->whereNull('deleted_at')
            ],
            'StartDate'         =>  'nullable|date_format:d/m/Y',
            'EndDate'           =>  'nullable|date_format:d/m/Y',
            'Description'       =>  'nullable|string|max:300',
            'Members'           =>  'nullable|array',
            'phases'            =>  'nullable|array',
            'Active'            =>  'string|nullable'
        ];

        $modeIsUpdate = array_key_exists('id', $request->input());
        if ($modeIsUpdate) {
            $arrCheck['id'] = 'integer|min:1|nullable';
        }
        $validator = Validator::make($request->all(), $arrCheck,[
            'Name.required' => 'Vui lòng điền tên Job!'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()]);
        } else {
            $validated = $validator->validate();
            $endDate    = $validated['EndDate'] ? Carbon::createFromFormat('d/m/Y', $validated['EndDate']) : null;
            $startDate  = $validated['StartDate'] ? Carbon::createFromFormat('d/m/Y', $validated['StartDate']) : null;
            if ($this->compareDate($validated['StartDate'], $validated['EndDate']) == false) {
                return $this->jsonErrors('Ngày kết thúc Job không được nhỏ hơn ngày bắt đầu!');
            }
            if($projectEndDate){
                if($projectEndDate){
                    if ($endDate && !$endDate->between($projectStartDate,$projectEndDate)) {
                        return $this->jsonErrors('Ngày kết thúc Phase phải nằm trong thời gian của dự án');
                    }
                    if ($startDate && !$startDate->between($projectStartDate,$projectEndDate)) {
                        return $this->jsonErrors('Ngày bắt đầu Phase phải nằm trong thời gian của dự án');
                    }
                }
            }
            $job = $this->jobService->store($request);
            if($job->wasRecentlyCreated){
                return AdminController::responseApi('200',null,true, ["mes" => "Đã thêm một Job mới!"]);
            }else{
                return AdminController::responseApi('200',null,true, ["mes" => "Đã cập nhật Job thành công!"]);
            }
        }
    }

    public function show(Request $request)
    {
        $currentUser = auth()->user();
        $data = $request->all();
        $this->data['managePermission'] = false;
        if ($currentUser->role_group == 2) {
            $this->data['managePermission'] = true;
        }
        $project = Project::with(['leaders'])->find($request->projectId);
        if (!$project) {
            return abort('404');
        }
        if (in_array($currentUser->id, $project->leaders->pluck('id')->toArray())) {
            $this->data['managePermission'] = true;
        } else {
            $data['userIds'][] = $currentUser->id;
        }
        $jobs = $this->projectService->getJobs($data);
        if (!$jobs) {
            return abort('404');
        }
        $this->data['jobs'] = $jobs;
        $jobsLastPage = $jobs->lastPage();
        return response()->json([
            'view' => view('projectmanager::includes/jobs-tbody', $this->data)->render(),
            'lastPage' => $jobsLastPage,
            'page' => $request->page ?? $jobs->currentPage(),
            'perPage' => $jobs->perPage(),
            'onPageItems' => count($jobs)
        ]);
    }
}
