<?php


namespace App\Exports\Sheets;


use App\Http\Controllers\Admin\AdminController;
use App\OvertimeWork;
use App\Project;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class listOvertimeSheet extends AdminController implements FromView, WithTitle, WithEvents, ShouldAutoSize
{
    use Exportable;
    protected $list;
    function __construct($list)
    {
		
        $newList = [];
		foreach($list as $item) {
			if($item->time > 0)
			{
				array_push($newList, $item);
			}
		}
		$this->list = $newList;
    }

    public function view(): View
    {
        $this->data['list'] = $this->list;
        $this->data['view'] = 0;

        return $this->viewAdminLayout('overtime-export', $this->data);
    }

    public function title(): string
    {
        return 'Summary';
    }

    public function registerEvents(): array
    {
        $count = 0;
        foreach ($this->list as $item){
            $count++;
        }
        $intTotalRow = 2 + $count;
        $intTotalCol = 0;
        return [
            AfterSheet::class => function(AfterSheet $event) use ($intTotalRow){

                $event->sheet->styleCells(
                    'A2:F'.$intTotalRow,
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
                    'A2:F2',
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
