<?php

namespace Modules\ProjectManager\Http\Repositories;

use App\Http\Controllers\Admin\AdminController;
use Carbon\Carbon;
use Modules\ProjectManager\Entities\ProjectUserDetail;

class DetailRepo extends AdminController
{
    public function store($projectUser, $work_id)
    {
        $data = [
            'project_user_id' => $projectUser['id'],
            'work_id' => $work_id,
            'join_date' => Carbon::now()->toDateString(),
            'active' => 1
        ];
        if (isset($projectUser['is_leader'])) {
            $data['is_leader'] = $projectUser['is_leader'];
        }
        $query = ProjectUserDetail::query()
            ->whereNull('deleted_at')
            ->updateOrCreate(
                ['project_user_id' => $data['project_user_id'], 'work_id' => $data['work_id']],
                $data,
                ['is_leader']
            );
        return $query->id;
    }

    public function deleteByColumns($data = [])
    {
        $query = ProjectUserDetail::query();
        foreach ($data as $key => $value) {
            $query->where($key, $value);
        }
        $query->whereNull('deleted_at')
            ->update(['deleted_at' => Carbon::now(), 'quit_date' => Carbon::now()->toDateString()]);
    }
}
