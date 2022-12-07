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

class DailyReportExport extends AdminController implements FromView, ShouldAutoSize, WithEvents
{

    use Exportable;
    protected $record;
    function __construct($record) {
        $this->record = $record;
    }

    /**
     * Get data and return template view
     * @return View\View
     */
    public function view(): View\View
    {
        $this->data = $this->record;
        $countCol = 0;
        foreach($this->data['projects'] as $item){
            $countCol++;
        }
        $this->data['intTotalCol'] = 3 + $countCol;
        return $this->viewAdminLayout('daily-report-export', $this->data);
    }

    public function registerEvents(): array
    {
        $record = $this->record;

        $countCol = 0;
        foreach($record['projects'] as $item){
            $countCol++;
        }
        $intTotalCol = 3 + $countCol;
        $countRow = 0;
        foreach($record['userList'] as $item){
            $countRow++;
        }
        $intTotalRow = 3 + $countRow;

        //col Index A = 1
        $cellColStart = Coordinate::stringFromColumnIndex(1);
        $cellColEnd = Coordinate::stringFromColumnIndex($intTotalCol);
        return [
            AfterSheet::class => function(AfterSheet $event) use ($cellColStart, $cellColEnd, $intTotalRow){
                $event->sheet->styleCells(
                    $cellColStart.'2'.':'.$cellColEnd.$intTotalRow,
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
                    $cellColStart.'2'.':'.$cellColEnd.$intTotalRow,
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'size'  => 10,
                            'bold'  => true,
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
                    $cellColStart.'2'.':'.$cellColEnd.'2',
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'size'  => 12,
                            'bold'  => true,
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
                    $cellColStart.'1',
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'bold'  => true,
                            'name' => 'Times News Roman',
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
            },
        ];
    }


}
