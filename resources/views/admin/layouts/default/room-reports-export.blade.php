<table>
    <thead>
    <tr>
        <th colspan="7" style="font-family:'Times New Roman'">Danh sách báo cáo theo phòng ban</th>
    </tr>
    <tr>
        <th ><b>@lang('admin.stt')</b></th>
        <th ><b>@lang('admin.times')</b></th>
        <th ><b>Công việc tuần</b></th>
        <th ><b>Công việc tồn đọng</b></th>
        <th ><b>Đề xuất</b></th>
        <th ><b>@lang('admin.note')</b></th>
        <th ><b>Thời gian sửa</b></th>


    </tr>
    </thead>
    <tbody>
    @foreach($list as $item)
        <tr>
            <td >{{ $loop->iteration }}</td>

            <td>{{ \Carbon\Carbon::parse($item->SDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($item->EDate)->format('d/m/Y') }}</td>

            <td> {!! nl2br(e($item->week_work)) !!} </td>
            <td>{!! nl2br(e($item->unfinished_work)) !!}</td>
            <td> {!! nl2br(e($item->Contents)) !!} </td>
            <td>  {!! nl2br(e($item->noted)) !!}  </td>
            <td>  {{ isset($item->DateUpdate) ? \Carbon\Carbon::parse($item->DateUpdate)->format('d/m/Y') : ''}} </td>
        </tr>
    @endforeach
    </tbody>
</table>

