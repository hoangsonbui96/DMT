<?php

namespace App\Http\Controllers;

use App\MasterData;
use App\Model\QrCode;
use App\RoleScreenDetail;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeEx;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ApiBaseController extends BaseController
{
    use AuthorizesRequests;

    const FOMAT_DMY = 'd/m/Y';
    const FOMAT_DB_YMD = 'Y-m-d';
    const FOMAT_DB_YMD_HI = 'Y-m-d H:i';
    const FOMAT_DB_YMD_HIS = 'Y-m-d H:i:s';
    const FOMAT_DB_DMY_HI = 'd/m/Y - H:i';
    const START_LOAD_QR = '06:00';
    const END_LOAD_QR = '18:00';
    const TIME_LOAD_QR = 20;
    const CONFIG_AKB_ONE = ['register' => 1];

    protected $menu;
    protected $role_list = ['Edit' => null, 'Add' => null, 'Delete' => null, 'View' => null, 'Export' => null, 'Review' => null];

    public function detailRoleScreen($alias){
        foreach ($this->role_list as $key => &$_role){
            $alias_role = $key == 'View' ? $alias : $alias.$key;
            $_role = RoleScreenDetail::where('alias', '=', $alias_role)->first();
        }
    }

    public function getAllRoleUser() {
        $role = AdminController::getAllRoleAssign();
        return AdminController::responseApi(200, null, null, $role);
    }

    public function getQrCode(Request $request) {

        $start_load_qr = MasterData::where('DataValue', '=', 'CD002')->first();
        $end_load_qr = MasterData::where('DataValue', '=', 'CD003')->first();

        $start_load_qr = !$start_load_qr ? self::START_LOAD_QR : $start_load_qr->DataDescription;
        $end_load_qr = !$end_load_qr ? self::END_LOAD_QR : $end_load_qr->DataDescription;
        $device_token = '';

        $dateE = Carbon::now()->format(self::FOMAT_DB_YMD).' '.$end_load_qr;
        $check_exist = QrCode::where('EDate', '>', $dateE)->first();
        $now = Carbon::now()->format(self::FOMAT_DB_YMD_HIS);
        $after_time_string = Carbon::now()->addSeconds(self::TIME_LOAD_QR)->format(self::FOMAT_DB_YMD_HIS);
        if($check_exist){
            $string_code = $check_exist->QRCode;
            $data['query'] = 1;
        }else{
            $data['query'] = 0;
            $string_code = bin2hex(random_bytes(20));
            $qr_code_checkin = new QrCode();
            $qr_code_checkin->QRCode = $string_code;
            $qr_code_checkin->SDate = $now;
            $qr_code_checkin->EDate = $after_time_string >= Carbon::parse($end_load_qr)->format(self::FOMAT_DB_YMD_HIS) ? Carbon::parse($start_load_qr)->addDay()->format(self::FOMAT_DB_YMD_HIS) : $after_time_string;
            $qr_code_checkin->save();
        }
        $data['date'] = Carbon::now()->format(self::FOMAT_DB_DMY_HI);
        $data['test'] = $string_code;
        $data['string_code'] = base64_encode(QrCodeEx::format('png')->merge('./imgs/logo_akb_200_200.png', 0.3 , true)->margin(0)->size(260)->errorCorrection('H')->generate(Carbon::parse($now)->format(self::FOMAT_DMY) . '---' . $string_code . $device_token));
        return AdminController::responseApi(200, null, null, $data);
    }

    public function getConfig() {
        $config = MasterData::where('DataValue', 'like', 'CD004')->first();
        if (!$config) {
            $configAKBONE = self::CONFIG_AKB_ONE;
        } else {
            $configAKBONE = @json_decode($config->DataDescription);
        }
        return AdminController::responseApi(200, null, null, $configAKBONE);
    }
    public function getDateTime(){
        $data['date'] = Carbon::now()->format(self::FOMAT_DB_DMY_HI);
        return AdminController::responseApi(200, null, null, $data);
    }
}
