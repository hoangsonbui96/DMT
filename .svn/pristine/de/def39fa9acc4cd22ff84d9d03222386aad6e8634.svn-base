<?php

namespace Modules\Recruit\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Recruit\Entities\InterviewJob;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Admin\AdminController;
use App\Menu;

class InterviewJobController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;

    const KEYMENU = array(
        "add" => "InterviewJobAdd",
        "view" => "InterviewJob",
        "edit" => "InterviewJobEdit",
        "delete" => "InterviewJobDelete",
    );

    function __construct(Request $request)
    {
        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
        $array = $this->RoleView('interviewJob.list', ['InterviewJob']);
        $this->data['menu'] = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if ($value == $row->alias)
                    $this->$key = $row;
            }
        }
    }

    public function interviewJobList(Request $request)
    {
        if ($request->ajax()) {
            $paginate = 20;
            $search = $request->search;
            $date_start = empty($request->start_date) ? '' : Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
            $date_end = empty($request->end_date) ? '' : Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
            $active = $request->active;

            $interviewJob = InterviewJob::selectRaw('jobs.id,jobs.name,jobs.content,jobs.active,jobs.start_date,jobs.end_date,jobs.updated_at,COUNT(candidates.id) as num_candides')
                ->leftJoin('candidates', 'jobs.id', '=', 'candidates.jobID');
            if ($search != '') {
                $interviewJob->where('jobs.name', 'like', '%' . $search . '%');
            }
            if ($date_start != '') {
                $interviewJob->where('jobs.start_date', '>=', $date_start);
            }
            if ($date_end != '') {
                $interviewJob->where('jobs.end_date', '<=', $date_end);
            }
            if ($active != ''){
                $interviewJob->where('jobs.active', $active);
            }
            $interviewJobs = $interviewJob->groupBy('jobs.id')
                ->orderByDesc('jobs.start_date')
                ->paginate($paginate);
            $add = $this->add;
            $edit = $this->edit;
            $delete = $this->delete;
            $data = view('recruit::InterviewJob.interviewJobLoad', compact('interviewJobs', 'add', 'edit', 'delete'))->render();
            return response()->json($data);
        } else {
            $paginate = 20;
            $this->data['add'] = $this->add;
            $this->data['edit'] = $this->edit;
            $this->data['delete'] = $this->delete;
            $this->data['interviewJobs']  = InterviewJob::get_list_interviewJob($paginate);
            return view('recruit::InterviewJob.interviewJobList', $this->data);
        }
    }

    public function interviewJobAdd(Request $request)
    {
        if ($request->ajax()) {
            $data = view('recruit::InterviewJob.interviewJobAdd')->render();
            return response()->json($data);
        } else {
            return redirect()->route('admin.interviewJob.list');
        }
    }

    public function interviewJobStore(Request $request)
    {
        if ($request->ajax()) {
            if (count($request->input()) === 0) {
                return abort('404');
            }

            $validator = Validator::make($request->all(), [
                'name' => 'bail|required|unique:jobs,name',
                'content' => 'bail|required|string',
                'date_start' => 'bail|required|string',
                'date_end' => 'bail|nullable',
                'active' => 'bail|nullable',
            ]);

            if ($validator->fails()) {
                $error = collect($validator->errors())->first();
                return response()->json(['errors' => $error]);
            } else {
                try {
                    $date_start = Carbon::createFromFormat('d/m/Y', $request->date_start)->format('Y-m-d');
                    $date_end =  empty($request->date_end) ? null : Carbon::createFromFormat('d/m/Y', $request->date_end)->format('Y-m-d');
                    $data = [
                        'name' => $request->name,
                        'content' => $request->content,
                        'start_date' => $date_start,
                        'end_date' => $date_end,
                        'active' => (int)$request->active,
                    ];
                    InterviewJob::create($data);
                    return response()->json(['success' => trans('admin.success.save')]);
                } catch (\Exception $e) {
                    return response()->json(['errors' => trans('admin.error.save')]);
                }
            }
        } else {
            return redirect()->route('admin.interviewJob.list');
        }
    }


    public function interviewJobEdit(Request $request)
    {
        if ($request->ajax()) {
            $job_id = $request->job_id;
            try {
                $interviewJob = InterviewJob::where('id', $job_id)->first();
                $start_date = FomatDateDisplay($interviewJob->start_date, 'd/m/Y');
                $end_date = FomatDateDisplay($interviewJob->end_date, 'd/m/Y');
                $data = view('recruit::InterviewJob.interviewJobEdit', compact('interviewJob', 'start_date', 'end_date'))->render();
                return response()->json($data);
            } catch (\Exception $e) {
                return response()->json(['errors' => $e->getMessage()]);
            }
        } else {
            return redirect()->route('admin.interviewJob.list');
        }
    }


    public function interviewJobUpdate(Request $request)
    {
        if ($request->ajax()) {
            if (count($request->input()) === 0) {
                return abort('404');
            }

            $validator = Validator::make($request->all(), [
                'name' => 'bail|required|unique:jobs,name,' . $request->id . 'id',
                'content' => 'bail|required|string',
                'date_start' => 'bail|required|string',
                'date_end' => 'bail|nullable',
                'active' => 'bail|nullable',
            ]);

            if ($validator->fails()) {
                $error = collect($validator->errors())->first();
                return response()->json(['errors' => $error]);
            } else {
                try {
                    $date_start = Carbon::createFromFormat('d/m/Y', $request->date_start)->format('Y-m-d');
                    $date_end =  empty($request->date_end) ? null : Carbon::createFromFormat('d/m/Y', $request->date_end)->format('Y-m-d');
                    $data = [
                        'name' => $request->name,
                        'content' => $request->content,
                        'start_date' => $date_start,
                        'end_date' => $date_end,
                        'active' => (int)$request->active,
                    ];
                    InterviewJob::where('id', $request->id)->update($data);
                    return response()->json(['success' => trans('admin.success.save')]);
                } catch (\Exception $e) {
                    return response()->json(['errors' => trans('admin.error.save')]);
                }
            }
        } else {
            return redirect()->route('admin.interviewJob.list');
        }
    }

    public function interviewJobDelete(Request $request)
    {
        if ($request->ajax()) {
            $job_id = $request->job_id;
            try {
                InterviewJob::where('id', $job_id)->delete();
                return response()->json(['success' => trans('admin.success.delete')]);
            } catch (\Exception $e) {
                return response()->json(['errors' => trans('admin.error.delete')]);
            }
        } else {
            return redirect()->route('admin.interviewJob.list');
        }
    }

    public function interviewJobChangeActive(Request $request)
    {
        if ($request->ajax()) {
            $job_id = $request->job_id;
            $active = (int)$request->active;
            $on = 1;
            if ($active == $on) {
                $active = 0;
            } else {
                $active = 1;
            }

            try {
                $data = [
                    'active' => $active,
                ];
                InterviewJob::where('id', $job_id)->update($data);
                return response()->json(['success' => trans('admin.success.change')]);
            } catch (\Exception $e) {
                return response()->json(['errors' => trans('admin.error.change')]);
            }
        } else {
            return redirect()->route('admin.interviewJob.list');
        }
    }
}
