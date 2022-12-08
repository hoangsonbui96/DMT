<?php

namespace App\Http\Controllers\Admin;

use App\DailyReport;
use App\Equipment;
use App\EquipmentRegistration;
use App\EquipmentType;
use App\EquipmentUsingHistory;
use App\Http\Controllers\Module\ModuleController;
use App\Members;
use App\Model\Absence;
use App\model\ListPosition;
use App\OvertimeWork;
use App\Project;
use App\RoleScreenDetail;
use App\Room;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Event\Entities\Question;

class AjaxController extends AdminController
{
    public function __construct(Request $request)
    {
        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
    }

    public function meetingList(Request $request)
    {
    }

    public function getTimeOfDayUser($id = null)
    {
        if ($id != null) {
            $users = User::query()->select('id', 'STimeOfDay', 'ETimeOfDay')
                ->where('id', $id)->get();

            return $users;
        }
    }

    public function getUsersByRoom($id = null)
    {
        if ($id != null) {
            return User::query()
                ->select('id', 'FullName', 'STimeOfDay', 'ETimeOfDay')
                ->where('RoomId', $id)
                ->where('Active', 1)
                ->where('deleted', 0)
                ->get()
                ->toArray();
        } else {
            return [];
        }
    }

    public function getUsersByActive($action)
    {
        if ($action != null) {
            $users = User::query()
                ->where('deleted', '!=', 1)
                ->where('role_group', '!=', 1)
                ->where(function ($query) use ($action) {
                    if ($action == 2) {
                        $query->where('Active', 1);
                    } elseif ($action == 3) {
                        $query->where('Active', 0);
                    }
                })->get();
            $users->toArray();
            return $users;
        } else {
            return [];
        }
    }

    public function getUsersByActiveAndLeaderPosition($active)
    {
        $currentDate = Carbon::now()->toDateString();
        $leaderId = auth()->id();

        $members = $this->getProjectMembersByLeader($active, $leaderId);

        $position = $this->getUserPosition($leaderId);
        if ($active != null) {
            $users = User::query()->select('users.id', 'users.FullName');
            if (count($position) > 0) {
                $users->leftJoin('list_position_user as lpu', 'lpu.UserId', '=', 'users.id')
                    ->where(function ($query) use ($position, $members) {
                        foreach ($position as $item) {
                            if ($item == 'CL001') {
                                $query->orwhere('lpu.DataValue', 'CL002');
                            } elseif ($item == 'TL001') {
                                $query->orwhere('lpu.DataValue', 'TL002');
                            }
                        }
                        $query->orwhereIn('users.id', $members);
                    })
                    ->whereNull('lpu.deleted_at');
            } else {
                $users = $users->whereIn('users.id', $members);
            }
            $users = $users->where('deleted', '!=', 1)
                ->where('role_group', '!=', 1)
                ->where(function ($query) use ($active) {
                    if ($active == 1) {
                        $query->where('Active', 1);
                    } elseif ($active == 2) {
                        $query->where('Active', 0);
                    }
                })->groupBy('users.id')->get();
            $users->toArray();
            return $users;
        } else {
            return [];
        }
    }

