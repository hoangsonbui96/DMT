<?php

/**
 |--------------------------------------------------------------------------
 | Web Routes
 |--------------------------------------------------------------------------
 |
 | Here is where you can register web routes for your application. These
 | routes are loaded by the RouteServiceProvider within a group which
 | contains the "web" middleware group. Now create something great!
 |
 */

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

$request = app()->make('Illuminate\Http\Request');
$cpn = is_null($request->segment(1)) ? 'akb' : $request->segment(1);
Route::match(['get', 'post'], '/', function () {
    return redirect()->route('login');
});

Route::get($cpn . '/login', 'Admin\Auth\LoginController@showLoginForm')->name('login');
Route::post($cpn . '/login', 'Admin\Auth\LoginController@login');
Route::get($cpn . '/', 'Admin\HomeController@index');
Route::get('admin/index.php', 'Admin\EquipmentController@exportQRView')->name('admin.equipmentQRcode')->middleware(['auth:web']);

Route::get('/assets/{module}/{type}/{file}', [ function ($module, $type, $file) {
    $module = ucfirst($module);

    $path = app_path("Modules/$module/Resources/Blocks/$type/$file");

    if (File::exists($path)) {
        return response()->download($path, "$file");
    }

    return response()->json([ ], 404);
}]);


//qr code
Route::get($cpn . '/qr-code', 'Admin\Checkin\CheckinController@showForm')->name('qrCode');

Route::prefix($cpn)->namespace('Admin')->middleware(['auth'])->group(function () {

    //file manager
    Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
        \UniSharp\LaravelFilemanager\Lfm::routes();
    });
    // ajax request
    Route::get('checkin-ip', 'Checkin\CheckinController@checkIP')->name('IpCheckin');

    Route::post('ajax/meeting-user-list', 'AjaxController@meetingList')->name('ajax.meetingList');
    Route::post('ajax/equipment-list', 'AjaxController@getEquipmentStatus')->name('ajax.equipmentStatus');
    Route::post('ajax/equipment-status', 'AjaxController@getEquipmentList')->name('ajax.equipmentList');
    // Route::post('ajax/equipment-list-by-user', 'AjaxController@getEquipmentListByUser')->name('ajax.equipmentListByUser');
    Route::post('ajax/equipment-handover', 'AjaxController@saveHandover')->name('ajax.saveHandover');
    Route::post('ajax/equipment-type-list', 'AjaxController@getEquipmentTypeList')->name('ajax.equipmentTypeList');
    Route::post('ajax/check-add-reg', 'AjaxController@checkAddRegistration')->name('ajax.checkAddReg');
    Route::post('ajax/equipment-approve-list', 'AjaxController@equipmentApproveList')->name('ajax.equipmentApproveList');
    Route::post('ajax/set-cookie', 'AjaxController@setCookie')->name('ajax.setCookie');
    Route::post('ajax/daily-report-status', 'DailyReportController@getUserReportStatus')->name('ajax.getUserReportStatus');
    // end ajax request

    Route::get('/', 'HomeController@index')->name('admin.home');

    Route::get('logout', 'Auth\LoginController@logout')->name('logout');

    // Checkin

    // Monthly Report
    Route::get('monthly-reports-create', 'ReportPMMonthlyController@main')->name('admin.MeetingWeeks');
    Route::post('monthly-reports-create', 'ReportPMMonthlyController@save')->name('admin.MeetingWeeklySave');
    Route::post('weekly-reports-comment/{id}', 'ReportPMMonthlyController@storeComment')->name('admin.MeetingWeeklyCommentSave');
    Route::get('monthly-reports-one/{id?}/{del?}', 'ReportPMMonthlyController@showMeeting')->name('admin.showMeeting');
    Route::get('monthly-reports-meeting/{id?}/{del?}','ReportPMMonthlyController@openMeeting')->name('admin.MeetingWeeksDetail');
    Route::get('monthly-reports/{id}', 'ReportPMMonthlyController@index')->name('admin.MonthlyReports');
    Route::post('monthly-reports', 'ReportPMMonthlyController@store')->name('admin.MonthlySave');
    Route::post('monthly-reports-update', 'ReportPMMonthlyController@updateDetail')->name('admin.weeklySave');
    Route::get('monthly-reports-detail/{id?}/{del?}','ReportPMMonthlyController@showDetail')->name('admin.MonthlyDetail');
    Route::get('monthly-reports-view-detail/{id?}/{del?}','ReportPMMonthlyController@reviewDetail')->name('admin.MonthlyReview');
    Route::get("project-comment/{id}", "ReportPMMonthlyController@openModalComment")->name("admin.ProjectComment");
    Route::get("project-comment/reports/{id}", "ReportPMMonthlyController@openModalCommentSpecific")->name("admin.ProjectCommentSpecific");
    Route::post("project-comment/update", "ReportPMMonthlyController@updateComment")->name("admin.ProjectUpdateComment");
    // Extra of monthly report
    Route::get("pdf/comment/{id}", "ReportPMMonthlyController@pdf")->name("admin.ProjectCommentDownloadPDF");
