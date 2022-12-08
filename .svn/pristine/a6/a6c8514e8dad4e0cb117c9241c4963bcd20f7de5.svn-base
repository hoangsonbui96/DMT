<?php

namespace Modules\Event\Http\Controllers;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\NotificationController;
use App\Jobs\SendEmail;
use App\MasterData;
use App\Menu;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Modules\Event\Entities\Answer;
use Modules\Event\Entities\EventResult;
use Modules\Event\Entities\Question;

/**
 * Class EventController
 * @package App\Http\Controllers\Admin
 * Controller screen Event
 */
class EventController extends AdminController
{
    /**
     *
     */
    const KEYMENU = array(
        "add" => "EventListAdd",
        "view" => "EventList",
        "edit" => "EventListEdit",
        "delete" => "EventListDelete",
        "vote" => "EventListVote",
        "stats" => "EventListStats",
    );
    /**
     * @var
     */
    protected $add;
    /**
     * @var
     */
    protected $edit;
    /**
     * @var
     */
    protected $delete;
    /**
     * @var
     */
    protected $view;
    /**
     * @var
     */
    protected $vote;

    /**
     * EventController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        if (strpos($request->getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
        $array = $this->RoleView('Events', ['EventList']);
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
     * @param string $orderBy
     * @param string $sortBy
     * @return Application|RedirectResponse|Redirector|View
     * @throws AuthorizationException Get data Question and return view
     */
    public function show(Request $request, $orderBy = 'id', $sortBy = 'desc')
    {
        $this->authorize('action', $this->view);
        $recordPerPage = $this->getRecordPage();
        if (Schema::hasColumn('questions', $orderBy)) {
            $list = Question::orderBy($orderBy, $sortBy);
        } else {
            return redirect()->back();
        }
        //Finding by column
        $this->data['request'] = $request->query();
        $one = Question::query()->first();
        if ($one) {
            $one = $one->toArray();
            if (array_key_exists('search', $request->input())) {
                $list = $list->where(function ($query) use ($one, $request) {
                    foreach ($one as $key => $value) {
                        if ($key == 'Name')
                            $query->orWhere($key, 'like', '%' . $request->input('search') . '%');
                    }
                });
            }
        }
        foreach ($this->data['request'] as $key => $value) {
            if (is_array($value)) {
                $value[0] != '' ? $value[0] = $this->fncDateTimeConvertFomat($value[0],
                    self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : $value[0];
                $value[1] != '' ? $value[1] = $this->fncDateTimeConvertFomat($value[1],
                    self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : $value[1];

                if ($value[0] != '' && $value[1] != '' && $value[0] !== $value[1]) {
                    $list = $list->whereRaw("(questions.SDate BETWEEN CAST('$value[0]' AS DATE) AND CAST('$value[1]' AS DATE)
                    OR questions.SDate BETWEEN CAST('$value[0]' AS DATE) AND CAST('$value[1]' AS DATE))");
                }
                if ($value[0] === $value[1] && $value[0] != '') {
                    $list = $list->whereRaw("CAST(questions.SDate AS DATE) = '$value[0]'");
                }
                if ($value[0] != '' && $value[1] == '') {
                    $list = $list->whereRaw("(CAST(questions.SDate AS DATE) >= '$value[0]'
                        OR '$value[0]' BETWEEN CAST(questions.SDate AS DATE) AND CAST(questions.EDate AS DATE))");
                }
                if ($value[0] == '' && $value[1] != '') {
                    $list = $list->whereRaw("(CAST(questions.EDate AS DATE) <= '$value[1]'
                        OR '$value[1]' BETWEEN CAST(questions.SDate AS DATE) AND CAST(questions.EDate AS DATE))");
                }
            }
        }

        $user = User::find(Auth::user()->id);
        if ($user->cant('admin', $this->menu)) {
            $list = $list->where('Status', 1);
        }
        $this->data['totalstt'] = $list->count();
        $list = $list->paginate($recordPerPage);
        foreach ($list as $item) {
            $item->CreateUID = User::withTrashed()
                ->where('id', $item->CreateUID)
                ->first()->FullName;
        }
        $this->data['list'] = $list;
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        parse_str(str_replace('?', '', $query_string), $query_array);
        if ($list->count() == 0) {
            if (array_key_exists('page', $query_array)) {
                if ($query_array['page'] > 1) {
                    $query_array['page'] = $list->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }
            }
        }
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;
        $this->data['stats'] = $this->stats;
        $this->data['vote'] = $this->vote;
        $this->data['recordPerPage'] = $recordPerPage;
        $this->data['sortBy'] = $sortBy;
        $this->data['typeQuestion'] = MasterData::query()
            ->where('DataKey', 'SK')
            ->get();
        $this->data['role_key'] = 'EventList';
        if (strpos($request->getRequestUri(), 'api') !== false) {
            return $this->data;
        }
//        return $this->viewAdminLayout('questions', $this->data);
        return \view("event::layouts.questions", $this->data);
    }

    /**
     * Process insert multiple records,update one records
     * @param Request $request
     * @param null $id
     * @return string|void
     */
    public function store(Request $request, $id = null)
    {
        $header = 'Kính gửi anh/chị/em!';
        try {
            if (count($request->input()) > 0) {
                if (array_key_exists('id', $request->input())) {
                    $validator = Validator::make($request->all(),
                        [
                            'Name' => 'required|string|max:100',
                            'Type' => 'required|string',
                            'Question' => 'required|string',
                            'SDate' => 'required|date_format:d/m/Y',
                            'EDate' => 'required|date_format:d/m/Y',
                            'Status' => 'string|nullable',
                            'QLink' => 'array|nullable',
                            'QLink.*' => 'integer|min:1',
                            'Answer' => 'array|nullable',
                            'Answer.*' => 'array|nullable',
                            'Answer.*.*' => 'required|string',
                            'id' => 'integer|min:1|nullable',
                        ]);
                } else {
                    $validator = Validator::make($request->all(),
                        [
                            'Name' => 'required|string|max:100',
                            'Type' => 'required|string',
                            'Question' => 'required|string',
                            'SDate' => 'required|date_format:d/m/Y',
                            'EDate' => 'required|date_format:d/m/Y',
                            'Status' => 'string|nullable',
                            'QLink' => 'array|nullable',
                            'QLink.*' => 'integer|min:1',
                            'Answer' => 'array|nullable',
                            'Answer.*' => 'array|nullable',
                            'Answer.*.*' => 'required|string',
                        ]
                    );
                }

                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()->all()]);
                }
                $validated = $validator->validate();
                $dateValidated = $this->fncDateTimeConvertFomat($validated['SDate'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                $eDateValidated = $this->fncDateTimeConvertFomat($validated['EDate'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                if ((strtotime($dateValidated) > strtotime($eDateValidated))) {
                    return $this->jsonErrors('Thời gian bắt đầu không được lớn hơn thời gian kết thúc!');
                }
                if ((strtotime(Carbon::today()) > strtotime($eDateValidated))) {
                    return $this->jsonErrors('Thời gian kết thúc không được nhỏ hơn ngày hôm nay!');
                }
                if (array_key_exists('id', $validated)) {
                    $one = Question::find($validated['id']);
                } else {
                    $one = new Question();
                    $one->CreateUID = Auth::id();
                }
                foreach ($validated as $key => $value) {
                    if (Schema::hasColumn('questions', $key) && !is_array($value))
                        $one->$key = $this->xssClean($value);
                }

                isset($validated['Status']) ? $one->Status = 1 : $one->Status = 0;

                if (isset($validated['QLink'])) {
                    $one->QLink = ',' . implode(',', $validated['QLink']) . ',';
                } else {
                    $one->QLink = null;
                }
                if (isset($validated['SDate'])) {
                    $one->SDate = $this->fncDateTimeConvertFomat($validated['SDate'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                }
                if (isset($validated['EDate'])) {
                    $one->EDate = $this->fncDateTimeConvertFomat($validated['EDate'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                }
                $check = $one->save();
                if ($check === true) {
                    $arrToken = DB::table('push_token')->whereNull('deleted_at')->pluck('token_push')->toArray();
                    if (count($arrToken) > 0) {
                        $sendData = [];
                        $sendData['id'] = $one->id;
                        $sendData['data'] = DB::table('master_data')->where('DataValue', $request['Type'])->pluck('DataKey')->first();
                        $headrmess = "Có 1 sự kiện mới";
                        $bodyNoti = "Từ " . $request['SDate'] . ' đến ' . $request['EDate'];
                        NotificationController::sendCloudMessaseNoti($headrmess, $arrToken, $bodyNoti, $sendData);
                    }

                }
                if (isset($validated['Answer'])) {
                    $listAnswer = Answer::query()->select('id')->where('QID', $one->id)->get()->toArray();
                    foreach ($listAnswer as $item) {
                        $check = false;
                        foreach ($validated['Answer'] as $key => $value) {
                            if ($item['id'] == $key) {
                                $check = true;
                                break;
                            }
                        }
                        if ($check == false) {
                            Answer::query()->where('id', $item['id'])->where('QID', $one->id)->delete();
                            EventResult::query()->where('AID', $item['id'])->delete();
                        }
                    }

                    foreach ($validated['Answer'] as $key => $value) {
                        $answer = Answer::find($key);
                        if (!$answer) {
                            foreach ($value as $item) {
                                $answer = new Answer();
                                $answer->QID = $one->id;
                                $answer->Answer = $this->xssClean($item);
                                $answer->CreateUID = $one->CreateUID;
                                $answer->save();
                            }
                        } else {
                            $answer->QID = $one->id;
                            $answer->Answer = $this->xssClean($value[0]);
                            $answer->CreateUID = $one->CreateUID;
                            $answer->save();
                        }
                    }
                } else {
                    $answers = Answer::query()
                        ->where('QID', $one->id)
                        ->get();
                    foreach ($answers as $answer) {
                        EventResult::query()
                            ->where('AID', $answer->id)
                            ->delete();
                        $answer->delete();
                    }
                }
                if (strpos($request->getRequestUri(), 'api') !== false) {
                    return AdminController::responseApi(200, null, __('admin.success.save'));
                }
                $this->sendMail($request->all(), $header);
                return $one->id;
            } else {
                return abort('404');
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

    }

    /**
     * @param $array
     * @param $header
     * @param null $comment
     * @param null $int
     */
    public function sendMail($array, $header, $comment = null, $int = null)
    {
        $uriEvent = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '?Date[]=' . $array['SDate'] . '&Date[]=' . $array['EDate'];
//        $listEmail = DB::table('users')->pluck('email');
        $users = Auth::user();
        $Room = DB::table('rooms')->where('id', $users['RoomId'])->first("Name");
        //Mr,Ms,Mrs
        if ($users['Gender'] == 0) {
            $users['Gender'] = 'Mr';
        }
        if ($users['Gender'] == 1 && $users['MaritalStt'] == 1) {
            $users['Gender'] = 'Mrs';
        }
        if ($users['Gender'] == 1 && $users['MaritalStt'] == 0) {
            $users['Gender'] = 'Ms';
        }
//        $Header = "Kính gửi Ban giám đốc và anh/chị/em trong công ty, ";
        $MasterDataValue = "tạo sự kiện bình chọn";
        $viewDay = $array['SDate'] . ' tới ' . $array['EDate'];
//        $Reason = $array['Question'];
//        $Remark = $uriEvent;
        $Approved = "";
//        $Comment = null;
//        $Management = "";
//        $UpdateBy = "";

        $this->attr_mail_view[self::KEY_DATA_BINDING] = [
            'Header' => "Kính gửi Ban giám đốc và anh/chị/em trong công ty, ",
            'MasterDataValue' => $MasterDataValue,
            'FullName' => $users['FullName'],
            'Room' => $Room->Name,
            'viewDay' => $array['SDate'] . ' tới ' . $array['EDate'],
            'Reason' => $array['Question'],
            'Remark' => $uriEvent,
            'Gender' => $users['Gender'],
            'Approved' => "",
            'Comment' => null,
            'Management' => "",
            'UpdateBy' => "",
        ];
        $this->attr_mail_view[self::KEY_SUBJECT_MAIL] = 'TB ' . mb_strtolower($MasterDataValue, 'UTF-8') . ' (' . $viewDay . ')';
        $this->attr_mail_view[self::KEY_VIEW_MAIL] = 'template_mail.eventvote-mail';
        $this->attr_mail_view[self::KEY_MAIL_NAME_FROM] = $users['FullName'] . ' - ' . $Room->Name;
        $this->attr_mail_view[self::KEY_MAIL_ADDRESS_TO] = ["akber@akb.vn",];
        $this->attr_mail_view[self::KEY_MAIL_ADDRESS_CC] = [];
        SendEmail::dispatch("send_view", $this->attr_mail_view);
    }

    /**
     * Show popup insert,update can insert multiple records
     * @param Request $request
     * @param null $oneId
     * @param null $del
     * @return Application|Factory|JsonResponse|\Illuminate\View\View|int
     */
    public function showDetail(Request $request, $oneId = null, $del = null)
    {
        $this->data['typeQuestion'] = MasterData::query()
            ->where('DataKey', 'SK')
            ->get();
        if ($oneId != null) {
            if ($del == 'del') {
                $one = Question::find($oneId);
                if ($one) $one->delete();
                $answers = Answer::query()
                    ->where('QID', $oneId)
                    ->get();
                foreach ($answers as $answer) {
                    EventResult::query()
                        ->where('AID', $answer->id)
                        ->delete();
                    $answer->delete();
                }
                if (strpos($request->getRequestUri(), 'api') !== false) {
                    return response()->json(['success' => 'Xóa thành công.']);
                }
                return 1;
            }

            $this->data['itemInfo'] = Question::find($oneId);
            if ($this->data['itemInfo']) {
                $this->data['relateQuestions'] = Question::query()
                    ->where('CreateUID', Auth::user()->id)
                    ->where('id', '!=', $oneId)
                    ->get();
                $result = Answer::query()
                    ->where('QID', $oneId)
                    ->orderBy('id')
                    ->orderBy('created_at')
                    ->get();
                if ($result) {
                    $this->data['itemInfo']->answers = $result;
                }
                if (!is_null($this->data['itemInfo']->QLink))
                    $this->data['itemInfo']->QLink = explode(',', $this->data['itemInfo']->QLink);
                else {
                    $this->data['itemInfo']->QLink = [];
                }
                if (strpos($request->getRequestUri(), 'api') !== false) {
                    return $this->data;
                }
                return view('event::includes.question-detail', $this->data);
            } else {
                return "";
            }
        } else {
            $this->data['relateQuestions'] = Question::query()
                ->where('CreateUID', Auth::id())
                ->get();
            if (strpos($request->getRequestUri(), 'api') !== false) {
                return $this->data;
            }
//            return $this->viewAdminIncludes('question-detail', $this->data);
            return \view('event::includes.question-detail', $this->data);
        }

    }

    /**
     * Show popup vote
     * @param Request $request
     * @param null $id
     * @return Application|Factory|View|\Illuminate\View\View
     */
    public function vote(Request $request, $id = null)
    {
        $this->data['question'] = Question::query()
            ->where('id', $id)
            ->first();
        $this->data['typeQuestion'] = MasterData::query()
            ->where('DataKey', 'SK')
            ->get();
        if ($this->data['question']) {
            $this->data['answers'] = Answer::query()
                ->where('QID', $this->data['question']->id)
                ->get();
            $array = [];
            foreach ($this->data['answers'] as &$row) {
                $choose = EventResult::query()->select("AID")
                    ->leftJoin("answers", "answers.id", "=", "event_results.AID")
                    ->where('UID', Auth::user()->id)
                    ->where('AID', $row->id)
                    ->get()->toArray();
                $array = count($choose) != 0 ? array_merge($array, $choose) : $array;
                $row->CheckAnswer = $this->checkAnswer($row->id);
            }
            $this->data['eventResults'] = $array;
            if (strpos($request->getRequestUri(), 'api') !== false) {
                return $this->data;
            }
//            return $this->viewAdminIncludes('vote-detail', $this->data);
            return view('event::includes.vote-detail', $this->data);
        }
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Application|Factory|JsonResponse|\Illuminate\View\View
     * @throws AuthorizationException
     */
    public function voteResult(Request $request, $id = null)
    {
        $this->data['menuCheck'] = Menu::query()
            ->where('RouteName', 'admin.Events')
            ->first();

        $this->authorize('view', $this->data['menuCheck']);
        $this->data['question'] = Question::query()
            ->where('id', $id)
            ->first();
        $this->data['role_key'] = 'EventList';
        if ($this->data['question']) {
            $this->data['answers'] = DB::table('answers')
                ->leftJoin('event_results', 'event_results.AID', 'answers.id')
                ->select('event_results.AID', 'answers.*', DB::raw('count(event_results.AID) as Count'))
                ->groupBy('answers.id')
                ->where('answers.QID', $id)
                ->orderBy('id')
                ->orderBy('created_at')
                ->get();
            $array = [];
            $notVote = [];
            foreach ($this->data['answers'] as $item) {
                $item->list = DB::table('event_results')
                    ->select('users.*')
                    ->join('users', 'event_results.UID', 'users.id')
                    ->where('AID', $item->id)
                    ->get();
                array_push($notVote, $item->list);
                $item->count = $item->list->count();
                array_push($array, $item->count);
                $item->countMale = 0;
                $item->countFemale = 0;
                $totalAge = 0;
                foreach ($item->list as $value) {
                    if ($value->Gender) $item->countFemale++;
                    else $item->countMale++;
                    $totalAge += Carbon::parse($value->Birthday)->age;
                }
                $item->avgAge = $totalAge / (($item->countMale + $item->countFemale) > 0 ? ($item->countMale + $item->countFemale) : 1);
            }
            $this->data['maxArray'] = count($array) != 0 ? max($array) : null;
        }
        $arrNotVote = [];
        foreach ($notVote as $eleNotVote) {
            foreach ($eleNotVote as $ele) {
                array_push($arrNotVote, $ele->id);
            }
        }
        $this->data['arrIdNotVote'] = DB::table('users')->select('id')->whereNotIn('id', $arrNotVote)->whereNull('deleted_at')->get();
        if (strpos($request->getRequestUri(), 'api') !== false) {
            return response()->json(['data' => $this->data]);
        }
//        return $this->viewAdminIncludes('vote-result', $this->data);
        return \view('event::includes.vote-result', $this->data);

    }

    /**
     * Process insert multiple records,update one records
     * @param Request $request
     * @return string|void
     */
    public function voteSave(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'answer' => 'required|array|nullable',
                'answer.*' => 'string|nullable',
                'question' => 'integer|required'
            ]);
        if ($validator->fails()) {
            if (strpos($request->getUri(), 'api') !== false) {
                return AdminController::responseApi(422, ['errors' => $validator->errors()->all()]);
            }
            return response()->json(['errors' => $validator->errors()->all()]);
        }
        $validated = $validator->validated();
        $question = Question::find($validated['question']);
        $listAnswer = Answer::query()
            ->where('QID', $question->id)
            ->get();
        foreach ($listAnswer as $answerItem) {
            $eventResult = EventResult::query()
                ->where('AID', $answerItem->id)
                ->where('UID', Auth::user()->id)
                ->delete();
        }
        if (isset($validated['answer']) && count($validated['answer']) >= 1) {
            foreach ($validated['answer'] as $item) {
                $answer = Answer::find($item);
                if ($answer) {
                    $result = new EventResult();
                    $result->AID = $item;
                    $result->UID = Auth::user()->id;
                    $result->save();
                    if ($question->Type == "SK001") break;
                } else {
                    $item = explode(';', $item, 2)[1];
                    $answer = new Answer();
                    $answer->Answer = $item;
                    $answer->QID = $question->id;
                    $answer->CreateUID = Auth::user()->id;
                    $answer->save();

                    $result = new EventResult();
                    $result->AID = $answer->id;
                    $result->UID = Auth::user()->id;
                    $result->save();
                }
            }
            if (strpos($request->getRequestUri(), 'api') !== false) {
                return response()->json(['success' => 'Lưu thành công.']);
            }
            return 1;
        } else {
            if (strpos($request->getRequestUri(), 'api') !== false) {
                return response()->json(['errors' => 'Lưu thất bại.']);
            }
            return 0;
        }
    }

    /**
     * @param $aid
     * @param $uid
     * @return JsonResponse
     */
    public function delVote($aid, $uid)
    {
        $result = EventResult::query()
            ->where('AID', $aid)
            ->where('UID', $uid)
            ->delete();
        if ($result == 1) {
            return response()->json(['success' => 'Xóa thành công.']);
        }
        return response()->json(['errors' => 'Xóa thất bại.']);
    }
}
