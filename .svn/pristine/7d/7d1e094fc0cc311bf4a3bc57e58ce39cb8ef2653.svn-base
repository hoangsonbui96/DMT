<?php

namespace App\Http\Controllers\Admin;

use App\RoleUserScreenDetailRelationship;
use App\RoleScreen;
use App\RoleScreenDetail;
use App\RoleUserGroup;

use App\UserGroup;
use Illuminate\Http\Request;
use App\RoomReport;
use App\Exports\RoomReportExport;
use App\DailyReport;
use App\Room;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Menu;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

use Maatwebsite\Excel\Facades\Excel;
//


/**
 * Class RoomReportController
 * @package App\Http\Controllers\Admin
 * Controller screen Room RepRoomReportExportort
 */
class RoomReportController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $export;
    const KEYMENU= array(
        "add" => "RoomReportsAdd",
        "view" => "RoomReports",
        "edit" => "RoomReportsEdit",
        "delete" => "RoomReportsDelete",
        "export" => "RoomReportsExport",
    );
    /**
     * RoomReportController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');
        $array = $this->RoleView(null,['RoomReports']);
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if($value == $row->alias)
                    $this->$key = $row;
            } 
        }
    }

    /**
     * @param Request $request
     * @return View (room-report)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * Get data room Report and return view
     */
    public function show(Request $request , $orderBy = 'id', $sortBy = 'desc', $id = null)
    {
        $this->authorize('view', $this->menu);
        $recordPerPage = $this->getRecordPage();
        if ($request->has('Date')) {
            if (\DateTime::createFromFormat('d/m/Y', $request['Date'][0]) === FALSE && $request['Date'][0] != '' ||
                \DateTime::createFromFormat('d/m/Y', $request['Date'][1]) === FALSE && $request['Date'][1] != '') {
                return Redirect::back();
            }
        }
        $list = $this->getListRoomReport($request, $orderBy, $sortBy);
        $count = $list->count();
         //Pagination
        $list = $list->paginate($recordPerPage);
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy=='desc' ? 'asc' : 'desc')."/".$query_string;
        $sort = ($sortBy=='asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);

        if($list->count() == 0){
            if(array_key_exists('page', $query_array)){
                if($query_array['page'] > 1){
                    $query_array['page'] = $overtime->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl  = $request->url() . '?' . $query_string;
                        return redirect($fullUrl);
                }
            }
        }
        $this->data['rooms'] = Room::query()->select('id','Name')
        ->where('MeetingRoomFlag','!=',1)->where('Active',1)->get();
        $this->data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $this->data['list'] = $list;
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['sort'] = $sort;
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['view'] = $this->view;
        $this->data['delete'] = $this->delete;
        $this->data['export'] = $this->export;
        return $this->viewAdminLayout('room-report',$this->data);
    }
    /**
     * @param $array
     * @param $orderBy
     * @param $sortBy
     * @param null $export
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Http\RedirectResponse
     */
    public function getListRoomReport($request, $orderBy, $sortBy, $export = null){
        //Get list RoomReport
        if(Schema::hasColumn('room_reports', $orderBy))
        {
            $list = RoomReport::query()
                ->select('room_reports.*','rooms.Name')
                ->leftJoin('rooms', 'room_reports.RoomID', '=', 'rooms.Name')
                ->orderBy($orderBy, $sortBy);
        }else{
            return redirect()->back();
        }
        $this->data['request'] = $request->query();

        //list room_reports
        //Search in columns
        $one = RoomReport::query()
            ->select('room_reports.SDate','room_reports.EDate','room_reports.week_work','room_reports.unfinished_work'
            ,'room_reports.Contents', 'room_reports.noted', 'rooms.Name')
            ->leftJoin('rooms', 'room_reports.RoomID', '=', 'rooms.Name')->first();
        if($one){
            $one = $one->toArray();
            if(array_key_exists('search', $request->input()))
            {
                $list = $list->where(function ($query) use ($one, $request){

                    foreach($one as $key=>$value){
                        if ($key == 'Name'){
                            $query->orWhere('rooms.'.$key, 'like', '%'.$request->input('search').'%');
                        }else{
                            $query->orWhere('room_reports.'.$key, 'like', '%'.$request->input('search').'%');
                        }
                    }
                    $query->orWhereRaw('(DATE_FORMAT(room_reports.SDate,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                    $query->orWhereRaw('(DATE_FORMAT(room_reports.EDate,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                });
            }
        }
        //check value request search
        if ($request->has('Date')) {
            if (\DateTime::createFromFormat('d/m/Y', $request['Date'][0]) === FALSE && $request['Date'][0] != '' ||
                \DateTime::createFromFormat('d/m/Y', $request['Date'][1]) === FALSE && $request['Date'][1] != '') {
                return Redirect::back();
            }
        }
        //Search with condition
        if ($request['RoomID'] != ''){
            $list = $list->where('room_reports.RoomID', 'like', '%'.$request['RoomID'].'%');
        }
        foreach($this->data['request'] as $key => $value)
            {
                if(is_array($value)){
                    $value[0] != '' ? $value[0] = $this->fncDateTimeConvertFomat($value[0],
                        self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : $value[0];
                    $value[1] != '' ? $value[1] = $this->fncDateTimeConvertFomat($value[1],
                        self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : $value[1];

                    if($value[0] != '' && $value[1] != '' && $value[0] !== $value[1]){
                        $list = $list->whereRaw("(room_reports.SDate BETWEEN CAST('$value[0]' AS DATE) AND CAST('$value[1]' AS DATE) 
                        OR room_reports.SDate BETWEEN CAST('$value[0]' AS DATE) AND CAST('$value[1]' AS DATE))");
                    }
                    if($value[0] === $value[1] && $value[0] != ''){
                        $list = $list->whereRaw("CAST(room_reports.SDate AS DATE) = '$value[0]'");
                    }
                    if ($value[0] != '' && $value[1] == ''){
                        $list = $list->whereRaw("(CAST(room_reports.SDate AS DATE) >= '$value[0]'
                         OR '$value[0]' BETWEEN CAST(room_reports.SDate AS DATE) AND CAST(room_reports.EDate AS DATE))");
                    }
                    if ($value[0] == '' && $value[1] != ''){
                        $list = $list->whereRaw("(CAST(room_reports.EDate AS DATE) <= '$value[1]'
                         OR '$value[1]' BETWEEN CAST(room_reports.SDate AS DATE) AND CAST(room_reports.EDate AS DATE))");
                    }
                }
            }
        if ($export != '' || $export != null){
            return $list->get();
        }
        return $list;
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request){
        $records = $this->getListRoomReport($request, 'id', 'desc', 'export');
        if($records->count() > 0){
            return Excel::download(new RoomReportExport($records), 'Bao-cao-phong-ban.xlsx');
        }else{
            return Redirect::back()->withErrors(['Không có dữ liệu!']);
        }
    }
    /**
     * Get data RoomReport one user
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return View screen (RoomReport-management)
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */

public function  showDetail(Request $request, $id= null, $del = null){
        $this->data['rooms'] = Room::query()->select('id','Name')
            ->where('MeetingRoomFlag','!=',1)->where('Active',1)->get();
        if($id != null)
        {
            $this->data['RoomReportInfo'] = RoomReport::find($id);

            if($del == 'del')
            {
                $one = RoomReport::find($id);
                if($one != null)
                {
                    $one->delete();
                }
                return 1;
            }
            if($this->data['RoomReportInfo']){
                $sdate = $this->data['RoomReportInfo']['SDate'];
                $edate = $this->data['RoomReportInfo']['EDate'];
                $sdate = FomatDateDisplay($sdate , FOMAT_DISPLAY_DAY);
                $edate = FomatDateDisplay($edate , FOMAT_DISPLAY_DAY);
                $this->data['RoomReportInfo']['OldDate'] = "Từ " . $sdate . " đến " . $edate;
                return $this->viewAdminIncludes('room-report-detail',$this->data);
            }else{
                return "";
            }
        }else{
        return $this->viewAdminIncludes('room-report-detail',$this->data);
        }
    }
    /**
     * Processing insert, update
     * @param Request $request
     * @param null $id
     * @return string|void
     */
    public function store(Request $request, $id = null){
        if (count($request->input()) === 0)
        {
            return abort('404');
        }

        try{
            $arrCheck = [
                'RoomID'  =>  'required|string',
                'SDate'               =>  'required|date_format:d/m/Y',
                'EDate'               =>  'required|date_format:d/m/Y',
                'week_work' =>  'required',
                'unfinished_work' =>  'required',
                'Contents' => 'string|nullable',
                'noted' => 'string|nullable'
            ];
            $modeIsUpdate = array_key_exists('id', $request->input());

            if ($modeIsUpdate){
                $arrCheck['id'] = 'integer|min:1|nullable';
            }

            $validator = Validator::make($request->all(), $arrCheck);


            if ($validator->fails())
            {
                return response()->json(['errors' => $validator->errors()->all()]);
            }
            $validated = $validator->validate();
            $one = !$modeIsUpdate ? new  RoomReport() :  RoomReport::find($validated['id']);
            $sDate = Carbon::createFromFormat('d/m/Y', $validated['SDate'] );
            $eDate = Carbon::createFromFormat('d/m/Y', $validated['EDate'] );
            if($sDate > $eDate) {
                return response()->json(['errors'=>['Thời gian kết thúc không hợp lệ']]);
            }
            // $check = DB::table('room_reports')
            //     ->where('RoomID', $validated['RoomID'])
            //     ->where(function($query) use ($stime, $etime){
            //         $query->orWhereBetween('SDate', array($stime, $etime));
            //         $query->orWhereBetween('EDate', array($stime, $etime));
            //     })
            //     ->whereNull('deleted_at')
            //     ->first();

            // if($check ){
            //     return response()->json(['errors'=>['Thời gian không hợp lệ']]);
            // }
            $one = array_key_exists('id', $validated) ? RoomReport::find($validated['id']) : new RoomReport();
            foreach($validated as $key => $value){
                if(Schema::hasColumn('room_reports', $key))
                {
                    if ($key == 'SDate'){
                        $value = $this->fncDateTimeConvertFomat($value, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                    }
                    if ($key == 'EDate'){
                        $value = $this->fncDateTimeConvertFomat($value, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                    }

                    $one->$key = $value;

                }
            }

            $one->DateUpdate = Carbon::now();
            $one->save();

            return $this->jsonSuccessWithRouter('admin.RoomReports');
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }
    /**
     * change active when click checkbox
     * @param $id
     * @param $active
     */
    /**
     * @param $search
     * @return bool|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */

    //

}
