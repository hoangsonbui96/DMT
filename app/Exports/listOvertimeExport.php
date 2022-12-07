<?php


namespace App\Exports;

use App\Exports\Sheets\listOvertimeSheet;
use App\Exports\Sheets\overtimeWorkSheet;
use App\Http\Controllers\Admin\AdminController;
use App\OvertimeWork;
use App\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class listOvertimeExport extends AdminController implements WithMultipleSheets
{

    protected $list;
    protected $request;
    function __construct($list,$request)
    {
		$newList = [];
		foreach($list as $item) {
			if($item->time > 0)
			{
				array_push($newList, $item);
			}
		}
		$this->list = $newList;
    //    $this->list = $list;
        $this->request = $request;
    }

    public function sheets(): array
    {
        $request = $this->request;
        $sheets = [];
        $sheets[] = new listOvertimeSheet($this->list);

        foreach ($this->list as $item){

            $overTimeOfUser = OvertimeWork::query()->select('overtime_works.*','tb1.FullName','projects.NameVi','tb2.FullName as NameUpdatedBy')
                ->leftJoin('projects', 'overtime_works.ProjectID', '=', 'projects.id')
                ->leftJoin('users as tb1', 'overtime_works.UserID', '=', 'tb1.id')
                ->leftJoin('users as tb2', 'overtime_works.UpdatedBy', '=', 'tb2.id')
                ->orderBy('STime', 'asc');

            $overTimeOfUser = $overTimeOfUser->where('overtime_works.UserID', $item->UserID)->where('overtime_works.Approved', '!=', 2);

            if ($request['ProjectID'] != null){
                $overTimeOfUser = $overTimeOfUser->where('overtime_works.ProjectID', $request['ProjectID']);
            }

            $value = array();
            $value[0] = $request['date'] != null && $request['date'][0] != '' ? $this->fncDateTimeConvertFomat($request['date'][0], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : '';
            $value[1] = $request['date'] != null && $request['date'][1] != '' ? $this->fncDateTimeConvertFomat($request['date'][1], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : '';
			
            $overTimeOfUser->where(function ($query) use ($value) {
                if ($value[0] != '' && $value[1] != '' && $value[0] !== $value[1]) {
                    $query = $query->whereBetween('overtime_works.STime', array(Carbon::parse($value[0])->startOfDay(),Carbon::parse($value[1])->endOfDay()))
                                    ->orWhereBetween('overtime_works.ETime', array(Carbon::parse($value[0])->startOfDay(),Carbon::parse($value[1])->endOfDay()))
                                    ->orWhereBetween('overtime_works.STimeLogOT', array(Carbon::parse($value[0])->startOfDay(),Carbon::parse($value[1])->endOfDay()))
                                    ->orWhereBetween('overtime_works.ETimeLogOT', array(Carbon::parse($value[0])->startOfDay(),Carbon::parse($value[1])->endOfDay()));
                }
                if ($value[0] === $value[1] && $value[0] != '') {
                    $query = $query->whereRaw("CAST(overtime_works.STime AS DATE) = '$value[0]'")
                                    ->orWhereRaw("CAST(overtime_works.STimeLogOT AS DATE) = '$value[0]'");
                }
                if ($value[0] != '' && $value[1] == '') {
                    $query = $query->where('overtime_works.STime', '>=', Carbon::parse($value[0])->startOfDay())
                                    ->orWhere('overtime_works.ETime', '>=', Carbon::parse($value[0])->startOfDay())
                                    ->orWhere('overtime_works.STimeLogOT', '>=', Carbon::parse($value[0])->startOfDay())
                                    ->orWhere('overtime_works.ETimeLogOT', '>=', Carbon::parse($value[0])->startOfDay());
                }
                if ($value[0] == '' && $value[1] != '') {
                    $query = $query->where('overtime_works.STime', '<=', Carbon::parse($value[1])->startOfDay())
                                    ->orWhere('overtime_works.STimeLogOT', '<=', Carbon::parse($value[1])->startOfDay())
                                    ->orWhere('overtime_works.ETime', '<=', Carbon::parse($value[1])->startOfDay())
                                    ->orWhere('overtime_works.ETimeLogOT', '<=', Carbon::parse($value[1])->startOfDay());
                }
                if (($value[0] == '' && $value[1] == '')) {
                    $query = $query->where('overtime_works.STime', '>=', Carbon::now()->startOfMonth())
                                    ->where('overtime_works.STimeLogOT', '>=', Carbon::now()->startOfMonth())
                                    ->Where('overtime_works.ETime', '<=', Carbon::now()->endOfMonth())
                                    ->where('overtime_works.ETimeLogOT', '<=', Carbon::now()->endOfMonth());
                }
            });

            $overTimeOfUser = $overTimeOfUser->get();
            $sheets[] = new overtimeWorkSheet($overTimeOfUser,self::WEEK_MAP);
        }

        return $sheets;
    }
}
