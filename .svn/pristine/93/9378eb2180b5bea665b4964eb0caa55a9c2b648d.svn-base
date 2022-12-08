<?php

namespace App\Exports;

use App\Equipment;
use App\EquipmentRegistration;
use App\EquipmentType;
use App\EquipmentUsingHistory;
use App\MasterData;
use App\Room;
use App\User;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\EqExportSheet;

class EqExport extends  Controller implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    protected $request;
    function __construct($request) {
        $this->request = $request;
    }
    public function sheets(): array
    {
        $requests =  $this->request;
        $sheets = [];
        $request = new \Illuminate\Http\Request;
        $sheets[] = new EqExportSheet('ds_thiet_bi',$requests,$request);
        return $sheets;
    }
}
