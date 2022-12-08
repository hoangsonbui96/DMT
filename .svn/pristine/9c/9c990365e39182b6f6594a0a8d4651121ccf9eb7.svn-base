<?php

namespace App\Http\Services\DailyReport;

use App\Project;
use App\User;
use Carbon\Carbon;

class DailyReportService
{
    public function getDataGeneralReportProject($validated, $arr_user_active_id, $order, $type)
    {
        $start_date = isset($validated["StartDate"])
            ? Carbon::createFromFormat(FOMAT_DISPLAY_DAY, $validated["StartDate"])
            : null;
        $end_date = isset($validated["EndDate"])
            ? Carbon::createFromFormat(FOMAT_DISPLAY_DAY, $validated["EndDate"])
            : null;
        $project_id = isset($validated["Project"])
            ? $validated["Project"]
            : [];
        $user_id = isset($validated["User"])
            ? $validated["User"]
            : [];
        if (!empty($project_id) && empty($user_id)) {
            $member = implode(",", Project::query()
                ->whereIn("id", $project_id)->select("Member")->pluck("Member")->toArray());
            $user_id = array_filter(array_unique(explode(",", $member)));
        }
        if (empty($project_id) && empty($user_id)) {
            $user_id = $arr_user_active_id;
        }

        // Query daily report
        $users_daily = User::with(["dailyReports" => function ($q) use ($start_date, $end_date, $project_id) {
            $q->when(!empty($project_id), function ($query) use ($project_id) {
                return $query->whereIn("ProjectID", $project_id);
            })
                ->when(($start_date == null && $end_date != null), function ($query) use ($end_date) {
                    return $query
                        ->whereDate("Date", ">=", Carbon::now()->startOfMonth()->format("Y-m-d"))
                        ->whereDate("Date", "<=", $end_date);
                })
                ->when(($start_date != null && $end_date == null), function ($query) use ($start_date) {
                    return $query
                        ->whereDate("Date", ">=", $start_date)
                        ->whereDate("Date", "<=", Carbon::now()->format("Y-m-d"));
                })
                ->when(($start_date != null && $end_date != null), function ($query) use ($start_date, $end_date) {
                    return $query
                        ->whereDate("Date", ">=", $start_date)
                        ->whereDate("Date", "<=", $end_date);
                })
                ->when(($start_date == null && $end_date == null), function ($query) {
                    return $query
                        ->whereMonth("Date", Carbon::now()->month)
                        ->whereYear("Date", Carbon::now()->year);
                })
                ->orderBy("id");
        }])->select("id", "FullName")
//            ->when(!empty($user_id), function ($query) use ($user_id) {
//                return $query->whereIn("id", $user_id);
//            })
//            ->when(empty($user_id), function ($query) {
//                return $query->where("id", auth()->id());
//            })
            ->where("Active", 1)
            ->where("role_group", "!=", 1)
            ->where("deleted", "!=", 1)
            ->whereIn("id", $user_id)
            ->orderBy("FullName")->get();

        $users_daily->transform(function ($item) use ($project_id) {
            $item->pivot = $item->dailyReports->groupBy("ProjectID");
            $item->pivot->transform(function ($item_1) {
                $item_1->TotalHours = $item_1->sum("WorkingTime");
                return $item_1;
            });
            $item->pivot->TotalHours = $item->pivot->sum("TotalHours");
            return $item;
        });
        return $this->_sortCollection($users_daily, $order, $type);
    }

    public function getDataGeneralReportWork($validated, $arr_user_active_id, $order, $type)
    {
        $start_date = isset($validated["StartDate"])
            ? Carbon::createFromFormat(FOMAT_DISPLAY_DAY, $validated["StartDate"])
            : null;
        $end_date = isset($validated["EndDate"])
            ? Carbon::createFromFormat(FOMAT_DISPLAY_DAY, $validated["EndDate"])
            : null;
        $work_type = isset($validated["WorkType"])
            ? $validated["WorkType"]
            : [];
        $user_id = isset($validated["User"])
            ? $validated["User"]
            : $arr_user_active_id;

        $users_daily = User::with(["dailyReports" => function ($q) use ($start_date, $end_date, $work_type) {
            $q->when(!empty($work_type), function ($query) use ($work_type) {
                return $query->whereIn("TypeWork", $work_type);
            })
                ->when(($start_date == null && $end_date != null), function ($query) use ($end_date) {
                    return $query
                        ->whereDate("Date", ">=", Carbon::now()->startOfMonth()->format("Y-m-d"))
                        ->whereDate("Date", "<=", $end_date);
                })
                ->when(($start_date != null && $end_date == null), function ($query) use ($start_date) {
                    return $query
                        ->whereDate("Date", ">=", $start_date)
                        ->whereDate("Date", "<=", Carbon::now()->format("Y-m-d"));
                })
                ->when(($start_date != null && $end_date != null), function ($query) use ($start_date, $end_date) {
                    return $query
                        ->whereDate("Date", ">=", $start_date)
                        ->whereDate("Date", "<=", $end_date);
                })
                ->when(($start_date == null && $end_date == null), function ($query) {
                    return $query
                        ->whereMonth("Date", Carbon::now()->month)
                        ->whereYear("Date", Carbon::now()->year);
                })
                ->orderBy("id");
        }])->select("id", "FullName")
            ->when(!empty($user_id), function ($query) use ($user_id) {
                return $query->whereIn("id", $user_id);
            })
//            ->when(empty($user_id), function ($query) {
//                return $query->where("id", auth()->id());
//            })
            ->where("Active", 1)
            ->where("role_group", "!=", 1)
            ->where("deleted", "!=", 1)
            ->orderBy("FullName")->get();

        $users_daily->transform(function ($item) use ($work_type) {
            $item->pivot = $item->dailyReports->groupBy("TypeWork");
            $item->pivot->transform(function ($item_1) {
                $item_1->TotalHours = $item_1->sum("WorkingTime");
                return $item_1;
            });
            $item->pivot->TotalHours = $item->pivot->sum("TotalHours");
            return $item;
        });
        return $this->_sortCollection($users_daily, $order, $type);
    }

    private function _sortCollection($obj, $order, $type)
    {
        if ($type == "asc") {
            return $obj->sortBy(function ($item) use ($order) {
                switch ($order) {
                    case "full-name":
                        return $item->FullName;
                    case "total-hours":
                        return $item->pivot->TotalHours;
                    default:
                        if (!isset($item->pivot[$order])) {
                            $item->pivot[$order] = (object)['TotalHours' => 0];
                        }
                        return $item->pivot[$order]->TotalHours;
                }
            })->values();
        }
        if ($type == "desc") {
            return $obj->sortByDesc(function ($item) use ($order) {
                switch ($order) {
                    case "full-name":
                        return $item->FullName;
                    case "total-hours":
                        return $item->pivot->TotalHours;
                    default:
                        if (!isset($item->pivot[$order])) {
                            $item->pivot[$order] = (object)['TotalHours' => 0];
                        }
                        return $item->pivot[$order]->TotalHours;
                }
            })->values();
        }
        return $obj;
    }
}
