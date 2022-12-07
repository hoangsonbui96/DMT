<?php

namespace App\Exports\Sheets;

use App\Equipment;
use App\EquipmentRegistration;
use App\EquipmentType;
use App\EquipmentUsingHistory;
use App\MasterData;
use App\Room;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class EqExportQRSheet extends AdminController implements FromView, WithTitle, WithColumnFormatting, WithEvents
{
    private $record;
    public function __construct($record,$requests){
        $this->record = $record;
        $this->requests = $requests;
    }
    public function collection()
    {
        return Equipment::all();
    }
    // public function view(): View\View
    // {
    //     //chuan bi du lieu
    // }
    public function view(): View\View
    {
        $request = $this->requests;
        $type_id= $request['type_id'];
        $status_id= $request['status_id'];
        $room_id= $request['room_id'];
        $warranty= $request['warranty'];
        $user_owner= $request['user_owner'];
        $sDate= $request['sDate'];
        $eDate= $request['eDate'];
        $search= $request['search'];
        $list = Equipment::query()->orderBy('id', 'desc');
        if(isset($search)){
            $one = Equipment::query()->select('equipment.id','equipment.code','equipment.name','equipment.period_date','equipment.serial_number','equipment.provider','equipment.info','equipment_types.type_name','rooms.Name','users.FullName')
                ->join('rooms','rooms.id','=','equipment.room_id')
                ->join('users','users.id','=','equipment.user_owner')
                ->join('equipment_types','equipment_types.type_id','=','equipment.type_id')
                ->first();
            if($one){
                $one = $one->toArray();
                if(array_key_exists('search', $request->input())){
                    if(null !== $request->input('search')){
                    $list = $list->select('equipment.*')
                                ->leftJoin('rooms','rooms.id','=','equipment.room_id')
                                ->leftJoin('users','users.id','=','equipment.user_owner')
                                ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
                        ->where(function ($query) use ($one, $request){
                        foreach($one as $key=>$value){
                            if($key == 'Name') {
                                $query->orWhere('rooms.'.$key, 'like', '%'.$request->input('search').'%');
                            }
                            else if($key == 'FullName') {
                                $query->orWhere('users.'.$key, 'like', '%'.$request->input('search').'%');
                            }
                            else if($key == 'type_name') {
                                $query->orWhere('equipment_types.'.$key, 'like', '%'.$request->input('search').'%');
                            }else{
                                if(in_array($key, ['provider', 'deal_date'])){
                                    $query->orWhereRaw('(DATE_FORMAT(equipment.'.$key.',"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                                }else{
                                    if(in_array($key, ['code', 'name','serial_number','info'])){
                                    $query->orWhere('equipment.'.$key, 'like', '%'.$request->input('search').'%');
                                    }
                                }
                            }
                            $query->orWhereRaw('(DATE_FORMAT(equipment.provider,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                            $query->orWhereRaw('(DATE_FORMAT(equipment.deal_date,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                        }

                    });
                }
                }
            }
        }
        $list = $list->get();

        $this->data['rooms'] = Room::query()
            ->select('rooms.id', 'rooms.Name')
            ->join('equipment', 'equipment.room_id', 'rooms.id')
            ->groupBy('rooms.id')
            ->get();
        $this->data['owners'] = User::query()
            ->select('users.id', 'users.FullName')
            ->join('equipment', 'equipment.user_owner', 'users.id')
            ->groupBy('users.id')
            ->get();
        $this->data['eqTypes'] = EquipmentType::query()
            ->select('type_id', 'type_name')
            ->get();
        $this->data['eqStatus'] = MasterData::query()
            ->where('DataKey', 'TB')
            ->get();

        //sreach
        if(isset($type_id)&& $type_id!=''){
            $list = $list->where('type_id',$type_id);
        }
        if(isset($status_id)&& $status_id!=''){
            $list = $list->where('status_id',$status_id);
        }
        if(isset($room_id)&& $room_id!=''){
            $list = $list->where('room_id',$room_id);
        }

        if(isset($user_owner)&& $user_owner!= 0){
            $list = $list->where('user_owner',$user_owner);
        }else if(isset($user_owner) && $user_owner == 0 && $user_owner !='undefined'){
            $list = $list->where('user_owner',$user_owner);
        }

        if(isset($sDate)&& $sDate!=''||isset($eDate)&& $eDate!=''){
            if($sDate !== null && $eDate !== null)
                $list = $list->where('deal_date', '>=', $this->fncDateTimeConvertFomat($sDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))
                    ->where('deal_date', '<=', $this->fncDateTimeConvertFomat($eDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
            else if($sDate !== null && $eDate == null)
            $list = $list->where('deal_date', '>=', $this->fncDateTimeConvertFomat($sDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
            else if($sDate == null && $eDate !== null)
            $list = $list->where('deal_date', '<=', $this->fncDateTimeConvertFomat($eDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
        }

        if(isset($warranty)&& $warranty!=''){
            if($warranty == 1){
                $list = $list->where('period_date', '>=', Carbon::now()->startOfDay());
            }
            if($warranty == 2){
                $list = $list->where('period_date', '<', Carbon::now()->startOfDay());
            }
        }
        $list = $list->where('user_owner',$this->record->id);
        $this->data['Equipments']= $list;
        return $this->viewAdminLayout('eqEquipmentQR-export', $this->data);
    }
    public function title(): string
    {
        return $this->record->FullName;
    }
    public function columnFormats(): array
    {
        return [
        ];
    }
    public function registerEvents(): array
    {
        $request = $this->requests;
        $type_id= $request['type_id'];
        $status_id= $request['status_id'];
        $room_id= $request['room_id'];
        $warranty= $request['warranty'];
        $user_owner= $request['user_owner'];
        $sDate= $request['sDate'];
        $eDate= $request['eDate'];
        $search= $request['search'];
        $list = Equipment::query()->orderBy('id', 'desc');
        if(isset($search)){
            $one = Equipment::query()->select('equipment.id','equipment.code','equipment.name','equipment.period_date','equipment.serial_number','equipment.provider','equipment.info','equipment_types.type_name','rooms.Name','users.FullName')
                ->join('rooms','rooms.id','=','equipment.room_id')
                ->join('users','users.id','=','equipment.user_owner')
                ->join('equipment_types','equipment_types.type_id','=','equipment.type_id')
                ->first();
            if($one){
                $one = $one->toArray();
                if(array_key_exists('search', $request->input())){
                    if(null !== $request->input('search')){
                    $list = $list->select('equipment.*')
                                ->leftJoin('rooms','rooms.id','=','equipment.room_id')
                                ->leftJoin('users','users.id','=','equipment.user_owner')
                                ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
                        ->where(function ($query) use ($one, $request){
                        foreach($one as $key=>$value){
                            if($key == 'Name') {
                                $query->orWhere('rooms.'.$key, 'like', '%'.$request->input('search').'%');
                            }
                            else if($key == 'FullName') {
                                $query->orWhere('users.'.$key, 'like', '%'.$request->input('search').'%');
                            }
                            else if($key == 'type_name') {
                                $query->orWhere('equipment_types.'.$key, 'like', '%'.$request->input('search').'%');
                            }else{
                                if(in_array($key, ['provider', 'deal_date'])){
                                    $query->orWhereRaw('(DATE_FORMAT(equipment.'.$key.',"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                                }else{
                                    if(in_array($key, ['code', 'name','serial_number','info'])){
                                    $query->orWhere('equipment.'.$key, 'like', '%'.$request->input('search').'%');
                                    }
                                }
                            }
                            $query->orWhereRaw('(DATE_FORMAT(equipment.provider,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                            $query->orWhereRaw('(DATE_FORMAT(equipment.deal_date,"%d/%m/%Y")) like ?', '%'.$request->input('search').'%' );
                        }

                    });
                }
                }
            }
        }
        $list = $list->get();
        //sreach
        if(isset($type_id)&& $type_id!=''){
            $list = $list->where('type_id',$type_id);
        }
        if(isset($status_id)&& $status_id!=''){
            $list = $list->where('status_id',$status_id);
        }
        if(isset($room_id)&& $room_id!=''){
            $list = $list->where('room_id',$room_id);
        }

        if(isset($user_owner)&& $user_owner!= 0){
            $list = $list->where('user_owner',$user_owner);
        }else if(isset($user_owner) && $user_owner == 0 && $user_owner !='undefined'){
            $list = $list->where('user_owner',$user_owner);
        }

        if(isset($sDate)&& $sDate!=''||isset($eDate)&& $eDate!=''){
            if($sDate !== null && $eDate !== null)
                $list = $list->where('deal_date', '>=', $this->fncDateTimeConvertFomat($sDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))
                    ->where('deal_date', '<=', $this->fncDateTimeConvertFomat($eDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
            else if($sDate !== null && $eDate == null)
            $list = $list->where('deal_date', '>=', $this->fncDateTimeConvertFomat($sDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
            else if($sDate == null && $eDate !== null)
            $list = $list->where('deal_date', '<=', $this->fncDateTimeConvertFomat($eDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
        }

        if(isset($warranty)&& $warranty!=''){
            if($warranty == 1){
                $list = $list->where('period_date', '>=', Carbon::now()->startOfDay());
            }
            if($warranty == 2){
                $list = $list->where('period_date', '<', Carbon::now()->startOfDay());
            }
        }

        $countRow = 0;
        foreach($list as $item){
            $countRow++;
        }

        $intTotalRow = 5 + $countRow;
        $html1='';
        $numkey = 0;
        $list = $list->where('user_owner',$this->record->id);
        foreach ($list as $key => $value) {
             $numkey = $numkey + 1;
            $imgQrcode = QrCode::format('png')->merge('./imgs/logo_akb_200_200.png', 0.3 , true)->size(150)->errorCorrection('H')->generate(route("exportQR.equipmentQRcode").'?device='.$value->code);
            // $output =  asset('imgs/Qr_code');
            Storage::disk('public')->put('imgs/'.$value->code.'.png', $imgQrcode);
            if(count($list) == $numkey){
                $html1 .= $value->code.'';
            }else{
                $html1 .= $value->code.',';
            }
        }
        $arrcode = explode(',',$html1);
        $num = 0;
        $path = Storage::disk("public")->getDriver()->getAdapter()->getPathPrefix();
        return [
             AfterSheet::class => function(AfterSheet $event) use ($intTotalRow,$arrcode, $path){
                $num =0;
                foreach ($arrcode as $key => $value) {
                    if($num == 0){
                        $num = $num+2;
                    }else{
                        $num = $num + 5;
                     }
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('Logo');
                    $drawing->setDescription('Logo');
                    $drawing->setPath($path. "/imgs/".$value.'.png');
                    $drawing->setHeight(90);
                    $drawing->setCoordinates('A'.$num);
                    $drawing->setWorksheet($event->sheet->getDelegate());
                }
             },
        ];
    }
}
