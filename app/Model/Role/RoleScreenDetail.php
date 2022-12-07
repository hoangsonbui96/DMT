<?php

namespace App\Model\Role;

use Illuminate\Database\Eloquent\Model;

class RoleScreenDetail extends Model
{
    protected $table = 'role_screen_details';

    const LIST_ROLE = [
        'AbsenceList' => [
            'view'      => 'AbsenceList',
            'add'       => 'AbsenceListAdd',
            'edit'      => 'AbsenceListEdit',
            'delete'    => 'AbsenceListDelete',
            'export'    => 'AbsenceListExport',
        ],
        'AbsenceListApprove' => [
            'view'      => 'AbsenceListApprove',
            'approve'   => 'ListApprove',
        ],
        'AbsenceManagement' => [
            'view'      => 'AbsenceManagement',
            'add'       => 'AbsenceManagementAdd',
            'edit'      => 'AbsenceManagementEdit',
            'delete'    => 'AbsenceManagementDelete',
        ],
        'AbsenceReports' => [
            'view'      => 'AbsenceReports',
            'export'    => 'AbsenceReportsExport',
        ],
        'Calendar' => [
            'view'      => 'Calendar',
            'add'       => 'CalendarAdd',
            'edit'      => 'CalendarEdit',
            'delete'    => 'CalendartDelete',
            'export'    => 'Calendarexport',
            'copy'      => 'CalendarCopy',
        ],
        'CalendarManagement' => [
            'view'      => 'CalendarManagement',
            'add'       => 'CalendarManagementAdd',
            'edit'      => 'CalendarManagementEdit',
            'delete'    => 'CalendarManagementDelete',
        ],
        'DailyReports' => [
            'view'      => 'DailyReports',
            'add'       => 'DailyReportsAdd',
            'edit'      => 'DailyReportsEdit',
            'delete'    => 'DailyReportsDelete',
        ],
        'DailyReportSummaries' => [
            'view'      => 'DailyReportSummaries',
            'export'    => 'DailyReportSummariesExport',
        ],
        'DocumentList' => [
            'view'      => 'DocumentList',
            'add'       => 'DocumentListAdd',
            'edit'      => 'DocumentListEdit',
            'delete'    => 'DocumentListDelete',
        ],
        'DocumentView' => [
            'view'      => 'DocumentView',
        ],
        'EquipmentList' => [
            'view'      => 'EquipmentList',
            'add'       => 'EquipmentListAdd',
            'edit'      => 'EquipmentListEdit',
            'delete'    => 'EquipmentListDelete',
            'export'    => 'EquipmentListExport',
            'export_qr' => 'EquipmentListExportQR',
        ],
        'EquipmentOffer' => [
            'view'      => 'EquipmentOffer',
            'add'       => 'EquipmentOfferAdd',
            'edit'      => 'EquipmentOfferEdit',
            'delete'    => 'EquipmentOfferDelete',
            'export'    => 'EquipmentOfferExport',
            'approve'   => 'EquipmentOfferAppr',
        ],
        'EquipmentRegistrations' => [
            'view'      => 'EquipmentRegistrations',
            'edit'      => 'EquipmentRegistrationsEdit',
            'delete'    => 'EquipmentRegistrationsDelete',
            'approve'   => 'EquipmentRegistrationsApprove',
        ],
        'EquipmentRotation' => [
            'view'      => 'EquipmentRotation',
            'can'       => 'EquipmentRotationCan',
        ],
        'EquipmentType' => [
            'view'      => 'EquipmentType',
            'add'       => 'EquipmentTypeAdd',
            'edit'      => 'EquipmentTypeEdit',
            'delete'    => 'EquipmentTypeDelete',
        ],
        'EventList' => [
            'view'      => 'EventList',
            'add'       => 'EventListAdd',
            'edit'      => 'EventListEdit',
            'delete'    => 'EventListDelete',
            'view-stat' => 'EventListStats',
            'vote'      => 'EventListVote',
        ],
        'JobCandidate' => [
            'view'      => 'JobCandidate',
            'add'       => 'JobCandidateAdd',
            'edit'      => 'JobCandidateEdit',
            'delete'    => 'JobCandidateDelete',
        ],
        'MasterData' => [
            'view'      => 'MasterData',
            'add'       => 'MasterDataAdd',
            'edit'      => 'MasterDataEdit',
            'delete'    => 'MasterDataDelete',
        ],
        'MeetingList' => [
            'view'      => 'MeetingList',
            'add'       => 'MeetingListAdd',
            'edit'      => 'MeetingListEdit',
            'delete'    => 'MeetingListDelete',
        ],
        'OvertimeDetails' => [
            'view'      => 'OvertimeDetails',
            'add'       => 'OvertimeDetailsAdd',
            'edit'      => 'OvertimeDetailsEdit',
            'delete'    => 'OvertimeDetailsDelete',
        ],
        'OvertimeDetailsApprove' => [
            'view'      => 'OvertimeDetailsApprove',
            'approve'   => 'OvertimeListApprove',
        ],
        'OvertimeOverviews' => [
            'view'      => 'OvertimeOverviews',
            'add'       => 'OvertimeOverviewsAdd',
            'edit'      => 'OvertimeOverviewsEdit',
            'delete'    => 'OvertimeOverviewsDelete',
            'search'    => 'OvertimeOverviewsSearch',
            'export'    => 'OvertimeOverviewsExport	',
        ],
        'OvertimeReports' => [
            'view'      => 'OvertimeReports',
            'export'    => 'OvertimeReportsExport',
        ],
        'PartnerList' => [
            'view'      => 'PartnerList',
            'add'       => 'PartnerListAdd',
            'edit'      => 'PartnerListEdit',
            'delete'    => 'PartnerListDelete',
            'export'    => 'PartnerListExport',
        ],
        'ProfileSkill' => [
            'view'      => 'ProfileSkill',
            'edit'      => 'ProfileSkillEdit',
            'delete'    => 'ProfileSkillDelete',
        ],
        'ProfileSkillList' => [
            'view'      => 'ProfileSkillList',
            'edit'      => 'ProfileSkillListEdit',
            'delete'    => 'ProfileSkillListDelete',
        ],
        'ProfileUser' => [
            'edit'      => 'ProfileUserEdit',
        ],
        'ProjectManagement' => [
            'view'      => 'ProjectManagement',
            'add'       => 'ProjectManagementAdd',
            'edit'      => 'ProjectManagementEdit',
            'delete'    => 'ProjectManagementDelete',
        ],
        'RoomList' => [
            'view'      => 'RoomList',
            'add'       => 'RoomListAdd',
            'edit'      => 'RoomListEdit',
            'delete'    => 'RoomListDelete',
        ],
        'RoomReports' => [
            'view'      => 'RoomReports',
            'add'       => 'RoomReportsAdd',
            'edit'      => 'RoomReportsEdit',
            'delete'    => 'RoomReportsDelete',
            'export'    => 'RoomReportsExport',
        ],
        'Schedule' => [
            'view'      => 'Schedule',
            'add'       => 'ScheduleAdd',
            'edit'      => 'ScheduleEdit',
            'delete'    => 'ScheduleDelete',
            'export'    => 'spendingExport',
        ],
        'spendingList' => [
            'view'      => 'spendingList',
            'add'       => 'spendingAdd',
            'edit'      => 'spendingEdit',
            'delete'    => 'spendingDelete',
            'export'    => 'spendingExport',
        ],
        'spendingStats' => [
            'view'      => 'spendingStats',
        ],
        'Timekeeping' => [
            'view'      => 'Timekeeping',
            'add'       => 'TimekeepingAdd',
            'edit'      => 'TimekeepingEdit',
            'delete'    => 'TimekeepingDelete',
            'import'    => 'TimekeepingImport',
            'export'    => 'TimekeepingExport',
        ],
        'TimekeepingNew' => [
            'view'      => 'TimekeepingNew',
            'add'       => 'TimekeepingAdd',
            'edit'      => 'TimekeepingEdit',
            'delete'    => 'TimekeepingDelete',
            'import'    => 'TimekeepingImport',
            'export'    => 'TimekeepingExport',
        ],
        'TimekeepingHistory' => [
            'view'      => 'TimekeepingHistory',
        ],
        'TotalReport' => [
            'view'      => 'TotalReport',
            'export'    => 'TotalReportExport',
        ],
        'UserInfo' => [
            'view'      => 'UserInfo',
            'edit'       => 'UserInfoSave',
        ],
        'UserList' => [
            'view'      => 'UserList',
            'add'       => 'UserListAdd',
            'edit'      => 'UserListEdit',
            'delete'    => 'UserListDelete',
            'group'     => 'UserListGroupEdit',
            'export'    => 'UserListExport',
        ],
        'WorkingSchedule' => [
            'view'      => 'WorkingSchedule',
            'add'       => 'WorkingScheduleAdd',
            'edit'      => 'WorkingScheduleEdit',
            'delete'    => 'WorkingScheduleDelete',
            'export'    => 'WorkingScheduleExport',
        ],
        'YearlyReports' => [
            'view'      => 'YearlyReports',
            'export'    => 'YearlyReportsExport',
        ],
        'TaskWorking' => [
            'view'      => 'TaskWorking',
            'add'       => 'TaskWorkingAdd',
            'delete'    => 'TaskWorkingDelete',
            'export'    => 'TaskWorkingExport',
            'edit'      =>  'TaskWorkingEdit',
            'review'    =>  'TaskWorkingReview'
        ],
    ];
}