//    Route::get("position/users", "ReportPMMonthlyController@getUserByPosition")->name("admin.GetUserByPosition");
//    Route::get("user/position", "ReportPMMonthlyController@getUserDetail")->name("admin.GetUserDetailPosition");
    Route::get("report-pm/status", "ReportPMMonthlyController@changePublic")->name("admin.ReportPMPublic");

    Route::get('daily-reports', 'DailyReportController@index')->name('admin.DailyReports');
    Route::get('need-approve-reports', 'ReportManager\NeedApproveReportController@index')->name('admin.NeedApproveReports');
    Route::get('daily-notifications', 'DailyReportController@notifications')->name('admin.DailyNotifications');
    Route::get('general-reports/{order?}/{type?}/{t?}', 'DailyReportController@generalReports')->name('admin.GeneralReports');
    Route::get('daily/{id?}/{del?}', 'DailyReportController@showDetail')->name('admin.DailyInfo');
//    Route::get('daily-one/{id?}', 'DailyReportController@showDetailOne')->name('admin.DailyInfoOne');
//    Route::post('daily-one', 'DailyReportController@storeOne');
    Route::post('daily-report', 'DailyReportController@store')->name('admin.DailySave');
    Route::get('daily-report-appove','ReportManager\NeedApproveReportController@aprroveReport')->name('admin.ApproveReport');
    Route::get('daily-report-deny','ReportManager\NeedApproveReportController@openDenyReport')->name('admin.openDenyReport');
    Route::post('daily-report-deny','ReportManager\NeedApproveReportController@denyReport')->name('admin.DenyReport');
    Route::get('listUserByAction/{action?}', 'AjaxController@getUsersByActive')->name('admin.getUsersByActive');
    Route::get('listUserByActionAndLeaderPosition/{action?}', 'AjaxController@getUsersByActiveAndLeaderPosition')->name('admin.getUsersByActiveAndLeaderPosition');
    Route::get('ListProjectByAction/{action?}', 'AjaxController@getProjectsByActive')->name('admin.getProjectsByActive');
