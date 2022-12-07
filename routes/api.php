<?php

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\ProjectManager\Http\Controllers\ApiTaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/loginApi', 'Api\ApiLoginController@login')->middleware('language');
Route::post('/signupApi', 'Api\ApiLoginController@signup')->middleware('language');
Route::post('/checkin', 'Api\ApiLoginController@checkin')->middleware('language');
// Route::group(['middleware' => ['auth:api', 'language']], function() {
//     Route::get('/logoutApi', 'Api\ApiLoginController@logout');

//     // menus
//     Route::get('/akb/menu_all', 'Admin\MenuController@getAllMenuApi');
//     Route::get('/akb/menu_user', 'Admin\MenuController@getMenuApi');
//     Route::get('/akb/menu', 'Admin\MenuController@menuList');

//     //user
//     Route::get('/akb/short_user_info', 'Admin\UserController@showShortProfileApi');
//     Route::get('/akb/user_info/{id?}', 'Admin\UserController@showProfileApi');
//     Route::post('/akb/user_update_profile', 'Admin\UserController@saveProfileApi');
//     Route::get('/akb/user_list/{view?}/{orderBy?}/{sortBy?}', 'Admin\UserController@showApi');
//     Route::post('/akb/user_insert', 'Admin\UserController@storeApi');
//     Route::post('/akb/user_update/{id?}', 'Admin\UserController@updateApi');
//     Route::post('/akb/user_upload_avatar/{id?}', 'Admin\UserController@updateImageApi');
//     Route::post('/akb/saveCapicityProfile/{id?}', 'Admin\UserController@saveCapicityProfileApi');
//     Route::post('/akb/change_password/{id?}', 'Admin\UserController@changePasswordApi');

//     //daily_report
//     Route::get('/akb/daily_list', 'Admin\DailyReportController@indexApi');
//     Route::get('/akb/daily_view_insert/{id?}', 'Admin\DailyReportController@showDetailOneApi');
//     Route::post('/akb/daily_insert', 'Admin\DailyReportController@storeApi');
//     Route::post('/akb/daily_update/{id?}', 'Admin\DailyReportController@updateApi');
//     Route::get('/akb/daily_delete/{id?}/{del?}', 'Admin\DailyReportController@deleteApi');

//     //room
//     Route::get('/akb/room_list/{orderBy?}/{sortBy?}', 'Admin\RoomController@showApi');

//     //absences
//     Route::get('/akb/absence_list/{orderBy?}/{sortBy?}', 'Admin\Absence\AbsenceController@showApi');
//     Route::get('/akb/absence_view_insert', 'Admin\Absence\AbsenceController@showDetailApi');
//     Route::post('/akb/absence_insert', 'Admin\Absence\AbsenceController@storeApi');
//     Route::post('/akb/absence_update/{id?}', 'Admin\Absence\AbsenceController@updateApi');
//     Route::get('/akb/absence_delete/{id?}', 'Admin\Absence\AbsenceController@deleteApi');
//     Route::get('/akb/absence_list_approved', 'Admin\Absence\AbsenceController@showListApproveApi');
//     Route::get('/akb/absence_count_list_approved', 'Admin\Absence\AbsenceController@countListApproveApi');
//     Route::get('/akb/absence_approve/{id?}', 'Admin\Absence\AbsenceController@apprAbsenceApi');
//     Route::get('/akb/absence_unApprove/{id?}', 'Admin\Absence\AbsenceController@unApprAbsenceApi');
//     Route::get('/akb/absence_list_of_user/{orderBy?}/{sortBy?}', 'Admin\Absence\AbsenceController@showAbsenceManagementApi');
//     Route::post('/akb/absence_management_insert', 'Admin\Absence\AbsenceController@storeManagementApi');
//     Route::post('/akb/absence_management_update/{id?}', 'Admin\Absence\AbsenceController@updateManagementApi');
//     Route::get('/akb/absence_management_delete/{id?}', 'Admin\Absence\AbsenceController@deleteManagementApi');
//     Route::get('/akb/absence_management_delete/{id?}', 'Admin\Absence\AbsenceController@deleteManagementApi');

