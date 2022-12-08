<?php

use Illuminate\Database\Seeder;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    //Quản lý báo cáo
        DB::table('menus')->insert([
            'id'          => 1,
            'ParentId'    => NULL,
            'FontAwesome' => 'glyphicon glyphicon-signal',

            'CssClass'  => '',
            'RouteName' => Null,
            'LangKey'   => 'report_management',
            'Order'     => 1,
            'alias'     => Null
        ]);

        DB::table('menus')->insert([
            'id'          => 2,
            'ParentId'    => 1,
            'FontAwesome' => Null,

            'CssClass'  => '',
            'RouteName' => 'admin.DailyReports',
            'LangKey'   => 'daily_reports',
            'Order'     => 1,
            'alias'     => 'DailyReports',
        ]);

        // Thống kê báo cáo - 2020/05/21
        DB::table('menus')->insert([
            'id'          => 3,
            'ParentId'    => 1,
            'FontAwesome' => null,
            'CssClass'    => null,
            'RouteName'   => 'admin.TotalReport',
            'LangKey'     => 'total_report',
            'Order'       => 4,
            'alias'       => 'TotalReport',
        ]);

        DB::table('menus')->insert([
            'id'          => 4,
            'ParentId'    => 1,
            'FontAwesome' => Null,

            'CssClass'  => '',
            'RouteName' => 'admin.GeneralReports',
            'LangKey'   => 'general_reports',
            'Order'     => 3,
            'alias'     => 'DailyReportSummaries',
        ]);

        //Báo cáo hằng năm
        DB::table('menus')->insert([
            'id'          => 5,
            'ParentId'    => 1,
            'FontAwesome' => Null,

            'CssClass'  => '',
            'RouteName' => 'admin.YearlyReports',
            'LangKey'   => 'yearly-reports',
            'Order'     => 5,
            'alias'     => 'YearlyReports',
        ]);

    //quản lý nhân viên
        DB::table('menus')->insert([
            'id'          => 6,
            'ParentId'    => Null,
            'FontAwesome' => 'fa fa-users',

            'CssClass'  => '',
            'RouteName' => Null,
            'LangKey'   => 'users',
            'Order'     => 2
        ]);
        DB::table('menus')->insert([
            'id'          => 7,
            'ParentId'    => 6,
            'FontAwesome' => Null,

            'CssClass'  => '',
            'RouteName' => 'admin.Users',
            'LangKey'   => 'users_list',
            'Order'     => 1,
            'alias'     => 'UserList',
        ]);

        //21/4/2020-Dung-
        //Danh sách đối tác
        DB::table('menus')->insert([
            'id'          => 48,
            'ParentId'    => 6,
            'FontAwesome' => Null,

            'CssClass'  => '',
            'RouteName' => 'admin.Partner',
            'LangKey'   => 'partner_list',
            'Order'     => 2,
            'alias'     => 'PartnerList',
        ]);


        DB::table('menus')->insert([
            'id'          => 8,
            'ParentId'    => Null,
            'FontAwesome' => 'glyphicon glyphicon-home',
            'CssClass'    => '',
            'RouteName'   => Null,
            'LangKey'     => 'rooms',
            'Order'       => 3,

        ]);

    //phòng ban
        DB::table('menus')->insert([
            'id'          => 9,
            'ParentId'    => 8,
            'FontAwesome' => Null,
            'CssClass'    => '',
            'RouteName'   => 'admin.Rooms',
            'LangKey'     => 'room_list',
            'Order'       => 3,
            'alias'       => 'RoomList',
        ]);
        DB::table('menus')->insert([
            'id'          => 10,
            'ParentId'    => 8,
            'FontAwesome' => Null,
            'CssClass'    => '',
            'RouteName'   => 'admin.MeetingSchedules',
            'LangKey'     => 'meeting_schedules',
            'Order'       => 3,
            'alias'       => 'MeetingList',
        ]);

        DB::table('menus')->insert([
            'id'          => 11,
            'ParentId'    => null,
            'FontAwesome' => 'fa fa-file-code-o',
            'CssClass'    => null,
            'RouteName'   => 'admin.Projects',
            'LangKey'     => 'projects',
            'Order'       => 4,
            'alias'       => 'ProjectManagement',
        ]);
        DB::table('menus')->insert([
            'id'          => 12,
            'ParentId'    => null,
            'FontAwesome' => 'fa fa-calendar-check-o',
            'CssClass'    => '',
            'RouteName'   => null,
            'LangKey'     => 'overtimes',
            'Order'       => 5
        ]);

        DB::table('menus')->insert([
            'id'          => 47,
            'ParentId'    => 12,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.OvertimeListApprove',
            'LangKey'     => 'apr_overtimes',
            'Order'       => 1,
            'alias'       => 'OvertimeDetailsApprove',
        ]);
        //Danh sách chi tiết
        DB::table('menus')->insert([
            'id'          => 14,
            'ParentId'    => 12,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.Overtimes',
            'LangKey'     => 'detail_overtimes',
            'Order'       => 2,
            'alias'       => 'OvertimeDetails',
        ]);

        //Danh sách tổng quan
        DB::table('menus')->insert([
            'id'          => 13,
            'ParentId'    => 12,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.OverviewOvertimes',
            'LangKey'     => 'overview_overtimes',
            'Order'       => 3,
            'alias'       => 'OvertimeOverviews',
        ]);

        //Báo cáo tổng hợp
        DB::table('menus')->insert([
            'id'          => 15,
            'ParentId'    => 12,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.ReportOvertimes',
            'LangKey'     => 'report_overtimes',
            'Order'       => 4,
            'alias'       => 'OvertimeReports',
        ]);

    //quản lý vắng mặt
        DB::table('menus')->insert([
            'id'          => 23,
            'ParentId'    => null,
            'FontAwesome' => 'fa fa-calendar',
            'CssClass'    => '',
            'RouteName'   => null,
            'LangKey'     => 'absence',
            'Order'       => 8
        ]);
        DB::table('menus')->insert([
            'id'          => 24,
            'ParentId'    => 23,
            'FontAwesome' => 'fa fa-calendar',
            'CssClass'    => '',
            'RouteName'   => 'admin.Absences',
            'LangKey'     => 'absences',
            'Order'       => 2,
            'alias'       => 'AbsenceList',
        ]);
        DB::table('menus')->insert([
            'id'          => 25,
            'ParentId'    => 23,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.AbsenceManagement',
            'LangKey'     => 'absence_management',
            'Order'       => 1,
            'alias'       => 'AbsenceManagement',
        ]);
        DB::table('menus')->insert([
            'id'          => 26,
            'ParentId'    => 23,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.AbsenceReports',
            'LangKey'     => 'absence_reports',
            'Order'       => 4,
            'alias'       => 'AbsenceReports',
        ]);
        DB::table('menus')->insert([
            'id'          => 46,
            'ParentId'    => 23,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.AbsencesListApprove',
            'LangKey'     => 'absences-list-approve',
            'Order'       => 3,
            'alias'       => 'AbsenceListApprove',
        ]);

        // DB::table('menus')->insert([
        //     'id' => 16,
        //     'ParentId' => null,
        //     'FontAwesome' => 'fa fa-address-card',
        //     'CssClass' => '',
        //     'RouteName' => null,
        //     'LangKey' => 'profile_skill',
        //     'Order' => 6
        // ]);
        DB::table('menus')->insert([
            'id'          => 17,
            'ParentId'    => 6,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.EmployerSkills',
            'LangKey'     => 'list_skill',
            'Order'       => 1,
            'alias'       => 'ProfileSkillList',
        ]);
        // DB::table('menus')->insert([
        //     'id' => 18,
        //     'ParentId' => 16,
        //     'FontAwesome' => null,
        //     'CssClass' => '',
        //     'RouteName' => 'admin.ProfileSkill',
        //     'LangKey' => 'self_skill',
        //     'Order' => 2,
        //     'alias' => 'ProfileSkill',
        // ]);

        DB::table('menus')->insert([
            'id'          => 19,
            'ParentId'    => null,
            'FontAwesome' => 'glyphicon glyphicon-compressed',
            'CssClass'    => '',
            'RouteName'   => 'admin.MasterData',
            'LangKey'     => 'master_data',
            'Order'       => 999,
            'alias'       => 'MasterData',
        ]);

    //sự kiện

        DB::table('menus')->insert([
            'id'          => 20,
            'ParentId'    => null,
            'FontAwesome' => 'glyphicon glyphicon-star',
            'CssClass'    => '',
            'RouteName'   => 'admin.Events',
            'LangKey'     => 'event',
            'Order'       => 7,
            'alias'       => 'EventList',
        ]);
        // DB::table('menus')->insert([
        //     'id' => 21,
        //     'ParentId' => 20,
        //     'FontAwesome' => null,
        //     'CssClass' => '',
        //     'RouteName' => 'admin.Events',
        //     'LangKey' => 'event_list',
        //     'Order' => 1,
        //     'alias' => 'EventList',
        // ]);
