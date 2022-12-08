<?php

namespace App\Exports;

use App\Http\Controllers\Admin\AdminController;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View;
use Maatwebsite\Excel\Concerns\WithEvents;

class EquipmentOfferExport extends AdminController implements FromView, ShouldAutoSize, WithEvents
{
    use Exportable;
    protected $record;
    protected $record_detail;

    function __construct($record, $record_detail) {
        $this->record = $record;
        $this->record_detail = $record_detail;
    }

    public function view(): View\View
    {
        $this->data['equipment_offer'] = $this->record;
        $this->data['equipment_offer_detail'] = $this->record_detail;
        return $this->viewAdminLayout('equipment.equipment-offer-export', $this->data);
    }

    public function registerEvents(): array
    {
        $countRow = 0;
        foreach($this->record_detail as $item){
            $countRow++;
        }
        $intTotalRow = 10 + $countRow;

        return [

            AfterSheet::class => function(AfterSheet $event) use ($intTotalRow){
                $event->sheet->getDelegate()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                for ($i = 1; $i <= ($intTotalRow + 8); $i++) {
                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(25);
                };
//                $event->sheet->styleCells(
//                    'A3:I4',
//                    [
//                        'font' => [
//                            'color' => ['rgb' => '000000'],
//                            'size'  => 12,
//                            'name' => 'Times News Roman',
//                            'style' => 'italic',
//                        ],
//                        'alignment' => [
//                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
//                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
//                        ],
//                        'borders' => [
//                            'allBorders' => [
//                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
//                            ],
//                        ],
//                    ]
//                );
//                $event->sheet->styleCells(
//                    'A7:I'.$intTotalRow,
//                    [
//                        'font' => [
//                            'color' => ['rgb' => '000000'],
//                            'size'  => 12,
//                            'name' => 'Times News Roman',
//                            'style' => 'italic'
//                        ],
//                        'alignment' => [
//                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
//                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
//                        ],
//                        'borders' => [
//                            'allBorders' => [
//                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
//                            ],
//                        ],
//                    ]
//                );
//                $event->sheet->styleCells(
//                    'B7:B'.$intTotalRow,
//                    [
//                        'font' => [
//                            'color' => ['rgb' => '000000'],
//                            'size'  => 12,
//                            'name' => 'Times News Roman',
//                            'style' => 'italic'
//                        ],
//                        'alignment' => [
//                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
//                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
//                        ],
//                        'borders' => [
//                            'allBorders' => [
//                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
//                            ],
//                        ],
//                    ]
//                );
//                $event->sheet->styleCells(
//                    'G7:G'.$intTotalRow,
//                    [
//                        'alignment' => [
//                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
//                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
//                            'wrapText' => true,
//                        ],
//                    ]
//                );
//                $event->sheet->styleCells(
//                    'D7:F'.$intTotalRow,
//                    [
//                        'font' => [
//                            'color' => ['rgb' => '000000'],
//                            'size'  => 12,
//                            'name' => 'Times News Roman',
//                            'style' => 'italic'
//                        ],
//                        'alignment' => [
//                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
//                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
//                        ],
//                        'borders' => [
//                            'allBorders' => [
//                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
//                            ],
//                        ],
//                    ]
//                );
//                $event->sheet->styleCells(
//                    'A6:I6',
//                    [
//                        'font' => [
//                            'color' => ['rgb' => '000000'],
//                            'size'  => 12,
//                            'name' => 'Times News Roman',
//                            'style' => 'italic',
//                            'bold'  => true,
//                        ],
//                        'alignment' => [
//                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
//                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
//                        ],
//                        'borders' => [
//                            'allBorders' => [
//                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
//                            ],
//                        ],
//                    ]
//                );
                $event->sheet->styleCells(
                    'A1:J3',
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'bold'  => true,
                            'name' => 'Times News Roman',
                            'size'  => 12,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                        ],
//                        'fill' => [
//                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
//                        ]

                    ]
                );
                $event->sheet->styleCells(
                    'A9:J10',
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'name' => 'Times News Roman',
                            'size'  => 12,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                        ],
//                        'fill' => [
//                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
//                        ]
                    ]
                );

                $event->sheet->styleCells(
                    'A4:J4',
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'bold'  => true,
                            'name' => 'Times News Roman',
                            'size'  => 12,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                        ],
