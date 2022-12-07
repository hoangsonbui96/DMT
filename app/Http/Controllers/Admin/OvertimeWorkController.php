<?php

namespace App\Http\Controllers\Admin;

use App\DailyReport;
use App\RoleUserScreenDetailRelationship;
use App\Exports\listOvertimeExport;
use App\OvertimeWork;
use App\Project;
use App\MasterData;
use App\Menu;
use App\RoleScreenDetail;
use App\User;
use Carbon\Carbon;
use App\Exports\OTExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Admin\NotificationController;
use Modules\ProjectManager\Entities\Task;

/**
 * Screen OvertimeWork
 * Class OvertimeWorkController
 * @package App\Http\Controllers\Admin
 */
class OvertimeWorkController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $viewAppr;
    protected $approve;
    protected $export;
    const KEYMENU = array(
        "add" => "OvertimeDetailsAdd",
        "view" => "OvertimeDetails",
        "edit" => "OvertimeDetailsEdit",
        "delete" => "OvertimeDetailsDelete",
        "approve" => "OvertimeListApprove",
        "viewAppr" => "OvertimeDetailsApprove",
        "export" => "OvertimeReportsExport"
    );
    /**
     * get role view, add, edit, delete
     * OvertimeWorkController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
        $array = $this->RoleView('Overtimes', ['OvertimeDetails', 'OvertimeDetailsApprove', 'OvertimeReports']);
        $this->menu = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if ($value == $row->alias)
                    $this->$key = $row;
            }
        }
    }

    /**
     * view overtime-works
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Request $request, $orderBy = 'id', $sortBy = 'desc')
    {
        $this->authorize('view', $this->menu);
        $this->getListOT($request, $orderBy, $sortBy);

        $this->data['add']      = $this->add;
        $this->data['edit']     = $this->edit;
        $this->data['view']     = $this->view;
        $this->data['delete']   = $this->delete;
        $this->data['approve']  = $this->approve;

        return $this->viewAdminLayout('overtime-works', $this->data);
    }

    /**
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @param null $export
     * @return View|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showOverview(Request $request, $orderBy = 'UserID', $sortBy = 'asc')
    {
        $this->authorize('view', $this->menu);
        $recordPerPage = $this->getRecordPage();

        if ($request->has('date')) {
            if (
                \DateTime::createFromFormat('d/m/Y', $request['date'][0]) === FALSE && $request['date'][0] != '' ||
                \DateTime::createFromFormat('d/m/Y', $request['date'][1]) === FALSE && $request['date'][1] != ''
            ) {
                return Redirect::back();
            }
        }
        //Get list project
        $this->data['projects'] = Project::all();

        //Get list overtime of users
        if (Schema::hasColumn('overtime_works', $orderBy)) {
            $list = OvertimeWork::query()->select('overtime_works.*', 'tb1.FullName', 'tb1.username')
                ->leftJoin('projects', 'overtime_works.ProjectID', '=', 'projects.id')
                ->join('users as tb1', 'overtime_works.UserID', '=', 'tb1.id')
                ->groupBy('overtime_works.UserID')
                ->orderBy($orderBy, $sortBy);
        } else {
            return redirect()->back();
        }

        $this->data['request'] = $request->query();
        $this->funcSearchWithRequest($request, $list);
        $count = $list->get()->count();
        $list = $list->paginate($recordPerPage);
        //Tính tổng giờ làm thêm
        foreach ($list as $item) {
            $user = User::find($item->UserID);
            if (!$user) {
                continue;
            }
            $item->EmployerName = User::find($item->UserID)->FullName;

            $works = OvertimeWork::query()
                ->where('UserID', $item->UserID)
                //                ->where('Approved', '!=', 2);
                ->where('Approved', 'like', 1);
            $this->funcSearchWithRequest($request, $works);
            $works = $works->get();

            $item->totalHours = 0;
            $item->time = 0;
            $item->countTimeIsWeeken = 0;
            $item->countHourIsWeeken = 0;
            $item->countTimeNotIsWeeken = 0;
            $item->countHourNotIsWeeken = 0;

            foreach ($works as $work) {
                $item->time++;

                if ($work->STime != null && $work->ETime != null) {
                    $stime = Carbon::parse($work->STime);
                    $etime = Carbon::parse($work->ETime);

                    $OTTimeinDay = $etime->diffInSeconds($stime) / 3600 - $work->BreakTime;

                    $item->totalHours += ($OTTimeinDay > 0) ? $OTTimeinDay : 0;

                    //đếm số lần cuối tuần
                    // if ($stime->isWeekend() && $etime->isWeekend()) {
                    if ($stime->dayOfWeek == Carbon::SUNDAY && $etime->dayOfWeek == Carbon::SUNDAY) {
                        $item->countTimeIsWeeken++;
                        $item->countHourIsWeeken += ($OTTimeinDay > 0) ? $OTTimeinDay : 0;
                    }
                }
            }

            //đếm số lần trong tuần
            $item->countTimeNotIsWeeken = $item->time - $item->countTimeIsWeeken;
            $item->countHourNotIsWeeken = $item->totalHours - $item->countHourIsWeeken;

            $item->totalHours = number_format($item->totalHours, 2) + 0;
            $item->countHourIsWeeken = number_format($item->countHourIsWeeken, 2);
            $item->countHourNotIsWeeken = number_format($item->countHourNotIsWeeken, 2);
        }

        //Phân trang + sort
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'asc' ? 'desc' : 'asc') . "/" . $query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);
        $page = array_key_exists('page', $query_array) ? $query_array['page'] : '';
        $stt = $page ? $count - (($page - 1) * $recordPerPage) : $count;
        if ($sort == 'asc') {
            $stt = $page ? ($page - 1) * $recordPerPage : '';
        }

        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['stt'] = $stt;
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['sort'] = $sort;
        $this->data['list'] = $list;
        $this->data['export'] = RoleScreenDetail::query()
            ->where('alias', 'OvertimeOverviewsExport')
            ->first();

        return $this->viewAdminLayout('overview-overtimes', $this->data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|string|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportOverview(Request $request)
    {
        if ($request->has('date')) {
            if (
                \DateTime::createFromFormat('d/m/Y', $request['date'][0]) === FALSE && $request['date'][0] != '' ||
                \DateTime::createFromFormat('d/m/Y', $request['date'][1]) === FALSE && $request['date'][1] != ''
            ) {
                return Redirect::back();
            }
        }
        //Get list project
        $this->data['projects'] = Project::all();

        //Get list overtime of users
        if (Schema::hasColumn('overtime_works', 'UserID')) {
            $list = OvertimeWork::query()->select('overtime_works.*', 'tb1.FullName', 'tb1.username')
                ->leftJoin('projects', 'overtime_works.ProjectID', '=', 'projects.id')
                ->join('users as tb1', 'overtime_works.UserID', '=', 'tb1.id')
                ->groupBy('overtime_works.UserID')
                ->orderBy('UserID', 'asc');
        } else {
            return redirect()->back();
        }

        $this->data['request'] = $request->query();
        $this->funcSearchWithRequest($request, $list);
        $list = $list->get();
        //Tính tổng giờ làm thêm
        foreach ($list as $item) {
            $item->EmployerName = User::find($item->UserID)->FullName;

            $works = OvertimeWork::query()
                ->where('UserID', $item->UserID)
                //                ->where('Approved', '!=', 2);
                ->where('Approved', 'like', 1);
            $this->funcSearchWithRequest($request, $works);
            $works = $works->get();

            $item->totalHours = 0;
            $item->time = 0;
            $item->countTimeIsWeeken = 0;
            $item->countHourIsWeeken = 0;
            $item->countTimeNotIsWeeken = 0;
            $item->countHourNotIsWeeken = 0;
            foreach ($works as $work) {
                $item->time++;

                if ($work->STime != null && $work->ETime != null) {
                    $stime = Carbon::parse($work->STime);
                    $etime = Carbon::parse($work->ETime);

                    $OTTimeinDay = $etime->diffInSeconds($stime) / 3600 - $work->BreakTime;

                    $item->totalHours += ($OTTimeinDay > 0) ? $OTTimeinDay : 0;

                    //đếm số lần cuối tuần
                    // if ($stime->isWeekend() && $etime->isWeekend()) {
                    if ($stime->dayOfWeek == Carbon::SUNDAY && $etime->dayOfWeek == Carbon::SUNDAY) {
                        $item->countTimeIsWeeken++;
                        $item->countHourIsWeeken += ($OTTimeinDay > 0) ? $OTTimeinDay : 0;
                    }
                }
            }

            //đếm số lần trong tuần
            $item->countTimeNotIsWeeken = $item->time - $item->countTimeIsWeeken;
            $item->countHourNotIsWeeken = $item->totalHours - $item->countHourIsWeeken;

            $item->totalHours = number_format($item->totalHours, 2);
            $item->countHourIsWeeken = number_format($item->countHourIsWeeken, 2);
            $item->countHourNotIsWeeken = number_format($item->countHourNotIsWeeken, 2);
        }

        if ($list->count() > 0) {
            return Excel::download(new listOvertimeExport($list, $request), 'DanhSachTongQuan.xlsx');
        }
        return $this->jsonErrors('Không có dữ liệu!');
    }

    /**
     * view screen báo cáo tổng hợp (overtime-reports)
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return View|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function  showReport(Request $request,  $orderBy = 'id', $sortBy = 'desc')
    {
        $this->authorize('view', $this->menu);
        $this->getListReport($request);

        return $this->viewAdminLayout('overtime-report', $this->data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Exception
     */
    public function export(Request $request)
    {
        $record = $this->getListReport($request);
        if ($record['userList']->count() > 0) {
            return Excel::download(new OTExport($record), 'Bao_cao tong_hop_gio_lam_them.xlsx');
        }
        return Redirect::back()->withErrors(['Không có dữ liệu!']);
    }

    /**
     * @param Request $request
     * @param null $id
     * @param null $del
     * @return View|int|string
     */
    public function showDetail(Request $request, $id = null, $del = null)
    {
        $now = Carbon::now()->format(self::FOMAT_DB_YMD);
        $now_string = Carbon::parse($now)->toDateString();

        $this->data['request_manager'] = RoleUserScreenDetailRelationship::query()
            ->select('user_id', 'FullName')
            ->join('users', 'users.id', '=', 'role_user_screen_detail_relationships.user_id')
            ->where('screen_detail_alias', '=', 'OvertimeListApprove')
            ->where('role_user_screen_detail_relationships.permission', '=', 1)
            ->get();

        //Danh sách nhân viên
        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['userLogged'] = Auth::user();
        $this->data['ProjectUser'] = Project::find(Auth::user()->ProjectID);

        //Danh sách dự án còn thời hạn và theo nhân viên
        $this->data['projects'] = Project::query()
            ->where('Active', 1)
            ->where(function ($query) use ($now_string) {
                $query->where('EndDate', '>=', $now_string)
                    ->orWhereNull('EndDate');
            })
            ->where(function ($query) {
                $query->where('Member', 'like', '%' . Auth::user()->id . '%')
                    ->orWhere('Leader', 'like', '%' . Auth::user()->id . '%');
            })->get();

        if ($id != null) {
            $one = OvertimeWork::find($id);
            if ($one) {
                if ($del == 'del') {
                    $one->delete();
                    return 1;
                }
                $this->data['OvertimeInfo'] = $one;
                if (!is_null($this->data['OvertimeInfo']->RequestManager)) {
                    $this->data['OvertimeInfo']->RequestManager = explode(',', $this->data['OvertimeInfo']->RequestManager);
                } else {
                    $this->data['OvertimeInfo']->RequestManager = [];
                }
                return $this->viewAdminIncludes('overtime-work-detail', $this->data);
            } else {
                return '';
            }
        } else {
            return $this->viewAdminIncludes('overtime-work-detail', $this->data);
        }
    }

    /**
     * add+edit overtime
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse|string|void
     */
    public function store(Request $request, $id = null)
    {
        $today = Carbon::now()->format('Y-m-d');
        if (count($request->input()) === 0) {
            return abort('404');
        }
        try {
            $arrCheck = [
                'UserID'         => 'required|integer|min:1',
                'STime'          => 'required|date_format:d/m/Y H:i',
                'ETime'          => 'required|date_format:d/m/Y H:i',
                'BreakTime'      => 'numeric|min:0',
                'ProjectID'      => 'required|integer|min:1',
                'TaskID'         => 'nullable|integer|min:1',  
                'Content'        => 'required|string',
                'UpdatedBy'      => 'integer|nullable',
                'RequestManager' => 'required|array',
            ];
            $modeIsUpdate = array_key_exists('id', $request->input());

            if ($modeIsUpdate) {
                $arrCheck['id'] = 'integer|min:1';
            }

            $validator = Validator::make($request->all(), $arrCheck);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()->first()]);
            }

            $validated = $validator->validate();

            // Check valid vacation time
            $one = !$modeIsUpdate ? new OvertimeWork() : OvertimeWork::find($validated['id']);
            $stime = Carbon::createFromFormat('d/m/Y H:i', $validated['STime']);
            $etime = Carbon::createFromFormat('d/m/Y H:i', $validated['ETime']);

            $diffHours = $stime->diffInSeconds($etime) / 3600 - $validated['BreakTime'];
            if ($diffHours < 0.5) {
                return response()->json(['errors' => ['Thời gian làm thêm tối thiểu là 30 phút']]);
            }

            // check time input
            if ($stime->format(self::FOMAT_DB_YMD) != $etime->format(self::FOMAT_DB_YMD)) {
                return response()->json(['errors' => ['Chỉ thêm giờ làm trong một ngày.']]);
            }

            $diffDay = Carbon::now()->diffInDays($etime, false);
            if ($diffDay > 0) {
                return response()->json(['errors' => ['Bạn không thể thêm giờ làm thêm ở tương lai']]);
            }

            if ($stime > $etime) {
                return response()->json(['errors' => ['Thời gian kết thúc không hợp lệ']]);
            }

            //Check dang ky giờ làm thêm trong vong 3 ngay ko tinh cuoi tuan
            $nTimeDiff = Carbon::createFromFormat('d/m/Y H:i', Carbon::now()->format('d/m/Y 23:59'));

            $day_diff = $etime->diffInDaysFiltered(function (Carbon $date) {
                return $date->isWeekday();
            }, $nTimeDiff);

            if ($day_diff > 3) {
                return $this->jsonErrors('Chỉ được tạo lịch làm thêm trong vòng 3 ngày làm việc.');
            }

            // kiem tra dieu kien ton tai giờ làm thêm cua user
            // $stimeFomat = $this->fncDateTimeConvertFomat($validated['STime'], 'd/m/Y H:i', self::FOMAT_DB_YMD_HI);
            // $etimeFomat = $this->fncDateTimeConvertFomat($validated['ETime'], 'd/m/Y H:i', self::FOMAT_DB_YMD_HI);

            $check = DB::table('overtime_works')
                ->where('UserID', $validated['UserID'])
                ->where(function ($query) use ($stime, $etime) {
                    $query->orWhere(function ($query) use ($stime, $etime) {
                        $query->orWhereBetween('STime', array($stime, $etime));
                        $query->orWhereBetween('ETime', array($stime, $etime));
                    });
                    $query->orWhere(function ($query) use ($stime, $etime) {
                        $query->where('STime', '<=', $stime);
                        $query->where('ETime', '>=', $etime);
                    });
                })
                ->whereNull('deleted_at');
            if (array_key_exists('id', $request->input())) {
                $check = $check->where('id', '!=', $validated['id']);
            }
            $check = $check->first();

            //Check trùng giờ làm thêm
            if ($check) {
                return $this->jsonErrors('Giờ làm thêm đã tồn tại, vui lòng chọn giờ khác!');
            }

            $validated['STimeLogOT'] = $validated['STime'];
            $validated['ETimeLogOT'] = $validated['ETime'];
            $validated['acceptedTimeOT'] = null;
            if ($etime->dayOfWeek != 6 && $etime->dayOfWeek != 0) {
                $validated['STime'] = null;
                $validated['ETime'] = null;

                $checkInTime = DB::table('timekeepings_new')
                    ->where('UserID', $validated['UserID'])
                    ->where('Date', Carbon::parse($etime)->format('Y-m-d'))
                    ->first();

                if ($checkInTime) {
                    $Master1 = MasterData::where('DataValue', 'WT001')->first();
                    $Master2 = MasterData::where('DataValue', 'WT002')->first();
                    $sTimeDay = ($checkInTime->STimeOfDay != null) ? $checkInTime->STimeOfDay : $Master1->Name;
                    $sBreak = ($checkInTime->SBreakOfDay != null) ? $checkInTime->SBreakOfDay : $Master2->Name;
                    $eBreak = ($checkInTime->EBreakOfDay != null) ? $checkInTime->EBreakOfDay : $Master2->DataDescription;
                    $eTimeDay = ($checkInTime->ETimeOfDay != null) ? $checkInTime->ETimeOfDay : $Master1->DataDescription;

                    $timeIn = $checkInTime->TimeIn;
                    $timeOut = $checkInTime->TimeOut;

                    if ($timeIn != null) {
                        $timeIn = ($timeIn > $sTimeDay) ? $timeIn : $sTimeDay;

                        $totalTimeWork = ($timeIn > $sBreak) ? Carbon::parse($sTimeDay)->diffInMinutes(Carbon::parse($sBreak)) +  Carbon::parse($eBreak)->diffInMinutes(Carbon::parse($eTimeDay))
                            : Carbon::parse($sTimeDay)->diffInMinutes(Carbon::parse($eTimeDay));
                        $accepted_OT_Time = Carbon::createFromFormat('d/m/Y H:i:s', Carbon::parse($etime)->format('d/m/Y') . ' ' . $timeIn)->addMinutes($totalTimeWork + self::ACTIVE_OT_AFTER_MINUTE);

                        $validated['acceptedTimeOT'] = Carbon::parse($accepted_OT_Time);

                        $validated['STime'] = ($stime->diffInMinutes($accepted_OT_Time, false) < 0) ? $validated['STimeLogOT'] : $accepted_OT_Time->format('d/m/Y H:i');
                        $validated['ETime'] = ($etime->diffInMinutes($accepted_OT_Time, false) < 0) ? $validated['ETimeLogOT'] : $accepted_OT_Time->format('d/m/Y H:i');
                   }
                } else {
                    return $this->jsonErrors('Không có dữ liệu chấm công đầu vào, chưa thể tính được dữ liệu làm thêm, vui lòng liên hệ Văn Phòng trong vòng 3 ngày làm việc từ thời điểm OT.');
                }
            }

            foreach ($validated as $key => $value) {
                if (Schema::hasColumn('overtime_works', $key)) {
                    if ($key == 'STime' || $key == 'ETime' || $key == 'STimeLogOT' || $key == 'ETimeLogOT') {
                        $value = ($value != null) ? $this->fncDateTimeConvertFomat($value, 'd/m/Y H:i', self::FOMAT_DB_YMD_HI) : null;
                    }

                    $one->$key = $value;
                }
            }

            $one->RequestManager = ',' . implode(',', $validated['RequestManager']) . ',';
            $one->TaskID = $request->TaskID;
            $one->save();
            if (isset($one->id)) {
                //firebase notification
                $arrTokenAd = collect(DB::table('push_token')->whereIn('UserID',  $request['RequestManager'])->where('allow_push', 1)->whereNull('deleted_at')->pluck('token_push')->toArray());
                $arrToken = ($arrTokenAd->merge(DB::table('push_token')->where('UserID', $one->UserID)->whereNull('deleted_at')->pluck('token_push')->toArray()))->all();
                $arrToken = array_unique($arrToken);
                //  $arrToken = DB::table('push_token')->whereIn('UserID',  $request['RequestManager'])->whereNull('deleted_at')->pluck('token_push')->toArray();

                if (count($arrToken) > 0) {
                    $sendData = [];
                    $sendData['id'] = $one->id;
                    $sendData['data'] = "LT";
                    $headrmess = Auth::user()->FullName . " đăng ký giờ làm thêm.";
                    $bodyNoti = "Từ " . $request['STime'] . ' đến ' . $request['ETime'];
                    NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
                }
            }

            $header = 'Kính gửi Ban giám đốc';
            $this->sendMail($validated, $header);
            return $this->jsonSuccessWithRouter('admin.OverviewOvertimes');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * view screen duyet don xin lam them (overtime-list-approve)
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showListApprove(Request $request, $orderBy = 'id', $sortBy = 'desc')
    {
        $this->authorize('view', $this->menu);

        $this->getListUnapprOT($request, $orderBy, $sortBy);
        $this->data['approve'] = $this->approve;

        return $this->viewAdminLayout('overtime-list-approve', $this->data);
    }

    /**
     * Duyệt/từ chối giờ làm thêm
     * @param Request $request
     * @param null $id
     * @param null $del
     * @return View|int|string
     */
    public function AprOvertime(Request $request, $id = null, $del = null)
    {

        $this->data['request'] = $request->query();
        if ($id != null) {
            $one = OvertimeWork::find($id);
            if ($del == 'del') {
                if ($one) {
                    $one->Approved = 2;
                    $one->UpdatedBy = Auth::user()->id;
                    $one->ApprovedDate = Carbon::now();
                    $one->Note = $this->data['request']['Note'];
                    if ($this->data['request']['Note'] == '') {
                        return $this->jsonErrors('Vui lòng điền lý do');
                    }
                    $one->save();
                    if ($one->save()) {
                        //firebase notification
                        $arrTokenAd = collect(DB::table('push_token')->whereIn('UserID',  explode(',', $one['RequestManager']))->where('allow_push', 1)->whereNull('deleted_at')->pluck('token_push')->toArray());
                        $arrToken = ($arrTokenAd->merge(DB::table('push_token')->where('UserID', $one->UserID)->whereNull('deleted_at')->pluck('token_push')->toArray()))->all();
                        $arrToken = array_unique($arrToken);

                        $eid = DB::table('overtime_works')->where('id',  $id)->whereNull('deleted_at')->pluck('UserID')->first();
                        // $arrToken = DB::table('push_token')->where('UserID',  $eid)->whereNull('deleted_at')->pluck('token_push')->toArray();
                        $stime = DB::table('overtime_works')->where('UserID',  $eid)->whereNull('deleted_at')->pluck('Stime')->first();
                        $etime = DB::table('overtime_works')->where('UserID',  $eid)->whereNull('deleted_at')->pluck('Etime')->first();
                        if (count($arrToken) > 0) {
                            $sendData = [];
                            $sendData['id'] = $id;
                            $sendData['data'] = "LT";
                            $headrmess = Auth::user()->FullName . " đã từ chối giờ làm thêm.";
                            $bodyNoti = "Lý do: " . $this->data['request']['Note'];

                            NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
                        }
                    }
                    $header = '';
                    $this->sendMail($one, $header, $this->data['request']['Note']);
                    return $this->jsonSuccess('Đã hủy.');
                }
                return 1;
            }
            if ($one) {
                $one->Approved = 1;
                $one->UpdatedBy = Auth::user()->id;
                $one->ApprovedDate = Carbon::now();
                $one->save();
                if ($one->save()) {
                    // Create OT Task
                    $task = Task::where('ProjectId',$one->ProjectID)
                    ->find($one->TaskID);
                    if($task){
                        $newTask = $task->replicate();
                        $newTask->ParentId = $task->id;
                        $newTask->StartDate = $one->STime;
                        $newTask->EndDate = $one->ETime;
                        $newTask->Duration  = Carbon::parse($one->STime)->diffInSeconds(Carbon::parse($one->ETime)) /3600 - $one->BreakTime;
                        $newTask->SubType = "OT";
                        $newTask->Status = 1;
                        $newTask->Name = "OT " . $task->Name;
                        $newTask->save();
                    }

                    //firebase notification
                    $arrTokenAd = collect(DB::table('push_token')->whereIn('UserID',  explode(',', $one['RequestManager']))->where('allow_push', 1)->whereNull('deleted_at')->pluck('token_push')->toArray());
                    $arrToken = ($arrTokenAd->merge(DB::table('push_token')->where('UserID', $one->UserID)->whereNull('deleted_at')->pluck('token_push')->toArray()))->all();
                    $arrToken = array_unique($arrToken);

                    $eid = DB::table('overtime_works')->where('id',  $id)->whereNull('deleted_at')->pluck('UserID')->first();
                    // $arrToken = DB::table('push_token')->where('UserID',  $eid)->whereNull('deleted_at')->pluck('token_push')->toArray();
                    $stime = DB::table('overtime_works')->where('UserID',  $eid)->whereNull('deleted_at')->pluck('Stime')->first();
                    $etime = DB::table('overtime_works')->where('UserID',  $eid)->whereNull('deleted_at')->pluck('Etime')->first();
                    if (count($arrToken) > 0) {
                        $sendData = [];
                        $sendData['id'] = $id;
                        $sendData['data'] = "LT";
                        $headrmess = Auth::user()->FullName . " đã duyệt giờ làm thêm.";
                        $bodyNoti = "Từ " . Carbon::parse($stime)->format('d/m/Y H:i:s') . ' đến ' . Carbon::parse($etime)->format('d/m/Y H:i:s');

                        NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
                    }
                }
                $header = 'Gửi anh/chị/em trong công ty';
                $this->sendMail($one, $header);

                return $this->jsonSuccess('Duyệt thành công');
            } else {
                return $this->jsonErrors('Duyệt thất bại');
            }
        } else {
            return $this->viewAdminIncludes('.overtime-work-detail', $this->data);
        }
    }

    public function showDetailUnapprove()
    {
        return $this->viewAdminIncludes('unapprove-overtime', $this->data);
    }


    //API

    /**
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showApi(Request $request, $orderBy = 'id', $sortBy = 'desc')
    {
        $this->authorize('action', $this->view);

        $this->getListOT($request, $orderBy, $sortBy);

        $data = $this->data;
        $data['role_key'] = 'OvertimeDetails';

        return AdminController::responseApi(200, null, null, $data);
    }

    /**
     * @param Request $request
     * @param null $id
     * @param null $del
     * @return \Illuminate\Http\JsonResponse
     */
    public function showDetailApi(Request $request, $id = null)
    {
        $data = array();
        $now = Carbon::now()->format(self::FOMAT_DB_YMD);
        $now_string = Carbon::parse($now)->toDateString();

        $data['request_manager'] = RoleUserScreenDetailRelationship::query()
            ->select('user_id', 'FullName')
            ->join('users', 'users.id', '=', 'role_user_screen_detail_relationships.user_id')
            ->where('screen_detail_alias', '=', 'OvertimeListApprove')
            ->where('role_user_screen_detail_relationships.permission', '=', 1)
            ->get();

        $data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $data['userLogged'] = Auth::user();
        $data['ProjectUser'] = Project::find(Auth::user()->ProjectID);

        $user_id = Auth::user()->id;
        if ($request['userID']) {
            $user_id = $request['userID'];
        }

        $data['projects'] = Project::query()
            ->where('Active', 1)
            ->where(function ($query) use ($now_string) {
                $query->where('EndDate', '>=', $now_string)
                    ->orWhereNull('EndDate');
            })
            ->where(function ($query) use ($user_id) {
                $query->where('Member', 'like', '%' . $user_id . '%')
                    ->orWhere('Leader', 'like', '%' . $user_id . '%');
            })->get();

        if ($id != null) {
            $data['OvertimeInfo'] = OvertimeWork::find($id);
            if ($data['OvertimeInfo'] != null) {
                if (!is_null($data['OvertimeInfo']->RequestManager)) {
                    $data['OvertimeInfo']->RequestManager = explode(',', $data['OvertimeInfo']->RequestManager);
                } else {
                    $data['OvertimeInfo']->RequestManager = [];
                }
            }
        }
        return AdminController::responseApi(200, null, null, $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function storeApi(Request $request)
    {
        $this->authorize('action', $this->add);
        if (count($request->input()) === 0) {
            return AdminController::responseApi(422, __('admin.error.data'));
        }
        try {
            $arrCheck = [
                'UserID'         => 'required|integer|min:1',
                'STime'          => 'required|date_format:d/m/Y H:i',
                'ETime'          => 'required|date_format:d/m/Y H:i',
                'BreakTime'      => 'numeric|min:0',
                'ProjectID'      => 'required|integer|min:1',
                'Content'        => 'required|string',
                'UpdatedBy'      => 'integer|nullable',
                'RequestManager' => 'required|array',
            ];

            $validator = Validator::make($request->all(), $arrCheck);

            if ($validator->fails()) {
                return AdminController::responseApi(422, $validator->errors()->first());
            }

            $validated = $validator->validate();

            $one = new OvertimeWork();
            $stime = Carbon::createFromFormat('d/m/Y H:i', $validated['STime']);
            $etime = Carbon::createFromFormat('d/m/Y H:i', $validated['ETime']);
            $diffHours = $stime->diffInSeconds($etime) / 3600 - $validated['BreakTime'];

            // check time input
            if ($stime->format(self::FOMAT_DB_YMD) != $etime->format(self::FOMAT_DB_YMD)) {
                return AdminController::responseApi(422, __('admin.error.ot-work.day-time'));
            }
            if ($stime > $etime) {
                return AdminController::responseApi(422, __('admin.error.ot-work.e-time'));
            }
            if ($diffHours < 0.5) {
                return AdminController::responseApi(422, __('admin.error.ot-work.time'));
            }
            $diffDay = Carbon::now()->diffInDays($etime, false);
            if ($diffDay > 0) {
                return AdminController::responseApi(422, __('Bạn không thể thêm giờ làm thêm ở tương lai'));
            }
            //Check dang ky giờ làm thêm trong vong 3 ngay ko tinh cuoi tuan
            $nTimeDiff = Carbon::createFromFormat('d/m/Y H:i', Carbon::now()->format('d/m/Y 23:59'));
            $day_diff = $etime->diffInDaysFiltered(function (Carbon $date) {
                return $date->isWeekday();
            }, $nTimeDiff);

            if ($day_diff > 3) {
                return AdminController::responseApi(422, __('Chỉ được tạo lịch làm thêm trong vòng 3 ngày làm việc.'));
            }
            // kiem tra dieu kien ton tai giờ làm thêm cua user
            // $stimeFomat = $this->fncDateTimeConvertFomat($validated['STime'], 'd/m/Y H:i', self::FOMAT_DB_YMD_HI);
            // $etimeFomat = $this->fncDateTimeConvertFomat($validated['ETime'], 'd/m/Y H:i', self::FOMAT_DB_YMD_HI);

            $check = DB::table('overtime_works')
                ->where('UserID', $validated['UserID'])
                ->where(function ($query) use ($stime, $etime) {
                    $query->orWhere(function ($query) use ($stime, $etime) {
                        $query->orWhereBetween('STime', array($stime, $etime));
                        $query->orWhereBetween('ETime', array($stime, $etime));
                    });
                    $query->orWhere(function ($query) use ($stime, $etime) {
                        $query->where('STime', '<=', $stime);
                        $query->where('ETime', '>=', $etime);
                    });
                })
                ->whereNull('deleted_at');
            if (array_key_exists('id', $request->input())) {
                $check = $check->where('id', '!=', $validated['id']);
            }
            $check = $check->first();

            //Check trùng giờ làm thêm
            if ($check) {
                return AdminController::responseApi(422, __('Giờ làm thêm đã tồn tại, vui lòng chọn giờ khác!'));
            }

            $validated['STimeLogOT'] = $validated['STime'];
            $validated['ETimeLogOT'] = $validated['ETime'];
            $validated['acceptedTimeOT'] = null;
            if ($etime->dayOfWeek != 6 && $etime->dayOfWeek != 0) {
                $validated['STime'] = null;
                $validated['ETime'] = null;

                $checkInTime = DB::table('timekeepings_new')
                    ->where('UserID', $validated['UserID'])
                    ->where('Date', Carbon::parse($etime)->format('Y-m-d'))
                    ->first();

                if ($checkInTime) {
                    $Master1 = MasterData::where('DataValue', 'WT001')->first();
                    $Master2 = MasterData::where('DataValue', 'WT002')->first();

                    $sTimeDay = ($checkInTime->STimeOfDay != null) ? $checkInTime->STimeOfDay : $Master1->Name;
                    $sBreak = ($checkInTime->SBreakOfDay != null) ? $checkInTime->SBreakOfDay : $Master2->Name;
                    $eBreak = ($checkInTime->EBreakOfDay != null) ? $checkInTime->EBreakOfDay : $Master2->DataDescription;
                    $eTimeDay = ($checkInTime->ETimeOfDay != null) ? $checkInTime->ETimeOfDay : $Master1->DataDescription;

                    $timeIn = $checkInTime->TimeIn;
                    $timeOut = $checkInTime->TimeOut;

                    if ($timeIn != null) {
                        $timeIn = ($timeIn > $sTimeDay) ? $timeIn : $sTimeDay;

                        $totalTimeWork = ($timeIn > $sBreak) ? Carbon::parse($sTimeDay)->diffInMinutes(Carbon::parse($sBreak)) +  Carbon::parse($eBreak)->diffInMinutes(Carbon::parse($eTimeDay))
                            : Carbon::parse($sTimeDay)->diffInMinutes(Carbon::parse($eTimeDay));

                        $accepted_OT_Time = Carbon::createFromFormat('d/m/Y H:i:s', Carbon::parse($etime)->format('d/m/Y') . ' ' . $timeIn)->addMinutes($totalTimeWork + self::ACTIVE_OT_AFTER_MINUTE);

                        $validated['acceptedTimeOT'] = Carbon::parse($accepted_OT_Time);

                        $validated['STime'] = ($stime->diffInMinutes($accepted_OT_Time, false) < 0) ? $validated['STimeLogOT'] : $accepted_OT_Time->format('d/m/Y H:i');
                        $validated['ETime'] = ($etime->diffInMinutes($accepted_OT_Time, false) < 0) ? $validated['ETimeLogOT'] : $accepted_OT_Time->format('d/m/Y H:i');

                        //                            if ($validated['STime'] <= $accepted_OT_Time->format('d/m/Y H:i') && $validated['ETime'] <= $accepted_OT_Time->format('d/m/Y H:i')) {
                        //                                $validated['STime'] = null;
                        //                                $validated['ETime'] = null;
                        //                            }
                    }
                } else {
                    return AdminController::responseApi(422, __('Không có dữ liệu chấm công đầu vào, chưa thể tính được dữ liệu làm thêm, vui lòng liên hệ Văn Phòng trong vòng 3 ngày làm việc từ thời điểm OT.'));
                }
            }
            foreach ($validated as $key => $value) {
                if (Schema::hasColumn('overtime_works', $key)) {
                    if ($key == 'STime' || $key == 'ETime' || $key == 'STimeLogOT' || $key == 'ETimeLogOT') {
                        $value = ($value != null) ? $this->fncDateTimeConvertFomat($value, 'd/m/Y H:i', self::FOMAT_DB_YMD_HI) : null;
                    }
                    $one->$key = $value;
                }
            }

            $one->RequestManager = ',' . implode(',', $validated['RequestManager']) . ',';
            $one->save();
            if (isset($one->id)) {
                //firebase notification
                // $arrToken = DB::table('push_token')->whereIn('UserID',  $request['RequestManager'])->whereNull('deleted_at')->pluck('token_push')->toArray();
                $arrTokenAd = collect(DB::table('push_token')->whereIn('UserID',  $request['RequestManager'])->where('allow_push', 1)->whereNull('deleted_at')->pluck('token_push')->toArray());
                $arrToken = ($arrTokenAd->merge(DB::table('push_token')->where('UserID', $one->UserID)->whereNull('deleted_at')->pluck('token_push')->toArray()))->all();
                $arrToken = array_unique($arrToken);
                if (count($arrToken) > 0) {
                    $sendData = [];
                    $sendData['id'] = $one->id;
                    $sendData['data'] = "LT";
                    $headrmess = Auth::user()->FullName . " đăng ký giờ làm thêm.";
                    $bodyNoti = "Từ " . $request['STime'] . ' đến ' . $request['ETime'];

                    NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
                }
            }

            $this->sendMail($validated, 'Kính gửi Ban giám đốc');
            return AdminController::responseApi(200, null, __('admin.success.save'));
        } catch (\Exception $e) {
            return AdminController::responseApi(422, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateApi(Request $request, $id = null)
    {
        $this->authorize('action', $this->edit);
        if (count($request->input()) === 0) {
            return AdminController::responseApi(422, __('admin.error.data'));
        }

        try {
            $arrCheck = [
                'UserID'         => 'required|integer|min:1',
                'STime'          => 'required|date_format:d/m/Y H:i',
                'ETime'          => 'required|date_format:d/m/Y H:i',
                'BreakTime'      => 'numeric|min:0',
                'ProjectID'      => 'required|integer|min:1',
                'Content'        => 'required|string',
                'UpdatedBy'      => 'integer|nullable',
                'RequestManager' => 'required|array',
            ];

            $validator = Validator::make($request->all(), $arrCheck);

            if ($validator->fails()) {
                return AdminController::responseApi(422, $validator->errors()->first());
            }
            $validated = $validator->validate();

            // Check valid vacation time
            $one = OvertimeWork::find($id);

            $stime = Carbon::createFromFormat('d/m/Y H:i', $validated['STime']);
            $etime = Carbon::createFromFormat('d/m/Y H:i', $validated['ETime']);
            $diffHours = $stime->diffInSeconds($etime) / 3600 - $validated['BreakTime'];

            // check time input
            if ($stime->format(self::FOMAT_DB_YMD) != $etime->format(self::FOMAT_DB_YMD)) {
                return AdminController::responseApi(422, __('admin.error.ot-work.day-time'));
            }
            if ($stime > $etime) {
                return AdminController::responseApi(422, __('admin.error.ot-work.e-time'));
            }
            if ($diffHours < 0.5) {
                return AdminController::responseApi(422, __('admin.error.ot-work.time'));
            }

            $diffDay = Carbon::now()->diffInDays($etime, false);
            if ($diffDay > 0) {
                return AdminController::responseApi(422, __('Bạn không thể thêm giờ làm thêm ở tương lai'));
            }
            //Check dang ky giờ làm thêm trong vong 3 ngay ko tinh cuoi tuan
            $nTimeDiff = Carbon::createFromFormat('d/m/Y H:i', Carbon::now()->format('d/m/Y 23:59'));
            $day_diff = $etime->diffInDaysFiltered(function (Carbon $date) {
                return $date->isWeekday();
            }, $nTimeDiff);

            if ($day_diff > 3) {
                return AdminController::responseApi(422, __('Chỉ được tạo lịch làm thêm trong vòng 3 ngày làm việc.'));
            }
            // kiem tra dieu kien ton tai giờ làm thêm cua user
            // $stimeFomat = $this->fncDateTimeConvertFomat($validated['STime'], 'd/m/Y H:i', self::FOMAT_DB_YMD_HI);
            // $etimeFomat = $this->fncDateTimeConvertFomat($validated['ETime'], 'd/m/Y H:i', self::FOMAT_DB_YMD_HI);

            $check = DB::table('overtime_works')
                ->where('UserID', $validated['UserID'])
                ->where(function ($query) use ($stime, $etime) {
                    $query->orWhere(function ($query) use ($stime, $etime) {
                        $query->orWhereBetween('STime', array($stime, $etime));
                        $query->orWhereBetween('ETime', array($stime, $etime));
                    });
                    $query->orWhere(function ($query) use ($stime, $etime) {
                        $query->where('STime', '<=', $stime);
                        $query->where('ETime', '>=', $etime);
                    });
                })
                ->whereNull('deleted_at');
            if (array_key_exists('id', $request->input())) {
                $check = $check->where('id', '!=', $id);
            }
            $check = $check->first();

            //Check trùng giờ làm thêm
            if ($check) {
                return AdminController::responseApi(422, __('Giờ làm thêm đã tồn tại, vui lòng chọn giờ khác!'));
            }

            $validated['STimeLogOT'] = $validated['STime'];
            $validated['ETimeLogOT'] = $validated['ETime'];
            $validated['acceptedTimeOT'] = null;
            if ($etime->dayOfWeek != 6 && $etime->dayOfWeek != 0) {
                $validated['STime'] = null;
                $validated['ETime'] = null;

                $checkInTime = DB::table('timekeepings_new')
                    ->where('UserID', $validated['UserID'])
                    ->where('Date', Carbon::parse($etime)->format('Y-m-d'))
                    ->first();

                if ($checkInTime) {
                    $Master1 = MasterData::where('DataValue', 'WT001')->first();
                    $Master2 = MasterData::where('DataValue', 'WT002')->first();

                    $sTimeDay = ($checkInTime->STimeOfDay != null) ? $checkInTime->STimeOfDay : $Master1->Name;
                    $sBreak = ($checkInTime->SBreakOfDay != null) ? $checkInTime->SBreakOfDay : $Master2->Name;
                    $eBreak = ($checkInTime->EBreakOfDay != null) ? $checkInTime->EBreakOfDay : $Master2->DataDescription;
                    $eTimeDay = ($checkInTime->ETimeOfDay != null) ? $checkInTime->ETimeOfDay : $Master1->DataDescription;

                    $timeIn = $checkInTime->TimeIn;
                    $timeOut = $checkInTime->TimeOut;

                    if ($timeIn != null) {
                        $timeIn = ($timeIn > $sTimeDay) ? $timeIn : $sTimeDay;

                        $totalTimeWork = ($timeIn > $sBreak) ? Carbon::parse($sTimeDay)->diffInMinutes(Carbon::parse($sBreak)) +  Carbon::parse($eBreak)->diffInMinutes(Carbon::parse($eTimeDay))
                            : Carbon::parse($sTimeDay)->diffInMinutes(Carbon::parse($eTimeDay));

                        $accepted_OT_Time = Carbon::createFromFormat('d/m/Y H:i:s', Carbon::parse($etime)->format('d/m/Y') . ' ' . $timeIn)->addMinutes($totalTimeWork + self::ACTIVE_OT_AFTER_MINUTE);

                        $validated['acceptedTimeOT'] = Carbon::parse($accepted_OT_Time);

                        $validated['STime'] = ($stime->diffInMinutes($accepted_OT_Time, false) < 0) ? $validated['STimeLogOT'] : $accepted_OT_Time->format('d/m/Y H:i');
                        $validated['ETime'] = ($etime->diffInMinutes($accepted_OT_Time, false) < 0) ? $validated['ETimeLogOT'] : $accepted_OT_Time->format('d/m/Y H:i');

                        //                            if ($validated['STime'] <= $accepted_OT_Time->format('d/m/Y H:i') && $validated['ETime'] <= $accepted_OT_Time->format('d/m/Y H:i')) {
                        //                                $validated['STime'] = null;
                        //                                $validated['ETime'] = null;
                        //                            }
                    }
                } else {
                    return AdminController::responseApi(422, __('Không có dữ liệu chấm công đầu vào, chưa thể tính được dữ liệu làm thêm, vui lòng liên hệ Văn Phòng trong vòng 3 ngày làm việc từ thời điểm OT.'));
                }
            }
            foreach ($validated as $key => $value) {
                if (Schema::hasColumn('overtime_works', $key)) {
                    if ($key == 'STime' || $key == 'ETime' || $key == 'STimeLogOT' || $key == 'ETimeLogOT') {
                        $value = ($value != null) ? $this->fncDateTimeConvertFomat($value, 'd/m/Y H:i', self::FOMAT_DB_YMD_HI) : null;
                    }
                    $one->$key = $value;
                }
            }
            $one->RequestManager = ',' . implode(',', $validated['RequestManager']) . ',';
            $one->save();
            if (isset($one->id)) {
                //firebase notification
                $arrTokenAd = collect(DB::table('push_token')->whereIn('UserID',  $request['RequestManager'])->where('allow_push', 1)->whereNull('deleted_at')->pluck('token_push')->toArray());
                $arrToken = ($arrTokenAd->merge(DB::table('push_token')->where('UserID', $one->UserID)->whereNull('deleted_at')->pluck('token_push')->toArray()))->all();
                $arrToken = array_unique($arrToken);
                // $arrToken = DB::table('push_token')->whereIn('UserID',  $request['RequestManager'])->whereNull('deleted_at')->pluck('token_push')->toArray();
                if (count($arrToken) > 0) {
                    $sendData = [];
                    $sendData['id'] = $one->id;
                    $sendData['data'] = "LT";
                    $headrmess = Auth::user()->FullName . " cập nhật giờ làm thêm.";
                    $bodyNoti = "Từ " . $request['STime'] . ' đến ' . $request['ETime'];

                    NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
                }
            }

            $this->sendMail($validated, 'Kính gửi Ban giám đốc');
            return AdminController::responseApi(200, null, __('admin.success.save'));
        } catch (\Exception $e) {
            return AdminController::responseApi(422, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function deleteApi(Request $request, $id = null)
    {
        $this->authorize('action', $this->delete);
        if ($id != null) {
            $one = OvertimeWork::find($id);
            if ($one != null) {
                $one->delete();
                return AdminController::responseApi(200, null, __('admin.success.delelte'));
            }
        }
        return AdminController::responseApi(422, __('admin.error.data'));
    }

    /**
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showListApproveApi(Request $request, $orderBy = 'id', $sortBy = 'desc')
    {
        $this->authorize('action', $this->viewAppr);

        $this->getListUnapprOT($request, $orderBy, $sortBy);

        $data = $this->data;
        $data['role_key'] = 'OvertimeDetailsApprove';

        return AdminController::responseApi(200, null, null, $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function countListApproveApi(Request $request)
    {
        $this->authorize('action', $this->viewAppr);

        $data['countListApprove'] = OvertimeWork::query()
            ->where('Approved', 0)
            ->where('RequestManager', 'like', '%,' . Auth::user()->id . ',%')
            ->count();
        $data['rule_key'] = 'OvertimeDetailsApprove';
        return AdminController::responseApi(200, null, null, $data);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function apprOvertimeApi(Request $request, $id = null)
    {
        $this->authorize('action', $this->approve);

        if ($id != null) {
            $one = OvertimeWork::find($id);
            if ($one) {
                $one->Approved = 1;
                $one->UpdatedBy = Auth::user()->id;
                $one->ApprovedDate = Carbon::now();
                $one->save();
                if ($one->save()) {
                    //firebase notification
                    $arrTokenAd = collect(DB::table('push_token')->whereIn('UserID',  explode(',', $one['RequestManager']))->where('allow_push', 1)->whereNull('deleted_at')->pluck('token_push')->toArray());
                    $arrToken = ($arrTokenAd->merge(DB::table('push_token')->where('UserID', $one->UserID)->whereNull('deleted_at')->pluck('token_push')->toArray()))->all();
                    $arrToken = array_unique($arrToken);
                    $eid = DB::table('overtime_works')->where('id',  $id)->whereNull('deleted_at')->pluck('UserID')->first();
                    // $arrToken = DB::table('push_token')->where('UserID',  $eid)->whereNull('deleted_at')->pluck('token_push')->toArray();
                    $stime = DB::table('overtime_works')->where('UserID',  $eid)->whereNull('deleted_at')->pluck('Stime')->first();
                    $etime = DB::table('overtime_works')->where('UserID',  $eid)->whereNull('deleted_at')->pluck('Etime')->first();
                    if (count($arrToken) > 0) {
                        $sendData = [];
                        $sendData['id'] = $id;
                        $sendData['data'] = "LT";
                        $headrmess = Auth::user()->FullName . " đã duyệt giờ làm thêm.";
                        $bodyNoti = "Từ " . Carbon::parse($stime)->format('d/m/Y H:i:s') . ' đến ' . Carbon::parse($etime)->format('d/m/Y H:i:s');

                        NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
                    }
                }
                $this->sendMail($one, 'Gửi anh/chị/em trong công ty');

                return AdminController::responseApi(200, null, __('admin.success.approve'));
            }
        }
        return AdminController::responseApi(422, __('admin.error.data'));
    }

    /**
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function unApprOvertimeApi(Request $request, $id = null)
    {
        $this->authorize('action', $this->approve);

        if ($request['Note'] == '') {
            return AdminController::responseApi(422, __('admin.error.comment-missing'));
        }
        if ($id != null) {
            $one = OvertimeWork::find($id);
            if ($one) {
                $one->Approved = 2;
                $one->UpdatedBy = Auth::user()->id;
                $one->ApprovedDate = Carbon::now();
                $one->Note = $request['Note'];

                $one->save();
                if ($one->save()) {
                    //firebase notification
                    $arrTokenAd = collect(DB::table('push_token')->whereIn('UserID',  explode(',', $one['RequestManager']))->where('allow_push', 1)->whereNull('deleted_at')->pluck('token_push')->toArray());
                    $arrToken = ($arrTokenAd->merge(DB::table('push_token')->where('UserID', $one->UserID)->whereNull('deleted_at')->pluck('token_push')->toArray()))->all();
                    $arrToken = array_unique($arrToken);
                    $eid = DB::table('overtime_works')->where('id',  $id)->whereNull('deleted_at')->pluck('UserID')->first();
                    // $arrToken = DB::table('push_token')->where('UserID',  $eid)->whereNull('deleted_at')->pluck('token_push')->toArray();
                    $stime = DB::table('overtime_works')->where('UserID',  $eid)->whereNull('deleted_at')->pluck('Stime')->first();
                    $etime = DB::table('overtime_works')->where('UserID',  $eid)->whereNull('deleted_at')->pluck('Etime')->first();
                    if (count($arrToken) > 0) {
                        $sendData = [];
                        $sendData['id'] = $id;
                        $sendData['data'] = "LT";
                        $headrmess = Auth::user()->FullName . " đã từ chối giờ làm thêm.";
                        $bodyNoti = "Lý do: " . $request['Note'];

                        NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
                    }
                }
                $this->sendMail($one, '', $request['Note']);

                return AdminController::responseApi(200, null, __('admin.success.un-approve'));
            }
        }
        return AdminController::responseApi(422, __('admin.error.data'));
    }


    // OvertimeWorkController

    /**
     * @param $request
     * @param $orderBy
     * @param $sortBy
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getListOT($request, $orderBy, $sortBy)
    {
        if ($request->has('Date')) {
            if (
                \DateTime::createFromFormat('d/m/Y', $request['Date'][0]) === FALSE && $request['Date'][0] != '' ||
                \DateTime::createFromFormat('d/m/Y', $request['Date'][1]) === FALSE && $request['Date'][1] != ''
            ) {
                return Redirect::back();
            }
        }
        //list
        $this->data['projects'] = Project::all();
        if (Schema::hasColumn('overtime_works', $orderBy)) {
            $list = OvertimeWork::query()->select('overtime_works.*', 'tb1.FullName', 'projects.NameVi', 'projects.NameShort', 'tb2.FullName as NameUpdatedBy')
                ->leftJoin('projects', 'overtime_works.ProjectID', '=', 'projects.id')
                ->leftJoin('users as tb1', 'overtime_works.UserID', '=', 'tb1.id')
                ->leftJoin('users as tb2', 'overtime_works.UpdatedBy', '=', 'tb2.id')
                ->orderBy($orderBy, $sortBy);
        } else {
            return redirect()->back();
        }
        //Search in columns
        $this->data['request'] = $request->query();
        $one = OvertimeWork::query()
            ->select('overtime_works.Content', 'overtime_works.ProjectID', 'users.FullName', 'projects.NameVi', 'users.username')
            ->leftJoin('projects', 'overtime_works.ProjectID', '=', 'projects.id')
            ->leftJoin('users', 'overtime_works.UserID', '=', 'users.id')->first();

        if ($one) {
            $one = $one->toArray();
            if (array_key_exists('search', $request->input())) {
                $list = $list->where(function ($query) use ($one, $request) {
                    foreach ($one as $key => $value) {
                        if ($key == 'FullName') {
                            $query->orWhere('tb1.' . $key, 'like', '%' . $request->input('search') . '%');
                        } elseif ($key == 'NameVi' || $key == 'NameShort') {
                            $query->orWhere('projects.' . $key, 'like', '%' . $request->input('search') . '%');
                        } elseif ($key == 'username') {
                            $query->orWhere('tb1.' . $key, 'like', '%' . $request->input('search') . '%');
                        } else {
                            $query->orWhere('overtime_works.' . $key, 'like', '%' . $request->input('search') . '%');
                        }
                    }
                    $query->orWhereRaw('(DATE_FORMAT(overtime_works.STime,"%d/%m/%Y")) like ?', '%' . $request->input('search') . '%');
                    $query->orWhereRaw('(DATE_FORMAT(overtime_works.ETime,"%d/%m/%Y")) like ?', '%' . $request->input('search') . '%');
                });
            }
        }

        // if OvertimeDate[] = []
        if (!isset($request['UserID']) && !isset($request['ProjectID']) && !isset($request['OvertimeDate'])) {
            $list = $list->where('overtime_works.STimeLogOT', '>=', Carbon::now()->startOfMonth())
                ->orWhere('overtime_works.ETimeLogOT', '>=', Carbon::now()->endOfMonth());
        }
        if ($request['UserID'] != '') {
            $list = $list->where('overtime_works.UserID', $request['UserID']);
        }
        if ($request['ProjectID'] != '') {
            $list = $list->where('overtime_works.ProjectID', $request['ProjectID']);
        }
        if (isset($request['Approved']) && $request['Approved'] === 'true') {
            $list = $list->where('Approved', '!=', 2);
        }

        foreach ($this->data['request'] as $key => $value) {
            if (is_array($value)) {
                $value[0] != '' ? $value[0] = $this->fncDateTimeConvertFomat($value[0], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : '';
                $value[1] != '' ? $value[1] = $this->fncDateTimeConvertFomat($value[1], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : '';

                $list->where(function ($query) use ($value) {
                    if ($value[0] != '' && $value[1] != '' && $value[0] !== $value[1]) {
                        $query = $query->whereBetween('overtime_works.STimeLogOT', array(Carbon::parse($value[0])->startOfDay(), Carbon::parse($value[1])->endOfDay()))
                            ->orWhereBetween('overtime_works.ETimeLogOT', array(Carbon::parse($value[0])->startOfDay(), Carbon::parse($value[1])->endOfDay()))
                            ->orWhereBetween('overtime_works.STime', array(Carbon::parse($value[0])->startOfDay(), Carbon::parse($value[1])->endOfDay()))
                            ->orWhereBetween('overtime_works.ETime', array(Carbon::parse($value[0])->startOfDay(), Carbon::parse($value[1])->endOfDay()));
                    }
                    if ($value[0] === $value[1] && $value[0] != '') {
                        $query = $query->whereRaw("CAST(overtime_works.STimeLogOT AS DATE) = '$value[0]'")
                            ->orWhereRaw("CAST(overtime_works.STime AS DATE) = '$value[0]'");
                    }
                    if ($value[0] != '' && $value[1] == '') {
                        $query = $query->where('overtime_works.STimeLogOT', '>=', Carbon::parse($value[0])->startOfDay())
                            ->orWhere('overtime_works.ETimeLogOT', '>=', Carbon::parse($value[0])->startOfDay())
                            ->orWhere('overtime_works.STime', '>=', Carbon::parse($value[0])->startOfDay())
                            ->orWhere('overtime_works.ETime', '>=', Carbon::parse($value[0])->startOfDay());
                    }
                    if ($value[0] == '' && $value[1] != '') {
                        $query = $query->where('overtime_works.STimeLogOT', '<=', Carbon::parse($value[1])->startOfDay())
                            ->orWhere('overtime_works.ETimeLogOT', '<=', Carbon::parse($value[1])->startOfDay())
                            ->orWhere('overtime_works.STime', '<=', Carbon::parse($value[1])->startOfDay())
                            ->orWhere('overtime_works.ETime', '<=', Carbon::parse($value[1])->startOfDay());
                    }
                });
            }
        }
        $count = $list->count();

        //Pagination
        $recordPerPage = $this->getRecordPage();
        $list = $list->paginate($recordPerPage);
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);

        if ($list->count() == 0) {
            if (array_key_exists('page', $query_array)) {
                if ($query_array['page'] > 1) {
                    $query_array['page'] = $list->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl  = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }
            }
        }

        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['sort'] = $sort;
        $this->data['list'] = $list;
        $this->data['weekMap']  = self::WEEK_MAP;

        return $this->data;
    }

    /**
     * @param $request
     * @param $orderBy
     * @param $sortBy
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getListUnapprOT($request, $orderBy, $sortBy)
    {
        $recordPerPage = config('settings.records_per_page');

        $this->data['projects'] = Project::all();

        $overtime = OvertimeWork::query()
            ->select('overtime_works.*', 'tb1.FullName', 'projects.NameVi', 'projects.NameShort', 'tb2.FullName as NameUpdatedBy')
            ->leftJoin('projects', 'overtime_works.ProjectID', '=', 'projects.id')
            ->leftJoin('users as tb1', 'overtime_works.UserID', '=', 'tb1.id')
            ->leftJoin('users as tb2', 'overtime_works.UpdatedBy', '=', 'tb2.id')
            ->where('Approved', 0)
            ->where('RequestManager', 'like', '%,' . Auth::user()->id . ',%');

        if (Schema::hasColumn('overtime_works', $orderBy)) {
            $overtime->orderBy($orderBy, $sortBy);
        }

        $this->data['request'] = $request->query();

        $one = OvertimeWork::query()
            ->select('overtime_works.Content', 'overtime_works.ProjectID', 'users.FullName', 'projects.NameShort', 'projects.NameVi')
            ->leftJoin('projects', 'overtime_works.ProjectID', '=', 'projects.id')
            ->leftJoin('users', 'overtime_works.UserID', '=', 'users.id')
            ->first();

        if ($one) {
            $one = $one->toArray();
            if (array_key_exists('search', $request->input())) {
                $overtime = $overtime->where(function ($query) use ($one, $request) {
                    foreach ($one as $key => $value) {
                        if ($key == 'FullName') {
                            $query->orWhere('tb1.' . $key, 'like', '%' . $request->input('search') . '%');
                        } elseif ($key == 'NameVi' || $key == 'NameShort') {
                            $query->orWhere('projects.' . $key, 'like', '%' . $request->input('search') . '%');
                        } else {
                            $query->orWhere('overtime_works.' . $key, 'like', '%' . $request->input('search') . '%');
                        }
                    }
                    $query->orWhereRaw('(DATE_FORMAT(overtime_works.STime,"%d/%m/%Y")) like ?', '%' . $request->input('search') . '%');
                    $query->orWhereRaw('(DATE_FORMAT(overtime_works.ETime,"%d/%m/%Y")) like ?', '%' . $request->input('search') . '%');
                });
            }
        }
        $count = $overtime->count();

        $overtime = $overtime->paginate($recordPerPage);
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);

        if ($overtime->count() == 0) {
            if (array_key_exists('page', $query_array)) {
                if ($query_array['page'] > 1) {
                    $query_array['page'] = $overtime->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl  = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }
            }
        }
        $this->data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $this->data['sort'] = $sort;
        $this->data['query_array'] = $query_array;
        $this->data['overtime'] = $overtime;
        $this->data['sort_link'] = $sort_link;
        $this->data['weekMap'] = self::WEEK_MAP;
    }

    /**
     * @param $request
     * @param $data
     */
    public function funcSearchWithRequest($request, $data)
    {
        if (!$request->has('UserID') && !$request->has('ProjectID') && !$request->has('date')) {
            $data = $data->where(function ($query1) {
                $query1->whereBetween(
                    'overtime_works.STime',
                    array(
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth()
                    )
                )->orWhereBetween(
                    'overtime_works.ETime',
                    array(
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth()
                    )
                )
                    ->orWhereBetween(
                        'overtime_works.ETimeLogOT',
                        array(
                            Carbon::now()->startOfMonth(),
                            Carbon::now()->endOfMonth()
                        )
                    )
                    ->orWhereBetween(
                        'overtime_works.STimeLogOT',
                        array(
                            Carbon::now()->startOfMonth(),
                            Carbon::now()->endOfMonth()
                        )
                    );
            });
        }
        //If not selected user
        if ($request['UserID'] != '') {
            $data = $data->where('overtime_works.UserID', $request['UserID']);
        }
        //If not selected project
        if ($request['ProjectID'] != '') {
            $data = $data->where('overtime_works.ProjectID', $request['ProjectID']);
        }

        //If request date not null
        foreach ($this->data['request'] as $key => $value) {
            if (is_array($value)) {
                $value[0] != '' ? $value[0] = $this->fncDateTimeConvertFomat(
                    $value[0],
                    self::FOMAT_DISPLAY_DMY,
                    self::FOMAT_DB_YMD
                ) : '';
                $value[1] != '' ? $value[1] = $this->fncDateTimeConvertFomat(
                    $value[1],
                    self::FOMAT_DISPLAY_DMY,
                    self::FOMAT_DB_YMD
                ) : '';

                $data->where(function ($query) use ($value) {
                    if ($value[0] != '' && $value[1] != '' && $value[0] !== $value[1]) {
                        $query = $query->whereBetween(
                            'overtime_works.STime',
                            array(
                                Carbon::parse($value[0])->startOfDay(),
                                Carbon::parse($value[1])->startOfDay()
                            )
                        )
                            ->orWhereBetween(
                                'overtime_works.ETime',
                                array(
                                    Carbon::parse($value[0])->startOfDay(),
                                    Carbon::parse($value[1])->startOfDay()
                                )
                            )
                            ->orWhereBetween(
                                'overtime_works.STimeLogOT',
                                array(
                                    Carbon::parse($value[0])->startOfDay(),
                                    Carbon::parse($value[1])->startOfDay()
                                )
                            )
                            ->orWhereBetween(
                                'overtime_works.ETimeLogOT',
                                array(
                                    Carbon::parse($value[0])->startOfDay(),
                                    Carbon::parse($value[1])->startOfDay()
                                )
                            );
                    }
                    if ($value[0] === $value[1] && $value[0] != '') {
                        $query = $query->whereRaw("CAST(overtime_works.STime AS DATE) = '$value[0]'")
                            ->whereRaw("CAST(overtime_works.STimeLogOT AS DATE) = '$value[0]'");
                    }
                    if ($value[0] != '' && $value[1] == '') {
                        $query = $query->where('overtime_works.STime', '>=', Carbon::parse($value[0])->startOfDay())
                            ->orWhere('overtime_works.STimeLogOT', '>=', Carbon::parse($value[0])->startOfDay())
                            ->orWhere('overtime_works.ETime', '>=', Carbon::parse($value[0])->startOfDay())
                            ->orWhere('overtime_works.ETimeLogOT', '>=', Carbon::parse($value[0])->startOfDay());
                    }
                    if ($value[0] == '' && $value[1] != '') {
                        $query = $query->where('overtime_works.STime', '>=', Carbon::parse($value[1])->startOfDay())
                            ->orWhere('overtime_works.STimeLogOT', '>=', Carbon::parse($value[0])->startOfDay())
                            ->orWhere('overtime_works.ETime', '>=', Carbon::parse($value[0])->startOfDay())
                            ->orWhere('overtime_works.ETimeLogOT', '>=', Carbon::parse($value[0])->startOfDay());
                    }
                });
            }
        }
    }

    /**
     * @param $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function getListReport($request)
    {
        if ($request->has('date')) {
            if (
                \DateTime::createFromFormat('d/m/Y', $request['date'][0]) === FALSE && $request['date'][0] != '' ||
                \DateTime::createFromFormat('d/m/Y', $request['date'][1]) === FALSE && $request['date'][1] != ''
            ) {
                return Redirect::back();
            }
        }

        //Get info OvertimeWork
        $userList = OvertimeWork::query()->select('overtime_works.*', 'users.FullName')
            ->join('users', 'overtime_works.UserID', '=', 'users.id')
            ->groupBy('UserID')
            ->orderBy('UserID', 'asc');


        $projects = OvertimeWork::query()
            ->select('projects.*')
            ->join('projects', 'projects.id', 'overtime_works.ProjectID')
            ->groupBy('ProjectID')
            ->orderBy('ProjectID', 'asc');

        $this->data['request'] = $request->query();

        $this->funcSearchWithRequest($request, $userList);
        $this->funcSearchWithRequest($request, $projects);

        //calculate time of project
        $userList = $userList->get();
        $projects = $projects->get();
        $totalOvertimeOnProject = [];
        $totalOvertimeWeekend = 0;
        $totalOvertimeNotWeekend = 0;

        foreach ($projects as $project) {
            $totalOvertimeOnProject[] = 0;
        }

        foreach ($userList as $user) {
            $user->FullName = User::find($user->UserID)->FullName;
            $tempData = [];
            $tempData1 = [];
            $key = 0;

            foreach ($projects as $project) {
                $works = OvertimeWork::query()
                    ->where('ProjectID', $project->id)
                    ->where('UserID', $user->UserID)
                    //                    ->where('Approved', '!=', 2);
                    ->where('Approved', 'like', 1);

                $this->funcSearchWithRequest($request, $works);

                $works = $works->get();
                $totalHours = 0;
                $totalHourWeekend = 0;

                foreach ($works as $work) {
                    $timeOTinday = Carbon::parse($work->STime)->diffInSeconds(Carbon::parse($work->ETime)) / 3600 - $work->BreakTime;
                    $totalHours +=  $timeOTinday > 0 ?  $timeOTinday : 0;
                    if (Carbon::parse($work->STime)->isWeekend() && Carbon::parse($work->ETime)->isWeekend()) {
                        $totalHourWeekend += Carbon::parse($work->STime)->diffInSeconds(Carbon::parse($work->ETime)) / 3600 - $work->BreakTime;
                    }
                }

                $tempData[] = number_format($totalHours, 2);
                $user->workOnProject = $tempData;
                $user->totalOvertime = array_sum($tempData);

                $tempData1[] = number_format($totalHourWeekend, 2);
                $user->totalOvertimeWeekend = array_sum($tempData1);
                $user->totalOvertimeNotWeekend = array_sum($tempData) - array_sum($tempData1);
                $totalOvertimeOnProject[$key] += $tempData[$key];
                $key++;
            }
            $totalOvertimeNotWeekend  += $user->totalOvertime - $user->totalOvertimeWeekend;
            $totalOvertimeWeekend     += $user->totalOvertimeWeekend;
        }

        $this->data['export'] = $this->export;
        $this->data['userList'] = $userList;
        $this->data['projects'] = $projects;
        $this->data['totalOvertimeOnProject']   = $totalOvertimeOnProject;
        $this->data['totalOvertimeNotWeekend']  = $totalOvertimeNotWeekend;
        $this->data['totalOvertimeWeekend']     = $totalOvertimeWeekend;

        return $this->data;
    }

    /**
     * send mail
     * @param $array
     * @param $header
     * @param null $note
     * @return bool
     */
    public function sendMail($array, $header, $note = null)
    {
        if (!isset($array['Approved'])) {
            $End = $this->fncDateTimeConvertFomat($array['ETimeLogOT'], 'd/m/Y H:i', self::FOMAT_DB_YMD);
        }

        $arrMail = [];
        $arrMailCc = [];
        if (isset($array['Approved'])) {
            if ($array['ETimeLogOT'] < Carbon::now()) {
                return false;
            }
            $array['STimeLogOT'] = $this->fncDateTimeConvertFomat($array['STimeLogOT'], self::FOMAT_DB_YMD_HIS, FOMAT_DISPLAY_DATE_TIME);
            $array['ETimeLogOT'] = $this->fncDateTimeConvertFomat($array['ETimeLogOT'], self::FOMAT_DB_YMD_HIS, FOMAT_DISPLAY_DATE_TIME);
        }

        $users = User::find($array['UserID']);
        $projects = Project::find($array['ProjectID']);

        if (!is_array($array['RequestManager'])) {
            $array['RequestManager'] = array_filter(explode(',', $array['RequestManager']));
        }
        foreach ($array['RequestManager'] as $value) {
            $mailUser = User::find($value);
            if ($mailUser->email != null) {
                $arrMailAddressTo = $mailUser->email;
                $arrMail[] = $arrMailAddressTo;
            }
        }
        $arrMailAddressTo = array_unique($arrMail);

        $arrayMailCc = MasterData::query()->where('DataValue', 'EM002')->get();
        $mailCc = array_filter(explode(',', $arrayMailCc[0]['DataDescription']));
        foreach ($mailCc as $value) {
            $arrMailCc[] = $value;
        }

        $viewBladeMail = 'template_mail.overtime-mail';
        $apr = isset($array['Approved']) ? $array['Approved'] : '';

        //cách gọi Mr,Ms,Mrs
        if ($users['Gender'] == 0) {
            $users['Gender'] = 'Mr';
        }
        if ($users['Gender'] == 1 && $users['MaritalStt'] == 1) {
            $users['Gender'] = 'Mrs';
        }
        if ($users['Gender'] == 1 && $users['MaritalStt'] == 0) {
            $users['Gender'] = 'Ms';
        }

        //gộp thời gian nếu cùng ngày
        $dateStart = $this->fncDateTimeConvertFomat($array['STimeLogOT'], FOMAT_DISPLAY_DATE_TIME, self::FOMAT_DB_YMD_HIS);
        $dateEnd = $this->fncDateTimeConvertFomat($array['ETimeLogOT'], FOMAT_DISPLAY_DATE_TIME, self::FOMAT_DB_YMD_HIS);
        $diffDay = Carbon::parse($dateStart)->diffInDays(Carbon::parse($dateEnd));

        $viewTime = 'từ ' . $array['STime'] . ' - ' . $array['ETimeLogOT'];
        if ($diffDay == 0) {
            $viewTime = Carbon::parse($dateStart)->format('d/m/Y') . ' ' . Carbon::parse($dateStart)->format(FOMAT_DISPLAY_TIME) . '-' . Carbon::parse($dateEnd)->format(FOMAT_DISPLAY_TIME);
        }
        $nameProject = $projects['NameVi'];
        if (strlen($projects['NameVi']) > 30) {
            $nameProject = $projects['NameShort'];
        }

        $dataBinding = [
            'Header'    => $header,
            'ProjectID' => $nameProject,
            'FullName'  => $users['FullName'],
            'viewTime'  => $viewTime,
            'Content'   => $array['Content'],
            'Gender'    => $users['Gender'],
            'Approved'  => $apr,
            'Note'      => $note,
        ];

        if (isset($array['Approved'])) {
            $nameFrom = 'Văn Phòng';
            if ($array['Approved'] == 1) {
                $subjectMail = 'TB duyệt làm thêm - Dự án: ' . $nameProject . '(' . $viewTime . ')';
            } else {
                $subjectMail = 'TB từ chối làm thêm - Dự án: ' . $nameProject . '(' . $viewTime . ')';
            }

            $arrMailAddressTo = [];
            if ($users['email'] != null) {
                array_push($arrMailAddressTo, $users['email']);
            } else {
                $replace_mailTO = MasterData::query()->where('DataValue', '=', 'EM006')->first();
                $arrMailAddressTo = explode(',', $replace_mailTO->DataDescription);
            }

            $userApproved = User::find($array['UpdatedBy']);
            if ($userApproved['email'] != null) {
                $arrMailCc[] = $userApproved['email'];
            }
        } else {
            $nameFrom = $users['FullName']  . ' Dự án: ' . $nameProject;
            $subjectMail = 'TB làm thêm - Dự án: ' . $nameProject . '(' . $viewTime . ')';

            if ($users['email'] !== null) {
                $arrMailCc[] = $users['email'];
            }
        }

        $addressMailCc = array_diff($arrMailCc, $arrMailAddressTo);

        $this->SendMailWithView([
            self::KEY_SUBJECT_MAIL => $subjectMail,
            self::KEY_VIEW_MAIL => $viewBladeMail,
            self::KEY_DATA_BINDING => $dataBinding,
            self::KEY_MAIL_NAME_FROM => $nameFrom,
            self::KEY_MAIL_ADDRESS_TO => $arrMailAddressTo,
            self::KEY_MAIL_ADDRESS_CC => $addressMailCc,
        ]);
    }
}
