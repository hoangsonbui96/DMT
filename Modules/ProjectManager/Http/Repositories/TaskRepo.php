<?php

namespace Modules\ProjectManager\Http\Repositories;

use App\Http\Controllers\Admin\AdminController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\ProjectManager\Entities\Task;

class TaskRepo extends AdminController
{
    public function getTasks($data)
    {
        $today = Carbon::now()->format('Y-m-d H:i:s');
        $data['memberId'] = $data['memberId'] ?? null;
        $data['startTime'] = $data['startTime'] ?? null;
        $data['endTime'] = $data['endTime'] ?? null;
        $tasks = Task::with([
            'giver',
            'member',
            'typeName',
            'project',
            'project.leaders',
            'lastReport',
            'OT',
            'phase',
            'job',
            'parentTask',
            'issues'
        ])
            ->select('t_tasks.*','p.order as pOrder','j.order as jOrder','member.FullName as mName');
            // ->selectRaw('ROW_NUMBER() OVER (ORDER BY IF(ISNULL(t_tasks.StartDate),t_tasks.created_at,t_tasks.StartDate)) as "order"' );
        $tasks = $tasks->selectSub(function ($query) use ($today) {
            $query->selectRaw('t_tasks.id')
                ->where('t_tasks.EndDate', '<', $today);
        }, 'outdated');
        if ($data['memberId']) {
            $tasks = $tasks->where('t_tasks.UserId', $data['memberId']);
        }
        if ($data['taskUserIds']) {
            $tasks = $tasks->whereIn('t_tasks.UserId', $data['taskUserIds']);
        }
        if ($data['startTime']) {
            $tasks = $tasks->where('t_tasks.StartDate', '>=', $data['startTime']);
        }
        if ($data['endTime']) {
            $endTime = $data['endTime'].'23:59:59';
            $tasks = $tasks->where('t_tasks.EndDate', '<=', $endTime);
        }
        if ($data['projectId']) {
            $tasks = $tasks->where('t_tasks.ProjectId', $data['projectId']);
        }
        if ($data['jobId']) {
            $tasks = $tasks->where('t_tasks.JobId', $data['jobId']);
        }
        if ($data['phaseId']) {
            $tasks = $tasks->where('t_tasks.PhaseId', $data['phaseId']);
        }
        if(substr($data['keyword'],0,1) == '#'){
            $tasks = $tasks->where('t_tasks.Tags','like', '%'.substr($data['keyword'],1).'%');
        }else{
            $tasks = $tasks->where('t_tasks.Name','like', '%'.$data['keyword'].'%');
        }
        $tasks = $tasks
                            ->leftJoin('t_phases as p','p.id','=','t_tasks.PhaseId')
                            ->leftJoin('t_jobs as j','j.id','=','t_tasks.JobId')
                            ->leftJoin('users as member','member.id','=','t_tasks.UserId');
        switch ($data['orderBy']) {
            case 'PhaseId':
                $tasks = $tasks ->orderByRaw('CASE WHEN pOrder IS NULL THEN 1 ELSE 0 END, pOrder')
                                ->orderByRaw('CASE WHEN t_tasks.StartDate IS NULL THEN 1 ELSE 0 END, t_tasks.StartDate');
                break;
            case 'JobId':
                $tasks = $tasks ->orderByRaw('CASE WHEN jOrder IS NULL THEN 1 ELSE 0 END, jOrder')
                                ->orderByRaw('CASE WHEN t_tasks.StartDate IS NULL THEN 1 ELSE 0 END, t_tasks.StartDate');
                break;
            case 'UserId':
                $tasks = $tasks->orderBy('mName');
                break;
            case 'Name':
                $tasks = $tasks->orderBy('t_tasks.Name');
                break;
            case 'StartDate':
                $tasks = $tasks ->orderByRaw('CASE WHEN t_tasks.StartDate IS NULL THEN 1 ELSE 0 END, t_tasks.StartDate');
                break;
            default:
                $tasks = $tasks ->orderByRaw('CASE WHEN pOrder IS NULL THEN 1 ELSE 0 END, pOrder');
                break;
        }
        $tasks = $tasks     ->orderBy('t_tasks.created_at')
                            ->get();
        return $tasks;
    }

    public function getOrderBy($data,$key){
        $tasks = Task::with([
        ])
            ->select('t_tasks.id');
        if ($data['projectId']) {
            $tasks = $tasks->where('t_tasks.ProjectId', $data['projectId']);
        }
        switch ($key) {
            case 'pOrder':
                $tasks = $tasks ->leftJoin('t_phases as p','p.id','=','t_tasks.PhaseId')
                                ->addSelect('p.order as pOrder')
                                ->orderByRaw('CASE WHEN pOrder IS NULL THEN 1 ELSE 0 END, pOrder');
                break;
            case 'jOrder':
                $tasks = $tasks 
                                ->leftJoin('t_jobs as j','j.id','=','t_tasks.JobId')
                                ->addSelect('j.order as jOrder')
                                ->orderByRaw('CASE WHEN jOrder IS NULL THEN 1 ELSE 0 END, jOrder');
                break;
            default:
                $tasks = $tasks ->leftJoin('t_phases as p','p.id','=','t_tasks.PhaseId')
                                ->orderByRaw('CASE WHEN pOrder IS NULL THEN 1 ELSE 0 END, pOrder')
                                ->addSelect('p.order as pOrder');
                break;
        }
        $tasks = $tasks     
                            ->orderByRaw('CASE WHEN t_tasks.StartDate IS NULL THEN 1 ELSE 0 END, t_tasks.StartDate')
                            ->orderBy('t_tasks.created_at')
                            ->get();
        return $tasks;
    }

    public function getInvolvedTasks($data){
        $tasks = Task::with([
            'project',
        ]);
        $tasks = $tasks->where('UserId',$data['memberId'])
                        ->where('id','!=',$data['taskId'])
                        ->where('ProjectId','=',$data['projectId']);
                        
        $tasks = $tasks->where(function($query) use($data){
            $query  ->orwhereBetween('StartDate',[$data['startTime'],$data['endTime']])
                    ->orWhereBetween('EndDate',[$data['startTime'],$data['endTime']]);
        });
        $tasks = $tasks->get();
        if ($tasks) {
            return $tasks;
        }
        return [];
    }

    public function getById($data)
    {
        $task = Task::with([
            'giver',
            'member',
            'typeName',
            'project',
            'lastReport',
            'OT',
            'dailyReports',
            'phase',
            'job',
            'issues'
        ]);
        return $task->find($data['taskId']);
    }

    public function store($data)
    {
        return Task::updateOrCreate(
            ['id' => $data['id']],
            $data
        );
    }
}
