<?php

namespace App\Http\Controllers\Admin;

use App\CalendarEvent;
use App\Model\Absence;
use App\Exports\TimekeepingExport;
use App\Exports\TimekeepingAbsencesExport;
use App\Imports\TimekeepingImport;
use App\Menu;
use App\RoleScreenDetail;
use App\Timekeeping;
use App\MasterData;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
// define('TimeInAM', '08:30');
// define('TimeOutAM', '12:00');
// define('TimeInPM', '13:00');
// define('TimeOutPM', '17:30');
class TimekeepingController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $export;
    protected $import;
    const KEYMENU= array(
        "add" => "TimekeepingAdd",
        "view" => "Timekeeping",
        "edit" => "TimekeepingEdit",
        "delete" => "TimekeepingDelete",
        "export" => "TimekeepingExport",
        "import" => "TimekeepingImport",
    );
    public function __construct(Request $request)
    {
        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
        $array = $this->RoleView('Timekeeping',['Timekeeping']);
        $this->data['menu'] =$array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if($value == $row->alias)
                    $this->$key = $row;
            }
        }
    }

    /**
     * View screen timekeeping
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, $orderBy = 'Date', $sortBy = 'asc') {
        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['users1'] = User::query()->select('id','FullName','Active')->where('role_group', '!=', self::USER_ROOT_GROUP)->get();

        if(isset($request->all()['UserID'])){
            $this->data['checkUser'] = User::find($request->all()['UserID']);
        }else{
            $this->data['checkUser'] = User::find(Auth::user()->id);
        }

        $Master1 = MasterData::where('DataValue','WT001')->first();
        $Master2 = MasterData::where('DataValue','WT002')->first();

        $TimeInAM = $Master1 ? $Master1->Name : '08:30';
        $TimeOutPM = $Master1 ? $Master1->DataDescription : '17:30';
        $TimeOutAM = $Master2 ? $Master2->Name : '12:00';
        $TimeInPM = $Master2 ? $Master2->DataDescription : '13:00';

        $totalTimeWork = (Carbon::parse($TimeInAM)->diffInMinutes(Carbon::parse($TimeOutAM))
            + Carbon::parse($TimeInPM)->diffInMinutes(Carbon::parse($TimeOutPM)))/60;
        if(null !== $request->input('UserID')) {
            $userid = User::find($request->input('UserID'));
        } else {
            $userid = User::find(Auth::user()->id);
        }

        $this->data['request'] = $request->query();

        if ($request->has('time')) {
            $time = '01/'.$request['time'];
            if (\DateTime::createFromFormat('d/m/Y', $time) !== FALSE) {
                $date = date_create($this->fncDateTimeConvertFomat(('01/'.$request['time']),self::FOMAT_DISPLAY_DMY,self::FOMAT_DB_YMD));
                $month = date_format($date,"m");
                $year = date_format($date,"Y");
            } else {
                return Redirect::back()->withErrors(['Tìm kiếm không hợp lệ']);
            }
        }

        $this->data['timekeepings'] = Timekeeping::query()
            ->whereMonth('Date', $request->has('time') ? $month : Carbon::now()->month)
            ->whereYear('Date', $request->has('time') ? $year : Carbon::now()->year)
            ->where('UserID', $request->has('UserID') ? $this->data['request']['UserID'] : Auth::user()->id )
            ->orderBy($orderBy, $sortBy)->get();
        $this->data['userSelect'] = User::find($request->has('UserID') ? $this->data['request']['UserID'] : Auth::user()->id);

        if(count($this->data['timekeepings']) > 0 ) {
            $TimeInAM = $this->data['timekeepings']->first()->STimeOfDay;
            if(isset($userid->ETimeOfDay)) {
                $TimeOutPM = $this->data['timekeepings']->first()->ETimeOfDay;
            } else {
                $TimeOutPM = date('H:i:s',strtotime('+'.($totalTimeWork+(Carbon::parse($TimeOutAM)->diffInMinutes(Carbon::parse($TimeInPM)))/60).'hour',strtotime($this->data['timekeepings']->first()->STimeOfDay)));
            }
        }
        else{
            $TimeInAM = $userid->STimeOfDay;
            if(isset($userid->ETimeOfDay)) {
                $TimeOutPM = $userid->ETimeOfDay;
            } else {
                $TimeOutPM = date('H:i:s',strtotime('+'.($totalTimeWork+(Carbon::parse($TimeOutAM)->diffInMinutes(Carbon::parse($TimeInPM)))/60).'hour',strtotime($userid->STimeOfDay)));
            }
        }

        $this->data['timekeepings']->totalKeeping = 0;
        $this->data['timekeepings']->overKeeping = 0;
        $this->data['timekeepings']->lateTimes = 0;
        $this->data['timekeepings']->lateHours = 0;
        $this->data['timekeepings']->soonTimes = 0;
        $this->data['timekeepings']->soonHours = 0;

        foreach($this->data['timekeepings'] as $item) {
            $dayOfTheWeek = Carbon::parse($item->Date)->dayOfWeek;
            $item->weekday = self::WEEK_MAP[$dayOfTheWeek];

            $item->calendarEvent = CalendarEvent::query()
                ->where('Type', '=', 0)
                ->where('StartDate', '<=' , Carbon::parse($item->Date)->toDateString())
                ->where('EndDate', '>=', Carbon::parse($item->Date)->toDateString())->first();

            $check_event = false;
            if (in_array($dayOfTheWeek, [0,6]) && $item->calendarEvent) {
                $check_event = true;
            }

            if($item->TimeIn != Null && Carbon::parse($item->TimeIn) > Carbon::parse($TimeInAM) && ((!in_array($dayOfTheWeek, [0,6])) || $check_event)) {
                $item->late = Carbon::parse($item->TimeIn)->diffInMinutes(Carbon::parse($TimeInAM));
                $this->data['timekeepings']->lateTimes += 1;
                $this->data['timekeepings']->lateHours += $item->late/60;
            } else
                $item->late = 0;

            if($item->TimeOut != Null && Carbon::parse($item->TimeOut) < Carbon::parse($TimeOutPM) && ((!in_array($dayOfTheWeek, [0,6])) || $check_event)) {
                $item->soon = Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($TimeOutPM));
                $this->data['timekeepings']->soonTimes += 1;
                $this->data['timekeepings']->soonHours += $item->soon/60;
            } else {
                $item->soon = 0;
            }
            if($item->TimeOut != Null &&  Carbon::parse($item->TimeOut) > Carbon::parse($TimeOutPM) && ((!in_array($dayOfTheWeek, [0,6])) || $check_event)) {
                $item->N = Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($TimeOutPM));
                $this->data['timekeepings']->overKeeping += $item->N/60;
            } else {
                $item->N = 0;
            }
            //thời gian làm việc
            if(isset($userid->ETimeOfDay) && ((!in_array($dayOfTheWeek, [0,6])) || $check_event)) {
                $floatWorkHours = (Carbon::parse($TimeInAM)->diffInMinutes(Carbon::parse($TimeOutAM)) + Carbon::parse($TimeInPM)->diffInMinutes(Carbon::parse($TimeOutPM)))/60;
            } else {
                $floatWorkHours = $totalTimeWork;
            }

            if(!is_null($item->TimeIn) && !is_null($item->TimeOut) && ((!in_array($dayOfTheWeek, [0,6])) || $check_event)) {
                //trường hợp vào ra ngoài thời gian làm việc
                if($item->TimeOut < $TimeInAM || $item->TimeIn > $TimeOutPM
                || ($item->TimeIn > $TimeOutAM && $item->TimeOut < $TimeInPM)) {
                    $item->hours = 0;
                } elseif ($item->TimeIn <= $TimeOutAM && $item->TimeOut >= $TimeInPM) {
                    $item->hours = Carbon::parse($item->TimeIn < $TimeInAM ? $TimeInAM : $item->TimeIn)->diffInMinutes(Carbon::parse($item->TimeOut < $TimeOutPM ? $item->TimeOut : $TimeOutPM))/60
                    - Carbon::parse($TimeInPM)->diffInMinutes(Carbon::parse($TimeOutAM))/60;
                    $item->keeping = $item->hours/$floatWorkHours;
                    $this->data['timekeepings']->totalKeeping += $item->keeping;
                } else {
                    $item->hours = Carbon::parse(
                        $item->TimeIn > $TimeOutAM // >12h
                        ? ($item->TimeIn < $TimeInPM ? $TimeInPM : $item->TimeIn)
                        : ($item->TimeIn < $TimeInAM ? $TimeInAM : $item->TimeIn)
                    )
                    ->diffInMinutes(Carbon::parse(
                        $item->TimeOut < $TimeInPM
                        ? ($item->TimeOut > $TimeOutAM ? $TimeOutAM : $item->TimeOut)
                        : ($item->TimeOut > $TimeOutPM ? $TimeOutPM : $item->TimeOut)
                        ))/60;
                    $item->keeping = $item->hours/$floatWorkHours;
                    $this->data['timekeepings']->totalKeeping += $item->keeping;
                }

            }

            //vang mat
            $item->absence = Absence::query()
                ->select('absences.*', 'master_data.Name')
                ->join('master_data', 'master_data.DataValue', 'absences.MasterDataValue')
                ->where('UID', $request->has('UserID') ? $this->data['request']['UserID'] : Auth::user()->id)
                ->where('SDate', '<=' , Carbon::parse($item->Date)->endOfDay())
                ->where('EDate', '>=', Carbon::parse($item->Date)->startOfDay())->get();

            foreach($item->absence as $absence) {
                if(Carbon::parse($absence->SDate)->day == Carbon::parse($item->Date)->day) {
                    $absence->STime = Carbon::parse($absence->SDate)->format('H:i');
                    if($absence->STime < $TimeInAM) {
                        $absence->STime = $TimeInAM;
                    }
                } else {
                    $absence->STime = $TimeInAM;
                }
                if(Carbon::parse($absence->EDate)->day == Carbon::parse($item->Date)->day) {
                    $absence->ETime = Carbon::parse($absence->EDate)->format('H:i');
                } else {
                    $absence->ETime = $TimeOutPM;
                }
            }
        }
        $this->data['add']      = $this->add;
        $this->data['edit']     = $this->edit;
        $this->data['delete']   = $this->delete;
        $this->data['export']   = $this->export;
        $this->data['import']   = $this->import;

        if (strpos(\Request::getRequestUri(), 'api') !== false) {
            return $this->data;
        }
        return $this->viewAdminLayout('timekeeping', $this->data);
    }
    public function export($month, $year,$user)
    {
        $time = $year.'-'.$month;

        $monthYear =  $month.'-'.$year;
        $User = explode(",", $user);
        $records = User::query()
            ->join('timekeepings', 'timekeepings.UserID', 'users.id')
            ->where('Date', 'like', '%'.$time.'%')
            ->where(function ($query) use ($User) {
                foreach($User as $value) {
                    $query->orWhere('UserID',$value);
                }
            })
            ->groupBy('users.id')
            ->select('users.FullName', 'users.IDFM', 'users.id as UserID', 'timekeepings.*')
            ->get();
        if($records->count() > 0) {
            return Excel::download(new TimekeepingExport($month, $year,$user), 'Timekeeping'.$monthYear.'.xlsx');
        } else {
            return $this->jsonErrors(['Không có dữ liệu!']);
        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    public function import(Request $request)
    {
        try{
            if(null != request()->file('file')) {
                // Excel::import(new TimekeepingImport(),request()->file('file'));
                Excel::import(new TimekeepingImport($request),request()->file('file'));
            }

        }
        catch(\Maatwebsite\Excel\Validators\ValidationException $e) {
            return $e->getMessage();
        }

        return back();
    }

    /**
     * show popup edit,insert timekeeping
     * @param null $id
     * @param null $del
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|int|string
     */
    public function detailTimekeeping(Request $request, $id = null, $del = null) {
        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['checkUser'] = User::find(Auth::user()->id);
        if(null !== $request->input('searchUser')) {
            $this->data['searchUser'] =  $request->input('searchUser');
        }
        if($id != null) {
            if($del == 'del') {
                $one = Timekeeping::find($id);
                if($one != null) {
                    $one->delete();
                    if (strpos(\Request::getRequestUri(), 'api') !== false) {
                        return response()->json(['success' => 'Lưu thành công.']);
                    }
                }
                return 1;
            }
            $this->data['timekeepingInfo'] = Timekeeping::find($id);
            $this->data['IsUser'] = User::query()->select('id', 'FullName')->where('id',$this->data['timekeepingInfo']['UserID'])->first();

            if($this->data['timekeepingInfo']) {
                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                    return $this->data;
                }
                return $this->viewAdminIncludes('timekeeping-detail', $this->data);
            } else {
                return "";
            }
        } else {
            if (strpos(\Request::getRequestUri(), 'api') !== false) {
                return $this->data;
            }
            return $this->viewAdminIncludes('timekeeping-detail', $this->data);
        }
    }

    /**
     * process insert, update
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string|void
     */
    public function saveTimekeeping(Request $request)
    {
        if (count($request->input()) == 0) {
            return abort('404');
        }
        try{
            $arrayValidator = [
                'UserID'  => 'integer|min:1',
                'Date'    => 'required|date_format:d/m/Y',
                'TimeIn'  => 'required|date_format:H:i',
                'TimeOut' => 'required|date_format:H:i',
            ];

            $modeIsUpdate = array_key_exists('id', $request->input());

            if ($modeIsUpdate) {
                $arrayValidator['id'] = 'integer|min:1';
            }
            $arrT = explode(',',$request->input('Date'));
            foreach($arrT as $key => $value){
                $arrayValidator['UserID'] = $request->input('UserID');
                $arrayValidator['Date'] = $value;
                $arrayValidator['TimeIn'] = $request->input('TimeIn');
                $arrayValidator['TimeOut'] = $request->input('TimeOut');
                // $validator = Validator::make($request->all(),$arrayValidator);
                // if ($validator->fails())
                // {
                //     return response()->json(['errors'=>$validator->errors()->first()]);
                // }
                $validated = $arrayValidator;

                $timekeepings = Timekeeping::query()->where('UserID', $validated['UserID'])
                    ->where('Date', $this->fncDateTimeConvertFomat($validated['Date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))->first();

                if($timekeepings != [] && !$modeIsUpdate) {
                    return $this->jsonErrors('Dữ liệu đã tồn tại');
                }
                if (strtotime($validated['TimeIn'])-strtotime($validated['TimeOut']) >= 0) {
                    return $this->jsonErrors('Thời gian không hợp lệ');
                }

                $one = !$modeIsUpdate ? new Timekeeping() : Timekeeping::find($validated['id']);

                foreach($validated as $key => $value) {
                    if(Schema::hasColumn('timekeepings', $key)) {
                        if ($key == 'Date') {
                            $value = $this->fncDateTimeConvertFomat($value, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                        }
                        $one->$key = $value;
                    }
                }
                $userid = User::find($validated['UserID']);
                $master = MasterData::where('DataValue','WT001')->first();
                if(isset($userid->STimeOfDay)) {
                    $one->STimeOfDay = $userid->STimeOfDay;
                } else {
                    $one->STimeOfDay = $master->Name;
                }

                if(isset($userid->STimeOfDay)) {
                    $one->ETimeOfDay = $userid->ETimeOfDay;
                } else {
                    $one->ETimeOfDay = $master->DataDescription;
                }
                $one->save();
                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                    return response()->json(['success' => 'Lưu thành công.']);
                }
                $this->jsonSuccessWithRouter('admin.Timekeeping');
            }

        }
        catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * show
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function absenceTimekeeping(Request $request) {
        $this->data['absenceTimekeeping'] = Absence::query()->select('absences.*', 'master_data.Name')
            ->join('master_data', 'master_data.DataValue', 'absences.MasterDataValue')
            ->where('UID', isset($request['UserID']) ? $request['UserID'] : Auth::user()->id)
            ->where('SDate', '<=' , Carbon::parse($request['date'])->endOfDay())
            ->where('EDate', '>=', Carbon::parse($request['date'])->startOfDay())->get();
        if(null !== $request->input('UserID')) {
            $userid = User::find($request->input('UserID'));
        } else {
            $userid = User::find(Auth::user()->id);
        }
        $TimeInAM = $userid->STimeOfDay;
        if(isset($userid->ETimeOfDay)) {
            $TimeOutPM = $userid->ETimeOfDay;
        } else {
            $TimeOutPM = date('H:i:s',strtotime('+9 hour',strtotime($userid->STimeOfDay)));
        }
        $absenceTimekeeping = $this->data['absenceTimekeeping'];
        foreach($absenceTimekeeping as &$absence) {
            if(Carbon::parse($absence->SDate)->day == Carbon::parse($request['date'])->day) {
                $absence->STime = Carbon::parse($absence->SDate)->format('H:i:s');
                if($absence->STime < $TimeInAM) {
                    $absence->STime = $TimeInAM;
                }
            } else {
                $absence->STime = $TimeInAM;
            }
            if(Carbon::parse($absence->EDate)->day == Carbon::parse($request['date'])->day) {
                $absence->ETime = Carbon::parse($absence->EDate)->format('H:i:s');
            } else {
                $absence->ETime = $TimeOutPM;
            }
            if ((Carbon::parse($absence->STime)->hour && Carbon::parse($absence->ETime)->hour) <= 12 ||(Carbon::parse($absence->STime)->hour && Carbon::parse($absence->ETime)->hour) >= 12) {
                $absence->hours = Carbon::parse($absence->ETime)->diffInMinutes(Carbon::parse($absence->STime))/60;
            }
            if(Carbon::parse($absence->STime)->hour <= 12 && Carbon::parse($absence->ETime)->hour >= 12) {
                $absence->hours = Carbon::parse($absence->ETime)->diffInMinutes(Carbon::parse($absence->STime))/60 - 1;
            }
        }
        return $this->viewAdminIncludes('timekeeping-absence-detail', $this->data);
    }
    public function exportabsence($month, $year,$user)
    {
        $time = $year.'-'.$month;

        $monthYear =  $month.'-'.$year;
        $User = explode(",", $user);
        $records = User::query()
            ->join('timekeepings', 'timekeepings.UserID', 'users.id')
            ->where('Date', 'like', '%'.$time.'%')
            ->where(function ($query) use ($User) {
                foreach($User as $value){
                    $query->orWhere('UserID',$value);
                }
            })
            ->groupBy('users.id')
            ->orderBy('users.id', 'asc')
            ->select('users.FullName', 'users.IDFM', 'users.id as UserID', 'timekeepings.*')
            ->get();
        if($records->count() > 0) {
            return Excel::download(new TimekeepingAbsencesExport($month, $year,$records), 'Tổng hợp tháng'.$monthYear.'.xlsx');
        } else {
            return $this->jsonErrors(['Không có dữ liệu!']);
        }

    }
}
