<?php

namespace App\Http\Controllers\Admin;

use App\CapicityProfile;
use App\DbLevel;
use App\ProgrammingLevel;
use App\ProgrammingSkill;
use App\TrainingHistory;
use App\User;
use App\RoleScreenDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Admin\AdminController;

class EmployerSkillController extends AdminController
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');

    }

    public function show(Request $request){
        $this->authorize('view', $this->menu);

        $this->data['request'] = $request;
        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);

        $list = DB::table('capicity_profiles')
            ->join('users', 'capicity_profiles.UserID', '=', 'users.id')
            ->select('users.FullName', 'users.BirthDay', 'capicity_profiles.*')
            ->groupBy('capicity_profiles.UserID');

        if(isset($request['user']) && $request['user'] != '') {
            $list = $list->where('UserID', '=', $request['user']);
        }

        $list = $list->get()->toArray();
//        print_r($list);
        foreach($list as $item){
//            $item->progSkills = ProgrammingLevel::query()
//                ->where('UserID', $item->UserID)
//                ->get();
            $item->progSkills = DB::table('programming_levels')
                ->leftJoin('programming_skills', 'programming_levels.ProgrammingSkillID', '=', 'programming_skills.id')

                ->select('programming_levels.*', 'programming_skills.*')
                ->where('programming_levels.UserID', $item->UserID)
                ->get();
            $item->DbSkills = DB::table('db_levels')
                ->leftJoin('db_skills', 'db_levels.DBSkillID', '=', 'db_skills.id')

                ->select('db_levels.*', 'db_skills.*')
                ->where('db_levels.UserID', $item->UserID)
                ->get();
        }
        $this->data['list'] = $list;
        return view('admin.layouts.'.config('settings.template').'.list-skill', $this->data);
    }

    public function showDetail($id=null){
        $this->authorize('view', $this->menu);
        if(is_null($id)){
            $profile = CapicityProfile::find(Auth::user()->id);
            $id = Auth::user()->id;
        }
        else{
            $profile = CapicityProfile::query()
                ->where('UserID', $id)
                ->first();
        }

        if($profile){
            $this->data['profile'] = $profile;
        }

        $this->data['progSkills'] = ProgrammingSkill::query()
            ->where('Active', 1)
            ->get();
        foreach($this->data['progSkills'] as $item){
            $result = ProgrammingLevel::query()
                ->where('UserID', $id)
                ->where('ProgrammingSkillID', $item->id)
                ->first();
            if($result){
                $item->Level = $result->Level;
                $item->YearExp = $result->YearExp;
            }

        }
        $this->data['dbSkills'] = ProgrammingSkill::query()
            ->where('Active', 1)
            ->get();
        foreach($this->data['dbSkills'] as $item){
            $result = DbLevel::query()
                ->where('UserID', $id)
                ->where('DBSkillID', $item->id)
                ->first();
            if($result){
                $item->Level = $result->Level;
                $item->YearExp = $result->YearExp;
            }

        }
        $this->data['trainings'] = TrainingHistory::query()
            ->where('UserID', $id)
            ->get();
        $this->data['user'] = User::find($id);
        $this->data['progSkillProfile'] = ProgrammingLevel::query()
            ->where('UserID', $id)
            ->get();
        $this->data['dbSkillProfile'] = DbLevel::query()
            ->where('UserID', $id)
            ->get();
        if(!$this->data['user']){
            return abort('404');
        }
        return view('admin.layouts.'.config('settings.template').'.profile-skill', $this->data);
    }

    public function store(Request $request){
        if($request->input('Action') == 'capicityProfile'){
            $validator = Validator::make($request->input(),[
                'UserID'    =>  'required|integer|min:1',
                'LevelEN'   =>  'numeric|min:0|nullable',
                'LevelJA'   =>  'numeric|min:0|nullable',
                'YearExperience'   =>  'numeric|min:0|nullable',
                'YearInJA'   =>  'numeric|min:0|nullable',
                'CVFile'   =>  'string|nullable',
                'CapacityOther'   =>  'string|nullable',
                'Favorite'   =>  'string|nullable',
                'Note'   =>  'string|nullable',
                'progSkill' =>  'array|required',
                'progSkill.*'   =>  'array|required',
                'progSkill.*.*' =>  'numeric|min:0|nullable',
                'dbSkill' =>  'array|required',
                'dbSkill.*'   =>  'array|required',
                'dbSkill.*.*' =>  'numeric|min:0|nullable',
                'SYear' =>  'array',
                'SYear.*'   =>  'date|required',
                'EYear' =>  'array',
                'EYear.*'   =>  'date|nullable',
                'Content' =>  'array',
                'Content.*'   =>  'string|required',

            ]);
            if($validator->fails()){
                return response()->json(['errors'=>$validator->errors()->all()]);
            }

            $validated = $validator->validated();
           return $validated;
            $checkUser = User::find($validated['UserID']);
            if(!$checkUser){
                return response()->json(['errors'=>['Người dùng không tồn tại!']]);
            }
            if($validated['UserID'] != Auth::user()->id){
                $this->authorize('admin', $this->menu);
            }
            $profileSkill = CapicityProfile::query()
                ->where('UserID', $validated['UserID'])
                ->first();
            if(!$profileSkill){
                $profileSkill = new CapicityProfile();
//                $profileSkill->UserID = $validated['UserID'];
            }
            foreach($validated as $key => $value){
                if(Schema::hasColumn('capicity_profiles', $key))
                    $profileSkill->$key = $value;
            }
            $profileSkill->save();

            ProgrammingLevel::query()
                ->where('UserID', $validated['UserID'])
                ->delete();

            DbLevel::query()
                ->where('UserID', $validated['UserID'])
                ->delete();
            TrainingHistory::query()
                ->where('UserID', $validated['UserID'])
                ->delete();
            try{
                foreach ($validated['progSkill'] as $key => $item){

                    if(!is_null($item[0]) || !is_null($item[1])){
                        $one = new ProgrammingLevel();
                        $one->UserID = $validated['UserID'];
                        $one->ProgrammingSkillID = $key;
                        $one->Level = $item[0]+0;
                        $one->YearExp = $item[1]+0;
                        $one->save();
                    }
                }

                foreach ($validated['dbSkill'] as $key => $item){
                    if(!is_null($item[0]) || !is_null($item[1])){
                        $one = new DbLevel();
                        $one->UserID = $validated['UserID'];
                        $one->DBSkillID = $key;
                        $one->Level = $item[0]+0;
                        $one->YearExp = $item[1]+0;
                        $one->save();
                    }
                }

                foreach($validated['SYear'] as $key => $item){
                    $one = new TrainingHistory();
                    $one->UserID = $validated['UserID'];
                    $one->SYear = $validated['SYear'][$key];
                    $one->EYear = $validated['EYear'][$key];
                    $one->Content = $validated['Content'][$key];
                    $one->save();
                }
            }catch (\Exception $e){
                return $e->getMessage();
            }

            return 1;


        }

    }
}
