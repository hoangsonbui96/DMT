<?php

namespace App\Http\Controllers\Admin\Work;

use App\Exports\WorkingScheduleExport;
use App\MasterData;
use App\Menu;
use App\Model\Absence;
use App\DailyReport;
use App\Http\Controllers\Admin\Absence\AbsenceController;
use App\RoleScreenDetail;
use App\RoleUserScreenDetailRelationship;
use App\Room;
use App\Project;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Requests\WorkingScheduleRequest;
use App\Http\Requests\DailyReportRequest;
use App\Http\Controllers\Admin\DailyReportController;
use App\Http\Requests\AbsenceRequest;
use App\Model\ListPosition;
use App\model\ListPositionUser;
// use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

use App\Model\WorkingSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\Console\Input\Input;

/**
 * Controller screen Working Schedule
 * Class WorkingScheduleController
 * @package App\Http\Controllers\Admin\Work
 */
class WorkingScheduleController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $app;
    protected $export;

    protected $urlAnFa = 'https://anfaapi.akb.vn:433/api/mode/akb/';

    const KEYMENU = array(
        "add" => "WorkingScheduleAdd",
        "view" => "WorkingSchedule",
        "edit" => "WorkingScheduleEdit",
        "delete" => "WorkingScheduleDelete",
        "export" => "WorkingScheduleExport",
        "app" => "ListApprove"
    );
    const timeWorking = [1 => '1 Ngày', 2 => '1 Tuần', 3 => '1 Tháng'];

    /**
     * WorkingScheduleController constructor.
     * @param Request $request
     * Check role view, insert, update
     */
    public function __construct(Request $request)
    {
        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
        $array = $this->RoleView('WorkingSchedule', ['WorkingSchedule', 'AbsenceListApprove']);
        $this->menu = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if ($value == $row->alias)
                    $this->$key = $row;
            }
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Exception
     */
    public function export(Request $request)
    {
        $records = $this->getListWithRequest($request, 'Date', 'desc', 'export');
        if ($records->count() > 0) {
            return Excel::download(new WorkingScheduleExport($records), 'Danh_sách_công_tác.xlsx');
        } else {
            return $this->jsonErrors('Không có dữ liệu.');
        }
    }

    /**
     * Get data working schedule
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return View screen (working-schedule)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Request $request, $orderBy = 'Date', $sortBy = 'desc')
    {
        $this->authorize('view', $this->menu);
        $recordPerPage = $this->getRecordPage();
        $working_schedule = $this->getListWithRequest($request, $orderBy, $sortBy);
        $working_schedule = $working_schedule->paginate($recordPerPage);
        $groupDataKey = ListPosition::query()
            ->select('DataValue', 'Name')
            ->groupBy('DataValue', 'Name')
            ->orderBy('DataValue', 'desc')
            ->get();

        $groupDataKeyNotNull = [];
        foreach ($groupDataKey as $item) {
            $existUser = ListPositionUser::query()->where('DataValue', $item['DataValue'])->get();
            $arrayListPositionUserEachValueCheck = [];
            foreach ($existUser as $item1) {
                $check = User::query()->where('id', '=', $item1['UserId'])->where('Active', '=', 1)->first();
                if ($check) {
                    array_push($arrayListPositionUserEachValueCheck, $item1['UserId']);
                }
            }
            if ($arrayListPositionUserEachValueCheck) {
                array_push($groupDataKeyNotNull, $item);
            }
        }
        $groupDataKey = $groupDataKeyNotNull;

        foreach ($working_schedule->items() as $i => &$value) {
            $arr_id = explode(",", $value['AssignID']);
            $name = [];
            foreach ($arr_id as $id) {
                if ($id != "0" && $id != "") {
                    $name_push = User::withTrashed()->where('id', $id)->first();
                    if ($name_push) {
                        array_push($name, $name_push->FullName);
                    }
                }
            }
            $value['AssignName'] = implode(", ", $name);
            //lay ra danh sach nhom chuc vu va nhan vien
            $arrayListPositionUser = [];
            $arrIdUser = explode(",", $value['AssignID']);
            foreach ($groupDataKey as $item) {
                $ListPositionUser =  ListPositionUser::query()->where('DataValue', '=', $item->DataValue)->select('UserId')->groupBy('UserId')->get();
                $arrayListPositionUserEachValue = [];
                foreach ($ListPositionUser as $key) {
                    $check = User::query()->where('id', '=', $key->UserId)->where('Active', '=', 1)->first();
                    if ($check) {
                        array_push($arrayListPositionUserEachValue, $key->UserId);
                    }
                }
                if ($arrayListPositionUserEachValue && $arrayListPositionUserEachValue[0] != null) {
                    array_push($arrayListPositionUser, ['DataValue' => $item->DataValue, 'item' => $arrayListPositionUserEachValue]);
                }
            };
            $arrayListPositionUserEachValue = [];
            $arrayIdUserCheck = explode(",", $value['AssignID']);
            foreach ($arrayListPositionUser as $item) {
                $result = array_intersect($item['item'], $arrIdUser);
                if (count($result) == count($item['item']) && count($result) != 0) {
                    array_push($arrayListPositionUserEachValue, $item['DataValue']);
                    foreach ($item['item'] as $key) {
                        if (($key = array_search($key, $arrayIdUserCheck)) !== false) {
                            unset($arrayIdUserCheck[$key]);
                        }
                    }
                }
            }
            foreach ($arrayIdUserCheck as $key) {
                if (($key = array_search("", $arrayIdUserCheck)) !== false) {
                    unset($arrayIdUserCheck[$key]);
                }
            }
            $dataAssignID = [];
            foreach(explode(",", $value['AssignID']) as $item1){
                if($item1!=""){
                    array_push($dataAssignID,$item1);
                }
            }
            $value['AssignID'] = implode(',',$dataAssignID);
            $value['listPosition'] = implode(',', $arrayListPositionUserEachValue);
            $value['AssignIDAfterRemovePosition'] = implode(',', $arrayIdUserCheck) != "" ? implode(',', $arrayIdUserCheck) : null;
            $value['AssignIDOld'] = implode(',', $arrayIdUserCheck) != "" ? implode(',', $arrayIdUserCheck) : null;
        }
        $count = $working_schedule->count();

        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);

        //redirect to the last page if current page has no record
        if ($working_schedule->count() == 0) {
            if (array_key_exists('page', $query_array)) {
                if ($query_array['page'] > 1) {
                    $query_array['page'] = $working_schedule->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }
            }
        }
        $rooms = Room::query()->where('Active', 1);
        $this->data['role_key'] = 'WorkingSchedule';
        $this->data['master_datas'] = $this->getReasonAbsence();
        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $this->data['working_schedule'] = $working_schedule;
        $this->data['request'] = $request;
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['sort'] = $sort;
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;
        $this->data['export'] = $this->export;
        $this->data['groupDataKey'] = $groupDataKey;
        $this->data['all_projects'] = Project::query()
            ->where('Member', 'like', '%' . Auth::user()->id . '%')
            ->orWhere('Leader', 'like', '%' . Auth::user()->id . '%')->get();
        $this->data['rooms'] = $rooms->get();
        if (strpos(\Request::getRequestUri(), 'api') !== false) {
            return response()->json(['data' => $this->data]);
        }
        return $this->viewAdminLayout('work.working-schedule', $this->data);
    }

    /**
     * @param Request $request
     * @param null $id
     * @param null $del
     * @return View popup (working-schedule-detail)
     */
    public function showDetail(Request $request, $id = null, $del = null)
    {
        $this->data['user_assign'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['master_datas'] = $this->getReasonAbsence();
        $this->data['boolean'] = 1;
        $this->data['add'] = $this->add;

        $this->data['all_project'] = Project::query()
            ->where('Member', 'like', '%' . Auth::user()->id . '%')
            ->orWhere('Leader', 'like', '%' . Auth::user()->id . '%')->get();
        $this->data['rooms'] = Room::query()->select('id', 'Name')
            ->where('Active', 1)
            ->get();
        $this->data['master_datas_working'] = MasterData::query()->where('DataKey', 'MBD')->get();

        $this->data['groupDataKey'] = ListPosition::query()
            ->select('DataValue', 'Name')
            ->groupBy('DataValue', 'Name')
            ->orderByDesc('DataValue')
            ->get();

        foreach ($this->data['groupDataKey'] as &$item) {
            $existUser = ListPositionUser::query()->where('DataValue', $item['DataValue'])->pluck("UserId");
            $users = User::query()->where("Active", self::USER_ACTIVE_FLAG)
                ->whereIn("id", $existUser)->pluck("id");
            $item->PositionUser = $users ? implode(',', $users->toArray()) : '';
        }

        $groupDataKeyNotNull = [];
        foreach ($this->data['groupDataKey'] as $item) {
            $existUser = ListPositionUser::query()->where('DataValue', $item['DataValue'])->get();
            $arrayListPositionUserEachValueCheck = [];
            foreach ($existUser as $item1) {
                $check = User::query()->where('id', '=', $item1['UserId'])->where('Active', '=', 1)->first();
                if ($check) {
                    array_push($arrayListPositionUserEachValueCheck, $item1['UserId']);
                }
            }
            if ($arrayListPositionUserEachValueCheck) {
                array_push($groupDataKeyNotNull, $item);
            }
        }
        $this->data['groupDataKey'] = $groupDataKeyNotNull;
        if ($id != null) {
            $this->data['working_schedule_info'] = WorkingSchedule::find($id);

            $arrIdUser = explode(",", $this->data['working_schedule_info']->AssignID);
            $arrayListPositionUser = [];
            foreach (($this->data['groupDataKey']) as $item) {
                $ListPositionUser =  ListPositionUser::query()->where('DataValue', '=', $item->DataValue)->select('UserId')->groupBy('UserId')->get();
                $arrayListPositionUserEachValue = [];
                foreach ($ListPositionUser as $key) {
                    $check = User::query()->where('id', '=', $key->UserId)->where('Active', '=', 1)->first();
                    if ($check) {
                        array_push($arrayListPositionUserEachValue, $key->UserId);
                    }
                }
                if ($arrayListPositionUserEachValue && $arrayListPositionUserEachValue[0] != null) {
                    array_push($arrayListPositionUser, ['DataValue' => $item->DataValue, 'item' => $arrayListPositionUserEachValue]);
                }
            };
            $arrayListPositionUserEachValue = [];
            $arrayIdUserCheck = $arrIdUser;
//            foreach ($arrayListPositionUser as $item) {
//                $result = array_intersect($item['item'], $arrIdUser);
//                if (count($result) == count($item['item']) && count($result) != 0) {
//                    array_push($arrayListPositionUserEachValue, $item['DataValue']);
//                    foreach ($item['item'] as $key) {
//                        if (($key = array_search($key, $arrayIdUserCheck)) !== false) {
//                            unset($arrayIdUserCheck[$key]);
//                        }
//                    }
//                }
//            }
//            foreach ($arrayIdUserCheck as $key) {
//                if (($key = array_search("", $arrayIdUserCheck))) {
//                    unset($arrayIdUserCheck[$key]);
//                }
//            }
            if (array_search(0, $arrayIdUserCheck)){
                $arrayIdUserCheck = User::all()->pluck("id")->toArray();
            }
            $this->data['working_schedule_info']->listPosition = implode(',', $arrayListPositionUserEachValue);
            $this->data['working_schedule_info']->AssignID = implode(',', $arrayIdUserCheck) != ""
                ? implode(',', $arrayIdUserCheck)
                : null;
            $this->data['working_schedule_info']->AssignIDOld = implode(',', $arrayIdUserCheck) != ""
                ? implode(',', $arrayIdUserCheck)
                : null;
            if ($del == 'del') {
                $one = WorkingSchedule::find($id);
                if ($one != null) {
                    if (Carbon::parse($one->Date) >= Carbon::now()->format(self::FOMAT_DB_YMD)) {
                        $this->sendMail([], $one, true);
                    }
                    try {
                        $client = new \GuzzleHttp\Client();
                        $guzzleResult = $client->get(
                            $this->urlAnFa . 'delete/' . $id,
                            [
                                'verify' => false,
                            ]
                        );
                    } catch (\GuzzleHttp\Exception\RequestException $e) {
                        $guzzleResult = $e->getResponse();
                    }
                    if ($one['in_out'] == 1) {
                        $listAbsenceUserWS = Absence::query()->where('IdWS', $one->id)->get();
                        $listDailyReportUserWS = DailyReport::query()->where('IdWS', $one->id)->get();
                        //xoa bao cao hang ngay
                        foreach ($listDailyReportUserWS as $itemDelete) {
                            $listDailyReportUserWSDelete = DailyReport::query()->where('id', $itemDelete['id'])->delete();
                        }
                        //xoa vang mat
                        foreach ($listAbsenceUserWS as $itemDelete) {
                            $listAbsenceUserWSDelete = Absence::query()->where('id', $itemDelete['id'])->delete();
                        }
                    }
                    $check = $one->delete();
                    if ($check) {
                        // firebase notification
                        if ($arrIdUser[0] === "0") {
                            $arrToken = DB::table('push_token')->whereNull('deleted_at')->where('allow_push', 1)->pluck('token_push')->toArray();
                        } else {
                            $arrToken = DB::table('push_token')->whereIn('UserID', $arrIdUser)->whereNull('deleted_at')->where('allow_push', 1)->pluck('token_push')->toArray();
                        }

                        if (count($arrToken) > 0) {
                            $sendData = [];
                            $sendData['id'] = $id;
                            $sendData['data'] = "LCT";
                            $headrmess = "Lịch công tác của bạn bị hủy";
                            $bodyNoti = "Ngày:" . Carbon::parse($this->data['working_schedule_info']->Date)->format('d/m/Y') . " từ " . Carbon::parse($this->data['working_schedule_info']->STime)->format('H:i') . ' đến ' . Carbon::parse($this->data['working_schedule_info']->ETime)->format('H:i');
                            NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
                        }
                    }
                }

                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                    return response()->json(['success' => 'Xóa thành công.']);
                }

                return 1;
            }
            if ($this->data['working_schedule_info']) {
                if (!is_null($this->data['working_schedule_info']->RequestManager)) {
                    $this->data['working_schedule_info']->RequestManager = explode(',', $this->data['working_schedule_info']->RequestManager);
                    $this->data['boolean'] = 2;
                } else {
                    $this->data['working_schedule_info']->RequestManager = [];
                }
                return $this->viewAdminIncludes('work.working-schedule-detail', $this->data);
            } else {
                return "";
            }
        } else {
            return $this->viewAdminIncludes('work.working-schedule-detail', $this->data);
        }
    }

    /**
     * @param WorkingScheduleRequest $request
     * @param null $id
     * @return string|void
     */
    public function store(WorkingScheduleRequest $request, $id = null)
    {
        // validate
        $dataRequestStore = $request->input();
        if (count($request->input()) === 0) {
            return abort('404');
        }
        if (count($request->get("ProjectID")) != 0) {
            $projectID = $request->get("ProjectID")[0];
        } else {
            $projectID = null;
        }
        try {
            if (!$request['assign_id'] && !$request['listPosition']) {
                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                    return AdminController::responseApi(422, 'Chưa chọn người hoặc nhóm chức vụ thực hiện.');
                }
                return $this->jsonErrors('Chưa chọn người hoặc nhóm chức vụ thực hiện.');
            }

            $data = [
                'date_work' => 'Date',
                'stime' => 'STime',
                'etime' => 'ETime',
                'content' => 'Content',
                'note' => 'Note',
                'address' => 'Address'
                // 'user_id' => 'UserID',
            ];
            // Check valid vacation time
            $date = $this->fncDateTimeConvertFomat($request->date_work, 'd/m/Y', 'Y-m-d');
            //            if ($this->checkHoliday(Carbon::parse($date))) {
            //                return $this->jsonErrors('Không đăng ký lịch công tác vào ngày nghỉ.');
            //            }
            if (Carbon::parse($request->etime) < Carbon::parse($request->stime)) {
                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                    return AdminController::responseApi(422, 'Thời gian kết thúc phải lớn hơn thời gian bắt đầu');
                }
                return $this->jsonErrors('Thời gian kết thúc phải lớn hơn thời gian bắt đầu');
            }
            if (Carbon::parse($request->etime)->diffInMinutes(Carbon::parse($request->stime)) < 5) {
                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                    return AdminController::responseApi(422, 'Thời gian công tác tối thiểu là 5 phút');
                }
                return $this->jsonErrors('Thời gian công tác tối thiểu là 5 phút');

            }
            // Check valid rooms
            if ($request->rooms == null && $request->gender == 0) {
                return $this->jsonErrors('Vui lòng chọn phòng');
            }
            if ($request->gender == 1 && $request->ProjectID[0] == null) {
                return $this->jsonErrors('Vui lòng chọn dự án');
            }
            $modeIsUpdate = array_key_exists('id', $request->input());
            $one = !$modeIsUpdate ? new WorkingSchedule() : WorkingSchedule::find($request->id);
            foreach ($data as $key => $value) {
                if (isset($request->$key) && $request->$key != '') {
                    if ($key == 'date_work') {
                        $request->$key = $this->fncDateTimeConvertFomat($request->$key, 'd/m/Y', 'Y-m-d');
                    }
                    $one->$value = $request->$key;
                } else {
                    $one->$value = null;
                }
            }

            $one->UserID = !$modeIsUpdate ? Auth::user()->id : $one->UserID;

            $one->ProjectID = $projectID;

            $one->in_out = $request->get("gender");
            $one->roomsID = $request->get("rooms");
            $one->minuteRoom = $request->get("minute");
            $arrayListPositionUser = [];
            if ($request->get("listPosition")) {
                foreach ($request->get("listPosition") as $item) {
                    $ListPosition = ListPositionUser::query()->where('DataValue', '=', $item)->get();
                    foreach ($ListPosition as $id) {
                        $check = User::query()->where('id', '=', (string)$id['UserId'])->where('Active', '=', 1)->first();
                        if ($check) {
                            array_push($arrayListPositionUser, (string)$id['UserId']);
                        }
                    }
                }
            }
            if ($request->get("assign_id")) {
                foreach ($request->get("assign_id") as $id) {
                    array_push($arrayListPositionUser, $id);
                }
            }
