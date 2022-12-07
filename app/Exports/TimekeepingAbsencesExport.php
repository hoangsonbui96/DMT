<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Http\Controllers\Admin\AdminController;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Illuminate\Contracts\View;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use App\MasterData;
use Carbon\Carbon;
use App\Timekeeping;
use App\CalendarEvent;
use App\User;
use App\Model\Absence;

class TimekeepingAbsencesExport extends AdminController implements FromView, ShouldAutoSize, WithEvents, WithTitle
{
	use Exportable;
    const ARRAY_DATE_COLUM =array("F","G","H","I","J","k","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ");
	function __construct($month, $year,$user) {
        $this->year = $year;
        $this->month = $month;
        $this->user = $user;
    }
    public function view(): View\View
    {
        $this->data['month'] = $this->month;
    	$this->data['user'] = $this->user;
        $Master1 = MasterData::where('DataValue','WT001')->first();
        $Master2 = MasterData::where('DataValue','WT002')->first();
        $reason = MasterData::where('Name','Ra ngoài')->first()->DataValue;
        $TimeOutAM = $Master2->Name;
        $TimeInPM = $Master2->DataDescription;
        $TimeInPMLate = "13:30";
        $totalTimeWork = (Carbon::parse($Master1->Name)->diffInMinutes(Carbon::parse($TimeOutAM))
            + Carbon::parse($TimeInPM)->diffInMinutes(Carbon::parse($Master1->DataDescription)))/60;
        $this->data['data'] = [];
        $this->data['datauser'] = [];
        $this->data['datadate'] = [];
        // print_r($this->user);
        foreach($this->user as $record){
            // echo $record->UserID.'||';
            $this->data['timekeepings'] = Timekeeping::query()
            ->whereMonth('Date', $this->month)
            ->whereYear('Date', $this->year)
            ->where('UserID', $record->UserID)
            ->orderBy('Date', 'asc')
            ->get();
            if(count($this->data['timekeepings']) >0 ){
                $TimeInAM = $this->data['timekeepings']->first()->STimeOfDay;
                if(isset($record->ETimeOfDay)){
                    $TimeOutPM = $this->data['timekeepings']->first()->ETimeOfDay;
                }else{
                    $TimeOutPM = date('H:i:s',strtotime('+'.($totalTimeWork+(Carbon::parse($TimeOutAM)->diffInMinutes(Carbon::parse($TimeInPM)))/60).'hour',strtotime($this->data['timekeepings']->first()->STimeOfDay)));
                }
            }
            else{
                $TimeInAM = $record->STimeOfDay;
                if(isset($record->ETimeOfDay)){
                    $TimeOutPM = $record->ETimeOfDay;
                }else{
                    $TimeOutPM = date('H:i:s',strtotime('+'.($totalTimeWork+(Carbon::parse($TimeOutAM)->diffInMinutes(Carbon::parse($TimeInPM)))/60).'hour',strtotime($record->STimeOfDay)));
                }
            }
            $this->data['userSelect'] = User::find($record->UserID);
            $this->data['timekeepings']->totalKeeping = 0;
            $this->data['timekeepings']->overKeeping = 0;
            $this->data['timekeepings']->lateTimes = 0;
            $this->data['timekeepings']->lateHours = 0;
            $this->data['timekeepings']->soonTimes = 0;
            $this->data['timekeepings']->soonHours = 0;
            $this->data['timekeepings']->offWorkTimes = 0;
            $this->data['timekeepings']->offWorkHours = 0;
            $this->data['timekeepings']->outTimes = 0;
            $this->data['timekeepings']->outHours = 0;

            foreach($this->data['timekeepings'] as $item){
                $dayOfTheWeek = Carbon::parse($item->Date)->dayOfWeek;
                $item->weekday = self::WEEK_MAP[$dayOfTheWeek];
                if($item->TimeOut != Null &&  Carbon::parse($item->TimeOut) > Carbon::parse($TimeOutPM)){
                    $item->N = Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($TimeOutPM));
                    $this->data['timekeepings']->overKeeping += $item->N/60;
                }

                else
                    $item->N = 0;

                //thời gian làm việc
                if(isset($userid->ETimeOfDay)){
                    $floatWorkHours = (Carbon::parse($TimeInAM)->diffInMinutes(Carbon::parse($TimeOutAM))
                                        + Carbon::parse($TimeInPM)->diffInMinutes(Carbon::parse($TimeOutPM)))/60;
                }else{
                    $floatWorkHours = $totalTimeWork;
                }
                if(!is_null($item->TimeIn) && !is_null($item->TimeOut)){
                    //trường hợp vào ra ngoài thời gian làm việc
                    if($item->TimeOut < $TimeInAM || $item->TimeIn > $TimeOutPM
                    || ($item->TimeIn > $TimeOutAM && $item->TimeOut < $TimeInPM)){
                        $item->hours = 0;
                    }elseif($item->TimeIn <= $TimeOutAM && $item->TimeOut >= $TimeInPM){
                        $item->hours = Carbon::parse($item->TimeIn < $TimeInAM ? $TimeInAM : $item->TimeIn)->diffInMinutes(Carbon::parse($item->TimeOut < $TimeOutPM ? $item->TimeOut : $TimeOutPM))/60
                        - Carbon::parse($TimeInPM)->diffInMinutes(Carbon::parse($TimeOutAM))/60;
                        $item->keeping = $item->hours/$floatWorkHours;
                        $this->data['timekeepings']->totalKeeping += $item->keeping;
                    }else{
                        $item->hours = Carbon::parse(
                            $item->TimeIn > $TimeOutAM
                            ? ($item->TimeIn < $TimeInPM ? $TimeInPM : $item->TimeIn)
                            : ($item->TimeIn < $TimeInAM ? $TimeInAM : $item->TimeIn)
                        )
                        ->diffInMinutes(Carbon::parse(
                            $item->TimeOut < $TimeInPM
                            ? ($item->TimeOut > $TimeOutAM ? $TimeOutAM : $item->TimeOut)
                            : ($item->TimeOut > $TimeOutPM ? $TimeOutPM : $item->TimeOut)
                            ))/60;
                        $item->keeping = $item->hours/$floatWorkHours;
                        $this->data['timekeepings']->totalKeeping += $item->keeping;
                    }

                }
                //ngay nghi le, lam bu
                $item->compensate = 0;
                $item->holiday = 0;
                $calendarEvent = CalendarEvent::query()
                    ->select('StartDate','EndDate','Content','Type')
                    ->where('StartDate', '<=' , Carbon::parse($item->Date)->endOfDay())
                    ->where('EndDate', '>=', Carbon::parse($item->Date)->startOfDay())
                    ->where('CalendarID',1)
                    ->where('Content','NOT LIKE','%Du lịch%')
                    ->get();
                foreach($calendarEvent as $calendar){
                    if($calendar->Type == 1)  $item->holiday = 1;
                    elseif($calendar->Type == 0)  $item->compensate = 1;
                }
                //vang mat
                $absences = Absence::query()
                    ->select('absences.SDate','absences.EDate')
                    ->join('master_data', 'master_data.DataValue', 'absences.MasterDataValue')
                    ->where('UID', isset($record->UserID) ? $record->UserID : '')
                    ->where('SDate', '<=' , Carbon::parse($item->Date)->endOfDay())
                    ->where('EDate', '>=', Carbon::parse($item->Date)->startOfDay())
                    ->where('MasterDataValue', $reason)
                    ->get();
                $item->out =0;
                foreach($absences as $absence){
                    $item->out += Carbon::parse($absence->SDate)->diffInMinutes(Carbon::parse($absence->EDate),false);
                    $this->data['timekeepings']->outTimes+=1;
                }
                if($item->out!=0){
                    $this->data['timekeepings']->outHours+=$item->out/60;
                }
                if($item->TimeIn!=null&&((Carbon::parse($this->data['userSelect']->STimeOfDay)->diffInMinutes(Carbon::parse($item->TimeIn),false)>0)&&(Carbon::parse($item->TimeIn)<Carbon::parse($TimeOutAM))&&(Carbon::parse($this->data['userSelect']->STimeOfDay)->diffInMinutes($TimeOutAM,false)>Carbon::parse($this->data['userSelect']->STimeOfDay)->diffInMinutes(Carbon::parse($item->TimeIn),false)))){
                    // $item->late = Carbon::parse($item->TimeIn)->diffInMinutes(Carbon::parse($TimeInAM));
                    $rangelate =(Carbon::parse($this->data['userSelect']->STimeOfDay)->diffInMinutes(Carbon::parse($item->TimeIn),false));
                    $this->data['timekeepings']->lateTimes += 1;
                    $this->data['timekeepings']->lateHours += $rangelate/60;
                }elseif($item->TimeIn!=null&&((Carbon::parse($TimeInPMLate)->diffInMinutes(Carbon::parse($item->TimeIn),false)>0)&&(Carbon::parse($item->TimeIn)<Carbon::parse($this->data['userSelect']->ETimeOfDay))&&(Carbon::parse($TimeInPMLate)->diffInMinutes($this->data['userSelect']->ETimeOfDay,false)>Carbon::parse($TimeInPMLate)->diffInMinutes(Carbon::parse($item->TimeIn),false)))){
                    $rangelate =(Carbon::parse($TimeInPMLate)->diffInMinutes(Carbon::parse($item->TimeIn),false));
                    $this->data['timekeepings']->lateTimes += 1;
                    $this->data['timekeepings']->lateHours += $rangelate/60;
                }
                else $rangelate = 0;

                $item->late = $rangelate;
                if($item->TimeOut!=null&&((Carbon::parse($TimeInPM)->diffInMinutes($this->data['userSelect']->ETimeOfDay,false))>(Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($this->data['userSelect']->ETimeOfDay),false)))&& ((Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($this->data['userSelect']->ETimeOfDay),false))>0)&&((Carbon::parse($TimeInPM)<Carbon::parse($item->TimeOut)))&&(Carbon::parse($item->TimeOut)<Carbon::parse($this->data['userSelect']->ETimeOfDay))){
                    $rangeearly =(Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($this->data['userSelect']->ETimeOfDay),false));
                    $this->data['timekeepings']->soonTimes += 1;
                    $this->data['timekeepings']->soonHours += $rangeearly/60;
                }elseif($item->TimeOut!=null&&((Carbon::parse($this->data['userSelect']->STimeOfDay)->diffInMinutes($TimeOutAM,false))>(Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($TimeOutAM),false)))&&((Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($TimeOutAM),false))>0)&&((Carbon::parse($this->data['userSelect']->STimeOfDay)<Carbon::parse($item->TimeOut)))&&(Carbon::parse($item->TimeOut)<Carbon::parse($TimeOutAM))){
                    $rangeearly =Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($TimeOutAM),false);
                    $this->data['timekeepings']->soonTimes += 1;
                    $this->data['timekeepings']->soonHours += $rangeearly/60;
                }
                else {
                    $rangeearly =0;
                }
                $item->early = $rangeearly;
                if(($item->weekday == 'T7'||$item->weekday == 'CN')&&$item->compensate==0)
                {
                    $rangeoffWork=0;
                }else {
                    if((($item->TimeOut == null&&$item->TimeIn == null)||($item->compensate==1&&$item->TimeOut == null&&$item->TimeIn == null))&&$item->holiday!=1){
                        $rangeoffWork = 1;
                        $this->data['timekeepings']->offWorkTimes+= 1;
                        $this->data['timekeepings']->offWorkHours+= $rangeoffWork;
                    }
                    else if(($item->TimeIn != null&&$item->TimeOut != null&&Carbon::parse($TimeInPM)>=Carbon::parse($item->TimeOut)&&$item->holiday!=1)) {
                        $rangeoffWork = 0.5;
                        $this->data['timekeepings']->offWorkTimes+= 1;
                        $this->data['timekeepings']->offWorkHours+= $rangeoffWork;
                    }
                    else if(($item->TimeIn != null&&$item->TimeOut != null&&Carbon::parse($TimeOutAM)<=Carbon::parse($item->TimeIn)&&$item->holiday!=1)) {
                        $rangeoffWork =0.5;
                        $this->data['timekeepings']->offWorkTimes+= 1;
                        $this->data['timekeepings']->offWorkHours+= $rangeoffWork;
                    }
                    else{
                        $rangeoffWork=0;
                    }
                }
                $item->offWork= $rangeoffWork;
            }
            array_push($this->data['data'],$this->data['timekeepings']);
            array_push($this->data['datauser'],$this->data['userSelect']);
        }
        $date = Carbon::parse($this->year."-".$this->month."-1")->daysInMonth;
        for ($i=1; $i <= $date; $i++) { 
            array_push($this->data['datadate'],$this->year."-".$this->month."-".$i);
            array_push($this->data['datadate'],self::WEEK_MAP[Carbon::parse($this->year."-".$this->month."-".$i)->dayOfWeek]);
        }
        $intLoopTmp = count($this->data['datadate'])/2+5;
        $colum =self::ARRAY_DATE_COLUM;
        $this->data['columexcel'] = $colum[(count($this->data['datadate'])/2)-1];
        $this->data['dateexcel'] = $intLoopTmp;
        $this->data['note'] = ["Nghỉ lễ","Làm bù","Cuối tuần","Chấm công thiếu","Công tác"];
        return view('admin.layouts.'.config('settings.template').'.timekeeping-absence-export', $this->data);
    }
    public function title(): string
    {
        return "T".$this->month;
    }
    public function registerEvents(): array
    {
        $columColor = ["ffff00","ccc0da","a6a6a6","ef8686","a2e831"];
        $numberUser=-1;
        $numberUsers=-1;
        $arrbusinesscolum = [];
        $arrshortcolum = [];
        $colum =self::ARRAY_DATE_COLUM;
        $date = Carbon::parse($this->year."-".$this->month."-1")->daysInMonth;
        foreach($this->user as $record){
            $numberUsers++;
            $arrbusiness = [];
            $short = [];
            for ($j=1; $j <= $date; $j++) {
                $business = Absence::query()
                    ->select('absences.SDate','absences.EDate','absences.UID')
                    ->join('master_data', 'master_data.DataValue', 'absences.MasterDataValue')
                    ->where('UID', isset($record->UserID) ? $record->UserID : '')
                    ->where('SDate', '<=' , Carbon::parse($this->year."-".$this->month."-".$j)->endOfDay())
                    ->where('EDate', '>=', Carbon::parse($this->year."-".$this->month."-".$j)->startOfDay())
                    ->where('MasterDataValue', "VM006")
                    ->get();
                if(count($business)>0){
                    array_push($arrbusiness,$j);
                }
                $timekeepings = Timekeeping::query()
                ->where('Date', $this->year."-".$this->month."-".$j)
                ->where('UserID', $record->UserID)
                ->orderBy('Date', 'asc')
                ->get();
                $dayOfTheWeek= self::WEEK_MAP[Carbon::parse($this->year."-".$this->month."-".$j)->dayOfWeek];
                $compensate = CalendarEvent::query()
                    ->select('StartDate','EndDate','Content','Type')
                    ->where('StartDate', '<=' , Carbon::parse($this->year."-".$this->month."-".$j)->endOfDay())
                    ->where('EndDate', '>=', Carbon::parse($this->year."-".$this->month."-".$j)->startOfDay())
                    ->where('CalendarID',1)
                    ->where('Type',0)
                    ->where('Content','not like','%Du lịch%')
                    ->get();
                foreach ($timekeepings as $row) {
                    if((($dayOfTheWeek != "T7"&&$dayOfTheWeek != "CN")||count($compensate)>0)&&($row->TimeIn!=null&&$row->TimeOut==null||$row->TimeIn==null&&$row->TimeOut!=null)) array_push($short,$j);
                }
            }
            $intTotalRowUserE= 3+ $numberUsers*4+1;
            foreach ($arrbusiness as $value) {
                array_push($arrbusinesscolum,$colum[$value-1].$intTotalRowUserE);
                array_push($arrbusinesscolum,$colum[$value-1].($intTotalRowUserE+1));
                array_push($arrbusinesscolum,$colum[$value-1].($intTotalRowUserE+2));
                array_push($arrbusinesscolum,$colum[$value-1].($intTotalRowUserE+3));
            }
            foreach ($short as $value) {
                array_push($arrshortcolum,$colum[$value-1].$intTotalRowUserE);
                array_push($arrshortcolum,$colum[$value-1].($intTotalRowUserE+1));
                array_push($arrshortcolum,$colum[$value-1].($intTotalRowUserE+2));
                array_push($arrshortcolum,$colum[$value-1].($intTotalRowUserE+3));
            }
        }
        foreach($this->user as $record){
            $numberUser++;
            $this->data['timekeepings'] = Timekeeping::query()
                ->whereMonth('Date', $this->month)
                ->whereYear('Date', $this->year)
                ->where('UserID', $record->UserID)
                ->orderBy('Date', 'asc')
                ->get();
            $this->data['userSelect'] = User::find($record->UserID);
            $this->data['timekeepings']->totalKeeping = 0;
            $this->data['timekeepings']->overKeeping = 0;
            $this->data['timekeepings']->lateTimes = 0;
            $this->data['timekeepings']->lateHours = 0;
            $this->data['timekeepings']->soonTimes = 0;
            $this->data['timekeepings']->soonHours = 0;
            $arrT7Row = [];
            $arrholiday = [];
            $arrcompensate = [];
            $arrtravel = [];
            for ($i=1; $i <= $date; $i++) {
                $calendarEvent = CalendarEvent::query()
                    ->select('StartDate','EndDate','Content','Type')
                    ->where('StartDate', '<=' , Carbon::parse($this->year."-".$this->month."-".$i)->endOfDay())
                    ->where('EndDate', '>=', Carbon::parse($this->year."-".$this->month."-".$i)->startOfDay())
                    ->where('CalendarID',1)
                    ->where('Content','not like','%Du lịch%')
                    ->get();
                $dayOfTheWeek= self::WEEK_MAP[Carbon::parse($this->year."-".$this->month."-".$i)->dayOfWeek];
                if($dayOfTheWeek == "T7"||$dayOfTheWeek == "CN") array_push($arrT7Row,$i);
                foreach($calendarEvent as $calendar){
                    if($calendar->Type == 1)  array_push($arrholiday,$i);
                    elseif($calendar->Type == 0)  array_push($arrcompensate,$i);
                }
            }
            $intLoopTmp = count($this->user);
            $intTotalRow = 3 + $intLoopTmp*4;
            $dateexcel = $date;
            $num =0;
        	return [
                AfterSheet::class => function(AfterSheet $event) use ($num,$columColor,$arrshortcolum,$arrbusinesscolum,$arrcompensate,$arrholiday,$arrT7Row,$intTotalRow,$dateexcel,$colum){
                    $event->sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                    $event->sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1,3);
                    // $event->sheet->setPageMargin(array(0.1, 0, 0.1, 0.5));
                    $number=0;
                    $event->sheet->getStyle('B4:B'.$intTotalRow)->getAlignment()->setWrapText(true);
                    foreach ($columColor as $value) {
                        $event->sheet->getStyle('B'.($intTotalRow+4+$number).':B'.($intTotalRow+4+$number))->getAlignment()->setWrapText(true);
                        $number++;
                    }

                    foreach ($arrT7Row as $value) {
                        $event->sheet->styleCells(
                            $colum[$value-1].'4:'.$colum[$value-1].$intTotalRow,
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
                                    'color' => ['rgb' => 'a6a6a6']
                                ]
                            ]
                        );
                    }
                    foreach ($arrcompensate as $value) {
                        $event->sheet->styleCells(
                            $colum[$value-1].'4:'.$colum[$value-1].$intTotalRow,
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
                                    'color' => ['rgb' => 'ccc0da']
                                ]
                            ]
                        );
                    }
                    foreach ($arrholiday as $value) {
                        $event->sheet->styleCells(
                            $colum[$value-1].'4:'.$colum[$value-1].$intTotalRow,
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
                                    'color' => ['rgb' => 'ffff00']
                                ]
                            ]
                        );
                    }
                    foreach ($arrbusinesscolum as $value){
                        $event->sheet->styleCells(
                            $value,
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
                                    'color' => ['rgb' => 'a2e831']
                                ]
                            ]
                        );
                    }
                    foreach ($arrshortcolum as $value){
                        $event->sheet->styleCells(
                            $value,
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
                                    'color' => ['rgb' => 'ef8686']
                                ]
                            ]
                        );
                    }
                    foreach ($columColor as $value) {
                        $event->sheet->styleCells(
                            'A'.($intTotalRow+4+$num).':A'.($intTotalRow+4+$num),
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
                                    'color' => ['rgb' => $value]
                                ]
                            ]
                        );
                        $event->sheet->styleCells(
                            'B'.($intTotalRow+4+$num).':B'.($intTotalRow+4+$num),
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
                            ]
                        );
                        $num++;
                    }
                    $event->sheet->styleCells(
                            'A1:'.$colum[$dateexcel-1].$intTotalRow,
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
                            ]
                        );
                        $event->sheet->styleCells(
                            'A2:'.$colum[$dateexcel-1].'3',
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
                                    'color' => ['rgb' => 'fcd5b4']
                                ]
                            ]
                        );
                        $event->sheet->styleCells(
                            'D4:D'.$intTotalRow,
                            [
                                'font' => [
                                    'color' => ['rgb' => '000000'],
                                    'bold'  => true,
                                    'italic' => true,
                                ],
                                'alignment' => [
                                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                                ],
                            ]
                        );
                        $event->sheet->styleCells(
                            'E4:E'.$intTotalRow,
                            [
                                'font' => [
                                    'color' => ['rgb' => '000000'],
                                    'bold'  => true,
                                    'italic' => true,
                                ],
                                'alignment' => [
                                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                                ],
                            ]
                        );
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
                            ]
                        ]
                    );
                    $event->sheet->getDelegate()->freezePane('A4');
                    $event->sheet->getPageSetup()->setFitToWidth(1);
                    $event->sheet->getPageSetup()->setFitToHeight(0);
                },
            ];
        }
    }
}
