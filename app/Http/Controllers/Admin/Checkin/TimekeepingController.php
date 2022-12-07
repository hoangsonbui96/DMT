<?php

namespace App\Http\Controllers\Admin\Checkin;

use App\CalendarEvent;
use App\Exports\TimekeepingNewAbsencesExport;
use App\Exports\TimekeepingNewExport;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Export\Excel\TimeKeeping\ExportExcelTimeKeepingController;
use App\Http\Services\Timekeeping\TimekeepingService;
use App\Imports\TimekeepingNewImport;
use App\MasterData;
use App\Model\Absence;
use App\Model\CheckinHistory;
use App\Model\Timekeeping;
use App\Model\TimeKeepingAllDay;
use App\RoleScreenDetail;
use App\TimekeepingNew;
use App\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class TimekeepingController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $viewHistory;
    protected $export;
    protected $import;
    const KEYMENU = array(
        "add" => "TimekeepingAdd",
        "view" => "Timekeeping",
        "edit" => "TimekeepingEdit",
        "delete" => "TimekeepingDelete",
        "export" => "TimekeepingExport",
        "import" => "TimekeepingImport",
        "viewHistory" => "TimekeepingHistory",
    );

    const FOMAT_DISPLAY_DMY = 'd/m/Y';
    const FOMAT_DB_YMDHI = 'Y-m-d H:i';
    const FOMAT_DB_YMDHIS = 'Y-m-d H:i:s';

    public function __construct(Request $request)
    {
        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
        $array = $this->RoleView('TimekeepingNew', ['TimekeepingNew', 'Timekeeping', 'TimekeepingHistory']);
        $this->data['menu'] = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if ($value == $row->alias)
                    $this->$key = $row;
            }
        }
        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
    }

    /**
     * View screen timekeeping
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, $orderBy = 'Date', $sortBy = 'asc')
    {
        $this->data['users1'] = User::query()->select('id', 'FullName', 'Active')->where('role_group', '!=', self::USER_ROOT_GROUP)->get();
        $this->data['checkUser'] = Auth::user();

        if (null !== $request->input('UserID')) {
            $user = User::find($request->input('UserID'));
        } else {
            $user = Auth::user();
        }

        $this->data['request'] = $request->query();
        $day = Carbon::now()->day;
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        if ($request->has('time')) {
            $time = '01/' . $request['time'];
            if (\DateTime::createFromFormat('d/m/Y', $time) !== FALSE) {
                $date = date_create($this->fncDateTimeConvertFomat(('01/' . $request['time']), self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                $day = Carbon::parse($date)->endOfMonth()->format('d');
                $month = date_format($date, "m");
                $year = date_format($date, "Y");
            } else {
                return Redirect::back()->withErrors(['Tìm kiếm không hợp lệ']);
            }
        }

        $this->data['timekeepings'] = TimeKeepingAllDay::query()
            ->leftJoin('timekeepings_new', function ($q) use ($user, $month, $year) {
                $q->on('timekeepings_all_day.id', '=', 'timekeepings_new.Day')
                    ->whereMonth('timekeepings_new.Date', $month)
                    ->whereYear('timekeepings_new.Date', $year)
                    ->where('timekeepings_new.UserID', $user->id);
            })
            ->where('timekeepings_all_day.id', '<=', $day)
            ->select('timekeepings_all_day.id as DateID', 'timekeepings_new.*')
            ->orderBy('timekeepings_all_day.id', $sortBy)->get();
        $this->timekeeping($request, $user, $month, $year);
        $this->data['userSelect'] = $user;
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;
        $this->data['export'] = $this->export;
        $this->data['import'] = $this->import;

        //dd($this->data['timekeepings']);

        return $this->viewAdminLayout('checkin.timekeeping', $this->data);
    }

    public function export($month, $year, $user)
    {
        $time = $year . '-' . $month;
        $monthYear = $month . '-' . $year;
        $User = explode(",", $user);
        $records = User::query()
            ->join('timekeepings_new', 'timekeepings_new.UserID', 'users.id')
            ->where('Date', 'like', '%' . $time . '%')
            ->where(function ($query) use ($User) {
                foreach ($User as $value) {
                    $query->orWhere('UserID', $value);
                }
            })
            ->groupBy('users.id')
            ->select('users.FullName','users.username', 'users.IDFM', 'users.id as UserID', 'timekeepings_new.*')
            ->get();
        if ($records->count() > 0) {
            return Excel::download(new TimekeepingNewExport($month, $year, $user), 'Chấm công T' . $monthYear . '.xlsx');
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
        try {
            if (null != request()->file('file')) {
                // Excel::import(new TimekeepingImport(),request()->file('file'));
                Excel::import(new TimekeepingNewImport($request), request()->file('file'));
            }
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
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
    public function detailTimekeeping(Request $request, $id = null, $del = null)
    {
        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['checkUser'] = Auth::user();
        if (null !== $request->input('searchUser')) {
            $this->data['searchUser'] = $request->input('searchUser');
        }
        if ($id != null) {
            if ($del == 'del') {
                $one = Timekeeping::find($id);
                if ($one != null) {
                    $one->delete();
                    if (strpos(\Request::getRequestUri(), 'api') !== false) {
                        return response()->json(['success' => 'Lưu thành công.']);
                    }
                }
                return 1;
            }
            $this->data['timekeepingInfo'] = Timekeeping::find($id);
            $this->data['IsUser'] = User::query()->select('id', 'FullName')->where('id', $this->data['timekeepingInfo']['UserID'])->first();

            if ($this->data['timekeepingInfo']) {
                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                    return $this->data;
                }
                return $this->viewAdminIncludes('checkin.timekeeping-detail', $this->data);
            } else {
                return "";
            }
        } else {
            $timekeeping = new Timekeeping();
            $timekeeping->STimeOfDay = $this->data['checkUser']->STimeOfDay;
            $timekeeping->ETimeOfDay = $this->data['checkUser']->ETimeOfDay;
            $timekeeping->UserID = isset($request['searchUser']) ? $request['searchUser'] : Auth::user()->id;

            if (isset($request['date'])) {
                $timekeeping->Date = $request['date'];
            }
            $this->data['timekeepingInfo'] = $timekeeping;
            if (strpos(\Request::getRequestUri(), 'api') !== false) {
                return $this->data;
            }
            return $this->viewAdminIncludes('checkin.timekeeping-detail', $this->data);
        }
    }

    /**
     * process insert, update
     * @param Request $request
     * @return JsonResponse|string|void
     */
    public function saveTimekeeping(Request $request)
    {
        if (count($request->input()) == 0) {
            return abort('404');
        }
        try {
            $arrayValidator = [
                'UserID' => 'integer|min:1',
                'Date' => 'required|date_format:d/m/Y',
                'TimeIn' => 'required',
                'TimeOut' => 'nullable',
                'IsInCpn' => 'required|integer'
            ];

            $modeIsUpdate = array_key_exists('id', $request->input());

            if ($modeIsUpdate) {
                $arrayValidator['id'] = 'integer|min:1';
            }

            $validator = Validator::make($request->all(), $arrayValidator);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()->first()]);
            }
            $validated = $validator->validate();
            $timekeepings = Timekeeping::query()->where('UserID', $validated['UserID'])
                ->where('Date', $this->fncDateTimeConvertFomat($validated['Date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))->first();

            if ($timekeepings != [] && !$modeIsUpdate) {
                return $this->jsonErrors('Dữ liệu đã tồn tại');
            }
            if ($validated['TimeOut'] != null && (strtotime($validated['TimeIn']) - strtotime($validated['TimeOut'])) >= 0) {
                return $this->jsonErrors('Thời gian không hợp lệ');
            }
            $one = !$modeIsUpdate ? new Timekeeping() : Timekeeping::find($validated['id']);
            $one->UserUpdateID = Auth::user()->id;
            foreach ($validated as $key => $value) {
                if (Schema::hasColumn('timekeepings_new', $key)) {
                    if ($key == 'Date') {
                        $value = $this->fncDateTimeConvertFomat($value, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                    }
                    $one->$key = $value;
                }
            }
            $userid = User::find($validated['UserID']);
            $master = MasterData::where('DataValue', 'WT001')->first();
            if (isset($userid->STimeOfDay)) {
                $one->STimeOfDay = $userid->STimeOfDay;
            } else {
                $one->STimeOfDay = $master->Name;
            }

            if (isset($userid->STimeOfDay)) {
                $one->ETimeOfDay = $userid->ETimeOfDay;
            } else {
                $one->ETimeOfDay = $master->DataDescription;
            }

            if (!$modeIsUpdate) {
                $one->Day = Carbon::parse($one->Date)->format('d');
            }
            $one->SBreakOfDay = Auth::user()->SBreakOfDay;
            $one->EBreakOfDay = Auth::user()->EBreakOfDay;
            $one->save();
            if (strpos(\Request::getRequestUri(), 'api') !== false) {
                return response()->json(['success' => 'Lưu thành công.']);
            }
            $this->jsonSuccessWithRouter('admin.Timekeeping');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * show
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function absenceTimekeeping(Request $request)
    {
        $this->data['absenceTimekeeping'] = Absence::query()->select('absences.*', 'master_data.Name')
            ->join('master_data', 'master_data.DataValue', 'absences.MasterDataValue')
            ->where('UID', isset($request['UserID']) ? $request['UserID'] : Auth::user()->id)
            ->where('SDate', '<=', Carbon::parse($request['date'])->endOfDay())
            ->where('EDate', '>=', Carbon::parse($request['date'])->startOfDay())->get();
        if (null !== $request->input('UserID')) {
            $userid = User::find($request->input('UserID'));
        } else {
            $userid = User::find(Auth::user()->id);
        }
        $TimeInAM = $userid->STimeOfDay;
        if (isset($userid->ETimeOfDay)) {
            $TimeOutPM = $userid->ETimeOfDay;
        } else {
            $TimeOutPM = date('H:i:s', strtotime('+9 hour', strtotime($userid->STimeOfDay)));
        }
        $absenceTimekeeping = $this->data['absenceTimekeeping'];
        foreach ($absenceTimekeeping as &$absence) {
            if (Carbon::parse($absence->SDate)->day == Carbon::parse($request['date'])->day) {
                $absence->STime = Carbon::parse($absence->SDate)->format('H:i:s');
                if ($absence->STime < $TimeInAM) {
                    $absence->STime = $TimeInAM;
                }
            } else {
                $absence->STime = $TimeInAM;
            }
            if (Carbon::parse($absence->EDate)->day == Carbon::parse($request['date'])->day) {
                $absence->ETime = Carbon::parse($absence->EDate)->format('H:i:s');
            } else {
                $absence->ETime = $TimeOutPM;
            }
            if ((Carbon::parse($absence->STime)->hour && Carbon::parse($absence->ETime)->hour) <= 12 || (Carbon::parse($absence->STime)->hour && Carbon::parse($absence->ETime)->hour) >= 12) {
                $absence->hours = Carbon::parse($absence->ETime)->diffInMinutes(Carbon::parse($absence->STime)) / 60;
            }
            if (Carbon::parse($absence->STime)->hour <= 12 && Carbon::parse($absence->ETime)->hour >= 12) {
                $absence->hours = Carbon::parse($absence->ETime)->diffInMinutes(Carbon::parse($absence->STime)) / 60 - 1;
            }
        }
        return $this->viewAdminIncludes('checkin.timekeeping-absence-detail', $this->data);
    }

    /**
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return \Illuminate\Support\Facades\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showHistory(Request $request, $orderBy = 'RequestTime', $sortBy = 'desc')
    {
        $this->authorize('action', $this->viewHistory);

        $this->history($request, $orderBy, $sortBy);

        return $this->viewAdminLayout('checkin.checkin-history', $this->data);
    }

    //API

    /**
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return \Illuminate\Http\RedirectResponse
     */
    public function indexApi(Request $request, $orderBy = 'Date', $sortBy = 'asc')
    {
        $this->authorize('action', $this->view);

        if (null !== $request->input('UserID')) {
            $user = User::find($request->input('UserID'));
        } else {
            $user = Auth::user();
        }

        $date = $request->has('time') ? Carbon::createFromFormat('m/Y', $request['time'])->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $month = Carbon::parse($date)->format('m');
        $year = Carbon::parse($date)->format('Y');

        $this->data['timekeepings'] = Timekeeping::query()
            ->whereMonth('Date', $month)
            ->whereYear('Date', $year)
            ->where('UserID', $user->id)
            ->orderBy($orderBy, $sortBy)->get();

        $this->timekeeping($request, $user, $month, $year);

        $data = $this->data;
        $data['users1'] = User::query()->select('id', 'FullName', 'Active')->where('role_group', '!=', self::USER_ROOT_GROUP)->orderBy('username')->get();
        $data['checkUser'] = Auth::user();
        $data['request'] = $request->query();
        $data['userSelect'] = $user;
        $data['role_key'] = 'TimekeepingNew';

        $data['totalkeeping']['totalKeeping'] = $this->data['timekeepings']->totalKeeping;
        $data['totalkeeping']['overKeeping'] = $this->data['timekeepings']->overKeeping;
        $data['totalkeeping']['lateTimes'] = $this->data['timekeepings']->lateTimes;
        $data['totalkeeping']['lateHours'] = $this->data['timekeepings']->lateHours;
        $data['totalkeeping']['soonTimes'] = $this->data['timekeepings']->soonTimes;
        $data['totalkeeping']['soonHours'] = $this->data['timekeepings']->soonHours;

        $newData = [];
        foreach ($this->data['timekeepings'] as $value) {
            if ($value['TimeIn'] == null && $value['TimeOut'] == null) {

            } else {
                $newData[] = $value;
            }
        }

        $data['timekeepings'] = $newData;

        return AdminController::responseApi(200, null, null, $data);
    }

    /**
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showHistoryApi(Request $request, $orderBy = 'RequestTime', $sortBy = 'desc')
    {
        $this->authorize('action', $this->viewHistory);

        $this->history($request, $orderBy, $sortBy);
        $data = $this->data;
        $data['role_key'] = 'TimekeepingHistory';

        return AdminController::responseApi(200, null, null, $data);
    }

    //TimekeepingController

    /**
     * @param $request
     * @param $user
     * @param $month
     * @param $year
     */
    public function timekeeping($request, $user, $month, $year)
    {
        $Master1 = MasterData::where('DataValue', 'WT001')->first();
        $Master2 = MasterData::where('DataValue', 'WT002')->first();

        $TimeInAM = $Master1 ? $Master1->Name : '08:30';
        $TimeOutPM = $Master1 ? $Master1->DataDescription : '17:30';
        $TimeOutAM = $Master2 ? $Master2->Name : '12:00';
        $TimeInPM = $Master2 ? $Master2->DataDescription : '13:00';

        $totalTimeWork = (Carbon::parse($TimeInAM)->diffInMinutes(Carbon::parse($TimeOutAM)) + Carbon::parse($TimeInPM)->diffInMinutes(Carbon::parse($TimeOutPM))) / 60;

        $this->data['timekeepings']->totalKeeping = 0;
        $this->data['timekeepings']->overKeeping = 0;
        $this->data['timekeepings']->lateTimes = 0;
        $this->data['timekeepings']->lateHours = 0;
        $this->data['timekeepings']->soonTimes = 0;
        $this->data['timekeepings']->soonHours = 0;
        $this->data['timekeepings']->checkinAtCompany = 0;
        $this->data['timekeepings']->checkinAtHome = 0;

        foreach ($this->data['timekeepings'] as $item) {

            //dd($this->data['timekeepings']);

            if (!$item->Date && isset($item->DateID)) {
                $item->Date = Carbon::create($year, $month, $item->DateID)->toDateString();
            }

            $TimeInAM = isset($item->STimeOfDay) && $item->STimeOfDay != '' && $item->STimeOfDay !== '00:00:00' ? $item->STimeOfDay : $TimeInAM;
            $TimeOutPM = isset($item->ETimeOfDay) && $item->ETimeOfDay != '' && $item->ETimeOfDay !== '00:00:00' ? $item->ETimeOfDay : $TimeOutPM;

            $TimeSBreak = isset($item->SBreakOfDay) && $item->SBreakOfDay != '' && $item->SBreakOfDay !== '00:00:00' ? $item->SBreakOfDay : $TimeOutAM;
            $TimeEBreak = isset($item->EBreakOfDay) && $item->EBreakOfDay != '' && $item->EBreakOfDay !== '00:00:00' ? $item->EBreakOfDay : $TimeInPM;

            $dayOfTheWeek = Carbon::parse($item->Date)->dayOfWeek;
            $item->weekday = self::WEEK_MAP[$dayOfTheWeek];

            $item->calendarEvent = CalendarEvent::query()
                ->where('Type', '=', 0)
                ->where('StartDate', '<=', Carbon::parse($item->Date)->toDateString())
                ->where('EndDate', '>=', Carbon::parse($item->Date)->toDateString())
                ->where('CalendarID', 1)
                ->first();

            $check_event = false;
            if (in_array($dayOfTheWeek, [0, 6]) && $item->calendarEvent) {
                $check_event = true;
            }

            if ($item->TimeIn != Null && Carbon::parse($item->TimeIn) > Carbon::parse($TimeInAM) && ((!in_array($dayOfTheWeek, [0, 6])) || $check_event)) {
                $item->late = Carbon::parse($item->TimeIn)->diffInSeconds(Carbon::parse($TimeInAM));
                $this->data['timekeepings']->lateTimes += 1;
                $this->data['timekeepings']->lateHours += $item->late / 60;
            } else {
                $item->late = 0;
            }

            if ($item->TimeOut != Null && Carbon::parse($item->TimeOut) < Carbon::parse($TimeOutPM) && ((!in_array($dayOfTheWeek, [0, 6])) || $check_event)) {
                $item->soon = Carbon::parse($item->TimeOut)->diffInSeconds(Carbon::parse($TimeOutPM));
                $this->data['timekeepings']->soonTimes += 1;
                $this->data['timekeepings']->soonHours += $item->soon / 60;
            } else {
                $item->soon = 0;
            }
            if ($item->TimeOut != Null && Carbon::parse($item->TimeOut) > Carbon::parse($TimeOutPM) && ((!in_array($dayOfTheWeek, [0, 6])) || $check_event)) {
                $item->N = Carbon::parse($item->TimeOut)->diffInSeconds(Carbon::parse($TimeOutPM));
                $this->data['timekeepings']->overKeeping += $item->N / 60;
            } else {
                $item->N = 0;
            }
            //thời gian làm việc
            if (isset($item->ETimeOfDay)) {
                $floatWorkHours = (Carbon::parse($TimeInAM)->diffInMinutes(Carbon::parse($TimeSBreak)) + Carbon::parse($TimeEBreak)->diffInMinutes(Carbon::parse($TimeOutPM))) / 60;
            } else {
                $floatWorkHours = $totalTimeWork;
            }
            if (!is_null($item->TimeIn) && !is_null($item->TimeOut) && ((!in_array($dayOfTheWeek, [0, 6])) || $check_event)) {
                //trường hợp vào ra ngoài thời gian làm việc

                if ($item->TimeOut < $TimeInAM || $item->TimeIn > $TimeOutPM || ($item->TimeIn > $TimeSBreak && $item->TimeOut < $TimeEBreak)) {
                    $item->hours = 0;
                    $item->hoursTT = 0;
                } elseif ($item->TimeIn <= $TimeSBreak && $item->TimeOut >= $TimeEBreak) {
                    $item->hours = Carbon::parse($item->TimeIn < $TimeInAM ? $TimeInAM : $item->TimeIn)
                            ->diffInMinutes(Carbon::parse($item->TimeOut < $TimeOutPM ? $item->TimeOut : $TimeOutPM)) / 60
                        - Carbon::parse($TimeEBreak)->diffInMinutes(Carbon::parse($TimeSBreak)) / 60;

                    $item->hoursTT = abs(Carbon::parse($item->TimeIn)->diffInSeconds(Carbon::parse($item->TimeOut)));

                    $item->keeping = $item->hours / $floatWorkHours;
                    $this->data['timekeepings']->totalKeeping += $item->keeping;

                } else {
                    $item->hours = Carbon::parse($item->TimeIn > $TimeSBreak ? ($item->TimeIn < $TimeEBreak ? $TimeEBreak : $item->TimeIn)
                            : ($item->TimeIn < $TimeInAM ? $TimeInAM : $item->TimeIn))
                            ->diffInMinutes(Carbon::parse($item->TimeOut < $TimeEBreak ? ($item->TimeOut > $TimeSBreak ? $TimeSBreak : $item->TimeOut)
                                : ($item->TimeOut > $TimeOutPM ? $TimeOutPM : $item->TimeOut))) / 60;

                    // dd(Carbon::parse($item->TimeIn)->diffInSeconds(Carbon::parse($item->TimeOut)) - Carbon::parse($TimeEBreak)->diffInSeconds(Carbon::parse($TimeSBreak)));
                    $item->hoursTT = abs(Carbon::parse($item->TimeIn)->diffInSeconds(Carbon::parse($item->TimeOut)));

                    $item->keeping = $item->hours / $floatWorkHours;
                    $this->data['timekeepings']->totalKeeping += $item->keeping;
                }
            }

            //vang mat
            $item->absence = Absence::query()
                ->select('absences.*', 'master_data.Name')
                ->join('master_data', 'master_data.DataValue', 'absences.MasterDataValue')
                ->where('UID', $user->id)
                ->where('SDate', '<=', Carbon::parse($item->Date)->endOfDay())
                ->where('EDate', '>=', Carbon::parse($item->Date)->startOfDay())->get();

            foreach ($item->absence as $absence) {
                if (Carbon::parse($absence->SDate)->day == Carbon::parse($item->Date)->day) {
                    $absence->STime = Carbon::parse($absence->SDate)->format('H:i');
                    if ($absence->STime < $TimeInAM) {
                        $absence->STime = Carbon::parse($TimeInAM)->format('H:i');
                    }
                } else {
                    $absence->STime = Carbon::parse($TimeInAM)->format('H:i');
                }
                if (Carbon::parse($absence->EDate)->day == Carbon::parse($item->Date)->day) {
                    $absence->ETime = Carbon::parse($absence->EDate)->format('H:i');
                } else {
                    $absence->ETime = Carbon::parse($TimeOutPM)->format('H:i');
                }
            }
            $item->type = CheckinHistory::query()
                ->select("Type")
                ->distinct()
                ->where("UserID", $user->id)
                ->whereDate("CheckinTime", Carbon::create($item->Date)->format(self::FOMAT_DB_YMD))
                ->get();
            if ($item->id != null) {
                if ($item->IsInCpn == 1) $this->data['timekeepings']->checkinAtCompany += 1;
                else $this->data['timekeepings']->checkinAtHome += 1;
            }
        }
    }

    /**
     * @param $request
     * @param $orderBy
     * @param $sortBy
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function history($request, $orderBy, $sortBy)
    {
        $recordPerPage = $this->getRecordPage();
        if ($request->input('UserID') !== null) {
            $userID = $request->input('UserID');
        } else {
            $userID = Auth::user()->id;
        }

        $checkin_history = CheckinHistory::query()
            ->select('checkin_history.*', 'qr_code.QRCode')
            ->leftJoin('qr_code', 'checkin_history.QRCodeID', '=', 'qr_code.id')
            ->where('checkin_history.UserID', $userID)
            ->orderBy($orderBy, $sortBy);

        $one = CheckinHistory::query()->select('checkin_history.DeviceName', 'checkin_history.DeviceInfo',
            'checkin_history.OsVersion', 'checkin_history.Type', 'checkin_history.CheckinTime',
            'checkin_history.RequestTime', 'checkin_history.MacAddress', 'qr_code.QRCode')
            ->leftJoin('qr_code', 'checkin_history.QRCodeID', '=', 'qr_code.id')->first();

        if ($one) {
            $one = $one->toArray();
            if (array_key_exists('search', $request->input())) {
                $checkin_history = $checkin_history->where(function ($query) use ($one, $request) {
                    foreach ($one as $key => $value) {
                        if ($key == 'QRCode') {
                            $query->orWhere('qr_code.' . $key, 'like', '%' . $request->input('search') . '%');
                        } elseif ($key == 'CheckinTime' || $key == 'RequestTime') {
                            $query->orWhereRaw('(DATE_FORMAT(checkin_history.' . $key . ',"%d/%m/%Y")) LIKE ?', '%' . $request->input('search') . '%');
                        } else {
                            $strSearch = trim($this->convert_vi_to_en($request->input('search')));
                            $query->orWhere('checkin_history.' . $key, 'LIKE', '%' . $strSearch . '%');
                        }
                    }
                });
            }
        }

        $start_month = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $end_month = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
        if ($request['time']) {
            $start_month = Carbon::createFromFormat('m/Y', $request['time'])->startOfMonth()->format('Y-m-d H:i:s');
            $end_month = Carbon::createFromFormat('m/Y', $request['time'])->endOfMonth()->format('Y-m-d H:i:s');
        }
        $checkin_history = $checkin_history->where('CheckinTime', '>=', $start_month)->where('CheckinTime', '<=', $end_month);
        if (isset($request['type']) && $request['type'] != '') {
            $checkin_history = $checkin_history->where('Type', '=', $request['type']);
        }

        $checkin_history = $checkin_history->paginate($recordPerPage);
        $count = $checkin_history->count();

        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);

        //redirect to the last page if current page has no record
        if ($count == 0) {
            if (array_key_exists('page', $query_array)) {
                if ($query_array['page'] > 1) {
                    $query_array['page'] = $checkin_history->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }
            }
        }

        $this->data['master_datas'] = $this->getReasonAbsence();
        $this->data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $this->data['type_checkin'] = CheckinHistory::query()->select('Type')->distinct()->get();
        $this->data['checkin_history'] = $checkin_history;
        $this->data['request'] = $request;
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['sort'] = $sort;
    }

    /**
     * show
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checkinWorkAt(Request $request)
    {
        if (!isset($request->user_id) || !isset($request->check_in_time)) {
            return AdminController::responseApi(422, __('admin.error.data-missing'));
        }

        $user = User::find($request->user_id);

        if (!$user) {
            Log::info(Carbon::now()->toDateTimeString() . $request->user_id . ' lỗi: user k tồn tại');
            return AdminController::responseApi(422, __('admin.error.user-missing'));
        }

        $now = Carbon::now()->format(self::FOMAT_DB_YMDHIS);
        $subMinutes = Carbon::now()->subMinutes(15)->format(self::FOMAT_DB_YMDHIS);

        $check_15m = CheckinHistory::where('UserID', '=', $request->user_id)
            ->where('CheckinTime', '>', $subMinutes)
            ->first();

        if ($check_15m) {
            Log::info(Carbon::now()->toDateTimeString() . $request->user_id . ' lỗi: da check công 15 phut sau check lại');
            return AdminController::responseApi(422, __('admin.error.checkin-15p', ['time' => 15 - Carbon::parse($now)->diffInMinutes(Carbon::parse($check_15m->CheckinTime))]));
        }
        try {
            $checkinHistory = new CheckinHistory();
            $checkinHistory->UserId = $request->user_id;
            $checkinHistory->QRCodeID = "";
            $checkinHistory->DeviceName = $request->device_name;
            $checkinHistory->DeviceInfo = $request->device_info;
            $checkinHistory->OsVersion = $request->os_version;
            $checkinHistory->Type = 'Tại nhà';
            $checkinHistory->CheckinTime = $now;

            $arr_date = str_replace('-', '', $request->check_in_time);
            $checkinHistory->RequestTime = Carbon::parse(date(self::FOMAT_DB_YMDHIS, strtotime(str_replace('/', '-', $arr_date))))->format(self::FOMAT_DB_YMDHIS);

            $checkinHistory->MacAddress = isset($request->mac_address) ? $request->mac_address : null;
            $checkinHistory->save();

            $userEdateTime = Carbon::parse($user->ETimeOfDay)->format(self::FOMAT_DB_YMDHIS);
            $check_checkin = Timekeeping::where('UserID', $request->user_id)
                ->where('Date', '=', Carbon::parse($now)->toDateString())->first();
            $user_active_str = implode(",", User::query()
                ->select("id")
                ->where('deleted', '!=', 1)
                ->where('role_group', '!=', 1)
                ->where('Active', 1)
                ->pluck("id")->toArray());
            if (isset($check_checkin) && $check_checkin && isset($check_checkin->TimeIn) && $check_checkin->TimeIn != '' && $check_checkin->TimeIn != '00:00:00') {
                $check_checkin->TimeOut = Carbon::parse($now)->toTimeString();
                $check_checkin->SBreakOfDay = Auth::user()->SBreakOfDay;
                $check_checkin->EBreakOfDay = Auth::user()->EBreakOfDay;
                $check_checkin->IsInCpn = (\auth()->user()->workAt === "" || \auth()->user()->workAt === null) ? 1 : \auth()->user()->workAt;
                $check_checkin->UserActive = $user_active_str;
                $check_checkin->save();

            } elseif (isset($check_checkin) && $check_checkin && (!isset($check_checkin->TimeIn) || $check_checkin->TimeIn == '' || $check_checkin->TimeIn == '00:00:00')) {
                if (Carbon::parse($now) >= Carbon::parse($userEdateTime)) {
                    $check_checkin->TimeOut = Carbon::parse($now)->toTimeString();
                } else {
                    $check_checkin->TimeIn = Carbon::parse($now)->toTimeString();
                }
                $check_checkin->SBreakOfDay = Auth::user()->SBreakOfDay;
                $check_checkin->EBreakOfDay = Auth::user()->EBreakOfDay;
                $check_checkin->IsInCpn = (\auth()->user()->workAt === "" || \auth()->user()->workAt === null) ? 1 : \auth()->user()->workAt;
                $check_checkin->UserActive = $user_active_str;
                $check_checkin->save();
            } else {
                $check_checkin = new Timekeeping();
                $check_checkin->UserID = $request->user_id;
                $check_checkin->Day = Carbon::parse($now)->format('d');
                $check_checkin->Date = Carbon::parse($now)->toDateString();

                $check_checkin->STimeOfDay = $user->STimeOfDay;
                $check_checkin->ETimeOfDay = $user->ETimeOfDay;
                if (Carbon::parse($now) >= Carbon::parse($userEdateTime)) {
                    $check_checkin->TimeIn = null;
                    $check_checkin->TimeOut = Carbon::parse($now)->toTimeString();
                } else {
                    $check_checkin->TimeIn = Carbon::parse($now)->toTimeString();
                    $check_checkin->TimeOut = null;
                }
                $check_checkin->SBreakOfDay = isset(Auth::user()->SBreakOfDay) ? Auth::user()->SBreakOfDay : MasterData::where('DataValue', 'WT002')->first()['Name'];
                $check_checkin->EBreakOfDay = isset(Auth::user()->EBreakOfDay) ? Auth::user()->EBreakOfDay : MasterData::where('DataValue', 'WT002')->first()['DataDescription'];
                $check_checkin->IsInCpn = (\auth()->user()->workAt === "" || \auth()->user()->workAt === null) ? 1 : \auth()->user()->workAt;
                $check_checkin->UserActive = $user_active_str;
                $check_checkin->save();
            }
            Log::info(Carbon::now()->toDateTimeString() . $request->user_id . ' success: chấm công thành công');
            return AdminController::responseApi(200, '', __('admin.success.check-in'));
        } catch (\Exception $e) {
            return AdminController::responseApi(500, $e->getMessage());
        } finally {
        }
    }

    public function exportabsence($month, $year, $user)
    {
        $time = $year . '-' . $month;

        $monthYear = $month . '-' . $year;
        $User = explode(",", $user);
        $records = User::query()
            ->join('timekeepings_new', 'timekeepings_new.UserID', 'users.id')
            ->where('Date', 'like', '%' . $time . '%')
            ->where(function ($query) use ($User) {
                foreach ($User as $value) {
                    $query->orWhere('UserID', $value);
                }
            })
            ->groupBy('users.id')
            ->orderBy('users.id', 'asc')
            ->select('users.FullName', 'users.IDFM', 'users.id as UserID', 'timekeepings_new.*')
            ->get();
        if ($records->count() > 0) {
            return Excel::download(new TimekeepingNewAbsencesExport($month, $year, $records), 'Tổng hợp T' . $monthYear . '.xlsx');
        } else {
            return $this->jsonErrors(['Không có dữ liệu!']);
        }
    }

    public function summaryTime(Request $request, $date = null)
    {
//        $this->data['add'] = $this->add;
//        $this->data['edit'] = $this->edit;
//        $this->data['delete'] = $this->delete;
//        $this->data['export'] = $this->export;
        $this->data["export"] = RoleScreenDetail::where('alias', "TimekeepingCompanyExport")->first();
        $dateNow = $request['time'] != null

            ? Carbon::createFromFormat('m/Y', $request['time'])->format(self::FOMAT_DB_YMD)
            : Carbon::now()->format(self::FOMAT_DB_YMD);
        $users = $this->GetListUser(self::USER_ACTIVE_FLAG);
        // thang va nam
        $timekeepings = TimekeepingNew::query()
            ->whereYear("Date", Carbon::create($dateNow)->year)
            ->whereMonth("Date", Carbon::create($dateNow)->month)
            ->get();

        $arrUserId = $users->pluck("id")->toArray();

        $days = Carbon::create($dateNow)->daysInMonth;
        $allMonth = [];
        for ($i = 1; $i <= $days; $i++) {
            $temp = $i <= 9 ? "0" . $i : $i;
            $key = Carbon::create($dateNow)->format("Y") . "-" . Carbon::create($dateNow)->format("m") . "-" . $temp;
            $date_of_week = Carbon::parse($key)->dayOfWeek;
            $allMonth[$key] = [
                "date" => $date_of_week == 0 ? "CN" : "T" . ($date_of_week + 1),
                "arrUserLateMonth" => [],
                "arrUserSoonMonth" => [],
                "arrUserNotCheckinMonth" => [],
                "arrUserCheckinAtCpnMonth" => [],
                "arrUserCheckinAtHomeMonth" => [],
                "arrUserBackSoonMonth" => [],
                "arrUserActive" => [],
            ];
        }
        $arrayIdUser = [];
        foreach ($timekeepings as $timekeeping) {
            // se them neu la ngay hom nay
            $date = $timekeeping->Date;
            $date_of_week = Carbon::parse($date)->dayOfWeek;
            $arrayIdUser[] = $timekeeping->UserActive;

            if ($timekeeping->TimeIn > $timekeeping->STimeOfDay) {
                $allMonth[$date]["arrUserLateMonth"][] = $timekeeping->UserID;
            } else {
                $allMonth[$date]["arrUserSoonMonth"][] = $timekeeping->UserID;
            }

            if ($timekeeping->IsInCpn == 0) {
                $allMonth[$date]["arrUserCheckinAtHomeMonth"][] = $timekeeping->UserID;
            } else {
                $allMonth[$date]["arrUserCheckinAtCpnMonth"][] = $timekeeping->UserID;
            }

            if ($timekeeping->TimeOut != null && $timekeeping->TimeOut < $timekeeping->ETimeOfDay) {
                $allMonth[$date]["arrUserBackSoonMonth"][] = $timekeeping->UserID;
            }
            if ($timekeeping->UserActive != null) {
                $allMonth[$date]["arrUserActive"] = explode(",", $timekeeping->UserActive);
            } else {
                $allMonth[$date]["arrUserActive"] = $arrUserId;
            }

        }

        // $this->data['idUsers'] = $arrayIdUser;
        $this->data['filterDate'] = $dateNow;
        $this->data['allMonths'] = $allMonth;
        return $this->viewAdminLayout('checkin.timekeepingCompany', $this->data);
    }

    public function latecomers(Request $request)
    {
        $dataTables = [];
        $dataTitle = '';
        $arrUserLateToday = [];
        $dateNow = Carbon::create($request->route('date'))->format(self::FOMAT_DB_YMD);
        $dateToday = Carbon::now()->format(self::FOMAT_DB_YMD);
        $arrayActive = TimekeepingNew::query()->whereDate('Date', $dateNow)->first();

        $titleDate = "Ngày hôm nay";
        if ($dateNow == $dateToday) {
            $titleDate = 'Ngày hôm nay - ' . Carbon::now()->format('H:i');
        } else {
            $titleDate = Carbon::parse($dateNow)->format('d/m/Y');
        }

        if ($request->route('type') == 'latecomers') {

            $timekeepings = DB::table('timekeepings_new')
                ->whereColumn('TimeIn', '>', 'STimeOfDay')
                ->whereDate('Date', $dateNow)
                ->get();

            $timekeepings->transform(function ($record) {
                $user_id = $record->UserID;
                $date = $record->Date;
                $record->absence = Absence::whereDate("SDate", $date)
                    ->join('master_data', 'master_data.DataValue', 'absences.MasterDataValue')
                    ->where("UID", $user_id)
                    ->where("master_data.DataKey", "VM")
                    ->get();
                return $record;
            });

        }


        if ($request->route('type') == 'tkCompany') {
            $timekeepings = TimekeepingNew::query()
                ->whereDate('Date', $dateNow)
                ->where("IsInCpn", 1)->get();
            $timekeepings->transform(function ($record) {
                $user_id = $record->UserID;
                $date = $record->Date;
                $record->absence = Absence::whereDate("SDate", $date)
                    ->join('master_data', 'master_data.DataValue', 'absences.MasterDataValue')
                    ->where("UID", $user_id)
                    ->where("master_data.DataKey", "VM")
                    ->get();
                return $record;
            });
        }
        if ($request->route('type') == 'tkHome') {
            $timekeepings = TimekeepingNew::query()
                ->whereDate('Date', $dateNow)
                ->where("IsInCpn", 0)->get();
            $timekeepings->transform(function ($record) {
                $user_id = $record->UserID;
                $date = $record->Date;
                $record->absence = Absence::whereDate("SDate", $date)
                    ->join('master_data', 'master_data.DataValue', 'absences.MasterDataValue')
                    ->where("UID", $user_id)
                    ->where("master_data.DataKey", "VM")
                    ->get();
                return $record;
            });
        }
        if ($request->route('type') == 'notKeeping') {
            $users = $this->GetListUser(self::USER_ACTIVE_FLAG);
            if ($arrayActive->UserActive != null) {
                $arrUserId = explode(",", $arrayActive->UserActive);
            } else {
                $arrUserId = $users->pluck("id")->toArray();
            }

            $arrayID = TimekeepingNew::query()
                ->whereDate('Date', $dateNow)
                ->pluck("UserID")
                ->unique()
                ->toArray();
            $arrDiff = array_diff(array_values($arrUserId), array_values($arrayID));

            $timekeepings = collect();
            foreach ($arrDiff as $id) {
                $temp = [];
                $temp = (object)$temp;
                $temp->UserID = $id;
                $temp->TimeIn = null;
                $temp->TimeOut = null;
                $temp->STimeOfDay = null;
                $temp->ETimeOfDay = null;
                $temp->absence = Absence::whereDate("SDate", $dateNow)
                    ->join('master_data', 'master_data.DataValue', 'absences.MasterDataValue')
                    ->where("UID", $id)
                    ->where("master_data.DataKey", "VM")
                    ->get();
                $timekeepings->push((object)$temp);
            }
        }

        if ($request->route('type') == 'tkKeeping') {
            $timekeepings = TimekeepingNew::query()
                ->whereDate('Date', $dateNow)->get();
            $timekeepings->transform(function ($record) {
                $user_id = $record->UserID;
                $date = $record->Date;
                $record->absence = Absence::whereDate("SDate", $date)
                    ->join('master_data', 'master_data.DataValue', 'absences.MasterDataValue')
                    ->where("UID", $user_id)
                    ->where("master_data.DataKey", "VM")
                    ->get();
                return $record;
            });
        }

        if ($request->route('type') == 'backSoon') {
            $timekeepings = DB::table('timekeepings_new')->whereNotNull('TimeOut')->whereColumn('TimeOut', '<', 'ETimeOfDay')->whereDate('Date', $dateNow)->get();

            $timekeepings->transform(function ($record) {
                $user_id = $record->UserID;
                $date = $record->Date;
                $record->absence = Absence::whereDate("EDate", $date)
                    ->join('master_data', 'master_data.DataValue', 'absences.MasterDataValue')
                    ->where("UID", $user_id)
                    ->where("master_data.DataKey", "VM")
                    ->get();
                return $record;
            });
        }


        switch ($request->route('type')) {
            case 'latecomers':
                $dataTitle = "Danh sách nhân viên đi muộn - ";
                break;
            case 'tkHome':
                $dataTitle = "Danh sách nhân viên chấm công từ xa - ";
                break;
            case 'tkCompany':
                $dataTitle = "Danh sách nhân viên chấm công tại công ty - ";
                break;
            case 'tkKeeping':
                $dataTitle = "Danh sách nhân viên đã chấm công - ";
                break;
            case 'notKeeping':
                $dataTitle = "Danh sách nhân viên không chấm công - ";
                break;
            case 'backSoon' :
                $dataTitle = "Danh sách nhân viên về sớm - ";
                break;
            default:
                # code...
                break;
        }

        $users = User::query()
            ->select("FullName")
            ->whereIn("users.id", $arrUserLateToday)
            ->get();

        $this->data['lateUsers'] = $users;
        $this->data['title'] = $dataTitle . $titleDate;
        $this->data['timekeepings'] = $timekeepings;

        return $this->viewAdminLayout('checkin.latecomers-modal', $this->data);
    }

    #Longle 5/8/2021
    public function exportSummaryTimekeeping(Request $request, TimekeepingService $service)
    {
        try {
            $this->authorize("action", RoleScreenDetail::where('alias', '=', "TimekeepingCompanyExport")->first());
        } catch (AuthorizationException $e) {
            abort(403);
        }
        $validator = Validator::make($request->only("date"), [
            "date" => "nullable|date_format:m/Y"
        ], [
            "date.date_format" => "Sai định dạng ngày"
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 400);
        }
        $validated = $validator->validate();
        $data = $service->getDataTotalInCpn($validated);
        if ($data["_summary"]["total_come_back_soon"] == 0
            && $data["_summary"]["total_not_ck"] == 0
            && $data["_summary"]["total_not_ck_in"] == 0
            && $data["_summary"]["total_not_ck_out"] == 0
            && $data["_summary"]["total_ck_late"] == 0
            && $data["_summary"]["total_ck_on_time"] == 0
            && $data["_summary"]["total_ck_at_cpn"] == 0
            && $data["_summary"]["total_ck_at_home"] == 0) {
            abort(503, "Không có dữ liệu");
        }
        new ExportExcelTimeKeepingController(Carbon::createFromFormat("m/Y", $validated["date"])->format("m_Y"), $data);
        return response()->json("Export success");
    }
}
