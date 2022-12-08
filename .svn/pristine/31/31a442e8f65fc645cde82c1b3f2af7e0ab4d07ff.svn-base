<?php

namespace App\Http\Controllers\Admin;

use App\CalendarEvent;
use App\DailyReport;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmail;
use App\MasterData;
use App\Members;
use App\Menu;
use App\model\ListPosition;
use App\Model\Role\RoleScreenDetail;
use App\project;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use phpDocumentor\Reflection\Types\Array_;

class AdminController extends Controller
{
    const USER_ROOT_GROUP = 1;
    const DELETE_FLAG = 1;

    const USER_ACTIVE_FLAG = 1;
    const USER_DEACTIVE_FLAG = 0;

    const FOMAT_DISPLAY_DMY = 'd/m/Y';
    const FOMAT_DISPLAY_DMY_HIS = 'd/m/Y H:i:s';
    const FOMAT_DB_YMD = 'Y-m-d';
    const FOMAT_DB_YMD_HI = 'Y-m-d H:i';
    const FOMAT_DB_YMD_HIS = 'Y-m-d H:i:s';

    const START_LOAD_QR = 6;
    const END_LOAD_QR = 18;

    const KEY_SUBJECT_MAIL = 'subjectMail';
    const KEY_VIEW_MAIL = 'viewBladeMail';
    const KEY_DATA_BINDING = 'dataBinding';
    const KEY_MAIL_NAME_FROM = 'mailNameFrom';
    const KEY_MAIL_ADDRESS_TO = 'arrMailAddressTo';
    const KEY_MAIL_ADDRESS_CC = 'arrMailCC';

    const KAFKA_HOST = "58.186.80.27:9092";
    const KAFKA_TOPIC = "AKB-PI-MAIN";
    const WEEK_MAP = [
        0 => 'CN',
        1 => 'T2',
        2 => 'T3',
        3 => 'T4',
        4 => 'T5',
        5 => 'T6',
        6 => 'T7',
    ];

    const ACTIVE_OT_AFTER_MINUTE = 15;

    protected $role_list = ['Edit' => null, 'Add' => null, 'Delete' => null, 'View' => null, 'Export' => null, 'Review' => null];
    protected $request;
    protected $cpn;

    protected $startTime = '08:30';
    protected $endTime = '17:30';
    protected $timeOutAm = '12:00';
    protected $timeInPm = '13:00';
    protected $working_days = [1, 2, 3, 4, 5];

    //attributes for send mail with HTML
    protected $attr_mail_html = [
        "subjectMail"               =>  "",
        "contentMail"               =>  "",
        "arrMailAddressFrom"        =>  "",
        "mailNameFrom"              =>  "",
        "arrMailAddressTo"          =>  "",
        "arrMailAddressCC"          =>  ""
    ];

