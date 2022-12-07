<?php

namespace App\Exports;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\AbsencesTimekeepingSheet;
use App\Exports\Sheets\t_AbsencesTimekeepingSheet;
use App\Http\Controllers\Admin\AdminController;
use Carbon\Carbon;

class AbsencesTimekeepingExport extends AdminController implements WithMultipleSheets
{
	use Exportable;
    protected $records;
    function __construct($records) {
        $this->records = $records;
    }
    // public function sheets(): array
    // {
    // 	$this->data = $this->records;
    // 	$arrayYear=[];
    //     $numMonth = Carbon::parse($this->fncDateTimeConvertFomat($this->data['request']['date'][0],self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))->diffInMonths($this->fncDateTimeConvertFomat($this->data['request']['date'][1],self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
    //     if($numMonth==0&&Carbon::parse($this->fncDateTimeConvertFomat($this->data['request']['date'][0],self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))->month!=Carbon::parse($this->fncDateTimeConvertFomat($this->data['request']['date'][1],self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))->month)
    //     	$numMonth =1;
    //     for ($i=0; $i <= $numMonth; $i++) { 
    //     	$date = Carbon::parse($this->fncDateTimeConvertFomat($this->data['request']['date'][0],self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
    //     	if($i>0)
    //     		$date = Carbon::parse($this->fncDateTimeConvertFomat($this->data['request']['date'][0],self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))->addMonth($i);
    //     	if(!in_array($date->year, $arrayYear))
    //     		array_push($arrayYear,$date->year);
    //     }
    //     $sheets = [];
    //     foreach($arrayYear as $Year){
	        // $sheets[] = new AbsencesTimekeepingSheet('TK vắng mặt '.$Year,$this->data,$Year);
    // 	}
    // 	return $sheets;
    // }
    public function sheets(): array
    {
    	$this->data = $this->records;
	    $sheets[] = new t_AbsencesTimekeepingSheet('TKVM',$this->data);
    	return $sheets;
    }
}