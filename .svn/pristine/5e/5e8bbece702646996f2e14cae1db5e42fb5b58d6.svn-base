<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\ProjectManager\Entities\Project;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProjectsExport implements FromView, ShouldAutoSize, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    private $record, $totalCol, $totalRow;
    public function __construct(
        $record,
        $totalCol,
        $totalRow,
        $projectService
    ) {
        $this->record = $record;
        $this->totalCol = $totalCol;
        $this->totalRow = $totalRow;
        $this->projectService = $projectService;
    }

    public function collection()
    {
        return Project::all();
    }

    public function view(): View
    {
        return view(
            'projectmanager::includes.projects-export',
            [
                'projects' => $this->record
            ]
        );
    }

    public function registerEvents(): array
    {
        $totalRow = $this->totalRow + 2;
        $totalCol = $this->totalCol + 2;
        $cellColStart = Coordinate::stringFromColumnIndex(1);
        $cellColEnd = Coordinate::stringFromColumnIndex($totalCol);
        return [
            AfterSheet::class => function (AfterSheet  $event) use ($totalCol, $totalRow, $cellColStart, $cellColEnd) {
                $event->sheet->styleCells(
                    //Header
                    'A1:' . $cellColEnd . '1',
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
                    'A2:' . $cellColEnd . '2',
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
                    'A2:' . $cellColEnd . $totalRow,
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
                    'A3:' . 'A' . $totalRow,
                    [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ]
                    ]
                );
                //B3 to C end
                $event->sheet->styleCells(
                    'B3:' . 'C' . $totalRow,
                    [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ]
                    ]
                );

                //D3 to E end
                $event->sheet->styleCells(
                    'D3:' . 'E' . $totalRow,
                    [
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ]
                    ]
                );
                //F3 to end
                $event->sheet->styleCells(
                    'F3:' . $cellColEnd . $totalRow,
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
                while ($i <= $totalRow) {
                    $event->sheet->getRowDimension($i)->setRowHeight(40);
                    $i++;
                }
                $event->sheet->getStyle('D3:E' . $totalRow)->getAlignment()->setWrapText(true);
            }
        ];
    }
}