//        DB::table('menus')->insert([
//            'id' => 22,
//            'ParentId' => 20,
//            'FontAwesome' => null,
//            'CssClass' => '',
//            'RouteName' => 'admin.EventReports',
//            'LangKey' => 'total_report',
//            'Order' => 7
//        ]);

        //trang thiet bi
        DB::table('menus')->insert([
            'id'          => 27,
            'ParentId'    => null,
            'FontAwesome' => 'fa fa-laptop',
            'CssClass'    => '',
            'RouteName'   => null,
            'LangKey'     => 'equipment',
            'Order'       => 9
        ]);
        DB::table('menus')->insert([
            'id'          => 28,
            'ParentId'    => 27,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.Equipment',
            'LangKey'     => 'equipment_list',
            'Order'       => 1,
            'alias'       => 'EquipmentList',
        ]);
        DB::table('menus')->insert([
            'id'          => 29,
            'ParentId'    => 27,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.EquipmentType',
            'LangKey'     => 'equipment_type',
            'Order'       => 2,
            'alias'       => 'EquipmentType',
        ]);
        DB::table('menus')->insert([
            'id'          => 30,
            'ParentId'    => 27,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.EquipmentHistories',
            'LangKey'     => 'equipment_handover',
            'Order'       => 3,
            'alias'       => 'EquipmentRotation',
        ]);
        DB::table('menus')->insert([
            'id'          => 31,
            'ParentId'    => 27,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.EquipmentRegistrations',
            'LangKey'     => 'equipment_change',
            'Order'       => 4,
            'alias'       => 'EquipmentRegistrations',
        ]);
        //Phân công công việc
