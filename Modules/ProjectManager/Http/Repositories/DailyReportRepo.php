<?php

namespace Modules\ProjectManager\Http\Repositories;

use App\Http\Controllers\Admin\AdminController;
use Carbon\Carbon;
use Modules\ProjectManager\Entities\DailyRerport;
use Modules\ProjectManager\Entities\ProjectUserDetail;

class DailyReportRepo extends AdminController
{
    public function store($data)
    {
        $query = DailyRerport::query()
            ->updateOrCreate($data);
        if($query){
            return $query->id;
        }
        return 0;
    }
}
