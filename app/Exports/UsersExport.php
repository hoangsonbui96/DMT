<?php

namespace App\Exports;

use App\Http\Controllers\Admin\AdminController;
use App\Room;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Illuminate\Contracts\View;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithEvents;

class UsersExport extends AdminController implements FromView, ShouldAutoSize, WithEvents
{
    use Exportable;
    protected $records;
    protected $view;
    function __construct($records, $view) {
        $this->records = $records;
        $this->view = $view;
    }

    public function view(): View\View
    {

        foreach ($this->records as $item){

            $item->RoomId = Room::find($item->RoomId) ? Room::find($item->RoomId)->Name : '';
            $item->Gender = $item->Gender ? 'Nam' : "Nữ";
            $item->MaritalStt = $item->MaritalStt ? 'Đã kết hôn' : 'Độc thân';
            $item->Active = $item->Active ? 'Hoạt động' : 'Không hoạt động';
        }
        $this->data['view'] = $this->view;
        $this->data['users'] = $this->records;

        return view('admin.layouts.'.config('settings.template').'.user-export', $this->data);
    }
//    format colume
//    public function columnFormats(): array
//    {
//        return [
//            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
//        ];
//    }

    public function registerEvents(): array
    {
        $countRow = 0;
        foreach($this->records as $item){
            $countRow++;
        }
        $intTotalRow = 2 + $countRow;
        if ($this->view == 'detail'){
            return [

                AfterSheet::class => function(AfterSheet $event) use ($intTotalRow){

                    $event->sheet->styleCells(
                        'A2:I'.$intTotalRow,
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
                        'A2:I2',
                        [
                            'font' => [
                                'color' => ['rgb' => '000000'],
                                'size'  => 16,
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
                                'size'  => 24,
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
        return [

            AfterSheet::class => function(AfterSheet $event) use ($intTotalRow){

                $event->sheet->styleCells(
                    'A2:K'.$intTotalRow,
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
                    'A2:K2',
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'size'  => 16,
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
                            'size'  => 24,
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
