<?php

namespace Modules\ProjectManager\Http\Repositories;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\DB;
use Modules\ProjectManager\Entities\Phase;


class PhaseRepo extends AdminController
{
    public function getById($id)
    {
        $phase = Phase::with([
            'members',
            'taskTypes',
            'members.tasks' => function ($query) use ($id) {
                $query->where('t_tasks.PhaseId', $id);
            },
            'members.todoTasks' => function ($query) use ($id) {
                $query->where('t_tasks.PhaseId', $id);
            },
            'members.doingTasks' => function ($query) use ($id) {
                $query->where('t_tasks.PhaseId', $id);
            },
            'members.reviewTasks' => function ($query) use ($id) {
                $query->where('t_tasks.PhaseId', $id);
            },
            'members.doneTasks' => function ($query) use ($id) {
                $query->where('t_tasks.PhaseId', $id);
            },
        ])
            ->find($id);
        if ($phase) {
            return $phase;
        }
        return [];
    }

    public function getAll($data)
    {
        $phases = Phase::query()
            ->with([
                'phaseType',
                'tasks',
                'project'
            ]);
        if ($data['projectId']) {
            $phases = $phases->where('project_id', $data['projectId']);
        }

        if ($data['phaseSearch']) {
            $phases = $phases->where(
                function ($query) use ($data) {
                    $query->where('name', 'like', '%' . $data['phaseSearch'] . '%')
                        ->orWhere('description', 'like', '%' . $data['phaseSearch'] . '%');
                }
            );
        }
        if($data['userIds']){
            $phases = $phases->where(function($query)use($data){
                $query->whereIn('leader_id',$data['userIds'])
                    ->orWhereHas('members',function($query2)use($data){
                        $query2->whereIn('users.id',$data['userIds']);
                    });
            });
        }
        if ($data['phaseStartDate']) {
            $phases = $phases->where('start_date', '>=', $data['phaseStartDate']);
        }
        if ($data['phaseEndDate']) {
            $phases = $phases->where('end_date', '<=', $data['phaseEndDate']);
        }
        $phases = $phases->orderBy('order')->paginate(10);
        return $phases;
    }

    public function store($data)
    {
        $phase = Phase::updateOrCreate(
            ['id' => $data['id']],
            $data
        );
        return $phase;
    }

    public function delete($id)
    {
        $phase = Phase::find($id);
        if(count($phase->tasks) > 0){
            return ['deleted'=>false,'mes'=>"Không thể xóa vì trong Phase có Task đang được thực hiện!"];
        }
        $phase->delete();
        return ['deleted'=>true,'mes'=>"Đã xóa Phase!"];
    }

    public function deleteByColumn($column, $value = [])
    {
        return Phase::whereIn($column, $value)->delete();
    }
}
