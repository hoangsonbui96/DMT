<?php


namespace App\Exports\Sheets;


use App\Http\Controllers\Admin\AdminController;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
    $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});
class YearlyReportSheet extends AdminController implements FromView, WithTitle, WithColumnFormatting, WithEvents, ShouldAutoSize
{
    private $summary;
    private $month;
    public function __construct($summary,$month)
    {
        $this->summary  = $summary;
        $this->month    = $month;
    }


    public function view(): View\View
    {
        $this->data = $this->summary;
        $colList = 0;
        foreach ($this->summary['masterData'] as $item){
            $colList++;
        }
        $intTotalCol = 4 + $colList;
        $this->data['month'] = $this->month;
        $this->data['view'] = 1;
        $this->data['col'] = $intTotalCol;
        return view('admin.layouts.'.config('settings.template').'.yearly-reports-excel', $this->data);
    }
    /**
     * @return string
     */
    public function title(): string
    {
            return 'T'.$this->month;
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public function registerEvents(): array
    {
        //count row of one sheet
        $countList = 0;
        $colList = 0;
        //đếm dòng cho bảng tổng hợp
        foreach ($this->summary['total'] as $item){
            $countList++;
        }
        //đếm cột cho bảng tổng hợp
        foreach ($this->summary['masterData'] as $item){
            $colList++;
        }
        $intTotalCol = 4 + $colList;

//        $cellColStart   = Coordinate::stringFromColumnIndex(1);
        $cellColEnd     = Coordinate::stringFromColumnIndex($intTotalCol);
        $intTotalRow = 4 + $countList;

        //danh sách báo cáo bắt đầu từ
        $rowOfListStart = $intTotalRow + 2;
        $rowOfList = 0;

        //for lấy danh row của list
        foreach ($this->summary['dailyReports'] as $item){
            $rowOfList++;
        }
        $rowOfListEnd = $rowOfList + $intTotalRow + 2;
        return [

            AfterSheet::class    => function(AfterSheet $event) use ($intTotalRow,$cellColEnd,$rowOfListStart,$rowOfListEnd){

                $event->sheet->styleCells(
                    'A2'.':'.$cellColEnd.$intTotalRow,
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
                    'A'.$rowOfListStart.':'.'J'.$rowOfListEnd,
//                    'A7:'.'J22',
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
                    'A1',
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'bold'  => true,
                            'name' => 'Times News Roman',
                            'size'  => 14,
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
                    'A3:K3',
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'size'  => 12,
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
                            'color' => ['rgb' => '78bfeb']
                        ]
                    ]
                );
            },
        ];
    }
}