//            if (($key = array_search("0", array_unique($arrayListPositionUser))) !== false) {
//                $one->AssignID = "0";
//            } else {
//                $one->AssignID = ',' . implode(',', array_unique($arrayListPositionUser)) . ',';
//            }
            if (count(array_unique($arrayListPositionUser)) == User::all()->count()){
                $one->AssignID = "0";
            } else {
                $one->AssignID = ',' . implode(',', array_unique($arrayListPositionUser)) . ',';
            }
            if ($one->in_out == 1) {

                $timeWork = Carbon::parse($dataRequestStore['etime'])->diffInMinutes(Carbon::parse($dataRequestStore['stime']));

                $requestDailyReportNew = new DailyReportRequest;

                // $requestDailyReportNew['_token'] = $dataRequestStore['_token'];
                $requestDailyReportNew['Date'] = [$dataRequestStore['date_work']];
                $requestDailyReportNew['ProjectID'] = $dataRequestStore['ProjectID'];
                $requestDailyReportNew['ScreenName'] =  [null];
                $requestDailyReportNew['TypeWork'] = ["BC007"];
                $requestDailyReportNew['Contents'] = [$dataRequestStore['content']];
                $requestDailyReportNew['WorkingTime'] =  [strval($timeWork / 60)];
                $requestDailyReportNew['Progressing'] = ["100"];
                $requestDailyReportNew['Note'] = [$dataRequestStore['note']];

                //Kiem tra nguoi tao lich co phai la PM(QL003) hoac GD(QL001) hoac PDG(QL002) khong
                $listPositionQL = ListPositionUser::query()->whereIn('DataValue', ['QL001', 'QL002', 'QL003'])->get();
                $listPositionQLId = [];
                foreach ($listPositionQL as $item) {
                    array_push($listPositionQLId, $item['UserId']);
                }
                $requestAbsenceNew = new AbsenceRequest;
                if (in_array($one->UserID, $listPositionQLId)) {
                    $requestAbsenceNew['Approved'] =  1;
                }
                // $requestAbsenceNew['_token'] = $dataRequestStore['_token'];

                $requestAbsenceNew['RoomID'] =  $dataRequestStore['rooms'];
                $requestAbsenceNew['MasterDataValue'] = 'VM006';
                $requestAbsenceNew['SDate'] = $dataRequestStore['date_work'] . ' ' . $dataRequestStore['stime'];
                $requestAbsenceNew['EDate'] = $dataRequestStore['date_work'] . ' ' . $dataRequestStore['etime'];
                $requestAbsenceNew['Reason'] = $dataRequestStore['content'];
                $requestAbsenceNew['Remark'] = $dataRequestStore['note'];
                $requestAbsenceNew['RequestManager'] = ['52'];
                $checkNull = 0;

                if ($modeIsUpdate == false) {

                    foreach (explode(',', $one->AssignID) as $item) {
                        if ($item != "") {
                            //Kiểm tra đơn vắng mặt tồn tại
                            $check = DB::table('absences')
                                ->where('UID', $item)
                                ->where(function ($query) use ($requestAbsenceNew) {
                                    $query->orWhere(function ($query) use ($requestAbsenceNew) {
                                        $query->orWhereBetween('SDate', array(\DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->SDate)->format('Y-m-d H:i'), \DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->EDate)->format('Y-m-d H:i')));
                                        $query->orWhereBetween('EDate', array(\DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->SDate)->format('Y-m-d H:i'), \DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->EDate)->format('Y-m-d H:i')));
                                    });
                                    $query->orWhere(function ($query) use ($requestAbsenceNew) {
                                        $query->where('SDate', '<=', \DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->SDate)->format('Y-m-d H:i'));
                                        $query->where('EDate', '>=', \DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->EDate)->format('Y-m-d H:i'));
                                    });
                                })
                                ->whereNull('deleted_at');
                            $check = $check->first();
                            if ($check) {
                                $FullNameOfAbsenceUser = User::query()->where('id', '=', $item)->whereNull('deleted_at')->first();

                                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                                    return AdminController::responseApi(422, 'Thành viên ' . $FullNameOfAbsenceUser['FullName'] . ' có đơn vắng mặt trong khoảng thời gian này.Vui lòng kiểm tra lại!');
                                }
                                return $this->jsonErrors('Thành viên ' . $FullNameOfAbsenceUser['FullName'] . ' có đơn vắng mặt trong khoảng thời gian này.Vui lòng kiểm tra lại!');
                            }
                            //Kiểm tra trùng lịch công tác
                            $checkExistWS = DB::table('working_schedule')
                                ->where('Date', '=', $one->Date)
                                ->where('AssignID', 'like', '%,' .  $item . ',%')
                                ->where(function ($query) use ($requestAbsenceNew) {
                                    $query->orWhere(function ($query) use ($requestAbsenceNew) {
                                        $query->orWhereBetween('STime', array(\DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->SDate)->format('H:i:s'), \DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->EDate)->format('H:i:s')));
                                        $query->orWhereBetween('ETime', array(\DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->SDate)->format('H:i:s'), \DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->EDate)->format('H:i:s')));
                                    });
                                    $query->orWhere(function ($query) use ($requestAbsenceNew) {
                                        $query->where('STime', '<=', \DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->SDate)->format('H:i:s'));
                                        $query->where('ETime', '>=', \DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->EDate)->format('H:i:s'));
                                    });
                                });
                            $checkExistWS = $checkExistWS->first();
                            if ($checkExistWS) {
                                $FullNameOfAbsenceUser = User::query()->where('id', '=', $item)->whereNull('deleted_at')->first();

                                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                                    return AdminController::responseApi(422, 'Thành viên ' . $FullNameOfAbsenceUser['FullName'] . ' có lịch công tác trong khoảng thời gian này.Vui lòng kiểm tra lại!');
                                }
                                return $this->jsonErrors('Thành viên ' . $FullNameOfAbsenceUser['FullName'] . ' có lịch công tác trong khoảng thời gian này.Vui lòng kiểm tra lại!');
                            }
                        } else {
                            $checkNull = $checkNull + 1;
                        }
                    }
                } else {
                    foreach (explode(',', $one->AssignID) as $item) {
                        if ($item != "") {
                            //Kiểm tra đơn vắng mặt tồn tại
                            $check = DB::table('absences')
                                ->where(function ($query) use ($one) {
                                    $query->where('IdWS', '!=', $one->id)
                                        ->orWhere('IdWS', null);
                                })
                                ->where('UID', $item)
                                ->where(function ($query) use ($requestAbsenceNew) {
                                    $query->orWhere(function ($query) use ($requestAbsenceNew) {
                                        $query->orWhereBetween('SDate', array(\DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->SDate)->format('Y-m-d H:i'), \DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->EDate)->format('Y-m-d H:i')));
                                        $query->orWhereBetween('EDate', array(\DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->SDate)->format('Y-m-d H:i'), \DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->EDate)->format('Y-m-d H:i')));
                                    });
                                    $query->orWhere(function ($query) use ($requestAbsenceNew) {
                                        $query->where('SDate', '<=', \DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->SDate)->format('Y-m-d H:i'));
                                        $query->where('EDate', '>=', \DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->EDate)->format('Y-m-d H:i'));
                                    });
                                })
                                ->whereNull('deleted_at');
                            $check = $check->first();
                            if ($check) {
                                $FullNameOfAbsenceUser = User::query()->where('id', '=', $item)->whereNull('deleted_at')->first();
                                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                                    return AdminController::responseApi(422, 'Thành viên ' . $FullNameOfAbsenceUser['FullName'] . ' có đơn vắng mặt trong khoảng thời gian này.Vui lòng kiểm tra lại!');
                                }
                                return $this->jsonErrors('Thành viên ' . $FullNameOfAbsenceUser['FullName'] . ' có đơn vắng mặt trong khoảng thời gian này. Vui lòng kiểm tra lại!');
                            }
                            //Kiểm tra trùng lịch công tác
                            $checkExistWS = DB::table('working_schedule')
                                ->where('id', '!=', $one->id)
                                ->where('Date', '=', $one->Date)
                                ->where(function ($query) use ($item) {
                                    $query->where('AssignID', 'like', '%,' .  $item . ',%')
                                        ->orWhere('AssignID', '=', "0");
                                })
                                ->where(function ($query) use ($requestAbsenceNew) {
                                    $query->orWhere(function ($query) use ($requestAbsenceNew) {
                                        $query->orWhereBetween('STime', array(\DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->SDate)->format('H:i:s'), \DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->EDate)->format('H:i:s')));
                                        $query->orWhereBetween('ETime', array(\DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->SDate)->format('H:i:s'), \DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->EDate)->format('H:i:s')));
                                    });
                                    $query->orWhere(function ($query) use ($requestAbsenceNew) {
                                        $query->where('STime', '<=', \DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->SDate)->format('H:i:s'));
                                        $query->where('ETime', '>=', \DateTime::createFromFormat('d/m/Y H:i', $requestAbsenceNew->EDate)->format('H:i:s'));
                                    });
                                });
                            $checkExistWS = $checkExistWS->first();
                            if ($checkExistWS) {
                                $FullNameOfAbsenceUser = User::query()->where('id', '=', $item)->whereNull('deleted_at')->first();
                                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                                    return AdminController::responseApi(422, 'Thành viên ' . $FullNameOfAbsenceUser['FullName'] . ' có lịch công tác trong khoảng thời gian này.Vui lòng kiểm tra lại!');
                                }
                                return $this->jsonErrors('Thành viên ' . $FullNameOfAbsenceUser['FullName'] . ' có lịch công tác trong khoảng thời gian này.Vui lòng kiểm tra lại!');
                            }
                        } else {

                            $checkNull = $checkNull + 1;
                        }
                    }
                }

                if ($checkNull == count(explode(',', $one->AssignID))) {

                    if (strpos(\Request::getRequestUri(), 'api') !== false) {
                        return AdminController::responseApi(422, 'Nhóm chức vụ lựa chọn chưa tồn tại nhân viên!');
                    }
                    return $this->jsonErrors('Nhóm chức vụ lựa chọn chưa tồn tại nhân viên!');
                }
            } else if ($one->in_out == 0) {

                $checkNull = 0;
                $requestNew['SDate'] = $dataRequestStore['date_work'] . ' ' . $dataRequestStore['stime'];
                $requestNew['EDate'] = $dataRequestStore['date_work'] . ' ' . $dataRequestStore['etime'];
                $SDateCheck = $dataRequestStore['date_work'] . ' ' . $dataRequestStore['stime'];
                $EDateCheck = $dataRequestStore['date_work'] . ' ' . $dataRequestStore['etime'];
                if ($one->AssignID != "0") {
                    foreach (explode(',', $one->AssignID) as $item) {
                        if ($item != "") {
                            //Kiểm tra đơn vắng mặt tồn tại
                            $check = DB::table('absences')
                                ->where(function ($query) use ($one) {
                                    $query->where('IdWS', '!=', $one->id)
                                        ->orWhere('IdWS', null);
                                })
                                ->where('UID', $item)
                                ->where(function ($query) use ($SDateCheck, $EDateCheck) {
                                    $query->orWhere(function ($query) use ($SDateCheck, $EDateCheck) {
                                        $query->orWhereBetween('SDate', array(\DateTime::createFromFormat('d/m/Y H:i', $SDateCheck)->format('Y-m-d H:i'), \DateTime::createFromFormat('d/m/Y H:i', $EDateCheck)->format('Y-m-d H:i')));
                                        $query->orWhereBetween('EDate', array(\DateTime::createFromFormat('d/m/Y H:i', $SDateCheck)->format('Y-m-d H:i'), \DateTime::createFromFormat('d/m/Y H:i', $EDateCheck)->format('Y-m-d H:i')));
                                    });
                                    $query->orWhere(function ($query) use ($SDateCheck, $EDateCheck) {
                                        $query->where('SDate', '<=', \DateTime::createFromFormat('d/m/Y H:i', $SDateCheck)->format('Y-m-d H:i'));
                                        $query->where('EDate', '>=', \DateTime::createFromFormat('d/m/Y H:i', $EDateCheck)->format('Y-m-d H:i'));
                                    });
                                })
                                ->whereNull('deleted_at');
                            $check = $check->first();
                            if ($check) {
                                $FullNameOfAbsenceUser = User::query()->where('id', '=', $item)->whereNull('deleted_at')->first();
                                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                                    return AdminController::responseApi(422, 'Thành viên ' . $FullNameOfAbsenceUser['FullName'] . ' có đơn vắng mặt trong khoảng thời gian này.Vui lòng kiểm tra lại!');
                                }
                                return $this->jsonErrors('Thành viên ' . $FullNameOfAbsenceUser['FullName'] . ' có đơn vắng mặt trong khoảng thời gian này.Vui lòng kiểm tra lại!');
                            }
                            $checkExistWS = DB::table('working_schedule');
                            $one->id && $checkExistWS = $checkExistWS->where('id', '!=', $one->id);
                            $checkExistWS = $checkExistWS->where('Date', '=', $one->Date)
                                ->where('AssignID', 'like', '%,' .  $item . ',%')
                                ->where(function ($query) use ($requestNew) {
                                    $query->orWhere(function ($query) use ($requestNew) {
                                        $query->orWhereBetween('STime', array(\DateTime::createFromFormat('d/m/Y H:i', $requestNew['SDate'])->format('H:i:s'), \DateTime::createFromFormat('d/m/Y H:i', $requestNew['EDate'])->format('H:i:s')));
                                        $query->orWhereBetween('ETime', array(\DateTime::createFromFormat('d/m/Y H:i', $requestNew['SDate'])->format('H:i:s'), \DateTime::createFromFormat('d/m/Y H:i', $requestNew['EDate'])->format('H:i:s')));
                                    });
                                    $query->orWhere(function ($query) use ($requestNew) {
                                        $query->where('STime', '<=', \DateTime::createFromFormat('d/m/Y H:i', $requestNew['SDate'])->format('H:i:s'));
                                        $query->where('ETime', '>=', \DateTime::createFromFormat('d/m/Y H:i', $requestNew['EDate'])->format('H:i:s'));
                                    });
                                });
                            $checkExistWS = $checkExistWS->first();
                            if ($checkExistWS) {
                                $FullNameOfAbsenceUser = User::query()->where('id', '=', $item)->whereNull('deleted_at')->first();
                                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                                    return AdminController::responseApi(422, 'Thành viên ' . $FullNameOfAbsenceUser['FullName'] . ' có lịch công tác trong khoảng thời gian này.Vui lòng kiểm tra lại!');
                                }
                                return $this->jsonErrors('Thành viên ' . $FullNameOfAbsenceUser['FullName'] . ' có lịch công tác trong khoảng thời gian này.Vui lòng kiểm tra lại!');
                            }
                        } else {
                            $checkNull = $checkNull + 1;
                        }
                        if ($checkNull == count(explode(',', $one->AssignID))) {
                            if (strpos(\Request::getRequestUri(), 'api') !== false) {
                                return AdminController::responseApi(422, 'Nhóm chức vụ lựa chọn chưa tồn tại nhân viên!');
                            }
                            return $this->jsonErrors('Nhóm chức vụ lựa chọn chưa tồn tại nhân viên!');
                        }
                    }
                } else {
                    $checkExistWS = DB::table('working_schedule');
                    $one->id && $checkExistWS = $checkExistWS->where('id', '!=', $one->id);
                    $checkExistWS = $checkExistWS->where('Date', '=', $one->Date)
                        ->where(function ($query) use ($requestNew) {
                            $query->orWhere(function ($query) use ($requestNew) {
                                $query->orWhereBetween('STime', array(\DateTime::createFromFormat('d/m/Y H:i', $requestNew['SDate'])->format('H:i:s'), \DateTime::createFromFormat('d/m/Y H:i', $requestNew['EDate'])->format('H:i:s')));
                                $query->orWhereBetween('ETime', array(\DateTime::createFromFormat('d/m/Y H:i', $requestNew['SDate'])->format('H:i:s'), \DateTime::createFromFormat('d/m/Y H:i', $requestNew['EDate'])->format('H:i:s')));
                            });
                            $query->orWhere(function ($query) use ($requestNew) {
                                $query->where('STime', '<=', \DateTime::createFromFormat('d/m/Y H:i', $requestNew['SDate'])->format('H:i:s'));
                                $query->where('ETime', '>=', \DateTime::createFromFormat('d/m/Y H:i', $requestNew['EDate'])->format('H:i:s'));
                            });
                        });
                    $checkExistWS = $checkExistWS->first();
                    if ($checkExistWS) {
                        if (strpos(\Request::getRequestUri(), 'api') !== false) {
                            return AdminController::responseApi(422, 'Tồn tại thành viên có lịch công tác trong khoảng thời gian này.Vui lòng kiểm tra lại!');
                        }
                        return $this->jsonErrors('Tồn tại thành viên có lịch công tác trong khoảng thời gian này.Vui lòng kiểm tra lại!');
                    }
                }
                //check trung phong hop
                $checkExistRoomWS = DB::table('working_schedule');
                $one->id && $checkExistRoomWS = $checkExistRoomWS->where('id', '!=', $one->id);
                $checkExistRoomWS = $checkExistRoomWS
                    ->where('Date', '=', $one->Date)
                    ->where('RoomsID', '=', $one->roomsID)
                    ->where(function ($query) use ($requestNew) {
                        $query->orWhere(function ($query) use ($requestNew) {
                            $query->orWhereBetween('STime', array(\DateTime::createFromFormat('d/m/Y H:i', $requestNew['SDate'])->format('H:i:s'), \DateTime::createFromFormat('d/m/Y H:i', $requestNew['EDate'])->format('H:i:s')));
                            $query->orWhereBetween('ETime', array(\DateTime::createFromFormat('d/m/Y H:i', $requestNew['SDate'])->format('H:i:s'), \DateTime::createFromFormat('d/m/Y H:i', $requestNew['EDate'])->format('H:i:s')));
                        });
                        $query->orWhere(function ($query) use ($requestNew) {
                            $query->where('STime', '<=', \DateTime::createFromFormat('d/m/Y H:i', $requestNew['SDate'])->format('H:i:s'));
                            $query->where('ETime', '>=', \DateTime::createFromFormat('d/m/Y H:i', $requestNew['EDate'])->format('H:i:s'));
                        });
                    });
                $checkExistRoomWS = $checkExistRoomWS->first();
                if ($checkExistRoomWS) {
                    $RoomName = Room::query()->where('id', '=', $one->roomsID)->whereNull('deleted_at')->first();
                    if (strpos(\Request::getRequestUri(), 'api') !== false) {
                        return AdminController::responseApi(422, 'Phòng ' . $RoomName['Name'] . ' đã có cuộc họp trong thời gian này. Vui lòng chọn phòng khác!');
                    }
                    return $this->jsonErrors('Phòng ' . $RoomName['Name'] . ' đã có cuộc họp trong thời gian này. Vui lòng chọn phòng khác!');
                }
            }
            $one->save();

            if ($one->in_out == 1 && $one->id) {

                if ($modeIsUpdate == false) {
                    foreach (explode(',', $one->AssignID) as $item) {
                        if ($item != "") {
                            //Lưu
                            $requestDailyReportNew['reqID'] = $item;
                            $requestAbsenceNew['UID'] = $item;
                            $requestDailyReportNew['IdWS'] = $one->id;
                            $requestAbsenceNew['IdWS'] = $one->id;
                            DailyReportController::storeStatic($requestDailyReportNew);
                            AbsenceController::storeStatic($requestAbsenceNew);
                        }
                    }
                } else {

                    $arrayListUserRemove = [];
                    $arrayListUserRemoveId = [];
                    $arrayListUserRemoveDailyReport = [];
                    $arrayListUserAbsence = [];
                    $arrayListUserDailyReport = [];
                    $listAbsenceUserWS = Absence::query()->where('IdWS', $one->id)->get();
                    $listDailyReportUserWS = DailyReport::query()->where('IdWS', $one->id)->get();
                    foreach ($listAbsenceUserWS as $item1) {
                        if (!in_array(strval($item1['UID']), explode(',', $one->AssignID))) {
                            array_push($arrayListUserRemove, $item1['id']);
                            array_push($arrayListUserRemoveId, $item1['UID']);
                        } else {
                            $arrayListUserAbsence[$item1['UID']] = $item1['id'];
                        }
                    }
                    foreach ($listDailyReportUserWS as $item2) {
                        if (!in_array(strval($item2['UserID']), explode(',', $one->AssignID))) {
                            array_push($arrayListUserRemoveDailyReport, $item2['id']);
                        } else {
                            $arrayListUserDailyReport[$item2['UserID']] = $item2['id'];
                        }
                    }

                    //xoa bao cao hang ngay nhung nguoi khong duoc chon
                    foreach ($arrayListUserRemoveDailyReport as $itemDelete) {
                        $listDailyReportUserWSDelete = DailyReport::query()->where('id', $itemDelete)->delete();
                    }
                    //xoa lich vang mat cua nhung nguoi khong duoc chon
                    foreach ($arrayListUserRemove as $itemDelete) {
                        $listAbsenceUserWSDelete = Absence::query()->where('id', $itemDelete)->delete();
                    }
                    //Thong bao den nguoi bi xoa khoi danh sach cong tac

                    $arrToken = DB::table('push_token')->whereIn('UserID', $arrayListUserRemoveId)->whereNull('deleted_at')->where('allow_push', 1)->pluck('token_push')->toArray();
                    if (count($arrToken) > 0) {
                        $sendData = [];
                        $sendData['id'] = $id;
                        $sendData['data'] = "LCT";
                        $headrmess = "Lịch công tác của bạn đã được cập nhật";
                        $bodyNoti = "Ngày:" . Carbon::parse($one->Date)->format('d/m/Y') . " từ " . Carbon::parse($one->STime)->format('H:i') . ' đến ' . Carbon::parse($one->ETime)->format('H:i');
                        NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
                    }

                    foreach (explode(',', $one->AssignID) as $item) {
                        if ($item != "") {

                            //Lưu
                            $idCheck = isset($arrayListUserAbsence[strval($item)]);
                            $idCheckDailyReport = isset($arrayListUserDailyReport[strval($item)]);
                            $requestDailyReportNew['reqID'] = $item;
                            $requestDailyReportNew['UserID'] = $item;
                            $requestAbsenceNew['UID'] = $item;
                            $requestDailyReportNew['id'] = $idCheckDailyReport ? [$arrayListUserDailyReport[strval($item)]] : [];
                            $requestAbsenceNew['id'] = $idCheck ? $arrayListUserAbsence[strval($item)] : null;
                            $requestDailyReportNew['IdWS'] = $one->id;
                            $requestAbsenceNew['IdWS'] = $one->id;
                            DailyReportController::storeStatic($requestDailyReportNew);
                            AbsenceController::storeStatic($requestAbsenceNew);
                        }
                    }
                }
            }

            if ($one->in_out == 0 && $one->id && $modeIsUpdate) {

                $arrayListUserRemoveId = [];
                $arrayListUserRemove = [];
                $arrayListUserRemoveDailyReport = [];
                $arrayListUserAbsence = [];
                $arrayListUserDailyReport = [];

                $listAbsenceUserWS = Absence::query()->where('IdWS', $one->id)->get();
                $listDailyReportUserWS = DailyReport::query()->where('IdWS', $one->id)->get();

                foreach ($listAbsenceUserWS as $item1) {
                    array_push($arrayListUserRemove, $item1['id']);
                }
                foreach ($listDailyReportUserWS as $item2) {
                    array_push($arrayListUserRemoveDailyReport, $item2['id']);
                }

                //xoa bao cao hang ngay nhung nguoi khong duoc chon
                foreach ($arrayListUserRemoveDailyReport as $itemDelete) {
                    $listDailyReportUserWSDelete = DailyReport::query()->where('id', $itemDelete)->delete();
                }
                //xoa lich vang mat cua nhung nguoi khong duoc chon
                foreach ($arrayListUserRemove as $itemDelete) {
                    $listAbsenceUserWSDelete = Absence::query()->where('id', $itemDelete)->delete();
                }
                if (($request->input())['AssignIDOld']) {
                    foreach (explode(',', ($request->input())['AssignIDOld']) as $item3) {
                        if (!in_array($item3, explode(',', $one->AssignID))) {
                            if ($item3 != "") {
                                array_push($arrayListUserRemoveId, $item3);
                            }
                        }
                    }
                }
                //Thong bao den nguoi bi xoa khoi danh sach cong tac
                $arrToken = DB::table('push_token')->whereIn('UserID', $arrayListUserRemoveId)->whereNull('deleted_at')->where('allow_push', 1)->pluck('token_push')->toArray();
                if (count($arrToken) > 0) {
                    $sendData = [];
                    $sendData['id'] = $id;
                    $sendData['data'] = "LCT";
                    $headrmess = "Lịch công tác của bạn đã được cập nhật";
                    $bodyNoti = "Ngày:" . Carbon::parse($one->Date)->format('d/m/Y') . " từ " . Carbon::parse($one->STime)->format('H:i') . ' đến ' . Carbon::parse($one->ETime)->format('H:i');
                    NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
                }
            }
            $dataIOT = [];


            if (isset($one->id) && $one->id != null && $request->rooms != null && $request->minute != null) {

                $timeCarbon = Carbon::parse($request->stime)->subMinutes($request->get("minute"))->format("H:i");
                $timerIOT = explode(":", $timeCarbon);
                $date_work = explode("-", $request->date_work);

                $dataIOT['room_id'] = $request->rooms;
                $dataIOT['hour'] = $timerIOT[0];
                $dataIOT['minute'] = $timerIOT[1];
                $dataIOT['day'] = $date_work[2];
                $dataIOT['month'] = $date_work[1];
                $dataIOT['year'] = $date_work[0];
                $dataIOT['meeting_id'] = $one->id;

                try {
                    $client = new \GuzzleHttp\Client();
                    $guzzleResult = $client->post(
                        $this->urlAnFa,
                        [
                            'verify' => false,
                            'json' => $dataIOT
                        ]
                    );
                } catch (\GuzzleHttp\Exception\RequestException $e) {
                    // dd('catch', $e);
                }
            }

            if (!$one) {
                return $this->jsonErrors(__('admin.error.save'));
            } else {

                $this->sendMail($request, $one);
                //firebase notification
                if ($one->AssignID[0] === "0") {
                    $arrToken = DB::table('push_token')->whereNull('deleted_at')->where('allow_push', 1)->pluck('token_push')->toArray();
                } else {
                    $arrToken = DB::table('push_token')->whereIn('UserID', explode(',', $one->AssignID))->whereNull('deleted_at')->where('allow_push', 1)->pluck('token_push')->toArray();
                }

                if (count($arrToken) > 0) {
                    $sendData = [];
                    $sendData['id'] = $one->id;
                    $sendData['data'] = "LCT";
                    if ($request['id'] === null) {
                        $headrmess = "Bạn có lịch công tác mới";
                    } else {
                        $headrmess = "Lịch công tác của bạn đã được cập nhật";
                    }

                    $bodyNoti = "Ngày:" . $request['date_work'] . " từ " . $request['stime'] . ' đến ' . $request['etime'];

                    NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
                }

                return $dataIOT;
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function storeAPIWorkingShecdule(WorkingScheduleRequest $request, $id = null)
    {
        if (count(array($request->input())) === 0) {
            return abort('404');
        }

        if (count(array($request->get("ProjectID"))) != 0) {
            $projectID = $request->get("ProjectID")[0];
        } else {
            $projectID = null;
        }

        try {
            $data = [
                'date_work' => 'Date',
                'stime' => 'STime',
                'etime' => 'ETime',
                'content' => 'Content',
                'note' => 'Note',
                'address' => 'Address'
            ];

            // Check valid vacation time
            $date = $this->fncDateTimeConvertFomat($request->date_work, 'd/m/Y', 'Y-m-d');
            if ($this->checkHoliday(Carbon::parse($date))) {
                return $this->jsonErrors(__('admin.error.date-holiday'));
            }

            if (Carbon::parse($request->etime)->diffInMinutes(Carbon::parse($request->stime)) < 5) {
                return $this->jsonErrors('Thời gian công tác tối thiểu là 5 phút');
            }

            // Check valid rooms
            if ($request['rooms'] === null && $request->gender == 0) {
                return $this->jsonErrors('Vui lòng chọn phòng');
            }

            $modeIsUpdate = array_key_exists('id', $request->input());
            $one = !$modeIsUpdate ? new WorkingSchedule() : WorkingSchedule::find($request->id);
            foreach ($data as $key => $value) {
                if (isset($request->$key) && $request->$key != '') {
                    if ($key == 'date_work') {
                        $request->$key = $this->fncDateTimeConvertFomat($request->$key, 'd/m/Y', 'Y-m-d');
                    }
                    $one->$value = $request->$key;
                } else {
                    $one->$value = null;
                }
            }

            $one->UserID = !$modeIsUpdate ? Auth::user()->id : $one->UserID;
            $one->AssignID = $request->assign_id[0];

            $one->ProjectID = $projectID;

            $one->in_out = $request->get("gender");
            $one->roomsID = $request->get("rooms");
            $one->minuteRoom = $request->get("minute");
            $one->AssignID = $one->AssignID == 0 ? $one->AssignID : ',' . $one->AssignID . ',';
            $one->save();

            if (isset($one->id)) {
                //firebase notification
                if ($request->assign_id[0] === "0") {
                    $arrToken = DB::table('push_token')->whereNull('deleted_at')->where('allow_push', 1)->pluck('token_push')->toArray();
                } else {
                    $arrToken = DB::table('push_token')->whereIn('UserID', $request['assign_id'])->whereNull('deleted_at')->where('allow_push', 1)->pluck('token_push')->toArray();
                }

                if (count($arrToken) > 0) {
                    $sendData = [];
                    $sendData['id'] = $one->id;
                    $sendData['data'] = "LCT";
                    if ($request['id'] === null) {
                        $headrmess = "Bạn có lịch công tác mới";
                    } else {
                        $headrmess = "Bạn có lịch công tác cập nhật";
                    }
                    $bodyNoti = "Ngày:" . $request['date_work'] . " từ " . $request['stime'] . ' đến ' . $request['etime'];
                    NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
                }
            }

            // call api dat lich dien phong hop
            if (isset($one->id) && $one->id != null && $request->get("gender") === "0" && $request->minute != null) {
                $dataIOT = [];

                $timeCarbon = Carbon::parse($request->stime)->subMinutes($request->get("minute"))->format("H:i");
                $timerIOT = explode(":", $timeCarbon);
                $date_work = explode("-", $request->date_work);
                $dataIOT['room_id'] = $request->get('rooms');
                $dataIOT['hour'] = $timerIOT[0];
                $dataIOT['minute'] = $timerIOT[1];

                $dataIOT['day'] = $date_work[2];
                $dataIOT['month'] = $date_work[1];
                $dataIOT['year'] = $date_work[0];
                $dataIOT['meeting_id'] = $one->id;

                try {
                    $client = new \GuzzleHttp\Client();
                    $guzzleResult = $client->post(
                        $this->urlAnFa,
                        [
                            'verify' => false,
                            'json' => $dataIOT
                        ]
                    );
                } catch (\GuzzleHttp\Exception\RequestException $e) {
                    $guzzleResult = $e->getResponse();
                }
            }

            if ($request->get("gender") === "1") {
                //Save Absence
                $arayUID = $request->input();
                foreach ($arayUID['RequestUID'] as $keyUID => $valueUID) {
                    # code...
                    $roomIdToWorking = DB::table('users')->where('id', $valueUID)->value('RoomId');
                    $oneAbsence = isset($id) && $id != null ? Absence::find($id) : new Absence();
                    foreach ($request->all() as $key => $value) {
                        if (Schema::hasColumn('absences', $key)) {
                            if ($key == 'SDate') {
                                $value = $this->fncDateTimeConvertFomat($value, 'd/m/Y H:i', 'Y-m-d H:i');
                            }
                            if ($key == 'EDate') {
                                $value = $this->fncDateTimeConvertFomat($value, 'd/m/Y H:i', 'Y-m-d H:i');
                            }
                            $oneAbsence->$key = $value;
                        }
                    }

                    if (Carbon::parse($oneAbsence->SDate)->gt(Carbon::parse($oneAbsence->EDate))) {
                        return ['error' => __('admin.error.absence.date'), 'status' => 422];
                    }

                    $oneAbsence->TotalTimeOff = $this->getDiffHours(Carbon::parse($oneAbsence->SDate), Carbon::parse($oneAbsence->EDate), $request->UID) * 60;

                    // nếu xin nghỉ vào cuối tuần
                    if ($this->checkHoliday(Carbon::parse($oneAbsence->SDate)) && $this->checkHoliday(Carbon::parse($oneAbsence->EDate))) {
                        return ['error' => __('admin.error.date-holiday'), 'status' => 422];
                    }
                    if ($oneAbsence->TotalTimeOff < 5) {
                        return ['error' => __('admin.error.date-range'), 'status' => 422];
                    }
                    $check = DB::table('absences')
                        ->where('UID', $valueUID)
                        ->where(function ($query) use ($oneAbsence) {
                            $query->orWhere(function ($query) use ($oneAbsence) {
                                $query->orWhereBetween('SDate', array($oneAbsence->SDate, $oneAbsence->EDate));
                                $query->orWhereBetween('EDate', array($oneAbsence->SDate, $oneAbsence->EDate));
                            });
                            $query->orWhere(function ($query) use ($oneAbsence) {
                                $query->where('SDate', '<=', $oneAbsence->SDate);
                                $query->where('EDate', '>=', $oneAbsence->EDate);
                            });
                        })
                        ->whereNull('deleted_at');
                    if (array_key_exists('id', $request->input())) {
                        $check = $check->where('id', '!=', $request->id);
                    }
                    $check = $check->first();
                    if ($check) {
                        return ['error' => __('admin.error.absence.isset'), 'status' => 422];
                    }
                    $oneAbsence->RoomID = $roomIdToWorking;
                    $oneAbsence->UID = $valueUID;
                    $oneAbsence->RequestManager = ',' . implode(',', $request->RequestManager) . ',';
                    $oneAbsence->AbsentDate = Carbon::now()->format('Y-m-d');
                    $oneAbsence->save();
                }

                if (!$oneAbsence) {
                    return ['error' => __('admin.error.save'), 'status' => 403];
                } else {
                    foreach ($arayUID['RequestUID'] as $keyUID => $valueUID) {
                        $roomIdToWorking = DB::table('users')->where('id', $valueUID)->value('RoomId');
                        $sendMail = array();
                        $sendMail['RoomID'] = $roomIdToWorking;
                        $sendMail['UID'] = $valueUID;
                        $sendMail['MasterDataValue'] = $request->MasterDataValue;
                        $sendMail['SDate'] = $request->SDate;
                        $sendMail['EDate'] = $request->EDate;
                        $sendMail['Reason'] = $request->Reason;
                        $sendMail['Remark'] = $request->Remark;
                        $sendMail['RequestManager'] = $request->RequestManager;
                        $this->sendMail($sendMail, 'Kính gửi Ban Giám đốc');
                    }
                    return ['success' => __('admin.success.save'), 'status' => 200];
                }


                // Save DailyReport
                foreach ($arayUID['RequestUID'] as $keyUID => $valueUID) {
                    if (count($request->input()) > 0) {
                        $oneDailyReport = new DailyReport();
                        $oneDailyReport->Date = $this->fncDateTimeConvertFomat($request->Date, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                        $oneDailyReport->DateCreate = date(now());
                        $oneDailyReport->ProjectID = $request->ProjectID;
                        $oneDailyReport->ScreenName = $request->ScreenName;
                        $oneDailyReport->TypeWork = $request->TypeWork;
                        $oneDailyReport->Contents = $request->Contents;
                        $oneDailyReport->WorkingTime = $request['WorkingTime'];
                        $oneDailyReport->Progressing = 100;

                        $oneDailyReport->Note = $request->Note;
                        if (isset($valueUID)) {
                            $oneDailyReport->UserID = $valueUID;
                        }
                        if ($this->StringIsNullOrEmpty($oneDailyReport->UserID)) {
                            $oneDailyReport->UserID = Auth::user()->id;
                        }
                        $oneDailyReport->save();
                    }
                }
                // return AdminController::responseApi(200, null, __('admin.success.save'));
            }


            if (!$one) {
                return $this->jsonErrors(__('admin.error.save'));
            } else {
                if (Carbon::parse($date) >= Carbon::now()->format('Y-m-d')) {
                    $this->sendMail($request, $one);
                }

                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                    return response()->json(['success' => 'Lưu thành công.']);
                }

                return $this->jsonSuccessWithRouter('admin.WorkingSchedule');
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $request
     * @param $orderBy
     * @param $sortBy
     * @param null $export
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder|\Illuminate\Database\Query\Builder[]|\Illuminate\Http\RedirectResponse|\Illuminate\Support\Collection
     * @throws \Exception
     */
    public function getListWithRequest($request, $orderBy, $sortBy, $export = null)
    {
        //Get list absence
        if (Schema::hasColumn('working_schedule', $orderBy)) {
            $working_schedule = WorkingSchedule::query()
                ->select('working_schedule.*', 'users.FullName')
                ->leftJoin('users', 'working_schedule.UserID', '=', 'users.id')
                ->orderBy($orderBy, $sortBy);
        } else {
            return redirect()->back();
        }

        $this->data['request'] = $request->query();
        // Search in columns

        $one = WorkingSchedule::query()->select(
            'working_schedule.Date',
            'working_schedule.Content',
            'working_schedule.Address',
            'working_schedule.Note',
            'working_schedule.UserID',
            'users.FullName'
        )
            ->leftJoin('users', 'working_schedule.UserID', '=', 'users.id')->first();

        if ($one) {
            $one = $one->toArray();
            if (array_key_exists('search', $request->input())) {
                $working_schedule = $working_schedule->where(function ($query) use ($one, $request) {

                    foreach ($one as $key => $value) {
                        if ($key == 'FullName') {
                            $query->orWhere('users.' . $key, 'like', '%' . $request->input('search') . '%');
                        } elseif ($key == 'Date') {
                            $query->orWhereRaw('(DATE_FORMAT(working_schedule.' . $key . ',"%d/%m/%Y")) LIKE ?', '%' . $request->input('search') . '%');
                        } else {
                            $strSearch = trim($this->convert_vi_to_en($request->input('search')));
                            $query->orWhere('working_schedule.' . $key, 'like', '%' . $strSearch . '%');
                        }
                    }
                });
            }
        }

        $start_month = Carbon::now()->startOfMonth()->format('Y-m-d');
        if ($request['Date']) {
            $start_month = $this->fncDateTimeConvertFomat($request['Date'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
        }
        $working_schedule = $working_schedule->where('Date', '>=', $start_month);
        if (isset($request['timeWorking']) && $request['timeWorking'] != '') {
            $date = ($request['Date'] ? Carbon::parse(str_replace('/', '-', $request['Date'])) : Carbon::now())->format('Y-m-d');
            if ($request['timeWorking'] == 1) {
                $working_schedule = $working_schedule->whereDate('Date', '=', $date);
            } elseif ($request['timeWorking'] == 2) {
                $start_week = Carbon::parse($date)->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
                $end_week = Carbon::parse($date)->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
                $working_schedule = $working_schedule->whereDate('Date', '>=', $start_week)
                    ->whereDate('Date', '<=', $end_week);
            } else {
                $start_month = Carbon::parse($date)->startOfMonth()->format('Y-m-d');
                $end_month = Carbon::parse($date)->endOfMonth()->format('Y-m-d');
                $working_schedule = $working_schedule->whereDate('Date', '>=', $start_month)
                    ->whereDate('Date', '<=', $end_month);
            }
        }
        if ($request['dataValue'] && $request['UID']) {
            $listUserPosition = ListPositionUser::query()->where('DataValue', '=', $request['dataValue'])->get();
            $two = [];
            if (count($listUserPosition) != 0) {
                foreach ($listUserPosition as $item) {
                    array_push($two, $item['UserId']);
                }
            }
            $user_id_select = $request['UID'];
            foreach ($user_id_select as $item) {
                array_push($two, $item);
            }
            $two = array_unique($two);
            $assign_id = implode(',', $two);
            $working_schedule = $working_schedule->where(function ($query) use ($item, $two) {
                foreach ($two as $item) {
                    if ($item && $item == $two[0]) {
                        $query->where('AssignID', '=', '0')->orWhere('AssignID', 'like', '%,' . $item . ',%');
                    } else {
                        $query->orWhere('AssignID', 'like', '%,' . $item . ',%');
                    }
                }
            });
        } else if ($request['dataValue']) {
            $listUserPosition = ListPositionUser::query()->where('DataValue', '=', $request['dataValue'])->get();
            if (count($listUserPosition) != 0) {
                $two = [];
                foreach ($listUserPosition as $item) {
                    array_push($two, $item['UserId']);
                }

                $assign_id = implode(',', $two);

                $working_schedule = $working_schedule->where(function ($query) use ($item, $two) {
                    foreach ($two as $item) {
                        if ($item && $item == $two[0]) {
                            $query->where('AssignID', '=', '0')->orWhere('AssignID', 'like', '%,' . $item . ',%');
                        } else {
                            $query->orWhere('AssignID', 'like', '%,' . $item . ',%');
                        }
                    }
                });
            }
        } else if ($request['UID'] && $request['UID'][0] != null) {
            $assign_id = $request['UID'];
            // $working_schedule = $working_schedule->where('AssignID', 'like', '%' . $assign_id . '%');
            $working_schedule = $working_schedule->where(function ($query) use ($assign_id) {
                foreach ($assign_id as $item) {
                    if ($item && $item == $assign_id[0]) {
                        $query->where('AssignID', '=', '0')->orWhere('AssignID', 'like', '%,' . $item . ',%');
                    } else {
                        $query->orWhere('AssignID', 'like', '%,' . $item . ',%');
                    }
                }
            });
        }

        if ($export != '' && $export != null) {
            return $working_schedule->get();
        }
        return $working_schedule;
    }

    public static function getListAssignUser($list_assign_id = null, $list_user = null, $export = false)
    {
        $list_assign_user = array();
        $list_assign_ids = explode(',', $list_assign_id);
        foreach ($list_assign_ids as $list_assign_id) {
            $user_fullname = User::withTrashed()->where('id', $list_assign_id)->first();
            if ($user_fullname) {
                $list_assign_user[] = $user_fullname->FullName;
            }
        }
        if ($list_assign_ids[0] == '0') {
            $assign_user = 'Tất cả nhân viên công ty';
        } else if (count($list_assign_user) < count($list_user)) {
            if ($export)
                $assign_user = implode(' <br> ', $list_assign_user);
            else {
                $assign_user = implode("", array_map(function ($user) {
                    return "<span class='td_user' style='display: block'>$user</span>";
                }, $list_assign_user));
            }
        } else {
            $assign_user = 'Tất cả nhân viên công ty';
        }
        return $assign_user;
    }
    public static function getEndTime($start_time = '', $time = '')
    {
        $end_time = '';
        if ($time == 1) {
            $end_time = Carbon::parse($start_time)->addDays(1);
        } elseif ($time == 2) {
            $end_time = Carbon::parse($start_time)->addDays(7);
        } elseif ($time == 3) {
            $end_time = Carbon::parse($start_time)->addDays(30);
        }
        return $end_time;
    }
    public function sendMail($request, $one, $delete = false)
    {
        $offerUser = User::findOrFail($one->UserID);
        $roomName = Room::find($offerUser['RoomId']);
        $arrMailAddressTo = array();
        if ($delete) {

            $subjectMail = 'TB cập nhật lịch công tác (' . Carbon::parse($one->Date)->format('d/m/Y') . ')';
            $contentMail = 'Kính gửi Ông/Bà <br/><br/>';
            $contentMail .= "<div style='display:block'>";
            $contentMail .= ' Lịch công tác của Ông/Bà trong thời gian ' . Carbon::parse($one->Date)->format('d/m/Y') . ' ' . $one->STime . ' - ' . $one->ETime . ' đã hủy.<br/>';
            $contentMail .= "</div><br/>";
            $contentMail .= "Xin chân thành cảm ơn.<br/>";
            $listAssignId = explode(',', $one->AssignID);
            if (isset($one->AssignID[0]) &&  $one->AssignID[0] == "0") {
                $arrMailAddressTo[] = "akber@akb.vn";
            } else {
                foreach ($listAssignId as $list_assign_id) {
                    $user_fullname = User::withTrashed()->where('id', $list_assign_id)->first();
                    if ($user_fullname && $user_fullname->email != null) {
                        $arrMailAddressTo[] = $user_fullname->email;
                    }
                }
            }
        } else {

            $modeIsUpdate = array_key_exists('id', $request->input());
            if ($modeIsUpdate) {
                $subjectMail = 'TB cập nhật lịch công tác vào ngày ' . Carbon::parse($one->Date)->format('d/m/Y') . '';
            } else {
                $subjectMail = 'TB lịch công tác vào ngày ' . Carbon::parse($one->Date)->format('d/m/Y') . '';
            }

            $contentMail = 'Kính gửi Ông/Bà <br/><br/>';
            $contentMail .= "<div style='display:block'>";

            if ($modeIsUpdate) {


                $contentMailBody = ' Lịch công tác đã được thay đổi.<br/>';
                $contentMailBody .= 'Thời gian: ' . Carbon::parse($one->Date)->format('d/m/Y') . ' ' . $one->STime . ' - ' . $one->ETime . ' <br/>';
                if ($one->in_out == 0) {
                    $namRooms = DB::table('rooms')->where('id', $one->roomsID)->first();
                    $contentMailBody .= 'Tại địa chỉ : ' . $namRooms->Name . ' <br/>';
                } else {
                    $contentMailBody .= 'Tại địa chỉ : ' . $one->Address . ' <br/>';
                }
                $contentMailBody .= ' Với nội dung: ' . $one->Content . ' <br/>';

                if (isset($one->Note) && $one->Note != '') {
                    $contentMailBody .= ' Ghi chú: ' . $one->Note . ' <br/>';
                }

                $contentMailBody .= '<br>Kính mong ông bà tham dự đúng giờ. <br/><br>';
                $contentMailBody .= "</div><br/>";
                $contentMailBody .= "Xin chân thành cảm ơn.<br/>";
            } else {
                $contentMailBody = 'Ông/Bà có lịch công tác <br/>';
                $contentMailBody .= 'Thời gian: ' . Carbon::parse($one->Date)->format('d/m/Y') . ' ' . $one->STime . ' - ' . $one->ETime . ' <br/>';
                if ($one->in_out == 0) {
                    $namRooms = DB::table('rooms')->where('id', $one->roomsID)->first();
                    $contentMailBody .= 'Tại địa chỉ : ' . $namRooms->Name . ' <br/>';
                } else {
                    $contentMailBody .= 'Tại địa chỉ : ' . $one->Address . ' <br/>';
                }
                $contentMailBody .= ' Với nội dung: ' . $one->Content . ' <br/>';

                if (isset($one->Note) && $one->Note != '') {
                    $contentMailBody .= 'Ghi chú:' . $one->Note . ' <br/>';
                }
                $contentMailBody .= '<br>Kính mong ông bà tham dự đúng giờ. <br/><br>';
                $contentMailBody .= "</div><br/>";
                $contentMailBody .= "Xin chân thành cảm ơn.<br/>";
            }

            $contentMail .= $contentMailBody;
            // dd($request);
            if ($one->AssignID[0] == "0") {

                $arrMailAddressTo[] = "akber@akb.vn";
            } else {
                $listAssignId = explode(',', $one->AssignID);
                foreach ($listAssignId as $list_assign_id) {
                    $user_fullname = User::find($list_assign_id);
                    if ($user_fullname && $user_fullname->email != null) {
                        $arrMailAddressTo[] = $user_fullname->email;
                    }
                }
            }
        }

        $arrMailCC = array();
        $mailCC = MasterData::query()->where('DataValue', '=', 'EM004')->first();
        if (isset($mailCC)) {
            $arrMailCC = explode(',', $mailCC->DataDescription);
        }
        $this->SendMailHtml($subjectMail, $contentMail, config('mail.from.address'), $offerUser['FullName'] . ' - ' . $roomName['Name'], $arrMailAddressTo, $arrMailCC);
    }
}
