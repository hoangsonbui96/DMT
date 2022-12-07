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

class WorkingScheduleExport extends AdminController implements FromView, ShouldAutoSize, WithEvents
{
    use Exportable;
    protected $records;

    function __construct($records) {
        $this->records = $records;
    }

    public function view(): View\View
    {
        $this->data['working_schedule'] = $this->records;
        $this->data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        return $this->viewAdminLayout('work.working-schedule-export', $this->data);
    }

    public function registerEvents(): array
    {
        //chuan bi du lieu

        $countRow = 0;
        foreach($this->records as $item){
            $countRow++;
        }
        $intTotalRow = 2 + $countRow;

        return [

            AfterSheet::class => function(AfterSheet $event) use ($intTotalRow){

                $event->sheet->styleCells(
                    'A2:G'.$intTotalRow,
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
                    ]
                );
                $event->sheet->styleCells(
                    'A2:G2',
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'size'  => 14,
                            'name' => 'Times News Roman',
                            'style' => 'italic'
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
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
            },
        ];
    }

}
