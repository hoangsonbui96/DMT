<?php

namespace App\Exports;

use App\Equipment;
use App\EquipmentRegistration;
use App\EquipmentType;
use App\EquipmentUsingHistory;
use App\MasterData;
use App\Room;
use App\User;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\EqExportQRSheet;

class EqExportQR extends  Controller implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    protected $request;
    function __construct($request) {
        $this->request = $request;
    }
    public function sheets(): array
    {
        $requests =  $this->request;
        $type_id= $requests->type_id;
        $status_id= $requests->status_id;
        $room_id= $requests->room_id;
        $warranty= $requests->warranty;
        $user_owner= $requests->user_owner;
        $sDate= $requests->sDate;
        $eDate= $requests->eDate;
        $search= $requests->search;
        $list = Equipment::query()->orderBy('id', 'desc');
        if(isset($search)){
            $one = Equipment::query()->select('equipment.id','equipment.code','equipment.name','equipment.period_date','equipment.serial_number','equipment.provider','equipment.info','equipment_types.type_name','rooms.Name','users.FullName')
                ->leftJoin('rooms','rooms.id','=','equipment.room_id')
                ->leftJoin('users','users.id','=','equipment.user_owner')
                ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
                ->first();
            if($one){
                $one = $one->toArray();
                if(array_key_exists('search', $requests->input())){
                    if(null !== $requests->input('search')){
                    $list = $list->select('equipment.*')
                                ->leftJoin('rooms','rooms.id','=','equipment.room_id')
                                ->leftJoin('users','users.id','=','equipment.user_owner')
                                ->leftJoin('equipment_types','equipment_types.type_id','=','equipment.type_id')
                        ->where(function ($query) use ($one, $requests){
                        foreach($one as $key=>$value){
                            if($key == 'Name') {
                                $query->orWhere('rooms.'.$key, 'like', '%'.$requests->input('search').'%');
                            }
                            else if($key == 'FullName') {
                                $query->orWhere('users.'.$key, 'like', '%'.$requests->input('search').'%');
                            }
                            else if($key == 'type_name') {
                                $query->orWhere('equipment_types.'.$key, 'like', '%'.$requests->input('search').'%');
                            }else{
                                if(in_array($key, ['provider', 'deal_date'])){
                                    $query->orWhereRaw('(DATE_FORMAT(equipment.'.$key.',"%d/%m/%Y")) like ?', '%'.$requests->input('search').'%' );
                                }else{
                                    if(in_array($key, ['code', 'name','serial_number','info'])){
                                    $query->orWhere('equipment.'.$key, 'like', '%'.$requests->input('search').'%');
                                    }
                                }
                            }
                            $query->orWhereRaw('(DATE_FORMAT(equipment.provider,"%d/%m/%Y")) like ?', '%'.$requests->input('search').'%' );
                            $query->orWhereRaw('(DATE_FORMAT(equipment.deal_date,"%d/%m/%Y")) like ?', '%'.$requests->input('search').'%' );
                        }
                        
                    });
                }
                }
            }
        }
        // $list = $list->get();
        //sreach    
        if(isset($type_id) && $type_id != ''){
            $list = $list->where('equipment.type_id',$type_id);
        }
        if(isset($status_id)&& $status_id!=''){
            $list = $list->where('equipment.status_id',$status_id);
        }
        if(isset($room_id)&& $room_id!=''){
            $list = $list->where('equipment.room_id',$room_id);
        }
        
        if(isset($user_owner)&& $user_owner!= 0){
            $list = $list->where('equipment.user_owner',$user_owner);
        }else if(isset($user_owner) && $user_owner == 0 && $user_owner !='undefined'){ 
            $list = $list->where('equipment.user_owner',$user_owner);
        }

        if(isset($sDate)&& $sDate!=''||isset($eDate)&& $eDate!=''){
            if($sDate !== null && $eDate !== null)
                $list = $list->where('equipment.deal_date', '>=', $this->fncDateTimeConvertFomat($sDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))
                    ->where('equipment.deal_date', '<=', $this->fncDateTimeConvertFomat($eDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
            else if($sDate !== null && $eDate == null)
            $list = $list->where('equipment.deal_date', '>=', $this->fncDateTimeConvertFomat($sDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
            else if($sDate == null && $eDate !== null)
            $list = $list->where('equipment.deal_date', '<=', $this->fncDateTimeConvertFomat($eDate, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
        }

        if(isset($warranty)&& $warranty!=''){
            if($warranty == 1){
                $list = $list->where('equipment.period_date', '>=', Carbon::now()->startOfDay());
            }
            if($warranty == 2){
                $list = $list->where('equipment.period_date', '<', Carbon::now()->startOfDay());
            }
        }
        $list = $list->groupBy('equipment.user_owner');
        $list = $list->get();
        $sheets = [];

        $request = new \Illuminate\Http\Request;
        $num = 0;
        foreach ($list as $key => $value) {
            $record = User::find($value->user_owner);
            if($record != null){
                $sheets[] = new EqExportQRSheet($record, $requests, $request);
            }else{
                if($value->user_owner == 0){
                    $value->FullName = 'Văn phòng';
                }else{
                    $value->FullName = 'Vô chủ';
                }
                $value->id = $value->user_owner;
                $sheets[] = new EqExportQRSheet($value, $requests, $request);
            }
        }
        return $sheets;
    }
}