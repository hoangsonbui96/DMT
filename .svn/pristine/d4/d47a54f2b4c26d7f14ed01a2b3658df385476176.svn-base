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
use App\Exports\Sheets\EqExportHistoriesSheet;
class EqExportHistories extends  Controller implements WithMultipleSheets
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
        $data = array();
        foreach ($requests['list'] as $key => $value) {
        	if (!array_key_exists($value->old_user_owner.$value->user_owner,$data)){
        		$data[$value->old_user_owner.$value->user_owner]=array();
        	}
        	array_push($data[$value->old_user_owner.$value->user_owner], $value);
        }
        $sheets = [];
        $userName = '';
        $request = new \Illuminate\Http\Request;
        foreach ($data as $row) {
        	if($row[0]->old_user_owner!='')
        		$userName=User::find($row[0]->old_user_owner)->FullName;
        	else
        		$userName='Kho';

        	$sheets[] = new EqExportHistoriesSheet($row,$userName);
        }
        return $sheets;
    }
}