//                        'fill' => [
//                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
//                        ]
                    ]
                );

                $event->sheet->styleCells(
                    'A5:J8',
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'name' => 'Times News Roman',
                            'size'  => 12,
                        ],
//                        'fill' => [
//                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
//                        ]
                    ]
                );
                $event->sheet->styleCells(
                    'A9:j'.($intTotalRow + 1),
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
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                        ],
                    ]
                );
                $event->sheet->styleCells(
                    'A'.($intTotalRow + 2).':j'.($intTotalRow + 3),
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'size'  => 12,
                            'name' => 'Times News Roman',
                            'style' => 'italic'
                        ],
//                        'fill' => [
//                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
//                        ]
                    ]
                );
                $event->sheet->styleCells(
                    'C11:E'.$intTotalRow,
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'size'  => 12,
                            'name' => 'Times News Roman',
                            'style' => 'italic'
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
//                        'fill' => [
//                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
//                        ]
                    ]
                );
                $event->sheet->styleCells(
                    'I11:J'.($intTotalRow + 1),
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'size'  => 12,
                            'name' => 'Times News Roman',
                            'style' => 'italic'
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
//                        'fill' => [
//                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
//                        ]
                    ]
                );
                $event->sheet->styleCells(
                    'A'.($intTotalRow + 4).':j'.($intTotalRow + 8),
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
//                        'fill' => [
//                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
//                        ]
                    ]
                );
//                $event->sheet->getRowDimension('1')->setWidth(35);
//                $event->sheet->getRowDimension('1')->setAutoSize(false);
                $event->sheet->getStyle('A1:J1')->getAlignment()->setWrapText(true);
                $event->sheet->getStyle('A9:J10')->getAlignment()->setWrapText(true);

                $event->sheet->getRowDimension(1)->setRowHeight(35);
                $event->sheet->getRowDimension(2)->setRowHeight(10);
                $event->sheet->getRowDimension(3)->setRowHeight(10);
                $event->sheet->getRowDimension(5)->setRowHeight(10);

                $event->sheet->getColumnDimension('A')->setWidth(10);
                $event->sheet->getColumnDimension('A')->setAutoSize(false);
                
                $event->sheet->getColumnDimension('B')->setWidth(15);
                $event->sheet->getColumnDimension('B')->setAutoSize(false);

                $event->sheet->getColumnDimension('C')->setWidth(15);
                $event->sheet->getColumnDimension('C')->setAutoSize(false);

                $event->sheet->getColumnDimension('D')->setWidth(15);
                $event->sheet->getColumnDimension('D')->setAutoSize(false);

                $event->sheet->getColumnDimension('E')->setWidth(15);
                $event->sheet->getColumnDimension('E')->setAutoSize(false);

                $event->sheet->getColumnDimension('F')->setWidth(10);
                $event->sheet->getColumnDimension('F')->setAutoSize(false);

                $event->sheet->getColumnDimension('G')->setWidth(10);
                $event->sheet->getColumnDimension('G')->setAutoSize(false);

                $event->sheet->getColumnDimension('H')->setWidth(10);
                $event->sheet->getColumnDimension('H')->setAutoSize(false);

                $event->sheet->getColumnDimension('I')->setWidth(10);
                $event->sheet->getColumnDimension('I')->setAutoSize(false);

                $event->sheet->getColumnDimension('J')->setWidth(10);
                $event->sheet->getColumnDimension('J')->setAutoSize(false);
                $event->sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A5);
            },
        ];
    }

}
