<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleScreensTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('role_screens')->insert([
            'name' => 'Báo cáo hàng ngày',
            'alias' => 'DailyReports',
        ]);
        //28/4/2020--Dung--
        // DB::table('role_screens')->insert([
        //     'name' => 'Báo cáo theo phòng ban',
        //     'alias' => 'RoomReports',
        // ]);


        DB::table('role_screens')->insert([
            'name' => 'Báo cáo tổng hợp',
            'alias' => 'DailyReportSummaries',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Báo cáo hằng năm',
            'alias' => 'YearlyReports',
        ]);


        DB::table('role_screens')->insert([
            'name' => 'Danh sách nhân viên',
            'alias' => 'UserList',
        ]);
         //21/4/2020--Dung--
        DB::table('role_screens')->insert([
            'name' => 'Danh sách đối tác',
            'alias' => 'PartnerList',
        ]);
        // DB::table('role_screens')->insert([
        //     'name' => 'Thông tin cá nhân',
        //     'alias' => 'ProfileUser',
        // ]);
        DB::table('role_screens')->insert([
            'name' => 'Danh sách phòng ban',
            'alias' => 'RoomList',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Danh sách ca họp',
            'alias' => 'MeetingList',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Quản lý dự án',
            'alias' => 'ProjectManagement',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Tổng quan Giờ làm thêm',
            'alias' => 'OvertimeOverviews',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Chi tiết Giờ làm thêm',
            'alias' => 'OvertimeDetails',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Báo cáo tổng hợp Giờ làm thêm',
            'alias' => 'OvertimeReports',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Duyệt giờ làm thêm',
            'alias' => 'OvertimeDetailsApprove',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Danh sách Hồ sơ năng lực',
            'alias' => 'ProfileSkillList',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Hồ sơ năng lực cá nhân',
            'alias' => 'ProfileSkill',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Sự kiện',
            'alias' => 'EventList',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Danh sách Vắng mặt',
            'alias' => 'AbsenceList',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Báo cáo tổng hợp Vắng mặt',
            'alias' => 'AbsenceReports',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Danh sách Thiết bị',
            'alias' => 'EquipmentList',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Loại thiết bị',
            'alias' => 'EquipmentType',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Luân chuyển thiết bị',
            'alias' => 'EquipmentRotation',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Đăng ký thay đổi thiết bị',
            'alias' => 'EquipmentRegistrations',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Master Data',
            'alias' => 'MasterData',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Quản lý nhóm',
            'alias' => 'RoleGroups',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Chấm công',
            'alias' => 'Timekeeping',
        ]);
        //Bằng
        DB::table('role_screens')->insert([
            'name' => 'Công việc và ứng viên',
            'alias' => 'JobCandidate',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Lịch phỏng vấn',
            'alias' => 'Schedule',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Quản lý vắng mặt',
            'alias' => 'AbsenceManagement',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Duyệt đơn xin nghỉ',
            'alias' => 'AbsenceListApprove',
        ]);
        //Tien
        DB::table('role_screens')->insert([
            'name' => 'Danh sách Lịch',
            'alias' => 'CalendarManagement',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Lịch AKB',
            'alias' => 'Calendar',
        ]);


        //Danh Sách văn Bản
        DB::table('role_screens')->insert([
            'name' => 'Danh sách văn bản',
            'alias' => 'DocumentList',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Nội quy, Chế tài',
            'alias' => 'DocumentView',
        ]);

        DB::table('role_screens')->insert([
            'name' => 'Optimize',
            'alias' => 'optimize',
        ]);

        // Thống kê báo cáo
        DB::table('role_screens')->insert([
            'name' => 'Thống kê báo cáo',
            'alias' => 'TotalReport',
        ]);
        //financial
        DB::table('role_screens')->insert([
            'name' => 'Danh sách Chi tiêu',
            'alias' => 'spendingList',
        ]);
        DB::table('role_screens')->insert([
            'name' => 'Thống kê chi tiêu',
            'alias' => 'spendingStats',
        ]);

        DB::table('role_screens')->insert([
            'name' => 'Xem Quyền',
            'alias' => 'ViewRole',
        ]);

        DB::table('role_screens')->insert([
            'name' => 'Tùy biến menu',
            'alias' => 'ListMenus',
        ]);
    }
}
