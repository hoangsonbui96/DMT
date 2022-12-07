<?php

namespace App\Http\Services\Timekeeping;

use App\TimekeepingNew;
use App\User;
use Carbon\Carbon;

class TimekeepingService
{
    public function getDataTotalInCpn($validate): array
    {
        $date = isset($validate["date"])
            ? Carbon::createFromFormat("m/Y", $validate["date"])->endOfMonth()
            : Carbon::now();

        $timekeepers = TimekeepingNew::query()
            ->whereMonth("Date", $date->month)
            ->whereYear("Date", $date->year)
            ->get();

        $user_keeping = $timekeepers->pluck("UserID")->unique();

        $users_with_trash = User::withTrashed()
            ->select("id", "FullName", "deleted", "role_group", "Active", "deleted_at")
            ->get();

        $users_active = $users_with_trash->filter(function ($r) {
            return ($r->deleted != 1 && $r->role_group != 1 && $r->Active == 1 && $r->deleted_at == null);
        })->pluck("id");

        $users_merge = $users_active->merge($user_keeping)->unique()->toArray();

        $users_before_month_7 = $users_with_trash->filter(function ($r) use ($users_merge) {
            return in_array($r->id, $users_merge);
        })->pluck("FullName", "id")->toArray();

        $data = [
            "_date" => $date,
            "_summary" => [
                "item" => [],
                "total_come_back_soon" => 0,
                "total_min_come_back_soon" => 0,
                "total_not_ck" => 0,
                "total_not_ck_in" => 0,
                "total_not_ck_out" => 0,
                "total_ck_late" => 0,
                "total_min_ck_late" => 0,
                "total_ck_on_time" => 0,
                "total_ck_at_cpn" => 0,
                "total_ck_at_home" => 0,
                "u_come_back_soon" => [],
                "u_min_come_back_soon" => [],
                "u_not_ck" => [],
                "u_not_ck_in" => [],
                "u_not_ck_out" => [],
                "u_ck_late" => [],
                "u_min_ck_late" => [],
                "u_ck_on_time" => [],
                "u_ck_at_cpn" => [],
                "u_ck_at_home" => []
            ],
            "_come_back_soon" => [
                "item" => [],
                "total_come_back_soon" => 0,
                "total_min_come_back_soon" => 0
            ],
            "_not_ck" => [
                "item" => [],
                "total_not_ck" => []
            ],
            "_not_ck_in" => [
                "item" => [],
                "total_not_ck" => 0
            ],
            "_not_ck_out" => [
                "item" => [],
                "total_not_ck" => 0
            ],
            "_ck_late" => [
                "item" => [],
                "total_ck_late" => 0,
                "total_min_ck_late" => 0
            ],
            "_ck_on_time" => [
                "item" => [],
                "total_ck_on_time" => 0,
            ],
            "_ck_at_cpn" => [
                "item" => [],
                "total_ck_at_cpn" => [],
            ],
            "_ck_at_home" => [
                "item" => [],
                "total_ck_at_home" => []
            ]
        ];
        $number_of_days = $date->day;
        $index = 1;
        while ($index <= $number_of_days) {
            $current = Carbon::create($date->year, $date->month, $index);
            $name = $current->format(FOMAT_DISPLAY_DAY);
            $index_of_week = $current->dayOfWeek;
            $weekday = $index_of_week == 0 ? "CN" : "T" . ($index_of_week + 1);
            $data["_summary"]["item"][] = [
                "name" => $name,
                "weekday" => $weekday,
                "u_come_back_soon" => [],
                "come_back_soon" => 0,
                "u_not_ck" => [],
                "not_ck" => 0,
                "u_not_ck_in" => [],
                "not_ck_in" => 0,
                "u_not_ck_out" => [],
                "not_ck_out" => 0,
                "u_ck_late" => [],
                "ck_late" => 0,
                "u_ck_on_time" => [],
                "ck_on_time" => 0,
                "u_ck_at_cpn" => [],
                "ck_at_cpn" => 0,
                "u_ck_at_home" => [],
                "ck_at_home" => 0
            ];
            $data["_come_back_soon"]["item"][] = [
                "name" => $name,
                "weekday" => $weekday,
                "u_come_back_soon" => [],
                "come_back_soon" => 0,
            ];
            $data["_not_ck"]["item"][] = [
                "name" => $name,
                "weekday" => $weekday,
                "u_not_ck" => [],
                "not_ck" => 0,
            ];
            $data["_not_ck_in"]["item"][] = [
                "name" => $name,
                "weekday" => $weekday,
                "u_not_ck_in" => [],
                "not_ck_in" => 0,
            ];
            $data["_not_ck_out"]["item"][] = [
                "name" => $name,
                "weekday" => $weekday,
                "u_not_ck_out" => [],
                "not_ck_out" => 0,
            ];
            $data["_ck_late"]["item"][] = [
                "name" => $name,
                "weekday" => $weekday,
                "u_ck_late" => [],
                "ck_late" => 0,
            ];
            $data["_ck_on_time"]["item"][] = [
                "name" => $name,
                "weekday" => $weekday,
                "u_ck_on_time" => [],
                "ck_on_time" => 0,
            ];
            $data["_ck_at_cpn"]["item"][] = [
                "name" => $name,
                "weekday" => $weekday,
                "u_ck_at_cpn" => [],
                "ck_at_cpn" => 0,
            ];
            $data["_ck_at_home"]["item"][] = [
                "name" => $name,
                "weekday" => $weekday,
                "u_ck_at_home" => [],
                "ck_at_home" => 0,
            ];
            $index++;
        }
        foreach ($timekeepers as $timekeeping) {
//            if ($timekeeping->Day == "T7" || $timekeeping->Day == "CN") {
//                continue;
//            }
            $i = Carbon::create($timekeeping->Date)->day - 1;
            $user_active = explode(",", $timekeeping->UserActive);
            $users = $timekeeping->UserActive == null
                ? $users_before_month_7
                : $users_with_trash->filter(function ($item) use ($user_active) {
                    return in_array($item->id, $user_active);
                })->pluck("FullName", "id")->toArray();
            // reference
            $summary_item = &$data["_summary"]["item"];
            $come_back_soon_item = &$data["_come_back_soon"]["item"];
            $ck_late = &$data["_ck_late"]["item"];
            $ck_on_time = &$data["_ck_on_time"]["item"];
            $not_ck_in = &$data["_not_ck_in"]["item"];
            $not_ck_out = &$data["_not_ck_out"]["item"];
            $ck_at_cpn = &$data["_ck_at_cpn"]["item"];
            $ck_at_home = &$data["_ck_at_home"]["item"];
            $not_ck = &$data["_not_ck"]["item"];

            // Người về sớm
            if ($timekeeping->TimeOut != null && $timekeeping->TimeOut < $timekeeping->ETimeOfDay) {
                // Add new user
                $this->_pushIfNotIn($timekeeping->UserID, $summary_item[$i]["u_come_back_soon"], $users);
                $come_back_soon_item[$i]["u_come_back_soon"] = $summary_item[$i]["u_come_back_soon"];
                $data["_summary"]["u_come_back_soon"] = array_unique(array_merge($data["_summary"]["u_come_back_soon"], $summary_item[$i]["u_come_back_soon"]));

                // Increase turn
                ++$summary_item[$i]["come_back_soon"];
                $come_back_soon_item[$i]["come_back_soon"] = $summary_item[$i]["come_back_soon"];
                ++$data["_summary"]["total_come_back_soon"];

                // Increase minutes
                $diff_min = $this->_diffMin($timekeeping->TimeOut, $timekeeping->ETimeOfDay);
                $data["_summary"]["total_min_come_back_soon"] += $diff_min;
                $data["_come_back_soon"]["total_min_come_back_soon"] += $diff_min;

            }
            // Người chấm công muộn
            if ($timekeeping->TimeIn != null && $timekeeping->TimeIn > $timekeeping->STimeOfDay) {
                // Add new user
                $this->_pushIfNotIn($timekeeping->UserID, $summary_item[$i]["u_ck_late"], $users);
                $ck_late[$i]["u_ck_late"] = $summary_item[$i]["u_ck_late"];
                $data["_summary"]["u_ck_late"] = array_unique(array_merge($data["_summary"]["u_ck_late"], $summary_item[$i]["u_ck_late"]));
                // Increase turn
                ++$summary_item[$i]["ck_late"];
                $ck_late[$i]["ck_late"] = $summary_item[$i]["ck_late"];
                ++$data["_summary"]["total_ck_late"];
                // Increase minutes
                $diff_min = $this->_diffMin($timekeeping->TimeIn, $timekeeping->STimeOfDay);
                $data["_summary"]["total_min_ck_late"] += $diff_min;
                $data["_ck_late"]["total_min_ck_late"] += $diff_min;
            }
            // Người chấm công đúng giờ
            if ($timekeeping->TimeIn != null && $timekeeping->TimeIn <= $timekeeping->STimeOfDay) {
                // Add new user
                $this->_pushIfNotIn($timekeeping->UserID, $summary_item[$i]["u_ck_on_time"], $users);
                $ck_on_time[$i]["u_ck_on_time"] = $summary_item[$i]["u_ck_on_time"];
                $data["_summary"]["u_ck_on_time"] = array_unique(array_merge($data["_summary"]["u_ck_on_time"], $summary_item[$i]["u_ck_on_time"]));
                // Increase turn
                ++$summary_item[$i]["ck_on_time"];
                $ck_on_time[$i]["ck_on_time"] = $summary_item[$i]["ck_on_time"];
                ++$data["_summary"]["total_ck_on_time"];
            }
            // Người không chấm công tới
            if ($timekeeping->TimeIn == null && $timekeeping->TimeOut != null) {
                // Add new user
                $this->_pushIfNotIn($timekeeping->UserID, $summary_item[$i]["u_not_ck_in"], $users);
                $not_ck_in[$i]["u_not_ck_in"] = $summary_item[$i]["u_not_ck_in"];
                $data["_summary"]["u_not_ck_in"] = array_unique(array_merge($data["_summary"]["u_not_ck_in"], $summary_item[$i]["u_not_ck_in"]));
                // Increase turn
                ++$summary_item[$i]["not_ck_in"];
                $not_ck_in[$i]["not_ck_in"] = $summary_item[$i]["not_ck_in"];
                ++$data["_summary"]["total_not_ck_in"];
            }
            // Người không chấm công về
            if ($timekeeping->TimeIn != null && $timekeeping->TimeOut == null) {
                // Add new user
                $this->_pushIfNotIn($timekeeping->UserID, $summary_item[$i]["u_not_ck_out"], $users);
                $not_ck_out[$i]["u_not_ck_out"] = $summary_item[$i]["u_not_ck_out"];
                $data["_summary"]["u_not_ck_out"] = array_unique(array_merge($data["_summary"]["u_not_ck_out"], $summary_item[$i]["u_not_ck_out"]));
                // Increase turn
                ++$summary_item[$i]["not_ck_out"];
                $not_ck_out[$i]["not_ck_out"] = $summary_item[$i]["not_ck_out"];
                ++$data["_summary"]["total_not_ck_out"];
            }
            // Người chấm công tại công ty
            if ($timekeeping->IsInCpn == 1) {
                // Add new user
                $this->_pushIfNotIn($timekeeping->UserID, $summary_item[$i]["u_ck_at_cpn"], $users);
                $ck_at_cpn[$i]["u_ck_at_cpn"] = $summary_item[$i]["u_ck_at_cpn"];
                $data["_summary"]["u_ck_at_cpn"] = array_unique(array_merge($data["_summary"]["u_ck_at_cpn"], $summary_item[$i]["u_ck_at_cpn"]));
                // Increase turn
                ++$summary_item[$i]["ck_at_cpn"];
                $ck_at_cpn[$i]["ck_at_cpn"] = $summary_item[$i]["ck_at_cpn"];
                ++$data["_summary"]["total_ck_at_cpn"];
            }
            // Người chấm công tại nhà
            if ($timekeeping->IsInCpn == 0) {
                // Add new user
                $this->_pushIfNotIn($timekeeping->UserID, $summary_item[$i]["u_ck_at_home"], $users);
                $ck_at_home[$i]["u_ck_at_home"] = $summary_item[$i]["u_ck_at_home"];
                $data["_summary"]["u_ck_at_home"] = array_unique(array_merge($data["_summary"]["u_ck_at_home"], $summary_item[$i]["u_ck_at_home"]));
                // Increase turn
                ++$summary_item[$i]["ck_at_home"];
                $ck_at_home[$i]["ck_at_home"] = $summary_item[$i]["ck_at_home"];
                ++$data["_summary"]["total_ck_at_home"];
            }
            // Người không chấm công
            $arr_merge = array_unique(array_merge($summary_item[$i]["u_ck_late"], $summary_item[$i]["u_ck_on_time"]));
            $arr_diff = array_diff($users, $arr_merge);
            // Add new user
            $summary_item[$i]["u_not_ck"] = $arr_diff;
            $not_ck[$i]["u_not_ck"] = $summary_item[$i]["u_not_ck"];
            $data["_summary"]["u_not_ck"] = array_unique(array_merge($data["_summary"]["u_not_ck"], $arr_diff));
        }
        return $data;
    }

    private function _pushIfNotIn($key_id, &$array, $users)
    {
        if (isset($users[$key_id])) {
            $array[$key_id] = $users[$key_id];
        }
    }

    private function _diffMin($a, $b): int
    {
        return Carbon::createFromFormat("H:i:s", $a)->diffInMinutes(Carbon::createFromFormat("H:i:s", $b));
    }

}
