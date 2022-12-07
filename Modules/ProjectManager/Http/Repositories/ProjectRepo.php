<?php

namespace Modules\ProjectManager\Http\Repositories;

use App\Http\Controllers\Admin\AdminController;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\ProjectManager\Entities\Project;
use Modules\ProjectManager\Entities\Task;

class ProjectRepo extends AdminController
{
    public function get($request){
        $userId = $request->userId ?? null;
        $projects = Project::query();
        if($userId){
            $projects = $projects->whereHas('users',function ($query) use ($userId){
                $query->where('users.id',$userId);
            });
        }
        return $projects->get();
    }

    public function getAll($data = null)
    {
        $projects = Project::with(
            [
                'phases',
                'jobs',
                'tasks',
                'tasks.lastReport',
                'users',
                'users.tasks',
                'leaders' => function ($query) {
                    $query->select('users.*');
                },
                'members' => function ($query) {
                    $query->select('users.*');
                },
            ]);
        if ($data['searchKey']) {
            $searchKey = $data['searchKey'];
            $projects = $projects->where(function ($query) use ($searchKey) {
                $query->where('NameVi', 'LIKE', '%' . $searchKey . '%')
                    ->orWhere('NameJa', 'LIKE', '%' . $searchKey . '%')
                    ->orWhere('NameEn', 'LIKE', '%' . $searchKey . '%')
                    ->orWhere('NameShort', 'LIKE', '%' . $searchKey . '%')
                    ->orWhere('Customer', 'LIKE', '%' . $searchKey . '%');
            });
        }

        if ($data['startDate']) {
            $projects = $projects->where('StartDate', '>=', $data['startDate']);
        }
        if ($data['endDate']) {
            $projects = $projects->where('EndDate', '<=', $data['endDate']);
        }
        $projects = $projects->where('Active', $data['Active']);
        $projects = $projects->where('Active', $data['Active']);
        if ($data['userIds']) {
            $projects = $projects->whereHas('users', function ($query) use ($data) {
                $query->whereIn('users.id', $data['userIds']);
            });
        }
        $projects = $projects->where('Active', $data['Active']);
        if ($data['export']) {
            $projects = $projects->get();
        } else {
            if ($data['orderBy']) {
                if ($data['sortBy'] == 'desc') {
                    $projects = $projects->orderBy($data['orderBy'], $data['sortBy']);
                } else {
                    $projects = $projects->orderBy($data['orderBy']);
                }
            } else {
                $projects = $projects->orderBy('id', 'desc');
            }
            $projects = $projects->paginate(10);
        }
        return $projects;
    }

    public function getById($data)
    {
        $project = Project::with([
            'phases',
            'inactiveUsers',
            'deletedUsers',
            'users' => function ($query) use ($data) {
                if ($data['progressUserIds']) {
                    $query = $query->whereIn('id', $data['progressUserIds']);
                }
            },
            'users.projectUsers' => function ($query) use ($data) {
                $query->where('t_project_user.project_id', $data['projectId']);
            },
            'users.tasks' => function ($query) use ($data) {
                $query->where('ProjectId', $data['projectId']);
            },
            'users.todoTasks' => function ($query) use ($data) {
                $query->where('ProjectId', $data['projectId']);
            },
            'users.doingTasks' => function ($query) use ($data) {
                $query->where('ProjectId', $data['projectId']);
            },
            'users.reviewTasks' => function ($query) use ($data) {
                $query->where('ProjectId', $data['projectId']);
            },
            'users.doneTasks' => function ($query) use ($data) {
                $query->where('ProjectId', $data['projectId']);
            },
            'todoTasks',
            'doingTasks',
            'reviewTasks',
            'doneTasks',
            'phases.phaseType',
            'phases.taskTypes',
            'jobs',
            'tasks.lastReport',
            'tasks.member'
        ]);

        $project = $project->find($data['projectId']);
        if ($project) {
            return $project;
        }
        return $project;
    }

    public function getMembers($data)
    {
        $project = Project::with([
            'members',
            'members.tasks',
        ]);
        return $project->find($data['projectId']);
    }

    public function getTasks($data)
    {
        $project = Project::with([
            'tasks'=>function($query){
                $query = $query->orderByRaw('CASE WHEN t_tasks.StartDate IS NULL THEN 1 ELSE 0 END, t_tasks.StartDate')
                ->orderBy('t_tasks.created_at');
            },
            'jobs'=>function($query){
                $query = $query->orderByRaw('CASE WHEN t_jobs.start_date IS NULL THEN 1 ELSE 0 END, t_jobs.start_date')
                ->orderBy('t_jobs.created_at');
            },
        ]);
        return $project ->find($data['projectId']);
    }

    public function store($data)
    {
        $data['id'] = isset($data['id']) ? $data['id'] : null;
        $project = Project::updateOrCreate(
            ['id' => $data['id']],
            $data
        );
        return $project;
    }

    public function delete($project)
    {
        return $project->delete();
    }
}
