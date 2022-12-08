<?php

namespace Modules\Leave\Http\Controllers;

use App\Absence as AppAbsence;
use App\CalendarEvent;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\Model\Absence;
use App\Model\TimeKeepingAllDay;
use App\TimekeepingNew;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Modules\Leave\Entities\Leave;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use stdClass;

class LeaveController extends AdminController
{
    protected $view;
    protected $export;
    const KEYMENU = array(
        "view" => "Leave",
        "export" => "LeaveExport",
        "lock" => "LeaveLock",
    );

    const LEAVE_TIME_PER_MONTH = 8 * 3; //thời gian nghỉ phép tối đa trong 1 tháng
    const MID_MONTH_DAY = 15; //ngày giữa tháng
    const WORKING_HOURS_PER_DAY = 8; //số giờ làm việc trong 1 ngày
    const MINUS_PER_HOUR = 60; // số phút 1 giờ
    const TYPE_ABSENCE = ['VM001', 'VM002', 'VM003', 'VM004', 'VM005'];
    const NO_TIMEKEEPING = 1;
    const LATE_SOON = 2;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');
        $array = $this->RoleView('Leave', ['Leave']);
        $this->menu = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if ($value == $row->alias)
                    $this->$key = $row;
            }
        }
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            "users_search" => "nullable|array",
            "users_search.*" => "required|integer",
            "date" => "nullable|date_format:d/m/Y"
        ]);

        $users = $this->data["users"] = $this->GetListUser(self::USER_ACTIVE_FLAG);

        $search_date = isset($validated["date"])
            ? Carbon::createFromFormat(self::FOMAT_DISPLAY_DMY, $validated["date"])
            : Carbon::now();

        //lấy ra id của user
        $user_select = isset($validated["users_search"])
            ? $validated["users_search"]
            // : $this->data["users"]->pluck("id")->toArray();
            : [Auth::user()->id];

        $user_leave = $this->data["users_leave"] = $this->getDataUser($user_select, $search_date);

        //dd($user_leave);

        $search_date = Carbon::parse($search_date)->format('d-m-Y');
        $s_day = Carbon::createFromFormat('d-m-Y', $search_date)->format('d');
        $s_month = Carbon::createFromFormat('d-m-Y', $search_date)->format('m');
        $s_year = Carbon::createFromFormat('d-m-Y', $search_date)->format('Y');
        $startOfYear = Carbon::now()->firstOfYear();
        
        //lấy ra số ngày đi làm trong 1 năm (trừ t7 cn và các ngày lễ) 
        $workDaysOfYear = Cache::remember('users', 1000, function () use ($s_year) {
            $getEventCalendar = $this->getEventCalendar($s_year);
            $workDaysOfYear = [];
            for ($month = 1; $month <= 12; $month++) {
                $daysInMonth = Carbon::now()->month($month)->daysInMonth;
                for($days=1; $days <= $daysInMonth; $days++) {
                    //dd($today->dayOfWeek);
                    $date = Carbon::createFromFormat('Y-m-d', $s_year . '-' . $month . '-' . $days);
                    if($date->dayOfWeek != 0 && $date->dayOfWeek != 6){
                        //lọc bỏ các ngày nghỉ lễ
                        foreach($getEventCalendar as $item){
                            $startDate = Carbon::createFromFormat('Y-m-d', $item->StartDate);
                            $endDate = Carbon::createFromFormat('Y-m-d', $item->EndDate);
                            if($date->gte($startDate) && $date->lte($endDate)){
                                $flag = true;
                                break;
                            }else{
                                $flag = false;
                            }
                        }
                        if($flag == false){
                            $workDaysOfYear[] = Carbon::createFromDate($date->year, $month, $days)->format('Y-m-d');
                        }
                    }
                }
            }
            return $workDaysOfYear;
        });

        foreach ($user_leave as $user) {
            $startDate = $user->SDate; // ngày bắt đầu vào làm
            $ODate = $user->OfficialDate; //ngày kí hợp đồng chính thức

            $o_day = date('d', strtotime($ODate));
            $o_month = date('m', strtotime($ODate));
            $o_year = date('Y', strtotime($ODate));

            $start_days = date('d', strtotime($startDate));
            $start_months = date('m', strtotime($startDate));
            $start_years = date('Y', strtotime($startDate));


            //lấy ra tgian còn tồn của năm ngoái
            $T_LastYear = Leave::query()
                ->where('UserID', $user->id)
                //->where('UserID', '225')
                ->where('DecisiveYear', ((int)$s_year - 1))
                ->select('TimeRemaining')
                ->first();
            if (!$T_LastYear) {
                $last_year_before = 0;
            } else {
                $last_year_before = $T_LastYear->TimeRemaining;
            }
            
            $time_available = $this_year_before = $this_year_after = $absence = $beyond_time = $no_timekeeping = $late_soon = 0;
            $last_year_after = $last_year_before;
            $AbsenceSearchMonth = 0;
            //dd($last_year_before);
            //dd($o_year);
            //nếu năm kí hđ = năm search thì sẽ trả về tháng ký hợp đồng ko thì sẽ trả về 1
            $start_month = ($o_year == $s_year) ? $o_month : 1;

            //nếu tháng search màng lớn hơn 6 thì reset tgian nghỉ phép của năm trước về 0
            if($s_month > 6){
                $last_year_before = 0;
                $last_year_after = 0;
            }

            $dataAbsenceByUserId = $this->getDataAbsenceByUserId($user->id, $search_date, $s_year == $o_year ? $ODate : $startOfYear);
            $dataTimeKeepingByUserId = $this->getSoonLateTime($user->id, $search_date, $s_year == $o_year ? $ODate : $startOfYear);
 
            $totalSoonLateTime = 0;

            $filterToGetSoonLateTime = [];
            foreach($dataTimeKeepingByUserId as $item){
                if(isset($item->lateConvertToSecond) && isset($item->soonConvertToSecond)){
                    $totalSoonLateTime += ($item->lateConvertToSecond + $item->soonConvertToSecond);
                }else if(isset($item->lateConvertToSecond)){
                    $totalSoonLateTime += $item->lateConvertToSecond;
                }else if(isset($item->soonConvertToSecond)){
                    $totalSoonLateTime += $item->soonConvertToSecond;
                }
            }

            /**
                đoạn code sửa tính ngày nghỉ phép cho đến thời điểm search
            */
            for ($month = $start_month; $month <= $s_month; $month++) {

                if($month != $s_month){
                    $this_year_before += 1;
                }else if($month == $s_month && $s_day > 15){
                    $this_year_before += 1;
                }
            }

            //lấy ra những ngày thực tế đi làm của từng user
            //$workDaysByUserID = [];

            if($ODate == null){
                $ODatesCarbon = Carbon::createFromFormat('Y-m-d', $start_years . '-' . $start_months . '-' . $start_days);
            }else{
                $ODatesCarbon = Carbon::createFromFormat('Y-m-d', $o_year . '-' . $o_month . '-' . $o_day);
            }
            $SearchDatesCarbon = Carbon::createFromFormat('Y-m-d', $s_year . '-' . $s_month . '-' . $s_day);

            //hàm tính toán lấy ra những ngày phải đi làm nhưng ko có chấm công
            $workDaysByUserID = $this->funCaculateWork($ODatesCarbon, $SearchDatesCarbon, $workDaysOfYear, $user->id);


            //lọc lấy ra những ngày thiếu check in hoặc check out 
            $noCheckInOrOut = $this->getTimeKeepingWithMonth($user->id, $s_year, $s_month, $s_day);
            $noCheckInOrOutArr = [];
            foreach($noCheckInOrOut as $item){
                if(!$item->TimeIn || !$item->TimeOut){
                    array_push($noCheckInOrOutArr, $item);
                }
            }
            //với mỗi ngày check in thiếu + 8h mỗi ngày(cột ko chấm công)
            foreach($noCheckInOrOutArr as $item){
                $no_timekeeping += 8;
            }
            
            //với mỗi ngày phải đi làm nhưng ko có chấm công + 8h mỗi ngày(cột ko chấm công)
            foreach($workDaysByUserID as $item){
                $no_timekeeping += 8;
            }

            //trường hợp user chưa có ngày ký HĐ thì sẽ ko có ngày phép
            if($ODate == null){
                $this_year_before = 0;
            }else{
                //nhân số ngày được nghỉ với 8 để lấy ra tổng tgian nghỉ phép
                $this_year_before *= 8;
            }
     
            //tính số tgian phép còn lại cho tới hiện tại
            $calForBeyondAndTYearAfter = $this_year_before - ($dataAbsenceByUserId + $totalSoonLateTime);

            if($calForBeyondAndTYearAfter < 0){
                $beyond_time = $calForBeyondAndTYearAfter;
                $this_year_after = 0;
            }else{
                $this_year_after = $calForBeyondAndTYearAfter;
                $beyond_time = 0;
            }

            //dd($this_year_after);
            //dd($no_timekeeping);
            $user->beyond_time = $beyond_time;
            $user->last_year_before = $last_year_before;
            $user->last_year_after = $last_year_after;

            $user->this_year_before = $this_year_before;
            $user->this_year_after = $this_year_after;

            $user->AbsenceSearchMonth = $dataAbsenceByUserId;
            $user->no_timekeeping = $no_timekeeping;
            $user->late_soon = $totalSoonLateTime;
        }

        $this->data['export'] = $this->export;
        $this->data['lock'] = $this->lock;
        $this->data['loginUser'] = Auth::user()->id;
        $this->data['loginUserName'] = Auth::user()->FullName;

        if (isset($validated["date"]) || isset($validated["users_search"])) {
            return view('leave::includes.leave-load', $this->data);
        }
        //dd($this->data['users_leave']);
        return view("leave::layouts.leave-list", $this->data);
    }

    public function getDataUser($users_search, $date)
    {
        //$date = $date->format("Y-m-d");
        $users = User::query()
            ->select("id", "FullName", "OfficialDate", "SDate", "STimeOfDay", "ETimeOfDay")
            ->where('deleted', '!=', 1)
            ->where('role_group', '!=', 1)
            ->where('Active', 1)
            ->whereIn("id", $users_search)
            ->orderBy("FullName")
            ->get();
        return $users;
    }

    public function absence(Request $request)
    {   
        $getOfficeYear = date('Y', strtotime($request['OfficeDate']));
        $startOfYear = Carbon::now()->firstOfYear();
        $getCurrentYear = date('Y', strtotime($startOfYear));

        $search_date = (null != $request['date'])
            ? $request['date']
            : Carbon::now();
        $userid = (null != $request['UserID'])
            ? $request['UserID']
            : Auth::user()->id;

        $getOfficeDate = $request['OfficeDate'];
        $s_month = date('m', strtotime($search_date));
        $s_year = date('Y', strtotime($search_date));

        //trường hợp năm ký HĐ cùng với năm search thì sẽ lấy dữ liệu từ ngày ký HĐ
        if($getOfficeYear == $getCurrentYear){
            $firstOfMonth = Carbon::parse(Carbon::parse($getOfficeDate)->firstOfMonth())->startOfDay();
        }else{
            $firstOfMonth = Carbon::parse(Carbon::parse($startOfYear)->firstOfMonth())->startOfDay();
        }
        
        $lastOfMonth = Carbon::parse(Carbon::parse($search_date))->endOfDay();
        $this->data['absenceLeave'] = Absence::query()->select('absences.*', 'master_data.Name')
            ->join('master_data', 'master_data.DataValue', 'absences.MasterDataValue')
            ->where('UID', '=', $userid)
            ->whereIn("absences.MasterDataValue", self::TYPE_ABSENCE)
            ->Where(function ($query) use ($firstOfMonth, $lastOfMonth) {
                $query->orWhereBetween('SDate', array($firstOfMonth, $lastOfMonth))
                    ->orWhereBetween('EDate', array($firstOfMonth, $lastOfMonth));
            })
            ->orWhere(function ($query) use ($search_date) {
                $query->where('SDate', '<=', Carbon::parse($search_date)->toDateString())
                    ->where('EDate', '>=', Carbon::parse($search_date)->toDateString());
            })
            ->where('UID', '=', $userid)
            ->orderBy('SDate','asc')
            ->get();
        $absenceLeave = $this->data['absenceLeave'];
        foreach ($absenceLeave as $absence) {
            $absence->SDate = Carbon::parse($absence->SDate)->format('d/m/Y H:i:s');
            $absence->EDate = Carbon::parse($absence->EDate)->format('d/m/Y H:i:s');
            $absence->TotalTimeOff = round(($absence->TotalTimeOff / self::MINUS_PER_HOUR), 2);
            $absence->errorReport = false;
        }

        if($request['Type'] == 1){
            $absenceLeave = $absenceLeave->paginate(15, null, 1);
            $absenceLeave->userId = $request['UserID'];
            $absenceLeave->searchDate = $request['date'];
            $absenceLeave->OfficeDate = $request['OfficeDate'];
            $absenceLeave->searchDate = $request['date'];
            $absenceLeave->lastPages = $absenceLeave->lastPage();
            return view("leave::includes.absence-detail")
            ->with(compact('absenceLeave'));
        }else{
            $absenceLeave = $absenceLeave->paginate(15, null, $request['pageNums']);
            return $absenceLeave;
        }
    }

    public function getUnregisteredList(Request $request)
    {
        //dd('te');
        $getOfficeYear = date('Y', strtotime($request['OfficeDate']));
        $startOfYear = Carbon::now()->firstOfYear();
        // $getCurrentYear = date('Y', strtotime($startOfYear));
        $getOfficeDate = $request['OfficeDate'];

        $search_date = (null != $request['date']) ? Carbon::createFromDate($request->date) : Carbon::now();
        $UserID = (null != $request['UserID']) ? $request['UserID'] : Auth::user()->id;
        // $day = Carbon::parse($search_date)->format('d');
        // $month = Carbon::parse($search_date)->format('m');
        $year = Carbon::parse($search_date)->format('Y');

        $dataTimeKeepingByUserId = $this->getSoonLateTime($UserID, $search_date, $year == $getOfficeYear ? $getOfficeDate : $startOfYear);
        
        $filterToGetSoonLateTime = [];

        foreach($dataTimeKeepingByUserId as $item){
            if(isset($item->late) || isset($item->soon)){
                $item->TypeSelect = (null != $request['TypeSelect']) ? $request['TypeSelect'] : 1;               
                array_push($filterToGetSoonLateTime, $item);
            }
        }

        //dd($filterToGetSoonLateTime);

        if($request['Type'] == 1){
            $filterToGetSoonLateTime = $this->paginateArr($filterToGetSoonLateTime, 15, 1);
            $filterToGetSoonLateTime->userId = $request['UserID'];
            $filterToGetSoonLateTime->searchDate = $request['date'];
            $filterToGetSoonLateTime->OfficeDate = $request['OfficeDate'];
            return view("leave::includes.late-soon-detail", compact('filterToGetSoonLateTime'));
        }else{
            $filterToGetSoonLateTime = $this->paginateArr($filterToGetSoonLateTime, 15, $request['pageNums']);
            return $filterToGetSoonLateTime;
        }
    }

    public function getDataAbsenceByUserId($userid , $searchTime, $searchFrom){
        $totalAbsenceMinute = 0;
        $searchTimeNewFomat = Carbon::parse($searchTime)->format('Y-m-d H:i:s');
        //dd($newFomat);

        $getMonthFrom = date('m', strtotime($searchFrom));
        $getMonthTo = date('m', strtotime($searchTime));

        $data = DB::table('absences')
        ->select(['TotalTimeOff', 'AbsentDate'])
        ->where('UID', $userid)
        ->whereIn("MasterDataValue", self::TYPE_ABSENCE)
        ->Where(function($query) use ($searchFrom, $searchTimeNewFomat){
            $query->whereBetween('AbsentDate', array($searchFrom, $searchTimeNewFomat));
        })  
        ->get();

        //dd($data);
        foreach($data as $item){
            $totalAbsenceMinute += $item->TotalTimeOff;
        }
        $totalAbsenceHour = $totalAbsenceMinute / 60;
        return $totalAbsenceHour;
    }

    public function getSoonLateTime($userid, $searchTime, $searchFrom){
        $searchTimeNewFomat = Carbon::parse($searchTime)->format('Y-m-d H:i:s');

        $data = DB::table('timekeepings_new')
        ->select(['Date', 'TimeIn', 'TimeOut', 'STimeOfDay', 'ETimeOfDay','UserID'])
            ->where('UserID', $userid)
            ->Where(function($query) use ($searchFrom, $searchTimeNewFomat){
                $query->whereBetween('Date', array($searchFrom, $searchTimeNewFomat));
            })
            ->whereNotNull('TimeIn')
            ->whereNotNull('TimeOut')
            ->get();
        
        foreach($data as $item){
            //lấy ra h đi muộn
            if ($item->TimeIn != Null && Carbon::parse($item->TimeIn) > Carbon::parse($item->STimeOfDay)) {
                $item->lateConvertToSecond = round(Carbon::parse($item->TimeIn)->diffInSeconds(Carbon::parse($item->STimeOfDay)) / (60 * 60), 2);
                $t1 = strtotime($item->TimeIn);
                $t2 = strtotime($item->STimeOfDay);
                $diff = gmdate('H:i:s', $t1 - $t2);
                $item->late = $diff;
                //dd($diff);
            
            }
            //lấy ra h đi sớm
            if ($item->TimeOut != Null && Carbon::parse($item->TimeOut) < Carbon::parse($item->ETimeOfDay)) {
                $item->soonConvertToSecond = round(Carbon::parse($item->TimeOut)->diffInSeconds(Carbon::parse($item->ETimeOfDay)) / (60 * 60), 2);
                $t1 = strtotime($item->TimeOut);
                $t2 = strtotime($item->ETimeOfDay);
                $diff = gmdate('H:i:s', $t2 - $t1);
                $item->soon = $diff;

            }
        }
        return $data;
    }

    //hàm tính toán lấy những ngày phải đi làm trong năm
    public function funCaculateWork($ODates, $SDates, $workDaysOfYear, $userId){
        /**
            Lấy ra số ngày chỉ đi làm trong năm
            + ko phải là t7, cn, và các ngày lễ
            + ko có ngày giờ đăng ký nghỉ phép
            + ngày đó ko có tgian check out và check in
            + ngày lấy ra phải nhỏ hơn ngày search hoặc ngày hiện tại và phải lớn hơn ngày ký HĐ hoặc mùng 1 đầu năm
         */

        $workDaysByUserID = [];
        //dd($workDaysOfYear);
        //lấy ra tất cả các ngày phải đi làm trong năm của từng nhân viên
        foreach($workDaysOfYear as $item){
            //$workDayCarbon = Carbon::createFromFormat('Y-m-d' ,$item);
            $workDayCarbon = Carbon::parse($item);
            if($workDayCarbon->greaterThanOrEqualTo($ODates) && $workDayCarbon->lessThanOrEqualTo($SDates)){
                array_push($workDaysByUserID, $item);
            }
        }

        $SYear = date('Y', strtotime($SDates));
        //lấy ra hết ngày chấm công của user
        $getTimeKeeping = $this->getTimeKeeping($userId, $SYear);
        if(($getTimeKeeping->isNotEmpty())){
            foreach($getTimeKeeping as $item){
                $lstDayKeepping[] = $item->Date;
            }
        }else{
            $lstDayKeepping[] = (array) null;
        }


        //list lọc những ngày phải đi làm nhưng không chấm công
        $listWork = [];
        //check xem những ngày phải đi làm nhưng lại ko có tgian chấm công
        foreach($workDaysByUserID as $item){
            //$workDayCarbon = Carbon::createFromFormat('Y-m-d', $item);
            if(in_array($item, $lstDayKeepping)){
                //array_push($listWord, $item);
            }else{
                array_push($listWork, $item);
            }
        }

        //dd($listWork);

        $allDayAbsence = [];
        //lấy ra hết ngày đã xin nghỉ phép
        $getTimeAbsence = $this->getTimeAbsence($userId);
        foreach($getTimeAbsence as $item){
            //dd($item->SDate);
            $sDateConvert = Carbon::parse($item->SDate)->format('Y-m-d');
            $eDateConvert = Carbon::parse($item->EDate)->format('Y-m-d');

            //lấy ra những ngày trong khoảng giữa 2 ngày search
            $period = CarbonPeriod::create($sDateConvert, $eDateConvert);
            
            foreach($period as $item2){
                array_push($allDayAbsence, $item2->format('Y-m-d'));
            }
        }

        $noTimeKeeping = [];
        //list lọc những ngày phải đi làm ko chấm công với ngày đã đăng ký nghỉ phép
        foreach($listWork as $item){
            if(in_array($item, $allDayAbsence)){
            }else{
                array_push($noTimeKeeping, $item);
            }
        }

        return $noTimeKeeping;
    }

    //lấy các ngày chấm công
    public function getTimeKeeping($userId, $sYear){
        $getTimeKeeping = DB::table('timekeepings_new')
            ->select('Date','TimeIn', 'TimeOut')
            ->where('userid' , '=' ,$userId)
            ->whereYear('Date', '=', $sYear)
            ->get();
        return $getTimeKeeping;
    }

    //lấy các ngày chấm công với điều kiện ngày tháng năm
    public function getTimeKeepingWithMonth($userId, $sYear, $sMonth, $sDay){
        $getTimeKeeping = DB::table('timekeepings_new')
            ->select('Date','TimeIn', 'TimeOut')
            ->where('userid' , '=' ,$userId)
            ->whereDay('Date' , '<=', $sDay)
            ->whereMonth('Date' , '<=', $sMonth)
            ->whereYear('Date', '=', $sYear)
            ->get();
        return $getTimeKeeping;
    }

    //lấy các ngày đky nghỉ phép
    public function getTimeAbsence($userId){
        $getTimeAbsence = DB::table('absences')
            ->select('SDate','EDate', 'MasterDataValue')
            ->where('UID' , '=' ,$userId)
            ->get();
        return $getTimeAbsence;
    }

    //lấy các ngày nghỉ lễ trong năm
    public function getEventCalendar($s_year){
        $data = DB::table('calendar_events')
            ->select('StartDate', 'EndDate', 'Content')
            ->where('CalendarID', '=', 1)
            ->where('Type', '=', 1)
            ->whereYear('StartDate', '=' ,$s_year)
            ->orderBy('StartDate','asc')
            ->get();
        return $data;
    }

    //Cột ko chấm công
    public function getNoTimeKeeping(Request $request){
        $s_year = date('Y', strtotime($request['date']));
        $s_month = date('m', strtotime($request['date']));
        $s_day = date('d', strtotime($request['date']));

        $o_year =  date('Y', strtotime($request['OfficeDate']));
        $o_month =  date('m', strtotime($request['OfficeDate']));
        $o_day =  date('d', strtotime($request['OfficeDate']));

        $userId = $request['UserID'];

        $start_years =  date('Y', strtotime($request['StartDate']));
        $start_months =  date('m', strtotime($request['StartDate']));
        $start_days =  date('d', strtotime($request['StartDate']));

        if($request['OfficeDate'] != null){
            $ODatesCarbon = Carbon::createFromFormat('Y-m-d', $o_year . '-' . $o_month . '-' . $o_day);
        }else{
            $ODatesCarbon = Carbon::createFromFormat('Y-m-d', $start_years . '-' . $start_months . '-' . $start_days);
        }
        $SearchDatesCarbon = Carbon::createFromFormat('Y-m-d', $s_year . '-' . $s_month . '-' . $s_day);

        //lấy ra những ngày cần đi làm trong năm
        $getEventCalendar = $this->getEventCalendar($s_year);
        $workDaysOfYear = [];
        for ($month = 1; $month <= 12; $month++) {
            $daysInMonth = Carbon::now()->month($month)->daysInMonth;
            for($days=1; $days <= $daysInMonth; $days++) {
                $date = Carbon::createFromFormat('Y-m-d', $s_year . '-' . $month . '-' . $days);
                if($date->dayOfWeek != 0 && $date->dayOfWeek != 6){
                    //lọc bỏ các ngày nghỉ lễ
                    foreach($getEventCalendar as $item){
                        $startDate = Carbon::createFromFormat('Y-m-d', $item->StartDate);
                        $endDate = Carbon::createFromFormat('Y-m-d', $item->EndDate);
                        if($date->gte($startDate) && $date->lte($endDate)){
                            $flag = true;
                            break;
                        }else{
                            $flag = false;
                        }
                    }
                    if($flag == false){
                        $workDaysOfYear[] = Carbon::createFromDate($date->year, $month, $days)->format('Y-m-d');
                    }
                }
            }
        }

        //lọc lấy ra những ngày thiếu check in hoặc check out 
        $noCheckInOrOut = $this->getTimeKeepingWithMonth($userId, $s_year, $s_month, $s_day);
        $noCheckInOrOutArr = [];
        foreach($noCheckInOrOut as $item){
            if(!$item->TimeIn || !$item->TimeOut){
                array_push($noCheckInOrOutArr, $item);
            }
        }

        //hàm tính toán lấy ra những ngày phải đi làm nhưng ko có chấm công
        $workDaysByUserID = $this->funCaculateWork($ODatesCarbon, $SearchDatesCarbon, $workDaysOfYear, $userId);

        //Trường hợp đặc biệt : trường hợp xin nghỉ phép (ko full) chỉ một khoảng tgian trong ngày
        $absenceNotFull = $this->getAbsenceNotFullDate($userId);
        //dd($absenceNotFull[1]['Day']);
        //dd($absenceNotFull);

        // echo 'ODatesCarbon'.$ODatesCarbon.'---'.'SearchDatesCarbon'.$SearchDatesCarbon.'---';
        // echo '<pre>';
        // print_r($workDaysOfYear);
        // echo '<pre>';
        // dd($workDaysByUserID);

        $collection = new Collection();
        foreach($workDaysByUserID as $item){
                $collection->push((object)['Date' => $item
            ]);
        }

        foreach($noCheckInOrOutArr as $item){
                $collection->push((object)[
                    'Date' => $item->Date,
                    'TimeIn' => $item->TimeIn,
                    'TimeOut' => $item->TimeOut,
            ]);
        }

        $sortedCollection = $collection->sortBy('Date');

        if($request['Type'] == 1){
            //$workDaysByUserID = $this->paginateArr($collection, 15, 1);
            $workDaysByUserID = $sortedCollection->values()->paginate(15, null, 1);
            $workDaysByUserID->userId = $userId;
            $workDaysByUserID->searchDate = $request['date'];
            $workDaysByUserID->OfficeDate = $request['OfficeDate'];
            $workDaysByUserID->StartDate = $request['StartDate'];
    
            return view("leave::includes.no-keeping")
            ->with(compact('workDaysByUserID'));
        }else{
            $workDaysByUserID = $sortedCollection->values()->paginate(15, null, $request['pageNums']);
            return $workDaysByUserID;
        }
    }

    public function paginateArr($items, $perPage = 4, $page = null)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $total = count($items);
        $currentpage = $page;
        $offset = ($currentpage * $perPage) - $perPage ;
        $itemstoshow = array_slice($items , $offset , $perPage);
        return new LengthAwarePaginator($itemstoshow ,$total ,$perPage, $currentpage);
    }

    public function getAbsenceNotFullDate($userId){
        //lấy ra hết ngày đã xin nghỉ phép
        $getTimeAbsence = $this->getTimeAbsence($userId);
        //dd($getTimeAbsence);
        // $user = User::query()
        //         ->select("STimeOfDay", "ETimeOfDay")
        //         ->where("id", $userId)
        //         ->get();

        //lấy ra giờ phải đi làm thực tế của user
        // foreach($user as $item){
        //     $userStartTime = $item->STimeOfDay;
        //     $userEndTime = $item->ETimeOfDay;
        // }
        $arrData = [];
        $getAbsenceNotFullDate = new stdClass();
        $getAbsenceNotFullDate->te = 5;
        foreach($getTimeAbsence as $item){
            $subArr = [];
            //lấy ra ngày bắt đầu và ngày kết thúc của xin nghỉ phép
            $startDate = Carbon::parse($item->SDate);
            $endDate = Carbon::parse($item->EDate);

            $startDateFormat = $startDate->format('Y-m-d');

            //lấy ra ngày chấm công của ngày có xin nghỉ phép và giờ mặc định phải đi làm của hôm đó
            $timeKeeping = TimekeepingNew::query()
            ->select("Date", "TimeIn", "TimeOut", "STimeOfDay", "ETimeOfDay")
            ->where("UserId", "=", $userId)
            ->where("Date", "=", $startDateFormat)
            //->where("Date", "=", "2022-11-10")
            ->first();

            //dd($timeKeeping);

            //check chỉ lấy những data # rỗng(là trường hợp có nghỉ phép và có cả tgian chấm công)
            if($timeKeeping != null && $timeKeeping->TimeIn != null && $timeKeeping->TimeOut != null){
                //dd($timeKeeping);
                //array_push($arrs, $timeKeeping);
                //ngày của ngày xin nghỉ phép
                $dayOfStartDate = date('d', strtotime($startDate));
                $dayOfEndDate = date('d', strtotime($endDate));

                //thời gian bắt đầu và kết thúc của xin nghỉ phép
                $dayAbsenceOfStartTime = date('H:i:s', strtotime($startDate));
                $dayAbsenceOfEndTime = date('H:i:s', strtotime($endDate));

                //nếu thời gian bắt đầu xin nghỉ phép nhỏ hơn thời gian phải đi làm thì 
                //thời gian bắt đầu xin nghỉ phép sẽ fix về thời gian phải đi làm
                if($dayAbsenceOfStartTime < $timeKeeping->STimeOfDay){
                    $dayAbsenceOfStartTime = $timeKeeping->STimeOfDay;
                }
                // và kết thúc của xin nghỉ phép mà lớn hơn thời gian phải check out thì
                //thời gian kết thúc nghỉ phép sẽ fix về t/gian phải check out
                if($dayAbsenceOfEndTime > $timeKeeping->ETimeOfDay){
                    $dayAbsenceOfEndTime = $timeKeeping->ETimeOfDay;
                }

                //TH1 : ngày SDate = ngày EDate(chỉ nghỉ trong 1 ngày)
                if($dayOfStartDate == $dayOfEndDate){
                    $subArr['Day'] = $startDateFormat;
                    //check tgian nghỉ phải trong khoảng tgian phải đi làm
                    if($dayAbsenceOfStartTime >= $timeKeeping->STimeOfDay && $dayAbsenceOfEndTime <= $timeKeeping->ETimeOfDay){
                        //Chia tiếp ra 3 trường hợp
                        //TH1 :nghỉ nửa trước(H bắt đầu xin nghỉ = H phải chấm công)
                        if($dayAbsenceOfStartTime == $timeKeeping->STimeOfDay && $dayAbsenceOfEndTime < $timeKeeping->ETimeOfDay){
                            //check xem có đủ cả check in check out ko thiếu thì vào trg hợp ko chấm công
                            if($timeKeeping->TimeOut != null && $timeKeeping->TimeIn != null){
                                //check đi muộn về sớm
                                //nếu h check in muộn hơn h kết thúc xin nghỉ thì là (đi muộn)
                                if($timeKeeping->TimeIn > $dayAbsenceOfEndTime){
                                    //dd($timeKeeping->TimeIn, $dayAbsenceOfEndTime);
                                    $lateTime = gmdate( "H:i:s", strtotime($timeKeeping->TimeIn) - strtotime($dayAbsenceOfEndTime));
                                    $subArr['lateTime'] = $lateTime;
                                    //dd($lateTime);
                                }
                                //nếu h check out mà sớm hơn h phải check out mặc định thì là (về sớm)
                                if($timeKeeping->TimeOut < $timeKeeping->ETimeOfDay){
                                    $soonTime = gmdate( "H:i:s", strtotime($timeKeeping->ETimeOfDay) - strtotime($timeKeeping->TimeOut));
                                    //trừ thêm 1 tiếng h nghỉ trưa
                                    $soonTimes = gmdate( "H:i:s", strtotime($soonTime) - strtotime("01:00:00"));
                                    $subArr['soonTime'] = $soonTimes;
                                    //dd($soonTimes);
                                }
                                array_push($arrData , $subArr);
                            }else{
                                //check ko chấm công(1 trong 2 h check out check in ko có)
                                if(($timeKeeping->TimeOut == null || $timeKeeping->TimeIn == null) || ($timeKeeping->TimeOut == null && $timeKeeping->TimeIn == null)){
                                    //dd('thieu smth');
                                }
                            }
                        }
                        //TH2 : nghỉ giữa ngày
                        else if($dayAbsenceOfStartTime > $timeKeeping->STimeOfDay && $dayAbsenceOfEndTime < $timeKeeping->ETimeOfDay){
                            //dd('smth');
                        }   
                        //TH3 : nghỉ nửa sau(H kết thúc xin nghỉ = H phải kết thúc chấm công)
                        else if($dayAbsenceOfStartTime > $timeKeeping->STimeOfDay && $dayAbsenceOfEndTime == $timeKeeping->ETimeOfDay){
                            //dd('smth');

                        }
                        //TH4 : nghỉ full cả ngày(ko xử lý)
                    }
                }
                //TH2 : ngày SDate # ngày EDate(tức nghỉ nhiều ngày)
                else{
                    
                }



            }
            //trường hợp đăng ký nghỉ 1 khoảng trong ngày nhưng ko có check in check out
            else{

            }

            
        }
        dd($arrData);
        return $arrData;

    }
}
