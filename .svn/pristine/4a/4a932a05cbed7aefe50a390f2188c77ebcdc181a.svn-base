<?php

namespace App\Http\Controllers\Admin;

use App\Job;
use App\Interview;
use App\Candidate;
use App\Menu;
use App\RoleScreenDetail;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class InterviewJobController extends AdminController
{
    protected $add;
    protected $view;
    protected $edit;
    protected $delete;

    protected $addSchedule;
    protected $viewSchedule;
    protected $editSchedule;
    protected $deleteSchedule;
    const KEYMENU= array(
        "add" => "JobCandidateAdd",
        "view" => "JobCandidate",
        "edit" => "JobCandidateEdit",
        "delete" => "JobCandidateDelete",
        "addSchedule" => "ScheduleAdd",
        "viewSchedule" => "Schedule",
        "editSchedule" => "ScheduleEdit",
        "deleteSchedule" => "ScheduleDelete",
    );
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');
        $array = $this->RoleView('Absences',['CalendarManagement','JobCandidate','Schedule']);
        $this->menu = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if($value == $row->alias)
                    $this->$key = $row;
            } 
        }
    }

    /**
     * get data return view Danh sach cong viec tuyen dung
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function interviewJob(Request $request, $orderBy = 'id', $sortBy = 'desc'){
        $recordPerPage = $this->getRecordPage();
        //list
        if(Schema::hasColumn('jobs',$orderBy)){
            $jobs = Job::orderBy($orderBy, $sortBy);
        }
        else {
            return redirect()->back();
        }
        //tim kiem theo column
        $this->data['request'] = $request->query();
        $one = Job::query()->select('jobs.Name','jobs.Description')->first();
        if($one){
            $one = $one->toArray();
            if(array_key_exists('search', $request->input())){
                $jobs = $jobs->where(function ($query) use ($one, $request){
                    foreach($one as $key=>$value){
                        $query->orWhere($key, 'like', '%'.$request->input('search').'%');
                    }
                });
            }
        }

        $count = $jobs->get()->count();
        //phan trang
        $jobs = $jobs->paginate($recordPerPage);
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);

        //Edit by:24.03.2020 bang -- STT
        $this->data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        //End Edit:24.03.2020

        $this->data['jobs'] = $jobs;
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['sort'] = $sort;
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;

        return $this->viewAdminLayout('interview-job', $this->data);
    }

    /**
     * View detail Job
     * @param null $id
     * @param null $del
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|int|string
     */
    public function showDetail($id = null, $del = null){

        if($id != null){
            $one = Job::find($id);
            if($del == 'del'){
                if($one != null){
                    $one->delete();
                }
                return 1;
            }
            if($one){
                $this->data['jobInfo'] = $one;
                return $this->viewAdminIncludes('job-detail', $this->data);
            }else{
                return "false";
            }
        }else{
            return $this->viewAdminIncludes('job-detail', $this->data);
        }
    }

    /**
     * Insert,Update Job
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse|string|void
     */
    public function storeJob(Request $request, $id = null){
        if (count($request->input()) === 0){
            return abort('404');
        }
        try{
            $arrCheck =[
                'Name'                   =>  'required|string',
                'Description'            =>  'nullable|string',
                'Content'                =>  'required|string',
                'Active'                 =>  'nullable|string',
            ];
            $modeIsUpdate = array_key_exists('id',$request->input());
            if ($modeIsUpdate){
                $arrCheck['id'] = 'integer|min:1';
            }

            $validator = Validator::make($request->all(), $arrCheck);
            if ($validator->fails()) {
                return response()->json(['errors'=>$validator->errors()->first()]);
            }

            $validated = $validator->validate();
            $one = !$modeIsUpdate ? new Job() : Job::find($validated['id']);

            foreach($validated as $key => $value){
                if(Schema::hasColumn('jobs', $key))
                    $one->$key = $value;
            }

            isset($validated['Active']) ? $one->Active = 1 : $one->Active = 0;
            $one->save();
            return response()->json(['success' => route('admin.InterviewJob')]);
        } catch (\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Get data return view Danh sach lịch phong van
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function interviewSchedule(Request $request, $orderBy = 'InterviewDate', $sortBy = 'asc'){
        $recordPerPage = $this->getRecordPage();

        if(Schema::hasColumn('interviews',$orderBy)){
            $schedules = Interview::query()
                ->select('interviews.*','jobs.Name','candidates.FullName')
                ->join('jobs','jobs.id','=','interviews.JobID')
                ->leftJoin('candidates','candidates.id','=','interviews.CandidateID')
//                ->where('Approve', 1)
                ->orderBy($orderBy, $sortBy);
        }
        else {
            return redirect()->back();
        }

        //tim kiem theo column
        $this->data['request'] = $request->query();
        $one = Interview::query()->select('interviews.InterviewDate','interviews.Note as NoteInter','jobs.Name','candidates.FullName')
            ->join('jobs','jobs.id','=','interviews.JobID')
            ->leftJoin('candidates','candidates.id','=','interviews.CandidateID')->first();
        if($one){
            $one = $one->toArray();
            if(array_key_exists('search', $request->input())){
                $schedules = $schedules->where(function ($query) use ($one, $request){
                    foreach($one as $key => $value){
                        if ($key = 'Name'){
                            $query->orWhere('jobs.'.$key, 'like', '%'.$request->input('search').'%');
                        }
                        if ($key = 'FullName'){
                            $query->orWhere('candidates.'.$key, 'like', '%'.$request->input('search').'%');
                        }
                        $query->orWhere($key, 'like', '%'.$request->input('search').'%');
                    }
                });
            }
        }

        //phan trang
        $schedules = $schedules->paginate($recordPerPage);
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
        parse_str(str_replace('?', '', $query_string), $query_array);

        //redirect to the last page if current page has no record
        if($schedules->count() == 0){
            if(array_key_exists('page', $query_array)){
                if($query_array['page'] > 1){
                    $query_array['page'] = $schedules->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl  = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }
            }
        }
        //Edit by:24.03.2020 bang -- STT
        $page = array_key_exists('page', $query_array) ? $query_array['page'] : '';
        $stt = $page ? ($page - 1) * $recordPerPage : '';
        $this->data['stt'] = $stt;
        //End Edit:24.03.2020

        $this->data['schedules'] = $schedules;
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['add']       = $this->addSchedule;
        $this->data['edit']      = $this->editSchedule;
        $this->data['delete']    = $this->deleteSchedule;

        return view('admin.layouts.'.config('settings.template').'.interview-schedule', $this->data);
    }

    public function showScheduleDetail($id = null, $del = null){
        $this->data['jobs']             = Job::query()->select('id','Name')->where('Active',1)->get();
        $this->data['userInters']       = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['Candidate']        = Candidate::query()->get();
        if($id != null){
            $one = Interview::find($id);
            if($del == 'del'){
                if($one != null){
                    $one->delete();
                }
                return 1;
            }
            if($one){
                $this->data['candidateInfo'] = $one;
                if(!is_null($one->UserInterviews))
                {
                    $one->UserInterviews = explode(',', $one->UserInterviews);
                }else{
                    $one->UserInterviews = [];
                }
                return view('admin.includes.schedule-detail', $this->data);
            }else{
                return "";
            }
        }else{
            return view('admin.includes.schedule-detail', $this->data);
        }
    }

    public function actionSchedule($id = null, $del = null){
        if($id != null){
            if($del == 'del'){
                $one = Interview::find($id);
                if($one != null){
                    $one->delete();
                }
                return 1;
            }
            $this->data['candidateInfo'] = Candidate::find($id);
            if($this->data['candidateInfo']){
                return $this->viewAdminIncludes('info-candidate-detail', $this->data);
            }else{
                return "false";
            }
        }else{
            return $this->viewAdminIncludes('info-candidate-detail', $this->data);
        }
    }


    /**
     * Insert update
     *
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse|string|void
     */
    public function storeSchedule(Request $request, $id = null){
        if (count($request->input()) == 0){
            return abort('404');
        }
        try{
            $arrCheck = [
//                'JobID'                    =>  'required|integer|min:1',
//                'CandidateID'              =>  'required|array|min:1',
//                'CandidateID.*'            =>  'required|integer|min:1',
//                'InterviewDate'            =>  'required|array',
//                'InterviewDate.*'          =>  'nullable|date_format:Y/m/d H:i',
//                'Note'                     =>  'nullable|string',
//                'UserInterviews'           =>  'nullable|array',
                'JobID'                    =>  'required|integer|min:1',
                'CandidateID'              =>  'required|integer|min:1',
                'InterviewDate'            =>  'required|date_format:d/m/Y H:i',
                'Note'                     =>  'nullable|string',
                'UserInterviews'           =>  'nullable|array',
            ];

            $isUpdate = array_key_exists('id', $request->input());
            if ($isUpdate){
                $arrCheck['id']  = 'integer|min:1';
            }
            $validator = Validator::make($request->all(),$arrCheck);
            if ($validator->fails()) {
                return response()->json(['errors'=>$validator->errors()->first()]);
            }
            $validated = $validator->validate();
            $one = $isUpdate ? Interview::find($validated['id']) : new Interview();

            foreach($validated as $key => $value){
                if(Schema::hasColumn('interviews', $key)) {
                    if ($key == 'InterviewDate') {
                        $value = $this->fncDateTimeConvertFomat($value, FOMAT_DISPLAY_DATE_TIME, self::FOMAT_DB_YMD_HI);
                    }
                    $one->$key = $value;
                }
            }

            $one->UserInterviews = ','.implode(',', $validated['UserInterviews']).',';
//            $this->sendMail($validated);
            $one->save();
            return response()->json(['success' => route('admin.InterviewJob')]);
        } catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function sendMail($data){
        $arrMail = [];
        foreach ($data['UserInterviews'] as $value){
            $mailUser = User::find($value);
            $arrMailAddressTo = $mailUser->email;

            $arrMail[] = $arrMailAddressTo;
        }
        $subjectMail        = 'TB tham gia phỏng vấn';
        $viewBladeMail      = 'template_mail.interview-mail';
        $dataBinding        = [
                                ];
        $nameFrom           = 'AKB Văn Phòng';
        $arrMailAddressTo   = $arrMail;

        $this->SendMailWithView([
            self::KEY_SUBJECT_MAIL       => $subjectMail,
            self::KEY_VIEW_MAIL          => $viewBladeMail,
            self::KEY_DATA_BINDING       => $dataBinding,
            self::KEY_MAIL_NAME_FROM     => $nameFrom,
            self::KEY_MAIL_ADDRESS_TO    => $arrMailAddressTo,
        ]);
    }

    public function showListCandidate($id = null){
        $this->data['candidates'] = Candidate::query()->where('JobID',$id)->get();
        $this->data['rmb_id'] = $id;
        return $this->viewAdminIncludes('list-candidate-detail', $this->data);
    }

    public function showInfoCandidate($rmb_id = null, $id = null, $del = null){
        $this->data['jobs'] = Job::query()->select('id','Name')->where('id', $rmb_id)->get();
        $this->data['candidateInfo'] = Candidate::query()->where('id',$id)->get();
        $this->data['rmb_id'] = $rmb_id;
        if($id != null){
            if($del == 'del'){
                $one = Candidate::find($id);
                if($one != null){
                    $one->delete();
                }
                return 1;
            }
            $this->data['candidateInfo'] = Candidate::find($id);
            if($this->data['candidateInfo']){
                return $this->viewAdminIncludes('info-candidate-detail', $this->data);
            }else{
                return "";
            }
        }else{
            return $this->viewAdminIncludes('info-candidate-detail', $this->data);
        }
    }

    /**
     * Insert,Update thong tin ung vien
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse|string|void
     */
    public function storeCandidate(Request $request, $id = null){
        if (count($request->input()) === 0){
            return abort('404');
        }
        try{
            $arrCheck =[
                'JobID'                 => 'nullable|string',
                'CVpath'                => 'required|string',
                'FullName'              => 'required|string',
                'Email'                 => 'required|string|email',
                'Tel'                   => 'required|regex:/^([0-9\s\-\+\(\)]*)$/||min:10|max:12',
                'Birthday'              => 'required|date_format:d/m/Y|before:15 years ago',
                'PerAddress'            => 'required|string',
                'CurAddress'            => 'required|string',
                'Note'                  => 'nullable|string',
            ];
            $message = [
                'CVpath.required'       => 'Vui lòng tải một bản CV.',
                'Birthday.before'       => 'Ngày sinh không hợp lệ.',
                'PerAddress.required'   => 'Vui lòng điền hộ khẩu.',
                'CurAddress.required'   => 'Vui lòng điền nơi ở hiện tại.',
            ];
            $modeIsUpdate = array_key_exists('id',$request->input());

            if ($modeIsUpdate){
                $arrCheck['id'] = 'integer|min:1';
            }

            $validator = Validator::make($request->all(), $arrCheck, $message);

            if ($validator->fails())
            {
                return response()->json(['errors'=>$validator->errors()->first()]);
            }

            $validated = $validator->validate();

            $one = !$modeIsUpdate ? new Candidate() : Candidate::find($validated['id']);

            foreach($validated as $key => $value){
                if(Schema::hasColumn('candidates', $key))
                    if ($key == 'Birthday'){
                        $value = $this->fncDateTimeConvertFomat($value, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                    }
                    $one->$key = $value;
            }

            $one->save();
            return response()->json(['success' => route('admin.CandidateList')]);
        } catch (\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Get user of job
     *
     * @param null $id
     * @return mixed
     */
    public function getUserOfJob($id = null){
        $this->data['jobs'] = Job::query()->select('id','Name')->get();
        $this->data['candidates'] = Candidate::query()->select('candidates.id','candidates.FullName')->where('JobID',$id)->get();
        return $this->data;
    }

    /**
     * change active screen job interview
     *
     * @param $id
     * @param $active
     */
    public function changerCheckboxActive($id, $active){
        if ($id != ''){
            $jobs = Job::find($id);
            $jobs->Active = $active;
            $jobs->save();
        }
    }

    /**
     * change approved screen schedule interview
     *
     * @param $id
     * @return bool
     */
    public function changeApproveSchedule($id){
        if ($id != ''){
            $interviewSchedule = Interview::find($id);
            $interviewSchedule->Approve = 1;
            $interviewSchedule->save();
        }else{
            return false;
        }
    }

    /**
     * Download cv
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function routeDownloadCV(Request $request){

        if ($request['path'] != '' || $request['path'] != null){
            if (file_exists($request['path'])) {
                return Response::download($request['path']);
            }
            return response()->json(['errors' => ['Tài liệu không tồn tại']]);
        }
        return response()->json(['errors' => ['Tài liệu không tồn tại']]);
    }
}
