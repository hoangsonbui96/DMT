<?php

namespace App\Http\Controllers\Admin\Checkin;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Export\Excel\TimeKeeping\ExportExcelTimeKeepingSchedulerController;
use App\Http\Services\Timekeeping\TimekeepingSchedulerService;
use App\MasterData;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class TimekeepingSchedulerController extends AdminController
{
    //
    const ALIAS = "TimekeepingScheduler";
    private $_timekeeping_service;

    /**
     * TimekeepingSchedulerController constructor.
     * @param Request $request
     * @param TimekeepingSchedulerService $_timekeeping_service
     */
    public function __construct(Request $request, TimekeepingSchedulerService $_timekeeping_service)
    {
        parent::__construct($request);
        $this->detailRoleScreen(self::ALIAS);
        $this->_timekeeping_service = $_timekeeping_service;
    }

    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        try {
            $this->authorize("action", $this->role_list["View"]);
        } catch (AuthorizationException $e) {
            abort(403);
        }
        $validated = $request->validate([
            "users_search" => "nullable|array",
            "users_search.*" => "required|integer",
            "date" => "nullable|date_format:d/m/Y"
        ]);
        $this->data["users"] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $date = isset($validated["date"])
            ? Carbon::createFromFormat(self::FOMAT_DISPLAY_DMY, $validated["date"])
            : Carbon::now();
        $user_select = isset($validated["users_search"])
            ? $validated["users_search"]
            : $this->data["users"]->pluck("id")->toArray();
        $this->data["users_keeping"] = $this->_timekeeping_service->getData($user_select, $date);
        $this->data["role"] = $this->role_list;
        $master = MasterData::query()->whereIn("DataValue", ["WT001", "WT002"])->get();
        $this->data["WT001"] = $master[0];
        $this->data["WT002"] = $master[1];
        return $this->viewAdminLayout("checkin.timekeeping-scheduler", $this->data);
    }

    public function export(Request $request): JsonResponse
    {
        try {
            $this->authorize("action", $this->role_list["Export"]);
        } catch (AuthorizationException $e) {
            return response()->json($e->getMessage());
        }
        $validated = $request->validate([
            "users_search" => "nullable|array",
            "users_search.*" => "required|integer",
            "date" => "nullable|date_format:d/m/Y"
        ]);
        $date = isset($validated["date"])
            ? Carbon::createFromFormat(self::FOMAT_DISPLAY_DMY, $validated["date"])
            : Carbon::now();
        $user_select = isset($validated["users_search"])
            ? $validated["users_search"]
            : $this->GetListUser(self::USER_ACTIVE_FLAG)->pluck("id")->toArray();
        $data = $this->_timekeeping_service->getData($user_select, $date);
        new ExportExcelTimeKeepingSchedulerController($date->format("d_m_Y"), $data);
        return response()->json("Export success");
    }
}
