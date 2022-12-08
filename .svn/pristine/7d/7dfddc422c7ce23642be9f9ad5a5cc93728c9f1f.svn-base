<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SpendingExport;
use App\Finance;
use App\FinanceCategory;
use App\Http\Controllers\Controller;
use App\MasterData;
use App\RoleScreenDetail;
use App\User;
use DateTime;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class FinanceController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $export;
    const KEYMENU= array(
        "add" => "spendingAdd",
        "view" => "spendingList",
        "edit" => "spendingEdit",
        "delete" => "spendingDelete",
        "export" => "spendingExport",
    );
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');
        $array = $this->RoleView(null,['spendingList']);
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if($value == $row->alias)
                    $this->$key = $row;
            } 
        }
    }

    public function spendingList(Request $request, $orderBy = 'date', $sortBy = 'desc')
    {
        if ($request->has('date')) {
            if (DateTime::createFromFormat('d/m/Y', $request['date'][0]) === FALSE && $request['date'][0] != '' ||
                DateTime::createFromFormat('d/m/Y', $request['date'][1]) === FALSE && $request['date'][1] != '') {
                return Redirect::back();
            }
        }
        $recordPerPage = $this->getRecordPage();
        $this->getDataSpending($request, $orderBy, $sortBy);

        $count = $this->data['spendingList']->count();

        //Pagination
        $this->data['spendingList'] = $this->data['spendingList']->paginate($recordPerPage);
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc')."/".$query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);

        //redirect to the last page if current page has no record
        if($this->data['spendingList']->count() == 0)
        {
            if(array_key_exists('page', $query_array))
            {
                if($query_array['page'] > 1)
                {
                    $query_array['page'] = $this->data['spendingList']->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl  = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }
            }
        }


        $this->data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $this->data['request'] = $request->query();
        $this->data['query_array'] = $query_array;
        $this->data['export'] = $this->export;
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;

        return $this->viewAdminLayout('spending-list', $this->data);
    }

    public function exportExcel(Request $request)
    {
        $this->getDataSpending($request, 'date', 'desc');
        $record = $this->data['spendingList']->get();
        if (count($record) > 0) {
            return Excel::download(new SpendingExport($record), 'DanhSachChiTieu.xlsx');
        }

        return $this->jsonErrors('Không có dữ liệu!');
    }

    /**
     * @param $request
     * @param $orderBy
     * @param $sortBy
     * @return RedirectResponse
     */
    public function getDataSpending($request, $orderBy, $sortBy)
    {

        if (Schema::hasColumn('finances', $orderBy)) {
            $spendingList = Finance::orderBy($orderBy, $sortBy);
        } else {
            return redirect()->back();
        }

        $spendingList = $spendingList->select('finances.*', 'master_data.Name as categoryName', 'users.FullName')
            ->join('master_data', 'master_data.DataValue', 'finances.finance_category')
            ->leftJoin('users', 'users.id', 'finances.user_spend');

        //if request == null
        if (!isset($request['finance_category']) && !isset($request['user_spend']) && !isset($request['date'])) {
            $spendingList = $spendingList->where('finances.date', '>=', \Carbon\Carbon::now()->startOfMonth())
                ->where('finances.date', '<=', Carbon::now());
        }
        //if request finance_category != null
        if ($request['finance_category'] != '') {
            $spendingList = $spendingList->where('finance_category', $request['finance_category']);
        }
        //if request user_spend != null
        if ($request['user_spend'] != '') {
            $spendingList = $spendingList->where('user_spend', $request['user_spend']);
        }

        if (is_array($request['date'])) {
            foreach ($request->query() as $key => $value) {
                if ($key == 'date') {
                    $value[0] != '' ? $value[0] = $this->fncDateTimeConvertFomat($value[0],
                        self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : $value[0];
                    $value[1] != '' ? $value[1] = $this->fncDateTimeConvertFomat($value[1],
                        self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD) : $value[1];

                    $spendingList->where(function ($query) use ($value) {
                        if ($value[0] != '' && $value[1] != '' && $value[0] !== $value[1]) {
                            $query = $query->where('date', '>=', \Carbon\Carbon::parse($value[0])->startOfDay())
                                ->where('date', '<=', Carbon::parse($value[1])->endOfDay());
                        }
                        if ($value[0] != '' && $value[1] != '' && $value[1] == $value[0]) {
                            $query = $query->whereRaw("CAST(finances.date AS DATE) = '$value[0]'");
                        }
                        if ($value[0] != '' && $value[1] == '') {
                            $query = $query->where('date', '<=', Carbon::now())
                                ->where('date', '>=', Carbon::parse($value[0])->startOfDay());
                        }
                        if ($value[0] == '' && $value[1] != '') {
                            $query = $query->where('Date', '>=', Carbon::parse()->startOfYear())
                                ->where('date', '<=', Carbon::parse($value[1])->endOfDay());
                        }
                    });
                }
            }
        }

        //input search
        $one = Finance::query()->select('date','master_data.Name','expense', 'note', 'desc')
            ->leftJoin('master_data', 'finances.finance_category', '=', 'master_data.DataValue')->first();

        if ($one){
            $one = $one->toArray();
            if(array_key_exists('search', $request->input()))
            {
                $spendingList = $spendingList->where(function ($query) use ($one, $request){

                    foreach($one as $key => $value){
                        if($key == 'Name'){
                            $query->orWhere('master_data.'.$key, 'like', '%'.$request->input('search').'%');
                        }else{
                            $strSearch = trim($this->convert_vi_to_en($request->input('search')));

                            if($key == 'date'){
                                $query->orWhereRaw('(DATE_FORMAT(finances.'.$key.',"%d/%m/%Y")) LIKE ?', '%'.$strSearch.'%' );
                            }else{
                                $query->orWhere('finances.'.$key, 'LIKE', '%'.$strSearch.'%');
                            }
                        }
                    }
                });
            }
        }

//        $spendingList = $spendingList->get();
        $this->data['spendingList'] = $spendingList;

        $this->data['cats'] = MasterData::query()
            ->where('DataKey', 'FN')
            ->get();
//        $spendingUsers = Finance::query()
//            ->select('users.FullName', 'finances.user_spend')
//            ->leftJoin('users', 'users.id', 'finances.user_spend')
//            ->groupBy('finances.user_spend')
//            ->orderBy('finances.user_spend', 'asc')
//            ->get();
//        $this->data['spendingUsers'] = $spendingUsers;
        $this->data['spendingUsers'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        return $this->data;
    }

    public function spendingDetail(Request $request, $id = null, $del = null)
    {
        $this->data['cats'] = MasterData::query()
            ->where('DataKey', 'FN')
            ->get();
        $this->data['spendingUsers'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        if ($id != null) {
            if ($del == 'del') {
                $one = Finance::find($id);
                if ($one) $one->delete();
                return 1;
            }

            $this->data['itemInfo'] = Finance::find($id);

            if ($this->data['itemInfo']) {
                return $this->viewAdminIncludes('spending-detail', $this->data);
            } else {
                return "";
            }
        } else {
            return $this->viewAdminIncludes('spending-detail', $this->data);
        }
        return $this->viewAdminIncludes('spending-detail', $this->data);
    }

    public function spendingStore(Request $request)
    {
        if (count($request->input()) === 0) {
            return abort('404');
        }

        try {
            $catArray = MasterData::query()
                ->where('DataKey', 'FN')
                ->pluck('DataValue')
                ->toArray();
            $arrCheck = [

                'id'               => 'nullable|integer',
                'date'             => 'required|date_format:d/m/Y',
                'expense'          => 'required|numeric|min:1000',
                'finance_category' => [
                    'required',
                    Rule::in($catArray),
                ],
                'note'             => 'nullable|string',
                'desc'             => 'nullable|string',
                'user_spend'       => 'required'
            ];

            $messages = [
                'date.required'             => 'Vui lòng chọn ngày.',
                'expense.required'          => 'Chưa điền số tiền chi.',
                'expense.numeric'           => 'Số tiền không hợp lệ.',
                'finance_category.required' => 'Chưa chọn danh mục chi tiêu.',
                'user_spend.required'       => 'Người chi không được để trống.',
            ];

            $validator = Validator::make($request->input(), $arrCheck, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()->first()]);
            }

            $validated = $validator->validate();

            if (array_key_exists('id', $validated)) {
                // $this->authorize('action', $this->edit);
                $finance = Finance::find($validated['id']);
            } else {
                // $this->authorize('action', $this->add);
                $finance = new Finance();
            }

            foreach ($validated as $key => $value) {
                if (Schema::hasColumn('finances', $key)) {
                    if ($key == 'date' && $value != '') {
                        $value = $this->formatDateWithCol($value);
                    }
                    $finance->$key = $value;
                }
            }
            $finance->user_create = Auth::user()->id;
            $finance->save();

            return 1;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function showCategory()
    {

    }

    public function stats(Request $request)
    {
        $this->data['cats'] = MasterData::query()
            ->select('master_data.*')
            ->join('finances', 'finances.finance_category', 'master_data.DataValue')
            // ->whereIn('master_data.DataValue', explode(',', $request->input('cat')))
            ->groupBy('master_data.DataValue')
            ->orderBY('master_data.DataValue', 'asc')
            ->get();
        $spendingUsers = Finance::query()
            ->select('users.FullName', 'finances.user_spend')
            ->leftJoin('users', 'users.id', 'finances.user_spend')
            ->groupBy('finances.user_spend')
            ->orderBy('finances.user_spend', 'asc')
            ->get();

        //
        $this->data['minDate'] = Finance::query()
            ->select('date')
            ->orderBy('date', 'asc')
            ->first();
        if($this->data['minDate']) $this->data['minDate'] = $this->data['minDate']->date;
        else $this->data['minDate'] = 'now';
        $this->data['spendingUsers'] = $spendingUsers;

        $this->data['request'] = $request->query();
        return $this->viewAdminLayout('spending-stats', $this->data);
    }

    public function postStats(Request $request)
    {
        $year = $request->input('startYear');
        $arrData = [];
        $arrData[0][] = 'Tháng';

        for ($i = 1; $i <= 12; $i++) {
            $arrData[$i] = ["Tháng " . $i];
        }
        $yearIndex = 1;


        if($request->input('cat') == 'all'){
            $cats = MasterData::query()
                ->select('master_data.*')
                ->join('finances', 'finances.finance_category', 'master_data.DataValue')
                ->groupBy('master_data.DataValue')
                ->orderBY('master_data.DataValue', 'asc')
                ->get();
        }else{
            $cats = MasterData::query()
                ->select('master_data.*')
                ->join('finances', 'finances.finance_category', 'master_data.DataValue')
                ->whereIn('master_data.DataValue', explode(',', $request->input('cat')))
                ->groupBy('master_data.DataValue')
                ->orderBY('master_data.DataValue', 'asc')
                ->get();
        }

        while($year <= $request->input('endYear')){
            foreach($cats as $cat){
                $arrData[0][] = $cat->Name.' ' . $year;
                if($year == $request->input('endYear') && $request->input('endYear') > $request->input('startYear')){
                    $arrData[0][] = '{"role": "annotation"}';
                }
            }
            if($request->input('cat') == 'all' || in_array('', explode(',', $request->input('cat')))){
                $blnTong = true;
                $arrData[0][] = 'Tổng '. $year;
                if($year == $request->input('endYear') && $request->input('endYear') > $request->input('startYear')){
                    $arrData[0][] = '{"role": "annotation"}';
                }
            }else{
                $blnTong = false;
            }

            $spendingList = Finance::query()
                ->whereYear('date', $year);

            if ($request->input('user')){
                $spendingList = $spendingList->where('user_spend', $request->input('user'));
            }
            $spendingList->orderBy('finance_category', 'asc');
            $spendingList = $spendingList->get();

            for ($i = 1; $i <= 12; $i++) {
                $catCount = 1;
                foreach($cats as $cat){
                    $monthData = $spendingList->filter(function ($value, $key) use ($year, $i, $cat) {
                        $month = Carbon::parse($value->date);
                        return ($month->year == $year && $month->month == $i && $cat->DataValue == $value->finance_category);
                    });
                    if($monthData->isNotEmpty()){

                        if($year == $request->input('endYear') && $request->input('endYear') > $request->input('startYear')){
                            $arrData[$i][($yearIndex-1)*($blnTong ? $cats->count()+1 : $cats->count()) + 2*($catCount -1) + 1] = $monthData->sum('expense');
                            $tmpPercent = $arrData[$i][($yearIndex-2)*($blnTong ? $cats->count()+1 : $cats->count()) + $catCount] ?
                            ($arrData[$i][($yearIndex-1)*($blnTong ? $cats->count()+1 : $cats->count()) + 2*($catCount -1) + 1] / $arrData[$i][($yearIndex-2)*($blnTong ? $cats->count()+1 : $cats->count()) + $catCount] - 1)*100 : 0;
                            $tmpPercent = $tmpPercent ? number_format($tmpPercent, 2, ',', '.') : 0;
                            $arrData[$i][] = $tmpPercent ? ($tmpPercent < 0 ? $tmpPercent . "%" : "+".$tmpPercent . "%") : null;
                        }else{
                            $arrData[$i][($yearIndex-1)*($blnTong ? $cats->count()+1 : $cats->count()) + $catCount] = $monthData->sum('expense');
                        }
                    }else{
                        if($year == $request->input('endYear') && $request->input('endYear') > $request->input('startYear')){
                            $arrData[$i][($yearIndex-1)*($blnTong ? $cats->count()+1 : $cats->count()) + 2*($catCount -1) + 1] = 0;
                            $arrData[$i][] = null;
                        }else{
                            $arrData[$i][($yearIndex-1)*($blnTong ? $cats->count()+1 : $cats->count()) + $catCount] = 0;
                        }
                    }
                    $catCount++;
                }
                if($blnTong){
                    $monthData = $spendingList->filter(function ($value, $key) use ($year, $i) {
                        $month = Carbon::parse($value->date);
                        return ($month->year == $year && $month->month == $i);
                    });
                    if($monthData->isNotEmpty()){

                        if($year == $request->input('endYear') && $request->input('endYear') > $request->input('startYear')){
                            $arrData[$i][($yearIndex-1)*($cats->count()+1) + 2*($catCount -1) + 1] = $monthData->sum('expense');
                            $tmpPercent = $arrData[$i][($yearIndex-2)*($cats->count()+1) + $catCount] ?
                            ($arrData[$i][($yearIndex-1)*($cats->count()+1) + 2*($catCount -1) + 1] / $arrData[$i][($yearIndex-2)*($cats->count()+1) + $catCount] - 1)*100 : 0;
                            $tmpPercent = $tmpPercent ? number_format($tmpPercent, 2, ',', '.') : 0;
                            $arrData[$i][] = $tmpPercent ? ($tmpPercent < 0 ? $tmpPercent . "%" : "+".$tmpPercent . "%") : null;
                        }else{
                            $arrData[$i][($yearIndex-1)*($cats->count()+1) + $catCount] = $monthData->sum('expense');
                        }
                    }else{

                        if($year == $request->input('endYear') && $request->input('endYear') > $request->input('startYear')){
                            $arrData[$i][($yearIndex-1)*($cats->count()+1) + 2*($catCount -1) + 1] = 0;
                            $arrData[$i][] = null;
                        }else{
                            $arrData[$i][($yearIndex-1)*($cats->count()+1) + $catCount] = 0;
                        }
                    }
                }
            }

            $year++;
            $yearIndex++;
        }
        return \GuzzleHttp\json_encode($arrData);
    }
}
