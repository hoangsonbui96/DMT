<?php

namespace App\Http\Controllers\Admin;


use App\Answer;
use App\EventResult;
use App\Menu;
use App\Question;
use App\User;
use App\MasterData;
use App\RoleScreenDetail;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Admin\NotificationController;

/**
 * Class EventController
 * @package App\Http\Controllers\Admin
 * Controller screen Event
 */
class EventController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $vote;
    const KEYMENU= array(
        "add" => "EventListAdd",
        "view" => "EventList",
        "edit" => "EventListEdit",
        "delete" => "EventListDelete",
        "vote" => "EventListVote",
        "stats" => "EventListStats",
    );
    /**
     * EventController constructor.
     * @param Request $request
     */
    public function __construct(Request $request) {
        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
        $array = $this->RoleView('Events',['EventList']);
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
     * @return View (questions)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * Get data Question and return view
     */
    public function show(Request $request, $orderBy = 'id', $sortBy = 'desc') {
        $this->authorize('action', $this->view);
        $recordPerPage = $this->getRecordPage();
        //list users
        if (Schema::hasColumn('questions',$orderBy)) {
            $list = Question::orderBy($orderBy, $sortBy);
        } else {
            return redirect()->back();
        }
        //tim kiem theo column
        $this->data['request'] = $request->query();
        $one = Question::query()->first();
        if ($one) {
            $one = $one->toArray();
            if (array_key_exists('search', $request->input())) {
                $list = $list->where(function ($query) use ($one, $request) {
                    foreach ($one as $key=>$value) {
                        if ($key == 'Name')
                        $query->orWhere($key, 'like', '%'.$request->input('search').'%');
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
        //phan trang
        //Tien 8/4/2020
        $this->data['totalstt'] = $list->count();
        //Tien 8/4/2020

        $list = $list->paginate($recordPerPage);
        foreach ($list as $item) {
            //Tien 8/4/2020
            $item->CreateUID = User::query()->withTrashed()
                                ->where('id', $item->CreateUID)
                                ->first()->FullName;
            //Tien 8/4/2020

            // $item->CreateUID = User::find($item->CreateUID)->FullName;
        }
        $this->data['list'] = $list;
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
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
        $this->data['query_array'] = $query_array;

        $this->data['sort_link'] = $sort_link;
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;
        $this->data['stats'] = $this->stats;
        $this->data['vote'] = $this->vote;
        //Tien 8/4/2020
        $this->data['recordPerPage'] = $recordPerPage;
        $this->data['sortBy'] = $sortBy;
        $this->data['typeQuestion'] = MasterData::query()
            ->where('DataKey', 'SK')
            ->get();
        //Tien 38/4/2020
        $this->data['role_key'] = 'EventList';
        if (strpos(\Request::getRequestUri(), 'api') !== false) {
            return $this->data;
        }
        return $this->viewAdminLayout('questions', $this->data);
    }

    /**
     * Process insert multiple records,update one records
     * @param Request $request
     * @param null $id
     * @return string|void
     */
    public function store(Request $request, $id = null) {

        $arrToken = DB::table('push_token')->whereNull('deleted_at')->pluck('token_push')->toArray();
        $header = 'Kính gửi anh/chị/em!';

        try {
            if (count($request->input()) > 0) {
                if (array_key_exists('id', $request->input())) {
                    $validator = Validator::make($request->all(),
                        [
                            'Name'       => 'required|string|max:100',
                            'Type'       => 'required|string',
                            'Question'   => 'required|string',
                            'SDate'      => 'required|date_format:d/m/Y',
                            'EDate'      => 'required|date_format:d/m/Y',
                            'Status'     => 'string|nullable',
                            'QLink'      => 'array|nullable',
                            'QLink.*'    => 'integer|min:1',
                            'Answer'     => 'array|required',
                            'Answer.*'   => 'array|required',
                            'Answer.*.*' => 'required|string',
                            'id'         => 'integer|min:1|nullable',
                        ]);
                } else {
                    $validator = Validator::make($request->all(),
                        [
                            'Name'       => 'required|string|max:100',
                            'Type'       => 'required|string',
                            'Question'   => 'required|string',
                            'SDate'      => 'required|date_format:d/m/Y',
                            'EDate'      => 'required|date_format:d/m/Y',
                            'Status'     => 'string|nullable',
                            'QLink'      => 'array|nullable',
                            'QLink.*'    => 'integer|min:1',
                            'Answer'     => 'array|required', 
                            'Answer.*'   => 'array|required',
                            'Answer.*.*' => 'required|string',
                        ]
                    );
                }

                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()->all()]);
                }

                $validated = $validator->validate();

                //Tien 8/4/2020
                $datevalidated = $this->fncDateTimeConvertFomat($validated['SDate'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                $edatevalidated = $this->fncDateTimeConvertFomat($validated['EDate'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                if ((strtotime($datevalidated) > strtotime($edatevalidated))) {
                    return $this->jsonErrors('Thời gian bắt đầu không được lớn hơn thời gian kết thúc!');
                }
                if ((strtotime(Carbon::today()) > strtotime($edatevalidated))) {
                    return $this->jsonErrors('Thời gian kết thúc không được nhỏ hơn ngày hôm nay!');
                }
                // Tien 8/4/2020
                if (array_key_exists('id', $validated)) {
                    $one = Question::find($validated['id']);
                } else {
                    $one = new Question();
                    $one->CreateUID = Auth::user()->id;
                }
                foreach ($validated as $key => $value) {
                    if (Schema::hasColumn('questions', $key) && !is_array($value))
                        $one->$key = $this->xssClean($value);
                }

                isset($validated['Status']) ? $one->Status = 1 : $one->Status = 0;

                if (isset($validated['QLink'])) {
                    $one->QLink = ','.implode(',', $validated['QLink']).',';
                } else {
                    $one->QLink = null;
                }
                //Tien 8/4/2020
                if (isset($validated['SDate'])) {
                    $one->SDate = $this->fncDateTimeConvertFomat($validated['SDate'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                }
                if (isset($validated['EDate'])) {
                    $one->EDate = $this->fncDateTimeConvertFomat($validated['EDate'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                }
                //Tien 8/4/2020 && Khanh 28/04/2021
                $check = $one->save();

                if($check === true){
                    $arrToken = DB::table('push_token')->whereNull('deleted_at')->pluck('token_push')->toArray();

                    if(count($arrToken) > 0){
                        $sendData = [];
                        $sendData['id'] = $one->id;
                        $sendData['data'] = DB::table('master_data')->where('DataValue',$request['Type'])->pluck('DataKey')->first();

                        $headrmess = "Có 1 sự kiện mới";

                        $bodyNoti = "Từ ".$request['SDate'].' đến '.$request['EDate'];

                        NotificationController::sendCloudMessaseNoti($headrmess, $arrToken,$bodyNoti,$sendData);
                    }
                    
                }
                // print_r($validated['Answer']);
                // die('xxx');
                // echo "<pre>";
                if (isset($validated['Answer'])) {
                    $listAnswer = Answer::query()->select('id') ->where('QID', $one->id)->get()->toArray();
                    // return $listAnswer;
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
                        // print_r($key.'+'.$value[0]);
                        
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
                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                    // return response()->json(['success' => 'Lưu thành công.']);
                    return AdminController::responseApi(200, null, __('admin.success.save'));
                }
                $this->sendMail($request->all(),$header);
                return $one->id;
            } else {
                return abort('404');
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    /**
     * Show popup insert,update can insert multiple records
     * @param Request $request
     * @param null $oneId
     * @param null $del
     * @return View (question-detail)
     */
    public function showDetail(Request $request, $oneId = null, $del = null) {
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
                if (strpos(\Request::getRequestUri(), 'api') !== false) {
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
                else{
                    $this->data['itemInfo']->QLink = [];
                }
                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                    return $this->data;
                }
                return view('admin.includes.question-detail', $this->data);
            } else {
                return "";
            }
        } else {
            $this->data['relateQuestions'] = Question::query()
                ->where('CreateUID', Auth::user()->id)
                // ->where('id', '!=', $oneId)
                ->get();
            if (strpos(\Request::getRequestUri(), 'api') !== false) {
                return $this->data;
            }
            return $this->viewAdminIncludes('question-detail', $this->data);
        }

    }

    /**
     * Show popup vote
     * @param null $id
     * @return View (vote-detail)
     */
    public function vote($id = null) {
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
            $array = array();
            foreach($this->data['answers'] as $row){
                $choose = EventResult::query()->select("AID")
                ->leftJoin("answers","answers.id","=","event_results.AID")
                ->where('UID', Auth::user()->id)
                ->where('AID', $row->id)
                ->get()->toArray();
                if(count($choose)>0)
                    $array = array_merge($array,$choose);
            }
            $this->data['eventResults'] =$array;
            // dd($this->data['eventResults']);
            if (strpos(\Request::getRequestUri(), 'api') !== false) {
                return $this->data;
            }
            return $this->viewAdminIncludes('vote-detail', $this->data);
        }

    }

    /**
     * @param null $id
     * @return View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function voteResult($id = null) {
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
                // ->orderBy('Count', 'desc')
                ->where('answers.QID', $id)
                ->orderBy('id')
                ->orderBy('created_at')
                ->get();

            $array = array();
            $notVote = [];
            foreach ($this->data['answers'] as $item) {
                $item->list = DB::table('event_results')
                    ->select('users.*')
                    ->join('users', 'event_results.UID', 'users.id')
                    ->where('AID', $item->id)
                    ->get();
                array_push( $notVote,$item->list);
                $item->count = $item->list->count();
                array_push($array,$item->count);
                $item->countMale = 0;
                $item->countFemale = 0;
                $totalAge = 0;
                foreach ($item->list as $value) {
                    if ($value->Gender) $item->countFemale++;
                    else $item->countMale++;
                    $totalAge += Carbon::parse($value->Birthday)->age;
                }
                $item->avgAge = $totalAge/(($item->countMale + $item->countFemale) > 0 ? ($item->countMale + $item->countFemale) : 1);
            }
            $this->data['maxArray'] = max($array);
        }
        $arrNotVote = [];
        foreach($notVote as $eleNotVote){
            foreach($eleNotVote as $ele){
                    array_push($arrNotVote,$ele->id);
            }
        }
        $this->data['arrIdNotVote'] = DB::table('users')->select('id')->whereNotIn('id', $arrNotVote)->whereNull('deleted_at')->where('Active','=',1)->where('FullName','!=','Root')->orderBy('username','asc')->get();
        // dd($this->data['arrIdNotVote']);
        if (strpos(\Request::getRequestUri(), 'api') !== false) {
            return response()->json(['data' => $this->data ]);
        }
        return $this->viewAdminIncludes('vote-result', $this->data);

    }

    /**
     * Process insert multiple records,update one records
     * @param Request $request
     * @return string|void
     */
    public function voteSave(Request $request) {
        $validator = Validator::make($request->all(),
            [
                'answer'   => 'required|array|nullable',
                'answer.*' => 'string|nullable',
                'question' => 'integer|required'
            ]);

        if ($validator->fails()) {
            if (strpos(\Request::getRequestUri(), 'api') !== false) {
                return AdminController::responseApi(422, ['errors'=>$validator->errors()->all()]);
            }
            return response()->json(['errors'=>$validator->errors()->all()]);
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
        if (isset($validated['answer']) && count($validated['answer'])>=1) {
            foreach ($validated['answer'] as $item) {
                $answer = Answer::find($item);
                if ($answer) {
                    $result = new EventResult();
                    $result->AID = $item;
                    $result->UID = Auth::user()->id;
                    $result->save();
                    if ($question->Type == "SK001") break;
                } else {
                    $item = explode(';',$item, 2)[1];
                    $answer = new Answer();
                    $answer->Answer = $item;
                    $answer->QID = $question->id;
                    $answer->CreateUID = Auth::user()->id;
                    $answer->save();

                    $result = new EventResult();
                    $result->AID = $answer->id;
                    $result->UID = Auth::user()->id;
                    $result->save();
                    // if ($question->Type == 0) break;
                }
            }
            if (strpos(\Request::getRequestUri(), 'api') !== false) {
                return response()->json(['success' => 'Lưu thành công.']);
            }
            return 1;
        } else {
            if (strpos(\Request::getRequestUri(), 'api') !== false) {
                return response()->json(['errors' => 'Lưu thất bại.']);
            }
            return 0;
        }
    }

    public function delVote($aid, $uid) {
        $result = EventResult::query()
            ->where('AID', $aid)
            ->where('UID', $uid)
            ->delete();
        if ($result === 1) {
            return response()->json(['success' => 'Xóa thành công.']);
        }
        return response()->json(['errors' => 'Xóa thất bại.']);
    }
    public function sendMail($array,$header,$comment = null, $int=null){
        

        $uriEvent = $_SERVER['HTTP_HOST'] .$_SERVER['REQUEST_URI'].'?Date[]='.$array['SDate'].'&Date[]='.$array['EDate'];
        $listEmail = DB::table('users')->pluck('email');
        $users =Auth::user();
        $Room = DB::table('rooms')->where('id',$users['RoomId'])->first("Name");

        //cách gọi Mr,Ms,Mrs
        if ($users['Gender'] == 0){
            $users['Gender'] = 'Mr';
        }
        if ($users['Gender'] == 1 && $users['MaritalStt'] == 1){
            $users['Gender'] = 'Mrs';
        }
        if ($users['Gender'] == 1 && $users['MaritalStt'] == 0){
            $users['Gender'] = 'Ms';
        }

        $Header = "Kính gửi Ban giám đốc và anh/chị/em trong công ty, ";
        $MasterDataValue = "tạo sự kiện bình chọn";
        // $Room = "Phòng Dev 3B";
        $viewDay = $array['SDate'].' tới '.$array['EDate'];
        $Reason = $array['Question'];
        $Remark =  $uriEvent;
        $Approved = "";
        $Comment = null;
        $Management = "";
        $UpdateBy = "";
        $dataBinding = [
                'Header'                => $Header,
                'MasterDataValue'       => $MasterDataValue,
                'FullName'              => $users['FullName'],
                'Room'                  => $Room->Name,
                'viewDay'               => $viewDay,
                'Reason'                => $Reason,
                'Remark'                => $Remark,
                'Gender'                => $users['Gender'],
                'Approved'              => $Approved,
                'Comment'               => $Comment,
                'Management'            => $Management,
                'UpdateBy'              => $UpdateBy,
            ];  
        $subjectMail= "TB Event".$array['SDate'].' đến '.$array['EDate'];
        $viewBladeMail = 'template_mail.eventvote-mail';
        // $nameFrom = 'AKB Văn Phòng EVENT';
        $nameFrom = $users['FullName'] . ' - ' .$Room->Name;
            $subjectMail = 'TB '.mb_strtolower($MasterDataValue, 'UTF-8').' ('.$viewDay.')';
        // $arrMailAddressTo = array_unique($listEmail);
        $arrMailAddressTo = [
                                "akber@akb.vn",
                            ];
        $addressMailCc = [];

        $this->SendMailWithView([        
            self::KEY_SUBJECT_MAIL      => $subjectMail,
            self::KEY_VIEW_MAIL         => $viewBladeMail,
            self::KEY_DATA_BINDING      => $dataBinding,
            self::KEY_MAIL_NAME_FROM    => $nameFrom,
            self::KEY_MAIL_ADDRESS_TO   => $arrMailAddressTo,
            self::KEY_MAIL_ADDRESS_CC   => $addressMailCc,
        ]);
    }
}
