<?php

namespace App\Exports\Sheets;

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
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Sheet;
use App\MasterData;
use App\Model\Absence;

class AbsencesTimekeepingSheet extends AdminController implements FromView, WithTitle, WithEvents
{
	private $records;
	private $Year;
	private $title;
    public function __construct($title,$records,$Year){
        $this->title = $title;
        $this->records = $records;
        $this->Year = $Year;
        $this->arrayMoth=[];
        $numMonth = Carbon::parse($this->fncDateTimeConvertFomat($this->records['request']['date'][0],self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))->diffInMonths($this->fncDateTimeConvertFomat($this->records['request']['date'][1],self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
        if($numMonth==0&&Carbon::parse($this->fncDateTimeConvertFomat($this->records['request']['date'][0],self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))->month!=Carbon::parse($this->fncDateTimeConvertFomat($this->records['request']['date'][1],self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))->month)
        	$numMonth =1;
        for ($i=0; $i <= $numMonth; $i++) { 
        	$date = Carbon::parse($this->fncDateTimeConvertFomat($this->records['request']['date'][0],self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD));
        	if($i>0)
        		$date = Carbon::parse($this->fncDateTimeConvertFomat($this->records['request']['date'][0],self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD))->addMonth($i);
        	if($this->Year==$date->year)
        		array_push($this->arrayMoth, $date->year.'-'.$date->month);
        }
        $this->columexcel=["M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AX","AY","AZ","BA","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP","BQ","BR","BS","BT","BU","BV","BX","BY","BZ","CA","CB","CC","CD","CE","CF","CG","CH","CI","CJ","CK","CL","CM","CN","CO","CP","CQ","CR","CS","CT","CU","CV","CX","CY","CZ","DA","DB","DC","DD"];
    }
    public function view(): View\View
    {
    	$arrayOffWorkH = [];
        $arrayOffWorkT = [];
        $arrayOutH = [];
        $arrayOutT = [];
        $arrayLateH = [];
        $arrayLateT = [];
        $arrayEarlyH = [];
        $arrayEarlyT = [];
        $num =0;
    	$TimeInPMLate = "13:30";
        $Master1 = MasterData::where('DataValue','WT001')->first();
        $Master2 = MasterData::where('DataValue','WT002')->first();
        $reason = MasterData::where('Name','Ra ngoài')->first()->DataValue;
        $TimeOutAM = $Master2->Name;
        $TimeInPM = $Master2->DataDescription;
    	$this->data = $this->records;
        $this->data['types'] = ["Nghỉ phép","Ra ngoài","Đi muộn","Về sớm"];
        $this->data['calculations'] = ["số giờ","số lần"];
        $this->data['arraydata']=array();
        $columnumber=0;
        foreach ($this->arrayMoth as $month) {
        	array_push($arrayOffWorkH, $this->columexcel[$num*8]);
        	array_push($arrayOffWorkT, $this->columexcel[($num*8)+1]);
        	array_push($arrayOutH, $this->columexcel[($num*8)+2]);
        	array_push($arrayOutT, $this->columexcel[($num*8)+3]);
        	array_push($arrayLateH, $this->columexcel[($num*8)+4]);
        	array_push($arrayLateT, $this->columexcel[($num*8)+5]);
        	array_push($arrayEarlyH, $this->columexcel[($num*8)+6]);
        	array_push($arrayEarlyT, $this->columexcel[($num*8)+7]);
        	$columnumber=($num*8)+20;
        	$num++;
        	foreach ($this->data['User'] as $value) {
	        	$lateTimes = 0;
	            $lateHours = 0;
	            $soonTimes = 0;
	            $soonHours = 0;
	            $offWorkTimes = 0;
	            $offWorkHours = 0;
	            $outTimes = 0;
	            $outHours = 0;
	            $dem=0;
	            $absences = Absence::query()
                ->select('absences.SDate','absences.EDate')
                ->join('master_data', 'master_data.DataValue', 'absences.MasterDataValue')
                ->where('UID', isset($value) ? $value->id : '')
                ->where('SDate', '>=' , isset($this->data['request']['date'][0])?Carbon::parse($this->fncDateTimeConvertFomat($this->data['request']['date'][0],self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD)):Carbon::now()->startOfMonth())
                ->where('EDate', '<=', isset($this->data['request']['date'][1])?Carbon::parse($this->fncDateTimeConvertFomat($this->data['request']['date'][1],self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD)):Carbon::now()->endOfMonth())
                ->where('MasterDataValue', $reason)
                ->get();
	        	foreach ($this->data['dataquery'] as $item) {
		        	if(Carbon::parse($item->Date)>=Carbon::parse($month)->startOfMonth()&&Carbon::parse($item->Date)<=Carbon::parse($month)->endOfMonth()&&$value->id==$item->UserID){
		        		if($item->UserID==$value->id){
		                    $dayOfTheWeek = Carbon::parse($item->Date)->dayOfWeek;
		                    $weekday = self::WEEK_MAP[$dayOfTheWeek];
		                    if($item->TimeIn!=null&&((Carbon::parse($value->STimeOfDay)->diffInMinutes(Carbon::parse($item->TimeIn),false)>0)&&(Carbon::parse($item->TimeIn)<Carbon::parse($TimeOutAM))&&(Carbon::parse($value->STimeOfDay)->diffInMinutes($TimeOutAM,false)>Carbon::parse($value->STimeOfDay)->diffInMinutes(Carbon::parse($item->TimeIn),false)))){
		                        $rangelate =(Carbon::parse($value->STimeOfDay)->diffInMinutes(Carbon::parse($item->TimeIn),false));
		                        $lateTimes += 1;
		                        $lateHours += $rangelate/60;
		                    }elseif($item->TimeIn!=null&&((Carbon::parse($TimeInPMLate)->diffInMinutes(Carbon::parse($item->TimeIn),false)>0)&&(Carbon::parse($item->TimeIn)<Carbon::parse($value->ETimeOfDay))&&(Carbon::parse($TimeInPMLate)->diffInMinutes($value->ETimeOfDay,false)>Carbon::parse($TimeInPMLate)->diffInMinutes(Carbon::parse($item->TimeIn),false)))){
		                        $rangelate =(Carbon::parse($TimeInPMLate)->diffInMinutes(Carbon::parse($item->TimeIn),false));
		                        $lateTimes += 1;
		                        $lateHours += $rangelate/60;
		                    }
		                    else $rangelate = 0;
		                    if($item->TimeOut!=null&&((Carbon::parse($TimeInPM)->diffInMinutes($value->ETimeOfDay,false))>(Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($value->ETimeOfDay),false)))&& ((Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($value->ETimeOfDay),false))>0)&&((Carbon::parse($TimeInPM)<Carbon::parse($item->TimeOut)))&&(Carbon::parse($item->TimeOut)<Carbon::parse($value->ETimeOfDay))){
		                        $rangeearly =(Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($value->ETimeOfDay),false));
		                        $soonTimes += 1;
		                        $soonHours += $rangeearly/60;
		                    }elseif($item->TimeOut!=null&&((Carbon::parse($value->STimeOfDay)->diffInMinutes($TimeOutAM,false))>(Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($TimeOutAM),false)))&&((Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($TimeOutAM),false))>0)&&((Carbon::parse($value->STimeOfDay)<Carbon::parse($item->TimeOut)))&&(Carbon::parse($item->TimeOut)<Carbon::parse($TimeOutAM))){
		                        $rangeearly =Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($TimeOutAM),false);
		                        $soonTimes += 1;
		                        $soonHours += $rangeearly/60;
		                    }
		                    else {
		                        $rangeearly =0;
		                    }

		                    $compensate = 0;
		                    if(($weekday == 'T7'||$weekday == 'CN')&&$compensate==0)
		                    {
		                        $rangeoffWork=0;
		                    }else {
		                        if(($item->TimeOut == null&&$item->TimeIn == null)||($compensate==1&&$item->TimeOut == null&&$item->TimeIn == null)){
		                            $rangeoffWork = Carbon::parse($value->STimeOfDay)->diffInMinutes(Carbon::parse($TimeOutAM),false)+ Carbon::parse($TimeInPM)->diffInMinutes(Carbon::parse($value->ETimeOfDay),false);
		                            $offWorkTimes+= 1;
		                            $offWorkHours+= $rangeoffWork/60;
		                        }
		                        else if(($item->TimeIn != null&&$item->TimeOut != null&&Carbon::parse($TimeInPM)>=Carbon::parse($item->TimeOut))) {
		                            $rangeoffWork = Carbon::parse($TimeInPM)->diffInMinutes(Carbon::parse($value->ETimeOfDay),false);
		                            $offWorkTimes+= 1;
		                            $offWorkHours+= $rangeoffWork/60;
		                        }
		                        else if(($item->TimeIn != null&&$item->TimeOut != null&&Carbon::parse($TimeOutAM)<=Carbon::parse($item->TimeIn))) {
		                            $rangeoffWork =Carbon::parse($value->STimeOfDay)->diffInMinutes(Carbon::parse($TimeOutAM),false);
		                            $offWorkTimes+= 1;
		                            $offWorkHours+= $rangeoffWork/60;
		                        }
		                        else{
		                            $rangeoffWork=0;
		                        }
		                    }
		                    $out =0;
		                    foreach($absences as $absence){
		                        if(Carbon::parse($absence->SDate)->format('Y-m-d')==Carbon::parse($item->Date)->format('Y-m-d')){
		                           $out += Carbon::parse($absence->SDate)->diffInMinutes(Carbon::parse($absence->EDate),false);
		                            $outTimes+=1; 
		                        }
		                    }
		                    if($out!=0){
		                        $outHours+=$out/60;
		                    }
		                }
		        	}
		        	
		        }
	        	$totalH = $lateHours+$soonHours+$offWorkHours+$outHours;
        		$totalT = $lateTimes+$soonTimes+$offWorkTimes+$outTimes;
	        	if (!array_key_exists($value->id,$this->data['arraydata'])){
	        		$this->data['arraydata'][$value->id]=array();
	        	}
	        	array_push($this->data['arraydata'][$value->id], $offWorkHours,$offWorkTimes,$outHours,$outTimes,$lateHours,$lateTimes,$soonHours,$soonTimes);
	        }
        }
        $this->data['arrayOffWorkH'] =$arrayOffWorkH;
        $this->data['arrayOffWorkT'] =$arrayOffWorkT;
        $this->data['arrayOutH'] =$arrayOutH;
        $this->data['arrayOutT'] =$arrayOutT;
        $this->data['arrayLateH'] =$arrayLateH;
        $this->data['arrayLateT'] =$arrayLateT;
        $this->data['arrayEarlyH'] =$arrayEarlyH;
        $this->data['arrayEarlyT'] =$arrayEarlyT;
        $this->data['arrayMoth'] =$this->arrayMoth;
        $this->data['Year'] =$this->Year;
        $this->data['columnumber'] =$columnumber;
        return $this->viewAdminLayout('absence.absences-timekeeping-export', $this->data);
    }
     public function title(): string
    {
        return $this->title;
    }
    public function registerEvents(): array
    {
    	$this->data = $this->records;
    	$arrayOffWorkH = [];
        $arrayOffWorkT = [];
        $arrayOutH = [];
        $arrayOutT = [];
        $arrayLateH = [];
        $arrayLateT = [];
        $arrayEarlyH = [];
        $arrayEarlyT = [];
    	$num=0;
    	foreach ($this->arrayMoth as $month) {
    		array_push($arrayOffWorkH, $this->columexcel[$num*8]);
        	array_push($arrayOffWorkT, $this->columexcel[($num*8)+1]);
        	array_push($arrayOutH, $this->columexcel[($num*8)+2]);
        	array_push($arrayOutT, $this->columexcel[($num*8)+3]);
        	array_push($arrayLateH, $this->columexcel[($num*8)+4]);
        	array_push($arrayLateT, $this->columexcel[($num*8)+5]);
        	array_push($arrayEarlyH, $this->columexcel[($num*8)+6]);
        	array_push($arrayEarlyT, $this->columexcel[($num*8)+7]);
        	$columname=$this->columexcel[($num*8)+7];
        	$num++;
        }
    	$intTotalRow=count($this->data['User'])+4;
    	return [
            AfterSheet::class => function(AfterSheet $event) use ($intTotalRow,$columname,$arrayOffWorkH,$arrayOffWorkT,$arrayOutH,$arrayOutT,$arrayLateH,$arrayLateT,$arrayEarlyH,$arrayEarlyT){
            	$event->sheet->styleCells(
                    'A1',
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'bold'  => true,
                            'size'  => 24,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => 'c6e2ff']
                        ]
                    ]
                );
            	$event->sheet->styleCells(
                    'A1:'.$columname.$intTotalRow,
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'name' => 'Times News Roman',
                            'style' => 'italic'
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                        ],
                    ]
                );
                $event->sheet->styleCells(
                    'E2:L2',
                    [
                    	'font' => [
                            'color' => ['rgb' => 'f44336'],
                            'bold'  => true,
                            'italic' => true,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => 'ffeb3b']
                        ]
                    ]
                );
                $event->sheet->styleCells(
                    'C2',
                    [
                    	'font' => [
                            'color' => ['rgb' => 'f44336'],
                            'bold'  => true,
                            'italic' => true,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => 'ffeb3b']
                        ]
                    ]
                );
                $event->sheet->styleCells(
                    'D2',
                    [
                    	'font' => [
                            'color' => ['rgb' => 'f44336'],
                            'bold'  => true,
                            'italic' => true,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => 'ffeb3b']
                        ]
                    ]
                );
                $event->sheet->styleCells(
                    'E3:F'.$intTotalRow,
                    [
                    	'font' => [
                            'color' => ['rgb' => '000000'],
                            'bold'  => true,
                            'italic' => true,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => '2f75b5']
                        ]
                    ]
                );
                $event->sheet->styleCells(
                    'G3:H'.$intTotalRow,
                    [
                    	'font' => [
                            'color' => ['rgb' => '000000'],
                            'bold'  => true,
                            'italic' => true,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => '92d050']
                        ]
                    ]
                );
                $event->sheet->styleCells(
                    'I3:J'.$intTotalRow,
                    [
                    	'font' => [
                            'color' => ['rgb' => '000000'],
                            'bold'  => true,
                            'italic' => true,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => 'ffd966']
                        ]
                    ]
                );
                $event->sheet->styleCells(
                    'K3:L'.$intTotalRow,
                    [
                    	'font' => [
                            'color' => ['rgb' => '000000'],
                            'bold'  => true,
                            'italic' => true,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => 'f4b084']
                        ]
                    ]
                );
                $event->sheet->styleCells(
                        'M2:'.$columname.'2',
                        [
                            'font' => [
                                'color' => ['rgb' => '000000'],
                                'size'  => 10,
                                'name' => 'Times News Roman',
                                'style' => 'italic'
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                ],
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => ['rgb' => 'f44336']
                            ]
                        ]
                    );
                foreach ($arrayOffWorkH as $value){
                	$event->sheet->styleCells(
                        $value.'3:'.$value.$intTotalRow,
                        [
                            'font' => [
                                'color' => ['rgb' => '000000'],
                                'size'  => 10,
                                'name' => 'Times News Roman',
                                'style' => 'italic'
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                ],
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => ['rgb' => 'e2efda']
                            ]
                        ]
                    );
                }
                foreach ($arrayOffWorkT as $value){
                	$event->sheet->styleCells(
                        $value.'3:'.$value.$intTotalRow,
                        [
                            'font' => [
                                'color' => ['rgb' => '000000'],
                                'size'  => 10,
                                'name' => 'Times News Roman',
                                'style' => 'italic'
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                ],
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => ['rgb' => 'e2efda']
                            ]
                        ]
                    );
                }
                foreach ($arrayOutH as $value){
                	$event->sheet->styleCells(
                        $value.'3:'.$value.$intTotalRow,
                        [
                            'font' => [
                                'color' => ['rgb' => '000000'],
                                'size'  => 10,
                                'name' => 'Times News Roman',
                                'style' => 'italic'
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                ],
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => ['rgb' => '92d050']
                            ]
                        ]
                    );
                }
                foreach ($arrayOutT as $value){
                	$event->sheet->styleCells(
                        $value.'3:'.$value.$intTotalRow,
                        [
                            'font' => [
                                'color' => ['rgb' => '000000'],
                                'size'  => 10,
                                'name' => 'Times News Roman',
                                'style' => 'italic'
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                ],
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => ['rgb' => '92d050']
                            ]
                        ]
                    );
                }
                foreach ($arrayLateH as $value){
                	$event->sheet->styleCells(
                        $value.'3:'.$value.$intTotalRow,
                        [
                            'font' => [
                                'color' => ['rgb' => '000000'],
                                'size'  => 10,
                                'name' => 'Times News Roman',
                                'style' => 'italic'
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                ],
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => ['rgb' => 'b4c6e7']
                            ]
                        ]
                    );
                }
                foreach ($arrayLateT as $value){
                	$event->sheet->styleCells(
                        $value.'3:'.$value.$intTotalRow,
                        [
                            'font' => [
                                'color' => ['rgb' => '000000'],
                                'size'  => 10,
                                'name' => 'Times News Roman',
                                'style' => 'italic'
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                ],
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => ['rgb' => 'b4c6e7']
                            ]
                        ]
                    );
                }
                foreach ($arrayEarlyH as $value){
                	$event->sheet->styleCells(
                        $value.'3:'.$value.$intTotalRow,
                        [
                            'font' => [
                                'color' => ['rgb' => '000000'],
                                'size'  => 10,
                                'name' => 'Times News Roman',
                                'style' => 'italic'
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                ],
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => ['rgb' => 'd0cece']
                            ]
                        ]
                    );
                }
                foreach ($arrayEarlyT as $value){
                	$event->sheet->styleCells(
                        $value.'3:'.$value.$intTotalRow,
                        [
                            'font' => [
                                'color' => ['rgb' => '000000'],
                                'size'  => 10,
                                'name' => 'Times News Roman',
                                'style' => 'italic'
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                ],
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => ['rgb' => 'd0cece']
                            ]
                        ]
                    );
                }
            },
        ];
    }
}