    public function getProjectsByActive($active = 1)
    {
        $leaderId = auth()->id();
        $position = $this->getUserPosition($leaderId);
        $projects = [];
        $currentDate = Carbon::now()->toDateString();
        $projects = Project::query()
            ->select(
                'projects.id',
                'projects.NameVi'
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
                $query->where('projects.EndDate', '<', $currentDate)
                    ->orwhere('projects.Active', 0);
            });
        }
        if (count($position) > 0) {
            $projects->leftJoin('list_position_user as lpu', 'projects.Member', 'like', DB::raw("CONCAT('%,', lpu.UserId, ',%')"))
                ->where(function ($query) use ($position, $leaderId) {
                    foreach ($position as $item) {
                        if ($item == 'CL001') {
                            $query->orwhere('lpu.DataValue', 'CL002');
                        } elseif ($item == 'TL001') {
                            $query->orwhere('lpu.DataValue', 'TL002');
                        }
                    }
                    $query->orwhere('projects.Leader', 'like', '%,' . $leaderId . ',%');
                })
                ->whereNull('lpu.deleted_at');
        } else {
            $projects = $projects->where('projects.Leader', 'like', '%,' . $leaderId . ',%');
        }
        $projects = $projects->groupBy('projects.id')->get();

        if ($projects) {
            return $projects->toArray();
        } else {
            return [];
        }
    }

    public function getEquipmentList(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'eqType' => 'array|nullable',
            'eqOwner' => 'integer|required'
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()->all()]);
        $validated = $validator->validated();

        $eqList = Equipment::query();
        if (isset($validated['eqType']) && count($validated['eqType'])) {
            $eqList = $eqList->where(function ($query) use ($validated) {
                foreach ($validated['eqType'] as $item) {
                    $query->orWhere('type_id', $item);
                }
            });
        }

        $eqList = $eqList->where('user_owner', $validated['eqOwner']);

        $eqList = $eqList->get()->toArray();
        return $eqList;
    }

    public function getEquipmentTypeList(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'change_id' => 'integer|nullable',
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()->all()]);
        $validated = $validator->validated();
        // return $validated;
        $eqTypeList = EquipmentType::query();
        if (is_null($validated['change_id'])) {
            return array();
        } elseif ($validated['change_id'] == 2) {
            $eqTypeList = $eqTypeList->join('equipment', 'equipment.type_id', 'equipment_types.type_id')
                ->where('equipment.user_owner', Auth::user()->id)
                ->groupBy('equipment_types.type_id');
        }
        // $eqList = $eqList->get()->toArray();
        return $eqTypeList->get()->toArray();
    }

    public function getEquipmentStatus(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'code' => 'string|nullable',
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()->all()]);
        $validated = $validator->validated();
        // return $validated;
        $eqStatus = DB::table('master_data');
        if (is_null($validated['code'])) {
            return array();
        } else {
            $eqStatus = $eqStatus->join('equipment', 'equipment.status_id', 'master_data.id')
                ->where('equipment.code', $validated['code']);
        }

        $eqStatus = $eqStatus->select('master_data.*');
        // $eqList = $eqList->get()->toArray();
        return $eqStatus->get()->toArray();
    }

    public function saveHandover(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'eq1'              => 'required|array',
            'eq1.*'            => 'required|string',
            'receive_owners'   => 'required|array',
            'receive_owners.*' => 'required|integer',
            'deal_date'        => 'required|array',
            'deal_date.*'      => 'required|date_format:d/m/Y',
            'note'             => 'required|array',
            'note.*'           => 'string|nullable',
        ]);
        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()->all()]);
        $validated = $validator->validated();
        foreach ($validated['eq1'] as $key => $value) {
            //insert into equipment_histories table
            //check receiver

            $equipment = Equipment::query()
                ->where('code', $validated['eq1'][$key])
                ->first();
            if ($equipment) {
                $receiver = User::find($validated['receive_owners'][$key]);
                $history = new EquipmentUsingHistory();
                $history->code = $validated['eq1'][$key];
                $history->user_owner = null;
                $history->deal_date = $this->fncDateTimeConvertFomat($validated['deal_date'][$key], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                $history->note = $validated['note'][$key];
                $history->created_user = Auth::user()->id;
                $history->deal_flag = 1;
                $history->old_user_owner = $equipment->user_owner;
                $history->old_status_id = $equipment->status_id;
                $history->status_id = $equipment->status_id;
                if ($receiver) {
                    $history->user_owner = $validated['receive_owners'][$key];
                    // $history->status_id = 16;
                } else {
                    $history->user_owner = 0;
                    $validated['receive_owners'][$key] = 0;
                }

                $history->save();

                $equipment->user_owner = $validated['receive_owners'][$key];
                $equipment->status_id = $history->status_id;
                $equipment->save();
            }
        }
        if (strpos(\Request::getRequestUri(), 'api') !== false) {
            return AdminController::responseApi(200, null, __('admin.success.save'));
        }
        return $validated;
    }

    public function checkAddRegistration(Request $request)
    {
        // return $request->input();
        $checkType = EquipmentRegistration::query()
            ->where('type_id', $request->input('typeId'))
            ->where('change_id', $request->input('changeId'))
            ->where('user_id', Auth::user()->id)
            ->where('id', '<>', $request->input('id'))
            ->whereNull('code')
            ->where('status', 0)
            ->first();
        $checkEq = EquipmentRegistration::query()
            ->where('user_id', Auth::user()->id)
            ->where('code', $request->input('eqId'))
            ->where('id', '<>', $request->input('id'))
            ->whereNotNull('code')
            ->where('status', 0)
            ->first();
        if ($checkType || $checkEq) {
            if (strpos(\Request::getRequestUri(), 'api') !== false) {
                return AdminController::responseApi(422, "Đăng ký thay đổi thiết bị đã tồn tại");
            }
            return 0;
        } else {
            return AdminController::responseApi(200, null, __('admin.success.save'));
            return 1;
        }
    }

    public function equipmentApproveList(Request $request)
    {
        // return $request->input();
        $itemList = Equipment::query()
            ->where('user_owner', $request->input('user_owner'))
            ->where('type_id', $request->input('type_id'))
            ->where('status_id', 15)
            ->get()->toArray();
        return $itemList;
    }

    //User by Overtime // 19.5
    public function getUsersByOvertime($id = null)
    {
        if ($id != null) {
            $users = User::query()
                ->select('id', 'FullName')
                ->where('Active', 1)
                ->where('deleted', 0)
                ->get()
                ->toArray();
            return $users;
        } else {
            return array();
        }
    }

    //Project,user overtime // 19.5
    public function getProjectByUserId($id = null)
    {
        $now_string = Carbon::now()->toDateString();
        if ($id != null) {
            $projects = Project::query()
                ->where('Active', 1)
                ->where(function ($query) use ($now_string) {
                    $query->where('EndDate', '>=', $now_string)
                        ->orWhereNull('EndDate');
                })
                ->where(function ($query) use ($id) {
                    $query->where('Member', 'like', '%' . $id . '%')
                        ->orWhere('Leader', 'like', '%' . $id . '%');
                })->get()
                ->toArray();
            return $projects;
        } else {
            return array();
        }
    }

    public function getAllNotification(Request $request)
    {
        $today = Carbon::now()->toDateString();
        // Empty array
        $absenceList3Days = [];
        $listAbsenceApproved = [];
        $listAbsences = [];
        $listOTApproved = [];
        $listOT = [];

        //Prepare variable
        $user_auth = \auth()->user();
        $currentUserId = $user_auth->id;
        $currentMonth = date('m');
        $rqDateNow = date_create($request->input('dateNow') ? $request->input('dateNow') : date(self::FOMAT_DB_YMD));
        $dayOfTheWeek = Carbon::now()->dayOfWeek;
        $weekday = self::WEEK_MAP[$dayOfTheWeek];
        $range_date = $weekday == "T6" ? 4 : 2;
        $date_create = date(self::FOMAT_DB_YMD, mktime(0, 0, 0, date("m"), date("d") + $range_date, date("Y")));
        $rqNext2Day = date_create($request->filled("next2Day")
            ? $request->get("next2Day")
            : $date_create);

        // absence request
        if ($user_auth->can('action', RoleScreenDetail::where('alias', 'AbsenceListApprove')->first())) {
            $absenceList = Absence::query()
                ->select('absences.*', 'users.FullName', 'master_data.Name')
                ->leftJoin('users', 'absences.UID', '=', 'users.id')
                ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
                ->where('users.Active', '=', 1)
                ->where('absences.RequestManager', 'LIKE', "%," . $user_auth->id . ",%")
                ->get()->toArray();
            foreach ($absenceList as $absence) {
                if ($absence['Approved'] == 1 || $absence['Approved'] == 2) {
                    $listAbsences[] = $absence;
                } else {
                    $listAbsenceApproved[] = $absence;
                }
            }
        }

        // absence in next 3 days
        if ($user_auth->can('action', RoleScreenDetail::query()->where('alias', 'AbsenceList')->first())) {
            $rqDate_format = date_format($rqDateNow, self::FOMAT_DB_YMD);
            $rqNext2Day_format = date_format($rqNext2Day, self::FOMAT_DB_YMD);
            $sql_raw = '((CAST(absences.SDate AS DATE) >= \''
                . $rqDate_format .
                '\' AND CAST(absences.SDate AS DATE) <= \''
                . $rqNext2Day_format .
                '\') OR (CAST(absences.EDate AS DATE) >= \''
                . $rqDate_format . '\' AND CAST(absences.EDate AS DATE) <= \''
                . $rqNext2Day_format . '\') OR (CAST(absences.SDate AS DATE) <= \''
                . $rqDate_format . '\' AND CAST(absences.EDate AS DATE) >= \''
                . $rqNext2Day_format . '\'))';

            $absenceList3Days = Absence::query()
                ->select('absences.*', 'users.FullName', 'master_data.Name')
                ->leftJoin('users', 'absences.UID', '=', 'users.id')
                ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
                ->where('users.Active', '=', 1)
                ->where('absences.Approved', '!=', 2)
                ->whereRaw($sql_raw)
                ->get()->toArray();
        }

        if ($user_auth->can('action', RoleScreenDetail::where('alias', 'OvertimeDetailsApprove')->first())) {
            // OT request
            $OTList = OvertimeWork::query()
                ->select('overtime_works.*', 'users.FullName')
                ->leftJoin('users', 'overtime_works.UserID', '=', 'users.id')
                ->where('users.Active', '=', 1)
                ->where('overtime_works.RequestManager', 'LIKE', "%," . $user_auth->id . ",%")
                ->get()->toArray();
            foreach ($OTList as $oT) {
                if ($oT['Approved'] == 1 || $oT['Approved'] == 2) {
                    $listOT[] = $oT;
                } else {
                    $listOTApproved[] = $oT;
                }
            }
        }

        // If module Event is enable
        $questionList = ModuleController::eventNotification();

        // Birthday in month
        $users = User::query()
            ->leftJoin('rooms', 'users.RoomId', '=', 'rooms.id')
            ->whereRaw('MONTH(users.Birthday) = ' . $currentMonth)
            ->where('users.Active', 1)
            ->orderByRaw('DAY(users.Birthday)')
            ->get()->toArray();

        // Need approving report list
        $currentUserPositions = [];
        $currentUserPositions = $this->getUserPosition($currentUserId);

        $dailyReportsWithUserPosition = DailyReport::query()
            ->select(
                'daily_reports.id AS DailyReportId',
                'projects.Leader'
            )
            ->selectRaw("GROUP_CONCAT(`lpu`.`DataValue` SEPARATOR ',') AS Position")
            ->leftjoin('list_position_user as lpu', function ($join) {
                $join->on('lpu.UserId', '=', 'daily_reports.UserID')
                    ->whereNull('lpu.deleted_at');
            })
            ->join('projects', 'projects.id', '=', 'daily_reports.ProjectID')
            ->where('daily_reports.status', 0)
            ->whereNull('projects.deleted_at')
            ->groupBy('DailyReportId')
            ->orderBy('daily_reports.UserID')
            ->orderBy('daily_reports.Date', 'DESC')
            ->get();

        $dailyReports = $dailyReportsWithUserPosition;
        $dailyReportsByLeader = clone $dailyReports;
        foreach ($dailyReports as $key => $item) {

            if (isset($item->Leader)) {
                if (
                    (isset($item->Position)
                        && (str_contains($item->Position, 'CL002') || str_contains($item->Position, 'TL002'))
                        || (!str_contains($item->Leader, ',' . $currentUserId . ',')))
                ) {
                    unset($dailyReportsByLeader[$key]);
                }
            } else {
                unset($dailyReportsByLeader[$key]);
            }
        }
        $dailyReportsByLeaderComtor = clone $dailyReports;
        foreach ($dailyReports as $key => $item) {
            if (isset($item->Position)) {
                if (!str_contains($item->Position, 'CL002')) {
                    unset($dailyReportsByLeaderComtor[$key]);
                }
            } else {
                unset($dailyReportsByLeaderComtor[$key]);
            }
        }

        $dailyReportsByLeaderTester = clone $dailyReports;
        foreach ($dailyReports as $key => $item) {
            if (isset($item->Position)) {
                if (!str_contains($item->Position, 'TL002')) {
                    unset($dailyReportsByLeaderTester[$key]);
                }
            } else {
                unset($dailyReportsByLeaderTester[$key]);
            }
        }
        if (!in_array('CL001', $currentUserPositions) && !in_array('TL001', $currentUserPositions)) {
            $dailyReports = $dailyReportsByLeader;
        } elseif (in_array('CL001', $currentUserPositions) && in_array('TL001', $currentUserPositions)) {
            $dailyReports = $dailyReportsByLeader;
            $dailyReports = $dailyReports->concat($dailyReportsByLeaderComtor);
            $dailyReports = $dailyReports->concat($dailyReportsByLeaderTester);
            $dailyReports = $dailyReports->unique('DailyReportId');
        } elseif (in_array('CL001', $currentUserPositions)) {
            $dailyReports = $dailyReportsByLeader->concat($dailyReportsByLeaderComtor);
            $dailyReports = $dailyReports->unique('DailyReportId');
        } elseif (in_array('TL001', $currentUserPositions)) {
            $dailyReports = $dailyReportsByLeader->concat($dailyReportsByLeaderTester);
            $dailyReports = $dailyReports->unique('DailyReportId');
        }
        if ($user_auth->can('action', RoleScreenDetail::query()->where('alias', 'NeedApproveReports')->first())) {
            $totalNeedApprDailyReports = $dailyReports->count();
        }
        //        if (strpos(\Request::getRequestUri(), 'api') !== false) {
        //            return $arrResult;
        //        }
        //        return json_encode($arrResult);
        return [
            'FullName'  => \auth()->user()->FullName,
            'birthday' => [
                'currentMonth' => $currentMonth,
                'listData' => $users
            ],
            'absence' => [
                'request' => [
                    'all' => [
                        'count' => count($listAbsenceApproved),
                        'listData' => $listAbsenceApproved
                    ],
                    'threeDays' => [
                        'count' => count($absenceList3Days),
                        'listData' => $absenceList3Days
                    ],
                ],
                'approved' => [
                    'count' => count($listAbsences),
                    'listData' => $listAbsences,
                ]
            ],
            'events' => [
                'count' => count($questionList),
                'listData' => $questionList,
            ],
            'overtimes' => [
                'request' => [
                    'count' => count($listOTApproved),
                    'listData' => $listOTApproved
                ],
                'overtime' => [
                    'count' => count($listOT),
                    'listData' => $listOT
                ]
            ],
            'dailyReports' => [
                'count' => $totalNeedApprDailyReports
            ],
            // 'workingshedule' => $workingshedule,
        ];
    }

    public function getNotificationAPI(Request $request)
    {
        $idUID = \auth()->id();
        $absenceList3Days = [];
        $listAbsenceApproved = [];
        $listAbsences = [];
        $listOTApproved = [];
        $listOT = [];
        $QuestionList = [];
        $currentMonth = date('m');
        $rqDateNow = date_create($request->input('dateNow') ? $request->input('dateNow') : date(self::FOMAT_DB_YMD));
        $dayOfTheWeek = Carbon::now()->dayOfWeek;
        $weekday = self::WEEK_MAP[$dayOfTheWeek];
        $rqNext3Day = date_create($request->input('next3Day') ? $request->input('next3Day') : date(self::FOMAT_DB_YMD, mktime(0, 0, 0, date("m"), date("d") + 2, date("Y"))));
        $rqBack1Day = date_create($request->input('back1Day') ? $request->input('back1Day') : date(self::FOMAT_DB_YMD, mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"))));
        // if($weekday=='T6'){00000
        //     $rqNext1Day = date_create($request->input('next1Day') ? $request->input('next1Day') : date(self::FOMAT_DB_YMD, mktime(0, 0, 0, date("m")  , date("d")+4, date("Y"))));
        //     $rqBack1Day = date_create($request->input('back1Day') ? $request->input('back1Day') : date(self::FOMAT_DB_YMD, mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"))));
        // }
        // else{
        //     $rqNext1Day = date_create($request->input('next1Day') ? $request->input('next1Day') : date(self::FOMAT_DB_YMD, mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"))));
        //     $rqBack1Day = date_create($request->input('back1Day') ? $request->input('back1Day') : date(self::FOMAT_DB_YMD, mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"))));
        //     // dd('e',$rqNext1Day);
        // }

        // absence request
        if (Auth::user()->can('action', RoleScreenDetail::query()->where('alias', 'AbsenceListApprove')->first())) {
            $absenceList = Absence::query()
                ->select('absences.*', 'users.FullName', 'master_data.Name')
                ->leftJoin('users', 'absences.UID', '=', 'users.id')
                ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
                ->where('users.Active', '=', 1)
                ->where('absences.RequestManager', 'LIKE', "%," . Auth::user()->id . ",%")
                ->get()->toArray();
            foreach ($absenceList as $absence) {
                if ($absence['Approved'] == 1 || $absence['Approved'] == 2) {
                    $listAbsences[] = $absence;
                } else {
                    $listAbsenceApproved[] = $absence;
                }
            }
        }

        if (Auth::user()->can('action', RoleScreenDetail::query()->where('alias', 'AbsenceList')->first())) {
            // absence in next 3 days
            $absenceList3Days = Absence::query()
                ->select('absences.*', 'users.FullName', 'master_data.Name')
                ->leftJoin('users', 'absences.UID', '=', 'users.id')
                ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
                ->where('users.Active', '=', 1)
                ->where('absences.Approved', '!=', 2)
                ->whereRaw('((CAST(absences.SDate AS DATE) >= \'' . date_format($rqDateNow, self::FOMAT_DB_YMD) . '\' AND CAST(absences.SDate AS DATE) <= \'' . date_format($rqNext3Day, self::FOMAT_DB_YMD) . '\') OR (CAST(absences.EDate AS DATE) >= \'' . date_format($rqDateNow, self::FOMAT_DB_YMD) . '\' AND CAST(absences.EDate AS DATE) <= \'' . date_format($rqNext3Day, self::FOMAT_DB_YMD) . '\') OR (CAST(absences.SDate AS DATE) <= \'' . date_format($rqDateNow, self::FOMAT_DB_YMD) . '\' AND CAST(absences.EDate AS DATE) >= \'' . date_format($rqNext3Day, self::FOMAT_DB_YMD) . '\'))')
                ->get()->toArray();
        }
        // nghi trong 3 ngay
        $arrAbs = [];
        foreach ($absenceList3Days as $abs) {
            if ($idUID === $abs['UID']) {
                array_push($arrAbs, $abs);
            }
        }
        $this->data['absenceList'] = $arrAbs;
        if (Auth::user()->can('action', RoleScreenDetail::query()->where('alias', 'OvertimeDetails')->first())) {
            // overtime in next 3 days
            $overtimeList3Days = OvertimeWork::query()
                ->select('overtime_works.*', 'users.FullName')
                ->leftJoin('users', 'overtime_works.UserID', '=', 'users.id')
                ->where('users.Active', '=', 1)
                ->where('overtime_works.Approved', '!=', 2)
                ->whereRaw('((CAST(overtime_works.STime AS DATE) >= \'' . date_format($rqDateNow, self::FOMAT_DB_YMD) . '\' AND CAST(overtime_works.STime AS DATE) <= \'' . date_format($rqNext3Day, self::FOMAT_DB_YMD) . '\') OR (CAST(overtime_works.STime AS DATE) >= \'' . date_format($rqDateNow, self::FOMAT_DB_YMD) . '\' AND CAST(overtime_works.STime AS DATE) <= \'' . date_format($rqNext3Day, self::FOMAT_DB_YMD) . '\') OR (CAST(overtime_works.STime AS DATE) <= \'' . date_format($rqDateNow, self::FOMAT_DB_YMD) . '\' AND CAST(overtime_works.STime AS DATE) >= \'' . date_format($rqNext3Day, self::FOMAT_DB_YMD) . '\'))')
                ->get()->toArray();
        }
        $arrOvertime = [];
        foreach ($overtimeList3Days as $abs) {
            if ($idUID === $abs['UserID']) {
                array_push($arrOvertime, $abs);
            }
        }
        $this->data['overtimeList'] = $arrOvertime;

        // bao cao trc do 1ngay
        $reportList3Days = DailyReport::query()
            ->select('daily_reports.*', 'users.FullName')
            ->leftJoin('users', 'daily_reports.UserID', '=', 'users.id')
            ->where('users.Active', '=', 1)
            ->whereRaw('((CAST(daily_reports.Date AS DATE) >= \'' . date_format($rqBack1Day, self::FOMAT_DB_YMD) . '\' AND CAST(daily_reports.Date AS DATE) < \'' . date_format($rqDateNow, self::FOMAT_DB_YMD) . '\') )')
            ->get()->toArray();
        $arrDaily = [];
        foreach ($reportList3Days as $rp) {
            if ($idUID === $rp['UserID']) {
                array_push($arrDaily, $rp);
            }
        }
        $this->data['dailyreport'] = $arrDaily;
        // working in next 3 days
        $workingshedule = DB::table('working_schedule')
            ->whereRaw('((CAST(working_schedule.Date AS DATE) >= \'' . date_format($rqDateNow, self::FOMAT_DB_YMD) . '\' AND CAST(working_schedule.Date AS DATE) <= \'' . date_format($rqNext3Day, self::FOMAT_DB_YMD) . '\'))')
            // ->whereRaw('Date = \''.date_format($rqDateNow,self::FOMAT_DB_YMD).'\'')
            ->get()->toArray();

        // all project your
        $this->data['all_project'] = Project::query()
            ->where('Member', 'like', '%' . Auth::user()->id . '%')
            ->orWhere('Leader', 'like', '%' . Auth::user()->id . '%')->get();

        //  lich cong tac trong 3 ngay
        $this->data['workingshedule'] = [];
        $arrWor = [];
        $Room = Room::query()->select('id', 'Name')->get();
        foreach ($workingshedule as $wr) {

            foreach ($Room as $rp) {
                if ($wr->roomsID === $rp['id']) {
                    $roomsName = $rp['Name'];
                    $wr->roomsName = $roomsName;
                }
            }
            if (in_array($idUID, explode(',', $wr->AssignID)) || $wr->AssignID == '0') {
                array_push($arrWor, $wr);
            }
        }
        $this->data['workingshedule'] = $arrWor;
        $this->data['count'] = count($arrWor) + count($arrOvertime) + count($arrAbs);
        return response()->json(['data' => $this->data]);
    }

    public function getCountAllNotification(Request $request)
    {
        $absenceList3Days = array();
        $listAbsenceApproved = array();
        $listAbsences = array();
        $listOTApproved = array();
        $listOT = array();
        $QuestionList = array();

        $currentMonth = date('m');
        $rqDateNow = date_create($request->input('dateNow') ? $request->input('dateNow') : date(self::FOMAT_DB_YMD));
        $dayOfTheWeek = Carbon::now()->dayOfWeek;
        $weekday = self::WEEK_MAP[$dayOfTheWeek];
        if ($weekday == 'T6')
            $rqNext2Day = date_create($request->input('next2Day') ? $request->input('next2Day') : date(self::FOMAT_DB_YMD, mktime(0, 0, 0, date("m"), date("d") + 4, date("Y"))));
        else
            $rqNext2Day = date_create($request->input('next2Day') ? $request->input('next2Day') : date(self::FOMAT_DB_YMD, mktime(0, 0, 0, date("m"), date("d") + 2, date("Y"))));

        // absence request
        if (Auth::user()->can('action', RoleScreenDetail::query()->where('alias', 'AbsenceListApprove')->first())) {
            $absenceList = Absence::query()
                ->select('absences.*', 'users.FullName', 'master_data.Name')
                ->leftJoin('users', 'absences.UID', '=', 'users.id')
                ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
                ->where('users.Active', '=', 1)
                ->where('absences.RequestManager', 'LIKE', "%," . Auth::user()->id . ",%")
                ->get()->toArray();
            foreach ($absenceList as $absence) {
                if ($absence['Approved'] == 1 || $absence['Approved'] == 2) {
                    $listAbsences[] = $absence;
                } else {
                    $listAbsenceApproved[] = $absence;
                }
            }
        }

        if (Auth::user()->can('action', RoleScreenDetail::query()->where('alias', 'AbsenceList')->first())) {
            // absence in next 3 days
            $absenceList3Days = Absence::query()
                ->select('absences.*', 'users.FullName', 'master_data.Name')
                ->leftJoin('users', 'absences.UID', '=', 'users.id')
                ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
                ->where('users.Active', '=', 1)
                ->where('absences.Approved', '!=', 2)
                ->whereRaw('((CAST(absences.SDate AS DATE) >= \'' . date_format($rqDateNow, self::FOMAT_DB_YMD) . '\' AND CAST(absences.SDate AS DATE) <= \'' . date_format($rqNext2Day, self::FOMAT_DB_YMD) . '\') OR (CAST(absences.EDate AS DATE) >= \'' . date_format($rqDateNow, self::FOMAT_DB_YMD) . '\' AND CAST(absences.EDate AS DATE) <= \'' . date_format($rqNext2Day, self::FOMAT_DB_YMD) . '\') OR (CAST(absences.SDate AS DATE) <= \'' . date_format($rqDateNow, self::FOMAT_DB_YMD) . '\' AND CAST(absences.EDate AS DATE) >= \'' . date_format($rqNext2Day, self::FOMAT_DB_YMD) . '\'))')
                ->get()->toArray();
        }

        if (Auth::user()->can('action', RoleScreenDetail::query()->where('alias', 'OvertimeDetailsApprove')->first())) {
            // OT request
            $OTList = OvertimeWork::query()
                ->select('overtime_works.*', 'users.FullName')
                ->leftJoin('users', 'overtime_works.UserID', '=', 'users.id')
                ->where('users.Active', '=', 1)
                ->where('overtime_works.STime', '>=', date(self::FOMAT_DB_YMD))
                ->where('overtime_works.RequestManager', 'LIKE', "%," . Auth::user()->id . ",%")
                ->get()->toArray();

            foreach ($OTList as $oT) {
                if ($oT['Approved'] == 1 || $oT['Approved'] == 2) {
                    $listOT[] = $oT;
                } else {
                    $listOTApproved[] = $oT;
                }
            }
        }
        //If module Event enable then return data
        $QuestionList = ModuleController::eventNotification();

        //        if (Auth::user()->can('action', RoleScreenDetail::query()->where('alias', 'EventList')->first())) {
        //            // list event
        //            $QuestionList = Question::query()
        //                ->select('questions.*', 'users.FullName')
        //                ->selectRaw('(SELECT CASE WHEN COUNT(event_results.AID) > 0 THEN 1 WHEN COUNT(event_results.AID) = 0 THEN 0 END FROM event_results INNER JOIN answers ON event_results.AID = answers.id WHERE answers.QID = questions.id and event_results.UID = ' . Auth::user()->id . ') AS StatusA')
        //                ->leftJoin('users', 'questions.CreateUID', '=', 'users.id')
        //                ->where('users.Active', '=', 1)
        //                ->where('questions.Status', '=', 1)
        //                ->where('questions.SDate', '<=', date(self::FOMAT_DB_YMD))
        //                ->where('questions.EDate', '>=', date(self::FOMAT_DB_YMD))
        //                ->orderBy('questions.id')
        //                ->get()->toArray();
        //        }

        // birthday in month
        $users = User::query()
            ->leftJoin('rooms', 'users.RoomId', '=', 'rooms.id')
            ->whereRaw('MONTH(users.Birthday) = ' . $currentMonth)
            ->where('users.Active', 1)
            ->orderByRaw('DAY(users.Birthday)')
            ->count();

        $arrResult = array(
            'birthday' => array(
                'count' => $users
            ),
            'absence' => array(
                'request' => array(
                    'all' => array(
                        'count' => count($listAbsenceApproved),
                    ),
                    'threeDays' => array(
                        'count' => count($absenceList3Days),
                    ),
                ),
                'approved' => array(
                    'count' => count($listAbsences),
                )
            ),
            'events' => array(
                'count' => count($QuestionList),
            ),
            'overtimes' => array(
                'request' => array(
                    'count' => count($listOTApproved),
                ),
                'overtime' => array(
                    'count' => count($listOT),
                )
            ),
        );
        return $arrResult;
    }

    public function setCookie(Request $request)
    {
        foreach ($request->input() as $key => $value) {
            Cookie::queue($key, $value, 10000000);
        }
        return 1;
    }

    public function showfirebase(Request $request, $orderBy = 'SDate', $sortBy = 'desc')
    {
        $this->data['data'] = DB::table('push_token_list')
            ->where('user_id', Auth::user()->id)
            ->orderBy('updated_at', $sortBy)
            ->paginate(20);
        if (strpos(\Request::getRequestUri(), 'api') !== false) {
            return response()->json(['data' => $this->data]);
        }
        // return $this->viewAdminLayout('work.working-schedule', $this->data);
    }

    public function getNotificationInYear(Request $request)
    {
        $idUID = Auth::user()->id;
        $absenceList3Days = [];
        $listAbsenceApproved = [];
        $listAbsences = [];
        $listOTApproved = array();
        $listOT = array();
        $QuestionList = array();
        $rqDateNow = date_create($request->input('dateNow') ? $request->input('dateNow') : date(self::FOMAT_DB_YMD));
        $year = $rqDateNow->format('Y');

        // absence request
        if (Auth::user()->can('action', RoleScreenDetail::query()->where('alias', 'AbsenceListApprove')->first())) {
            $absenceList = Absence::query()
                ->select('absences.*', 'users.FullName', 'master_data.Name')
                ->leftJoin('users', 'absences.UID', '=', 'users.id')
                ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
                ->where('users.Active', '=', 1)
                ->where('absences.RequestManager', 'LIKE', "%," . Auth::user()->id . ",%")
                ->get()->toArray();
            foreach ($absenceList as $absence) {
                if ($absence['Approved'] == 1 || $absence['Approved'] == 2) {
                    $listAbsences[] = $absence;
                } else {
                    $listAbsenceApproved[] = $absence;
                }
            }
        }

        if (Auth::user()->can('action', RoleScreenDetail::query()->where('alias', 'AbsenceList')->first())) {
            // absence in this year
            $absenceList3Days = Absence::query()
                ->select('absences.*', 'users.FullName', 'master_data.Name')
                ->leftJoin('users', 'absences.UID', '=', 'users.id')
                ->leftJoin('master_data', 'absences.MasterDataValue', '=', 'master_data.DataValue')
                ->where('users.Active', '=', 1)
                ->where('absences.Approved', '!=', 2)
                ->whereYear('absences.SDate', $year)
                ->get()->toArray();
        }
        $arrAbs = [];
        foreach ($absenceList3Days as $abs) {
            if ($idUID === $abs['UID']) {
                array_push($arrAbs, $abs);
            }
        }
        $this->data['absenceList'] = $arrAbs;

        if (Auth::user()->can('action', RoleScreenDetail::query()->where('alias', 'OvertimeDetails')->first())) {
            // overtime in this month
            $overtimeList3Days = OvertimeWork::query()
                ->select('overtime_works.*', 'users.FullName')
                ->leftJoin('users', 'overtime_works.UserID', '=', 'users.id')
                ->where('users.Active', '=', 1)
                ->where('overtime_works.Approved', '!=', 2)
                ->whereYear('overtime_works.STime', $year)
                ->get()->toArray();
        }
        // lam them trong 3 ngay
        $arrOvertime = [];
        foreach ($overtimeList3Days as $abs) {
            if ($idUID === $abs['UserID']) {
                array_push($arrOvertime, $abs);
            }
        }
        $this->data['overtimeList'] = $arrOvertime;
        // working in next 3 days
        $workingshedule = DB::table('working_schedule')
            ->whereYear('working_schedule.Date', $year)
            ->get()->toArray();

        // all project your
        //        $this->data['all_project'] = Project::query()
        //            ->where('Member', 'like', '%' . Auth::user()->id . '%')
        //            ->orWhere('Leader', 'like', '%' . Auth::user()->id . '%')->get();

        //  lich cong tac trong 3 ngay
        $this->data['workingshedule'] = [];
        $arrWor = [];
        $Room = Room::query()->select('id', 'Name')->get();
        foreach ($workingshedule as $wr) {
            foreach ($Room as $rp) {
                if ($wr->roomsID === $rp['id']) {
                    $roomsName = $rp['Name'];
                    $wr->roomsName = $roomsName;
                }
            }
            if (in_array($idUID, explode(',', $wr->AssignID)) || $wr->AssignID == '0') {
                array_push($arrWor, $wr);
            }
        }
        $this->data['workingshedule'] = $arrWor;
        $this->data['count'] = count($arrWor) + count($arrOvertime) + count($arrAbs);
        return response()->json(['data' => $this->data]);
    }
}