    //attributes for send mail with View
    protected $attr_mail_view = [
        self::KEY_SUBJECT_MAIL      =>  "",
        self::KEY_VIEW_MAIL         =>  "",
        self::KEY_DATA_BINDING      =>  "",
        self::KEY_MAIL_NAME_FROM    =>  "",
        self::KEY_MAIL_ADDRESS_TO   =>  "",
        self::KEY_MAIL_ADDRESS_CC   =>  "",
    ];

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->request = $request;
        $this->cpn = is_null($this->request->segment(1)) ? 'akb' : $this->request->segment(1);
    }

    /**
     * Meno      : Convert fomat date time
     * Create by : 2019.11.22 AKB Nguyen Thanh Tung
     * @param string $dataBeforeFomat
     * @param string $fomatDateCurrent
     * @param string $fomatDateConvert
     * @return string
     */
    protected function fncDateTimeConvertFomat($dataBeforeFomat, $fomatDateCurrent, $fomatDateConvert)
    {
        $date = \DateTime::createFromFormat($fomatDateCurrent, $dataBeforeFomat);
        return $date->format($fomatDateConvert);
    }

    /**
     * SendMailHtml
     * Create by : 2020.04.04 AKB Nguyen Thanh Tung
     * @param $subjectMail
     * @param $contentMail
     * @param $arrMailAddressFrom
     * @param $mailNameFrom
     * @param $arrMailAddressTo
     * @param $arrMailCC
     * @param $delay_mins
     * @param null $dateTimeAction
     */
    public static function SendMailHtml($subjectMail, $contentMail, $arrMailAddressFrom, $mailNameFrom,
                                        $arrMailAddressTo, $arrMailCC, $dateTimeAction = null, $delay_mins = 0)
    {
//         try {
//             Mail::send([], [], function (Message $message) use ($arrMailAddressFrom, $mailNameFrom, $arrMailAddressTo, $arrMailCC, $subjectMail, $contentMail) {
//                 $message->from($arrMailAddressFrom, $mailNameFrom)
//                     ->to($arrMailAddressTo)
//                     ->cc(null !== $arrMailCC ? $arrMailCC : [])
//        //          //    ->cc([])
//                     ->subject($subjectMail)
//                     ->setBody($contentMail, 'text/html');
//             });
//         } catch (\Exception $e) {
//
//         }
        $attr_mail_html["subjectMail"] = $subjectMail;
        $attr_mail_html["contentMail"] = $contentMail;
        $attr_mail_html["arrMailAddressFrom"] = $arrMailAddressFrom;
        $attr_mail_html["mailNameFrom"] = $mailNameFrom;
        $attr_mail_html["arrMailAddressTo"] = $arrMailAddressTo;
        $attr_mail_html["arrMailAddressCC"] = $arrMailCC;
        // if ($delay_mins != 0) {
        //     SendEmail::dispatch("send_html", $attr_mail_html)->delay(now()->addMinutes($delay_mins));
        // } else {
        //     SendEmail::dispatch("send_html", $attr_mail_html);
        // }
        SendEmail::dispatch("send_html", $attr_mail_html)->delay(now()->addMinutes($delay_mins));
    }

    /**
     * SendMailWithView
     * Create by : 2020.04.04 AKB Nguyen Thanh Tung
     * @param array/string $arrMailAddressTo
     * @param int $delay_min
     */
    protected function SendMailWithView(array $arrData, int $delay_min = 0)
    {
//        if (!isset($arrData) || empty($arrData)) {
//            return;
//        }
//
//        $configMailFrom = config('mail.from', []);
//        $mailAddressFrom = array_key_exists('address', $configMailFrom) ? $configMailFrom['address'] : '';
//        $mailNameFrom = array_key_exists('name', $configMailFrom) ? $configMailFrom['name'] : '';
//        $subjectMail = array_key_exists(self::KEY_SUBJECT_MAIL, $arrData) ? trim($arrData[self::KEY_SUBJECT_MAIL]) : '';
//        $viewBladeMail = array_key_exists(self::KEY_VIEW_MAIL, $arrData) ? trim($arrData[self::KEY_VIEW_MAIL]) : '';
//        $dataBinding = array_key_exists(self::KEY_DATA_BINDING, $arrData) && is_array($arrData[self::KEY_DATA_BINDING]) ? $arrData[self::KEY_DATA_BINDING] : [];
//        $mailNameFrom = array_key_exists(self::KEY_MAIL_NAME_FROM, $arrData) ? $arrData[self::KEY_MAIL_NAME_FROM] : $mailNameFrom;
//        $arrMailAddressTo = array_key_exists(self::KEY_MAIL_ADDRESS_TO, $arrData) ? $arrData[self::KEY_MAIL_ADDRESS_TO] : '';
//        $arrMailCC = array_key_exists(self::KEY_MAIL_ADDRESS_CC, $arrData) ? $arrData[self::KEY_MAIL_ADDRESS_CC] : null;
//
//        if (is_array($arrMailAddressTo) && !empty($arrMailAddressTo)) {
//            $arrMailAddressTo = array_filter($arrMailAddressTo);
//        }
//
//        if ('' === $subjectMail || '' === $viewBladeMail || '' === $arrMailAddressTo || !isset($arrMailAddressTo) || empty($arrMailAddressTo)) {
//            return;
//        }
//
//        try {
//            Mail::send($viewBladeMail, $dataBinding,
//                function (Message $message) use ($arrMailAddressTo, $arrMailCC, $subjectMail, $mailNameFrom, $mailAddressFrom) {
//                    $message->from($mailAddressFrom, $mailNameFrom);
//                    $message->to($arrMailAddressTo)
//                        ->cc(null !== $arrMailCC ? $arrMailCC : [])
//                        ->subject($subjectMail);
//                });
//        } catch (\Exception $e) {
//            Log::debug($e->getMessage());
//        }
        // if ($delay_min != 0) {
        //     SendEmail::dispatch("send_view", $arrData)->delay(now()->addMinutes($delay_min));
        // }
        // else {
        //     SendEmail::dispatch("send_view", $arrData);
        // }
        SendEmail::dispatch("send_view", $arrData)->delay(now()->addMinutes(1));
    }

    /**
     * Get list user
     * Create by : 2020.04.04 AKB Nguyen Thanh Tung
     * @param number $activeUser
     * @return listUser
     */
    protected function GetListUser($active = -1)
    {
        $queryUser = User::query()->where('deleted', '!=', self::DELETE_FLAG)
            ->where('role_group', '!=', self::USER_ROOT_GROUP)->orderBy('username');

        if (
            self::USER_ACTIVE_FLAG === $active
            || self::USER_DEACTIVE_FLAG === $active
        ) {
            $queryUser = $queryUser->where('Active', '=', $active);
        }

        return $queryUser->get();
    }

    public function getUserPosition($userId)
    {
        $currentUserPositions = User::query()
            ->select('list_position.DataValue')
            ->join('list_position_user', 'list_position_user.UserId', '=', 'users.id')
            ->join('list_position', 'list_position_user.DataValue', '=', 'list_position.DataValue')
            ->where('users.id', $userId)
            ->whereNull('list_position_user.deleted_at')
            ->pluck('list_position.DataValue')
            ->toArray();
        return $currentUserPositions;
    }

    public function getUsersByPosition($listproject, $positionDataValues = [])
    {
        $members = '';
        foreach ($listproject as $item) {
            $members .= $item->Member;
        }
        $members = array_unique(array_filter(explode(',', $members)));
        $users = [];
        $users = User::query()->select('users.id', 'users.FullName')
            ->join('list_position_user as lpu', 'lpu.UserId', '=', 'users.id')
            ->where(function ($query) use ($positionDataValues, $members) {
                $query->whereIn('users.id', $members);
                foreach ($positionDataValues as $item) {
                    if ($item == 'CL001') {
                        $query->orwhere('lpu.DataValue', 'CL002');
                    } elseif ($item == 'TL001') {
                        $query->orwhere('lpu.DataValue', 'TL002');
                    }
                }
            })
            ->whereNull('lpu.deleted_at')
            ->whereNull('users.deleted_at')
            ->groupBy('users.id')
            ->get();
        return $users;
    }

    protected function getComtorIds()
    {
        $queryUser = User::query()
            ->whereNull('deleted_at')
            ->where('role_group', '!=', self::USER_ROOT_GROUP)->orderBy('username');
        return $queryUser->get();
    }

    public function getMembersByComtorLeader()
    {
        $members = User::query()->select('users.*')
            ->leftJoin('list_position_user', 'list_position_user.UserId', '=', 'users.id')
            ->where('list_position_user.DataValue', '=', 'CL002')
            ->whereNull('list_position_user.deleted_at')
            ->whereNull('users.deleted_at')
            ->get();
        return $members;
    }

    public function getMembersByTesterLeader()
    {
        $members = User::query()
            ->leftJoin('list_position_user', 'list_position_user.UserId', '=', 'users.id')
            ->where('list_position_user.DataValue', '=', 'TL002')
            ->whereNull('list_position_user.deleted_at')
            ->whereNull('users.deleted_at')
            ->get();
        return $members;
    }

    //Projects

    public function getActiveProjectsByComtorLeader($comtors = [])
    {
        $currentDate = Carbon::now()->toDateString();
        $projects = Project::query()
            ->where('Active', 1)
            ->where(function ($query) use ($currentDate) {
                $query->where('EndDate', '>=', $currentDate)
                    ->orWhereNull('EndDate');
            })
            ->whereNull('deleted_at')
            ->where(function ($query) use ($comtors) {
                foreach ($comtors as $item) {
                    $query->orwhere('Member', 'like', '%,' . $item . ',%');
                }
            })
            ->get();
        return $projects;
    }


    public function getActiveProjectsByTesterLeader($testers = [])
    {
        $currentDate = Carbon::now()->toDateString();
        $projects = Project::query()
            ->where('Active', 1)
            ->where(function ($query) use ($currentDate) {
                $query->where('EndDate', '>=', $currentDate)
                    ->orWhereNull('EndDate');
            })
            ->whereNull('deleted_at')
            ->where(function ($query) use ($testers) {
                foreach ($testers as $item) {
                    $query->orwhere('Member', 'like', '%,' . $item . ',%');
                }
            })
            ->get();
        return $projects;
    }

    protected function getProjects()
    {
        $currentDate = Carbon::now()->toDateString();
        $projects = Project::query()
            ->where('Active', 1)
            ->where(function ($query) use ($currentDate) {
                $query->where('EndDate', '>=', $currentDate)
                    ->orWhereNull('EndDate');
            })
            ->get()
            ->toArray();
        return $projects;
    }

    protected function GetListActiveProject()
    {
        $currentDate = Carbon::now()->toDateString();
        $queryProject = Project::query()->whereNull('deleted_at')
            ->where('Active', 1)
            ->where(function ($query) use ($currentDate) {
                $query->where('EndDate', '>=', $currentDate)
                    ->orWhereNull('EndDate');
            });
        return $queryProject->get();
    }

    protected function GetListActiveProjectByLeader($leader)
    {
        $currentDate = Carbon::now()->toDateString();
        $queryProject = Project::query()->whereNull('deleted_at')
            ->where('Active', 1)
            ->where(function ($query) use ($leader) {
                $query->where('Leader', 'LIKE', '%,' . $leader . ',%');
            })
            ->where(function ($query) use ($currentDate) {
                $query->where('EndDate', '>=', $currentDate)
                    ->orWhereNull('EndDate');
            });
        return $queryProject->get();
    }

    public function getProjectsByLeaderPosition($leaderId = null, $position = [])
    {
        $active = isset($active) ? $active : 1;
        $projects = [];
        $currentDate = Carbon::now()->toDateString();
        $projects = Project::query()
            ->select(
                'projects.id',
                'projects.NameVi',
                'projects.Member'
            )
            ->whereNull('projects.deleted_at');
        if ($active == 1) {
            $projects->where(function ($query) use ($currentDate) {
                $query->where('projects.EndDate', '>=', $currentDate)
                    ->orwhereNull('projects.EndDate');
            })
                ->where('projects.Active', 1);
        } elseif ($active == 2) {
            $projects = $projects->where(function ($query) use ($currentDate) {
                $query->where('projects.EndDate', '<=', $currentDate)
                    ->orwhere('projects.Active', 0);
            });
        }

        if (count($position) > 0) {
            $projects->leftJoin('users as u', 'projects.Member', 'like', DB::raw("CONCAT('%,', u.id, ',%')"))
                ->leftJoin('list_position_user as lpu', 'lpu.UserId', '=', 'u.id')
                ->where(function ($query) use ($position, $leaderId) {
                    foreach ($position as $item) {
                        $DataKey = substr($item, 0, -3);
                        $query->orwhere('lpu.DataValue', $DataKey . '002');
                    }
                    $query->orwhere('projects.Leader', 'like', '%,' . $leaderId . ',%');
                })
                ->whereNull('lpu.deleted_at');
        } else {
            $projects = $projects->where('projects.Leader', 'like', '%,' . $leaderId . ',%');
        }
        $projects = $projects->groupBy('projects.id')->get();

        if ($projects) {
            return $projects;
        } else {
            return [];
        }
    }

    public function getProjectMembersByLeader($active, $leaderId)
    {
        $currentDate = Carbon::now()->toDateString();

        $projects = Project::query()
            ->select('Member')
            ->where('projects.Leader', 'like', '%,' . $leaderId . ',%')
            ->whereNull('projects.deleted_at');
        if ($active == 1) {
            $projects->where(function ($query) use ($currentDate) {
                $query->where('projects.EndDate', '>=', $currentDate)
                    ->orwhereNull('projects.EndDate');
            })
                ->where('projects.Active', 1);
        } elseif ($active == 2) {
            $projects = $projects->where(function ($query) use ($currentDate) {
                $query->where('projects.EndDate', '<', $currentDate)
                    ->orwhere('projects.Active', 0);
            });
        }
        $members = '';
        $projects = $projects->pluck('Member')->toArray();
        foreach ($projects as $key => $item) {
            $members .= $item;
        }
        $members = array_unique(array_filter(explode(',', $members)));
        return $members;
    }

    public function getDailyReportsWithUserPosition($conditionData)
    {
        $today = $conditionData['today'];
        $month = $conditionData['month'];
        $year = $conditionData['year'];
        $selectedUserId = $conditionData['selectedUserId'];
        $selectedProjectId = $conditionData['selectedProjectId'];
        $status = $conditionData['status'];
        $currentUserId = $conditionData['currentUserId'];

        $dailyReports = DailyReport::query()
            ->select(
                'master_data.Name',
                'master_data.DataValue as TypeWork',
                'daily_reports.id as DailyReportId',
                'daily_reports.UserID',
                'projects.NameVi',
                'projects.NameShort',
                'daily_reports.Date',
                'daily_reports.ScreenName',
                'daily_reports.DateCreate',
                'daily_reports.WorkingTime',
                'daily_reports.Progressing',
                'daily_reports.Delay',
                'daily_reports.Note',
                'daily_reports.Status',
                'daily_reports.Issue',
                'daily_reports.Contents',
                'daily_reports.ProjectID',
                'daily_reports.created_at',
                'users.FullName as Reporter',
                'projects.Leader'
            )
            ->selectRaw('\'\' AS AbsentName')
            ->selectRaw('\'\' AS AbsentSDate')
            ->selectRaw('\'\' AS AbsentEDate')
            ->selectRaw('\'\' AS absencesDuration')
            ->selectRaw("GROUP_CONCAT(`lpu`.`DataValue` SEPARATOR ',') AS Position")
            ->join('projects', 'projects.id', '=', 'daily_reports.ProjectID')
            ->Join('users', 'users.id', '=', 'daily_reports.UserID')
            ->leftJoin('master_data', 'master_data.DataValue', '=', 'daily_reports.TypeWork')

            ->leftjoin(
                'list_position_user as lpu',
                function ($join) {
                    $join->on('lpu.UserId', '=', 'daily_reports.UserID')
                        ->whereNull('lpu.deleted_at');
                }
            )

            ->whereNull('projects.deleted_at')
            ->whereMonth('daily_reports.Date', $month)
            ->whereYear('daily_reports.Date', $year)
            ->whereNull('users.deleted_at');

        if (count($selectedUserId) > 0) {
            $dailyReports->whereIn('daily_reports.UserID', $selectedUserId);
        } else {
        }

        if (count($selectedProjectId) > 0) {
            $dailyReports->whereIn('projects.id', $selectedProjectId);
        } else {
            $dailyReports
                ->where(function ($query) use ($today) {
                    $query->where('projects.EndDate', '>=', $today)
                        ->orWhereNull('projects.EndDate');
                })
                ->where('projects.Active', 1);
        }

        if ($status == 1) {
            $dailyReports->where('daily_reports.status', 1)
                ->where('daily_reports.ApprovedBy', $currentUserId);
        } else {
            $dailyReports->where('daily_reports.status', 0);
        }
        $dailyReports->groupBy('daily_reports.id');

        $dailyReports
            ->orderBy('daily_reports.UserID')
            ->orderBy('daily_reports.Date', 'DESC');
        return $dailyReports->get();
    }
    /**
     * Check string null or empty
     * Create by : 2020.04.04 AKB Nguyen Thanh Tung
     * @param string $param
     * @return bool
     */
    protected function StringIsNullOrEmpty($param)
    {
        return null === $param || '' === $param;
    }

    /**
     * View layout
     * Create by : 2020.04.04 AKB Nguyen Thanh Tung
     * @param null $viewName
     * @param array $data
     * @return View
     */
    protected function viewAdminLayout($viewName = null, $data = [])
    {
        $_viewName = '';
        if (View::exists('admin.layouts.' . $this->cpn . '.' . $viewName)) {
            $_viewName = 'admin.layouts.' . $this->cpn . '.' . $viewName;
        } else {
            $_viewName = 'admin.layouts.' . config('settings.template') . '.' . $viewName;
        }
        return view($_viewName, $data);
    }

    /**
     * View popup
     * Create by : 2020.04.04 AKB Nguyen Thanh Tung
     * @param null $viewName
     * @param array $data
     * @return View
     */
    protected function viewAdminIncludes($viewName = null, $data = [])
    {
        $_viewName = '';
        if (View::exists('admin.includes.' . $this->cpn . '.' . $viewName)) {
            $_viewName = 'admin.includes.' . $this->cpn . '.' . $viewName;
        } else {
            $_viewName = 'admin.includes.' . $viewName;
        }
        return view($_viewName, $data);
    }

    /**
     * Return message success with router
     * Create by : 2020.04.04 AKB Nguyen Thanh Tung
     * @param $router
     * @return string
     */
    protected function jsonSuccessWithRouter($router)
    {
        return response()->json(['success' => route($router)]);
    }

    /**
     * Return message errors
     * Create by : 2020.04.04 AKB Nguyen Thanh Tung
     * @param $errors
     * @return string
     */
    public static function jsonErrors($errors)
    {
        return response()->json(['errors' => [$errors]]);
    }

    /**
     * Return message errors
     * Create by : 2020.04.04 AKB Tong Ly Bang
     * @param $errors
     * @return string
     */
    protected function jsonArrErrors($errors)
    {
        return response()->json(['errors' => $errors]);
    }

    /**
     * Return success
     * Create by : 2020.04.04 AKB Tong Ly Bang
     * @param $success
     * @return string
     */
    protected function jsonSuccess($success)
    {
        return response()->json(['success' => [$success]]);
    }

    public static function responseApi($status_code = 200, $error = null, $success = null, $data = null, $role = [])
    {
        return response()->json([
            'status_code'   => $status_code,
            'error'         => $error,
            'success'       => $success,
            'data'          => $data,
            'role'          => $role,
        ], $status_code);
    }

    /**
     * Đánh số thứ tự
     * Create by : 2020.04.04 AKB Tong Ly Bang
     * @param $query_array
     * @param $recordPerPage
     * @return float|int|string
     */
    protected function numericalOrder($query_array, $recordPerPage)
    {
        $page = array_key_exists('page', $query_array) ? $query_array['page'] : '';
        $stt = $page ? ($page - 1) * $recordPerPage : '';
        return $stt;
    }

    /**
     * Đánh số thứ tự có thể sắp xếp
     * @param $query_array
     * @param $recordPerPage
     * @param $sort
     * @param $count
     * @return float|int|string
     */
    protected function numericalOrderSort($query_array, $recordPerPage, $sort, $count)
    {
        $page = array_key_exists('page', $query_array) ? $query_array['page'] : '';
        $stt = $page ? $count - (($page - 1) * $recordPerPage) : $count;

        if ($sort == 'desc') {
            $stt = $page ? ($page - 1) * $recordPerPage : '';
        }
        return $stt;
    }

    /**
     * Compare Date
     * Create by : 2020.04.04 AKB Tong Ly Bang
     * @param $dateOne
     * @param $dateTwo
     * @param $message
     * @return message
     */
    protected function compareDate($dateOne, $dateTwo)
    {
        if ($dateOne != '' && $dateTwo != '') {
            $dateOne = $this->fncDateTimeConvertFomat($dateOne, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
            $dateTwo = $this->fncDateTimeConvertFomat($dateTwo, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);

            return !Carbon::parse($dateOne)->gt(Carbon::parse($dateTwo));
        }

        return true;
    }

    /**
     * Create by : 2020.04.04 AKB Nguyen Thanh Tung
     * @return \Illuminate\Config\Repository|mixed
     */
    protected function getRecordPage()
    {
        return config('settings.members_per_page');
    }

    /**
     * Get Reason Absence
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected function getReasonAbsence()
    {
        return MasterData::query()->select('Name', 'DataValue')
            ->where('DataKey', 'VM')->get();
    }

    protected function formatDateWithCol($value)
    {
        return $this->fncDateTimeConvertFomat($value, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
    }

    /**
     * @param $date
     * @return bool
     */
    protected function checkHoliday($date)
    {
        //check weekend
        if ($date->isWeekend()) {
            //kiem tra xem co phải ngày làm bù ko
            $queryOne = CalendarEvent::query()
                ->where('StartDate', '<=', $date->toDateString())
                ->where('EndDate', '>=', $date->toDateString())
                ->where('Type', 0)
                ->where('CalendarID', 1)
                ->first();
            return $queryOne ? false : true;
        } else {
            //kiểm tra xem có phải ngày nghỉ lễ ko
            $queryOne = CalendarEvent::query()
                ->where('StartDate', '<=', $date->toDateString())
                ->where('EndDate', '>=', $date->toDateString())
                ->where('Type', '!=', 0)
                ->where('CalendarID', 1)
                ->first();
            return $queryOne ? true : false;
        }
    }

    protected function getDiffHours($from, $to, $userId)
    {
        $user = User::find($userId);
        // $startTime = Carbon::parse($this->startTime);
        // $endTime = Carbon::parse($this->endTime);
        $startTime = !is_null($user->STimeOfDay) ? Carbon::parse($user->STimeOfDay) : Carbon::parse($this->startTime);
        $endTime = !is_null($user->ETimeOfDay) ? Carbon::parse($user->ETimeOfDay) : Carbon::parse($this->endTime);

        if (!isset($to)) {
            $to = Carbon::now('UTC');
        }
        if ($to->format('H:i') <= $startTime->format('H:i')) {
            $to->addDays(-1);
            $to->hour = $endTime->hour;
            $to->minute = $endTime->minute;
            $to->second = 0;
        }
        if ($to->format('H:i') > $endTime->format('H:i')) {
            $to->hour = $endTime->hour;
            $to->minute = $endTime->minute;
            $to->second = 0;
        }
        if ($from->format('H:i') >= $endTime->format('H:i')) {
            $from->addDays(1);
            $from->hour = $startTime->hour;
            $from->minute = $startTime->minute;
            $from->second = 0;
        }
        if ($from->format('H:i') < $startTime->format('H:i')) {
            $from->hour = $startTime->hour;
            $from->minute = $startTime->minute;
            $from->second = 0;
        }
        if ($from->format('H:i') >= $this->timeOutAm && $from->format('H:i') < $this->timeInPm) {
            $from->hour = Carbon::parse($this->timeInPm)->hour;
            $from->minute = Carbon::parse($this->timeInPm)->minute;
            $from->second = 0;
        }
        if ($to->format('H:i') > $this->timeOutAm && $to->format('H:i') <= $this->timeInPm) {
            $to->hour = Carbon::parse($this->timeOutAm)->hour;
            $to->minute = Carbon::parse($this->timeOutAm)->minute;
            $to->second = 0;
        }
        //nếu ngày bắt đầu rơi vào ngày nghỉ
        $nextWorkingDay = $this->getNextWorkingDays(1, $from->toDateString())[0];
        if ($from->toDateString() != $nextWorkingDay) {
            $from = Carbon::parse($nextWorkingDay);
            $from->hour = $startTime->hour;
            $from->minute = $startTime->minute;
            $from->second = 0;
        }
        //Nếu ngày kết thúc rơi vào ngày nghỉ
        $prevWorkingDay = $this->getNextWorkingDays(1, $to->toDateString(), -1)[0];
        if ($to->toDateString() != $prevWorkingDay) {
            $to = Carbon::parse($prevWorkingDay);
            $to->hour = $endTime->hour;
            $to->minute = $endTime->minute;
            $to->second = 0;
        }
        // if(
        //    ($from->format('H:i') < $this->timeOutAm && $to->format('H:i') >= $this->timeInPm)
        //    || ($from->format('H:i') < $this->timeOutAm && $from->day != $to->day)
        //    || ($to->format('H:i') > $this->timeInPm && $from->day != $to->day)
        //    || ($from->hour == $to->hour && $from->day == $to->day && $from->minute == $to->minute)
        // ){
        //    $minus = Carbon::parse($this->timeInPm)->diffInHours($this->timeOutAm);
        // }else{
        //    $minus = 0;
        // }
        $minus = Carbon::parse($this->timeInPm)->diffInHours($this->timeOutAm);
        if ($from->gte($to)) {
            return 0;
        }

        $diffDays = $to->diffInDaysFiltered(function ($date) {
                return !$this->checkHoliday($date);
            }, $from) - 1;
        if ($diffDays < 0) {
            $diffDays == 0;
        }

        $weekends = $to->diffInDaysFiltered(function ($date) {
            return $this->checkHoliday($date);
        }, $from);//Weekends or holidays
        $finalDiff = $diffDays * ($endTime->diffInHours($startTime) - $minus);
        if ($diffDays > 0) {
            $from->addDays($diffDays + $weekends);
        }

        $diffHours = $to->diffFiltered(CarbonInterval::hour(), function (Carbon $date) use ($startTime, $endTime) {
            // print_r($date->hour.'/');
            if ($this->checkHoliday($date)) {
                return 0;
            }

            if ($date->hour > $startTime->hour && $date->hour <= $endTime->hour) {
                return 1;
            } else {
                return 0;
            }
        }, $from, true);

        if ($from->hour != $startTime->hour) {
            $diffHours -= 1;
        }
        if (($from->hour > $to->hour || ($from->hour == $to->hour && $from->minute >= $to->minute)) && ($from->hour < 12 || $to->hour >= 13)) {
            $diffHours -= 1;
        }
        $finalDiff += $diffHours;
        if ($to->minute > $from->minute) {
            $correct = ($to->minute - $from->minute) / 60;
        } else {
            $correct = ($to->minute + 60 - $from->minute) / 60;
        }

        if (($from->format('H:i') < $this->timeOutAm && $to->format('H:i') > $this->timeInPm)) {
            $minus = Carbon::parse($this->timeInPm)->diffInHours($this->timeOutAm);
        } else {
            $minus = 0;
        }

        $finalDiff += $correct - $minus;
        // echo $finalDiff;
        return $finalDiff;
    }

    //lấy danh sách các ngày làm việc tiếp theo
    protected function getNextWorkingDays($intNum, $datetime, $sub = 1)
    {
        $intCount = 0;
        $arrDates = [];
        while ($intCount < $intNum) {
            $weekDay = Carbon::parse($datetime)->dayOfWeek;
            //Truong hop neu la ngay nghi cuoi tuan
            if (!in_array($weekDay, $this->working_days)) {
                //kiem tra xem co phai ngay lam bu khong
                $queryOne = CalendarEvent::query()
                    ->where('StartDate', '<=', Carbon::parse($datetime)->toDateString())
                    ->where('EndDate', '>=', Carbon::parse($datetime)->toDateString())
                    ->where('Type', 0)
                    ->where('CalendarID', 1)
                    ->first();
                if ($queryOne) {
                    $arrDates[] = Carbon::parse($datetime)->toDateString();
                    $intCount++;
                }
            } else {
                //kiem tra xem ngay hien tai co phai ngay le hay ngay nghi khong
                $queryOne = CalendarEvent::query()
                    ->where('StartDate', '<=', Carbon::parse($datetime)->toDateString())
                    ->where('EndDate', '>=', Carbon::parse($datetime)->toDateString())
                    ->where('Type', '!=', 0)
                    ->where('CalendarID', 1)
                    ->first();
                if (!$queryOne) {
                    $arrDates[] = Carbon::parse($datetime)->toDateString();
                    $intCount++;
                }
            }
            $datetime = Carbon::parse($datetime)->addDays($sub);
        }
        return $arrDates;
    }

    // Lấy ip hiện tại
    protected function getIp()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }

    public static function translateToWords($number)
    {
        $max_size = pow(10, 18);
        $string = 'không';
        if (!$number) return "không";
        if (is_int($number) && $number < abs($max_size)) {
            switch ($number) {
                // set up some rules for converting digits to words
                case $number < 0:
                    $prefix = "negative";
                    $suffix = AdminController::translateToWords(-1 * $number);
                    $string = $prefix . " " . $suffix;
                    break;
                case 1:
                    $string = "một";
                    break;
                case 2:
                    $string = "hai";
                    break;
                case 3:
                    $string = "ba";
                    break;
                case 4:
                    $string = "bốn";
                    break;
                case 5:
                    $string = "năm";
                    break;
                case 6:
                    $string = "sáu";
                    break;
                case 7:
                    $string = "bảy";
                    break;
                case 8:
                    $string = "tám";
                    break;
                case 9:
                    $string = "chín";
                    break;
                case 10:
                    $string = "mười";
                    break;
                case $number < 20:
                    $string = AdminController::translateToWords($number % 10);
                    $suffix = "mười";
                    if ($number % 10 == 5) {
                        $string = "mười lăm";
                    } else {
                        $string = $suffix . " " . $string;
                    }
                    break;
                case 20:
                    $string = "hai mươi";
                    break;
                case 30:
                    $string = "ba mươi";
                    break;
                case 40:
                    $string = "bốn mươi";
                    break;
                case 50:
                    $string = "năm mươi";
                    break;
                case 60:
                    $string = "sáu mươi";
                    break;
                case 70:
                    $string = "bảy mươi";
                    break;
                case 80:
                    $string = "tám mươi";
                    break;
                case 90:
                    $string = "chín mươi";
                    break;
                case $number < 100:
                    $prefix = AdminController::translateToWords($number - $number % 10);
                    $suffix = AdminController::translateToWords($number % 10);
                    $string = $prefix . " " . $suffix;
                    break;
                // handles all number 100 to 999
                case $number < pow(10, 3):
                    // floor return a float not an integer
                    $prefix = AdminController::translateToWords(intval(floor($number / pow(10, 2)))) . " trăm";
                    $suffix = '';
                    if ($number % pow(10, 2)) {
                        $suffix = AdminController::translateToWords($number % pow(10, 2));
                    }
//                    dd($number%pow(10,2));
                    if ($number % pow(10, 2) < 10 && $number % pow(10, 2) != 0) {
                        $string = $prefix . " linh " . $suffix;
                    } elseif ($number % pow(10, 2) == 0) {
                        $string = $prefix;
                    } else {
                        $string = $prefix . " " . $suffix;
                    }
                    break;
                case $number < pow(10, 6):
                    // floor return a float not an integer
                    $prefix = AdminController::translateToWords(intval(floor($number / pow(10, 3)))) . " nghìn";
                    $suffix = '';
                    if ($number % pow(10, 3)) {
                        $suffix = AdminController::translateToWords($number % pow(10, 3));
                    }
                    $string = $prefix . " " . $suffix;
                    break;
                case $number < pow(10, 9):
                    // floor return a float not an integer
                    $prefix = AdminController::translateToWords(intval(floor($number / pow(10, 6)))) . " triệu";
                    $suffix = '';
                    if ($number % pow(10, 6)) {
                        $suffix = AdminController::translateToWords($number % pow(10, 6));
                    }
                    $string = $prefix . " " . $suffix;
                    break;
                case $number < pow(10, 12):
                    // floor return a float not an integer
                    $prefix = AdminController::translateToWords(intval(floor($number / pow(10, 9)))) . " tỷ";
                    $suffix = '';
                    if ($number % pow(10, 9)) {
                        $suffix = AdminController::translateToWords($number % pow(10, 9));
                    }
                    $string = $prefix . " " . $suffix;
                    break;
                case $number < pow(10, 15):
                    // floor return a float not an integer
                    $prefix = AdminController::translateToWords(intval(floor($number / pow(10, 12)))) . " nghìn tỷ";
                    $suffix = '';
                    if ($number % pow(10, 12)) {
                        $suffix = AdminController::translateToWords($number % pow(10, 12));
                    }
                    $string = $prefix . " " . $suffix;
                    break;
                case $number < pow(10, 18):
                    // floor return a float not an integer
                    $prefix = AdminController::translateToWords(intval(floor($number / pow(10, 15)))) . " triệu tỷ";
                    $suffix = '';
                    if ($number % pow(10, 15)) {
                        $suffix = AdminController::translateToWords($number % pow(10, 15));
                    }
                    $string = $prefix . " " . $suffix;
                    break;
                default:
                    break;
            }
        }
        return $string;
    }

    public static function getRoleByScreen($screen = null)
    {
        if ($screen == null) {
            return array();
        }

        $role = array();
        $user = is_null(Auth::user()) ? Auth::guard('api')->user() : Auth::user();
        $listRoleUser = Controller::getRoleUser($user);
        $listRoleAll = RoleScreenDetail::LIST_ROLE;
        if (array_key_exists($screen, $listRoleAll)) {
            foreach ($listRoleAll[$screen] as $key => $value) {
                if (in_array($value, $listRoleUser)) {
                    $role[$screen][] = $key;
                }
            }
        }

        return $role;
    }

    public static function getAllRoleAssign()
    {
        $role = array();
        $user = is_null(Auth::user()) ? Auth::guard('api')->user() : Auth::user();
        $listRoleUser = Controller::getRoleUser($user);
        $listRoleAll = RoleScreenDetail::LIST_ROLE;

        foreach ($listRoleAll as $role_screen_alias => $value) {
            foreach ($value as $key => $alias) {
                if (in_array($alias, $listRoleUser)) {
                    $role[$role_screen_alias][] = $key;
                }
            }
        }

        return $role;
    }

    /**
     * RoleView
     * Create by : 2021.03.05 AKB Nguyen Thuy Tien
     */
    protected function RoleView($admin, $view)
    {
        $rst = array(
            'menu' => array(),
            'role' => array(),
        );

        if (isset($admin))
            $rst['menu'] = Menu::query()->where('RouteName', 'admin.' . $admin)->first();
        if (count($view) >= 1) {
            $query = \App\RoleScreenDetail::query();
            foreach ($view as $value) {
                $query = $query->orWhere('role_screen_alias', $value);
            }
            $rst['role'] = $query->get();
        }
        return $rst;
    }

    protected function detailRoleScreen($alias)
    {
        foreach ($this->role_list as $key => &$_role) {
            $alias_role = $key == 'View' ? $alias : $alias . $key;
            $_role = \App\RoleScreenDetail::alias($alias_role)->first();
        }
    }

    public function checkManager($userId)
    {
        $isManager = false;
        $userPosition = ListPosition::query()
            ->select('DataKey')
            ->join('list_position_user as p', 'p.DataValue', '=', 'list_position.DataValue')
            ->join('users as u', 'u.id', '=', 'p.UserId')
            ->where('u.id', $userId)
            ->where('p.deleted_at', '=', null)
            ->groupBy('DataKey')
            ->get();

        foreach ($userPosition as $item) {
            if ($item->DataKey == 'QL') {
                $isManager = true;
            }
        }
        return $isManager;
    }
}
