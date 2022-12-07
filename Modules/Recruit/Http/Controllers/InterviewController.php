<?php

namespace Modules\Recruit\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Recruit\Entities\Interview;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Admin\AdminController;

class InterviewController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $app;
    const KEYMENU = array(
        // "add" => "InterviewJobAdd",
        // "view" => "InterviewJob",
        // "edit" => "InterviewJobEdit",
        // "delete" => "InterviewJobDelete",
        // "app" => "ListApprove"
    );

    public function _construct(Request $request)
    {
        // if (strpos(\Request::getRequestUri(), 'api') === false) {
        //     parent::__construct($request);
        //     $this->middleware('auth');
        // }
        // $array = $this->RoleView('Recruit', ['Recruit', 'Recruit']);
        // $this->menu = $array['menu'];
        // foreach (self::KEYMENU as $key => $value) {
        //     foreach ($array['role'] as $row) {
        //         if ($value == $row->alias)
        //             $this->$key = $row;
        //     }
        // }
    }

    public function interviewList()
    {

        $data['interviews'] = Interview::all();
        return view('recruit::interview.interviewList', $this->data);
    }
}
