<?php

namespace App\Http\Controllers\Admin;

use App\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MasterData;
use App\Project;
use App\TaskHistory;
use App\TaskWorkHistory;
use App\User;
use App\WorkList;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Work;
use App\WorkTable;
use App\WorkType;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class TaskController extends Controller
{
    protected $startTime;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');
        $this->startTime = "08:00";
    }


    public function show(Request $request, $orderBy = 'id', $sortBy = 'desc'){

        $recordPerPage = config('settings.records_per_page');

        //list users
        if(Schema::hasColumn('work_tables',$orderBy)){
            $list = WorkTable::orderBy($orderBy, $sortBy);
        }
        else
        {
            return redirect()->back();

        }

        //tim kiem theo column
        $this->data['request'] = $request->query();
        $one = WorkTable::query()->first();
        if($one){
            $one = $one->toArray();
            if(array_key_exists('search', $request->input())){
                $list = $list->where(function ($query) use ($one, $request){
                    foreach($one as $key=>$value){
                        $query->orWhere($key, 'like', '%'.$request->input('search').'%');
                    }
                });

            }
        }

        $this->data['projects'] = Project::query()
            ->where('Active', 1)
            ->get();

        //phan trang
        $list = $list->paginate(100);

        $this->data['list'] = $list;
        $query_string = str_replace($request->url(), '', $request->fullUrl());

        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
        parse_str(str_replace('?', '', $query_string), $query_array);
        $this->data['query_array'] = $query_array;

        $this->data['sort_link'] = $sort_link;


        return view('admin.layouts.'.config('settings.template').'.work-tables', $this->data);
    }
    public function store(Request $request, $id=null){
        try{
            if(count($request->input()) >0){
//                return $request->input();
                if(array_key_exists('id', $request->input())){
                    $validator = Validator::make($request->all(),
                        [
                            'Name'  =>  'required|string|max:100',
                            'id'    =>  'integer|min:1|nullable',
                            'Active' =>  'string|nullable',
                            'MeetingRoomFlag'    =>  'string|nullable',

                        ]);
                }else{
                    $validator = Validator::make($request->all(),
                        [
                            'Name'  =>  'required|string|max:100',
                            'Active' =>  'string|nullable',
                            'MeetingRoomFlag'    =>  'string|nullable',
                        ]);
                }

                if ($validator->fails())
                {
                    return response()->json(['errors'=>$validator->errors()->all()]);
                }

                $validated = $validator->validate();

                if(array_key_exists('id', $validated)){
                    $one = Task::find($validated['id']);
                }else{
                    $one = new Task();
                }
                foreach($validated as $key => $value){
                    if(Schema::hasColumn('tasks', $key))
                        $one->$key = $value;
                }
                if(isset($validated['Active'])){
                    $one->Active = 1;
                }else{
                    $one->Active = 0;
                }


                $one->save();
                return response()->json(['success' => route('admin.Rooms')]);

            }else{
                return abort('404');
            }
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }
    public function showDetail($roomId=null, $del=null){
        if($roomId!=null){
            if($del == 'del'){
                $one = Task::find($roomId);
                if($one) $one->delete();
                return 1;
            }
            $this->data['itemInfo'] = Task::find($roomId);

            if($this->data['itemInfo']){
                return view('admin.includes.task-detail', $this->data);
            }else{
                return "";
            }
        }else{
            return view('admin.includes.task-detail', $this->data);
        }

    }

    public function showTasksInProject($projectId = null)
    {
        $oneProject = WorkTable::find($projectId);
        if(!$oneProject) return abort('404');
        $this->data['workList'] = WorkList::query()
            ->where('table_id', $projectId)
            ->orderBy('order', 'asc')
            ->get();
        $this->data['projectId'] = $projectId;
        foreach($this->data['workList'] as $item){
            $item->works = Work::query()
                ->where('work_list_id', $item->id)
                ->orderBy('order', 'asc')
                ->get();
        }
        $one = TaskWorkHistory::query()
        ->where('user_id', Auth::user()->id)
        ->orderBy('created_at', 'desc')
        ->first();
        if($one){
            //kiem tra xem ngay hom nay da khai bao chua, neu co thi truyen ngay gio de khoi phai nhap lai
            if(Carbon::now()->startOfDay() == Carbon::parse($one->date))
            $this->data['lastTaskHistory'] = $one;
        }
        return view('admin.layouts.'.config('settings.template').'.project-tasks', $this->data);
    }

    //ajax

    public function newWorkList(Request $request){
        // return $request->input();
        $validator = Validator::make($request->all(), [
            'name'  =>   'required|string|max:50',
            'projectId'    =>  'required|integer'
        ]);

        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()->all()]);
        }
        $validated = $validator->validate();
        $project = WorkTable::find($validated['projectId']);
        if($project){
            $last = WorkList::query()
            ->where('table_id', $project->id)
            ->orderBy('order', 'desc')
            ->first();
            if(!$last) $order = 1;
            else $order = $last->order + 1;
            $newWorkList = new WorkList();
            $newWorkList->name = $validated['name'];
            $newWorkList->table_id = $project->id;
            $newWorkList->order = $order;
            $newWorkList->created_user_id = Auth::user()->id;
            $newWorkList->save();
            return $newWorkList;
        }else{
            return 0;
        }

        // $newWorkList->project_id =
    }

    //update work list order
    public function updateWorkListOrder(Request $request){
        $order = 1;
        DB::beginTransaction();
        foreach($request->input('id') as $value){
            $one = WorkList::find($value);
            if(!$one){
                DB::rollback();
                exit();
            }

            $one->order = $order;
            $one->save();
            $order++;
        }
        DB::commit();
    }
    //update work list title
    public function updateWorkListTitle(Request $request){
        $validator = Validator::make($request->input(),[
            'id'    =>  'required|integer',
            'title' =>  'required|string|max:50',
        ]);
        $validated = $validator->validate();

        DB::beginTransaction();
        $one = WorkList::find($validated['id']);
        if($one){
            $one->name = $validated['title'];
            $one->save();
        }
        DB::commit();
    }

    //them việc mới

    public function newWork(Request $request){
        // return $request->input();

        $validator = Validator::make($request->input(),[
            'workList'    =>  'required|integer',
            'work' =>  'required|string|max:100',
        ]);
        $validated = $validator->validate();
        DB::beginTransaction();
        $last = Work::query()
            ->where('work_list_id', $validated['workList'])
            ->orderBy('order', 'desc')
            ->first();
        if(!$last) $order = 1;
        else $order = $last->order + 1;
        $one = new Work();
        if($one){
            $one->name = $validated['work'];
            $one->work_list_id = $validated['workList'];
            // $one->assigned_user_id = '';
            $one->order = $order;
            $one->created_user_id = Auth::user()->id;
            $one->save();
            return $one;
        }
        DB::commit();

    }

    //update work order, drag and drop giữa các work list
    public function updateWorkOrder(Request $request){
        // return $request->input();
        $order = 1;
        $workList = WorkList::find($request->input('workList'));
        if(!$workList) return 1;
        try{
            DB::beginTransaction();
            $one = Work::find($request->input('work'));
            if(!$one) return 2;
            $one->work_list_id = $request->input('workList');
            $one->save();
            foreach($request->input('id') as $value){
                $one = Work::query()
                    ->where('id', $value)
                    ->where('work_list_id', $workList->id)
                    ->first();
                if(!$one){
                    return 3;
                    DB::rollback();
                    exit();
                }

                $one->order = $order;
                // $one->assigned_user_id = '';
                $one->save();
                $order++;
            }
            DB::commit();
        }
        catch(\Exception $e){
            return $e->getMessage();
        }

        return 1;
    }
    //delete work list
    public function deleteWorkList($workListId){
        WorkList::query()
            ->where('id', $workListId)
            ->delete();
    }

    //new Work table

    public function newWorkTable(Request $request){
        //
        $validator = Validator::make($request->input(),[
            "name"  =>  "required|string|max:30",
            'project_id'    =>  'integer|nullable',

        ]);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()->all()]);
        }
        $validated = $validator->validate();
        $workTable = new WorkTable();
        $workTable->name = $validated['name'];
        $workTable->project_id = $validated['project_id'];
        $workTable->created_user_id = Auth::user()->id;
        $workTable->save();
        return 1;
    }


    //chi tiet cong viec

    public function showWork($id){
        if($id!=null){

            $this->data['itemInfo'] = Work::find($id);

            if($this->data['itemInfo']){

                $project = Work::query()
                    ->join('work_lists', 'work_lists.id', 'works.work_list_id')
                    ->join('work_tables', 'work_tables.id', 'work_lists.table_id')
                    ->join('projects', 'projects.id', 'work_tables.project_id')
                    ->select('projects.Member')
                    ->first();
                $tempArr = explode(',',$this->data['itemInfo']->assigned_user_id);

                //danh sach thanh vien duoc gan vao cong viec
                $listWorkMember = User::query();
                foreach($tempArr as $tempItem){
                    $listWorkMember = $listWorkMember->orWhere('id', $tempItem);
                }
                $list = $listWorkMember->get();
                $this->data['listWorkMember'] = $list;
                $this->data['workMemberArr'] = [];
                foreach($list as $item){
                    $this->data['workMemberArr'][] = $item->id;
                }
                $memberArr = explode(",", $project->Member);
                $this->data['members'] = User::query()
                ->whereIn('id', $memberArr)
                ->get();

                $this->data['taskTypes'] = MasterData::query()
                    ->where('DataKey', 'BC')
                    ->get();
                $this->data['tasks'] = Task::query()
                    ->where('work_id', $id)
                    ->get();

                foreach($this->data['tasks'] as $item){
                    $item->histories = TaskWorkHistory::query()
                        ->join('users', 'task_work_histories.user_id', 'users.id')
                        ->select('task_work_histories.*', 'users.FullName')
                        ->where('task_id', $item->id)
                        ->orderBy('id', 'asc')
                        ->get();
                }






                return view('admin.includes.work-detail', $this->data);
            }else{
                return "";
            }
        }else return 0;
    }

    //xu ly cac action lien quan den chi tiet cong viec
    public function updateWork(Request $request){
        // return $request->input();
        $work = Work::find($request->input('workId'));
        if(!$work) return 0;
        if($request->input('action') == 'updateMember'){

            $idArr = $request->input("listMember");
            if(is_null($idArr)) $idArr = [];
            foreach($idArr as $item){
                $user = User::find($item);
                if(!$user) return 1;

            }
            if(count($idArr) > 0){
                $str = ','.implode(',', $idArr).',';
            }else{
                $str = "";
            }

            $work->assigned_user_id = $str;
            $work->save();
        }
        if($request->input('action') == 'updateDesc'){
            $validator = Validator::make($request->input(),[
                'desc'    =>  'string|nullable',

            ]);
            if($validator->fails()){
                return response()->json(['errors'=>$validator->errors()->all()]);
            }
            $validated = $validator->validate();
            $work->description = $validated['desc'];
            $work->save();
            return 1;
        }
        if($request->input('action') == 'newTask'){
            $validator = Validator::make($request->input(),[
                'taskTitle'    =>  'string|max:191|required',
                'taskType'  =>  'required|string'
            ]);
            if($validator->fails()){
                return response()->json(['errors'=>$validator->errors()->all()]);
            }
            $validated = $validator->validate();
            $checkType = MasterData::query()
                ->where('DataKey', 'BC')
                ->where('DataValue', $validated['taskType'])
                ->first();
            if(!$checkType) return 0;
            $task = new Task();
            $task->name = $validated['taskTitle'];
            $task->created_user_id = Auth::user()->id;
            $task->work_id = $work->id;
            $task->task_type_id = $validated['taskType'];
            $task->save();
            return $task;
        }
        if($request->input('action') == 'updateTitle'){
            $validator = Validator::make($request->input(),[
                'title'    =>  'string|max:191|required',

            ]);
            if($validator->fails()){
                return response()->json(['errors'=>$validator->errors()->all()]);
            }
            $validated = $validator->validate();
            $work->name = $validated['title'];
            $work->save();
            return 1;
        }
    }
    public function updateTask(Request $request){
        $task = Task::find($request->input('taskId'));
        if(!$task) return 0;

        if($request->input('action') == 'updateTitle'){
            $validator = Validator::make($request->input(),[
                'title' =>  'required|string|max:191',

            ]);
            if($validator->fails()){
                return response()->json(['errors'=>$validator->errors()->all()]);
            }
            $validated = $validator->validate();
            $task->name = $validated['title'];
            $task->save();
            return 1;

        }
        if($request->input('action') == 'newTaskHistory'){
            //fasdf
            $validator = Validator::make($request->input(),[
                'total' =>  'required|numeric|min:0',
                'date' =>  'required|date_format:Y/m/d',
                'STime' =>  'required|date_format:H:i',
                'ETime' =>  'required|date_format:H:i',
                'taskId'    =>  'required|integer',
                'total' =>  'required|numeric|min:0',
            ]);
            if($validator->fails()){
                return response()->json(['errors'=>$validator->errors()->all()]);
            }
            $validated = $validator->validate();
            $lastHistory = TaskWorkHistory::query()
                ->where('user_id', Auth::user()->id)
                ->orderBy('created_at', 'desc')
                ->first();
            if($lastHistory){
                // return 1;
                //check dieu kien thoi gian nhap vao

                if(Carbon::parse($validated['date']) < Carbon::parse($lastHistory->date) || Carbon::parse($validated['date']) > Carbon::now()->startOfDay())
                return response()->json(['errors'=>['Ngày nhập vào không hợp lệ']]);
                if(Carbon::parse($validated['STime']) >= Carbon::parse($validated['ETime'])){
                    return response()->json(['errors'=>['Thời gian không hợp lệ']]);
                }
                if(Carbon::parse($validated['date']) == Carbon::parse($lastHistory->date)){
                    //check time
                    // return Carbon::parse($lastHistory->end_time);
                    if(Carbon::parse($validated['STime']) <= Carbon::parse($lastHistory->end_time)){
                        return response()->json(['errors'=>['Thời gian không hợp lệ']]);
                    }
                    $dif = Carbon::parse($validated['ETime'])->diffInMinutes($validated['STime']);
                    $hour = number_format($dif/60, 2);
                    //check thời gian nghỉ
                    if($hour < $validated['total'])
                    return response()->json(['errors'=>['Thời gian nghỉ không hợp lệ']]);

                }


            }

            $history = new TaskWorkHistory();
            $history->user_id = Auth::user()->id;
            $history->task_id = $validated['taskId'];
            $history->rest_time = $validated['total'];
            $history->date = $validated['date'];
            $history->start_time = $validated['STime'];
            $history->end_time = $validated['ETime'];

            $history->save();
            $history->FullName = User::find($history->user_id)->FullName;
            $history->nextStartTime = Carbon::parse($history->end_time)->addMinute()->format("H:i");
            return $history;
        }
        if($request->input('action') == 'updateStatus'){
            $task->status = !$task->status;
            $task->save();
            return $task;
        }
    }
}
