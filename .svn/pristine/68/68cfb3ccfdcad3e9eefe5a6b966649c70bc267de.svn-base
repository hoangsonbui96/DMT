<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ApiBaseController;
use App\Http\Controllers\Controller;
use App\Project;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class ApiMembersController extends  ApiBaseController
{
    //
    private const SCREEN_NAME = 'TaskWorking';

    public function __construct()
    {
        $this->detailRoleScreen(self::SCREEN_NAME);
    }

    /*
     * Trả về thông tin của tất cả thành viên trong 1 dự án
     */
    public function getInfoMembers(Request $request, $id_pro): \Illuminate\Http\JsonResponse
    {
        try {
            $this->authorize('action', $this->role_list['View']);
            $project = Project::withTrashed()->findOrFail($id_pro);
            $id_leaders = array_slice(explode(",", $project->Leader), 1, -1);
            $id_members = array_slice(explode(",", $project->Member), 1, -1);
            $value = array_unique(array_merge($id_leaders, $id_members));
            return AdminController::responseApi(200, null, true, ['members' => $this->_getInfoByID($value, count($id_leaders)), 'role_key' => self::SCREEN_NAME]);
        } catch (AuthorizationException $e) {
            return AdminController::responseApi(403, $e->getMessage(), false);
        } catch (\Exception $exception) {
            return AdminController::responseApi(500, $exception->getMessage(), false);
        }
    }

    private function _getInfoByID($arr_id, $number = null): array
    {
        if (empty($arr_id))
            return [];
        $arr_info = [];
        foreach ($arr_id as $index => $id){
            $temp = User::withTrashed()->select('id', 'FullName')->where('id', $id)->first();
            $temp['leader'] = false;
            if ($index < $number){
                $temp['leader'] = true;
            }
            array_push($arr_info, $temp);
        }
        return $arr_info;
    }
}
