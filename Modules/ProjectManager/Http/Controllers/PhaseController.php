<?php

namespace Modules\ProjectManager\Http\Controllers;

use App\Http\Controllers\Admin\AdminController;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\ProjectManager\Entities\Project;
use Modules\ProjectManager\Http\Services\CommonService;
use Modules\ProjectManager\Http\Services\PhaseService;
use Modules\ProjectManager\Http\Services\ProjectService;
use Modules\ProjectManager\Http\Controllers\TraitCommonController;

class PhaseController extends AdminController
{
    public $phaseService;
    public $userService;
    public $projectService;
    public $commonService;

    public function __construct(
        PhaseService $phaseService,
        ProjectService $projectService,
        CommonService $commonService
    ) {
        $this->phaseService = $phaseService;
        $this->projectService = $projectService;
        $this->commonService = $commonService;
    }

    public function getById(Request $request)
    {
        $phase = $this->phaseService->getById($request);
        return response()->json($phase);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $projectStartDate   = $request->projectStartDate ? date('d/m/Y', strtotime($request->projectStartDate)) : null;
        $projectEndDate     = $request->projectEndDate ? date('d/m/Y', strtotime($request->projectEndDate)) : null;
        $projectStartDate   = $projectStartDate ? Carbon::createFromFormat('d/m/Y', $projectStartDate):null;
        $projectEndDate     = $projectEndDate ? Carbon::createFromFormat('d/m/Y', $projectEndDate):null;
        if (count($request->input()) === 0) {
            return abort('404');
        }

        $arrCheck = [
            'Name'         =>  [
                'required',
                'string', 'max:100',
                Rule::unique('t_phases', 'name')->ignore($request->id, 'id')->where('project_id', $request->projectId)->whereNull('deleted_at')
            ],
            'type'              =>  'required',
            'StartDate'         =>  'nullable|date_format:d/m/Y',
            'EndDate'           =>  'nullable|date_format:d/m/Y',
            'leader'            =>  'nullable',
            'Description'       =>  'nullable|string|max:100',
            'Active'            =>  'string|nullable'
        ];
        $modeIsUpdate = array_key_exists('id', $request->input());
        if ($modeIsUpdate) {
            $arrCheck['id'] = 'integer|min:1|nullable';
        }

        $validator = Validator::make($request->all(), $arrCheck,[
            'Name.required' => 'Vui lòng điền tên Phase!'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()]);
        } else {
            $validated = $validator->validate();
            $startDate = $validated['StartDate'] ? Carbon::createFromFormat('d/m/Y', $validated['StartDate']) : null;
            $endDate   = $validated['EndDate'] ? Carbon::createFromFormat('d/m/Y', $validated['EndDate']) : null;
            if ($this->compareDate($validated['StartDate'], $validated['EndDate']) == false) {
                return $this->jsonErrors('Ngày kết thúc phase không được nhỏ hơn ngày bắt đầu!');
            }
            if($projectEndDate){
                if ($endDate && !$endDate->between($projectStartDate,$projectEndDate)) {
                    return $this->jsonErrors('Ngày kết thúc Phase phải nằm trong thời gian của dự án');
                }
                if ($startDate && !$startDate->between($projectStartDate,$projectEndDate)) {
                    return $this->jsonErrors('Ngày bắt đầu Phase phải nằm trong thời gian của dự án');
                }
            }
            $phase = $this->phaseService->store($request);
            if($phase->wasRecentlyCreated){
                return AdminController::responseApi('200',null,true, ["mes" => "Đã thêm một Phase mới!"]);
            }else{
                return AdminController::responseApi('200',null,true, ["mes" => "Đã cập nhật Phase thành công!"]);
            }
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Request $request)
    {
        $project = Project::with(['leaders'])->find($request->projectId); 
        $currentUser = auth()->user();
        $data = $request->all(); 
            $this->data['managePermission'] = false;
        if($currentUser->role_group == 2){
            $this->data['managePermission'] = true;
        }elseif (in_array($currentUser->id, $project->leaders->pluck('id')->toArray())) {
            $this->data['managePermission'] = true;
        }else{
            $data['userIds'][] = $currentUser->id;
        }
        $phases = $this->projectService->getPhases($data);
        $this->data['phases'] = $phases;
        return response()->json([
            'view' => view('projectmanager::includes/phases-tbody', $this->data)->render(),
            'lastPage' => $phases->lastPage(),
            'page' => $request->page ?? $phases->currentPage(),
            'perPage' => $phases->perPage(),
            'onPageItems' => count($phases)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('projectmanager::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
