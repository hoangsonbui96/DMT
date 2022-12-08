<?php

namespace App\Exports;

use App\Http\Controllers\Admin\AdminController;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class AbsencesReportExport extends AdminController implements FromView, ShouldAutoSize, WithEvents
{
    use Exportable;
    protected $records;
    function __construct($records)
    {
        $this->records = $records;
    }

    public function view(): View\View
    {
        $this->data = $this->records;
        $countCol = 0;
        foreach ($this->data['master_datas'] as $item) {
            $countCol++;
        }
        foreach ($this->data['absence_report'] as $key => $item) {
            $sumTotalHour[$key] = 0;
            foreach ($item->hours as $j => $value ) {
                if($j == 5 || $j == 7){
                    continue;
                }
                if (isset($totalHour[$j]) && isset($totalTime[$j])) {
                    $totalHour[$j] += number_format($value / 60, 2);
                    $totalTime[$j] += number_format($item->times[$j]);
                } else {
                    $totalHour[$j] = number_format($value / 60, 2);
                    $totalTime[$j] = number_format($item->times[$j]);
                }
                $sumTotalHour[$key] += (number_format($value / 60, 2));
            }
            
        }
        $this->data['totalHour'] = $totalHour;
        $this->data['sumTotalHour'] = $sumTotalHour;
        $this->data['totalTime'] = $totalTime;
        $countCol = $countCol * 2;
        $this->data['countCol'] = 4 + $countCol;
        return $this->viewAdminLayout('absence.absences-report-export', $this->data);
    }

    public function registerEvents(): array
    {
        $record = $this->records;

        $countRow = 1;
        $countCol = 0;
        $totalHour = [];

        foreach ($record['absence_report'] as $key => $item) {
            $countRow++;
            foreach ($item->hours as $j => $value) {
                if (isset($totalHour[$j])) {
                    $totalHour[$j] += $value;
                } else {
                    $totalHour[$j] = 0;
                }
            }
        }

        foreach ($record['master_datas'] as $item) {
            $countCol++;
        }
        $countCol = $countCol * 2;
        $intTotalRow = 7 + $countRow;
        $intTotalCol = 4 + $countCol;

        $cellColStart = Coordinate::stringFromColumnIndex(1);
        $cellColEnd = Coordinate::stringFromColumnIndex($intTotalCol);
        return [

            AfterSheet::class => function (AfterSheet $event) use ($intTotalRow, $cellColStart, $cellColEnd) {
                foreach(range('C','T') as $columnID) {
                    $event->sheet->getColumnDimension($columnID)
                        ->setWidth(10);
                }
                $event->sheet->styleCells(
                    $cellColStart . '7' . ':' . $cellColEnd . $intTotalRow,
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'size'  => 10,
                            'name' => 'Times News Roman',
                            'style' => 'italic'
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                        ],
                    ]
                );
                $event->sheet->styleCells(
                    'A2:' . $cellColEnd . $intTotalRow,
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'size'  => 12,
                            'name' => 'Times News Roman',
                            'style' => 'italic'
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                    ]
                );
                $event->sheet->styleCells(
                    'A2',
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'bold'  => true,
                            'name' => 'Times News Roman',
                            'size'  => 26,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => 'ffffff']
                        ]

                    ]
                );
                $event->sheet->styleCells(
                    'B9:B'.$intTotalRow,
                    [
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                    ]
                );
                $event->sheet->styleCells(
                    'C7:D'.$intTotalRow,
                    [
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => 'FDEADA']
                        ]
                    ]
                );
                $event->sheet->styleCells(
                    'E7:'.$cellColEnd.'8',
                    [
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => 'FFFF00']
                        ]
                    ]
                );
            },
        ];
    }
}