//        DB::table('menus')->insert([
//            'id' => 32,
//            'ParentId' => null,
//            'FontAwesome' => 'glyphicon glyphicon-list-alt',
//            'CssClass' => '',
//            'RouteName' => 'admin.Tasks',
//            'LangKey' => 'tasks',
//            'Order' => 4
//        ]);


        //Tuyển dụng
        DB::table('menus')->insert([
            'id'          => 33,
            'ParentId'    => null,
            'FontAwesome' => 'fa fa-handshake-o',
            'CssClass'    => '',
            'RouteName'   => null,
            'LangKey'     => 'interview_job',
            'Order'       => 4
        ]);
        DB::table('menus')->insert([
            'id'          => 34,
            'ParentId'    => 33,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.InterviewJob',
            'LangKey'     => 'interview_jobs',
            'Order'       => 1,
            'alias'       => 'JobCandidate',
        ]);
        DB::table('menus')->insert([
            'id'          => 35,
            'ParentId'    => 33,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.InterviewSchedule',
            'LangKey'     => 'interview_schedule',
            'Order'       => 2,
            'alias'       => 'Schedule',
        ]);

        DB::table('menus')->insert([
            'id'          => 36,
            'ParentId'    => null,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.RoleSetup',
            'LangKey'     => 'role_setup',
            'Order'       => 11,
            'alias'       => 'RoleGroups',
        ]);
        DB::table('menus')->insert([
            'id'          => 39,
            'ParentId'    => null,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.ViewRole',
            'LangKey'     => 'view_role',
            'Order'       => 11,
            'alias'       => 'ViewRole',
        ]);

        //cham cong
        DB::table('menus')->insert([
            'id'          => 37,
            'ParentId'    => null,
            'FontAwesome' => 'glyphicon glyphicon-time',
            'CssClass'    => '',
            'RouteName'   => 'admin.Timekeeping',
            'LangKey'     => 'timekeeping',
            'Order'       => 6,
            'alias'       => 'Timekeeping',
        ]);
