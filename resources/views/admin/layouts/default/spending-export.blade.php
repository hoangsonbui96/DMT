<table>
    <thead>
    <tr>
        <th colspan="7">Danh sách chi tiêu</th>
    </tr>
    <tr>
        <th>@lang('admin.stt')</th>
        <th>Ngày chi</th>
        <th>Danh mục chi tiêu</th>
        <th>Số tiền (VNĐ)</th>
        <th>Người chi</th>
        <th>Mô tả</th>
        <th>@lang('admin.note')</th>
    </tr>
    </thead>
    <tbody>
    @foreach($spendingList as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <th>{{ FomatDateDisplay($item->date, FOMAT_DISPLAY_DAY) }}</th>
            <td>{{ $item->categoryName }}</td>
            <td>{{ number_format($item->expense,0,",",".") }}</td>
            <td>{{ $item->FullName }}</td>
            <td>{{ $item->desc }}</td>
            <td>{{ $item->note }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
