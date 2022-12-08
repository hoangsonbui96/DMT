<?php


namespace Modules\ProjectManager\Http\Repositories;


use Modules\ProjectManager\Entities\Calendar;

class CalendarRepo
{
    public function get($data)
    {
        $holidays = Calendar::query();

        if ($data['startDate']) {
            $holidays = $holidays->where('StartDate','>=', $data['startDate']);
        }
        if ($data['endDate']) {
            $holidays = $holidays->where('EndDate','<=', $data['endDate']);
        }
        $holidays = $holidays->where('CalendarID',1) -> get();
        return $holidays;
    }
}
