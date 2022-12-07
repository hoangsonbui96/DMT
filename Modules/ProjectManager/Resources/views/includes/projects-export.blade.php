<table>
    <thead>
        <tr>
            <th colspan="12" style="color: white">Danh sách dự án</th>
        </tr>
        <tr>
            <th>STT</th>
            <th>Tên dự án</th>
            <th>Đối tác</th>
            <th>Quản lý</th>
            <th>Thành viên</th>
            <th>Số Phase</th>
            <th>Số Job</th>
            <th>Số Task hoàn thành/ Tổng task</th>
            <th>Ngày bắt đầu</th>
            <th>Ngày kết thúc</th>
            <th>Số giờ làm(h)</th>
            <th>Tiến độ(%)</th>
            <th>Trạng thái</th>
        </tr>
    </thead>
    <tbody>
        @foreach($projects as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->NameVi }}</td>
            <td>{{ $item->Customer }}</td>
            <td>
                {{$item->leaders->implode('FullName',', ')}}
            </td>
            <td>
                {{$item->members->implode('FullName',', ')}}
            </td>
            <td>{{count($item->phases)}}</td>
            <td>{{count($item->jobs)}}</td>
            <td>{{count($item->doneTasks). '/ '. count($item->tasks)}}</td>
            <td>{{FomatDateDisplay($item->StartDate, FOMAT_DISPLAY_DAY)}}</td>
            <td>{{FomatDateDisplay($item->EndDate, FOMAT_DISPLAY_DAY)}}</td>
            <td>{{$item->workedHours}}</td>
            <td>{{$item->progress}}</td>
            <td>{{ $item->Active == 1? 'Đang hoạt động' : 'Đã dừng'}}</td>
        </tr>
        @endforeach
    </tbody>
</table>