//    Route::post('search-general-reports', 'DailyReportController@searchGeneralReport')->name('admin.generalReportSearch');

    Route::get('export/daily-report', 'DailyReportController@export')->name('export.exportDailyReport');
    Route::get('export/daily-reports', 'DailyReportController@exportReport')->name('export.exportDailyReports');

    // TotalReport
    Route::get('total-report', 'DailyReportController@DailyReportStatus')->name('admin.TotalReport');
    Route::post('total-report-detail', 'DailyReportController@showTotalDetail')->name('admin.TotalReportDetail');
    Route::post('save-listNotWriteDaily', 'DailyReportController@saveArrayNWD')->name('admin.saveArrayNWD');
    // Route::post('total-report', 'TotalReportController@Search')->name('admin.TotalReportSearch');

    // Yearly Report
    Route::get('yearly-reports', 'DailyReportController@yearlyReports')->name('admin.YearlyReports');
    Route::get('export/yearly-reports', 'DailyReportController@exportYearlyReport')->name('admin.exportYearlyReport');

    // 2020/05/08
    Route::get('getAllNotification', 'AjaxController@getAllNotification')->name('admin.getAllNotification');

    //RoomReport 4/5/2020
    Route::get('room-report/{orderBy?}/{sortBy?}', 'RoomReportController@show')->name('admin.RoomReports');
    Route::post('room-report', 'RoomReportController@store')->name('admin.RoomReportStore');
    Route::get('room-reports/{id?}/{del?}', 'RoomReportController@showDetail')->name('admin.RoomReportInfo');
    Route::get('export/room-report/{search?}', 'RoomReportController@export')->name('export.RoomReport');

    // User Group
    Route::get('user-groups/{id?}/{del?}', 'UserGroupController@show')->name('admin.UserGroups');
    Route::post('user-groups/{id?}', 'UserGroupController@store');
    // Route::get('del-user-group/{id}', 'UserGroupController@delete');

    // Users
    Route::get('export/users/{view?}', 'UserController@export')->name('export.users');
    Route::get('users/{view?}/{orderBy?}/{sortBy?}', 'UserController@show')->name('admin.Users');
    Route::get('list_position/{view?}/{orderBy?}/{sortBy?}', 'Position\ListPositionController@show')->name('admin.ListPosition');
    Route::get('list-position-item/{id?}/{del?}', 'Position\ListPositionController@showDetail')->name('admin.ListPositionItem');
    Route::get('add-group-position/{id?}/{del?}', 'Position\ListPositionController@showDetailGroupPosition')->name('admin.AddGroupPosition');
    Route::post('list_position', 'Position\ListPositionController@store')->name('admin.ListPosition');
    Route::post('store-group-position', 'Position\ListPositionController@storeGroupPosition')->name('admin.StoreGroupPosition');
    Route::post('users', 'UserController@store')->name('admin.UserStore');
    Route::post('users-working', 'UserController@storeWorkingMode')->name('admin.storeWorkingMode');
    Route::post('users-info', 'UserController@storeDetailInfo')->name('admin.storeDetailInfo');
    Route::get('user/{id?}/{del?}', 'UserController@showUser')->name('admin.UserInfo');
    Route::get('profile_user/{id?}', 'UserController@showProfile')->name('admin.ProfileUser');
    Route::post('storeProfile', 'UserController@storeProfile')->name('admin.storeProfile');
    Route::post('changePassword', 'UserController@changePassword')->name('admin.changePassword');
    Route::get('viewChangePassword', 'UserController@viewLayoutChangePassword')->name('admin.viewLayoutChangePassword');
    Route::get('changeActive/{id?}/{active?}', 'UserController@changeCheckboxActive')->name('admin.CheckboxActive');
    Route::get('users-mode/{view?}/{orderBy?}/{sortBy?}', 'UserController@showWorkingMode')->name('admin.workingMode');
    Route::get('user-working/{id?}/{del?}', 'UserController@showUserWorking')->name('admin.showUserWorking');

    //
    // 4/5/2020--Đối tác
    Route::get('partner/{orderBy?}/{sortBy?}', 'PartnerController@show')->name('admin.Partner');
    Route::post('partner', 'PartnerController@store')->name('admin.PartnerStore');
    Route::get('partners/{id?}/{del?}', 'PartnerController@showDetail')->name('admin.PartnerInfo');
    Route::get('export/partners/{search?}', 'PartnerController@export')->name('export.Partner');

    // Rooms
    Route::get('rooms/{orderBy?}/{sortBy?}', 'RoomController@show')->name('admin.Rooms');
    Route::post('rooms', 'RoomController@store');
    Route::get('room/{id?}/{del?}', 'RoomController@showRoom')->name('admin.RoomInfo');

    // Meeting Schedules
    Route::get('meetings/{orderBy?}/{sortBy?}', 'MeetingScheduleController@show')->name('admin.MeetingSchedules');
    // Route::get('meetings/{orderBy?}/{sortBy?}', 'MeetingScheduleController@checkdroom')->name('admin.MeetingSchedule');
    Route::post('meetings', 'MeetingScheduleController@store');
    Route::get('meeting/{id?}/{del?}', 'MeetingScheduleController@showDetail')->name('admin.MeetingInfo');

    // Project
    Route::get('projects/{orderBy?}/{sortBy?}', 'ProjectController@show')->name('admin.Projects');
    Route::post('projects', 'ProjectController@store');
    Route::get('project/{id?}/{del?}', 'ProjectController@showDetail')->name('admin.ProjectInfo');
    Route::get('projectManager/{id?}', 'ProjectController@showPhaseJob')->name('admin.ProjectPhaseJob');
    Route::get('projectChangeActive/{id?}/{active?}', 'ProjectController@changeCheckboxActive')->name('admin.CheckboxActiveProject');

    // Overtime Work
    Route::get('overtimes/{orderBy?}/{sortBy?}', 'OvertimeWorkController@show')->name('admin.Overtimes');
    Route::get('overview-overtimes/{orderBy?}/{sortBy?}', 'OvertimeWorkController@showOverview')->name('admin.OverviewOvertimes');
    Route::post('overtimes', 'OvertimeWorkController@store')->name('admin.OvetimeStore');
    Route::get('report-overtimes/', 'OvertimeWorkController@showReport')->name('admin.ReportOvertimes');
    Route::get('overtime/{id?}/{del?}', 'OvertimeWorkController@showDetail')->name('admin.OvertimeInfo');
    Route::get('overtime-apr/{id?}/{del?}', 'OvertimeWorkController@AprOvertime')->name('admin.AprOvertime');
    Route::get('overtime-list-approve/{orderBy?}/{sortBy?}', 'OvertimeWorkController@showListApprove')->name('admin.OvertimeListApprove');
    Route::get('overtime-unapprove', 'OvertimeWorkController@showDetailUnapprove')->name('admin.OvertimeUnapprove');

    // export excel overtime
    Route::get('export/overtime/{search?}', 'OvertimeWorkController@export')->name('export.ExportOvertimes');
    Route::get('users-by-overtime/{id?}', 'AjaxController@getUsersByOvertime')->name('admin.getUsersByOvertime');
    Route::get('project-by-user/{id?}', 'AjaxController@getProjectByUserId')->name('admin.getProjectByUserId');

    //xuất excel danh sách tổng quan(giờ làm thêm)
    Route::get('export-overtime', 'OvertimeWorkController@exportOverview')->name('export.listOvertime');


    // Absences
    Route::get('export/absences', 'Absence\AbsenceController@export')->name('export.Absences');
    Route::get('export/absencesReport', 'Absence\AbsenceController@absencesReportExport')->name('export.AbsencesReport');
    Route::get('absences/{orderBy?}/{sortBy?}', 'Absence\AbsenceController@show')->name('admin.Absences');
    Route::get('absence-management/{id?}/{orderBy?}/{sortBy?}', 'Absence\AbsenceController@showabsencemanagement')->name('admin.AbsenceManagement');
    Route::post('absence-managements', 'Absence\AbsenceController@store')->name('admin.ManagementStore');
    Route::get('absence-reports/{orderBy?}/{sortBy?}', 'Absence\AbsenceController@showreport')->name('admin.AbsenceReports');
    Route::get('absence/{id?}/{del?}', 'Absence\AbsenceController@showdetail')->name('admin.AbsenceInfo');
    Route::get('absence-apr/{id?}/{del?}', 'Absence\AbsenceController@AprAbsence')->name('admin.AprAbsence');
    Route::get('absences-list-approve/{orderBy?}/{sortBy?}', 'Absence\AbsenceController@showListApprove')->name('admin.AbsencesListApprove');
    Route::get('absence-unApprove', 'Absence\AbsenceController@showDetailUnapprove')->name('admin.UnApprove');
