<?php

namespace Modules\ProjectManager\Http\Repositories;

use App\Http\Controllers\Admin\AdminController;
use Modules\ProjectManager\Entities\Job;

class JobRepo extends AdminController
{
    public function getById($id)
    {
        $job = Job::with([
            'members',
            'members.tasks' => function ($query) use ($id) {
                $query->where('t_tasks.JobId', $id);
            },
            'members.todoTasks' => function ($query) use ($id) {
                $query->where('t_tasks.JobId', $id);
            },
            'members.doingTasks' => function ($query) use ($id) {
                $query->where('t_tasks.JobId', $id);
            },
            'members.reviewTasks' => function ($query) use ($id) {
                $query->where('t_tasks.JobId', $id);
            },
            'members.doneTasks' => function ($query) use ($id) {
                $query->where('t_tasks.JobId', $id);
            },
        ])
            ->find($id);
        if ($job) {
            return $job;
        }
        return [];
    }

    public function getAll($data)
    {
        $jobs = Job::query()
            ->with([
                'tasks',
                'phases'
            ]);
        if($data['projectId']){
            $jobs = $jobs ->where('project_id', $data['projectId']);
        }    
            
        if ($data['jobSearch']) {
            $jobs = $jobs->where(function ($query) use ($data) {
                $query->where('name', 'like', '%' . $data['jobSearch'] . '%')
                    ->orWhere('description', 'like', '%' . $data['jobSearch'] . '%');
            });
        }
        if($data['userIds']){
            $jobs = $jobs->whereHas('members',function($query2)use($data){
                        $query2->whereIn('users.id',$data['userIds']);
                    });
        }
        if ($data['jobStartDate']) {
            $jobs = $jobs->where('start_date', '>=', $data['jobStartDate']);
        }
        if ($data['jobEndDate']) {
            $jobs = $jobs->where('end_date', '<=', $data['jobEndDate']);
        }
        $jobs = $jobs->orderBy('order')->paginate(10);
        return $jobs;
    }

    public function store($data)
    {
        $job = Job::updateOrCreate(
            ['id' => $data['id']],
            $data
        );
        return $job;
    }

    public function getUsersInjob($jobId)
    {
        $job = Job::with('details.projectUser.user')
            ->find($jobId);
        if (count($job->details) > 0) {
            foreach ($job->details as $detail) {
                $users[] = $detail->projectUser->user;
            }
            return $users;
        }
        return [];
    }

    public function delete($id)
    {
        $job = Job::find($id);
        if(count($job->tasks) > 0){
            return ['deleted'=>false,'mes'=>"Không thể xóa vì trong Job có Task đang được thực hiện!"];
        }
        $job->delete();
        return ['deleted'=>true,'mes'=>"Đã xóa Job!"];
    }

    public function deleteByColumn($column, $value = [])
    {
        return Job::whereIn($column, $value)->delete();
    }
}
