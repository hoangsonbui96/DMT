<?php


namespace App\Http\Controllers\Export\Excel\TimeKeeping;


use App\Http\Controllers\Export\Excel\ExportExcelController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportExcelTimeKeepingSchedulerController extends ExportExcelController
{
    private $_date;
    private $_data;
    private $_title;
    private $_style_sunday;
    private $_style_saturday;
    private $_style_border;
    private $_style_center;

    public function __construct($_date, $_data)
    {
        $this->_date = $_date;
        $this->_data = $_data;
        $this->_setStyle();
        $this->setTitle();
        parent::__construct($_date);
    }

    protected function setFolderName()
    {
        // TODO: Implement setFolderName() method.
        $this->folder_name = "Timekeeping\\";
    }

    protected function setFileName()
    {
        // TODO: Implement setFileName() method.
        $this->file_name = "TheoDoiChamCong.xlsx";
    }

    private function setTitle()
    {
        $date = Carbon::createFromFormat("d_m_Y", $this->_date)->format(FOMAT_DISPLAY_DAY);
        $this->_title = 'BẢNG THEO DÕI CHẤM CÔNG ' . $date;
    }

    protected function setPathFile()
    {
        // TODO: Implement setPathFile() method.
        $this->path_file = $this->folder_name . $this->file_name;
    }

    protected function bindData(&$spread_sheet)
    {
        try {
            $spread_sheet->getDefaultStyle()->getFont()->setName('Times New Roman');
            $sheet = $spread_sheet->getSheet(0);
            $time = Carbon::now()->format("H:i, d/m/Y");
            $sheet->setCellValueByColumnAndRow(1, 1, $this->_title);
            $sheet->setCellValueByColumnAndRow(1, 2, 'Dữ liệu được thống kê vào: ' . $time);

            //Bind data column 3
            $sheet->setCellValueByColumnAndRow(3, 3, round($this->_data->totalKeeping, 2));
            $sheet->setCellValueByColumnAndRow(3, 4, round($this->_data->overKeeping / 60, 2));

            //Bind data column 6
            $sheet->setCellValueByColumnAndRow(6, 3, $this->_data->lateTimes);
            $sheet->setCellValueByColumnAndRow(6, 4, $this->_data->soonTimes);

            //Bind data column 10
            $sheet->setCellValueByColumnAndRow(10, 3, round($this->_data->lateHours / 60, 2));
            $sheet->setCellValueByColumnAndRow(10, 4, $this->_data->soonHours);

            //Bind data column 14
            $sheet->setCellValueByColumnAndRow(14, 3, $this->_data->checkinAtCompany);
            $sheet->setCellValueByColumnAndRow(14, 4, $this->_data->checkinAtHome);

            //Row data
            $start_row = 8;
            $sheet->freezePane("A$start_row");
            foreach ($this->_data as $i => $user) {
                $sheet->setCellValueByColumnAndRow(1, $start_row, $i + 1);
                $sheet->setCellValueByColumnAndRow(2, $start_row, $user->FullName);
                $sheet->mergeCells("B$start_row:C$start_row");
                if (isset($user->timekeepings->first()->id)) {
                    $t = $user->timekeepings->first();
                    $sheet->setCellValueByColumnAndRow(4, $start_row, isset($t->TimeIn)
                        ? $t->TimeIn : null);
                    $sheet->setCellValueByColumnAndRow(5, $start_row, isset($t->TimeOut)
                        ? $t->TimeOut : null);
                    $late = $t->late != "00:00:00"
                        ? Carbon::parse($t->late)->format("H:i:s")
                        : null;
                    $soon = $t->soon != "00:00:00"
                        ? Carbon::parse($t->soon)->format("H:i:s")
                        : null;
                    $sheet->setCellValueByColumnAndRow(6, $start_row, $late);
                    $sheet->setCellValueByColumnAndRow(7, $start_row, $soon);
                    if ($late) {
                        $sheet->getStyle("F$start_row")->applyFromArray($this->_style_sunday);
                    }
                    if ($soon) {
                        $sheet->getStyle("G$start_row")->applyFromArray($this->_style_saturday);
                    }
                    $sheet->setCellValueByColumnAndRow(8, $start_row, round($t->hours, 2, PHP_ROUND_HALF_UP));
                    $sheet->setCellValueByColumnAndRow(9, $start_row, $t->keeping > 1
                        ? 1
                        : number_format($t->keeping, 2));
                    $sheet->setCellValueByColumnAndRow(10, $start_row, $t->N);
                    $sheet->setCellValueByColumnAndRow(11, $start_row, 0);
                    $absence_str = "";
                    foreach ($t->absence as $absence) {
                        $absence_str .= "$absence->Name "
                            . "(" . Carbon::parse($absence->SDate)->format("H:i")
                            . " - " . Carbon::parse($absence->EDate)->format("H:i") . ")";
                    }
                    $sheet->setCellValueByColumnAndRow(12, $start_row, $absence_str);
                    $sheet->setCellValueByColumnAndRow(13, $start_row, ($t->id != null && $t->IsInCpn == 1) ? "X" : null);
                    $sheet->setCellValueByColumnAndRow(14, $start_row, ($t->id != null && $t->IsInCpn == 0) ? "X" : null);
                } else {
                    $t = $user->timekeepings->first();
                    $sheet->setCellValueByColumnAndRow(4, $start_row, null);
                    $sheet->setCellValueByColumnAndRow(5, $start_row, null);
                    $sheet->setCellValueByColumnAndRow(6, $start_row, null);
                    $sheet->setCellValueByColumnAndRow(7, $start_row, null);
                    $sheet->setCellValueByColumnAndRow(8, $start_row, null);
                    $sheet->setCellValueByColumnAndRow(9, $start_row, 0);
                    $sheet->setCellValueByColumnAndRow(10, $start_row, 0);
                    $sheet->setCellValueByColumnAndRow(11, $start_row, 0);
                    $absence_str = "";
                    foreach ($t->absence as $absence) {
                        $absence_str .= "$absence->Name "
                            . "(" . Carbon::parse($absence->SDate)->format("H:i")
                            . " - " . Carbon::parse($absence->EDate)->format("H:i") . ")";
                    }
                    $sheet->setCellValueByColumnAndRow(12, $start_row, $absence_str);
                    $sheet->setCellValueByColumnAndRow(13, $start_row, null);
                    $sheet->setCellValueByColumnAndRow(14, $start_row, null);
                    $sheet->getStyle("A${start_row}:N${start_row}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => [
                                'rgb' => 'FEEFD0',
                            ],
                        ],
                    ]);
                }
                ++$start_row;
            }
            --$start_row;
            $sheet->getStyle("A8:N$start_row")->applyFromArray($this->_style_border);
            $sheet->getStyle("M8:N$start_row")->applyFromArray($this->_style_center);
            $sheet->getStyle("L8:L$start_row")->getAlignment()->setWrapText(true);
            $sheet->getStyle("L8:L$start_row")->applyFromArray($this->_style_center);
            $sheet->getStyle("A8:L$start_row")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ]);

        } catch (\Exception $exception) {
            Log::info("Download excel Timekeeping Scheduler: " . $exception->getMessage());
        }
    }

    private function _setStyle()
    {
        $this->_style_border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];
        $this->_style_center = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'font' => [
                'size' => 9,
            ],
        ];
        $this->_style_sunday = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'FF99CC',
                ],
            ],
        ];
        $this->_style_saturday = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'CCCCFF',
                ],
            ],
        ];
    }
}
