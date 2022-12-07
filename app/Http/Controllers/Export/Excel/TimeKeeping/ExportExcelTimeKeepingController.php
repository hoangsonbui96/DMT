<?php

namespace App\Http\Controllers\Export\Excel\TimeKeeping;

use App\Http\Controllers\Export\Excel\ExportExcelController;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;


class ExportExcelTimeKeepingController extends ExportExcelController
{
    public $data;
    public $time;
    private $_style_sunday;
    private $_style_saturday;
    private $_style_border;

    public function __construct($date, $data)
    {
        $this->data = $data;
        $this->time = Carbon::now()->format(FOMAT_DISPLAY_TIME);
        $this->_setStyle();
        parent::__construct($date);
    }

    private function _setStyle()
    {
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
        $this->_style_border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];
    }

    public function bindData(&$spread_sheet)
    {
        try {

            $come_back_soon_sheet = $spread_sheet->getSheet(0);
            $this->_sameSheet($come_back_soon_sheet, "Bảng chi tiết người về sớm tháng ", "total_come_back_soon", "come_back_soon", "u_come_back_soon", "_come_back_soon", "total_min_come_back_soon");

            $ck_late_sheet = $spread_sheet->getSheet(2);
            $this->_sameSheet($ck_late_sheet, "Bảng chi tiết người chấm công muộn tháng ", "total_ck_late", "ck_late", "u_ck_late", "_ck_late", "total_min_ck_late");

            $ck_at_home_sheet = $spread_sheet->getSheet(3);
            $this->_sameSheet($ck_at_home_sheet, "Bảng chi tiết người chấm công tại nhà tháng ", "total_ck_at_home", "ck_at_home", "u_ck_at_home", "_ck_at_home");

            $ck_at_cpn_sheet = $spread_sheet->getSheet(4);
            $this->_sameSheet($ck_at_cpn_sheet, "Bảng chi tiết người chấm công tại công ty tháng ", "total_ck_at_cpn", "ck_at_cpn", "u_ck_at_cpn", "_ck_at_cpn");

            // Sheet not checkin
            $not_ck_sheet = $spread_sheet->getSheet(1);
            $this->_notCKSheet($not_ck_sheet);

            // Sheet Summary
            $summary_sheet = $spread_sheet->getSheet(5);
            $this->_summarySheet($summary_sheet);
        } catch (Exception $e) {

        }
    }

    private function _sameSheet(&$same_sheet, $title, $total_attr, $attr, $u_attr, $_attr, $total_min_attr = null)
    {
        $data = $this->data;

        // Column 1
        $same_sheet->setCellValueByColumnAndRow(1, 1, $title . $data["_date"]->month);
        $same_sheet->setCellValueByColumnAndRow(1, 2, "Dữ liệu được thống kê vào lúc $this->time ngày " . $data["_date"]->format(FOMAT_DISPLAY_DAY));

        $same_sheet->setCellValueByColumnAndRow(2, 3, count($data["_summary"][$u_attr]) . " người");
        $same_sheet->setCellValueByColumnAndRow(3, 3, $data["_summary"][$total_attr] . " lần");

        // if ($total_min_attr != null) {
        //     $same_sheet->setCellValueByColumnAndRow(4, 3, number_format($data["_summary"][$total_min_attr], 2) . " giờ");
        // }
        $row = 7;
        $same_sheet->freezePane("A$row");
        foreach ($data[$_attr]["item"] as $item) {
            $weekday = $item["weekday"];
            if ($weekday == "CN") {
                $same_sheet->getStyle("A$row:I$row")->applyFromArray($this->_style_sunday);
            }
            if ($weekday == "T7") {
                $same_sheet->getStyle("A$row:I$row")->applyFromArray($this->_style_saturday);
            }
            $same_sheet->setCellValueByColumnAndRow(1, $row, $item["name"]);
            $same_sheet->setCellValueByColumnAndRow(2, $row, $item["weekday"]);
            $same_sheet->setCellValueByColumnAndRow(3, $row, $item[$attr]);
            $same_sheet->setCellValueByColumnAndRow(4, $row, count($item[$u_attr]));
            $same_sheet->setCellValueByColumnAndRow(5, $row, implode(", ", $item[$u_attr]));
            $same_sheet->getStyle("E$row:I$row")->getAlignment()->setWrapText(true);
            $same_sheet->mergeCells("E$row:I$row");
            ++$row;
        }
        --$row;
        $same_sheet->getStyle("A7:I$row")->applyFromArray($this->_style_border);
    }

    private function _notCKSheet(&$not_ck_sheet)
    {
        $data = $this->data;

        $not_ck_sheet->setCellValueByColumnAndRow(1, 1, "Bảng chi tiết người không chấm công tháng " . $data["_date"]->month);
        $not_ck_sheet->setCellValueByColumnAndRow(1, 2, "Dữ liệu được thống kê vào lúc $this->time ngày " . $data["_date"]->format(FOMAT_DISPLAY_DAY));

        $not_ck_sheet->setCellValueByColumnAndRow(2, 3, count($data["_summary"]["u_not_ck_in"]) . " người");
        $not_ck_sheet->setCellValueByColumnAndRow(3, 3, $data["_summary"]["total_not_ck_in"] . " lần");
        $not_ck_sheet->setCellValueByColumnAndRow(8, 3, count($data["_summary"]["u_not_ck_out"]) . " người");
        $not_ck_sheet->setCellValueByColumnAndRow(9, 3, $data["_summary"]["total_not_ck_out"] . " lần");
        $not_ck_sheet->setCellValueByColumnAndRow(15, 3, count($data["_summary"]["u_not_ck"]) . " người");
        $number_record = count($data["_summary"]["item"]);
        $row = 8;
        $not_ck_sheet->freezePane("A$row");
        for ($i = 0; $i < $number_record; $i++) {
            $item_summary = $data["_summary"]["item"][$i];
            $item_not_ck_in = $data["_not_ck_in"]["item"][$i];
            $item_not_ck_out = $data["_not_ck_out"]["item"][$i];
            $item_not_ck = $data["_not_ck"]["item"][$i];

            $weekday = $item_summary["weekday"];
            if ($weekday == "CN") {
                $not_ck_sheet->getStyle("A$row:S$row")->applyFromArray($this->_style_sunday);
            }
            if ($weekday == "T7") {
                $not_ck_sheet->getStyle("A$row:S$row")->applyFromArray($this->_style_saturday);
            }
            $not_ck_sheet->setCellValueByColumnAndRow(1, $row, $item_summary["name"]);
            $not_ck_sheet->setCellValueByColumnAndRow(2, $row, $item_summary["weekday"]);

            $not_ck_sheet->setCellValueByColumnAndRow(3, $row, $item_not_ck_in["not_ck_in"]);
            $not_ck_sheet->setCellValueByColumnAndRow(4, $row, $item_not_ck_out["not_ck_out"]);

            $not_ck_sheet->setCellValueByColumnAndRow(5, $row, count($item_not_ck_in["u_not_ck_in"]));
            $not_ck_sheet->setCellValueByColumnAndRow(6, $row, count($item_not_ck_out["u_not_ck_out"]));
            $not_ck_sheet->setCellValueByColumnAndRow(7, $row, count($item_not_ck["u_not_ck"]));

            $not_ck_sheet->setCellValueByColumnAndRow(8, $row, implode(", ", $item_not_ck_in["u_not_ck_in"]));
            $not_ck_sheet->getStyle("H$row:K$row")->getAlignment()->setWrapText(true);
            $not_ck_sheet->mergeCells("H$row:K$row");

            $not_ck_sheet->setCellValueByColumnAndRow(12, $row, implode(", ", $item_not_ck_out["u_not_ck_out"]));
            $not_ck_sheet->getStyle("L$row:O$row")->getAlignment()->setWrapText(true);
            $not_ck_sheet->mergeCells("L$row:O$row");

            $not_ck_sheet->setCellValueByColumnAndRow(16, $row, implode(", ", $item_not_ck["u_not_ck"]));
            $not_ck_sheet->getStyle("P$row:S$row")->getAlignment()->setWrapText(true);
            $not_ck_sheet->mergeCells("P$row:S$row");

            ++$row;
        }
        --$row;
        $not_ck_sheet->getStyle("A8:S$row")->applyFromArray($this->_style_border);
    }

    private function _summarySheet(&$summary_sheet)
    {
        $data = $this->data;

        // Column 1
        $summary_sheet->setCellValueByColumnAndRow(1, 1, "Bảng dữ liệu chấm công tháng " . $data["_date"]->month);
        $summary_sheet->setCellValueByColumnAndRow(1, 2, "Dữ liệu được thống kê vào lúc $this->time ngày " . $data["_date"]->format(FOMAT_DISPLAY_DAY));

        // Column 2
        $summary_sheet->setCellValueByColumnAndRow(2, 3, count($data["_summary"]["u_not_ck_in"]) . " người");
        $summary_sheet->setCellValueByColumnAndRow(2, 4, count($data["_summary"]["u_not_ck_out"]) . " người");
        $summary_sheet->setCellValueByColumnAndRow(2, 5, count($data["_summary"]["u_not_ck"]) . " người");

        // Column 3
        $summary_sheet->setCellValueByColumnAndRow(3, 3, $data["_summary"]["total_not_ck_in"] . " lần");
        $summary_sheet->setCellValueByColumnAndRow(3, 4, $data["_summary"]["total_not_ck_out"] . " lần");

        // Column 5
        $summary_sheet->setCellValueByColumnAndRow(5, 3, count($data["_summary"]["u_ck_late"]) . " người");
        $summary_sheet->setCellValueByColumnAndRow(5, 4, count($data["_summary"]["u_come_back_soon"]) . " người");

        // Column 6
        $summary_sheet->setCellValueByColumnAndRow(6, 3, $data["_summary"]["total_ck_late"] . " lần");
        $summary_sheet->setCellValueByColumnAndRow(6, 4, $data["_summary"]["total_come_back_soon"] . " lần");

        // Column 7
        // $summary_sheet->setCellValueByColumnAndRow(7, 3, number_format($data["_summary"]["total_min_ck_late"], 2) . " giờ");
        // $summary_sheet->setCellValueByColumnAndRow(7, 4, number_format($data["_summary"]["total_min_come_back_soon"], 2) . " giờ");

        // Column 9
        $summary_sheet->setCellValueByColumnAndRow(9, 3, $data["_summary"]["total_ck_at_cpn"] . " lần");
        $summary_sheet->setCellValueByColumnAndRow(9, 4, $data["_summary"]["total_ck_at_home"] . " lần");

        $row = 9;
        $summary_sheet->freezePane("A$row");
        foreach ($data["_summary"]["item"] as $item) {
            $weekday = $item["weekday"];
            if ($weekday == "CN") {
                $summary_sheet->getStyle("A$row:I$row")->applyFromArray($this->_style_sunday);
            }
            if ($weekday == "T7") {
                $summary_sheet->getStyle("A$row:I$row")->applyFromArray($this->_style_saturday);
            }
            $summary_sheet->setCellValueByColumnAndRow(1, $row, $item["name"]);
            $summary_sheet->setCellValueByColumnAndRow(2, $row, $item["weekday"]);
            $summary_sheet->setCellValueByColumnAndRow(3, $row, $item["not_ck_in"]);
            $summary_sheet->setCellValueByColumnAndRow(4, $row, $item["not_ck_out"]);
            $summary_sheet->setCellValueByColumnAndRow(5, $row, $item["not_ck"]);
            $summary_sheet->setCellValueByColumnAndRow(6, $row, $item["ck_late"]);
            $summary_sheet->setCellValueByColumnAndRow(7, $row, $item["come_back_soon"]);
            $summary_sheet->setCellValueByColumnAndRow(8, $row, $item["ck_at_cpn"]);
            $summary_sheet->setCellValueByColumnAndRow(9, $row, $item["ck_at_home"]);
            ++$row;
        }
        --$row;
        $summary_sheet->getStyle("A9:I$row")->applyFromArray($this->_style_border);
    }

    protected function setFolderName()
    {
        $this->folder_name = "Timekeeping\\";
    }

    protected function setFileName()
    {
        $this->file_name = "ThongKeChamCong.xlsx";
    }

    protected function setPathFile()
    {
        $this->path_file = $this->folder_name . $this->file_name;
    }
}
