<?php

namespace App\Exports;

use App\DailyReport;
use App\Exports\Sheets\YearlyReportSheet;
use App\Exports\Sheets\YearlyTotalReportSheet;
use App\Http\Controllers\Admin\AdminController;
use App\MasterData;
use App\OvertimeWork;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class YearlyReportExport extends AdminController implements WithMultipleSheets
{
    protected $summary;
    protected $request;
    protected $statistic_year;
    function __construct($summary, $request, $statistic_year) {
        $this->summary = $summary;
        $this->request = $request;
        $this->statistic_year = $statistic_year;
    }

    public function sheets(): array
    {
        $request = $this->request;
        $sheets = [];
        $year = $request['year'];
        for($month = 1; $month <= 12; $month++){
            $request['time'] = $month.'/'.$request['year'];
            $this->getDataForSheet($request);
            if ($this->data['dailyReports']->count() > 0){
                $sheets[] = new YearlyReportSheet($this->data, $month);
            }
        }
//        $summary = $this->summary;
        $sheets[] = new YearlyTotalReportSheet($this->summary, $year, $this->statistic_year);
        return $sheets;
    }

    // get data for sheet of month
    public function getDataForSheet(Request $request) {
        $firstOfMonth   = null;
        $endOfMonth     = null;

        //if isset request time
        if ($request->has('time')) {
            $time = '01/'.$request['time'];
            if (\DateTime::createFromFormat('d/m/Y', $time) !== FALSE) {
                $date = date_create($this->fncDateTimeConvertFomat(('01/'.$request['time']),self::FOMAT_DISPLAY_DMY,self::FOMAT_DB_YMD));
                $month = date_format($date,"m");
                $year = date_format($date,"Y");

                $firstOfMonth = $date;
                $endOfMonth = Carbon::parse($date)->endOfMonth();
            } else {
                return Redirect::back();
            }
        } else {
            $now = Carbon::now();
            $date = date_create($this->fncDateTimeConvertFomat(('01/'.$now->month.'/'.$now->year),self::FOMAT_DISPLAY_DMY,self::FOMAT_DB_YMD));
            $month = date_format($date,"m");
            $year = date_format($date,"Y");

            $firstOfMonth = $date;
            $endOfMonth = $now->endOfMonth();
        }

        $dailyReports = DailyReport::query()
            // ->select('daily_reports.*','projects.NameVi','master_data.Name')
            ->select(
                'daily_reports.id',
                'daily_reports.UserID',
                'projects.NameVi',
                'daily_reports.Date',
                'daily_reports.ScreenName',
                'daily_reports.DateCreate',
                'daily_reports.WorkingTime',
                'daily_reports.Progressing',
                'daily_reports.Delay',
                'daily_reports.Note',
                'daily_reports.Contents',
                'daily_reports.ProjectID',
                'master_data.Name'
            )
            ->selectRaw('1 AS TypeReport')
            ->join('projects','projects.id','=','daily_reports.ProjectID')
            ->leftJoin('master_data','master_data.DataValue','=','daily_reports.TypeWork')
            ->leftJoin('users','users.id','=','daily_reports.UserID')
            ->whereMonth('Date', $request->has('time') ? $month : Carbon::now()->month)
            ->whereYear('Date', $request->has('time') ? $year : Carbon::now()->year)
            ->where('UserID', $request->has('UID') ? $request['UID'] : Auth::user()->id);

        //  search daily-report
        foreach($request as $key => $value) {
            if(!is_array($value)) {
                if(Schema::hasColumn('daily_reports', $key) && $value !== null) {
                    $dailyReports = $dailyReports->where($key, 'like', '%'.$value.'%');
                }
            } else {
                if ($value[0] == null) {
                    $dailyReports = $dailyReports->where('UserID', Auth::user()->id);
                }
            }
        }

        // neeu co OT thi them vao
        $OT = OvertimeWork::query()
            ->select('overtime_works.id', 'overtime_works.UserID', 'projects.NameVi')
            ->selectRaw('DATE_FORMAT(overtime_works.STime, \'%Y-%m-%d\') AS Date')
            ->selectRaw('\'\' AS ScreenName')
            ->selectRaw('DATE_FORMAT(overtime_works.STime, \'%Y-%m-%d\') as DateCreate')
            ->selectRaw('FORMAT((TIMESTAMPDIFF(MINUTE, overtime_works.STime, overtime_works.ETime) - overtime_works.BreakTime*60)/60, 2) AS WorkingTime')
            ->selectRaw('100 AS Progressing')
            ->selectRaw('\'0\'AS Delay')
            ->selectRaw('\'\' AS Note')
            ->selectRaw('overtime_works.Content AS Contents')
            ->selectRaw('\'\' AS ProjectID')
            ->selectRaw('\'OT\' AS Name')
            ->selectRaw('2 AS TypeReport')
            ->leftJoin('projects','projects.id','=','overtime_works.ProjectID')
            ->leftJoin('users','users.id','=','overtime_works.UserID')
            ->where('UserID', $request->has('UID') ? $request['UID'] : Auth::user()->id)
            ->where('overtime_works.STime', '>=', $firstOfMonth)
            ->where('overtime_works.ETime', '<=', $endOfMonth)
            ->where('overtime_works.Approved', '!=', 2);

        $dailyReports = $dailyReports->union($OT)->orderBy('Date', 'ASC')->orderBy('id', 'ASC');
        // end test
        $dailyReports = $dailyReports->get();
        // total time
        if ($request->has('time')) {
            $this->totalDailyReport($request, $month, $year);
        } else {
            $this->totalDailyReport($request);
        }
        $this->data['dailyReports'] = $dailyReports;
    }

    public function totalDailyReport($request, $month = null, $year = null) {

        $masterData	= null;
        $masterData = MasterData::query()
            ->where('DataKey', 'BC')
            ->select('Name','DataValue');

        // test project
        $dateTemp = null;
        $firstOfMonth = Carbon::now()->firstOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        if($month != null && $year != null) {
            $dateTemp = date_create($this->fncDateTimeConvertFomat(('01/'.$month.'/'.$year), self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
            $firstOfMonth = $dateTemp;
            $endOfMonth = Carbon::parse($dateTemp)->endOfMonth();
        }

        $OT = OvertimeWork::query()
            ->select('overtime_works.ProjectID', 'projects.NameVi')
            ->selectRaw('FORMAT(SUM((TIMESTAMPDIFF( MINUTE, overtime_works.STime, overtime_works.ETime ) - overtime_works.BreakTime * 60 ) / 60), 2) AS WorkingTime')
            ->leftJoin('users','users.id','=','overtime_works.UserID')
            ->leftJoin('projects','projects.id','=','overtime_works.ProjectID')
            ->where('UserID', $request->has('UID') ? $request['UID'] : Auth::user()->id)
            ->where('overtime_works.STime', '>=', $firstOfMonth)
            ->where('overtime_works.ETime', '<=', $endOfMonth)
            ->where('overtime_works.Approved', '!=', 2)->groupBy('overtime_works.ProjectID')->get();
        if(count($OT) > 0) {
            $temp = MasterData::query()->selectRaw("'OT' as Name, 'BC999' as DataValue")->take(1);
            $masterData = $masterData->union($temp);
        }

        $masterData = $masterData->get();
        // end test project

        $total = DailyReport::query()
            ->select('daily_reports.ProjectID', 'projects.NameVi')
            ->join('projects','projects.id','=','daily_reports.ProjectID')
            ->whereMonth('Date', $request->has('time') ? $month : Carbon::now()->month)
            ->whereYear('Date', $request->has('time') ? $year : Carbon::now()->year)
            ->where('UserID', isset($request['UID']) ? $request['UID'] : Auth::user()->id )
            ->groupBy('daily_reports.ProjectID');

        $OT_temp = OvertimeWork::query()
            ->select('overtime_works.ProjectID', 'projects.NameVi')
            ->leftJoin('users','users.id','=','overtime_works.UserID')
            ->leftJoin('projects','projects.id','=','overtime_works.ProjectID')
            ->where('UserID', $request->has('UID') ? $request['UID'] : Auth::user()->id)
            ->where('overtime_works.STime', '>=', $firstOfMonth)
            ->where('overtime_works.ETime', '<=', $endOfMonth)
            ->where('overtime_works.Approved', '!=', 2)->groupBy('overtime_works.ProjectID');

        $total = $total->union($OT_temp)->get();

        $total->totalHours = 0;

        foreach($masterData as $data) {
            $key = $data->DataValue;
            $total->$key = 0;
        }

        foreach($total as $item) {
            $item->totalHours = 0;
            foreach($masterData as $data) {
                $key = $data->DataValue;
                $item->$key = DailyReport::query()
                    ->where('ProjectID', $item->ProjectID)
                    ->where('TypeWork', $data->DataValue)->whereMonth('Date', $request->has('time') ? $month : Carbon::now()->month)
                    ->whereYear('Date', $request->has('time') ? $year : Carbon::now()->year)
                    ->where('UserID', $request->has('UID') ? $request['UID'] : Auth::user()->id )
                    ->sum('WorkingTime');
                $item->totalHours += $item->$key;
                $total->$key += $item->$key;

                if(count($OT) > 0) {
                    foreach($OT as $record) {
                        if($item->ProjectID == $record->ProjectID && $data->DataValue == 'BC999') {
                            $item->$key = $record->WorkingTime;
                            $item->totalHours += $item->$key;
                            $total->$key += $item->$key;
                            continue 2;
                        }
                    }
                }
            }
            $total->totalHours += $item->totalHours;
        }

        $this->data['total'] = $total;
        $this->data['masterData'] = $masterData;
    }
}
