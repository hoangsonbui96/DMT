<?php

namespace App\Exports;
use App\Http\Controllers\Admin\AdminController;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class WorkingTaskExport extends AdminController implements FromView, ShouldAutoSize, WithEvents
{
    /**
    */
    private $record, $totalCol, $totalRow;

    public function __construct($record, $totalCol, $totalRow)
    {
        $this->record = $record;
        $this->totalCol = $totalCol;
        $this->totalRow = $totalRow;
    }

    private function _toName($str_id){
        $arr_id = explode(",", $str_id);
        array_shift($arr_id);
        array_pop($arr_id);
        $arr_name = User::withTrashed()->select('FullName')->whereIn('id', $arr_id)->pluck('FullName');
        return empty($arr_name) ? '' : $arr_name->implode(',');
    }

    public function view(): View\View
    {
        foreach ($this->record as $i => &$item){
            $item->StartDate = is_null($item->StartDate) ? '' : Carbon::createFromFormat(self::FOMAT_DB_YMD, $item->StartDate)->format(self::FOMAT_DISPLAY_DMY);
            $item->EndDate = is_null($item->EndDate) ? '' : Carbon::createFromFormat(self::FOMAT_DB_YMD, $item->EndDate)->format(self::FOMAT_DISPLAY_DMY);
            $item->Member = $this->_toName($item->Member);
            $item->Leader = $this->_toName($item->Leader);
        }
        return $this->viewAdminLayout('task-work.all-project-export', ['value' => $this->record]);
    }

    public function registerEvents(): array
    {
        $totalRow = $this->totalRow + 2;
        $totalCol = $this->totalCol + 1;
        $cellColStart = Coordinate::stringFromColumnIndex(1);
        $cellColEnd = Coordinate::stringFromColumnIndex($totalCol);
        return [
            AfterSheet::class => function(AfterSheet  $event) use ($totalCol, $totalRow,$cellColStart, $cellColEnd){
                $event->sheet->styleCells(
                    //Header
                    'A1:'. $cellColEnd .'1',
                    [
                        'font' => [
                            'color' => ['rbg' => 'FFFFFF'],
                            'size' => 16,
                            'bold' => true,
                            'name' => 'Times News Roman',
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['rgb' => '287599']
                        ]
                    ]
                );
                //Title
                $event->sheet->styleCells(
                    'A2:'.$cellColEnd.'2',
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'size' => 13,
                            'bold' => true,
                            'name' => 'Times News Roman',
                            'style' => 'italic',
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ],
                    ]
                );
                //All cell
                $event->sheet->styleCells(
                    'A2:'.$cellColEnd.$totalRow,
                    [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                        ],
                        'font'  => [
                            'name' => 'Times News Roman',
                            'style' => 'italic',
                        ]
                    ]
                );
                //Index column
                $event->sheet->styleCells(
                    'A3:'.'A'.$totalRow,
                    [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ]
                    ]
                );
                //B3 to C end
                $event->sheet->styleCells(
                    'B3:'.'C'.$totalRow,
                    [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ]
                    ]
                );

                //D3 to E end
                $event->sheet->styleCells(
                    'D3:'.'E'.$totalRow,
                    [
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ]
                    ]
                );
                //F3 to end
                $event->sheet->styleCells(
                    'F3:'.$cellColEnd.$totalRow,
                    [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ]
                    ]
                );

                $event->sheet->getRowDimension('1')->setRowHeight(35);
                $event->sheet->getRowDimension('2')->setRowHeight(25);

                $event->sheet->getColumnDimension('D')->setWidth(35);
                $event->sheet->getColumnDimension('D')->setAutoSize(false);

                $event->sheet->getColumnDimension('E')->setWidth(40);
                $event->sheet->getColumnDimension('E')->setAutoSize(false);

                $i = 1;
                while ($i<=$totalRow){
                    $event->sheet->getRowDimension($i)->setRowHeight(40);
                    $i++;
                }
                $event->sheet->getStyle('D3:E'.$totalRow)->getAlignment()->setWrapText(true);
            }
        ];
    }
}
