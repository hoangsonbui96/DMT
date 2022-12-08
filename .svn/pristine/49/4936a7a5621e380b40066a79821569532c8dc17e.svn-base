<?php

namespace App\Http\Controllers\Admin\Checkin;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Controller;
use App\Model\CheckinHistory;
use App\Model\Timekeeping;
use Illuminate\Http\Request;
use App\Model\QrCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\MasterData;
use Session;

class CheckinController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    /**
     * @return View (daily-report)
     */
    public function showForm(Request $request) {
        // dd(Auth::user());
        if (Auth::user() != null && Auth::user()->workAt === 0) {
            return $this->viewAdminLayout('checkin.timekeepingworkat', $this->data);
        } else {
            $ip_company = MasterData::where('DataValue', 'like', 'CD001')->first();
            if ($this->getIp() == null || !$ip_company || $ip_company->DataDescription != $this->getIp()) {
                $request->session()->flash('error_ip', 'Địa chỉ chấm công không hợp lệ');
                return redirect()
                ->route('admin.TimekeepingNew');
            }

            return $this->viewAdminLayout('checkin.qrcode', $this->data);
        }
    }

    public function getIp() {
        try {
            // $location = file_get_contents('http://ipinfo.io/');
            // $location = json_decode($location);
			
			 if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
				 $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
				 $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
			 }
			$client  = @$_SERVER['HTTP_CLIENT_IP'];
			$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
			$remote  = $_SERVER['REMOTE_ADDR'];

			if(filter_var($client, FILTER_VALIDATE_IP)){
				$clientIp = $client;
			}
			elseif(filter_var($forward, FILTER_VALIDATE_IP)){
				$clientIp = $forward;
			}
			else{
				$clientIp = $remote;
			}

			return $clientIp;
        } catch (\Exception $e) {
            return null;
        }
    }


    public function checkIP(Request $request){
        $now = Carbon::now()->format(self::FOMAT_DB_YMD_HI);
        $subMinutes = Carbon::now()->subMinutes(15)->format(self::FOMAT_DB_YMD_HI);
        if ($this->getIp() == '') {
            Session::put('checkin', 'checkinfail');
            Session::put('checkin-mess',  'Local không cần chấm công');
            return redirect()->route('qrCode');
        }
        if (!Auth::check()) {
            Session::put('checkin', 'checkinfail');
            Session::put('checkin-mess',  __('admin.error.login'));
            return redirect()->route('qrCode');
        }
        $ip_company = MasterData::where('DataValue', 'like', 'CD001')->first();
        if (!$ip_company) {
            Session::put('checkin', 'checkinfail');
            Session::put('checkin-mess',  __('admin.error.ip'));
            return redirect()->route('qrCode');
        }
        if ($ip_company->DataDescription != $this->getIp()) {
            Session::put('checkin', 'checkinfail');
            Session::put('checkin-mess',  __('admin.error.checkin-ip'));
            return redirect()->route('qrCode');
        }
        $check_15m = CheckinHistory::where('UserID', '=', Auth::user()->id)
            ->where('CheckinTime', '>', $subMinutes)
            ->first();
        if ($check_15m) {
            Session::put('checkin', 'checkinfail');
            Session::put('checkin-mess', 'Bạn đã chấm công. Vui lòng đợi ' . (15-Carbon::parse($now)->diffInMinutes(Carbon::parse($check_15m->CheckinTime))) .' phút để chấm công tiếp.');
            return redirect()->route('qrCode');
        }
        try {
            $check_in_history = new CheckinHistory();
            $check_in_history->UserID = Auth::user()->id;
            $check_in_history->QRCodeID = QrCode::query()->orderBy('SDate', 'Desc')->first()->id;
            $check_in_history->DeviceName = gethostname();
            $check_in_history->OsVersion = $request->ip();
            $check_in_history->DeviceInfo = $_SERVER['HTTP_USER_AGENT'];
            $check_in_history->Type = 'IP Address';
            $check_in_history->CheckinTime = $now;
            $check_in_history->RequestTime = $now;
            $check_in_history->save();

            $check_checkin = Timekeeping::where('UserID', Auth::user()->id)
                ->where('Date', '=', Carbon::parse($now)->toDateString())->first();
            if (isset($check_checkin) && $check_checkin && isset($check_checkin->TimeIn) && $check_checkin->TimeIn != '' && $check_checkin->TimeIn != '00:00:00') {
                $check_checkin->TimeOut = Carbon::parse($now)->toTimeString();
                $check_checkin->save();
            } elseif (isset($check_checkin) && $check_checkin && (!isset($check_checkin->TimeIn) || $check_checkin->TimeIn == '' || $check_checkin->TimeIn == '00:00:00')) {
                $check_checkin->TimeIn = Carbon::parse($now)->toTimeString();
                $check_checkin->save();
            }else {
                $check_checkin = new Timekeeping();
                $check_checkin->UserID = Auth::user()->id;
                $check_checkin->Day = Carbon::parse($now)->format('d');
                $check_checkin->Date = Carbon::parse($now)->toDateString();
                $check_checkin->TimeIn = Carbon::parse($now)->toTimeString();
                $check_checkin->TimeOut = null;
                $check_checkin->STimeOfDay = Auth::user()->STimeOfDay;
                $check_checkin->ETimeOfDay = Auth::user()->ETimeOfDay;
                $check_checkin->save();
            }

//            $body = [
//                'type'  => 'text',
//                'msg'   => asset('audio/translate_tts.mp3')
//            ];
//
//            Controller::sendNoticationRealTime('fcGSNf10OjEdqEFzSJV9F0:APA91bFcGXRfJynBE5mAFlRVzNeVzxQml_VNgC-thRSFnykBk-rQwyXTT_VmRRkPXCNF7KoebesjFTfgau0mZTRwRRuWyHKcNOxUwXN5a8UTEGzIrQPZ3auyIJLpigyaC_PTSbsjmEc4', '', $body);

            Session::put('checkin', 'checkinsuccess');
            Session::put('checkin-mess',  __('admin.success.check-in'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return redirect()->route('qrCode');
    }
}
