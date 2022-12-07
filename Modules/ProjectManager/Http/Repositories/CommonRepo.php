<?php

namespace Modules\ProjectManager\Http\Repositories;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\DB;

class CommonRepo extends AdminController
{
    public function getPhaseTypes(){
        return DB::table('master_data')
            ->where('DataKey','PHT')
            ->whereNull('deleted_at')
            ->get();
    }

    public function getPriorities(){
        return DB::table('master_data')
        ->where('DataKey','PRY')
        ->whereNull('deleted_at')
        ->get();
    }
}