//        DB::table('menus')->insert([
//            'id' => 38,
//            'ParentId' => 37,
//            'FontAwesome' => null,
//            'CssClass' => '',
//            'RouteName' => 'admin.Timekeeping',
//            'LangKey' => 'timekeeping_month',
//            'Order' => 11,
//            'alias' => 'Timekeeping',
//        ]);

    //lịch làm việc
        DB::table('menus')->insert([
            'id'          => 40,
            'ParentId'    => null,
            'FontAwesome' => 'fa fa-calendar',
            'CssClass'    => '',
            'RouteName'   => null,
            'LangKey'     => 'calendar',
            'Order'       => 2
        ]);
        DB::table('menus')->insert([
            'id'          => 41,
            'ParentId'    => 40,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.CalendarData',
            'LangKey'     => 'CalendarManagement',
            'Order'       => 1,
            'alias'       => 'CalendarManagement',
        ]);
        DB::table('menus')->insert([
            'id'          => 42,
            'ParentId'    => 40,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.Calendar',
            'LangKey'     => 'Calendar',
            'Order'       => 2,
            'alias'       => 'Calendar',
        ]);


    // Danh Sach Van Ban
        DB::table('menus')->insert([
            'id'          => 43,
            'ParentId'    => null,
            'FontAwesome' => 'fa fa-calendar',
            'CssClass'    => '',
            'RouteName'   => null,
            'LangKey'     => 'document',
            'Order'       => 19
        ]);
        DB::table('menus')->insert([
            'id'          => 44,
            'ParentId'    => 43,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.DocumentList',
            'LangKey'     => 'document_list',
            'Order'       => 1,
            'alias'       => 'DocumentList'
        ]);
        DB::table('menus')->insert([
            'id'          => 45,
            'ParentId'    => 43,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.DocumentView',
            'LangKey'     => 'document_view',
            'Order'       => 1,
            'alias'       => 'DocumentView'
        ]);
        DB::table('menus')->insert([
            'id'          => 51,
            'ParentId'    => null,
            'FontAwesome' => null,
            'CssClass'    => '',
            'RouteName'   => 'admin.optimize',
            'LangKey'     => 'optimize',
            'Order'       => 11,
            'alias'       => 'optimize',
        ]);

        DB::table('menus')->insert([
            'id' => 52,
            'ParentId' => null,
            'FontAwesome' => 'fa fa-money',
            'CssClass' => '',
            'RouteName' => null,
            'LangKey' => 'financial',
            'Order' => 12,
        ]);
        DB::table('menus')->insert([
            'id' => 53,
            'ParentId' => 52,
            'FontAwesome' => null,
            'CssClass' => '',
            'RouteName' => 'admin.spendingList',
            'LangKey' => 'spendingList',
            'Order' => 1,
            'alias' =>  'spendingList'
        ]);

        DB::table('menus')->insert([
            'id' => 54,
            'ParentId' => 52,
            'FontAwesome' => null,
            'CssClass' => '',
            'RouteName' => 'admin.spendingStats',
            'LangKey' => 'spendingStats',
            'Order' => 1,
            'alias' =>  'spendingStats'
        ]);



    //mở rộng
        DB::table('menus')->insert([
            'id'          => 56,
            'ParentId'    => null,
            'FontAwesome' => 'fa fa-cogs',
            'CssClass'    => null,
            'RouteName'   => null,
            'LangKey'     => 'extend',
            'Order'       => 13,
            'alias'       => null,
        ]);

        DB::table('menus')->insert([
            'id'          => 57,
            'ParentId'    => 56,
            'FontAwesome' => null,
            'CssClass'    => null,
            'RouteName'   => 'admin.ListMenus',
            'LangKey'     => 'list_menus',
            'Order'       => 1,
            'alias'       => 'ListMenus',
        ]);
    }
}
