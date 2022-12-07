<?php

namespace App\Exports;

use App\Exports\Sheets\TimekeepingNewSheet;
use App\Http\Controllers\Controller;
use App\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TimekeepingNewExport extends  Controller implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    protected $month;
    protected $year;
    protected $user;
    function __construct($month, $year,$user) {
        $this->year = $year;
        $this->month = $month;
        $this->user = $user;
    }
    public function sheets(): array
    {
        $time = $this->year.'-'.$this->month;
        $User = explode(",", $this->user);
        $records = User::query()
            ->join('timekeepings_new', 'timekeepings_new.UserID', 'users.id')
            ->where('Date', 'like', '%'.$time.'%')
            ->where(function ($query) use ($User){
                foreach($User as $value){
                    $query->orWhere('UserID',$value);
                }
            })
            ->groupBy('users.id')
            ->select('users.FullName', 'users.IDFM', 'users.id as UserID', 'timekeepings_new.*')
            ->get();  
        $sheets = [];
        foreach($records as $record){
            $sheets[] = new TimekeepingNewSheet($record, $time);
        }
        return $sheets;
    }
}
