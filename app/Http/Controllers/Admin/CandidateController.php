<?php

namespace App\Http\Controllers\Admin;

use App\Candidate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class CandidateController extends Controller
{
    public function showListCandidate(){
        $this->data['candidates'] = Candidate::query()->get();
        return view('admin.includes.list-candidate-detail',$this->data);
    }
    public function storeCandidate(Request $request, $id = null){
        try{
            if(count($request->input()) >0){
                //return $request->input();
                if(array_key_exists('id', $request->input())){
                    $validator = Validator::make($request->all(),
                        [
                            'id'                    => 'integer|min:1',
                            'JobId'                 => 'nullable|string',
                            'CVpath'                => 'required|string',
                            'FullName'              => 'required|string',
                            'Email'                 => 'required|string',
                            'Tel'                   => 'required|string',
                            'Birthday'              => 'required|date',
                            'PerAddress'            => 'required|date',
                            'CurAddress'            => 'required|date',
                            'Note'                  => 'nullable|date',
                        ]);
                }else{
                    $validator = Validator::make($request->all(),
                        [
                            'JobId'                 => 'nullable|string',
                            'CVpath'                => 'required|string',
                            'FullName'              => 'required|string',
                            'Email'                 => 'required|string',
                            'Tel'                   => 'required|string',
                            'Birthday'              => 'required|date',
                            'PerAddress'            => 'required|date',
                            'CurAddress'            => 'required|date',
                            'Note'                  => 'nullable|date',
                        ]);
                }

                if ($validator->fails())
                {
                    return response()->json(['errors'=>$validator->errors()->all()]);
                }

                $validated = $validator->validate();
                if(array_key_exists('id', $validated)){
                    $one = Candidate::find($validated['id']);
                }else{
                    $one = new Candidate();
                }
                foreach($validated as $key => $value){
                    if(Schema::hasColumn('jobs', $key))
                        $one->$key = $value;
                }
                return $one;
                $one->save();
                return response()->json(['success' => route('admin.CandidateList')]);

            }else{
                return abort('404');
            }
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }
}
