<?php

namespace Modules\TaskRequest\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use App\Exports\WorkingScheduleExport;
use App\MasterData;
use App\Menu;
use App\Model\Absence;
use App\DailyReport;
use App\model\PushToken;
use App\RoleScreenDetail;
use App\RoleUserScreenDetailRelationship;
use App\Room;
use App\Project;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Requests\WorkingScheduleRequest;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

use Modules\TaskRequest\Entities\TaskRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class TaskRequestController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $app;

    const KEYMENU = array(
        "add" => "TaskRequestAdd",
        "view" => "TaskRequest",
        "edit" => "TaskRequestEdit",
        "delete" => "TaskRequestDelete",
        "app" => "ListApprove"
    );

    /**
     * AbsenceController constructor.
     * @param Request $request
     * Check role view, insert, update
     */
    public function __construct(Request $request)
    {
        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
        $array = $this->RoleView('TaskRequest', ['TaskRequest', 'TaskRequest']);
        $this->menu = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if ($value == $row->alias)
                    $this->$key = $row;
            }
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
    public function getListWithRequest(Request $request)
    {

    	$one = TaskRequest::query()->where(function ($query) {
            $query->where('isPrivate', 0)
                ->orWhere(function ($query) {
                    $query->where('isPrivate', 1)
                        ->where(function ($query) {
                            $query->where('requestUserID', auth()->id())
                                ->orWhere('receiveUserID', auth()->id());
                        });
                });
        })->where(function ($query) use ($request) {
            if (count($request->input()) > 0) {
                $query->where(function ($query) use ($request) {
                    foreach ($request->all() as $key => $value) {
                        if ($key == 'UserID' && $value != '') {
                            $query->where(function ($query) use ($value) {
                                $query->where('requestUserID', 'like', (int)$value)
                                    ->orWhere('receiveUserID', 'like', (int)$value);
                            });
                        }
                        if ($key == 'ProjectID' && $value) {
                            $query->where('projectID',$value);
                        }
                        if ($key == 'StartTime' && $value) {
                            $value = str_replace('/', '-', $value);
                            $query->whereDate('requestTime', '>=', Carbon::parse($value)->format('Y-m-d'));
                        }
                        if ($key == 'EndTime' && $value) {
                            $value = str_replace('/', '-', $value);
                            $query->whereDate('requestTime', '<=', Carbon::parse($value)->format('Y-m-d'));
                        }
                    }
                });
            } else {
                $start_month = Carbon::now()->startOfMonth()->format('Y-m-d');
                $end_month = Carbon::now()->endOfMonth()->format('Y-m-d');

                $query->whereDate('requestTime', '>=', $start_month)
                    ->whereDate('requestTime', '<=', $end_month);
            }
        })->orderBy('updated_at', 'desc');
        return $one;
        // return view('taskrequest::create');
    }

    public function show(Request $request)
    {
        $this->authorize('view', $this->menu);
        $recordPerPage = $this->getRecordPage();
        $task_request_list = $this->getListWithRequest($request);
        $task_request_list = $task_request_list->paginate($recordPerPage);

        foreach ($task_request_list->items() as $i => &$value) {
            $value['View'] = true;
            $value['DeleteorEdit'] = false;
            $value['needResponse'] = false;

            if ($value['requestUserID'] == auth()->id()
                && $value['responseContent'] == null) {
                $value['DeleteorEdit'] = true;
            }

            if ($value['receiveUserID'] == auth()->id() && $value['responseContent'] == null) {
                $value['needResponse'] = true;
            }
        }

        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;
        $this->data['approve'] = $this->app;
        $this->data['task_request_list'] = $task_request_list;
        $this->data['request'] = $request;
        //Get list project
        $this->data['projects'] = Project::all();
        if (strpos(\Request::getRequestUri(), 'api') !== false) {
            return response()->json(['data' => $this->data]);
        }

        //return $this->viewAdminLayout('TaskRequest.task-request', $this->data);
        return view('taskrequest::task-request', $this->data);
    }

    public function showDetail(Request $request, $id = null, $del = null)
    {
        if ($del) {
            $one = TaskRequest::query()->where('id', $id)->first();
            if ($one) {
                if($one->delete()){
                    $this->SentNotification($request, $one, true);
                }
            }
            return 1;
        }

        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['projects'] = $this->getProjects();
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;
        $this->data['approve'] = $this->app;
        $this->data['taskRequest'] = [];
        if ($id != null) {
            $one = TaskRequest::query()->where('id', $id)->first();
            if ($one) {
                $this->data['taskRequest'] = $one;
//                return $this->viewAdminIncludes('TaskRequest.task-request-detail', $this->data);
                return view('taskrequest::task-request-detail', $this->data);
            }
        }

//        return $this->viewAdminIncludes('TaskRequest.task-request-detail', $this->data);
        return view('taskrequest::task-request-detail', $this->data);
    }

    public function store(Request $request)
    {
        if (count($request->input()) === 0) {
            return abort('404');
        }

        if ($request->has('NoteRespone')) {
            if ($request['NoteRespone'] == null || $request['NoteRespone'] == '') {
                return $this->jsonErrors('Vui lòng điền nội dung phản hồi công việc');
            }

            $request['receiveUserID'] = auth()->id();
            $request['responseTime'] = Carbon::now();
        } else {
            if ($request['assignID'] == null) {
                return $this->jsonErrors('Vui lòng chọn người thực hiện công việc');
            }

            if ($request['ProjectID'] == null) {
                return $this->jsonErrors('Vui lòng chọn dự án');
            }

            if ($request['sumaryContent'] == null || $request['sumaryContent'] == '') {
                return $this->jsonErrors('Vui lòng điền nội dung tóm tắt công việc');
            }

            $request['requestUserID'] = auth()->id();
            $request['requestTime'] = Carbon::now();
            if ($request->has('isPrivate') && $request['isPrivate'] == 'on') {
                $request['isPrivate'] = '1';
            } else {
                $request['isPrivate'] = '0';
            }
        }

        $data = [
            'assignID' => 'receiveUserID',
            'Note' => 'requestContent',
            'isPrivate' => 'isPrivate',
            'ProjectID' => 'projectID',
            'sumaryContent' => 'sumaryContent',
            'NoteRespone' => 'responseContent',
            'requestUserID' => 'requestUserID',
            'requestTime' => 'requestTime',
            'receiveUserID' => 'receiveUserID',
            'responseTime' => 'responseTime',
        ];

        try {
            $modeIsUpdate = array_key_exists('id', $request->input());
            $one = ($modeIsUpdate && $request->id != null) ? TaskRequest::find($request->id) : new TaskRequest();
            foreach ($data as $key => $value) {
                if (isset($request->$key) && $request->$key != '' && $request->$key != null) {
                    $one->$value = $request->$key;
                }
            }
            if ($one->save()) {
                $this->SentNotification($request, $one);
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function review(Request $request, $id = null, $del = null)
    {
        $this->data['taskRequest'] = [];
        if ($id != null) {
            $one = TaskRequest::query()->where('id', $id)->first();
            if ($one) {
                if ($one['receiveUserID'] == auth()->id() && $one['responseContent'] == null) {
                    $one['needResponse'] = true;
                }
                $this->data['taskRequest'] = $one;
//                return $this->viewAdminIncludes('TaskRequest.task-request-response', $this->data);
                return view('taskrequest::task-request-response', $this->data);
            }
        }

//        return $this->viewAdminIncludes('TaskRequest.task-request-detail-response', $this->data);
        return view('taskrequest::task-request-detail-response', $this->data);
    }

    public function SentNotification($request, $one, $delete = false)
    {
        $requestUser = User::findOrFail($one->requestUserID);
        $responseUser = User::findOrFail($one->receiveUserID);

        $isSentMail = true;
        $isSentNotif = true;

        if (!$requestUser->email && !$responseUser->email) {
            $isSentMail = false;
        }

        $arrToken = PushToken::query()->where(function ($query) use($requestUser, $responseUser) {
                                                        $query->where('UserID', $requestUser->id)
                                                            ->orWhere('UserID', $responseUser->id);
                                                    })
                                                    ->where('allow_push', 1)
                                                    ->whereNull('deleted_at')
                                                    ->pluck('token_push')->toArray();

        if (count($arrToken) == 0) {
            $isSentNotif = false;
        }

        $sendData = [];
        $sendData['id'] = $one->id;
        $sendData['data'] = 'RW';

        $contentMail = 'kính gửi Ông/Bà <br/><br/>';
        $contentMail .= "<div style='display:block'>";

        if ($delete) {
            if($isSentMail) {
                $subjectMail = 'TB hủy yêu cầu công việc từ ' . $requestUser->FullName;

                $contentMailBody = $requestUser->FullName . ' đã hủy yêu cầu công việc<br/>';
                $contentMailBody .= ' Nội dung: ' . $one->sumaryContent . ' <br/>';
                $contentMailBody .= ' Dự án: ' . DB::table('projects')->where('id', $one->projectID)->first()->NameVi . ' <br/>';
                $contentMailBody .= ' Thời gian: ' . Carbon::parse($one->requestTime)->format('d/m/Y H:i') . ' <br/>';

                $MailAddressTO = $responseUser->email;
                $roomName = Room::find($requestUser['RoomId']);
                $MailNameFrom = $requestUser->FullName;
                $MailAddressCC = $requestUser->email;
            }

            if($isSentNotif) {
                $headrmess = 'TB hủy yêu cầu công việc từ ' .$requestUser->FullName;
                $bodyNoti = 'Dự án: ' .DB::table('projects')->where('id', $one->projectID)->first()->NameVi
                                        .', lúc ' .Carbon::parse($one->requestTime)->format('d/m/Y H:i');
            }
        } else {
            $modeIsUpdate = array_key_exists('id', $request->input()) && $request->id != null;

            if ($modeIsUpdate && $request->has('NoteRespone')) {
                if($isSentMail) {
                    // response
                    $subjectMail = 'TB có phản hồi công việc từ ' . $responseUser->FullName;

                    $contentMailBody = ' ' . $responseUser->FullName . ' đã phản hồi về công việc<br/>';
                    $contentMailBody .= ' Nội dung: ' . $one->sumaryContent . ' <br/>';
                    $contentMailBody .= ' Dự án: ' . DB::table('projects')->where('id', $one->projectID)->first()->NameVi . ' <br/>';
                    $contentMailBody .= ' Thời gian: ' . Carbon::parse($one->responseTime)->format('d/m/Y H:i') . ' <br/>';

                    $MailAddressTO = $requestUser->email;
                    $roomName = Room::find($responseUser['RoomId']);
                    $MailNameFrom = $responseUser->FullName;
                    $MailAddressCC = $responseUser->email;
                }

                if($isSentNotif) {
                    $headrmess = 'TB có phản hồi công việc từ ' . $responseUser->FullName;
                    $bodyNoti = 'Dự án: ' .DB::table('projects')->where('id', $one->projectID)->first()->NameVi
                        .', lúc ' .Carbon::parse($one->responseTime)->format('d/m/Y H:i');
                }
            } elseif ($modeIsUpdate && !$request->has('NoteRespone')) {
                if($isSentMail) {
                    // update
                    $subjectMail = 'Yêu cầu công việc được cập nhập bởi ' . $requestUser->FullName;

                    $contentMailBody = ' ' . $requestUser->FullName . ' đã cập nhập yêu cầu công việc<br/>';
                    $contentMailBody .= ' Nội dung: ' . $one->sumaryContent . ' <br/>';
                    $contentMailBody .= ' Dự án: ' . DB::table('projects')->where('id', $one->projectID)->first()->NameVi . ' <br/>';
                    $contentMailBody .= ' Thời gian: ' . Carbon::parse($one->requestTime)->format('d/m/Y H:i') . ' <br/>';

                    $MailAddressTO = $responseUser->email;
                    $roomName = Room::find($requestUser['RoomId']);
                    $MailNameFrom = $requestUser->FullName;
                    $MailAddressCC = $requestUser->email;
                }

                if($isSentNotif) {
                    $headrmess = 'Yêu cầu công việc được cập nhập bởi ' . $requestUser->FullName;
                    $bodyNoti = 'Dự án: ' .DB::table('projects')->where('id', $one->projectID)->first()->NameVi
                        .', lúc ' .Carbon::parse($one->requestTime)->format('d/m/Y H:i');
                }
            } else {
                if($isSentMail) {
                    // add new
                    $subjectMail = 'TB có công việc mới từ ' . $requestUser->FullName;

                    $contentMailBody = ' ' . $requestUser->FullName . ' đã yêu cầu một công việc mới<br/>';
                    $contentMailBody .= ' Nội dung: ' . $one->sumaryContent . ' <br/>';
                    $contentMailBody .= ' Dự án: ' . DB::table('projects')->where('id', $one->projectID)->first()->NameVi . ' <br/>';
                    $contentMailBody .= ' Thời gian: ' . Carbon::parse($one->requestTime)->format('d/m/Y H:i') . ' <br/>';

                    $MailAddressTO = $responseUser->email;
                    $roomName = Room::find($requestUser['RoomId']);
                    $MailNameFrom = $requestUser->FullName;
                    $MailAddressCC = $requestUser->email;
                }

                if($isSentNotif) {
                    $headrmess = 'TB có yêu cầu công việc mới từ ' . $requestUser->FullName;
                    $bodyNoti = 'Dự án: ' .DB::table('projects')->where('id', $one->projectID)->first()->NameVi
                        .', lúc ' .Carbon::parse($one->requestTime)->format('d/m/Y H:i');
                }
            }
        }

        if($isSentMail) {
            $contentMailBody .= "</div><br/>";
            $contentMailBody .= '  Ông/Bà vui lòng xem nội dung chi tiết trên hệ thống.<br/>';
            $contentMailBody .= "Xin chân thành cảm ơn.<br/>";

            $contentMail .= $contentMailBody;

            $this->SendMailHtml($subjectMail, $contentMail, config('mail.from.address'), $MailNameFrom . ' - ' . $roomName['Name'], $MailAddressTO, $MailAddressCC);
        }

        if($isSentNotif) {
            NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
        }
    }
}
