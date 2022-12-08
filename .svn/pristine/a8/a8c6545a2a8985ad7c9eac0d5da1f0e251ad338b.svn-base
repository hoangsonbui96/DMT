<?php

namespace App\Http\Controllers\Admin;

use App\Calendar;
use Carbon\Carbon;
use App\CalendarEvent;
use App\Calendars_views;
use App\User;
use App\RoleScreenDetail;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class CalendarController
 * @package App\Http\Controllers\Admin
 * Controller screen Calendar
 */
class CalendarController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $addC;
    protected $editC;
    protected $deleteC;
    protected $viewC;
    protected $copyC;
    protected $export;
    const KEYMENU= array(
        "add" => "CalendarManagementAdd",
        "view" => "CalendarManagement",
        "edit" => "CalendarManagementEdit",
        "delete" => "CalendarManagementDelete",
        "addC" => "CalendarAdd",
        "viewC" => "Calendar",
        "editC" => "CalendarEdit",
        "deleteC" => "CalendartDelete",
        "copyC" => "CalendarCopy",
        "export" => "Calendarexport",
    );
    /**
     * CalendarController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
        $array = $this->RoleView('Calendar',['CalendarManagement','Calendar']);
        $this->menu = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if($value == $row->alias)
                    $this->$key = $row;
            } 
        }
    }

    /**
     * @param Request $request
     * @return View (calendar)
     * @throws AuthorizationException
     * Get data Calendar and return view
     */
    public function showCalendar(Request $request, $orderBy = 'id', $sortBy = 'desc')
    {
        $recordPerPage = $this->getRecordPage();
        $this->authorize('view', $this->menu);
        if (Schema::hasColumn('calendars', $orderBy)) {
            $calendarData = Calendar::orderBy($orderBy, $sortBy);
        } else {
            return redirect()->back();

        }
        //tim kiem theo column
        $this->data['request'] = $request->query();
        $one = Calendar::query()->select('Name', 'Title')->first();
        if ($one) {
            $one = $one->toArray();
            if (array_key_exists('search', $request->input())) {
                $calendarData = $calendarData->where(function ($query) use ($one, $request) {
                    foreach ($one as $key => $value) {
                        $query->orWhere($key, 'like', '%' . $request->input('search') . '%');
                    }
                });

            }
        }

        $user = User::find(Auth::user()->id);
        if ($user->cant('action', $this->edit)) {
            $calendarData = $calendarData->where('Active', 1);
        }

        //phan trang
        $count = $calendarData->count();
        $calendarData = $calendarData->paginate($recordPerPage);

        $this->data['calendarData'] = $calendarData;
        $query_string = str_replace($request->url(), '', $request->fullUrl());

        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        parse_str(str_replace('?', '', $query_string), $query_array);
        if ($calendarData->count() == 0) {
            if (array_key_exists('page', $query_array)) {
                if ($query_array['page'] > 1) {
                    $query_array['page'] = $calendarData->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }

            }
        }
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        $this->data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;
        $this->data['sort'] = $sort;
        return $this->viewAdminLayout('calendar', $this->data);

    }

    /**
     * Process insert multiple records,update one records
     * @param Request $request
     * @param null $id
     * @return string|void
     */
    public function storeCalendar(Request $request, $id = null)
    {
        try {
            if (count($request->input()) > 0) {
                if (array_key_exists('id', $request->input())) {
                    $validator = Validator::make($request->all(),
                        [
                            'Name'   => 'string|min:0|max:200',
                            'Title'  => 'string|min:0|max:200',
                            'Active' => 'string|nullable',
                            'id'     => 'integer|min:1|nullable',
                        ]);
                } else {
                    $validator = Validator::make($request->all(),
                        [
                            'Name'   => 'string|min:0|max:200',
                            'Title'  => 'string|min:0|max:200',
                            'Active' => 'string|nullable',
                        ]);
                }

                if ($validator->fails()) {
                    return $this->jsonArrErrors($validator->errors()->all());
                }

                $validated = $validator->validate();

                if (array_key_exists('id', $validated)) {
                    $one = Calendar::find($validated['id']);
                } else {
                    $one = new Calendar();
                }
                foreach ($validated as $key => $value) {
                    if (Schema::hasColumn('calendars', $key))
                        $one->$key = $value;
                }

                if (isset($validated['Active'])) {
                    $one->Active = 1;
                } else {
                    $one->Active = 0;
                }
                $save = $one->save();
                if (!$save) {
                    return $this->jsonErrors('Lưu không thành công');
                } else {
                    return $this->jsonSuccessWithRouter('admin.CalendarData');
                }

            } else {
                return abort('404');
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Show popup insert,update can insert multiple records
     * @param Request $request
     * @param null $oneId
     * @param null $del
     * @return View (calendar-data-detail)
     */
    public function showDetail(Request $request, $oneId = null, $del = null, $up = null)
    {
        if ($oneId != null) {
            if ($del == 'del') {
                $one = Calendar::find($oneId);
                if ($one) $save = $one->delete();
                if (!$save) {
                    return $this->jsonErrors('Xóa không thành công');
                } else {
                    return 1;
                }
            }
            if ($del == 'up') {
                $active = Calendar::query()->select('Active')->find($oneId);
                if (!isset($active)) {
                    return 2;
                }
                $one = Calendar::find($oneId);
                if ($active->Active == 0) {
                    $active = 1;
                } else {
                    $active = 0;
                }
                if ($one) {
                    $one->Active = $active;
                    $one->save();
                }
                return 1;
            }
            $this->data['itemInfo'] = Calendar::find($oneId);
            if ($this->data['itemInfo']) {
                return $this->viewAdminIncludes('calendar-data-detail', $this->data);
            } else {
                return "";
            }
        } else {
            return $this->viewAdminIncludes('calendar-data-detail', $this->data);
        }

    }

    /**
     * @param Request $request
     * @return View (calendars)
     * @throws AuthorizationException
     * Get data Calendars events and return view
     */
    public function showCalendars(Request $request)
    {
        $id = '';
        $year = '';
        $calendarchanger = '';
        $calendartype = '';
        $this->data['calendars'] = Calendar::query()
            ->select('id', 'Name', 'Title')
            ->where('Active', 1)
            ->get();

        $this->data['years'] = Carbon::now()->year;


        if ($request->isMethod('get')) {
            if (null !== $request->input('select-calendar')) {
                $id = $request->input('select-calendar');
            }

            if (null !== $request->input('select-year')) {
                $year = $request->input('select-year');
            }

            if (null !== $request->input('type-print')) {
                $calendarchanger = $request->input('type-print');
            }

            if (null !== $request->input('type-print-date')) {
                $calendartype = $request->input('type-print-date');
            }
        }

        if ($id == '') {
            $this->data['a'] = Calendar::query()
                ->select('id')
                ->where('Active', 1)
                ->limit(1)
                ->get();

            foreach ($this->data['a'] as $row) {
                $id = $row['id'];
            }
        }

        if ($year == '') {
            for ($i = $this->data['years']; $i <= $this->data['years']; $i++) {
                $year = $i;
            }
        }
        if ($calendarchanger == '') {
            $calendarchanger = 0;
        }
        if ($calendartype == '') {
            $calendartype = 0;
        }
        $sdate = '01/01/' . $year;
        $edate = '31/12/' . $year;

        $_SESSION['CalendarID'] = $id;
        $_SESSION['year'] = $year;
        $_SESSION['calendarchanger'] = $calendarchanger;
        $_SESSION['calendartype'] = $calendartype;

        if ($calendarchanger == '2') {
            $user = ',' . Auth::user()->id . ',';
            $pdo = \DB::connection()->getPdo();
            $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
            $Calendars_views = Calendars_views::query()
                ->where([['StartDate', '>=', $this->fncDateTimeConvertFomat($sdate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD)], ['EndDate', '<=', $this->fncDateTimeConvertFomat($edate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD)]])
                ->where(function ($query1) use ($user) {
                    $query1->orWhereNull('Participant')->orWhere('Participant', 'like', '%' . $user . '%');
                });

            $Calendars_views = $Calendars_views->where(function ($query2) use ($id) {
                $query2->orWhere('DataKey', '=', $id)
                    ->orWhere('DataKey', '=', 'E')
                    ->orWhere('DataKey', '=', 'H')
                    ->orWhere('DataKey', '=', 'VM');
            });
            $Calendars_views = $Calendars_views->orderBy('StartDate');
            $this->data['calendars_events'] = $Calendars_views->get();
        } else {
            $this->data['calendars_events'] = CalendarEvent::query()
                ->select('id', 'CalendarID', 'StartDate', 'EndDate', 'Content', 'Type', 'jaColor')
                ->where([['CalendarID', '=', $id], ['StartDate', '>=', $this->fncDateTimeConvertFomat($sdate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD)], ['EndDate', '<=', $this->fncDateTimeConvertFomat($edate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD)]])
                ->orderBy('StartDate')
                ->get();
        }

        $this->data['users'] = User::query()
            ->select('id', 'FullName', 'email', 'username', 'Birthday', 'Active', 'STimeOfDay', 'IDFM')
            ->where([['Active', '=', 1], ['deleted', '=', 'F']])
            ->orderBy('username', 'asc')
            ->get();

        $this->data['request'] = $request->query();

        $this->data['addC'] = $this->addC;
        $this->data['editC'] = $this->editC;
        $this->data['deleteC'] = $this->deleteC;
        $this->data['copyC'] = $this->copyC;
        $this->data['export'] = $this->export;

        
        if (strpos(\Request::getRequestUri(), 'api') !== false) {
            return response()->json(['calendars' => $this->data ]);
        }
        return $this->viewAdminLayout('calendars', $this->data);
    }

    /**
     * Show popup insert,update can insert multiple records
     * @param Request $request
     * @param null $oneId
     * @param null $del
     * @return View (calendar-event-detail)
     */
    public function showDetailCalendar(Request $request, $oneId = null, $del = null)
    {
        $this->data['addC'] = $this->addC;
        $this->data['editC'] = $this->editC;
        $this->data['deleteC'] = $this->deleteC;
        if ($oneId != null) {
            if ($del == 'del') {
                $one = CalendarEvent::find($oneId);
                if ($one) $save = $one->delete();
                if (!$save) {
                    return $this->jsonErrors('Xóa không thành công');
                } else {
                    return 1;
                }
            }
            $this->data['itemInfo'] = CalendarEvent::find($oneId);
            if ($this->data['itemInfo']) {
                return $this->viewAdminIncludes('calendar-event-detail', $this->data);
            } else {
                return "";
            }
        } else {
            return $this->viewAdminIncludes('calendar-event-detail', $this->data);
        }

    }

    /**
     * Process insert multiple records,update one records
     * @param Request $request
     * @param null $id
     * @return string|void
     */
    public function storeCalendarInfo(Request $request, $id = null)
    {
        $year = '';
        $yearcopy = '';
        $CalendarID = '';
        if ($request->isMethod('post')) {
            if (null !== $request->input('year')) {
                $year = $request->input('year');
                $yearcopy = $request->input('yearcopy');
                $CalendarID = $request->input('select-calendar');
            }
        }
        if ($year == '') {
            try {
                if (count($request->input()) > 0) {
                    if (array_key_exists('id', $request->input())) {
                        $validator = Validator::make($request->all(),
                            [
                                'CalendarID' => 'required|integer|min:0|max:20',
                                'StartDate'  => 'date_format:d/m/Y|nullable',
                                'EndDate'    => 'date_format:d/m/Y|nullable',
                                'Content'    => 'required|string|max:200',
                                'Type'       => 'required|string|nullable',
                                'jaColor'    => 'required|string|min:0|max:7',
                                'id'         => 'integer|min:1|nullable',
                            ]);
                    } else {
                        $validator = Validator::make($request->all(),
                            [
                                'CalendarID' => 'required|integer|min:0|max:20',
                                'StartDate'  => 'date_format:d/m/Y|nullable',
                                'EndDate'    => 'date_format:d/m/Y|nullable',
                                'Content'    => 'required|string|max:200',
                                'Type'       => 'required|string|nullable',
                                'jaColor'    => 'required|string|min:0|max:7',
                            ]);
                    }

                    if ($validator->fails()) {
                        return response()->json(['errors' => $validator->errors()->all()]);
                    }

                    $validated = $validator->validate();

                    $StartDate = $this->fncDateTimeConvertFomat($validated['StartDate'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                    $EndDate = $this->fncDateTimeConvertFomat($validated['EndDate'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                    if (Carbon::parse($StartDate)->gt(Carbon::parse($EndDate))) {
                        return $this->jsonErrors('Ngày bắt đầu phải nhỏ hơn ngày kết thúc !');
                    }

                    if (array_key_exists('id', $validated)) {
                        $one = CalendarEvent::find($validated['id']);
                    } else {
                        $one = new CalendarEvent();
                    }
                    foreach ($validated as $key => $value) {
                        if (Schema::hasColumn('calendar_events', $key))
                            $one->$key = $value;
                    }

                    if (isset($validated['StartDate'])) {
                        $one->StartDate = $StartDate;
                    }
                    if (isset($validated['EndDate'])) {
                        $one->EndDate = $EndDate;
                    }

                    $save = $one->save();
                    if (!$save) {
                        return $this->jsonErrors('Lưu không thành công');
                    } else {
                        return $this->jsonSuccessWithRouter('admin.Calendar');
                    }

                } else {
                    return abort('404');
                }
            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else {
            $sdate = '01/01/' . $yearcopy;
            $edate = '31/12/' . $yearcopy;

            $calendars_events = CalendarEvent::query()
                ->select('id', 'CalendarID', 'StartDate', 'EndDate', 'Content', 'Type', 'jaColor')
                ->where([['CalendarID', '=', $CalendarID], ['StartDate', '>=', $this->fncDateTimeConvertFomat($sdate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD)], ['EndDate', '<=', $this->fncDateTimeConvertFomat($edate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD)]])
                ->get();
            $arr = array();

            if (count($calendars_events) > 0) {
                foreach ($calendars_events as $row) {
                    if ($row['id'] != '') {
                        $key = $row['id'];
                        $arr[$key] = array('CalendarID' => $row['CalendarID'],
                                           'StartDate'  => $row['StartDate'],
                                           'EndDate'    => $row['EndDate'],
                                           'Content'    => $row['Content'],
                                           'Type'       => $row['Type'],
                                           'jaColor'    => $row['jaColor'],
                        );
                    }
                }
                foreach ($arr as $row) {
                    $one = new CalendarEvent();
                    foreach ($row as $key => $value) {
                        if (Schema::hasColumn('calendar_events', $key)) {
                            if ($key == 'StartDate' && $key != 'EndDate') {
                                $sday = str_replace($yearcopy, $year, $value);
                                $one->StartDate = $sday;
                            } else if ($key == 'EndDate' && $key != 'StartDate') {
                                $eday = str_replace($yearcopy, $year, $value);
                                $one->EndDate = $eday;
                            } else {
                                $one->$key = $value;
                            }
                        }
                    }

                    $one->save();
                }

                return $this->jsonSuccessWithRouter('admin.Calendar');
            }
        }

    }

    /**
     * Show popup insert,update can insert multiple records
     * @param Request $request
     * @param null $oneId
     * @param null $del
     * @return View (calendar-year-detail)
     */
    public function showDetailYear(Request $request, $oneId = null, $del = null)
    {
        $itemId = substr($oneId, 0, -1);
        $itemId1 = str_replace($itemId, "", $oneId);

        if ($oneId != null) {
            if ($del == 'del') {
                $sdate = '01/01/' . $itemId;
                $edate = '31/12/' . $itemId;

                $calendars_events = CalendarEvent::query()
                    ->select('id')
                    ->where([['CalendarID', '=', $itemId1], ['StartDate', '>=', $this->fncDateTimeConvertFomat($sdate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD)], ['EndDate', '<=', $this->fncDateTimeConvertFomat($edate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD)]])
                    ->get();

                if (count($calendars_events) > 0) {
                    foreach ($calendars_events as $row) {
                        $one = CalendarEvent::find($row['id']);
                        if ($one) $one->delete();
                    }
                }
                return 1;
            }
            $this->data['itemInfo'] = $itemId;
            $this->data['CalendarID'] = $itemId1;
            if ($this->data['itemInfo']) {
                return $this->viewAdminIncludes('calendar-year-detail', $this->data);
            } else {
                return "";
            }
        } else {
            return $this->viewAdminIncludes('calendar-year-detail', $this->data);
        }
    }

    /**
     * Show popup insert,update can insert multiple records
     * @param Request $request
     * @param null $oneId
     * @param null $del
     * @return View (calendar-event-detail)
     */
    public function showDetailCalendarWeek(Request $request, $oneId = null)
    {
        $ID = $request->input('ID');
        $Title = $request->input('Title');
        $start = $request->input('start');
        $end = $request->input('end');
        $timeStart = $request->input('timeStart');
        $timeEnd = $request->input('timeEnd');
        $CalendarID = $request->input('C');
        $Calendars_views = Calendars_views::query();
        $user = ',' . Auth::user()->id . ',';
        if ($CalendarID == 'H') {
            $Calendars_views = $Calendars_views->orWhere('Participant', 'like', '%' . $user . '%');
        }
        $Calendars_views = $Calendars_views->where('id', '=', $ID);
        $Calendars_views = $Calendars_views->where('DataKey', '=', $CalendarID);
        $this->data['User'] = User::query()->withTrashed()->get();
        $this->data['CalendarsDay'] = $Calendars_views->get();
        return $this->viewAdminIncludes('calendar-week-detail', $this->data);
    }
}
