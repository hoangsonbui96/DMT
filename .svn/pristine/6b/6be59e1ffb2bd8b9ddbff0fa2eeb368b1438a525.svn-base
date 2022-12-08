<?php

namespace App\Exports\Sheets;

use Illuminate\Contracts\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Sheet;

class EqExportHistoriesSheet extends AdminController implements FromView, WithTitle, WithEvents
{
	private $row;
	private $userName;
    public function __construct($row,$userName){
        $this->row = $row;
        $this->user = $userName;
    }
    public function view(): View\View
    {
    	$this->data['data']=$this->row;
    	$intTotalRow = 0;
    	foreach ($this->row as $key => $value) {
    		$intTotalRow = $key;
    	}
    	$this->data['intTotalRow']=$intTotalRow+5;
        return $this->viewAdminLayout('equipment-histories-export', $this->data);
    }
    public function title(): string
    {
    	return $this->user;
    }
    public function registerEvents(): array
    {	
    	$intTotalRow = 0;
    	foreach ($this->row as $key => $value) {
    		$intTotalRow = $key;
    	}
    	$intTotalRow=$intTotalRow+6;
        $intTotalRowKy=$intTotalRow+4;
    	return [
    		AfterSheet::class => function(AfterSheet $event) use ($intTotalRow,$intTotalRowKy){
    			$event->sheet->styleCells(
                'A1:J'.$intTotalRow,
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
                    'A1',
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'bold'  => true,
                            'name' => 'Times News Roman',
                            'size'  => 20,
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
                    'A'.$intTotalRowKy.':J'.$intTotalRowKy,
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'name' => 'Times News Roman',
                            'size'  => 13,
                            'bold'  => true,
                        ]
                    ]
                );
                $event->sheet->styleCells(
                    'A4:J4',
                    [
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'bold'  => true,
                            'italic' => true,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        ]

                    ]
                );
                $event->sheet->getDelegate()->getStyle('A4:J'.$intTotalRow)
                ->getAlignment()->setWrapText(true);
    		}
    	];
    }
}