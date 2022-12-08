<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use League\CommonMark\Inline\Element\Newline;

class ConvertController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');
        set_time_limit(10000);
    }
    public function index(){
        // $this->fixOvertime();
        //bang users
        // $this->convert('user', 'users',
        //     ['Birthday', 'SDate', 'OfficalDate', 'DaysOff'],
        //     [
        //         'ID' => 'id',
        //         'Username'  =>  'username',
        //         'Passwords' =>  'password',
        //         'Avatar'    =>  'avatar',
        //         'Email'     =>  'email',
        //         'RoomID'    =>  'RoomId',
        //         'DepartmentID'  =>  'DepartmentId',
        //         'Deleted'   =>  'deleted',
        //         'OfficalDate'   =>  'OfficialDate',
        //     ]
        // );
        // //bang absences
        $this->convert('absentlist', 'absences',
            ['SDate', 'EDate'],
            [
                'ID' => 'id',
                'AbsentReasonID'    =>  'MasterDataValue',
                'AbsentDate'  =>  'created_at',
            ]
        );
        // //bang answers
        // $this->convert('answers_', 'answers',
        //     [],
        //     [
        //         'ID' => 'id',
        //         'CreateDate'    =>  'created_at',
        //     ]
        // );
        // //bang calendars
        // $this->convert('calendar', 'calendars',
        //     [],
        //     [
        //         'ID' => 'id',
        //     ]
        // );



        // $this->convert('candidate', 'candidates',
        //     ['Birthday'],
        //     [
        //         'ID' => 'id',
        //     ]
        // );

        // $this->convert('capacityprofile', 'capicity_profiles',
        //     [],
        //     []
        // );

        // $this->convert('dailyreport', 'daily_reports',
        //     ['Date', 'DateCreate'],
        //     [
        //         'ID' => 'id',
        //     ]
        // );

        // $this->convert('dblevel', 'db_levels',
        //     [],
        //     [

        //     ]
        // );

        // $this->convert('dbskill', 'db_skills',
        //     [],
        //     [
        //         'ID' => 'id',
        //     ]
        // );



        // $this->convert('equipments', 'equipment',
        //     [],
        //     [
        //         'updated_date' => 'updated_at',
        //     ]
        // );
        // $this->convert('equipment_type', 'equipment_types',
        //     [],
        //     [
        //         'created_date' => 'created_at',
        //     ]
        // );

        // $this->convert('equipment_using_history', 'equipment_using_histories',
        //     [],
        //     [
        //         'created_date' => 'created_at',
        //     ]
        // );



        // $this->convert('events_', 'calendar_events',
        //     ['StartDate', 'EndDate'],
        //     [
        //         'ID' => 'id',
        //     ]
        // );

        // $this->convert('resultevents', 'event_results',
        //     [],
        //     [
        //         'Time' => 'created_at',
        //     ]
        // );




        // $this->convert('interview', 'interviews',
        //     [],
        //     [
        //         'ID' => 'id',
        //     ]
        // );

        // $this->convert('job', 'jobs',
        //     [],
        //     [
        //         'ID' => 'id',
        //     ]
        // );

        // $this->convert('overtimework', 'overtime_works',
        //     [],
        //     [
        //         'ID' => 'id',
        //     ]
        // );

        // $this->convert('programminglevel', 'programming_levels',
        //     [],
        //     [

        //     ]
        // );

        // $this->convert('programmingskill', 'programming_skills',
        //     [],
        //     [
        //         'ID' => 'id',
        //     ]
        // );

        // $this->convert('project', 'projects',
        //     ['StartDate', 'EndDate'],
        //     [
        //         'ID' => 'id',
        //     ]
        // );

        // $this->convert('question', 'questions',
        //     ['SDate', 'EDate'],
        //     [
        //         'ID' => 'id',
        //         'CreateDate'    =>  'created_at'
        //     ]
        // );

        // $this->convert('room', 'rooms',
        //     [],
        //     [
        //         'ID' => 'id',
        //     ]
        // );

        // $this->convert('timekeeping', 'timekeepings',
        //     ['Date'],
        //     [
        //         'ID' => 'id',
        //     ]
        // );



    }
    public function fixOvertime(){
        DB::beginTransaction();
        try{
            DB::table('overtimework')->orderBy('ID', 'asc')->chunk(100, function($data){
                foreach($data as $item){
                    DB::table('overtime_works')
                        ->where('id', $item->ID)
                        ->update(['UpdatedBy' => $item->UpdateBy]);
                }
            });
            Schema::dropIfExists('overtimework');
            DB::commit();
            echo 'ok';
        }catch(\Exception $e){
            DB::rollback();
            echo $e->getMessage();
        }

    }
    public function convert($oldTable, $newTable, $arrColum = [], $mapArr = []){
        if(Schema::hasTable($oldTable)){
            $one = DB::table($oldTable)->first();
            if($one){
                $one = (array)$one;

                DB::beginTransaction();
                try{
                    DB::table($oldTable)->orderBy(array_key_first($one), 'asc')->chunk(100, function($data) use ($newTable, $arrColum, $mapArr, $oldTable)
                    {
                        foreach($data as $item){
                            $item = (array)$item;

                            foreach($item as $key => $value){
                                //decode data

                                if(base64_encode(base64_decode($item[$key], true)) === $item[$key]
                                    && ($oldTable == 'answers_' || $oldTable == 'question' || $oldTable == 'job')){
                                    // print_r($item[$key]);
                                    if($key == 'Question')
                                    $item[$key] = base64_decode($item[$key], true);
                                    // print_r($item[$key]);
                                }
                                //gộp cột bảng vắng mặt
                                if($oldTable == 'absentlist'){
                                    if($key == 'SDate'){
                                        $item[$key] = $item['SDate'] . ' ' . $item['STime'] . ':00';
                                    }

                                    if($key == 'EDate'){
                                        $item[$key] = $item['EDate'] . ' ' . $item['ETime'] . ':00';
                                    }

                                    //convert key AbsentReasonID to MasterDataValue
                                    if($key == 'AbsentReasonID'){
                                        $arrKey = [
                                            1 => 'VM001',
                                            2 => 'VM002',
                                            3 => 'VM003',
                                            4 => 'VM004',
                                            5 => 'VM005',
                                            6 => 'VM006',
                                            7 => 'VM007',
                                            8 => 'VM008'
                                        ];
                                        $item['AbsentReasonID'] = $arrKey[$item['AbsentReasonID']];
                                    }

                                }
                                //kiểm tra cột có trong cơ sở dữ liệu mới hay không, nếu cột ko cần đổi tên thì xóa key
                                if(!array_key_exists($key, $mapArr)){
                                    if(!Schema::hasColumn($newTable, $key)) {
                                        unset($item[$key]);
                                    }
                                }
                                //kiem tra xem cot co can convert hay khong
                                if(in_array($key, $arrColum)){
                                    //thuc hien convert
                                    $item[$key] = substr_replace($item[$key], '-', 4, 0);
                                    $item[$key] = substr_replace($item[$key], '-', 7, 0);

                                }



                                //kiem tra xem cot co can doi ten hay khong
                                if(array_key_exists($key, $mapArr)){
                                    $item[$mapArr[$key]] = $item[$key];
                                    unset($item[$key]);
                                }
                                if($key == 'Passwords' && $oldTable == 'user'){
                                    $item['password'] = bcrypt('123456');
                                }
                            }

                            DB::table($newTable)->insertOrIgnore($item);
                        }
                    });
                    // Schema::dropIfExists($oldTable);
                    DB::commit();
                    echo "successfully converted " . $oldTable . ' to ' . $newTable . '</br>';
                }
                catch(\Exception $e){
                    DB::rollback();
                    echo $e->getMessage();
                }

            }
        }else{
            echo $oldTable . ' table is not exist.</br>';
        }


    }
}
