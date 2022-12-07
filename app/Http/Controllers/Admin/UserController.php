<?php

namespace App\Http\Controllers\Admin;

use App\CapicityProfile;
use App\DbLevel;
use App\DbSkill;
use App\Exports\UsersExport;
use App\ProgrammingLevel;
use App\ProgrammingSkill;
use App\RoleGroupScreenDetailRelationship;
use App\RoleScreen;
use App\RoleScreenDetail;
use App\RoleUserGroup;
use App\RoleUserScreenDetailRelationship;
use App\Room;
use App\TrainingHistory;
use App\User;
use App\UserGroup;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Nwidart\Modules\Laravel\Module;
use RdKafka;

/**
 * Class UserController
 * @package App\Http\Controllers\Admin
 * Screen list user
 */
class UserController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;
    protected $export;
    const KEYMENU = array(
        "add" => "UserListAdd",
        "view" => "UserList",
        "edit" => "UserListEdit",
        "delete" => "UserListDelete",
        "export" => "UserListExport",
    );

    /**
     * Check role view,insert,update
     * UserController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        if (strpos(\Request::getRequestUri(), 'api') === false) {
            parent::__construct($request);
            $this->middleware('auth');
        }
        $array = $this->RoleView('Users', ['UserList']);
        $this->data['menu'] = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if ($value == $row->alias)
                    $this->$key = $row;
            }
        }
    }

    /**
     * @param Request $request
     * @param string $view
     * @param string $orderBy
     * @param string $sortBy
     * @return View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Request $request, $view = 'default', $orderBy = 'username', $sortBy = 'asc')
    {
        $this->authorize('view', $this->menu);
        $recordPerPage = $this->getRecordPage();
        try {
            $users = $this->getDataWithCondition($request, $orderBy, $sortBy);
            $count = $users->count();
            //Pagination
            $users = $users->paginate($recordPerPage);
        } catch (Exception $e) {
            $users = User::query()
                ->where('id', 0);
            $count = $users->count();
            $users = $users->paginate($recordPerPage);

        }
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        $view = ($view == 'detail' ? 'detail' : 'default');

        parse_str(str_replace('?', '', $query_string), $query_array);

        //redirect to the last page if current page has no record
        if ($users->count() == 0) {
            if (array_key_exists('page', $query_array)) {
                if ($query_array['page'] > 1) {
                    $query_array['page'] = $users->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }
            }
        }

        $page = array_key_exists('page', $query_array) ? $query_array['page'] : '';
        $stt = $page ? $count - (($page - 1) * $recordPerPage) : $count;

        if ($sort == 'asc') {
            $stt = $page ? ($page - 1) * $recordPerPage : '';
        }

        $this->data['request'] = $request;
        $this->data['stt'] = $stt;
        $this->data['users'] = $users;
        $this->data['groups'] = UserGroup::query()->get();
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['sort'] = $sort;
        $this->data['view'] = $view;
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;
        $this->data['export'] = $this->export;
        return $this->viewAdminLayout('users', $this->data);
    }

    /**
     * @param $array
     * @param $orderBy
     * @param $sortBy
     * @param null $export
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getDataWithCondition($array, $orderBy, $sortBy, $export = null)
    {
        //Get list users left join with rooms
        if (Schema::hasColumn('users', $orderBy)) {
            $users = User::query()->select('users.*', 'rooms.Name')
                ->leftJoin('rooms', 'rooms.id', '=', 'users.RoomId');
            if (isset($array['Active']) && ($array['Active'] == 0 || $array['Active'] == 1)) {
                $users->where('users.Active', $array['Active']);
            }
            if (!isset($array['Active'])) {
                $users->where('users.Active', 1);
            }
            $users->orderBy('users.Active', 'DESC')->orderBy('users.' . $orderBy, $sortBy);
        } else {
            return redirect()->back();
        }
        //Search in columns
        $this->data['request'] = $array->query();
        $strSearch = trim($this->convert_vi_to_en($array->input('search')));
        if ($array->has('col') && $array->input('col') != '') {
            if (Schema::hasColumn('users', $array->input('col'))) {
                $users = $users->whereRaw('(DATE_FORMAT(users.' . $array->input('col') . ',"%d/%m/%Y")) LIKE ?', '%' . $strSearch . '%');
            }
        } else {
            $one = User::query()->select('users.FullName', 'users.username', 'users.Tel', 'users.email', 'users.email_user', 'users.SDate',
                'users.IDFM', 'users.OfficialDate', 'users.STimeOfDay', 'users.ETimeOfDay', 'users.Birthday', 'rooms.Name')
                ->join('rooms', 'rooms.id', '=', 'users.RoomId')
                ->first();
            if ($one) {
                $one = $one->toArray();
                if (array_key_exists('search', $array->input())) {
                    $users = $users->where(function ($query) use ($one, $array, $strSearch) {
                        foreach ($one as $key => $value) {
                            // echo $key;
                            if ($key == 'Name') {
                                $query->orWhereRaw('rooms.' . $key . ' LIKE ?', '%' . trim($strSearch) . '%');
                            } else {
                                if (in_array($key, ['Birthday', 'SDate', 'OfficialDate', 'STimeOfDay'])) {
                                    $query->orWhereRaw('(DATE_FORMAT(users.' . $key . ',"%d/%m/%Y")) LIKE ?', '%' . $strSearch . '%');
                                } else {
                                    $query->orWhere('users.' . $key, 'LIKE', '%' . $strSearch . '%');
                                }
                                if ($key == 'Birthday' && is_numeric($strSearch)) {
                                    $query->orWhereRaw('(DATE_FORMAT(FROM_DAYS(DATEDIFF( NOW( ), users.Birthday)), "%Y" ) + 0) LIKE BINARY ? ', '%' . $strSearch . '%');
                                }
                            }
                        }
                    });
                }
            }
        }

        $users = $users->where('role_group', '!=', 1);
        if ($export != '' || $export != null) {
            return $users->get();
        }
        return $users;
    }

    /**
     * @param Request $request
     * @param null $view
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request, $view = null)
    {

        $records = $this->getDataWithCondition($request, 'username', 'asc', 'export');

        if ($records->count() > 0) {
            return Excel::download(new UsersExport($records, $view), 'DanhSachNhanVien.xlsx');
        } else {
            return Redirect::back()->withErrors(['Không có dữ liệu!']);
        }
    }

    /**
     * Processing insert, update
     * @param Request $request
     * @param null $id
     * @return string|void
     */
    public function store(Request $request, $id = null)
    {
        if (count($request->input()) === 0) {
            return abort('404');
        }

        $check_user_name = User::onlyTrashed()
            ->where('username', $request->username)
            ->first();
        if ($check_user_name) {
            $check_user_name->restore();
            $request->request->add(['id' => $check_user_name->id]);
        }

        try {
            $arrCheck = [
                'FullName' => 'required|string|max:100',
                'Birthday' => 'required|date_format:d/m/Y',
                'Gender' => 'required|boolean',
                'MaritalStt' => 'required|boolean',
                'Tel' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/||min:10|max:12',
                'permission' => 'integer|min:1|max:2',
                'role_group' => 'integer|min:1|max:4',
                'RoomId' => 'required|min:1',
                'STimeOfDay' => 'required|date_format:H:i',
                'ETimeOfDay' => 'nullable|date_format:H:i',
                'Active' => 'required|boolean',
                'SDate' => 'date_format:d/m/Y|nullable',
                'expirationdate' => 'date_format:d/m/Y|nullable',
                'OfficialDate' => 'date_format:d/m/Y|nullable',
                'DaysOff' => 'date_format:d/m/Y|nullable',
                'PerAddress' => 'string|nullable',
                'CurAddress' => 'string|nullable',
                'Note' => 'string|nullable',
                'DepartmentId' => 'integer|min:1|nullable',
                'TimeRemain' => 'string|max:50|nullable',
                'RelativeName' => 'string|nullable',
                'Relationship' => 'string|nullable',
                'TelRelative' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:11|nullable',
                'GroupId' => 'integer|min:1|nullable',
                'CardNo2' => 'nullable|string|min:1|max:25',
                'workAt' => 'required|boolean',
                'SBreakOfDay' => 'required|date_format:H:i',
                'EBreakOfDay' => 'required|date_format:H:i',
            ];
            $modeIdUpdate = array_key_exists('id', $request->input());
            if ($modeIdUpdate) {
                $arrCheck['id'] = 'integer|min:1|nullable';
                $arrCheck['username'] = 'required|string|min:3|max:15|unique:users,username,' . $request['id'];
                $arrCheck['email'] = 'nullable|string|email|regex:/(.+)@akb\.(.+)/i|unique:users,email,' . $request['id'];
                $arrCheck['email_user'] = 'nullable|string|email|regex:/(.+)@(.+)\.(.+)/i|unique:users,email_user,' . $request['id'];
                $arrCheck['IDFM'] = 'required|unique:users,IDFM,' . $request['id'];
            } else {
                $arrCheck['username'] = 'required|string|min:3|max:15|unique:users';
                $arrCheck['password'] = 'required|string|min:6|required_with:password_confirmation';
                $arrCheck['password_confirmation'] = 'same:password';
                $arrCheck['email'] = 'nullable|string|email|regex:/(.+)@akb\.(.+)/i|unique:users,email';
                $arrCheck['email_user'] = 'nullable|string|email|regex:/(.+)@(.+)\.(.+)/i|unique:users,email_user';
                $arrCheck['IDFM'] = 'required|unique:users';
            }

            $validator = Validator::make($request->all(), $arrCheck);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()->first()], 400);
            }
            $validated = $validator->validate();
            $card_no2 = $validated["CardNo2"];
            if ($card_no2) {
                $user = User::query()
                    ->where("Active", self::USER_ACTIVE_FLAG)
                    ->where("CardNo2", $card_no2)
                    ->first();
                if ($user) {
                    if (isset($validated["id"])) {
                        if ($validated["id"] != $user->id) {
                            return response()->json(['errors' => 'Mã thẻ từ đã được sử dụng.'], 400);
                        }
                    } else {
                        return response()->json(['errors' => 'Mã thẻ từ đã được sử dụng.'], 400);
                    }
                }
            }
            $birthDay = $this->fncDateTimeConvertFomat($validated['Birthday'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
            if (Carbon::parse($birthDay)->age < 18) {
                return response()->json(['errors' => ['Ngày sinh không hợp lệ']]);
            }
            if ($this->compareDate($validated['SDate'], $validated['OfficialDate']) === false) {
                return response()->json(['errors' => ['Ngày làm chính thức không hợp lệ']]);
            }
            if ($this->compareDate($validated['Birthday'], $validated['SDate']) === false) {
                return response()->json(['errors' => ['Ngày vào công ty không hợp lệ']]);
            }
            if ($this->compareDate($validated['OfficialDate'], $validated['DaysOff']) === false) {
                return response()->json(['errors' => ['Ngày nghỉ việc không hợp lệ']]);
            }
            $user_name_old = "";
            $user_card_old = "";
            $user_active_old = "";
            if (array_key_exists('id', $validated)) {
                $this->authorize('action', $this->edit);
                $user = User::find($validated['id']);
                $user_name_old = $user->FullName;
                $user_card_old = $user->CardNo2;
                $user_active_old = $user->Active;
            } else {
                $this->authorize('action', $this->add);
                $user = new User();
            }

            foreach ($validated as $key => $value) {
                if (Schema::hasColumn('users', $key)) {
                    if ($key == 'Birthday') {
                        $value = $this->formatDateWithCol($value);
                    }
                    if ($key == 'SDate' && $value != '') {
                        $value = $this->formatDateWithCol($value);
                    }
                    if ($key == 'expirationdate' && $value != '') {
                        $value = $this->formatDateWithCol($value);
                    }
                    if ($key == 'OfficialDate' && $value != '') {
                        $value = $this->formatDateWithCol($value);
                    }
                    if ($key == 'DaysOff' && $value != '') {
                        $value = $this->formatDateWithCol($value);
                    }
                    $user->$key = $value;
                }
            }

            if (!array_key_exists('id', $validated)) {
                $user->password = Hash::make($validated['password']);
            }
//            $validated['permission'] != 1 ? $user->role_group = 3 : $user->role_group = 2;
            $user->save();
            $is_publish_kafka = function () use ($user_active_old, $user_card_old, $user_name_old, $user) {
                if ($user_active_old == "" && $user_card_old == "" && $user_name_old == "")
                    return true;
                elseif ($user_active_old != $user->Active || $user_name_old != $user->FullName
                    || $user_card_old != $user->CardNo2)
                    return true;
                else
                    return false;

            };
            if ($is_publish_kafka()) {
                $data = [
                    'UserID' => $user->id,
                    'Name' => $user->FullName,
                    'CardNo2' => $user->Active == self::USER_ACTIVE_FLAG ? $user->CardNo2 : null,
                    'Status' => $user->Active
                ];
                $this->_sendKafka($request, $data);
            }
            return $this->jsonSuccessWithRouter('admin.Users');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function storeApi(Request $request)
    {
        $this->authorize('action', RoleScreenDetail::query()->where('alias', 'UserListAdd')->first());

        if (count($request->input()) === 0) {
            return AdminController::responseApi(422, __('admin.error.data'));
        }

        $check_user_name = User::onlyTrashed()
            ->where('username', $request->username)
            ->first();
        if ($check_user_name) {
            $check_user_name->restore();
            $request->request->add(['id' => $check_user_name->id]);
        }

        try {
            $arrCheck = [
                'FullName' => 'required|string|max:100',
                'Birthday' => 'required|date_format:d/m/Y',
                'Gender' => 'required|boolean',
                'MaritalStt' => 'required|boolean',
                'Tel' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/||min:10|max:12',
                'permission' => 'integer|min:1|max:2',
                'role_group' => 'integer|min:1|max:4',
                'RoomId' => 'required|min:1',
                'STimeOfDay' => 'required|date_format:H:i',
                'ETimeOfDay' => 'nullable|date_format:H:i',
                'Active' => 'required|boolean',
                'SDate' => 'date_format:d/m/Y|nullable',
                'OfficialDate' => 'date_format:d/m/Y|nullable',
                'DaysOff' => 'date_format:d/m/Y|nullable',
                'PerAddress' => 'string|nullable',
                'CurAddress' => 'string|nullable',
                'Note' => 'string|nullable',
                'DepartmentId' => 'integer|min:1|nullable',
                'TimeRemain' => 'string|max:50|nullable',
                'RelativeName' => 'string|nullable',
                'Relationship' => 'string|nullable',
                'TelRelative' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:11|nullable',
                'GroupId' => 'integer|min:1|nullable',
                'CardNo2' => 'nullable|string|min:1|max:25',
                'workAt' => 'required|boolean',
                'SBreakOfDay' => 'required|date_format:H:i',
                'EBreakOfDay' => 'required|date_format:H:i',
            ];
            $modeIdUpdate = array_key_exists('id', $request->input());
            if ($modeIdUpdate) {
                $arrCheck['id'] = 'integer|min:1|nullable';
                $arrCheck['username'] = 'required|string|min:3|max:15|unique:users,username,' . $request['id'];
                $arrCheck['email'] = 'nullable|string|email|regex:/(.+)@akb\.(.+)/i|unique:users,email,' . $request['id'];
                $arrCheck['email_user'] = 'nullable|string|email|regex:/(.+)@(.+)\.(.+)/i|unique:users,email_user,' . $request['id'];
                $arrCheck['IDFM'] = 'required|unique:users,IDFM,' . $request['id'];
            } else {
                $arrCheck['username'] = 'required|string|min:3|max:15|unique:users';
                $arrCheck['password'] = 'required|string|min:6|required_with:password_confirmation';
                $arrCheck['password_confirmation'] = 'same:password';
                $arrCheck['email'] = 'nullable|string|email|regex:/(.+)@akb\.(.+)/i|unique:users,email';
                $arrCheck['email_user'] = 'nullable|string|email|regex:/(.+)@(.+)\.(.+)/i|unique:users,email_user';
                $arrCheck['IDFM'] = 'required|unique:users';
            }

            $validator = Validator::make($request->all(), $arrCheck);
            if ($validator->fails()) {
                return AdminController::responseApi(422, $validator->errors()->first());
            }

            $validated = $validator->validate();
            $birthDay = $this->fncDateTimeConvertFomat($validated['Birthday'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
            if (Carbon::parse($birthDay)->age < 18) {
                return AdminController::responseApi(422, __('admin.error.user.birthday'));
            }
            if ($this->compareDate($validated['SDate'], $validated['OfficialDate']) === false) {
                return AdminController::responseApi(422, __('admin.error.user.s-date'));
            }
            if ($this->compareDate($validated['Birthday'], $validated['SDate']) === false) {
                return AdminController::responseApi(422, __('admin.error.user.s-date'));
            }
            if ($this->compareDate($validated['OfficialDate'], $validated['DaysOff']) === false) {
                return AdminController::responseApi(422, __('admin.error.user.e-date'));
            }

            $user_name_old = "";
            $user_card_old = "";
            $user_active_old = "";
            if (array_key_exists('id', $validated)) {
                $user = User::find($validated['id']);
                $user_name_old = $user->FullName;
                $user_card_old = $user->CardNo2;
                $user_active_old = $user->Active;
            } else {
                $user = new User();
            }

            foreach ($validated as $key => $value) {
                if (Schema::hasColumn('users', $key)) {
                    if ($key == 'Birthday') {
                        $value = $this->formatDateWithCol($value);
                    }
                    if ($key == 'SDate' && $value != '') {
                        $value = $this->formatDateWithCol($value);
                    }
                    if ($key == 'OfficialDate' && $value != '') {
                        $value = $this->formatDateWithCol($value);
                    }
                    if ($key == 'DaysOff' && $value != '') {
                        $value = $this->formatDateWithCol($value);
                    }

                    $user->$key = $value;
                }
            }

            if (!array_key_exists('id', $validated)) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();
            $is_publish_kafka = function () use ($user_active_old, $user_card_old, $user_name_old, $user) {
                if ($user_active_old == "" && $user_card_old == "" && $user_name_old == "")
                    return true;
                elseif ($user_active_old != $user->Active || $user_name_old != $user->FullName
                    || $user_card_old != $user->CardNo2)
                    return true;
                else
                    return false;
            };
            if ($is_publish_kafka()) {
                $data = [
                    'UserID' => $user->id,
                    'Name' => $user->FullName,
                    'CardNo2' => $user->Active == self::USER_ACTIVE_FLAG ? $user->CardNo2 : null,
                    'Status' => $user->Active
                ];
                $this->_sendKafka($request, $data);
            }
            return AdminController::responseApi(200, null, __('admin.success.save'));
        } catch (\Exception $e) {
            return AdminController::responseApi(422, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param null $userId
     * @param null $del
     * @return View|string
     */
    public function showUser(Request $request, $userId = null, $del = null)
    {

        $this->data['userId'] = $userId;
        $this->data['rooms'] = Room::query()->select('id', 'Name')
            ->where('MeetingRoomFlag', '!=', 1)->where('Active', 1)->get();

        if ($request) {
            $userId = $request['id'];
            $del = $request['del'];
        }
        if ($userId != null) {
            if ($del == 'del') {
                $one = User::find($userId);
                if ($one != null) {
                    $one->CardNo2 = null;
                    $one->save();
                    $one->delete();
                    //return message api
                    $data = [
                        'UserID' => $one->id,
                        'Name' => null,
                        'CardNo2' => null,
                        'Status' => 0
                    ];
                    $this->_sendKafka($request, $data);
                    if (strpos(\Request::getRequestUri(), 'api') !== false) {
                        return response()->json(['success' => 'Xóa thành công.']);
                    }
                }
                return 1;
            }
            $this->data['userInfo'] = User::query()->select('users.*')
                ->where('users.id', $userId)->first();

            $this->data['groups'] = UserGroup::query()->get();
            $this->data['screens'] = RoleScreen::all();

            if ($this->data['userInfo']) {
                $query = RoleScreen::query()
                    ->select(
                        'role_screens.name as ScreenName',
                        'role_screens.alias as ScreenAlias',
                        'role_screen_details.name as ScreenDetailName',
                        'role_screen_details.alias as ScreenDetailAlias'
                    )
                    ->join(
                        'role_screen_details',
                        'role_screen_details.role_screen_alias',
                        'role_screens.alias'
                    )
                    ->orderBy('role_screen_details.role_screen_alias', 'asc');


                $this->data['listRole'] = $query->get();
                //kiểm tra xem role có được check hay không?
                foreach ($this->data['listRole'] as $item) {
                    $one = RoleUserScreenDetailRelationship::query()
                        ->where('user_id', $this->data['userInfo']->id)
                        ->where('screen_detail_alias', $item->ScreenDetailAlias)
                        ->first();
                    if ($one && $one->permission == 1) {
                        $item->checked = true;
                    } elseif ($one && $one->permission == 0) {
                        $item->checked = false;
                    } else {
                        $one = RoleGroupScreenDetailRelationship::query()
                            ->where('role_group_id', $this->data['userInfo']->role_group)
                            ->where('screen_detail_alias', $item->ScreenDetailAlias)
                            ->first();
                        if ($one) {
                            $item->checked = true;
                        } else {
                            $item->checked = false;
                        }
                    }
                }

                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                    return $this->data;
                }
                return $this->viewAdminIncludes('user-detail', $this->data);
            } else {
                return "test";
            }
        } else {

            $this->data['groups'] = UserGroup::query()->get();
            if (strpos(\Request::getRequestUri(), 'api') !== false) {
                return $this->data;
            }
            return $this->viewAdminIncludes('user-detail', $this->data);
        }
    }

    /**
     * change active when click checkbox
     * @param $id
     * @param $active
     */
    public function changeCheckboxActive($id, $active)
    {
        if ($id != '') {
            $user = User::find($id);
            $user->Active = $active;
            $user->save();
        }
    }

    /**
     * @param Request $request
     * @param null $id
     * @return View|void
     */
    public function showProfile(Request $request, $id = null)
    {
        if (is_null($id)) {
            $id = Auth::user()->id;
            $profile = CapicityProfile::query()
                ->where('UserID', $id)
                ->first();
        } else {
            $profile = CapicityProfile::query()
                ->where('UserID', $id)
                ->first();
        }

        if ($profile) {
            $this->data['profile'] = $profile;
        }


        $this->data['progSkills'] = ProgrammingSkill::query()
            ->where('Active', 1)
            ->get();
        foreach ($this->data['progSkills'] as $item) {
            $result = ProgrammingLevel::query()
                ->where('UserID', $id)
                ->where('ProgrammingSkillID', $item->id)
                ->first();
            if ($result) {
                $item->Level = $result->Level;
                $item->YearExp = $result->YearExp;
            }

        }
        $this->data['dbSkills'] = DbSkill::query()
            ->where('Active', 1)
            ->get();
        foreach ($this->data['dbSkills'] as $item) {
            $result = DbLevel::query()
                ->where('UserID', $id)
                ->where('DBSkillID', $item->id)
                ->first();
            if ($result) {
                $item->Level = $result->Level;
                $item->YearExp = $result->YearExp;
            }

        }
        $this->data['trainings'] = TrainingHistory::query()
            ->where('UserID', $id)
            ->get();
        $this->data['user'] = User::find($id);
        $this->data['roomName'] = User::query()->select('users.*', 'rooms.Name')
            ->leftJoin('rooms', 'rooms.id', '=', 'users.RoomId')
            ->where('users.id', $id)->get();
        $this->data['progSkillProfile'] = ProgrammingLevel::query()
            ->where('UserID', $id)
            ->get();
        $this->data['dbSkillProfile'] = DbLevel::query()
            ->where('UserID', $id)
            ->get();
        if (!$this->data['user']) {
            return abort('404');
        }
        return $this->viewAdminLayout('user-profile', $this->data);
    }

    /**
     * Process update info one user
     * @param Request $request
     * @param null $id
     * @return string|void
     */
    public function storeProfile(Request $request, $id = null)
    {
        try {
            DB::beginTransaction();
            if (count($request->input()) > 0) {
                if (!array_key_exists('blnAva', $request->input())) {
                    $validator = Validator::make($request->all(),
                        [
                            'id' => 'integer|min:1|nullable',
                            'FullName' => 'required|string|max:100',
                            'avatar' => 'nullable|string|max:200',
                            'email' => 'nullable|string|email|regex:/(.+)@akb\.(.+)/i|unique:users,email_user,' . $request['id'],
                            'email_user' => 'nullable|string|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email,' . $request['id'],
                            'Birthday' => 'required|date_format:d/m/Y|before:18 years ago',
                            'Gender' => 'nullable|boolean',
                            'MaritalStt' => 'nullable|boolean',
                            'SDate' => 'date_format:d/m/Y|nullable',
                            'OfficialDate' => 'date_format:d/m/Y|nullable',
                            'Tel' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:12',
                            'PerAddress' => 'string|nullable',
                            'CurAddress' => 'string|nullable',
                            'STimeOfDay' => 'date_format:H:i|nullable',
                            'Note' => 'string|nullable',
                            'RelativeName' => 'string|nullable',
                            'Relationship' => 'string|nullable',
                            'TelRelative' => 'string|min:10|max:12|nullable',
                            'Facebook' => 'string|nullable',
                            'Zalo' => 'string|nullable',
                            'Instagram' => 'string|nullable',
                            'SYear' => 'array',
                            'SYear.*' => 'date_format:d/m/Y|required',
                            'EYear' => 'array',
                            'EYear.*' => 'date_format:d/m/Y|nullable',
                            'Content' => 'array',
                            'Content.*' => 'string|required',
                            'LevelEN' => 'string|nullable',
                            'LevelJA' => 'string|nullable',
                            'YearExperience' => 'numeric|min:0|nullable',
                            'YearInJA' => 'numeric|min:0|nullable',
                            'CVFile' => 'string|nullable',
                            'CapacityOther' => 'string|nullable',
                            'Favorite' => 'string|nullable',
                            'NoteProfile' => 'string|nullable',
                            'progSkill' => 'array|nullable',
                            'progSkill.*' => 'array|nullable',
                            'progSkill.*.*' => 'numeric|min:0|nullable',
                            'dbSkill' => 'array|nullable',
                            'dbSkill.*' => 'array|nullable',
                            'dbSkill.*.*' => 'numeric|min:0|nullable',
                        ]);
                } else {
                    if (Auth::user()->role_group == 2) {
                        $user = User::find($request->input('id'));
                        if ($user) {
                            $parseUrl = parse_url($request->input('avatar'));
                            $user->avatar = $parseUrl['path'];
                            $user->save();
                        }
                        return 1;
                    }
                   	else {
                        return response()->json(['errors' => ['Bạn không có quyền thực hiện thao tác chỉnh sửa ảnh đại diện!']]);
                    }
                }
                if ($validator->fails()) {
                    return $this->jsonArrErrors($validator->errors()->all());
                }

                $validated = $validator->validate();

                // return $validated;
                $user = User::find($validated['id']);
                if (!$user) {
                    return response()->json(['errors' => ['Người dùng không tồn tại!']]);
                }
                if ($validated['id'] == Auth::user()->id || Auth::user()->role_group == 2) {
                    //$this->authorize('admin', $this->menu);
                    //check quyen sua nguoi dung khac o day
                    $check = true;
                } else {
                    $check = false;
                }
                if (!$check) {
                    return response()->json(['errors' => ['Bạn không có quyền thực hiện thao tác này!']]);
                }
                foreach ($validated as $key => $value) {
                    if (Schema::hasColumn('users', $key)) {
                        if ($key == 'Birthday') {
                            $value = $this->fncDateTimeConvertFomat($value, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                        }

                        $user->$key = $value;
                    }
                }

                $user->save();
                //update profile skill
                $profileSkill = CapicityProfile::query()
                    ->where('UserID', $validated['id'])
                    ->first();
                if (!$profileSkill) {
                    $profileSkill = new CapicityProfile();
                    $profileSkill->UserID = $validated['id'];
                }
                foreach ($validated as $key => $value) {
                    if (Schema::hasColumn('capicity_profiles', $key) && $key != 'id')
                        $profileSkill->$key = $value;
                    if ($key == 'NoteProfile') {
                        $profileSkill->Note = $value;
                    }
                }
                $profileSkill->save();

                ProgrammingLevel::query()
                    ->where('UserID', $validated['id'])
                    ->delete();

                DbLevel::query()
                    ->where('UserID', $validated['id'])
                    ->delete();
                //update lịch sử đào tạo
                TrainingHistory::query()
                    ->where('UserID', $validated['id'])
                    ->delete();
                try {
                    foreach ($validated['progSkill'] as $key => $item) {

                        if (!is_null($item[0]) || !is_null($item[1])) {
                            $one = new ProgrammingLevel();
                            $one->UserID = $validated['id'];
                            $one->ProgrammingSkillID = $key;
                            $one->Level = $item[0] + 0;
                            $one->YearExp = $item[1] + 0;
                            $one->save();
                        }
                    }
                    foreach ($validated['dbSkill'] as $key => $item) {
                        if (!is_null($item[0]) || !is_null($item[1])) {
                            $one = new DbLevel();
                            $one->UserID = $validated['id'];
                            $one->DBSkillID = $key;
                            $one->Level = $item[0] + 0;
                            $one->YearExp = $item[1] + 0;
                            $one->save();
                        }
                    }

                    foreach ($validated['SYear'] as $key => $item) {
                        $one = new TrainingHistory();
                        $one->UserID = $validated['id'];
                        $one->SYear = $this->formatDateWithCol($validated['SYear'][$key]);
                        $one->EYear = $this->formatDateWithCol($validated['EYear'][$key]);
                        $one->Content = $validated['Content'][$key];
                        $one->save();
                    }
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }

                if (strpos(\Request::getRequestUri(), 'api') !== false) {
                    return response()->json(['success' => 'Lưu thành công.']);
                }
            } else {
                return abort('404');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function saveProfile(Request $request, $id = null)
    {
        try {
            DB::beginTransaction();
            if (count($request->input()) > 0) {
                if (!array_key_exists('blnAva', $request->input())) {
                    $validator = Validator::make($request->all(),
                        [
                            'id' => 'integer|min:1|nullable',
                            'FullName' => 'required|string|max:100',
                            'avatar' => 'nullable|string|max:200',
                            'email' => 'nullable|string|email|regex:/(.+)@akb\.(.+)/i|unique:users,email,' . $request['id'],
                            'email_user' => 'nullable|string|email|regex:/(.+)@(.+)\.(.+)/i|unique:users,email_user,' . $request['id'],
                            'Birthday' => 'required|date_format:d/m/Y|before:18 years ago',
                            'Gender' => 'nullable|boolean',
                            'MaritalStt' => 'nullable|boolean',
                            'SDate' => 'date_format:d/m/Y|nullable',
                            'OfficialDate' => 'date_format:d/m/Y|nullable',
                            'Tel' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:12',
                            'PerAddress' => 'string|nullable',
                            'CurAddress' => 'string|nullable',
                            'STimeOfDay' => 'date_format:H:i|nullable',
                            'Note' => 'string|nullable',
                            'RelativeName' => 'string|nullable',
                            'Relationship' => 'string|nullable',
                            'TelRelative' => 'string|min:10|max:12|nullable',
                            'Facebook' => 'string|nullable',
                            'Zalo' => 'string|nullable',
                            'Instagram' => 'string|nullable',
                        ]);
                }
                if ($validator->fails()) {
                    return $this->jsonArrErrors($validator->errors()->all());
                }

                $validated = $validator->validate();

                // return $validated;
                $user = User::find($validated['id']);
                if (!$user) {
                    return response()->json(['errors' => ['Người dùng không tồn tại!']]);
                }
                if ($validated['id'] != Auth::user()->id) {
                    //check quyen sua nguoi dung khac o day
                    $check = false;
                } else {
                    $check = true;
                }
                if (!$check) {
                    return response()->json(['errors' => ['Bạn không có quyền thực hiện thao tác này!']]);
                }
                foreach ($validated as $key => $value) {
                    if (Schema::hasColumn('users', $key)) {
                        if ($key == 'Birthday') {
                            $value = $this->fncDateTimeConvertFomat($value, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                        }

                        $user->$key = $value;
                    }
                }

                $user->save();
                return $this->jsonSuccess(__('admin.success.save'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function saveCapicityProfile(Request $request, $id = null)
    {
        try {
            DB::beginTransaction();
            if (count($request->input()) > 0) {
                if (!array_key_exists('blnAva', $request->input())) {
                    $validator = Validator::make($request->all(),
                        [
                            'id' => 'integer|min:1|nullable',
                            'SYear' => 'array',
                            'SYear.*' => 'date_format:d/m/Y|required',
                            'EYear' => 'array',
                            'EYear.*' => 'date_format:d/m/Y|nullable',
                            'Content' => 'array',
                            'Content.*' => 'string|required',
                            'LevelEN' => 'string|nullable',
                            'LevelJA' => 'string|nullable',
                            'YearExperience' => 'numeric|min:0|nullable',
                            'YearInJA' => 'numeric|min:0|nullable',
                            'CVFile' => 'string|nullable',
                            'CapacityOther' => 'string|nullable',
                            'Favorite' => 'string|nullable',
                            'NoteProfile' => 'string|nullable',
                            'progSkill' => 'array|nullable',
                            'progSkill.*' => 'array|nullable',
                            'progSkill.*.*' => 'numeric|min:0|nullable',
                            'dbSkill' => 'array|nullable',
                            'dbSkill.*' => 'array|nullable',
                            'dbSkill.*.*' => 'numeric|min:0|nullable',
                        ]);
                }
                if ($validator->fails()) {
                    return $this->jsonArrErrors($validator->errors()->all());
                }

                $validated = $validator->validate();

                // return $validated;
                $user = User::find($validated['id']);
                if (!$user) {
                    return response()->json(['errors' => ['Người dùng không tồn tại!']]);
                }
                if ($validated['id'] != Auth::user()->id) {
                    //check quyen sua nguoi dung khac o day
                    $check = false;
                } else {
                    $check = true;
                }
                if (!$check) {
                    return response()->json(['errors' => ['Bạn không có quyền thực hiện thao tác này!']]);
                }
                //update profile skill
                $profileSkill = CapicityProfile::query()
                    ->where('UserID', $validated['id'])
                    ->first();
                if (!$profileSkill) {
                    $profileSkill = new CapicityProfile();
                    $profileSkill->UserID = $validated['id'];
                }
                foreach ($validated as $key => $value) {
                    if (Schema::hasColumn('capicity_profiles', $key) && $key != 'id')
                        $profileSkill->$key = $value;
                    if ($key == 'NoteProfile') {
                        $profileSkill->Note = $value;
                    }
                }
                $profileSkill->save();

                ProgrammingLevel::query()
                    ->where('UserID', $validated['id'])
                    ->delete();

                DbLevel::query()
                    ->where('UserID', $validated['id'])
                    ->delete();
                //update lịch sử đào tạo
                TrainingHistory::query()
                    ->where('UserID', $validated['id'])
                    ->delete();
                try {
                    foreach ($validated['progSkill'] as $key => $item) {

                        if (!is_null($item[0]) || !is_null($item[1])) {
                            $one = new ProgrammingLevel();
                            $one->UserID = $validated['id'];
                            $one->ProgrammingSkillID = $key;
                            $one->Level = $item[0] + 0;
                            $one->YearExp = $item[1] + 0;
                            $one->save();
                        }
                    }
                    foreach ($validated['dbSkill'] as $key => $item) {
                        if (!is_null($item[0]) || !is_null($item[1])) {
                            $one = new DbLevel();
                            $one->UserID = $validated['id'];
                            $one->DBSkillID = $key;
                            $one->Level = $item[0] + 0;
                            $one->YearExp = $item[1] + 0;
                            $one->save();
                        }
                    }

                    foreach ($validated['SYear'] as $key => $item) {
                        $one = new TrainingHistory();
                        $one->UserID = $validated['id'];
                        $one->SYear = $this->formatDateWithCol($validated['SYear'][$key]);
                        $one->EYear = $this->formatDateWithCol($validated['EYear'][$key]);
                        $one->Content = $validated['Content'][$key];
                        $one->save();
                    }

                } catch (\Exception $e) {
                    if (strpos(\Request::getRequestUri(), 'api') !== false) {
                        return response()->json(['success' => 'Lưu thành công.']);
                    }
                    echo $e->getMessage();
                }

            } else {
                return abort('404');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    /**
     * @return View
     */
    public function viewLayoutChangePassword()
    {
        $this->data['user'] = Auth::user()->id;
        return $this->viewAdminLayout('change-password', $this->data);
    }

    /**
     * Process change password
     * @param Request $request
     * @return string|void
     * Edit Password of user
     */
    public function changePassword(Request $request)
    {
        if (count($request->input()) === 0) {
            return abort('404');
        }

        try {
            if (array_key_exists('oldPassword', $request->input())) {
                $arrayCheck['id'] = 'integer|min:1|nullable';
                $arrayCheck['oldPassword'] = 'required|string|max:30|min:6';
                $arrayCheck['new_password'] = 'required|string|confirmed|different:oldPassword|min:8';
            } else {
                $arrayCheck['id'] = 'integer|min:1|nullable';
                $arrayCheck['new_password'] = 'required|string|confirmed|min:8';
            }
            $messages = [
                'new_password.confirmed' => 'Mật khẩu xác nhận không đúng.',
                'new_password.required' => 'Chưa điền mật khẩu mới.',
            ];
            $validator = Validator::make($request->all(), $arrayCheck, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()->first()]);
            }

            $validated = $validator->validate();
            $user = User::find($validated['id']);

            if (!$request->has('oldPassword')) {
                $user->fill(['password' => Hash::make($request['new_password'])])->save();
                return $this->jsonSuccess('Đổi mật khẩu thành công');
            }
            if (Hash::check($request['oldPassword'], $user['password'])) {
                $user->fill(['password' => Hash::make($request['new_password'])])->save();
                \Session::forget('checkPass');
                return $this->jsonSuccess('Đổi mật khẩu thành công');
            } else {
                return $this->jsonErrors('Mật khẩu hiện tại không đúng');
            }

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // API

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function showShortProfileApi()
    {
        $data = array();
        $data['id'] = Auth::user()->id;
        $data['avatar'] = Auth::user()->avatar;
        $data['FullName'] = Auth::user()->FullName;
        $group = RoleUserGroup::find(Auth::user()->role_group);
        $data['role_group'] = isset($group) && $group != null ? $group->name : '';
        return AdminController::responseApi(200, null, null, $data);
    }

    /**
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showProfileApi($id = null)
    {
//        $this->authorize('action', RoleScreenDetail::query()->where('alias', 'ProfileSkill')->first());

        $data = array();
        $data['user'] = is_null($id) ? Auth::user() : User::find($id);
        $id = is_null($id) ? Auth::user()->id : $id;
        $data['profile'] = CapicityProfile::query()->where('UserID', $id)->first();
        $data['progSkills'] = ProgrammingSkill::query()->where('Active', 1)->get();
        foreach ($data['progSkills'] as $item) {
            $result = ProgrammingLevel::query()->where('UserID', $id)->where('ProgrammingSkillID', $item->id)->first();
            if ($result) {
                $item->Level = $result->Level;
                $item->YearExp = $result->YearExp;
            }
        }
        $data['dbSkills'] = DbSkill::query()->where('Active', 1)->get();
        foreach ($data['dbSkills'] as $item) {
            $result = DbLevel::query()->where('UserID', $id)->where('DBSkillID', $item->id)->first();
            if ($result) {
                $item->Level = $result->Level;
                $item->YearExp = $result->YearExp;
            }
        }
        $data['trainings'] = TrainingHistory::query()->where('UserID', $id)->get();
        $data['roomName'] = User::query()->select('users.*', 'rooms.Name')->leftJoin('rooms', 'rooms.id', '=', 'users.RoomId')->where('users.id', $id)->get();
        $data['progSkillProfile'] = ProgrammingLevel::query()->where('UserID', $id)->get();
        $data['dbSkillProfile'] = DbLevel::query()->where('UserID', $id)->get();
//        $data['role_key'] = 'ProfileSkill';
        if (!$data['user']) {
            return AdminController::responseApi(404, __('admin.error.user-missing'));
        }
        return AdminController::responseApi(200, null, null, $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveProfileApi(Request $request)
    {
//        $this->authorize('action', RoleScreenDetail::query()->where('alias', 'ProfileSkillEdit')->first());
        try {
            DB::beginTransaction();
            if (count($request->input()) > 0) {
                if (!array_key_exists('blnAva', $request->input())) {
                    $validator = Validator::make($request->all(),
                        [
                            'FullName' => 'required|string|max:100',
                            'avatar' => 'nullable|string|max:200',
                            'email' => 'nullable|string|email|regex:/(.+)@akb\.(.+)/i|unique:users,email,' . $request['id'],
                            'email_user' => 'nullable|string|email|regex:/(.+)@(.+)\.(.+)/i|unique:users,email_user,' . $request['id'],
                            'Birthday' => 'required|date_format:d/m/Y|before:18 years ago',
                            'Gender' => 'nullable|boolean',
                            'MaritalStt' => 'nullable|boolean',
                            'SDate' => 'date_format:d/m/Y|nullable',
                            'OfficialDate' => 'date_format:d/m/Y|nullable',
                            'Tel' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:12',
                            'PerAddress' => 'string|nullable',
                            'CurAddress' => 'string|nullable',
                            'STimeOfDay' => 'date_format:H:i|nullable',
                            'Note' => 'string|nullable',
                            'RelativeName' => 'string|nullable',
                            'Relationship' => 'string|nullable',
                            'TelRelative' => 'string|min:10|max:12|nullable',
                            'Facebook' => 'string|nullable',
                            'Zalo' => 'string|nullable',
                            'Instagram' => 'string|nullable',
                        ]);
                }
                if ($validator->fails()) {
                    return AdminController::responseApi(422, $validator->errors()->first());
                }
                $validated = $validator->validate();

                $user = Auth::user();
                foreach ($validated as $key => $value) {
                    if (Schema::hasColumn('users', $key)) {
                        if ($key == 'Birthday') {
                            $value = $this->fncDateTimeConvertFomat($value, self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
                        }
                        $user->$key = $value;
                    }
                }
                $user->save();

                return AdminController::responseApi(200, null, __('admin.success.save'));
            } else {
                return AdminController::responseApi(422, __('admin.error.data-missing'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return AdminController::responseApi(422, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param string $view
     * @param string $orderBy
     * @param string $sortBy
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showApi(Request $request, $view = 'default', $orderBy = 'username', $sortBy = 'asc')
    {
        $data = array();
        $this->authorize('action', RoleScreenDetail::query()->where('alias', 'UserList')->first());

        $data['request'] = $request;
        $data['groups'] = UserGroup::query()->get();
        // $data['users'] = User::query()
        //     ->select('users.*', 'rooms.Name')
        //     ->leftJoin('rooms', 'users.RoomId', '=', 'rooms.id')
        //     ->where('role_group', '!=', '1')->get();
        $data['users'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        $data['role_key'] = 'UserList';
        $data['rooms'] = Room::query()->where('Active', 1)->get();
        return AdminController::responseApi(200, null, null, $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateApi(Request $request, $id)
    {
        $this->authorize('action', RoleScreenDetail::query()->where('alias', 'UserListEdit')->first());

        if (count($request->input()) === 0 || !$id) {
            return AdminController::responseApi(422, __('admin.error.data'));
        }

        try {
            $arrCheck = [
                'FullName' => 'required|string|max:100',
                'Birthday' => 'required|date_format:d/m/Y',
                'Gender' => 'required|boolean',
                'MaritalStt' => 'required|boolean',
                'Tel' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/||min:10|max:12',
                'permission' => 'integer|min:1|max:2',
                'role_group' => 'integer|min:1|max:4',
                'RoomId' => 'required|min:1',
                'STimeOfDay' => 'required|date_format:H:i',
                'ETimeOfDay' => 'nullable|date_format:H:i',
                'Active' => 'required|boolean',
                'SDate' => 'date_format:d/m/Y|nullable',
                'OfficialDate' => 'date_format:d/m/Y|nullable',
                'DaysOff' => 'date_format:d/m/Y|nullable',
                'PerAddress' => 'string|nullable',
                'CurAddress' => 'string|nullable',
                'Note' => 'string|nullable',
                'DepartmentId' => 'integer|min:1|nullable',
                'TimeRemain' => 'string|max:50|nullable',
                'RelativeName' => 'string|nullable',
                'Relationship' => 'string|nullable',
                'TelRelative' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:11|nullable',
                'GroupId' => 'integer|min:1|nullable',
                'id' => 'integer|min:1|nullable',
                'username' => 'required|string|min:3|max:15|unique:users,username,' . $request['id'],
                'email' => 'nullable|string|email|regex:/(.+)@akb\.(.+)/i|unique:users,email,' . $request['id'],
                'email_user' => 'nullable|string|email|regex:/(.+)@(.+)\.(.+)/i|unique:users,email_user,' . $request['id'],
                'IDFM' => 'required|unique:users,IDFM,' . $request['id']
            ];

            $validator = Validator::make($request->all(), $arrCheck);
            if ($validator->fails()) {
                return AdminController::responseApi(422, $validator->errors()->first());
            }

            $validated = $validator->validate();
            $birthDay = $this->fncDateTimeConvertFomat($validated['Birthday'], self::FOMAT_DISPLAY_DMY, self::FOMAT_DB_YMD);
            if (Carbon::parse($birthDay)->age < 18) {
                return AdminController::responseApi(422, __('admin.error.user.birthday'));
            }
            if ($this->compareDate($validated['SDate'], $validated['OfficialDate']) === false) {
                return AdminController::responseApi(422, __('admin.error.user.official-date'));
            }
            if ($this->compareDate($validated['Birthday'], $validated['SDate']) === false) {
                return AdminController::responseApi(422, __('admin.error.user.s-date'));
            }
            if ($this->compareDate($validated['OfficialDate'], $validated['DaysOff']) === false) {
                return AdminController::responseApi(422, __('admin.error.user.e-date'));
            }

            $user = User::find($validated['id']);

            foreach ($validated as $key => $value) {
                if (Schema::hasColumn('users', $key)) {
                    if ($key == 'Birthday') {
                        $value = $this->formatDateWithCol($value);
                    }
                    if ($key == 'SDate' && $value != '') {
                        $value = $this->formatDateWithCol($value);
                    }
                    if ($key == 'OfficialDate' && $value != '') {
                        $value = $this->formatDateWithCol($value);
                    }
                    if ($key == 'DaysOff' && $value != '') {
                        $value = $this->formatDateWithCol($value);
                    }
                    $user->$key = $value;
                }
            }

            $user->save();

            return AdminController::responseApi(422, null, __('admin.success.save'));
        } catch (\Exception $e) {
            return AdminController::responseApi(422, $e->getMessage());
        }
    }

    /**
     * $api /akb/user_upload_avatar
     * @function updateImageApi
     * @param Request $request
     * @param $id
     * @return json_success
     * @throws Exception
     */
    public function updateImageApi(Request $request, $id)
    {
        if (!$id) {
            return AdminController::responseApi(404, __('admin.error.user-missing'));
        }
        
        if (Auth::user()->role_group != 2) {
            return AdminController::responseApi(404, __('Bạn không có quyền thực hiện thao tác chỉnh sửa ảnh đại diện!'));
        }

        $path = public_path() . '\storage\app\public\photos';

        $files = File::directories($path);
        $nameFolder = [];
        foreach ($files as $file) {
            $nameFolder[] = basename($file);
        }
        $path = $path . '\\' . $id;
        $pathThumb = $path . '\thumbs';

        //if not find folder, create folder new and save in folder
        if (!in_array($id, $nameFolder)) {
            File::makeDirectory($path);
            File::makeDirectory($path . '\thumbs');
        }

        $datetime = new \DateTime();
        $real_name = $datetime->format('YmdHis') . '_' . $request['avatar']->getClientOriginalName();
        $request['avatar']->move($path, $real_name);

        copy($path . '\\' . $real_name, $pathThumb . '\\' . $real_name);

        // save path image to database
        $user = User::find($request['id']);
        if ($user) {
            $user->avatar = '.\storage\app\public\photos' . '\\' . $id . '\\' . $real_name;
            $user->save();
        }
        return AdminController::responseApi(200, null, __('admin.success.save'));
    }

    /**
     * @param Request $request
     * @param null $id
     * @return json_success
     */
    public function saveCapicityProfileApi(Request $request, $id = null)
    {
        try {
            DB::beginTransaction();
            if (count($request->input()) > 0) {
                if (!array_key_exists('blnAva', $request->input())) {
                    $validator = Validator::make($request->all(),
                        [
                            'SYear' => 'array',
                            'SYear.*' => 'date_format:d/m/Y|required',
                            'EYear' => 'array',
                            'EYear.*' => 'date_format:d/m/Y|nullable',
                            'Content' => 'array',
                            'Content.*' => 'string|required',
                            'LevelEN' => 'string|nullable',
                            'LevelJA' => 'string|nullable',
                            'YearExperience' => 'numeric|min:0|nullable',
                            'YearInJA' => 'numeric|min:0|nullable',
                            'CVFile' => 'string|nullable',
                            'CapacityOther' => 'string|nullable',
                            'Favorite' => 'string|nullable',
                            'NoteProfile' => 'string|nullable',
                            'progSkill' => 'array|nullable',
                            'progSkill.*' => 'array|nullable',
                            'progSkill.*.*' => 'numeric|min:0|nullable',
                            'dbSkill' => 'array|nullable',
                            'dbSkill.*' => 'array|nullable',
                            'dbSkill.*.*' => 'numeric|min:0|nullable',
                        ]);
                }
                if ($validator->fails()) {
                    return response()->json(['error' => $validator->errors()->first()], 422);
                }

                $validated = $validator->validate();

                //update profile skill
                $profileSkill = CapicityProfile::query()->where('UserID', Auth::user()->id)->first();
                if (!$profileSkill) {
                    $profileSkill = new CapicityProfile();
                    $profileSkill->UserID = Auth::user()->id;
                }
                foreach ($validated as $key => $value) {
                    if (Schema::hasColumn('capicity_profiles', $key) && $key != 'id')
                        $profileSkill->$key = $value;
                    if ($key == 'NoteProfile') {
                        $profileSkill->Note = $value;
                    }
                }

                ProgrammingLevel::query()->where('UserID', Auth::user()->id)->delete();
                DbLevel::query()->where('UserID', Auth::user()->id)->delete();
                TrainingHistory::query()->where('UserID', Auth::user()->id)->delete();

                try {
                    if (isset($validated['progSkill'])) {
                        foreach ($validated['progSkill'] as $key => $item) {
                            if (!is_null($item[0]) || !is_null($item[1])) {
                                $one = new ProgrammingLevel();
                                $one->UserID = Auth::user()->id;
                                $one->ProgrammingSkillID = $key;
                                $one->Level = $item[0] + 0;
                                $one->YearExp = $item[1] + 0;
                                $one->save();
                            }
                        }
                    }
                    if (isset($validated['dbSkill'])) {
                        foreach ($validated['dbSkill'] as $key => $item) {
                            if (!is_null($item[0]) || !is_null($item[1])) {
                                $one = new DbLevel();
                                $one->UserID = Auth::user()->id;
                                $one->DBSkillID = $key;
                                $one->Level = $item[0] + 0;
                                $one->YearExp = $item[1] + 0;
                                $one->save();
                            }
                        }
                    }

                    if (isset($validated['SYear'])) {
                        foreach ($validated['SYear'] as $key => $item) {
                            $one = new TrainingHistory();
                            $one->UserID = Auth::user()->id;
                            $one->SYear = $this->formatDateWithCol($validated['SYear'][$key]);
                            $one->EYear = $this->formatDateWithCol($validated['EYear'][$key]);
                            $one->Content = $validated['Content'][$key];
                            $one->save();
                        }
                    }
                    $profileSkill->save();
                } catch (\Exception $e) {
                    return AdminController::responseApi(422, $e->getMessage());
                }
                return AdminController::responseApi(200, null, __('admin.success.save'));
            } else {
                return AdminController::responseApi(422, __('admin.error.data-missing'));
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return AdminController::responseApi(422, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return json_success
     */
    public function changePasswordApi(Request $request)
    {
        if (count($request->input()) === 0) {
            return AdminController::responseApi(422, __('admin.error.data-missing'));
        }

        try {
            if (array_key_exists('oldPassword', $request->input())) {
                $arrayCheck['id'] = 'integer|min:1|nullable';
                $arrayCheck['oldPassword'] = 'required|string|max:30|min:6';
                $arrayCheck['new_password'] = 'required|string|confirmed|different:oldPassword|min:6';
            } else {
                $arrayCheck['id'] = 'integer|min:1|nullable';
                $arrayCheck['new_password'] = 'required|string|confirmed|min:6';
            }
            $messages = [
                'new_password.confirmed' => 'Mật khẩu xác nhận không đúng.',
                'new_password.required' => 'Chưa điền mật khẩu mới.',
            ];
            $validator = Validator::make($request->all(), $arrayCheck, $messages);
            if ($validator->fails()) {
                return AdminController::responseApi(422, $validator->errors()->first());
            }

            $validated = $validator->validate();
            $user = User::find($validated['id']);

            if (!$request->has('oldPassword')) {
                $user->fill(['password' => Hash::make($request['new_password'])])->save();
                return AdminController::responseApi(200, null, __('admin.success.password'));
            }

            if (Hash::check($request['oldPassword'], $user['password'])) {
                $user->fill(['password' => Hash::make($request['new_password'])])->save();
                return AdminController::responseApi(200, null, __('admin.success.password'));
            } else {
                return AdminController::responseApi(422, __('admin.error.user.old-pass'));
            }
        } catch (\Exception $e) {
            return AdminController::responseApi(422, $e->getMessage());
        }
    }

    private function _sendKafka(Request $request, $data)
    {
        $conf = new RdKafka\Conf();
        $conf->set('metadata.broker.list', self::KAFKA_HOST);
        $conf->set('enable.idempotence', 'true');
        $producer = new RdKafka\Producer($conf);
        $topic = $producer->newTopic(self::KAFKA_TOPIC);
        try {
            $topic->produce(RD_KAFKA_PARTITION_UA, 0, json_encode($data), 'Insert/Update');
            if (strpos($request->getRequestUri(), 'api') === false) {
                $request->session()->flash('alert', "Đồng bộ dữ liệu PI thành công!");
            }
            $producer->flush(10000);
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}
