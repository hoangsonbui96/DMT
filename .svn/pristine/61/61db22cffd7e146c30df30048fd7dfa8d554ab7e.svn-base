<?php

namespace Modules\ProjectManager\Http\Repositories;

use App\Http\Controllers\Admin\AdminController;
use Carbon\Carbon;
use Modules\ProjectManager\Entities\ProjectUser;
use Modules\ProjectManager\Entities\ProjectUserDetail;

class ProjectUserRepo extends AdminController
{
  
    public function store($data)
    {
        $query = ProjectUser::query()
            ->whereNull('deleted_at')
            ->updateOrCreate(
                ['project_user_id' => $data['id'], 'user_id' => $data['user_id']],
                $data,
                [$data['is_leader']]
            );
        return $query->id;
    }


    public function delete($obj)
    {
        $obj->deleted_at = Carbon::now();
        $obj->save();
    }

    public function deleteByColumns($data = [])
    {
        $now = Carbon::now();
        $query = ProjectUser::query();
        foreach ($data as $key => $value) {
            $query->where($key, $value);
        }
        $query->whereNull('deleted_at')
            ->update(['deleted_at' => $now, 'quit_date' => $now->toDateString()]);
    }

    public function getUserIdByProject($projectId)
    {
        $userIds = ProjectUser::query()
            ->select('user_id')
            ->where('project_id', $projectId)
            ->whereNull('deleted_at')
            ->get()->toArray();
        return $userIds;
    }

    public function getId($userIds, $projectId)
    {
        return ProjectUser::whereIn('user_id', $userIds)
            ->where('project_id', $projectId)
            ->pluck('id');
    }
}