//     //OvertimeWorkController
//     Route::get('/akb/ot_list/{orderBy?}/{sortBy?}', 'Admin\OvertimeWorkController@showApi');
//     Route::get('/akb/ot_view_insert', 'Admin\OvertimeWorkController@showDetailApi');
//     Route::post('/akb/ot_insert', 'Admin\OvertimeWorkController@storeApi');
//     Route::post('/akb/ot_update/{id}', 'Admin\OvertimeWorkController@updateApi');
//     Route::get('/akb/ot_delete/{id?}', 'Admin\OvertimeWorkController@deleteApi');
//     Route::get('/akb/ot_list_approved/{orderBy?}/{sortBy?}', 'Admin\OvertimeWorkController@showListApproveApi');
//     Route::get('/akb/ot_count_list_approved', 'Admin\OvertimeWorkController@countListApproveApi');
//     Route::get('/akb/ot_approve/{id?}', 'Admin\OvertimeWorkController@apprOvertimeApi');
//     Route::get('/akb/ot_unapprove/{id?}', 'Admin\OvertimeWorkController@unApprOvertimeApi');

//     //Timekeeping-New
//     Route::get('/akb/timekeeping_new', 'Admin\Checkin\TimekeepingController@indexApi');
//     Route::get('/akb/timekeeping_history', 'Admin\Checkin\TimekeepingController@showHistoryApi');

//     //Notication
//     Route::get('/akb/get_all_notification', 'Admin\AjaxController@getAllNotification');
//     Route::get('/akb/getUserByRoom/{id?}', 'Admin\AjaxController@getUsersByRoom');



//     // Chưa dùng
//     Route::get('/akb/user_view_insert', 'Admin\UserController@showUser');
//     Route::get('/akb/user_delete/{id?}/{del?}', 'Admin\UserController@showUser');

//     Route::get('/akb/daily_one', 'Admin\DailyReportController@user');
//     Route::get('/akb/daily_view_update/{id?}', 'Admin\DailyReportController@showDetail');

//     Route::get('/akb/room_view_in_up/{id?}', 'Admin\RoomController@showRoom');
//     Route::post('/akb/room_insert', 'Admin\RoomController@store');
//     Route::post('/akb/room_update', 'Admin\RoomController@store');
//     Route::get('/akb/room_delete/{id?}/{del?}', 'Admin\RoomController@showRoom');

//     Route::get('/akb/total_report', 'Admin\DailyReportController@generalReports');

//     //project
//     Route::get('/akb/project_list/{orderBy?}/{sortBy?}', 'Admin\ProjectController@show');
//     Route::get('/akb/project_view_insert', 'Admin\ProjectController@showDetail');
//     Route::post('/akb/project_insert', 'Admin\ProjectController@store');
//     Route::post('/akb/project_update', 'Admin\ProjectController@store');
//     Route::get('/akb/project_delete/{id?}/{del?}', 'Admin\ProjectController@showDetail');

//     //event EventController
//     Route::get('/akb/event_list/{orderBy?}/{sortBy?}', 'Admin\EventController@show');
//     Route::get('/akb/event_view_insert/{id?}', 'Admin\EventController@showDetail');
//     Route::post('/akb/event_insert', 'Admin\EventController@store');
//     Route::post('/akb/event_update', 'Admin\EventController@store');
//     Route::get('/akb/event_delete/{id?}/{del?}', 'Admin\EventController@showDetail');

//     Route::get('/akb/event_vote/{id?}', 'Admin\EventController@vote');
//     Route::get('/akb/event_save_vote/{orderBy?}/{sortBy?}', 'Admin\EventController@voteSave');
//     Route::get('/akb/event_vote_list/{id?}', 'Admin\EventController@voteResult');
//     Route::get('/akb/event_delete_vote/{aid?}/{uid?}', 'Admin\EventController@delVote');

//     Route::get('/akb/ot_of_user/{orderBy?}/{sortBy?}', 'Admin\OvertimeWorkController@show');

//     //TimeKeeping
//     Route::get('/akb/timekeeping_view/','Admin\TimekeepingController@index');
//     Route::get('/akb/timekeeping_view_insert/{id?}','Admin\TimekeepingController@detailTimekeeping');
//     Route::post('/akb/timekeeping_insert/','Admin\TimekeepingController@saveTimekeeping');
//     Route::post('/akb/timekeeping_update/','Admin\TimekeepingController@saveTimekeeping');
//     Route::get('/akb/timekeeping_delete/{id?}/{del?}','Admin\TimekeepingController@detailTimekeeping');

