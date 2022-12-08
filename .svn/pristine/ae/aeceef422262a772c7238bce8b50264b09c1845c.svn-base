<?php

namespace App\Imports;

use App\Model\Timekeeping;
use App\User;
use App\MasterData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
//use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\DB;

class TimekeepingNewImport implements ToCollection {
    /**
     * @param Collection $collection
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    private $request;
    public function __construct($request) {
        $this->request = $request;
    }

    public function collection(Collection $collection) {
        $request = $this->request;
        if(isset($request['Reset'])) {
            $reset = $request['Reset'];
        }
        $date = $request['Date'];
        $timestamp    = strtotime(\DateTime::createFromFormat('m/Y',$date)->format('M Y'));
        $first_second = date('Y-m-01', $timestamp);
        $last_second  = date('Y-m-t', $timestamp);
        //lấy định dạng file excel từ setting
        $intExcelFormat = config('settings.excel_import_format');

        //file định dạng của akb
        if($intExcelFormat == 0) {
            $userId = null;
            DB::beginTransaction();
            try {
                $olduser = '';
                foreach($collection->toArray() as $key => $row) {
                    preg_match_all('/(Mã nhân viên:)/', $row[0], $matches);

                    if(isset($matches[0]) && count($matches[0]) > 0) {
                        $userInfoString = $row[0];

                        preg_match_all('!\d+!', $userInfoString, $matches_info);
                        if(!isset($matches_info[0][0])) {
                            return Redirect::back()->withErrors(['File không đúng định dạng!']);
                        }

                        $userTemp = User::query()
                            ->where('IDFM', $matches_info[0][0])
                            ->first();

                        if(!$userTemp) {
                            // return Redirect::back()->withErrors(['Người dùng không tồn tại!']);
                            // continue;
                        } else {
                            $userId = $userTemp;
                        }
                    }
                    $master = MasterData::where('DataValue','WT001')->first();
                    if(isset($request['Reset'])) {
                        if(!is_null($reset) &&  $reset == 1 && $userId != null && $userId->id != 0 && $olduser != $userId->id) {
                            $olduser = $userId->id;
                            $num = 0;
                        }
                    }
                    if(isset($request['Reset'])) {
                        if(!is_null($reset) &&  $reset == 1 && ($userId != null && $userId->id != 0) && ($num == 0)) {
                            $num = $num + 1;
                            $one = Timekeeping::query()
                                    ->where('UserID',$userId->id)
                                    ->where('Date','>=',$first_second)
                                    ->where('Date','<=',$last_second);
                            if($one) {
                                $one->delete();
                            }
                        }
                    }
                    if(is_numeric($row[0]) && ($userId != null && $userId->id != 0)) {
                        $checkExistRecord = Timekeeping::query()
                            ->where('UserID', $userId->id)
                            ->where('Date', Date::excelToDateTimeObject($row[0])->format('Y/m/d'))
                            ->first();
                        if(!$checkExistRecord) {
                            $timekeeping = new Timekeeping();
                            $timekeeping->UserID = $userId->id;
                            $timekeeping->Day = Date::excelToDateTimeObject($row[0])->format('d');
                            $timekeeping->Date = Date::excelToDateTimeObject($row[0])->format('Y/m/d');
                            $timekeeping->TimeIn = !is_null($row[3]) ? Date::excelToDateTimeObject($row[3])->format('H:i') : null;
                            $timekeeping->TimeOut = !is_null($row[4]) ? Date::excelToDateTimeObject($row[4])->format('H:i') : null;
                            $timekeeping->STimeOfDay = !is_null($userId->STimeOfDay) ? $userId->STimeOfDay : $master->Name;
                            $timekeeping->ETimeOfDay = !is_null($userId->ETimeOfDay) ? $userId->ETimeOfDay : $master->DataDescription;
                            $timekeeping->save();
                        }
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $e->getMessage();
            }

        }

        //file định dạng aic_group
        if($intExcelFormat == 1) {
            if(count($collection)<=1) {
                return Redirect::back()->withErrors(['File không đúng định dạng!']);
            }

            DB::beginTransaction();
            foreach($collection->toArray() as $key => $row) {
                if($key>0) {
                    $row = $this->checkRowAIC($row);
                    if(!$row) {
                        DB::rollback();
                        return Redirect::back()->withErrors(['File không đúng định dạng!']);
                    }else{
                        $user = User::query()
                            ->where('IDFM', $row[3])
                            ->first();
                        if($user) {
                            $timekeeping = Timekeeping::query()
                                ->where('Date', $row[0])
                                ->where('UserID', $user->id)
                                ->first();
                            if(!$timekeeping) {
                                $timekeeping = new Timekeeping();
                            }

                            //cap nhat thoi gian vao
                            if(trim($row[2]) == 'Access Granted') {
                                if(is_null($timekeeping->Date) || $timekeeping->TimeIn > $row[1]) {
                                    $timekeeping->UserID = $user->id;
                                    //insert new record
                                    $timekeeping->Date = $row[0];
                                    $timekeeping->TimeIn = $row[1];

                                    $timekeeping->save();
                                }

                            }
                            //cap nhat thoi gian ra
                            if(trim($row[2]) == 'Exit Granted') {
                                if(is_null($timekeeping->Date) || $timekeeping->TimeOut < $row[1]) {
                                    $timekeeping->UserID = $user->id;
                                    //insert new record
                                    $timekeeping->Date = $row[0];
                                    $timekeeping->TimeOut = $row[1];

                                    $timekeeping->save();
                                }

                            }

                        }

                    }
                }

            }
            DB::commit();
            return Redirect::back()->with('success','Import thành công!');
        }
    }
    //function kiểm tra hàng theo định dạng file excel của aic group, nếu hợp lệ trả lại mảng
    public function checkRowAIC($row) {
        try {
            if(!isset($row[0]) || !isset($row[1]) || !isset($row[3]) || !isset($row[4])) return false;
            //check date
            if(is_numeric($row[0])) {
                $row[0] = Date::excelToDateTimeObject($row[0])->format('Y-m-d');
            }else{
                $arrDateTmp = explode('/', $row[0]);
                if(count($arrDateTmp) == 3) {
                    if(!checkdate($arrDateTmp[1], $arrDateTmp[0], $arrDateTmp[2])) {
                        return false;
                    }else{
                        $row[0] = $arrDateTmp[2].'-'.$arrDateTmp[1].'-'.$arrDateTmp[0];
                    }
                }else{
                    return false;
                }
            }
            //check h:m:i
            if(is_numeric($row[1])) {
                $excelNumberNow = Date::timestampToExcel(Carbon::now('UTC')->startOfDay()->timestamp);
                $row[1] = Date::excelToDateTimeObject($excelNumberNow + $row[1])->format('H:i:s');
            }else{
                preg_match('/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/', $row[1], $arrCheckHis);
                //format input is H:i:s exactly (has 2 ":")
                if(substr_count($row[1], ':') != 2) {
                    return false;
                }
                if(count($arrCheckHis) == 0) {
                    return false;
                }
            }
            return [
                $row[0],
                $row[1],
                $row[3],
                $row[4]
            ];
        }
        catch(\Exception $e) {
            return Redirect::back()->withErrors([$e->getMessage()]);
        }

    }
}
