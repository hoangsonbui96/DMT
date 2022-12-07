<?php

namespace Modules\ProjectManager\Http\Services;

use App\Http\Controllers\Admin\AdminController;
use Carbon\Carbon;
use Modules\ProjectManager\Http\Repositories\DailyReportRepo;

class DailyReportService extends AdminController
{
    public $dailyReportRepo;

    public function __construct(DailyReportRepo $dailyRerportRepo)
    {
        $this->dailyReportRepo = $dailyRerportRepo;
    }

    public function store($data){
        
        $today = Carbon::now()->toDateString();
        $data['Date'] = $this->fncDateTimeConvertFomat( $data['Date'], 'd/m/Y', self::FOMAT_DB_YMD);
        $data['DateCreate'] = $today;
        // $data['TypeWork'] = 'BC009';
		$data['Status'] = 2;
        return $this->dailyReportRepo->store($data);
    }
}