//     //get user by room
//     Route::get('/akb/getTimeOfDayUser/{uid?}', 'Admin\AjaxController@getTimeOfDayUser');

//     // action:[0,1,2] 0(không hoạt động) 1(hoạt động) 2(tất cả)
//     Route::get('/akb/getUsersByActive/{action?}', 'Admin\AjaxController@getUsersByActive');
//     Route::get('/akb/getUsersByOvertime/{id?}', 'Admin\AjaxController@getUsersByOvertime');
//     Route::get('/akb/getProjectByUserId/{id?}', 'Admin\AjaxController@getProjectByUserId');
// });

Route::prefix('akb-new')->group(function () {
    Route::post('login', 'Api\ApiLoginController@login')->middleware(['language', 'return-json']);
    Route::post('signup', 'Api\ApiLoginController@signup')->middleware(['language', 'return-json']);
    Route::post('qr-code', 'ApiBaseController@getQrCode')->middleware(['language', 'return-json'])->name('api.qr-code');
    Route::post('checkin', 'Api\ApiLoginController@checkin')->middleware(['language', 'return-json']);
    Route::get('redirect/{data?}', 'Api\ApiLoginController@redirect');
    Route::post('/checkin-card', 'Api\ApiLoginController@checkinCard')->middleware(['language', 'return-json']);


    //Config
    Route::get('getConfig', 'ApiBaseController@getConfig');
    Route::get('getDateTime', 'ApiBaseController@getDateTime')->name('api.getDateTime');
    Route::group(['middleware' => ['auth:api', 'language', 'return-json']], function () {
        Route::post('logout', 'Api\ApiLoginController@logout');

        // menus
        Route::get('menu_all', 'Admin\MenuController@getAllMenuApi');
        Route::get('menu_user', 'Admin\MenuController@getMenuApi');
        Route::get('menu', 'Admin\MenuController@menuList');

        //user
        Route::get('short_user_info', 'Admin\UserController@showShortProfileApi');
        Route::get('user_info/{id?}', 'Admin\UserController@showProfileApi');
        Route::post('user_update_profile', 'Admin\UserController@saveProfileApi');
        Route::get('user_list/{view?}/{orderBy?}/{sortBy?}', 'Admin\UserController@showApi');
        Route::post('user_insert', 'Admin\UserController@storeApi');
        Route::post('user_update/{id?}', 'Admin\UserController@updateApi');
        Route::post('user_upload_avatar/{id?}', 'Admin\UserController@updateImageApi');
        Route::post('saveCapicityProfile/{id?}', 'Admin\UserController@saveCapicityProfileApi');
        Route::post('change_password/{id?}', 'Admin\UserController@changePasswordApi');

        //daily_report
        Route::get('daily_list', 'Admin\DailyReportController@indexApi');
        Route::get('daily_view_insert/{id?}', 'Admin\DailyReportController@showDetailOneApi');
        Route::post('daily_insert', 'Admin\DailyReportController@storeApi');
        Route::post('daily_update/{id?}', 'Admin\DailyReportController@updateApi');
        Route::get('daily_delete/{id?}/{del?}', 'Admin\DailyReportController@deleteApi');
        Route::post('daily_insert_working_shecdule', 'Admin\DailyReportController@storeApiWorkingShecdule');

        //room
        Route::get('room_list/{orderBy?}/{sortBy?}', 'Admin\RoomController@showApi');

        //absences
        Route::get('absence_list/{orderBy?}/{sortBy?}', 'Admin\Absence\AbsenceController@showApi');
        Route::get('absence_view_insert', 'Admin\Absence\AbsenceController@showDetailApi');
        Route::post('absence_insert', 'Admin\Absence\AbsenceController@storeApi');
        Route::post('absence_update/{id?}', 'Admin\Absence\AbsenceController@updateApi');
        Route::get('absence_delete/{id?}', 'Admin\Absence\AbsenceController@deleteApi');
        Route::get('absence_list_approved', 'Admin\Absence\AbsenceController@showListApproveApi');
        Route::get('absence_count_list_approved', 'Admin\Absence\AbsenceController@countListApproveApi');
        Route::get('absence_approve/{id?}', 'Admin\Absence\AbsenceController@apprAbsenceApi');
        Route::get('absence_unApprove/{id?}', 'Admin\Absence\AbsenceController@unApprAbsenceApi');
        Route::get('absence_list_of_user/{orderBy?}/{sortBy?}', 'Admin\Absence\AbsenceController@showAbsenceManagementApi');
        Route::post('absence_management_insert', 'Admin\Absence\AbsenceController@storeManagementApi');
        Route::post('absence_management_update/{id?}', 'Admin\Absence\AbsenceController@updateManagementApi');
        Route::get('absence_management_delete/{id?}', 'Admin\Absence\AbsenceController@deleteManagementApi');
        Route::post('absence_management_insert_working', 'Admin\Absence\AbsenceController@insertToWorkingShecdule');

        //OvertimeWorkController
        Route::get('ot_list/{orderBy?}/{sortBy?}', 'Admin\OvertimeWorkController@showApi');
        Route::get('ot_view_insert', 'Admin\OvertimeWorkController@showDetailApi');
        Route::post('ot_insert', 'Admin\OvertimeWorkController@storeApi');
        Route::post('ot_update/{id}', 'Admin\OvertimeWorkController@updateApi');
        Route::get('ot_delete/{id?}', 'Admin\OvertimeWorkController@deleteApi');
        Route::get('ot_list_approved/{orderBy?}/{sortBy?}', 'Admin\OvertimeWorkController@showListApproveApi');
        Route::get('ot_count_list_approved', 'Admin\OvertimeWorkController@countListApproveApi');
        Route::get('ot_approve/{id?}', 'Admin\OvertimeWorkController@apprOvertimeApi');
        Route::get('ot_unapprove/{id?}', 'Admin\OvertimeWorkController@unApprOvertimeApi');

        //Timekeeping-New
        Route::get('timekeeping_new', 'Admin\Checkin\TimekeepingController@indexApi');
        Route::get('timekeeping_history', 'Admin\Checkin\TimekeepingController@showHistoryApi');

        //Notication
        Route::get('get_all_notification', 'Admin\AjaxController@getAllNotification');
        Route::get('get_count_all_notification', 'Admin\AjaxController@getCountAllNotification');
        Route::get('getUserByRoom/{id?}', 'Admin\AjaxController@getUsersByRoom');
        Route::get('get_notification_api', 'Admin\AjaxController@getNotificationAPI')->name('admin.NotificationAPI');
        Route::get('get_notification_api_year', 'Admin\AjaxController@getNotificationInYear')->name('admin.NotificationAPIYear');


        //Notication lisst
        Route::get('notification_list/{orderBy?}/{sortBy?}', 'Admin\AjaxController@showfirebase');

        //Role
        Route::get('get-all-role-user', 'ApiBaseController@getAllRoleUser');

        //project
        Route::get('project_list/{orderBy?}/{sortBy?}', 'Admin\ProjectController@showApi');
        Route::get('project_view_insert/{id?}', 'Admin\ProjectController@showDetailApi');
        Route::post('project_insert', 'Admin\ProjectController@storeApi');
        Route::post('project_update/{id?}', 'Admin\ProjectController@updateApi');
        Route::get('project_delete/{id?}', 'Admin\ProjectController@deleteApi');

        //room
        Route::get('room_view_in_up/{id?}', 'Admin\RoomController@showRoomApi');
        Route::post('room_insert', 'Admin\RoomController@storeApi');
        Route::post('room_update/{id?}', 'Admin\RoomController@updateApi');
        Route::get('room_delete/{id?}', 'Admin\RoomController@deleteApi');

        // Chưa dùng
        Route::get('user_view_insert', 'Admin\UserController@showUser');
        Route::get('user_delete/{id?}/{del?}', 'Admin\UserController@showUser');

        Route::get('daily_one', 'Admin\DailyReportController@user');
        Route::get('daily_view_update/{id?}', 'Admin\DailyReportController@showDetail');



        Route::get('total_report', 'Admin\DailyReportController@generalReports');

        //event EventController
        Route::get('event_list/{orderBy?}/{sortBy?}', 'Admin\EventController@show');
        Route::get('event_view_insert/{id?}', 'Admin\EventController@showDetail');
        Route::post('event_insert', 'Admin\EventController@store');
        Route::post('event_update/{id?}', 'Admin\EventController@store');
        Route::get('event_delete/{id?}/{del?}', 'Admin\EventController@showDetail');

        Route::get('event_vote/{id?}', 'Admin\EventController@vote');
        Route::post('event_save_vote/{orderBy?}/{sortBy?}', 'Admin\EventController@voteSave');
        Route::get('event_vote_list/{id?}', 'Admin\EventController@voteResult');
        Route::get('event_delete_vote/{aid?}/{uid?}', 'Admin\EventController@delVote');

        Route::get('ot_of_user/{orderBy?}/{sortBy?}', 'Admin\OvertimeWorkController@show');

        //WorkingSchedule
        Route::get('working_list/{orderBy?}/{sortBy?}', 'Admin\Work\WorkingScheduleController@show');
        Route::post('working_insert/{id?}', 'Admin\Work\WorkingScheduleController@store');
        Route::get('working_delete/{id?}/{del?}', 'Admin\Work\WorkingScheduleController@showDetail');
        Route::post('working_insert_dalily_absence/{id?}', 'Admin\Work\WorkingScheduleController@store');

        // Calendar
        Route::get('calendar_list', 'Admin\CalendarController@showCalendars');
        //TimeKeeping
        Route::get('timekeeping_view/', 'Admin\TimekeepingController@index');
        Route::get('timekeeping_view_insert/{id?}', 'Admin\TimekeepingController@detailTimekeeping');
        Route::post('timekeeping_insert/', 'Admin\TimekeepingController@saveTimekeeping');
        Route::post('timekeeping_update/', 'Admin\TimekeepingController@saveTimekeeping');
        Route::get('timekeeping_delete/{id?}/{del?}', 'Admin\TimekeepingController@detailTimekeeping');
        Route::post('timekeepingWorkAt', 'Admin\Checkin\TimekeepingController@checkinWorkAt');
        //get user by room
        Route::get('getTimeOfDayUser/{uid?}', 'Admin\AjaxController@getTimeOfDayUser');

        // action:[0,1,2] 0(không hoạt động) 1(hoạt động) 2(tất cả)
        Route::get('getUsersByActive/{action?}', 'Admin\AjaxController@getUsersByActive');
        Route::get('getUsersByOvertime/{id?}', 'Admin\AjaxController@getUsersByOvertime');
        Route::get('getProjectByUserId/{id?}', 'Admin\AjaxController@getProjectByUserId');
        //Equipment
        Route::get('equipment', 'Admin\EquipmentController@indexApi');
        Route::post('equipment_insert', 'Admin\EquipmentController@storeApi');
        Route::post('equipment_update/{id?}', 'Admin\EquipmentController@updateApi');
        Route::get('equipment_delete/{id?}', 'Admin\EquipmentController@deleteApi');
        Route::get('equipment_detail/{oneId?}', 'Admin\EquipmentController@showDetailApi');
        Route::get('equipment_history_detail/{oneId?}', 'Admin\EquipmentController@showStatusHistoryApi');
        Route::get('equipments/{device?}', 'Admin\EquipmentController@exportQRView');

        //Equipment Type
        Route::get('equipment_type', 'Admin\EquipmentTypeController@indexApi');
        Route::post('equipment_type_insert', 'Admin\EquipmentTypeController@storeApi');
        Route::post('equipment_type_update/{id?}', 'Admin\EquipmentTypeController@updateApi');
        Route::get('equipment_type_delete/{id?}', 'Admin\EquipmentTypeController@deleteApi');
        Route::get('equipment_type_detail/{oneId?}', 'Admin\EquipmentTypeController@showDetailApi');

        //Equipment History
        Route::get('equipment_history', 'Admin\EquipmentHistoryController@indexApi');
        Route::post('equipment_history_insert', 'Admin\EquipmentHistoryController@storeApi');
        Route::get('equipmenthistory_detail', 'Admin\EquipmentHistoryController@showDetailApi');
        Route::post('equipment_status', 'Admin\EquipmentHistoryController@getEquipmentList');

        //Equipment Registration
        Route::get('equipment_registration', 'Admin\EquipmentRegistrationController@indexApi');
        Route::post('equipment_registration_insert', 'Admin\EquipmentRegistrationController@storeApi');
        Route::post('equipment_registration_update/{id?}', 'Admin\EquipmentRegistrationController@updateApi');
        Route::get('equipment_registration_delete/{id?}', 'Admin\EquipmentRegistrationController@deleteApi');
        Route::get('equipment_registration_detail/{oneId?}', 'Admin\EquipmentRegistrationController@showDetailApi');
        Route::post('equipment_type_list', 'Admin\EquipmentRegistrationController@getEquipmentTypeList');
        Route::post('equipment_list', 'Admin\EquipmentRegistrationController@getEquipmentStatus');
        Route::post('equipment_registration_status', 'Admin\EquipmentRegistrationController@getEquipmentList');
        Route::get('equipment_registration_approve/{id?}', 'Admin\EquipmentRegistrationController@regApproveApi');
        Route::post('equipment-approve-detail', 'Admin\EquipmentRegistrationController@regApproveDetailStoreApi');
        Route::get('equipment_approve_detail/{id?}/{reject?}', 'Admin\EquipmentRegistrationController@regApproveDetailApi');
        Route::post('equipment_approve_list', 'Admin\EquipmentRegistrationController@equipmentApproveList');
        Route::post('equipment_reg_reject', 'Admin\EquipmentRegistrationController@regApproveRejectApi');
        Route::post('equipment_reg_reject_checkAddReg', 'Admin\AjaxController@checkAddRegistration');
        Route::get('calendar/{orderBy?}/{sortBy?}', 'Admin\CalendarController@showCalendar');
    });
});

