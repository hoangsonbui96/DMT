<table>
    <thead >
        <tr>
            <th colspan="9">@lang('admin.absence.absence')</th>
        </tr>
        <tr>
            <th>@lang('admin.stt')</th>
            <th>@lang('admin.absence.fullname')</th>
            <th>@lang('admin.absence.start')</th>
            <th>@lang('admin.absence.end')</th>
            <th>@lang('admin.absence.time')</th>
            <th>@lang('admin.absence.reason')</th>
            <th>@lang('admin.absence.absentDate')</th>
            <th>@lang('admin.absence.approvedDate')</th>
            <th>@lang('admin.absence.approve')</th>
        </tr>
    </thead>
    <tbody>
    @foreach($absence as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->FullName }}</td>
            <td>{{ FomatDateDisplay($item->SDate, FOMAT_DISPLAY_DATE_TIME) }}</td>
            <td>{{ FomatDateDisplay($item->EDate, FOMAT_DISPLAY_DATE_TIME) }}</td>
            <td>{{ number_format($item->TotalTimeOff/60, 2) }}</td>
            <td>{{ '('.$item->Name.')'.' '.$item->Reason }}</td>
            <td>{{ FomatDateDisplay($item->created_at, FOMAT_DISPLAY_DAY) }}</td>
            <td>{!! AddSpecial("<br/>", FomatDateDisplay($item->ApprovedDate, FOMAT_DISPLAY_DATE_TIME), e($item->NameUpdateBy)) !!}</td>
            <td> {!! ApprovedDisplayHtml($item->Approved) !!}</td>
        </tr>
    @endforeach
    </tbody>
</table>
