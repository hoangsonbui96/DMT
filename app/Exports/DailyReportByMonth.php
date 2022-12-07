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
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithTitle;

class DailyReportByMonth extends AdminController implements FromView, ShouldAutoSize, WithEvents,WithTitle
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
    	$this->data['dailyReports'] = $this->record['dailyReports'];
    	$this->data['user'] = $this->record['user'];
        $this->data['intLoopTmp'] =2+count($this->record['dailyReports']);
    	return $this->viewAdminLayout('daily-report-by-month', $this->data);
    }
    public function title(): string
    {
        return "Báo cáo hàng ngày";
    }
    public function registerEvents(): array
    {
    	$intLoopTmp =3+count($this->record['dailyReports']);
    	return [
            AfterSheet::class => function(AfterSheet $event) use ($intLoopTmp){
            	$event->sheet->styleCells(
                    'A1:J'.$intLoopTmp,
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
                                'size'  => 24,
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            ]

                        ]
                );
                $event->sheet->styleCells(
                    'A2:J2',
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'bold'  => true,
                            'italic' => true,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]
                );
                $event->sheet->styleCells(
                    'A'.$intLoopTmp.':J'.$intLoopTmp,
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'bold'  => true,
                            'italic' => true,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]
                );
            }
        ];
    }
}