//    Route::get('absence-get-time-of-user/{id?}', 'AjaxController@getTimeOfDayUser')->name('admin.getTimeOfDayUser');

    // user skill
    Route::get('skills/{orderBy?}/{sortBy?}', 'EmployerSkillController@show')->name('admin.EmployerSkills');
    Route::get('profile-skill/{id?}', 'EmployerSkillController@showDetail')->name('admin.ProfileSkill');
    Route::post('profile-skill/{id?}', 'EmployerSkillController@store');

    // master data
    Route::get('masters/{orderBy?}/{sortBy?}', 'MasterDataController@show')->name('admin.MasterData');
    Route::post('masters/', 'MasterDataController@store');
    Route::get('masterdata-item/{id?}/{del?}', 'MasterDataController@showDetail')->name('admin.MasterDataItem');

    // equipment
    Route::get('equipment/{orderBy?}/{sortBy?}', 'EquipmentController@show')->name('admin.Equipment');
    Route::post('equipment', 'EquipmentController@store');
    Route::get('equipment-detail/{id?}/{del?}', 'EquipmentController@showDetail')->name('admin.EquipmentInfo');
    Route::get('equipment-status-histories/{id?}', 'EquipmentController@showStatusHistory')->name('admin.EquipmentStatusHistories');
    // export excel equipment
    Route::get('export/equipment', 'EquipmentController@export')->name('export.equipment');
    Route::get('equipments', 'EquipmentController@exportQRView')->name('exportQR.equipmentQRcode');
    Route::get('exportQR/equipment', 'EquipmentController@exportQR')->name('exportQR.equipment');

    Route::get('maintenance/{id?}', 'EquipmentController@showMaintenance')->name('admin.Maintenance');
    Route::get('maintenance-detail/{id?}', 'EquipmentController@showMaintenanceDetail')->name('admin.MaintenanceInfo');

    // equipment type
    Route::get('equipment-types/{orderBy?}/{sortBy?}', 'EquipmentTypeController@show')->name('admin.EquipmentType');
    Route::post('equipment-types', 'EquipmentTypeController@store');
    Route::get('equipment-type-detail/{id?}/{del?}', 'EquipmentTypeController@showDetail')->name('admin.EquipmentTypeDetail');

    // equipment using histories
    Route::get('export/equipment-histories', 'EquipmentHistoryController@exportHistories')->name('export.equipmentHistories');
    Route::get('equipment-histories/{orderBy?}/{sortBy?}', 'EquipmentHistoryController@show')->name('admin.EquipmentHistories');
    Route::post('equipment-histories', 'EquipmentHistoryController@store');
    Route::get('equipment-history-detail/{id?}', 'EquipmentHistoryController@showDetail')->name('admin.EquipmentHistoryDetail');

    // equipment registration
    Route::get('equipment-registrations/{orderBy?}/{sortBy?}', 'EquipmentRegistrationController@show')->name('admin.EquipmentRegistrations');
    Route::post('equipment-registrations', 'EquipmentRegistrationController@store');
    Route::get('equipment-registration-detail/{id?}/{del?}', 'EquipmentRegistrationController@showDetail')->name('admin.EquipmentRegistrationDetail');
    Route::get('equipment-registration-approve/{id?}', 'EquipmentRegistrationController@regApprove')->name('admin.EquipmentRegistrationApprove');
    Route::get('equipment-approve-detail/{id?}/{reject?}', 'EquipmentRegistrationController@regApproveDetail')->name('admin.EquipmentApproveDetail');
    Route::post('equipment-approve-detail/', 'EquipmentRegistrationController@regApproveDetailStore');
    Route::post('equipment-reg-reject/', 'EquipmentRegistrationController@regApproveReject')->name('admin.EquipmentRegReject');

    Route::get('work-tables/{orderBy?}/{sortBy?}', 'TaskController@show')->name('admin.Tasks');
    Route::get('work-table/{id?}', 'TaskController@showTasksInProject')->name('admin.TasksInProject');
    Route::get('work/{id?}', 'TaskController@showWork')->name('admin.showWork');
    Route::post('work', 'TaskController@updateWork');
    Route::post('task', 'TaskController@updateTask')->name('admin.updateTask');

    // ajax task
    Route::post('ajax-task/new-work-list', 'TaskController@newWorkList')->name('admin.TaskNewWorkList');
    Route::post('ajax-task/update-work-list-order', 'TaskController@updateWorkListOrder')->name('admin.TaskUpdateWorkListOrder');
    Route::post('ajax-task/update-work-list-title', 'TaskController@updateWorkListTitle')->name('admin.TaskUpdateWorkListTitle');

    Route::post('ajax-task/new-work', 'TaskController@newWork')->name('admin.TaskNewWork');
    Route::post('ajax-task/update-work-order', 'TaskController@updateWorkOrder')->name('admin.TaskUpdateWorkOrder');
    Route::get('ajax-task/deleteWorkList/{id?}', 'TaskController@deleteWorkList')->name('admin.TaskDeleteWorkList');

    //new work table
    Route::post('ajax-task/new-work-table', 'TaskController@newWorkTable')->name('admin.TaskNewWorkTable');

    // Interview-Job
    Route::get('interview-jobs/{orderBy?}/{sortBy?}', 'InterviewJobController@interviewJob')->name('admin.InterviewJob');
    Route::get('interview-schedule', 'InterviewJobController@interviewSchedule')->name('admin.InterviewSchedule');
    Route::get('interview-job/{id?}/{del?}', 'InterviewJobController@showDetail')->name('admin.JobInfo');
    Route::post('interview-jobs', 'InterviewJobController@storeJob');
    Route::post('changer-active/{id?}/{active?}', 'InterviewJobController@changerCheckboxActive')->name('admin.InterCheckboxActive');

    Route::get('interview/{id?}', 'InterviewJobController@showListCandidate')->name('admin.CandidateList');
    Route::get('interviews/{rmb?}/{id?}/{del?}', 'InterviewJobController@showInfoCandidate')->name('admin.CandidateInfo');
    Route::post('interview-storeCandidate', 'InterviewJobController@storeCandidate');
    Route::get('downloadCV', 'InterviewJobController@routeDownloadCV')->name('admin.downloadCV');

    Route::get('interview-schedules/{id?}/{del?}', 'InterviewJobController@showScheduleDetail')->name('admin.ScheduleDetail');
    Route::post('interview-storeSchedule', 'InterviewJobController@storeSchedule');
    Route::get('getUser/{id?}', 'InterviewJobController@getUserOfJob');
    Route::post('changeApproveSchedule/{id?}', 'InterviewJobController@changeApproveSchedule');

    // get
    Route::post('users-by-room/{id?}', 'AjaxController@getUsersByRoom')->name('admin.getUsersByRoom');

    // bsetup role
    Route::get('role-groups', 'RoleGroupController@index')->name('admin.RoleSetup');
    Route::get('view_role', 'RoleGroupController@ViewRole')->name('admin.ViewRole');
    Route::get('list-menus', 'RoleGroupController@listMenus')->name('admin.ListMenus');


    Route::get('edit-menu/{id?}/{del?}', 'RoleGroupController@editMenu')->name('admin.EditMenu');
    Route::post('change-Lang-Menu', 'LocaleFileController@changeLang')->name('admin.changeLangMenu');
    Route::post('save-Menu', 'RoleGroupController@saveMenu')->name('admin.saveMenu');

    Route::get('role-group/{id?}', 'RoleGroupController@showDetail')->name('admin.RoleGroupDetail');
    Route::post('role-group/{id?}', 'RoleGroupController@store')->name('admin.RoleGroupDetail');
    Route::get('refreshRoleScreen', 'RoleGroupController@refreshRole')->name('admin.refreshRoleScreen');
    Route::post('get-role-user-screen', 'RoleGroupController@getRoleUserScreen')->name('admin.getRoleUserScreen');
    Route::post('get-role-group', 'RoleGroupController@getRoleGroup')->name('admin.getRoleGroup');
    Route::post('delete-role-one', 'RoleGroupController@deleteOne')->name('admin.deleteOne');
    Route::post('save-new-lang', 'RoleGroupController@saveNewLang')->name('admin.saveNewLang');
    Route::get('export-file-json', 'RoleGroupController@exportFileJson')->name('admin.exportFileJson');

    // ajax role group
    Route::get('ajax/role-screen-detail/{id?}/{groupId?}/{userId?}', 'RoleGroupController@AjaxRoleScreen')->name('admin.AjaxRoleScreenDetail');
    Route::get('ajax/role-screen-input/{groupId?}/{checked?}/{id?}/{userId?}', 'RoleGroupController@AjaxRoleScreenInput')->name('admin.AjaxRoleScreenInput');
    Route::get('ajax/role-screen-detail-selected/{groupId?}/{userId?}', 'RoleGroupController@AjaxRoleScreenDetailSelected')->name('admin.AjaxRoleScreenDetailSelected');
    Route::get('ajax/role-screen-detail-input/{groupId?}/{checked?}/{id?}/{userId?}', 'RoleGroupController@AjaxRoleScreenDetailInput')->name('admin.AjaxRoleScreenDetailInput');
    Route::get('ajax/role-screen-selected-delete/{groupId?}/{alias?}/{userId?}', 'RoleGroupController@AjaxRoleScreenSelectedDelete')->name('admin.AjaxRoleSelectedDelete');
    Route::post('ajax/role-screen-detail-input/', 'RoleGroupController@AjaxRoleScreenDetailInputAll')->name('admin.AjaxRoleScreenDetailInputPost');

    //Timekeeping Old
    Route::get('timekeeping', 'TimekeepingController@index')->name('admin.Timekeeping');
    Route::get('export/timekeeping/{month?}/{year?}/{user?}', 'TimekeepingController@export')->name('admin.exportTimekeeping');
    Route::get('exports/timekeeping/{month?}/{year?}/{user?}', 'TimekeepingController@exportabsence')->name('admin.exportAbsenceTimekeeping');
    Route::get('add/timekeeping/{id?}/{del?}', 'TimekeepingController@detailTimekeeping')->name('admin.detailTimekeeping');
    Route::post('import/timekeeping', 'TimekeepingController@import')->name('admin.importTimekeeping');
    Route::post('save/timekeeping', 'TimekeepingController@saveTimekeeping')->name('admin.TimekeepingSave');
    Route::post('absence/timekeeping', 'TimekeepingController@absenceTimekeeping')->name('admin.AbsenceTimekeeping');

    //Timekeeping New
    Route::get('timekeeping-new', 'Checkin\TimekeepingController@index')->name('admin.TimekeepingNew');
    Route::get('export/timekeeping-new/{month?}/{year?}/{user?}', 'Checkin\TimekeepingController@export')->name('admin.exportTimekeepingNew');
    Route::get('exports/timekeeping-new/{month?}/{year?}/{user?}', 'Checkin\TimekeepingController@exportabsence')->name('admin.exportAbsenceTimekeepingNew');
    Route::get('add/timekeeping-new/{id?}/{del?}', 'Checkin\TimekeepingController@detailTimekeeping')->name('admin.detailTimekeepingNew');
    Route::post('import/timekeeping-new', 'Checkin\TimekeepingController@import')->name('admin.importTimekeepingNew');
    Route::post('save/timekeeping-new', 'Checkin\TimekeepingController@saveTimekeeping')->name('admin.TimekeepingNewSave');
    Route::post('absence/timekeeping-new', 'Checkin\TimekeepingController@absenceTimekeeping')->name('admin.AbsenceTimekeepingNew');
    Route::get('timekeeping-history', 'Checkin\TimekeepingController@showHistory')->name('admin.TimekeepingHistory');

    Route::get('timekeeping-company', 'Checkin\TimekeepingController@summaryTime')->name('admin.TimekeepingCompany');
    Route::get('timekeeping-user/{type?}/{date?}', 'Checkin\TimekeepingController@latecomers')->name('admin.latecomers');
    Route::get('export/timekeeping-company', 'Checkin\TimekeepingController@exportSummaryTimekeeping')->name('admin.ExportTimeKeepingCompany');

    Route::get('timekeepingworkat-new', 'Checkin\CheckinController@showForm');
    Route::post('save/timekeepingWorkAt', 'Checkin\TimekeepingController@checkinWorkAt')->name('admin.TimekeepingNewWorkAt');

    //Timekeeping scheduler
    Route::group(['prefix' => 'timekeeping-scheduler', 'namespace' => 'Checkin'], function () {
        Route::get('', 'TimekeepingSchedulerController@index')->name('admin.TimekeepingScheduler');
        Route::get('export', 'TimekeepingSchedulerController@export')->name('admin.ExportTimekeepingScheduler');
    });

    //Timekeeping History

    // Calendar
    Route::get('CalendarManagement/{orderBy?}/{sortBy?}', 'CalendarController@showCalendar')->name('admin.CalendarData');
    Route::get('CalendarManagement-item/{id?}/{del?}', 'CalendarController@showDetail')->name('admin.CalendarDataItem');
    Route::post('CalendarManagement/', 'CalendarController@storeCalendar');
    Route::get('Calendar/{orderBy?}/{sortBy?}', 'CalendarController@showCalendars')->name('admin.Calendar');
    Route::get('Calendar-item/{id?}/{del?}', 'CalendarController@showDetailCalendar')->name('admin.CalendarItem');
    Route::post('Calendar/', 'CalendarController@storeCalendarInfo');
    Route::get('Calendar-year-copy/{id?}/{del?}', 'CalendarController@showDetailYear')->name('admin.CalendarYear');
    Route::get('Calendar-week-item/{id?}', 'CalendarController@showDetailCalendarWeek')->name('admin.CalendarItemWeek');

    //convert
    Route::get('convert', 'ConvertController@index');
    Route::get('optimize', 'OptimizeController@index')->name('admin.optimize');

    //financial
    Route::get('finance/list', 'FinanceController@spendingList')->name('admin.spendingList');
    Route::get('finance/detail/{id?}/{del?}', 'FinanceController@spendingDetail')->name('admin.spendingDetail');
    Route::post('finance/detail', 'FinanceController@spendingStore');
    Route::get('finance/list/export', 'FinanceController@exportExcel')->name('admin.exportExcelSpending');

    Route::get('finance/stats', 'FinanceController@stats')->name('admin.spendingStats');
    Route::post('finance/stats', 'FinanceController@postStats')->name('admin.postStats');

    // working schedule
    Route::get('export/working-schedule', 'Work\WorkingScheduleController@export')->name('export.WorkingSchedule');
    Route::get('working-schedule-list/{orderBy?}/{sortBy?}', 'Work\WorkingScheduleController@show')->name('admin.WorkingSchedule');
    Route::get('working-schedule/{id?}/{del?}', 'Work\WorkingScheduleController@showdetail')->name('admin.WorkingScheduleInfo');
    Route::post('working-schedule-store', 'Work\WorkingScheduleController@store')->name('admin.WorkingScheduleStore');

    //equipment offer
    Route::get('export/equipment-offer', 'Equipment\EquipmentOfferController@export')->name('export.EquipmentOffer');
    Route::get('equipment-offer-list/{orderBy?}/{sortBy?}', 'Equipment\EquipmentOfferController@show')->name('admin.EquipmentOffer');
    Route::get('equipment-offer/{id?}/{del?}', 'Equipment\EquipmentOfferController@showdetail')->name('admin.EquipmentOfferInfo');
    Route::post('equipment-offer-store', 'Equipment\EquipmentOfferController@store')->name('admin.EquipmentOfferStore');
    Route::get('equipment-offer-apr/{id?}/{del?}', 'Equipment\EquipmentOfferController@AprEquipmentOffer')->name('admin.AprEquipmentOffer');

    //task working
    Route::get('work-task', 'TaskWork\TaskWorkController@show')->name('admin.TaskWork');
    Route::get('report-task/{id}', 'TaskWork\TaskWorkController@openReportTaskModal')->name('admin.TaskDetail');
    Route::get('work-task-detail/{id}', 'TaskWork\TaskWorkController@detail')->name('admin.TaskWorkDetail');
    Route::get('add-task/form/{id_pr?}/{id?}', 'TaskWork\TaskWorkController@openAddTaskModal')->name('admin.TaskWorkAdd');
    Route::get('modal-member', 'TaskWork\TaskWorkController@openMemberModal')->name('admin.TaskWorkPopupMember');
    Route::get('modal-error-review/{id}', 'TaskWork\TaskWorkController@openErrorReviewModal')->name('admin.TaskWorkPopupErrorReview');
    Route::get('task-modal/{id}', 'TaskWork\TaskWorkController@openTaskMainModal')->name('admin.TaskWorkModal');
    Route::get('list-doc/{id}', 'TaskWork\TaskWorkController@getAllDoc')->name('admin.TaskWorkDoc');

    //Notification personal
    Route::get('notification-personal', 'DailyReportController@notificationPersonal')->name('admin.NotificationPersonal');
});

	Route::get("queue", function () {
	//	\Illuminate\Support\Facades\Artisan::call('config:clear');
	//	\Illuminate\Support\Facades\Artisan::call('cache:clear');
	//	\Illuminate\Support\Facades\Artisan::call('route:clear');
	//	\Illuminate\Support\Facades\Artisan::call('view:clear');
	//	\Illuminate\Support\Facades\Artisan::call('optimize:clear');
		\Illuminate\Support\Facades\Artisan::call("queue:work --stop-when-empty");
	//	dd("Queue working success");
	});

