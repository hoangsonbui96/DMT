<?php

use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_data')->insert([
            'DataKey' => 'VM',
            'Name'  =>  'Nghỉ phép',

            'TypeName' => 'Vắng mặt',
            'DataValue' => 'VM001',
            'DataDisplayOrder' => 1,
            'DataDescription' => 'Vắng Mặt - Nghỉ phép',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([
            'DataKey' => 'VM',
            'Name'  =>  'Nghỉ trừ lương',
            'TypeName' => 'Vắng mặt',
            'DataValue' => 'VM002',
            'DataDisplayOrder' => 2,
            'DataDescription' => 'Vắng mặt - Nghỉ trừ lương',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([

            'DataKey' => 'VM',
            'Name'  =>  'Đi muộn',
            'TypeName' => 'Vắng mặt',
            'DataValue' => 'VM003',
            'DataDisplayOrder' => 3,
            'DataDescription' => 'Vắng mặt - Đi muộn',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'VM',
            'Name'  =>  'Về sớm',
            'TypeName' => 'Vắng mặt',
            'DataValue' => 'VM004',
            'DataDisplayOrder' => 4,
            'DataDescription' => 'Vắng mặt - Về sớm',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'VM',
            'Name'  =>  'Nghỉ ốm',
            'TypeName' => 'Vắng mặt',
            'DataValue' => 'VM005',
            'DataDisplayOrder' => 5,
            'DataDescription' => 'Vắng mặt - Nghỉ ốm',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'VM',
            'Name'  =>  'Đi công tác',
            'TypeName' => 'Vắng mặt',
            'DataValue' => 'VM006',
            'DataDisplayOrder' => 6,
            'DataDescription' => 'Vắng mặt - Đi công tác',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'VM',
            'Name'  =>  'Khác',
            'TypeName' => 'Vắng mặt',
            'DataValue' => 'VM007',
            'DataDisplayOrder' => 7,
            'DataDescription' => 'Vắng mặt - Khác',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'CV',
            'Name'  =>  'Mở',
            'TypeName' => 'Phân công công việc',
            'DataValue' => 'CV001',
            'DataDisplayOrder' => 1,
            'DataDescription' => 'Phân công công việc - Mở',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'CV',
            'Name'  =>  'Đang xây dựng',
            'TypeName' => 'Phân công công việc',
            'DataValue' => 'CV002',
            'DataDisplayOrder' => 2,
            'DataDescription' => 'Phân công công việc - Đang xây dựng',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'CV',
            'Name'  =>  'Đã hoàn thành',
            'TypeName' => 'Phân công công việc',
            'DataValue' => 'CV003',
            'DataDisplayOrder' => 3,
            'DataDescription' => 'Phân công công việc - Đã hoàn thành',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'SK',
            'Name'  =>  'Chỉ chọn một câu trả lời',
            'TypeName' => 'Loại câu hỏi - Sự kiện',
            'DataValue' => 'SK001',
            'DataDisplayOrder' => 1,
            'DataDescription' => 'Loại câu hỏi - Sự kiện - Chỉ chọn một câu trả lời',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'SK',
            'Name'  =>  'Chọn nhiều câu trả lời',
            'TypeName' => 'Loại câu hỏi - Sự kiện',
            'DataValue' => 'SK002',
            'DataDisplayOrder' => 2,
            'DataDescription' => 'Loại câu hỏi - Sự kiện - Chọn nhiều câu trả lời',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'SK',
            'Name'  =>  'Có thể thêm câu trả lời',
            'TypeName' => 'Loại câu hỏi - Sự kiện',
            'DataValue' => 'SK003',
            'DataDisplayOrder' => 3,
            'DataDescription' => 'Loại câu hỏi - Sự kiện - Có thể thêm câu trả lời',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'SK',
            'Name'  =>  'Có thể thêm câu trả lời',
            'TypeName' => 'Loại câu hỏi - Sự kiện',
            'DataValue' => 'SK004',
            'DataDisplayOrder' => 4,
            'DataDescription' => 'Loại câu hỏi - Sự kiện - Có thể thêm câu trả lời',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        //tinh trang thiet bi
        DB::table('master_data')->insert([
            'DataKey' => 'TB',
            'Name'  =>  'Đang hoạt động',
            'TypeName' => 'Tình trạng - Thiết bị',
            'DataValue' => 'TB002',
            'DataDisplayOrder' => 1,
            'DataDescription' => 'Tình trạng - Thiết bị - Đang hoạt động',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'TB',
            'Name'  =>  'Không hoạt động',
            'TypeName' => 'Tình trạng - Thiết bị',
            'DataValue' => 'TB001',
            'DataDisplayOrder' => 2,
            'DataDescription' => 'Tình trạng - Thiết bị - Không hoạt động',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);



        DB::table('master_data')->insert([
            'DataKey' => 'TB',
            'Name'  =>  'Đang bảo hành',
            'TypeName' => 'Tình trạng - Thiết bị',
            'DataValue' => 'TB003',
            'DataDisplayOrder' => 3,
            'DataDescription' => 'Tình trạng - Thiết bị - Đang bảo hành',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'TB',
            'Name'  =>  'Hỏng',
            'TypeName' => 'Tình trạng - Thiết bị',
            'DataValue' => 'TB004',
            'DataDisplayOrder' => 4,
            'DataDescription' => 'Tình trạng - Thiết bị - Hỏng',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'TB',
            'Name'  =>  'Mất',
            'TypeName' => 'Tình trạng - Thiết bị',
            'DataValue' => 'TB005',
            'DataDisplayOrder' => 5,
            'DataDescription' => 'Tình trạng - Thiết bị - Mất',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'TB',
            'Name'  =>  'Đã thanh lý',
            'TypeName' => 'Tình trạng - Thiết bị',
            'DataValue' => 'TB006',
            'DataDisplayOrder' => 6,
            'DataDescription' => 'Tình trạng - Thiết bị - Đã thanh lý',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'BC',
            'Name'  =>  'Read Document',
            'TypeName' => 'Type of work - Báo cáo',
            'DataValue' => 'BC001',
            'DataDisplayOrder' => 1,
            'DataDescription' => 'Type of work - Báo cáo - Read Document',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'BC',
            'Name'  =>  'Code',
            'TypeName' => 'Type of work - Báo cáo',
            'DataValue' => 'BC002',
            'DataDisplayOrder' => 2,
            'DataDescription' => 'Type of work - Báo cáo - Code',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([
            'DataKey' => 'BC',
            'Name'  =>  'Test',
            'TypeName' => 'Type of work - Báo cáo',
            'DataValue' => 'BC003',
            'DataDisplayOrder' => 3,
            'DataDescription' => 'Type of work - Báo cáo - Test',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([
            'DataKey' => 'BC',
            'Name'  =>  'FixBug',
            'TypeName' => 'Type of work - Báo cáo',
            'DataValue' => 'BC004',
            'DataDisplayOrder' => 4,
            'DataDescription' => 'Type of work - Báo cáo - FixBug',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([
            'DataKey' => 'BC',
            'Name'  =>  'Study',
            'TypeName' => 'Type of work - Báo cáo',
            'DataValue' => 'BC005',
            'DataDisplayOrder' => 5,
            'DataDescription' => 'Type of work - Báo cáo - Study',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([
            'DataKey' => 'BC',
            'Name'  =>  'Management',
            'TypeName' => 'Type of work - Báo cáo',
            'DataValue' => 'BC006',
            'DataDisplayOrder' => 6,
            'DataDescription' => 'Type of work - Báo cáo - Management',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([
            'DataKey' => 'BC',
            'Name'  =>  'Others',
            'TypeName' => 'Type of work - Báo cáo',
            'DataValue' => 'BC007',
            'DataDisplayOrder' => 7,
            'DataDescription' => 'Type of work - Báo cáo - Others',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'PV',
            'Name'  =>  'Tuyển tester TN',
            'TypeName' => 'Tên job - Lịch phỏng vấn',
            'DataValue' => 'PV001',
            'DataDisplayOrder' => 1,
            'DataDescription' => 'Tên job - Lịch phỏng vấn - Tuyển tester TN',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'PV',
            'Name'  =>  'Tuyển phiên dịch tiếng Nhật',
            'TypeName' => 'Tên job - Lịch phỏng vấn',
            'DataValue' => 'PV002',
            'DataDisplayOrder' => 2,
            'DataDescription' => 'Tên job - Lịch phỏng vấn - Tuyển phiên dịch tiếng Nhật',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'PV',
            'Name'  =>  'Tuyển dụng Javascript',
            'TypeName' => 'Tên job - Lịch phỏng vấn',
            'DataValue' => 'PV003',
            'DataDisplayOrder' => 3,
            'DataDescription' => 'Tên job - Lịch phỏng vấn - Tuyển dụng Javascript',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([
            'DataKey' => 'PV',
            'Name'  =>  'Tuyển gấp LTV',
            'TypeName' => 'Tên job - Lịch phỏng vấn',
            'DataValue' => 'PV004',
            'DataDisplayOrder' => 4,
            'DataDescription' => 'Tên job - Lịch phỏng vấn - Tuyển gấp LTV',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'DT',
            'Name'  =>  'Giám đốc',
            'TypeName' => 'Đối tác',
            'DataValue' => 'DT001',
            'DataDisplayOrder' => 1,
            'DataDescription' => 'Đối tác - Giám đốc',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'DT',
            'Name'  =>  'Trưởng Phòng',
            'TypeName' => 'Đối tác',
            'DataValue' => 'DT002',
            'DataDisplayOrder' => 2,
            'DataDescription' => 'Đối tác - Trưởng phòng',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'DT',
            'Name'  =>  'Nhân Viên',
            'TypeName' => 'Đối tác',
            'DataValue' => 'DT003',
            'DataDisplayOrder' => 3,
            'DataDescription' => 'Đối tác - Nhân Viên',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([
            'DataKey' => 'DT',
            'Name'  =>  'Khác',
            'TypeName' => 'Đối tác',
            'DataValue' => 'DT004',
            'DataDisplayOrder' => 4,
            'DataDescription' => 'Đối tác - Khác',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        //Document
        DB::table('master_data')->insert([
            'DataKey' => 'TL',
            'Name'  =>  'Nội Quy',
            'TypeName' => 'Tài liệu',
            'DataValue' => 'TL001',
            'DataDisplayOrder' => 1,
            'DataDescription' => 'Tài liệu - Nội Quy',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([
            'DataKey' => 'TL',
            'Name'  =>  'Chế tài',
            'TypeName' => 'Tài liệu',
            'DataValue' => 'TL002',
            'DataDisplayOrder' => 2,
            'DataDescription' => 'Tài liệu - Chế tài',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([
            'DataKey' => 'TL',
            'Name'  =>  'Tài liệu',
            'TypeName' => 'Tài liệu',
            'DataValue' => 'TL003',
            'DataDisplayOrder' => 3,
            'DataDescription' => 'Tài liệu - Tài liệu',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([
            'DataKey' => 'TL',
            'Name'  =>  'Khác',
            'TypeName' => 'Tài liệu',
            'DataValue' => 'TL004',
            'DataDisplayOrder' => 4,
            'DataDescription' => 'Tài liệu - Khác',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);

        DB::table('master_data')->insert([
            'DataKey' => 'CT',
            'Name'  =>  'Công ty AIC',
            'TypeName' => 'Công ty',
            'DataValue' => 'CT001',
            'DataDisplayOrder' => 1,
            'DataDescription' => 'Công ty - AIC',
            'PermissionEdit'=>1,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([
            'DataKey' => 'EM',
            'Name'  =>  'Mail cc của vắng mặt',
            'TypeName' => 'Email',
            'DataValue' => 'EM001',
            'DataDisplayOrder' => 1,
            'DataDescription' => '',
            'PermissionEdit'=>0,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([
            'DataKey' => 'EM',
            'Name'  =>  'Mail cc của giờ làm thêm',
            'TypeName' => 'Email',
            'DataValue' => 'EM002',
            'DataDisplayOrder' => 2,
            'DataDescription' => '',
            'PermissionEdit'=>0,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([
            'DataKey' => 'EM',
            'Name'  =>  'Mail cc của lịch công tác',
            'TypeName' => 'Email',
            'DataValue' => 'EM004',
            'DataDisplayOrder' => 4,
            'DataDescription' => '',
            'PermissionEdit'=>0,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([
            'DataKey' => 'WT',
            'Name'  =>  '08:30',
            'TypeName' => 'Giờ làm việc',
            'DataValue' => 'WT001',
            'DataDisplayOrder' => 1,
            'DataDescription' => '17:30',
            'PermissionEdit'=>0,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([
            'DataKey' => 'WT',
            'Name'  =>  '12:00',
            'TypeName' => 'Giờ làm việc',
            'DataValue' => 'WT002',
            'DataDisplayOrder' => 2,
            'DataDescription' => '13:00',
            'PermissionEdit'=>0,
            'PermissionDelete'=>1
        ]);


        DB::table('master_data')->insert([
            'DataKey' => 'NWD',
            'Name'  =>  '1',
            'TypeName' => 'Not write daily',
            'DataValue' => 'NWD001',
            'DataDisplayOrder' => 1,
            'DataDescription' => 'Danh sách không cần viết báo cáo',
            'PermissionEdit'=>0,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([
            'DataKey' => 'FN',
            'Name'  =>  'Tiền điện',
            'TypeName' => 'Chi tiêu',
            'DataValue' => 'FN001',
            'DataDisplayOrder' => 1,
            'DataDescription' => 'Chi tiêu - Tiền điện',
            'PermissionEdit'=>0,
            'PermissionDelete'=>1
        ]);
        DB::table('master_data')->insert([
            'DataKey' => 'FN',
            'Name'  =>  'Tiền nước',
            'TypeName' => 'Chi tiêu',
            'DataValue' => 'FN002',
            'DataDisplayOrder' => 1,
            'DataDescription' => 'Chi tiêu - Tiền nước',
            'PermissionEdit'=>0,
            'PermissionDelete'=>1
        ]);
    }
}
