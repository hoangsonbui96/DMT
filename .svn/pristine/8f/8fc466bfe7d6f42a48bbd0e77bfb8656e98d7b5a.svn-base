<?php

namespace App\Http\Controllers\Export\Excel;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

abstract class ExportExcelController
{
    const FOLDER_EXCEL = "exports\\excel";
    private $_path_excel;
    protected $folder_name;
    protected $file_name;
    protected $path_file;
    private $_date;

    public function __construct($_date)
    {
        $this->_date = $_date;
        $this->_path_excel = Storage::disk("local")
                ->getDriver()
                ->getAdapter()
                ->getPathPrefix() . self::FOLDER_EXCEL;
        $this->setFolderName();
        $this->setFileName();
        $this->setPathFile();
        $this->_export();
    }

    public function getFileName()
    {
        return $this->file_name;
    }

    public function getFolderName()
    {
        return $this->folder_name;
    }

    public function getPathFile()
    {
        return $this->path_file;
    }

    protected function reader(string $file_location): ?Spreadsheet
    {
        $file_path = $this->_path_excel . "\\" . $file_location;
        try {
            $reader = IOFactory::createReaderForFile($file_path);
            return $reader->load($file_path);
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            return null;
        }
    }

    protected function download($file_name, $spreadsheet)
    {
        $arr_file = explode(".", $file_name);
        $arr_length = count($arr_file);
        $name = $arr_file[0];
        $extension = $arr_file[$arr_length - 1];
        $file_name = $name . "_" . $this->_date . "." . $extension;
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Type:application/octet-stream; charset=Shift_JIS');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Type: text/html; charset=UTF-8');
        header('Content-Disposition: attachment; filename=' . $file_name);
        header("Content-Transfer-Encoding: binary");
        header('Expires: 0');
        header('Cache-Control: no-cache, no-store');
        header('Pragma: no-cache');
        if (isset($_COOKIE['export']) && $_COOKIE['export'] == 0) {
            unset($_COOKIE['export']);
            setcookie("export", 1, -1, '/');
        }
        try {
            $check = mb_strlen($arr_file[$arr_length - 1]) == 3;
            $writer = $check
                ? IOFactory::createWriter($spreadsheet, ucfirst($arr_file[$arr_length - 1]))
                : IOFactory::createWriter($spreadsheet, "Xlsx");
            ob_end_clean();
            $writer->save("php://output");
            exit();
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
            Log::debug("PhpSpreadSheet: " . $e->getMessage());
        }
    }

    private function _export()
    {
        $spread_sheet = $this->reader($this->getPathFile());
        if ($spread_sheet) {
            $this->bindData($spread_sheet);
            $this->download($this->getFileName(), $spread_sheet);
        }
    }

    abstract protected function setFolderName();

    abstract protected function setFileName();

    abstract protected function setPathFile();

    abstract protected function bindData(&$spread_sheet);
}
