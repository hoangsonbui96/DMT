<?php

namespace Modules\Recruit\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Recruit\Entities\Candidate;
use Modules\Recruit\Entities\InterviewJob;
use Illuminate\Routing\Controller;
use App\Http\Controllers\Admin\AdminController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Recruit\Entities\Interview;
use Illuminate\Support\Facades\Mail;
use Modules\Recruit\Emails\SendMailInteview;
use App\Menu;
use App\MasterData;
use Illuminate\Support\Arr;

class CandidatesController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $download;

    const KEYMENU = array(
        "add" => "CandidateScheduleAdd",
        "view" => "CandidateSchedule",
        "edit" => "CandidateScheduleEdit",
        "delete" => "CandidateScheduleDelete",
        'download' => "CandidateScheduleDownload",
    );

    const REFUSED_TO_INTERVIEW = 1;
    const ACCEPT_INTERVIEW = 2;
    const REFUSE_TO_WORK = 3;

    public function __construct(Request $request)
    {
        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }

        $array = $this->RoleView('candidates.list', ['CandidateSchedule']);
        $this->data['menu'] = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if ($value == $row->alias)
                    $this->$key = $row;
            }
        }
    }

    public function candidateList(Request $request)
    {
        if ($request->ajax()) {
            $paginate = 20;
            $search = $request->search;
            $time_interview = $request->interview_time;
            $evaluation = $request->evaluation;
            $approve = $request->approve;
            $job_id = (int)$request->job_id;
            $interviewJob = (int)$request->interviewJob;
            $order_by = $request->order_by;
            $sort_by = $request->sort_by;
            $on = 1;
            if ($job_id != 0) {
                $job_name = InterviewJob::get_name($job_id);
                $candidate = Candidate::select('candidates.FullName', 'candidates.Email', 'candidates.Tel', 'candidates.CVpath', 'candidates.id', 'candidates.JobID', 'candidates.Experience', 'candidates.Note','candidates.Status', 'candidates.updated_at', 'jobs.Name', 'interviews.InterviewDate', 'interviews.Evaluate', 'interviews.Approve', 'interviews.id as interview_id')
                    ->leftjoin('jobs', 'jobs.id', '=', 'candidates.jobID')
                    ->leftjoin('interviews', 'interviews.CandidateID', '=', 'candidates.id')
                    ->where('candidates.jobID', $job_id);
                if ($search != '') {
                    $candidate->where('candidates.FullName', 'like', '%' . $search . '%');
                }
                if ($time_interview != '') {
                    $true = '1';
                    if ($time_interview == $true) {
                        $candidate->where('interviews.InterviewDate', '<>', Null);
                    } else {
                        $candidate->where('interviews.InterviewDate', Null);
                    }
                }

                if ($evaluation != '') {
                    $has_been = '1';
                    if ($evaluation == $has_been) {
                        $candidate->where('interviews.Evaluate', '<>', Null);
                    } else {
                        $candidate->where('interviews.Evaluate', Null);
                    }
                }
                if ($approve != '') {
                    $failed = 2;
                    if($approve != 0){
                        $candidate->where('interviews.Approve', $approve);
                    } else{
                        $candidate->where('candidates.Status', $failed);
                    }
                }
                $candidates = $candidate->orderBy($order_by, $sort_by)->paginate($paginate);
            } else {
                $job_name = null;
                $interviewJob = (int)$request->interviewJob;
                $candidate = Candidate::select('candidates.FullName', 'candidates.Email', 'candidates.Tel', 'candidates.CVpath', 'candidates.id', 'candidates.JobID', 'candidates.Experience', 'candidates.Note','candidates.Status', 'candidates.updated_at', 'jobs.Name', 'interviews.InterviewDate', 'interviews.Evaluate', 'interviews.Approve', 'interviews.id as interview_id')
                    ->leftjoin('jobs', 'jobs.id', '=', 'candidates.jobID')
                    ->leftjoin('interviews', 'interviews.CandidateID', '=', 'candidates.id');
                if ($search != '') {
                    $candidate->where('candidates.FullName', 'like', '%' . $search . '%');
                }
                if ($time_interview != '') {
                    $true = '1';
                    if ($time_interview == $true) {
                        $candidate->where('interviews.InterviewDate', '<>', Null);
                    } else {
                        $candidate->where('interviews.InterviewDate', Null);
                    }
                }

                if ($evaluation != '') {
                    $has_been = '1';
                    if ($evaluation == $has_been) {
                        $candidate->where('interviews.Evaluate', '<>', Null);
                    } else {
                        $candidate->where('interviews.Evaluate', Null);
                    }
                }
                if ($approve != '') {
                    $failed = 2;
                    if($approve != 0){
                        $candidate->where('interviews.Approve', $approve);
                    } else{
                        $candidate->where('candidates.Status', $failed);
                    }
                }

                if ($interviewJob != '') {
                    $candidate->where('candidates.jobID', $interviewJob);
                }
                $candidates = $candidate->orderBy($order_by, $sort_by)->paginate($paginate);
            }
            $add = $this->add;
            $edit = $this->edit;
            $delete = $this->delete;
            $download = $this->download;
            $view = view('recruit::candidate.candidateLoad', compact('job_name', 'candidates','interviewJob', 'job_id', 'add', 'edit', 'delete', 'download'))->render();
            $paginate = $candidates->links()->render();
            $data = [
                'view' => $view,
                'pagination' => $paginate,
            ];
            return response()->json($data);
        } else {
            $jobId = isset($request->jobId) ? (int)$request->jobId : null;
            $paginate = 20;
            $on = 1;
            if ($jobId != null) {
                $this->data['job_name'] = InterviewJob::get_name($jobId);
                $this->data['candidates'] = Candidate::getCandidateByJobId($jobId, $paginate);
            } else {
                $this->data['job_name'] = null;
                $this->data['list_job_interview'] = InterviewJob::get(['id', 'name']);
                $this->data['candidates'] = Candidate::getCandidateByJobId(null, $paginate);
            }
            $this->data['jobId'] = $jobId;
            $this->data['add'] = $this->add;
            $this->data['edit'] = $this->edit;
            $this->data['delete'] = $this->delete;
            $this->data['download'] = $this->download;
            return view('recruit::candidate.candidateList', $this->data);
        }
    }

    public function candidateAdd(Request $request)
    {
        if ($request->ajax()) {
            $jobId = (int)$request->job_id;
            $list_job_interview = InterviewJob::get_interviewJob_by_id($jobId);
            $apply_position = MasterData::select('id', 'DataKey', 'Name', 'DataValue')->where('Datakey', 'VTUT')->get();
            $data = view('recruit::candidate.candidateAdd', compact('list_job_interview', 'jobId', 'apply_position'))->render();
            return response()->json($data);
        } else {
            return redirect()->route('admin.candidates.list');
        }
    }

    public function candidateStore(Request $request)
    {
        if ($request->ajax()) {
            if (count($request->input()) === 0) {
                return abort('404');
            }

            $validator = Validator::make($request->all(), [
                'interviewJob' => 'required',
                'CVpath' => 'required|mimes:pdf|max:10000',
                'FullName' => 'bail|required|string|max:100',
                'Email' => 'bail|required|email',
                'Tel' => 'bail|required|regex:/^([0-9\s\-\+\(\)]*)$/||min:10|max:12',
                'Birthday' => 'string|nullable',
                'PerAddress' => 'string|nullable|max:190',
                'CurAddress' => 'string|nullable|max:190',
                'Note' => 'string|nullable|max:190',
                'Experience' => 'bail|required|numeric|min:0',
                'ApplyPosition' => 'bail|required|string|max:100',
            ]);

            if ($validator->fails()) {
                $error = collect($validator->errors())->first();
                return response()->json(['errors' => $error]);
            } else {
                try {
                    $file = $request->CVpath;
                    $file_name = $file->getClientOriginalName();
                    $file_extension = $file->getClientOriginalExtension();
                    if (strcasecmp($file_extension, 'pdf') === 0) {
                        $id_upload = rand(0, 1000);
                        $name =  now()->format('Y_m_d') . "_" . $id_upload . "." . $file_extension;
                        $file->move(storage_path('app/public/cv/'), $name);
                    }

                    if ($request->Birthday != null) {
                        $birthday = Carbon::createFromFormat('d/m/Y', $request->Birthday)->format('Y-m-d');
                    } else {
                        $birthday = null;
                    }

                    $data = [
                        'FullName' => $request->FullName,
                        'JobId' => (int)$request->interviewJob,
                        'CVpath' => $name,
                        'Email' => $request->Email,
                        'Tel' => $request->Tel,
                        'Birthday' => $birthday,
                        'PerAddress' => $request->PerAddress,
                        'CurAddress' => $request->CurAddress,
                        'Note' => $request->Note,
                        'Experience' => (int)$request->Experience,
                        'ApplyPosition' => $request->ApplyPosition,
                    ];
                    Candidate::create($data);
                    return response()->json(['success' => trans('admin.success.save')]);
                } catch (\Exception $e) {
                    return response()->json(['errors' => trans('admin.error.save')]);
                }
            }
        }
    }

    public function candidateEdit(Request $request)
    {
        if ($request->ajax()) {
            $jobId = 0;
            $id = (int)$request->id;
            $list_job_interview = InterviewJob::get_interviewJob_by_id($jobId);
            $candidate = Candidate::find($id);
            $apply_position = MasterData::select('id', 'DataKey', 'Name', 'DataValue')->where('Datakey', 'VTUT')->get();
            $birthday = FomatDateDisplay($candidate->Birthday, 'd/m/Y');
            $data = view('recruit::candidate.candidateEdit', compact('list_job_interview', 'candidate', 'birthday', 'apply_position'))->render();
            return response()->json($data);
        } else {
            return redirect()->route('admin.candidates.list');
        }
    }

    public function candidateUpdate(Request $request)
    {
        if ($request->ajax()) {
            if (count($request->input()) === 0) {
                return abort('404');
            }

            $validator = Validator::make($request->all(), [
                'interviewJob' => 'required',
                'CVpath' => 'mimes:pdf|max:10000',
                'FullName' => 'bail|required|string|max:100',
                'Email' => 'bail|required|email',
                'Tel' => 'bail|required|regex:/^([0-9\s\-\+\(\)]*)$/||min:10|max:12',
                'Birthday' => 'string|nullable',
                'PerAddress' => 'string|nullable|max:190',
                'CurAddress' => 'string|nullable|max:190',
                'Note' => 'string|nullable|max:190',
                'Experience' => 'bail|required|numeric|min:0',
                'ApplyPosition' => 'bail|required|string|max:100',
            ]);

            if ($validator->fails()) {
                $error = collect($validator->errors())->first();
                return response()->json(['errors' => $error]);
            } else {
                try {
                    if ($request->Birthday != null) {
                        $birthday = Carbon::createFromFormat('d/m/Y', $request->Birthday)->format('Y-m-d');
                    } else {
                        $birthday = null;
                    }
                    $id = (int)$request->candidate_id;
                    $old_CVpath = Candidate::find($id)->CVpath;
                    if ($request->CVpath != null) {
                        if (File::exists(storage_path('app/public/cv/' . $old_CVpath))) {
                            unlink(storage_path('app/public/cv/' . $old_CVpath));
                        }
                        $file = $request->CVpath;
                        $file_name = $file->getClientOriginalName();
                        $file_extension = $file->getClientOriginalExtension();
                        if (strcasecmp($file_extension, 'pdf') === 0) {
                            $id_upload = rand(0, 1000);
                            $name =  now()->format('Y_m_d') . "_" . $id_upload . "." . $file_extension;
                            $file->move(storage_path('app/public/cv/'), $name);
                        }
                        $cv_path = $name;
                    } else {
                        $cv_path = $old_CVpath;
                    }

                    $data = [
                        'FullName' => $request->FullName,
                        'JobId' => (int)$request->interviewJob,
                        'Email' => $request->Email,
                        'CVpath' => $cv_path,
                        'Tel' => $request->Tel,
                        'Birthday' => $birthday,
                        'PerAddress' => $request->PerAddress,
                        'CurAddress' => $request->CurAddress,
                        'Note' => $request->Note,
                        'Experience' => (int)$request->Experience,
                        'ApplyPosition' => $request->ApplyPosition,
                    ];
                    Candidate::where('id', $id)->update($data);
                    return response()->json(['success' => trans('admin.success.save')]);
                } catch (\Exception $e) {
                    return response()->json(['errors' => trans('admin.error.save')]);
                }
            }
        }
    }

    public function candidateDelete(Request $request)
    {
        if ($request->ajax()) {
            $id = (int)$request->id;
            try {
                $file = Candidate::find($id)->CVpath;
                if (file_exists(storage_path('app/public/cv/' . $file))) {
                    unlink(storage_path('app/public/cv/' . $file));
                }
                Interview::where('CandidateId', $id)->delete();
                Candidate::where('id', $id)->delete();
                return response()->json(['success' => trans('admin.success.delete')]);
            } catch (\Exception $e) {
                return response()->json(['errors' => trans('admin.error.delete')]);
            }
        } else {
            return redirect()->route('admin.candidates.list');
        }
    }

    public function candidateDownload(Request $request)
    {
        $file = $request->file;

        if (file_exists(storage_path('app/public/cv/' . $file))) {
            return response()->download(storage_path('app/public/cv/' . $file));
        }

        return $request->ajax() 
            ? abort(404)
            : redirect()->route('admin.candidates.list');
    }

    public function candidateGetCV(Request $request)
    {
        $pathFile = storage_path('app/public/cv/' . $request->file);

        if (file_exists($pathFile)) {
            return response()->file($pathFile);
        }
    }

    public function candidateCheckCV(Request $request)
    {
        $file = $request->file;

        if (file_exists(storage_path('app/public/cv/' . $file))) {
            return response()->file(storage_path('app/public/cv/' . $file));
        }

        return $request->ajax() 
            ? abort(404)
            : redirect()->route('admin.candidates.list');
    }

    public function candidateShowCV($jobId, $candidate_id){
        $CV = Candidate::where('id', $candidate_id);
        $CVpath = $CV->value('CVpath');
        $CV = $CV->first();
        $interviewJob = Interview::where('CandidateID', $candidate_id)
            ->select('id','CandidateID','JobID','InterviewDate','Note','Evaluate','Approve')
            ->first();
        if($interviewJob){
            $interviewJob['InterviewDate'] = FomatDateDisplay($interviewJob['InterviewDate'], 'd/m/Y H:i');
            $this->data['interviewJob'] = $interviewJob;
        }else{
            $this->data['interviewJob'] = '';
        }
        if (file_exists(storage_path('app/public/cv/' . $CVpath))) {
            $this->data['cv_path'] = $CVpath;
            $this->data['candidate'] = $CV;
            // dd($this->data);
            return view('recruit::candidate.candidateCV', $this->data);
        } else {
            return abort(404);
        }
    }

    public function candidateDecideCV(Request $request){
        try {
            $data = [
                'Status' => $request->status,
            ];
            if (array_key_exists('sendMail', $request->input())){
                $info_candidate = Candidate::find($request->CandidateID);
                $applyPosition = MasterData::select('Name')->where('DataValue', $info_candidate->ApplyPosition)->first()->Name;
                $data_mail = [
                    'email' => $info_candidate->Email,
                    'fullname' => $info_candidate->FullName,
                    'time_interview' => null,
                    'applyPosition' => $applyPosition,
                    'typeSendMail' => self::REFUSED_TO_INTERVIEW,
                ];
                $this->sendMailCandidate($data_mail);
            }
            Candidate::find($request->CandidateID)->update($data);
            Interview::where('CandidateID', $request->CandidateID)->delete();
            return response()->json(['success' => trans('admin.success.save')]);
        } catch (\Exception $e) {
            return response()->json(['errors' => trans('admin.error.save')]);
        }
    }

    public function interviewSheduleAdd(Request $request)
    {
        if ($request->ajax()) {
            $job_id = (int)$request->job_id;
            $candidate_id = (int)$request->candidate_id;
            $interviewJob = InterviewJob::select('id', 'Name')->where('id', $job_id)->first();
            $candidate = Candidate::select('id', 'FullName','Status')->where('id', $candidate_id)->first();
            $data = view('recruit::candidate.sheduleAdd', compact('interviewJob', 'candidate'))->render();
            return response()->json($data);
        } else {
            return redirect()->route('admin.candidates.list');
        }
    }

    public function interviewSheduleStore(Request $request)
    {
        if ($request->ajax()) {
            if (count($request->input()) === 0) {
                return abort('404');
            }

            $validator = Validator::make($request->all(), [
                'InterviewDate' => 'required|string',
                'Note' => 'max:190',
            ]);

            if ($validator->fails()) {
                $error = collect($validator->errors())->first();
                return response()->json(['errors' => $error]);
            } else {
                try {
                    $sendMail = (int)$request->sendMail;
                    $interviewDate = Carbon::createFromFormat('d/m/Y H:i', $request->InterviewDate)->format('Y-m-d H:i');
                    $status_unfinished = 0;
                    $data = [
                        'JobID' => (int)$request->JobID,
                        'CandidateID' => (int)$request->CandidateID,
                        'InterviewDate' => $interviewDate,
                        'Note' => $request->Note,
                        'Status' => $status_unfinished
                    ];
                    Interview::create($data);

                    $status_succes = 1;
                    $dataCandidate = [
                        'Status' => $status_succes,
                    ];

                    Candidate::find($request->CandidateID)->update($dataCandidate);
                    if ($sendMail == 1) {
                        // Send mail
                        $info_candidate = Candidate::find($request->CandidateID);
                        $data_interview = Carbon::createFromFormat('d/m/Y H:i', $request->InterviewDate);
                        $name_day = Carbon::parse($data_interview)->locale('vi')->isoFormat('dddd');
                        $day = Carbon::parse($data_interview)->locale('vi')->isoFormat('LL');
                        $hours = Carbon::parse($data_interview)->locale('vi')->isoFormat('HH:mm');
                        $time_interview = ucfirst($name_day) . "," . $hours . " ngày " . $day;
                        $applyPosition = MasterData::select('Name')->where('DataValue', $info_candidate->ApplyPosition)->first()->Name;
                        $data_mail = [
                            'email' => $info_candidate->Email,
                            'fullname' => $info_candidate->FullName,
                            'time_interview' => $time_interview,
                            'applyPosition' => $applyPosition,
                            'typeSendMail' => self::ACCEPT_INTERVIEW,
                        ];
                        $this->sendMailCandidate($data_mail);
                    }
                    return response()->json(['success' => trans('admin.success.save')]);
                } catch (\Exception $e) {
                    return response()->json(['errors' => trans('admin.error.save')]);
                }
            }
        }
    }

    public function interviewSheduleEdit(Request $request)
    {
        if ($request->ajax()) {
            $interview_id = (int)$request->interview_id;
            $job_id = (int)$request->job_id;
            $candidate_id = (int)$request->candidate_id;
            $interviewJob = InterviewJob::select('id', 'Name')->where('id', $job_id)->first();
            $candidate = Candidate::select('id', 'FullName')->where('id', $candidate_id)->first();
            $interview = Interview::find($interview_id);
            $interviewDate = FomatDateDisplay($interview->InterviewDate, 'd/m/Y H:i');
            $data = view('recruit::candidate.sheduleEdit', compact('interviewJob', 'candidate', 'interview', 'interviewDate'))->render();
            return response()->json($data);
        } else {
            return redirect()->route('admin.candidates.list');
        }
    }

    public function interviewSheduleUpdate(Request $request)
    {
        if ($request->ajax()) {
            if (count($request->input()) === 0) {
                return abort('404');
            }

            $validator = Validator::make($request->all(), [
                'InterviewDate' => 'required|string',
                'Note' => 'max:190',
                'Evaluate' => 'max:190',
            ]);

            if ($validator->fails()) {
                $error = collect($validator->errors())->first();
                return response()->json(['errors' => $error]);
            } else {
                try {
                    $id = (int)$request->interview_id;
                    $sendMail = (int)$request->sendMail;
                    $interviewDate = Carbon::createFromFormat('d/m/Y H:i', $request->InterviewDate)->format('Y-m-d H:i');
                    $data = [
                        'InterviewDate' => $interviewDate,
                        'Note' => $request->Note,
                        'Evaluate' => $request->Evaluate,
                        'Approve' => $request->approve,
                    ];
                    Interview::where('id', $id)->update($data);
                    if ($sendMail == 1) {
                        // Send mail
                        $info_candidate = Candidate::find($request->CandidateID);
                        $data_interview = Carbon::createFromFormat('d/m/Y H:i', $request->InterviewDate);
                        $name_day = Carbon::parse($data_interview)->locale('vi')->isoFormat('dddd');
                        $day = Carbon::parse($data_interview)->locale('vi')->isoFormat('LL');
                        $hours = Carbon::parse($data_interview)->locale('vi')->isoFormat('HH:mm');
                        $time_interview = ucfirst($name_day) . "," . $hours . " ngày " . $day;
                        $applyPosition = MasterData::select('Name')->where('DataValue', $info_candidate->ApplyPosition)->first()->Name;
                        $data_mail = [
                            'email' => $info_candidate->Email,
                            'fullname' => $info_candidate->FullName,
                            'time_interview' => $time_interview,
                            'applyPosition' => $applyPosition,
                        ];
                        if((int)$request->approve == 2){
                            $data_mail = Arr::add($data_mail, 'typeSendMail', self::REFUSE_TO_WORK);
                        }else{
                            $data_mail = Arr::add($data_mail, 'typeSendMail', self::ACCEPT_INTERVIEW);
                        }
                        $this->sendMailCandidate($data_mail);
                    }
                    return response()->json(['success' => trans('admin.success.save')]);
                } catch (\Exception $e) {
                    return response()->json(['errors' => trans('admin.error.save')]);
                }
            }
        }
    }

    public function sendMail($array, $comment = null, $int = null)
    {
        $mail_candidate = $array['email'];
        $fullname = $array['fullname'];
        $time_interview = $array['time_interview'];
        $applyPosition = $array['applyPosition'];
        $comment = null;
        $dataBinding = [
            'fullname' => $fullname,
            'time_interview' => $time_interview,
            'applyPosition' => $applyPosition,
            'comment' => $comment
        ];
        $subjectMail = "[AKB]TB Mời Phỏng Phấn";
        $viewBladeMail = 'template_mail.interview-mail';
        $nameFrom = 'AKB Văn Phòng';
        $arrMailAddressTo = $mail_candidate;
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

    public function sendMailCandidate($array)
    {
        $typeSendMail = $array['typeSendMail'];
        $email = $array['email'];
        $fullname = $array['fullname'];
        $time_interview = $array['time_interview'];
        $applyPosition = $array['applyPosition'];
        if($typeSendMail == self::REFUSED_TO_INTERVIEW){
            $subject = "[AKB - $fullname] Thông báo đã nhận CV ";
        } else if($typeSendMail == self::ACCEPT_INTERVIEW){
            $subject = "[AKB - $fullname] Thư mời phỏng vấn!";
        } else {
            $subject = "[AKB - $fullname] Thông báo kết quả phỏng vấn";
        }
        $mail = MasterData::select('DataDescription')->where('DataValue', 'EM007')->first()->DataDescription;
        $email_cc = explode(',', $mail);
        $data = [
            'fullname' => $fullname,
            'time_interview' => $time_interview,
            'applyPosition' => $applyPosition,
            'typeSendMail' => $typeSendMail,
        ];
        Mail::to($email)
            ->cc($email_cc)
            ->send(new SendMailInteview($data, $subject));
    }
}
