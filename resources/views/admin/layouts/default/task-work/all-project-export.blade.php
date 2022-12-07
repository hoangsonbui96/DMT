<table>
    <thead >
    <tr>
        <th colspan="14" style="color: white">Danh sách dự án</th>
    </tr>
    <tr>
        <th>STT</th>
        <th>Tên dự án</th>
        <th>Đối tác</th>
        <th>Quản lý</th>
        <th>Thành viên</th>
        <th>Chưa thực hiện</th>
        <th>Đang thực hiện</th>
        <th>Hoàn thành</th>
        <th>Đang duyệt</th>
        <th>Tổng số task</th>
        <th>Số giờ làm(h)</th>
        <th>Tiến độ(%)</th>
        <th>Ngày bắt đầu</th>
        <th>Ngày kết thúc</th>
    </tr>
    </thead>
    <tbody>
    @foreach($value as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->NameVi }}</td>
            <td>{{ $item->Customer }}</td>
            <td>{{ $item->Leader }}</td>
            <td>{{ $item->Member }}</td>
            <td>{{ $item->TaskNotFinish }}</td>
            <td>{{ $item->TaskWorking }}</td>
            <td>{{ $item->TaskFinish }}</td>
            <td>{{ $item->TaskReview }}</td>
            <td>{{ $item->TaskNotFinish + $item->TaskWorking + $item->TaskFinish + $item->TaskReview }}</td>
            <td>{{ $item->TotalHours }}</td>
            <td>{{ $item->Progress }}</td>
            <td>{{ $item->StartDate }}</td>
            <td>{{ $item->EndDate }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
