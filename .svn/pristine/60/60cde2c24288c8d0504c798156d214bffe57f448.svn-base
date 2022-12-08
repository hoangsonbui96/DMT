<?php

namespace App\Http\Controllers\Admin\Position;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use App\Model\ListPosition;
use App\Model\ListPositionUser;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\User;

class ListPositionController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    const KEYMENU = array(
        "add" => "ListPositionAdd",
        "view" => "ListPosition",
        "edit" => "ListPositionEdit",
        "delete" => "ListPositionDelete",
    );
    /**
     * MasterDataController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');
        $array = $this->RoleView('Users', ['ListPosition']);
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if ($value == $row->alias)
                    $this->$key = $row;
            }
        }
    }
    public function show(Request $request, $orderBy = 'level', $sortBy = 'asc')
    {
        $this->authorize('view', $this->menu);

        $recordPerPage = config('settings.records_per_page');
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        if (Schema::hasColumn('list_position', $orderBy)) {
            $listPosition = ListPosition::orderBy($orderBy, $sortBy)->where('DataValue', 'not like', '%' . '000' . '%');
        } else {
            return redirect()->back();
        }
        $groupDataKey = ListPosition::query()->withTrashed()
            ->select('DataKey', 'TypeName')
            ->groupBy('DataKey', 'TypeName')
            ->orderBy('DataKey', 'desc')
            ->get();
        $this->data['request'] = $request->query();
        $id = null !== $request->input('dataKey') ? $request->input('dataKey') : '';
        if ($id == '') {
            $oneDataKey = ListPosition::query()
                ->select('DataKey')
                ->limit(1)
                ->get();
            foreach ($oneDataKey as $rows) {
                $this->data['request'] =  array('dataKey' => $rows->DataKey);
            }
        }
        $one = ListPosition::query()->select('DataKey', 'Name', 'TypeName', 'DataValue', 'DataDescription')->first();
        if ($one) {
            $one = $one->toArray();
            if (array_key_exists('search', $request->input())) {
                $listPosition = $listPosition->where(function ($query) use ($one, $request) {
                    foreach ($one as $key => $value) {
                        $query->orWhere($key, 'like', '%' . $request->input('search') . '%');
                    }
                });
            }
        }
        foreach ($this->data['request'] as $key => $value) {
            if (!is_array($value)) {
                if (Schema::hasColumn('list_position', $key) && $value !== null) {
                    $listPosition = $listPosition->where($key, 'like', '%' . $value . '%');
                }
            }
        }
        $listPosition = $listPosition->paginate($recordPerPage);
        foreach ($listPosition as $item) {
            $listUser = ListPositionUser::orderBy($orderBy, $sortBy)->where('DataValue', '=', $item->DataValue)->get();
            $name = [];
            $userList = [];
            foreach ($listUser as $user) {
                $fullName = User::query()->where('id', '=', $user->UserId)->where('Active', '=', 1)->first();
                if ($fullName) {
                    array_push($name, $fullName->FullName);
                    array_push($userList, $user->UserId);
                }
            }
            $item['ListUser']    = $userList;
            $item['ListUserName'] = implode(", ", $name);
        }

        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;
        $this->data['groupDataKey'] = $groupDataKey;
        $this->data['sort_link'] = $sort_link;
        $this->data['listPosition'] = $listPosition;
        return $this->viewAdminLayout('listPosition.list-position', $this->data);
    }

    public function showDetail(Request $request, $oneId = null, $del = null)
    {
        if ($oneId != null) {
            if ($del == 'del') {
                $one = ListPosition::query()->where('id', '=', $oneId);
                $dataValue = $one->first();
                $saveListUserNew = ListPositionUser::query()->where('DataValue', '=', $dataValue->DataValue);
                $eight = ListPosition::query()->where('id', '=', $oneId)->first();
                $listDataPosition =  ListPosition::query()
                    ->where('DataKey', $eight['DataKey'])
                    ->orderBy('id', 'desc')->get();
                $two = [];
                $five = [];
                foreach ($listDataPosition as $item) {
                    if ($item->Level >= $eight['Level'] && $item->id != $eight['id']) {
                        $dataUpdate =  ['id' => $item->id, 'Level' => $item->Level];
                        array_push($two, $dataUpdate);
                        array_push($five, $item->Level);
                    }
                }
                if (!in_array($eight['Level'], $five)) {
                    foreach ($two as $item) {
                        $listPositionUpdateLevel = ListPosition::query()
                            ->where('DataKey', $eight['DataKey'])
                            ->orderBy('id', 'desc')->where('id', '=', $item['id'])->update(['Level' => ($item['Level'] - 1)]);
                    }
                }
                if ($one) {
                    $saveOne = $one->delete();
                    $saveTwo = $saveListUserNew->delete();
                }
                if (!$saveOne) {
                    return $this->jsonErrors('Xóa không thành công');
                } else {
                    return 1;
                }
            }
            $this->data['userAssign'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
            $this->data['itemInfo'] = ListPosition::find($oneId);
            $three = ListPosition::query()->withTrashed()
                ->select('Level', 'DataKey')
                ->where('DataKey', '=', ($this->data['itemInfo'])['DataKey'])
                ->where('Level','!=','0')
                ->groupBy('Level')
                ->orderBy('Level', 'desc')
                ->get();
            $four = [];
            $six  = [];
            for ($i = 1; $i <= $three->count() + 1; $i++) {
                array_push($four, ['id' => $i, 'Name' => $i]);
            }
            for ($i = 1; $i <= $three->count(); $i++) {
                array_push($six, ['id' => $i, 'Name' => $i]);
            }
            $this->data['level'] = $four;
            $this->data['countLevel'] = $three->count();
            $this->data['levelInsert'] = $six;
            if ($this->data['itemInfo']) {
                $item = $this->data['itemInfo'];
                $item['oldLevel'] = $item['Level'];
                // $item['level'] = $four;
                // $orderBy = 'id';$sortBy = 'asc';
                $listUser = ListPositionUser::query()->where('DataValue', '=', $item->DataValue)->get();
                $userList = [];
                foreach ($listUser as $user) {
                    array_push($userList, $user->UserId);
                }
                $item['ListUser'] = implode(", ", $userList);
                $item['ListUserOld'] = implode(",", $userList);
                $PermissionEdit = ListPosition::find($oneId)->PermissionEdit;
                if ($PermissionEdit == 1) {
                    return $this->jsonErrors('Bạn không có quyền sửa ');
                } else {
                    return $this->viewAdminIncludes('listPosition.list-position-detail', $this->data);
                }
            } else {
                return "";
            }
        } else {
            $this->data['userAssign'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
            $groupDataKey = ListPosition::query()->withTrashed()
                ->select('DataKey', 'TypeName')
                ->groupBy('DataKey', 'TypeName')
                ->orderBy('DataKey', 'desc')
                ->get();
            $this->data['groupDataKey'] = $groupDataKey;
                $three = ListPosition::query()->withTrashed()
                    ->select('Level', 'DataKey')
                    ->where('DataKey', '=', $request['DataKey'])
                    ->where('Level','!=','0')
                    ->groupBy('Level', 'DataKey')
                    ->orderBy('Level', 'desc')
                    ->get();
                $four = [];
                $six  = [];

                for ($i = 1; $i <= $three->count() + 1; $i++) {
                    array_push($four, ['id' => $i, 'Name' => $i]);
                }
                for ($i = 1; $i <= $three->count(); $i++) {
                    array_push($six, ['id' => $i, 'Name' => $i]);
                }
            $this->data['level'] = $four;
            // dd($arrayLevel['VP']);
            $this->data['levelInsert'] = $six;
            return $this->viewAdminIncludes('listPosition.list-position-detail', $this->data);
        }
    }
    public function showDetailGroupPosition(Request $request, $oneId = null, $del = null)
    {
            $this->data['userAssign'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
            return $this->viewAdminIncludes('listPosition.add-list-group-position', $this->data);
    }
    public static function getListAssignUser($list_assign_id = null, $list_user = null)
    {
        $list_assign_user = array();
        $list_assign_ids = explode(',', $list_assign_id);
        foreach ($list_assign_ids as $list_assign_id) {
            $user_fullname = User::withTrashed()->where('id', $list_assign_id)->first();
            if ($user_fullname) {
                $list_assign_user[] = $user_fullname->FullName;
            }
        }
        if (count($list_assign_user) < count($list_user)) {
            $assign_user = implode(' <br> ', $list_assign_user);
        } else {
            $assign_user = 'Tất cả nhân viên công ty';
        }

        return $assign_user;
    }
    public function storeGroupPosition(Request $request, $id = null)
    {
        // dd($request->input());
        try {
            if (count($request->input()) > 0) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'Name'  =>  'required|string|max:1000',
                        'DataDescription'  =>  'string|nullable',
                        'DataValue'  =>  'required',
                    ],
                    [
                        'Name.required'  =>  'Vui lòng nhập tên nhóm chức vụ.',
                        'Name.DataValue'  =>  'Vui lòng nhập mã nhóm chức vụ.',
                    ]
                );
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()->all()]);
                }
                $validated = $validator->validate();
                $groupDataKey = ListPosition::query()->withTrashed()
                    ->select('DataKey', 'TypeName')
                    ->groupBy('DataKey', 'TypeName')
                    ->orderBy('DataKey', 'desc')
                    ->get();
                $listIdGroupPosition = [];
                $listNameGroupPosition = [];
                foreach ($groupDataKey as $item) {
                    array_push($listIdGroupPosition, $item['DataKey']);
                    array_push($listNameGroupPosition, $item['TypeName']);
                }
                if (in_array($validated['Name'], $listNameGroupPosition)) {
                    return $this->jsonErrors('Tên nhóm chức vụ đã tồn tại');
                }
                if (in_array($validated['DataValue'], $listIdGroupPosition)) {
                    return $this->jsonErrors('Mã nhóm chức vụ đã tồn tại');
                }
                $one = new ListPosition();
                $one['DataKey'] = $validated['DataValue'];
                $one['Name'] = 'Nhóm ' . $validated['Name'];
                $one['TypeName'] = $validated['Name'];
                $one['DataValue'] = $validated['DataValue'] . '000';
                $one['Level'] ='0';
                $one['DataDescription'] = $validated['DataDescription'];
                // dd($one);
                $save = $one->save();
                if (!$save) {
                    return $this->jsonErrors('Lưu không thành công');
                } else {
                    return $this->jsonSuccessWithRouter('admin.ListPosition');
                }
            } else {
                return abort('404');
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function store(Request $request, $id = null)
    {

        try {
            if (count($request->input()) > 0) {
                if (array_key_exists('id', $request->input())) {
                    $validator = Validator::make(
                        $request->all(),
                        [
                            'Name'  =>  'required|string|max:1000',
                            'TypeName'  =>  'required|string|max:1000',
                            'DataKey'  =>  'required|string|max:1000',
                            'DataDescription'  =>  'string|nullable',
                            'GenderLevel'     => 'required',
                            'Level'  =>  'required|integer|min:1',
                            'oldLevel'  =>  'nullable',
                            'listUser'  =>  'required',
                            'countLevel'  =>  'nullable',
                            'ListUserOld'  =>  'nullable',
                            'DataValue'  =>  'nullable',
                            'id'    =>  'integer|min:1|nullable',
                        ],
                        [
                            'Name.required'  =>  'Vui lòng nhập tên chức vụ.',
                            'GenderLevel.required'     => 'Vui lòng nhập loại cấp độ lựa chọn',
                            'listUser.required'  =>  'Vui lòng chọn nhân viên.',
                        ]
                    );
                } else {
                    $validator = Validator::make(
                        $request->all(),
                        [
                            'Name'  =>  'required|string|max:1000',
                            'TypeName'  =>  'required|string|max:1000',
                            'DataKey'  =>  'required|string|max:1000',
                            'GenderLevel'     => 'nullable',
                            'Level'  =>  'required|integer|min:1',
                            'DataDescription'  =>  'string|nullable',
                            'listUser'  =>  'required',
                        ],
                        [
                            'Name.required'  =>  'Vui lòng nhập tên chức vụ.',
                            'GenderLevel.required'     => 'Vui lòng nhập loại cấp độ lựa chọn',
                            'listUser.required'  =>  'Vui lòng chọn nhân viên.',
                        ]
                    );
                }
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()->all()]);
                }
                $validated = $validator->validate();
                $groupDataKey = ListPosition::query()->withTrashed()
                    ->select('DataKey', 'Name', 'DataValue')
                    ->where('DataKey', $validated['DataKey'])
                    ->where('Name', $validated['Name']);
                if (($request->input())['DataValue']) {
                    $groupDataKey = $groupDataKey->where('DataValue', 'not like', '%' . ($request->input())['DataValue'] . '%');
                }
                $groupDataKey = $groupDataKey->first();
                if ($groupDataKey) {
                    return $this->jsonErrors('Tên chức vụ đã tồn tại');
                }
                if (array_key_exists('id', $validated)) {
                    $one = ListPosition::find($validated['id']);

                    $listDataPosition =  ListPosition::query()
                        ->where('DataKey', $validated['DataKey'])
                        ->orderBy('id', 'desc')->get();
                    if ($validated['oldLevel'] != $validated['Level']) {

                        if ($validated['GenderLevel'] == 0) {
                            $two = [];
                            $five = [];
                            if ($validated['Level'] < $validated['oldLevel']) {

                                foreach ($listDataPosition as $item) {
                                    if ($item->Level >= $validated['oldLevel'] && $item->id != $validated['id']) {
                                        $dataUpdate =  ['id' => $item->id, 'Level' => $item->Level];
                                        array_push($two, $dataUpdate);
                                        array_push($five, $item->Level);
                                    }
                                }

                                if (!in_array($validated['oldLevel'], $five)) {
                                    foreach ($two as $item) {

                                        $listPositionUpdateLevel = ListPosition::query()
                                            ->where('DataKey', $validated['DataKey'])
                                            ->orderBy('id', 'desc')->where('id', '=', $item['id'])->update(['Level' => ($item['Level'] - 1)]);
                                    }
                                }
                            } else {
                                foreach ($listDataPosition as $item) {
                                    if ($item->Level >= $validated['oldLevel'] && $item->id != $validated['id']) {
                                        $dataUpdate =  ['id' => $item->id, 'Level' => $item->Level];
                                        array_push($two, $dataUpdate);
                                        array_push($five, $item->Level);
                                    }
                                }
                                if (!in_array($validated['oldLevel'], $five)) {
                                    foreach ($two as $item) {

                                        $listPositionUpdateLevel = ListPosition::query()
                                            ->where('DataKey', $validated['DataKey'])
                                            ->orderBy('id', 'desc')->where('id', '=', $item['id'])->update(['Level' => ($item['Level'] - 1)]);
                                    }
                                    $validated['Level'] = $validated['Level'] - 1;
                                }
                            }
                        } else {
                            if ($validated['oldLevel'] > $validated['Level']) {
                                $two = [];
                                $five = [];
                                $seven = [];
                                foreach ($listDataPosition as $item) {
                                    if ($item->Level >= $validated['Level'] && $item->Level <= $validated['oldLevel'] && $item->id != $validated['id']) {
                                        $dataUpdate =  ['id' => $item->id, 'Level' => $item->Level];
                                        array_push($two, $dataUpdate);
                                        array_push($five, $item->Level);
                                    }
                                    if ($item->Level >= $validated['Level'] && $item->id != $validated['id']) {
                                        $dataUpdate =  ['id' => $item->id, 'Level' => $item->Level];
                                        array_push($seven, $dataUpdate);
                                    }
                                }
                                if (!in_array($validated['oldLevel'], $five)) {
                                    foreach ($two as $item) {

                                        $listPositionUpdateLevel = ListPosition::query()
                                            ->where('DataKey', $validated['DataKey'])
                                            ->orderBy('id', 'desc')->where('id', '=', $item['id'])->update(['Level' => ($item['Level'] + 1)]);
                                    }
                                } else {
                                    foreach ($seven as $item) {

                                        $listPositionUpdateLevel = ListPosition::query()
                                            ->where('DataKey', $validated['DataKey'])
                                            ->orderBy('id', 'desc')->where('id', '=', $item['id'])->update(['Level' => ($item['Level'] + 1)]);
                                    }
                                }
                            } else {

                                $two = [];
                                $five = [];
                                $seven = [];
                                foreach ($listDataPosition as $item) {
                                    if ($item->Level >= $validated['oldLevel'] && $item->Level <= $validated['Level'] && $item->id != $validated['id']) {
                                        $dataUpdate =  ['id' => $item->id, 'Level' => $item->Level];
                                        array_push($two, $dataUpdate);
                                        array_push($five, $item->Level);
                                    }
                                    if ($item->Level >= $validated['Level'] && $item->id != $validated['id']) {
                                        $dataUpdate =  ['id' => $item->id, 'Level' => $item->Level];
                                        array_push($seven, $dataUpdate);
                                    }
                                }

                                if (!in_array($validated['oldLevel'], $five)) {
                                    if ($validated['Level'] != $validated['countLevel'] + 1) {
                                        foreach ($two as $item) {
                                            $listPositionUpdateLevel = ListPosition::query()
                                                ->where('DataKey', $validated['DataKey'])
                                                ->orderBy('id', 'desc')->where('id', '=', $item['id'])->update(['Level' => ($item['Level'] - 1)]);
                                        }
                                    } else {

                                        foreach ($two as $item) {
                                            $listPositionUpdateLevel = ListPosition::query()
                                                ->where('DataKey', $validated['DataKey'])
                                                ->orderBy('id', 'desc')->where('id', '=', $item['id'])->update(['Level' => ($item['Level'] - 1)]);
                                        }
                                        $validated['Level'] = $validated['Level'] - 1;
                                    }
                                } else {
                                    if ($validated['Level'] != $validated['countLevel'] + 1) {
                                        foreach ($seven as $item) {
                                            $listPositionUpdateLevel = ListPosition::query()
                                                ->where('DataKey', $validated['DataKey'])
                                                ->orderBy('id', 'desc')->where('id', '=', $item['id'])->update(['Level' => ($item['Level'] + 1)]);
                                        }
                                    } else {
                                        foreach ($seven as $item) {
                                            $listPositionUpdateLevel = ListPosition::query()
                                                ->where('DataKey', $validated['DataKey'])
                                                ->orderBy('id', 'desc')->where('id', '=', $item['id'])->update(['Level' => ($item['Level'] + 1)]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $arrayOldListUser = explode(",", $validated['ListUserOld']);
                    if ($validated['listUser']) {
                        $arrayListUser = $validated['listUser'];
                        $arrayListUserRemove = [];
                        $arrayListUserAdd = [];
                        foreach ($arrayOldListUser as $item) {
                            if (!in_array($item, $arrayListUser)) {
                                array_push($arrayListUserRemove, $item);
                            }
                        };
                        if ($arrayListUserRemove) {
                            foreach ($arrayListUserRemove as $item) {
                                $saveListUserDataRemove = ListPositionUser::query()->where('UserId', '=', $item)->delete();
                            };
                        };
                        foreach ($arrayListUser as $item) {
                            if (!in_array($item, $arrayOldListUser)) {
                                array_push($arrayListUserAdd, $item);
                            }
                        };

                        if ($arrayListUserAdd) {
                            foreach ($arrayListUserAdd as $item) {
                                $saveListUserDataAdd = new ListPositionUser();
                                $saveListUserDataAdd['UserId'] = $item;
                                $saveListUserDataAdd['DataValue'] = $validated['DataValue'] ? $validated['DataValue'] : $one->DataValue;
                                $saveListUserDataAdd->save();
                            };
                        };
                    }
                } else {
                    $one = new ListPosition();
                    $listPosition = ListPosition::query()
                        ->where('DataKey', $validated['DataKey'])
                        ->orderBy('id', 'desc');
                    $listDataPosition =  $listPosition->get();
                    if ($validated['GenderLevel'] == 1) {
                        $two = [];
                        foreach ($listDataPosition as $item) {
                            if ($item->Level >= $validated['Level']) {
                                $dataUpdate =  ['id' => $item->id, 'Level' => $item->Level];
                                array_push($two, $dataUpdate);
                            }
                        }
                        foreach ($two as $item) {
                            $listPositionUpdateLevel = ListPosition::query()
                                ->where('DataKey', $validated['DataKey'])
                                ->orderBy('id', 'desc')->where('id', '=', $item['id'])->update(['Level' => ($item['Level'] + 1)]);
                        }
                    }
                    $masters = ListPosition::query()
                        ->where('DataKey', $validated['DataKey'])
                        ->orderBy('DataValue', 'desc')->first();
                    if ($masters) {
                        $stt = substr($masters->DataValue, 3);
                        $key = $masters['DataKey'] . '0';
                        $newStt = $stt + 1;
                        $one->DataValue = $key . substr("0000{$newStt}", -2);
                    } else {
                        $one->DataValue = $validated['DataKey'] . "001";
                    }
                    if ($validated['listUser']) {
                        foreach ($validated['listUser'] as $item) {
                            $saveListUserNew = new ListPositionUser();
                            $saveListUserNew['UserId'] = $item;
                            $saveListUserNew['DataValue'] = isset($validated['DataValue']) ? $validated['DataValue'] : $one->DataValue;
                            $saveListUserNew->save();
                        }
                    }
                }
                foreach ($validated as $key => $value) {
                    if (Schema::hasColumn('list_position', $key))
                        $one->$key = $value;
                }

                $save = $one->save();
                if (!$save) {
                    return $this->jsonErrors('Lưu không thành công');
                } else {
                    return $this->jsonSuccessWithRouter('admin.ListPosition');
                }
            } else {
                return abort('404');
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
