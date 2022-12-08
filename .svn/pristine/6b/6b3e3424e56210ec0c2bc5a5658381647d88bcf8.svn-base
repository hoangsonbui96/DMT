<?php
namespace App\Exports\Sheets;

use App\Model\Absence;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Controller;
use App\Timekeeping;
use App\MasterData;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
    $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});
// define('TimeInAM', '08:00');
// define('TimeOutAM', '12:00');
// define('TimeInPM', '13:30');
// define('TimeOutPM', '17:00');
class TimekeepingSheet extends AdminController implements FromView, WithTitle, WithColumnFormatting, WithEvents
{
    private $record;
    private $time;
    public function __construct($record, $time)
    {
        $this->record = $record;
        $this->time = $time;
    }

    /**
    * @return Builder
    */
//    public function array(): array
//    {
//        $sheet = Timekeeping::query()
//            ->where('UserID', $this->record->UserID)
//            ->where('Date', 'like', '%'.$this->time.'%')
//            ->select('Date', 'TimeIn', 'TimeOut')
//            ->get()->toArray();
//        $array = [
//            ['Bảng chi tiết chấm công'],
//            ['Mã nhân viên: '.$this->record->IDFM, null, null, null, 'Tên nhân viên '.$this->record->FullName]
//            ];
//        $sheet = array_merge($array,$sheet);
//
//        return $sheet;
//    }

