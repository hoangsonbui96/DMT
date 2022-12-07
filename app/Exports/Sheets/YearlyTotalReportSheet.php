<?php


namespace App\Exports\Sheets;
use App\Http\Controllers\Admin\AdminController;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
    $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class YearlyTotalReportSheet extends AdminController implements FromView, WithTitle, WithEvents
{
    private $summary;
    private $year;
    private $statistic_year;
    public function __construct($summary,$year, $statistic_year)
    {
        $this->summary = $summary;
        $this->year = $year;
        $this->statistic_year = $statistic_year;
    }


    public function view(): View\View
    {
        $this->data['projects'] = $this->summary;
        $this->data['statistic_year'] = $this->statistic_year;
        $this->data['view'] = 2;
        $this->data['year'] = $this->year;
        return view('admin.layouts.'.config('settings.template').'.yearly-reports-excel', $this->data);
    }
    /**
     * @return string
     */
    public function title(): string
    {
        return 'Summary';
    }


    public function registerEvents(): array
    {
        //count row of one sheet
        $countList = 0;

        foreach ($this->summary as $item){
            $countList++;
        }
        $intTotalRow = 3 + $countList;
        return [

            AfterSheet::class    => function(AfterSheet $event) use ($intTotalRow){

                $event->sheet->styleCells(
                    'A1:P'.$intTotalRow,
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
                    'A3:P3',
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
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => '78bfeb']
                        ]
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
