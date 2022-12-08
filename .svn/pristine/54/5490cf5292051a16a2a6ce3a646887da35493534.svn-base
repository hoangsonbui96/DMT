<?php

namespace App\Http\Controllers\Admin;

use App\MeetingSchedule;
use App\Room;
use App\User;
use App\RoleScreenDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
/**
 * Class MeetingScheduleController
 * @package App\Http\Controllers\Admin
 * Controller screen Meeting Schedule
 */
class MeetingScheduleController extends AdminController
{
    protected $add; 
    protected $edit;
    protected $delete;
    protected $view;
    const KEYMENU= array(
        "add" => "MeetingListAdd",
        "view" => "MeetingList",
        "edit" => "MeetingListEdit",
        "delete" => "MeetingListDelete",
    );
    /**
     * MeetingScheduleController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');
        $array = $this->RoleView(null,['MeetingList']);
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if($value == $row->alias)
                    $this->$key = $row;
            } 
        }
    }

    /**
     * @param Request $request
     * @return View (meetings)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * Get data MeetingSchedule and return view
     */
    public function show(Request $request, $orderBy = 'id', $sortBy = 'desc'){

        $recordPerPage = $this->getRecordPage();
        //get all users for searching
        $this->data['users'] = User::query()
            ->where('deleted','0')
            ->select('id', 'FullName')
            ->where('Active', 1)
            ->where('deleted','!=', 1)
            ->where('role_group', '!=', 1)
            ->get();

        //list meeting rooms
        $this->data['rooms'] = Room::query()
            ->where('Active', 1)
            ->where('MeetingRoomFlag', 1)
            ->get();
        //list meetings

        if(Schema::hasColumn('meeting_schedules',$orderBy)){
            $meetings = MeetingSchedule::orderBy($orderBy, $sortBy);
        }
        else
        {
            return redirect()->back();
        }

        //tim kiem theo column
        $this->data['request'] = $request->query();
        $one = MeetingSchedule::query()->select('meeting_schedules.Purpose','us2.FullName as RegisterID','us1.FullName as MeetingHostID','meeting_schedules.MeetingDate','meeting_schedules.MeetingTimeFrom','meeting_schedules.MeetingTimeTo','rooms.Name')
            ->join('rooms','rooms.id','=','meeting_schedules.RoomID')
            ->leftJoin('users as us2','us2.id','=','meeting_schedules.RegisterID')
            ->leftJoin('users as us1','us1.id','=','meeting_schedules.MeetingHostID')->first();
        if($one){
            $one = $one->toArray();
            if(array_key_exists('search', $request->input())){
                if(null !== $request->input('search')){
                    $meetings = $meetings
                    ->select('meeting_schedules.id','meeting_schedules.Purpose','meeting_schedules.RegisterID','meeting_schedules.MeetingHostID','meeting_schedules.MeetingDate','meeting_schedules.MeetingTimeFrom','meeting_schedules.MeetingTimeTo','rooms.Name','meeting_schedules.RoomID')
                    ->join('rooms','rooms.id','=','meeting_schedules.RoomID')
                    ->leftJoin('users as us2','us2.id','=','meeting_schedules.RegisterID')
                    ->leftJoin('users as us1','us1.id','=','meeting_schedules.MeetingHostID')
                    ->where(function ($query) use ($one, $request){
                        foreach($one as $key=>$value) {
                            if($key == 'Name') {
                                $query->orWhere('rooms.'.$key, 'like', '%'.$request->input('search').'%');
                            }else if($key == 'RegisterID'){
                                $query->orWhere('us2.FullName', 'like', '%'.$request->input('search').'%');
                            }else if($key == 'MeetingHostID'){
                                $query->orWhere('us1.FullName', 'like', '%'.$request->input('search').'%');
                            }else{
                                if(in_array($key, ['MeetingTimeFrom', 'MeetingTimeTo', 'MeetingDate'])){
                                    $query->orWhereRaw('(DATE_FORMAT(meeting_schedules.MeetingDate,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                                    // echo strpos( $request->input('search'), ':',2);
                                    if($key == 'MeetingTimeFrom' || $key == 'MeetingTimeTo'){ 
                                        $arrfind = explode("-",$request->input('search'));
                                        if(empty($arrfind[1])) {
                                            $strSearch = str_replace(':', '', $arrfind[0]);
                                            if(is_numeric($strSearch)) {
                                                $query->orWhere(function ($query1) use ($strSearch){
                                                    $query1->Where('MeetingTimeFrom', '=', $strSearch)
                                                        ->orWhere('MeetingTimeTo', '=',$strSearch)
                                                        ->orWhereRaw('HOUR(MeetingTimeFrom) = '.$strSearch)
                                                        ->orWhereRaw('HOUR(MeetingTimeTo) = '.$strSearch)
                                                        ->orWhereRaw('MINUTE(MeetingTimeTo) = '.$strSearch)
                                                        ->orWhereRaw('MINUTE(MeetingTimeTo) = '.$strSearch)
                                                    ;
                                                });
                                            }
                                        } else {
                                            $arrfind = explode("-",$request->input('search'));
                                            $strSearch = str_replace(':', '', $arrfind[0]);
                                            if(empty($arrfind[1])){
                                                    $query->orWhere(function ($query1) use ($arrfind){
                                                    $query1->Where('MeetingTimeFrom', '>=', $arrfind[0])
                                                    -> Where('MeetingTimeTo', '<=',$arrfind[1]);
                                                }); 
                                            }
                                            
                                        }
                                    }
                                }else{
                                    $query->orWhere('meeting_schedules.'.$key, 'like', '%'.$request->input('search').'%');
                                }
                            }
                        }
                        
                    });
                }
            }
        }

        //tim kiếm
        foreach($this->data['request'] as $key => $value){
            if(Schema::hasColumn('meeting_schedules', $key) && $value !== null){
                if(!is_array($value)){
                    if($key == 'MeetingHostID'){
                        $meetings = $meetings->where('meeting_schedules.'.$key,$value);
                    } elseif($key == 'Participant'){
                        $meetings = $meetings->where('meeting_schedules.'.$key, 'like', '%,'.$value.',%');
                    } elseif ($key == 'RegisterID') {
                        $meetings = $meetings->where('meeting_schedules.'.$key, 'like', $value);
                    } else{
                        $meetings = $meetings->where('meeting_schedules.'.$key, 'like', '%'.$value.'%');
                    }
                }
                if(is_array($value) && !is_null($value[0]) && !is_null($value[1]) 
                    && \DateTime::createFromFormat('d/m/Y', $value[0]) !== FALSE && \DateTime::createFromFormat('d/m/Y', $value[1]) !== FALSE){
                    $meetings = $meetings->where('MeetingDate', '>=', $this->fncDateTimeConvertFomat($value[0], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))
                        ->where('MeetingDate', '<=', $this->fncDateTimeConvertFomat($value[1], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                }
                else if(is_array($value) && !is_null($value[0]) && is_null($value[1]) && \DateTime::createFromFormat('d/m/Y', $value[0]) !== FALSE){
                    $meetings = $meetings->where('MeetingDate', '>=', $this->fncDateTimeConvertFomat($value[0], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                }
                else if(is_array($value) && is_null($value[0]) && !is_null($value[1]) && \DateTime::createFromFormat('d/m/Y', $value[1]) !== FALSE){
                    $meetings = $meetings->where('MeetingDate', '<=', $this->fncDateTimeConvertFomat($value[1], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                }
                else if(is_array($value) && ($value[0] !== null || $value[1] !== null) && (\DateTime::createFromFormat('d/m/Y', $value[0]) === FALSE || \DateTime::createFromFormat('d/m/Y', $value[1]) === FALSE)){
                    // $meetings = $meetings->where('MeetingDate', '=', $this->fncDateTimeConvertFomat('30/02/2020', self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
                    return Redirect::back()->withErrors(['Ngày tìm kiếm không hợp lệ']);
                }
            }
            if($key == 'Status'){
                if($value == 1){
                    $meetings = $meetings->where('MeetingDate', Carbon::now()->toDateString())
                        ->where('MeetingTimeFrom', '<=' , Carbon::now()->format("H:i"))
                        ->where('MeetingTimeTo', '>=', Carbon::now()->format("H:i"));
                }else if($value == 2){
                    $meetings = $meetings->where(function ($query){
                       $query->where('MeetingDate', '<', Carbon::now()->toDateString())
                           ->orWhere(function ($query2){
                               $query2->where('MeetingDate', '=', Carbon::now()->toDateString())
                                   ->where('MeetingTimeTo', '<', Carbon::now()->format("H:i"));
                           });
                    });

                }else if($value==3){
                    $meetings = $meetings->where(function ($query){
                        $query->where('MeetingDate', '>', Carbon::now()->toDateString())
                            ->orWhere(function ($query2){
                                $query2->where('MeetingDate', '=', Carbon::now()->toDateString())
                                    ->where('MeetingTimeFrom', '>', Carbon::now()->format("H:i"));
                            });
                    });
                } 
            } 
        }
        $ckeck = false;
        if(null === $request->input('Status')){
           $ckeck = true;
        } 
        $ckeck = ($ckeck == 'true'? '0' : $request->input('Status'));
        //phan trang 
        $count = $meetings->count();
        $meetings = $meetings->paginate($recordPerPage);
        foreach($meetings as $meeting){
            //Tien 1/4/2020
            $meeting->register = User::query()->withTrashed()
                    ->where('id', $meeting->RegisterID)          
                    ->first()->FullName;
            $meeting->host = User::query()->withTrashed()
                    ->where('id', $meeting->MeetingHostID)          
                    ->first()->FullName;
            $meeting->roomName = Room::query()->withTrashed()
                    ->where('id', $meeting->RoomID)          
                    ->first()->Name;
            //Tien 1/4/2020
                    
            // $meeting->register = Room::find($meeting->RegisterID)->FullName;
            // $meeting->host = Room::find($meeting->MeetingHostID)->FullName;
            // $meeting->roomName = Room::find($meeting->RoomID)->Name;
            $meeting->diffHours = gmdate('H:i', Carbon::parse($meeting->MeetingTimeFrom)->diffInSeconds($meeting->MeetingTimeTo));;
        }
        $this->data['meetings'] = $meetings;
        $query_string = str_replace($request->url(), '', $request->fullUrl());

        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
        parse_str(str_replace('?', '', $query_string), $query_array);
        if($meetings->count() == 0){
            if(array_key_exists('page', $query_array)){
                if($query_array['page'] > 1){
                    $query_array['page'] = $meetings->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl  = $request->url() . '?' . $query_string;
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
        $this->data['ckeck'] = $ckeck; 
        return $this->viewAdminLayout('meetings', $this->data);
    }

    /**
     * Process insert multiple records,update one records
     * @param Request $request
     * @param null $id
     * @return string|void
     */
    public function store(Request $request, $id=null){
        try{
            if(count($request->input()) >0){
//                return $request->input();
                if(array_key_exists('id', $request->input())){
                    $validator = Validator::make($request->all(),
                        [
//                            'RegisterID'  =>  'required|integer|min:1',
                            'id'    =>  'integer|min:1|nullable',
                            'RoomID'    =>  'required|integer|min:1',
                            'Description'   =>  'string|nullable',
                            'Purpose'   =>  'required|string',
                            'MeetingDate' =>  'required|date_format:d/m/Y',
                            'MeetingTimeFrom'    =>  'date_format:H:i',
                            'MeetingTimeTo'    =>  'date_format:H:i',
                            'Participant'   =>  'required|string|nullable',
                            'MeetingHostID' =>  'required|integer|nullable',
                        ]);
                }else{
                    $validator = Validator::make($request->all(),
                        [
//                            'RegisterID'  =>  'required|integer|min:1',
                            'RoomID'    =>  'required|integer|min:1',
                            'Description'   =>  'string|nullable',
                            'Purpose'   =>  'required|string',
                            'MeetingDate' =>  'required|date_format:d/m/Y',
                            'MeetingTimeFrom'    =>  'date_format:H:i',
                            'MeetingTimeTo'    =>  'date_format:H:i',
                            'Participant'   =>  'required|string|nullable',
                            'MeetingHostID' =>  'required|integer|nullable',
                        ]);
                }
                if ($validator->fails())
                {
                    $failedRules = $validator->failed();
                    if(isset($failedRules['RoomID'])) {
                        return response()->json(['errors'=>['Vui lòng chọn phòng họp']]);
                    }else if(isset($failedRules['Purpose'])){
                        return response()->json(['errors'=>$validator->errors()->all()]);
                    }else if(isset($failedRules['MeetingDate'])) {
                        return response()->json(['errors'=>['Vui lòng chọn ngày họp']]);
                    }else if(isset($failedRules['MeetingTimeFrom'])) {
                        return response()->json(['errors'=>['Vui lòng chọn giờ bắt đầu']]);
                    }else  if(isset($failedRules['MeetingTimeTo'])) {
                        return response()->json(['errors'=>['Vui lòng chọn giờ kết thúc']]);
                    }else  if(isset($failedRules['Participant'])) {
                        return response()->json(['errors'=>['Vui lòng chọn người tham gia']]);
                    }else  if(isset($failedRules['MeetingHostID'])) {
                        return response()->json(['errors'=>['Vui lòng chọn người chủ trì']]);
                    }
                }

                $validated = $validator->validate();
                if(array_key_exists('id', $validated)){
                    $meetingid = MeetingSchedule::query()
                            -> where('id','!=', $validated['id'])
                            ->get();
                }else{
                    $meetingid = MeetingSchedule::query()
                        ->get();
                }
                $arrayParticipanttotal = explode (',',$validated['Participant']);
                $demParticipant=0;
                foreach ($arrayParticipanttotal as $key => $value) {
                    if(is_numeric($value)){
                        $demParticipant =$demParticipant+1;
                    }
                }
                if($demParticipant<2){
                    return $this->jsonErrors('Thành phần tham gia tối thiểu là 2 nhân viên');
                }
                foreach ($meetingid as $value) {
                    $datevalidated = $this->fncDateTimeConvertFomat($validated['MeetingDate'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                    if($validated['RoomID'] == $value['RoomID']){
                   if(strtotime(date(self::FOMAT_DB_YMD, strtotime('now'))) > strtotime($datevalidated)){
                        return $this->jsonErrors('Không thể đặt vào ngày này!');
                   }else{
                        if(strtotime($validated['MeetingTimeFrom'])>strtotime($validated['MeetingTimeTo'])){
                           return $this->jsonErrors('Thời gian họp không thể nhỏ hơn 0'); 
                        }
                        $res= gmdate('H:i', Carbon::parse($validated['MeetingTimeTo'])->diffInSeconds($validated['MeetingTimeFrom']));
                        if(strtotime($res) < strtotime(date('00:15'))){
                             return $this->jsonErrors('Thời gian họp tối thiểu là 15 phút');
                        }
                         if(strtotime($datevalidated) == strtotime($value['MeetingDate'])) {
                            if((strtotime($validated['MeetingTimeFrom'].':00') >= strtotime($value['MeetingTimeFrom'])
                             && strtotime($validated['MeetingTimeTo'].':00') <= strtotime($value['MeetingTimeTo']))
                               ||((strtotime($validated['MeetingTimeFrom'].':00') <=  strtotime($value['MeetingTimeFrom']) )
                            &&(strtotime($validated['MeetingTimeTo'].':00') >=  strtotime($value['MeetingTimeFrom']) )
                            && (strtotime($validated['MeetingTimeTo'].':00') <=  strtotime($value['MeetingTimeTo'])))
                               ||( (strtotime($validated['MeetingTimeFrom'].':00') <=  strtotime($value['MeetingTimeFrom']))
                            && (strtotime($validated['MeetingTimeTo'].':00') >=  strtotime($value['MeetingTimeTo'])))
                                ||( (strtotime($validated['MeetingTimeFrom'].':00') <=  strtotime($value['MeetingTimeTo']) )
                            &&  (strtotime($validated['MeetingTimeFrom'].':00') >=  strtotime($value['MeetingTimeFrom']))
                            &&  (strtotime($validated['MeetingTimeTo'].':00') >=  strtotime($value['MeetingTimeTo'])))
                            )
                            {
                                return $this->jsonErrors('Đã có lịch họp từ ' .$value['MeetingTimeFrom'].' đến '.$value['MeetingTimeTo'] .'. Bạn vui lòng đặt giờ khác!');

                            }

                         }
                   }}
                }

                if(array_key_exists('id', $validated)){
                    $one = MeetingSchedule::find($validated['id']);
                }else{
                    $one = new MeetingSchedule();
                }
                foreach($validated as $key => $value){
                    if(Schema::hasColumn('meeting_schedules', $key))
                        $one->$key = $value;
                }
                if(isset($validated['MeetingDate'])){
                    $one->MeetingDate = $this->fncDateTimeConvertFomat($validated['MeetingDate'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                }
                $one->RegisterID = Auth::user()->id;
                if(array_key_exists('id', $validated)){
                    $arrayid = MeetingSchedule::find($validated['id']);
                    $Participantold = $arrayid->Participant;
                    $MeetingDateold = $this->fncDateTimeConvertFomat($arrayid->MeetingDate, self::FOMAT_DB_YMD, self::FOMAT_DISPLAY_DMY);
                    $RoomIDold = $arrayid->RoomID;
                    $MeetingTimeFromold = $arrayid->MeetingTimeFrom;
                    $MeetingHostIDold = $arrayid->MeetingHostID;
                    $MeetingTimeToold = $arrayid->MeetingTimeTo;
                    $Purposeold = $arrayid->Purpose;
                    $array = explode (',',$validated['Participant']);
                    $arrayold = explode (',',$Participantold);
                    $otherold = array_diff($arrayold, $array);
                    $othernew = array_diff($array, $arrayold);
                }
                $one->save();
                if(array_key_exists('id', $validated)){
                    if(count($otherold) == 0 && count($othernew)== 0)
                    {
                        if($MeetingHostIDold != $validated['MeetingHostID'] ||$Purposeold != $validated['Purpose'] || $MeetingTimeFromold != $validated['MeetingTimeFrom'].':00' || $RoomIDold != $validated['RoomID'] || $MeetingDateold != $validated['MeetingDate'] || $MeetingTimeToold != $validated['MeetingTimeTo'].':00'){
                            $this->sendMail(
                                $validated['Participant'],
                                $validated['MeetingDate'],
                                $validated['RoomID'],
                                $validated['MeetingTimeFrom'],
                                $validated['MeetingTimeTo'],
                                $validated['MeetingHostID'],
                                $validated['Purpose'],
                                'edit',
                                $Participantold
                            );
                        }
                    }else if(count($otherold) != 0 || count($othernew)!= 0){
                        if($MeetingHostIDold != $validated['MeetingHostID'] ||$Purposeold != $validated['Purpose'] || $MeetingTimeFromold != $validated['MeetingTimeFrom'].':00' || $RoomIDold != $validated['RoomID'] || $MeetingDateold != $validated['MeetingDate'] || $MeetingTimeToold != $validated['MeetingTimeTo'].':00'){
                            $this->sendMail(
                                $validated['Participant'],
                                $validated['MeetingDate'],
                                $validated['RoomID'],
                                $validated['MeetingTimeFrom'],
                                $validated['MeetingTimeTo'],
                                $validated['MeetingHostID'],
                                $validated['Purpose'],
                                'edit',
                                $Participantold
                            );
                        }else{
                           $this->sendMail(
                                $validated['Participant'],
                                $validated['MeetingDate'],
                                $validated['RoomID'],
                                $validated['MeetingTimeFrom'],
                                $validated['MeetingTimeTo'],
                                $validated['MeetingHostID'],
                                $validated['Purpose'],
                                'editother',
                                $Participantold
                            );
                        }
                    }
                }else{
                    $this->sendMail(
                        $validated['Participant'],
                        $validated['MeetingDate'],
                        $validated['RoomID'],
                        $validated['MeetingTimeFrom'],
                        $validated['MeetingTimeTo'],
                        $validated['MeetingHostID'],
                        $validated['Purpose'],
                        'add',
                        ''
                    );
                }
                
                return $this->jsonSuccessWithRouter('admin.MeetingSchedules');

            }else{
                return abort('404');
            }
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Show popup insert,update can insert multiple records
     * @param Request $request
     * @param null $oneId
     * @param null $del
     * @return View (meeting-detail)
     */
    public function showDetail(Request $request, $oneId=null, $del=null){
        if($request->exists('copy')){
            $this->data['copy'] = 1;
        }else{
            $this->data['copy'] = 0;
        }
        $this->data['users'] = User::query()
            ->where('deleted','0')
            ->select('id', 'FullName')
            ->where('Active', 1)
            ->where('deleted','!=', 1)
            ->where('role_group', '!=', 1)
            ->get();
        $this->data['meetingRooms'] = Room::query()
            ->where('Active', 1)
            ->where('MeetingRoomFlag', 1)
            ->get();
        $this->data['meeting'] = MeetingSchedule::query()
            ->get();
        if($oneId!=null){
            if($del == 'del'){
                $one = MeetingSchedule::find($oneId);
                $MeetingDate = $this->fncDateTimeConvertFomat($one->MeetingDate, self::FOMAT_DB_YMD, self::FOMAT_DISPLAY_DMY);
                if($one) $one->delete();
                $this->sendMail(
                    $one->Participant,
                    $MeetingDate,
                    $one->RoomID,
                    $one->MeetingTimeFrom,
                    $one->MeetingTimeTo,
                    $one->MeetingHostID,
                    $one->Purpose,
                    'del',
                    ''
                );
                return 1;
            }
            $this->data['itemInfo'] = MeetingSchedule::find($oneId);
            $this->data['itemInfo']->diffHours = gmdate('H:i', Carbon::parse($this->data['itemInfo']->MeetingTimeFrom)->diffInSeconds($this->data['itemInfo']->MeetingTimeTo));;
            if($this->data['itemInfo']){
                return $this->viewAdminIncludes('meeting-detail', $this->data);
            }else{
                return "";
            }
        }else{
            return $this->viewAdminIncludes('meeting-detail', $this->data);
        }

    }

    /**
     * Send data to mail serve
     * @param $array
     * @param $MeetingDate
     * @param $RoomID
     * @param $MeetingTimeFrom
     * @param $MeetingTimeTo
     * @param $MeetingHostID
     * @param $Purpose
     * @param $action
     * @param $Participantold
     */
    public function sendMail($array,$MeetingDate,$RoomID,$MeetingTimeFrom,$MeetingTimeTo,$MeetingHostID,$Purpose,$action,$Participantold){
        $arrMail = [];
        $rooms = Room::find($RoomID);
        $meetingHostID = User::find($MeetingHostID);
        $roomName = Room::find($meetingHostID['RoomId']);
        $array = explode (',',$array);
        $arrayold = explode (',',$Participantold);
        $otherold = array_diff($arrayold, $array);
        $othernew = array_diff($array, $arrayold);
        
        if($action == 'del'){
            $subjectMail = 'TB Hủy lịch họp vào ngày '.$MeetingDate;
        }else if($action == 'edit' || $action == 'editother'){
            $subjectMail = 'TB Thay đổi lịch họp vào ngày '.$MeetingDate;
            if(count($otherold) != 0 || count($othernew)!= 0){
               $array = array_diff($array, $othernew);
            }
        }else{
            $subjectMail = 'TB Mời họp vào ngày '.$MeetingDate;
        }

        if($action == 'edit' || $action == 'editother'){
            $arrOldMailAddressTo = array();
            foreach ($otherold as $value){
                $mailUser = User::find($value);
                $arrOldMailAddressTo[] = $mailUser->email;
            }
            if ($arrOldMailAddressTo && count($arrOldMailAddressTo) > 0) {
                $contentMail = 'Kính gửi Bạn <br/><br/>';
                $contentMail .= "<div style='display:block'>";
                $contentMail .= 'Ông/Bà không cần tham gia cuộc họp. <br/>';
                $contentMail .= 'Tại phòng : '.$rooms['Name'].' <br/>';
                $contentMail .= ' Người chủ trì : '.$meetingHostID['FullName'].' <br/>';
                $contentMail .= ' Nội dung cuộc họp : '.$Purpose.' <br/>';
                $contentMail .= ' Vào ngày : '.$MeetingDate.' <br/>';
                $contentMail .= ' Thời gian họp : '.$MeetingTimeFrom.' đến '.$MeetingTimeTo.' <br/>';
                $contentMail .= "</div>";
                $contentMail .= "Trân trọng.<br/>";

                $this->SendMailHtml($subjectMail,$contentMail, config('mail.from.address'), $meetingHostID['FullName'] . ' - ' . $roomName['Name'], $arrOldMailAddressTo);
            }

            $arrNewMailAddressTo = array();
            foreach ($othernew as $value){
                $mailUser = User::find($value);
                $arrNewMailAddressTo[] = $mailUser->email;
            }
            if ($arrNewMailAddressTo && count($arrNewMailAddressTo)) {
                $subjectMail = 'TB Mời họp vào ngày '.$MeetingDate;
                $contentMail = 'Kính gửi Ông/Bà <br/><br/>';
                $contentMail .= "<div style='display:block'>";
                $contentMail .= 'Ông/Bà được mời dự họp tại phòng : '.$rooms['Name'].' <br/>';
                $contentMail .= ' Người chủ trì : '.$meetingHostID['FullName'].' <br/>';
                $contentMail .= ' Nội dung cuộc họp : '.$Purpose.' <br/>';
                $contentMail .= ' Vào ngày : '.$MeetingDate.' <br/>';
                $contentMail .= ' Thời gian họp : '.$MeetingTimeFrom.' đến '.$MeetingTimeTo.' <br/>';
                $contentMail .= "</div>";
                $contentMail .= "<br/>
                                Kính mong ông bà tham dự đúng giờ.<br/><br/>
                                ";
                $contentMail .= "Trân trọng.<br/>";

                $this->SendMailHtml($subjectMail,$contentMail, config('mail.from.address'), $meetingHostID['FullName'] . ' - ' . $roomName['Name'], $arrNewMailAddressTo);
            }
        } else {
            $arrMailAddressBcc = array();
            foreach ($array as $value){
                if ($value == $MeetingHostID) {
                    continue;
                }
                $mailUser = User::find($value);
                if ($mailUser) {
                    $arrMailAddressBcc[] = $mailUser->email;
                }
            }
            if ($arrMailAddressBcc && count($arrMailAddressBcc)) {
                $contentMail = '';
                $contentMail .= 'Kính gửi Ông/Bà <br/><br/>';
                $contentMail .= "<div style='display:block'>";
                if($action == 'del'){
                    $contentMail .= 'Lịch họp đã bị hủy ! <br/>';
                    $contentMail .= 'Tại phòng : '.$rooms['Name'].' <br/>';
                }else if($action == 'edit'){
                    $contentMail .= 'Lịch họp đã được sửa đổi ! <br/>';
                    $contentMail .= 'Tại phòng : '.$rooms['Name'].' <br/>';
                }
                else{
                    $contentMail .= ' Ông/Bà được mời dự  họp tại phòng : '.$rooms['Name'].' <br/>';
                }

                $contentMail .= ' Người chủ trì : '.$meetingHostID['FullName'].' <br/>';
                $contentMail .= ' Nội dung cuộc họp : '.$Purpose.' <br/>';
                $contentMail .= ' Vào ngày : '.$MeetingDate.' <br/>';
                $contentMail .= ' Thời gian họp : '.$MeetingTimeFrom.' đến '.$MeetingTimeTo.' <br/>';
                $contentMail .= "</div>";
                if($action != 'del'){
                    $contentMail .= "<br/>
                                 Kính mong ông bà tham dự đúng giờ.<br/><br/>
                                ";
                }
                $contentMail .= "Trân trọng.<br/>";
                $this->SendMailHtml($subjectMail, $contentMail, config('mail.from.address'), $meetingHostID['FullName'] . ' - ' . $roomName['Name'], $meetingHostID->email, $arrMailAddressBcc);
            }
        }
    }
}