    public function view(): View\View
    {
        //chuan bi du lieu
        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $this->data['checkUser'] = User::find(Auth::user()->id);
        $Master1 = MasterData::where('DataValue','WT001')->first();
        $Master2 = MasterData::where('DataValue','WT002')->first();
        $TimeOutAM = $Master2->Name;
        $TimeInPM = $Master2->DataDescription;
        $userid = User::find($this->record->UserID);

        $totalTimeWork = (Carbon::parse($Master1->Name)->diffInMinutes(Carbon::parse($TimeOutAM))
            + Carbon::parse($TimeInPM)->diffInMinutes(Carbon::parse($Master1->DataDescription)))/60;

        // $TimeInAM = $userid->STimeOfDay;
        // if(isset($userid->ETimeOfDay)){
        //     $TimeOutPM = $userid->ETimeOfDay;
        // }else{
        //     $TimeOutPM = date('H:i:s',strtotime('+'.($totalTimeWork+(Carbon::parse($TimeOutAM)->diffInMinutes(Carbon::parse($TimeInPM)))/60).'hour',strtotime($userid->STimeOfDay)));
        // }

        $time =  explode('-', $this->time);
        $this->data['timekeepings'] = Timekeeping::query()
            ->whereMonth('Date', $time[1])
            ->whereYear('Date', $time[0])
            ->where('UserID', $this->record->UserID )
            ->orderBy('Date', 'asc')
            ->get();

        if(count($this->data['timekeepings']) >0 ){
            $TimeInAM = $this->data['timekeepings']->first()->STimeOfDay;
            if(isset($userid->ETimeOfDay)){
                $TimeOutPM = $this->data['timekeepings']->first()->ETimeOfDay;
            }else{
                $TimeOutPM = date('H:i:s',strtotime('+'.($totalTimeWork+(Carbon::parse($TimeOutAM)->diffInMinutes(Carbon::parse($TimeInPM)))/60).'hour',strtotime($this->data['timekeepings']->first()->STimeOfDay)));
            }
        }
        else{
            $TimeInAM = $userid->STimeOfDay;
            if(isset($userid->ETimeOfDay)){
                $TimeOutPM = $userid->ETimeOfDay;
            }else{
                $TimeOutPM = date('H:i:s',strtotime('+'.($totalTimeWork+(Carbon::parse($TimeOutAM)->diffInMinutes(Carbon::parse($TimeInPM)))/60).'hour',strtotime($userid->STimeOfDay)));
            }
        }

        $this->data['userSelect'] = User::find($this->record->UserID);
        $this->data['timekeepings']->totalKeeping = 0;
        $this->data['timekeepings']->overKeeping = 0;
        $this->data['timekeepings']->lateTimes = 0;
        $this->data['timekeepings']->lateHours = 0;
        $this->data['timekeepings']->soonTimes = 0;
        $this->data['timekeepings']->soonHours = 0;

        foreach($this->data['timekeepings'] as $item){
            $dayOfTheWeek = Carbon::parse($item->Date)->dayOfWeek;
            $item->weekday = self::WEEK_MAP[$dayOfTheWeek];
            if($item->TimeIn != Null && Carbon::parse($item->TimeIn) > Carbon::parse($TimeInAM)){
                $item->late = Carbon::parse($item->TimeIn)->diffInMinutes(Carbon::parse($TimeInAM));
                $this->data['timekeepings']->lateTimes += 1;
                $this->data['timekeepings']->lateHours += $item->late/60;
            }

            else $item->late = 0;

            if($item->TimeOut != Null && Carbon::parse($item->TimeOut) < Carbon::parse($TimeOutPM)){
                $item->soon = Carbon::parse($item->TimeOut)->diffInMinutes(Carbon::parse($TimeOutPM));
                $this->data['timekeepings']->soonTimes += 1;
                $this->data['timekeepings']->soonHours += $item->soon/60;
            }

            else {
                $item->soon = 0;

            }
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

            //vang mat
            $item->absence = Absence::query()
                ->select('absences.*', 'master_data.Name')
                ->join('master_data', 'master_data.DataValue', 'absences.MasterDataValue')
                ->where('UID', isset($this->record->UserID) ? $this->record->UserID : '')
                ->where('SDate', '<=' , Carbon::parse($item->Date)->endOfDay())
                ->where('EDate', '>=', Carbon::parse($item->Date)->startOfDay())
                ->get();

            foreach($item->absence as $absence) {
                if(Carbon::parse($absence->SDate)->day ==Carbon::parse($item->Date)->day){
                    $absence->STime = Carbon::parse($absence->SDate)->format('H:i');
                    if($absence->STime < $TimeInAM){
                        $absence->STime = $TimeInAM;
                    }
                }else{
                    $absence->STime = $TimeInAM;
                }
                if(Carbon::parse($absence->EDate)->day == Carbon::parse($item->Date)->day){
                    $absence->ETime = Carbon::parse($absence->EDate)->format('H:i');
                }else{
                    $absence->ETime = $TimeOutPM;
                }
            }
        }
        return view('admin.layouts.'.config('settings.template').'.timekeeping-export', $this->data);
    }
    /**
    * @return string
    */
    public function title(): string
    {
        return $this->record->FullName;
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public function registerEvents(): array
    {
        //chuan bi du lieu
        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);


        $time =  explode('-', $this->time);
        $this->data['timekeepings'] = Timekeeping::query()
            ->whereMonth('Date', $time[1])
            ->whereYear('Date', $time[0])
            ->where('UserID', $this->record->UserID )
            ->orderBy('Date', 'asc')
            ->get();
        $this->data['userSelect'] = User::find($this->record->UserID);
        $this->data['timekeepings']->totalKeeping = 0;
        $this->data['timekeepings']->overKeeping = 0;
        $this->data['timekeepings']->lateTimes = 0;
        $this->data['timekeepings']->lateHours = 0;
        $this->data['timekeepings']->soonTimes = 0;
        $this->data['timekeepings']->soonHours = 0;
        $arrT7Row = [];
        $arrCNRow = [];
        $intLoopTmp = 0;
        foreach($this->data['timekeepings'] as $item){
            $dayOfTheWeek = Carbon::parse($item->Date)->dayOfWeek;
            if($dayOfTheWeek == 6){
                $arrT7Row[] = 10 + $intLoopTmp;
            }
            if($dayOfTheWeek == 0){
                $arrCNRow[] = 10 + $intLoopTmp;
            }
            $intLoopTmp++;
        }
        $intTotalRow = 9 + $intLoopTmp;
        return [

            AfterSheet::class    => function(AfterSheet $event) use ($arrT7Row, $arrCNRow, $intTotalRow){
                $event->sheet->styleCells(
                    'A1:K'.$intTotalRow,
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
                $event->sheet->getDelegate()->getStyle('A1:K'.$intTotalRow)
                ->getAlignment()->setWrapText(true);
                $event->sheet->styleCells(
                    'E10:J'.$intTotalRow,
                    [

                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                        ],
                    ]
                );
                $event->sheet->styleCells(
                    'A1',
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'bold'  => true,
                            'size'  => 16,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => 'ffff00']
                        ]

                    ]
                );
                $event->sheet->styleCells(
                    'A2:K9',
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
                foreach($arrT7Row as $item){
                    $event->sheet->styleCells(
                        'A'.$item.':K'.$item,
                        [
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => ['rgb' => 'CCCCFF']
                            ]


                        ]
                    );
                    $event->sheet->getDelegate()->getStyle('K'.$item)
                ->getAlignment()->setWrapText(true);
                }

                foreach($arrCNRow as $item){
                    $event->sheet->styleCells(
                        'A'.$item.':K'.$item,
                        [
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => ['rgb' => 'FF99CC']
                            ]


                        ]
                    );
                $event->sheet->getDelegate()->getStyle('K'.$item)
                ->getAlignment()->setWrapText(true);
                }

                $event->sheet->getDelegate()->freezePane('A4');
                $event->sheet->getPageSetup()->setFitToWidth(1);
                $event->sheet->getPageSetup()->setFitToHeight(0);
            },
        ];
    }
}