Route::group(['prefix' => 'akb', 'middleware' => 'auth:api'], function () {
    Route::group(['middleware' => ['language', 'return-json']], function () {

        //ApiWorkTaskController
        Route::match(['get', 'post'], 'project-all/{id?}/{order_by?}/{sort_by?}', 'Api\ApiWorkTaskController@show')->name('admin.ApiAllProject');
        Route::post('task-add/{id?}', 'Api\ApiWorkTaskController@addTask')->name('admin.ApiTaskAction');
        Route::get('task-all/{id_project}/{status?}/{id_task?}', 'Api\ApiWorkTaskController@infoTask')->name('admin.ApiTaskDisplay');
        Route::post('report-task', 'Api\ApiWorkTaskController@reportTaskWork')->name('admin.ApiReportTask');
        Route::post('task-important/{id}', 'Api\ApiWorkTaskController@changeImportant')->name('admin.ApiChangeImportant');
        Route::post('task-change/status', 'Api\ApiWorkTaskController@changeStatus')->name('admin.ApiChangeStatus');
        Route::get('task-suggest/{id}', 'Api\ApiWorkTaskController@suggestSearch')->name('admin.ApiTaskSuggest');
        Route::post('task-delete', 'Api\ApiWorkTaskController@delete')->name('admin.ApiDeleteTask');
        Route::post('task-update/{id}', 'Api\ApiWorkTaskController@update')->name('admin.ApiUpdateTask');
        Route::post('error-review', 'Api\ApiWorkTaskController@addErrorReview')->name('admin.ApiAddErrorReview');
        Route::post('error-review/report', 'Api\ApiWorkTaskController@reportErrorReview')->name('admin.ApiReportErrorReview');
        Route::get('project-members/{id}', 'Api\ApiWorkTaskController@userDependProject')->name('admin.ApiMembersInProject');
        Route::get('suggest-all', 'Api\ApiWorkTaskController@suggestSearchAll')->name('admin.ApiSuggestAll');
        Route::get('loading/report-task/{id}', 'Api\ApiWorkTaskController@loadReport')->name('admin.ApiLoadReport');
        Route::post('task/{id}/upload-file', 'Api\ApiWorkTaskController@uploadFile')->name('admin.ApiTaskUploadFile');

        //ApiMembersController
        Route::get('info-members/{id_pro}', 'Api\ApiMembersController@getInfoMembers')->name('admin.ApiMembers');
    });
    Route::get('project-export/{order_by?}/{sort_by?}', 'Api\ApiWorkTaskController@export')->name('admin.ApiExportProject');
});
