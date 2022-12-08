<table>
    <thead >
        <tr>
            <th colspan="7">@lang('admin.working-schedule.header')</th>
        </tr>
        <tr>
            <th>@lang('admin.stt')</th>
            <th>@lang('admin.working-schedule.assign-id')</th>
            <th>@lang('admin.working-schedule.date')</th>
            <th>@lang('admin.working-schedule.content')</th>
            <th>@lang('admin.partner.address')</th>
            <th>@lang('admin.working-schedule.note')</th>
            <th>@lang('admin.working-schedule.user-id')</th>
        </tr>
    </thead>
    <tbody>
    @foreach($working_schedule as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ str_replace('<br>', ',', \App\Http\Controllers\Admin\Work\WorkingScheduleController::getListAssignUser($item->AssignID, $users, true)) }}</td>
            <td>{{ FomatDateDisplay($item->Date, FOMAT_DISPLAY_DAY) . ' ' . FomatDateDisplay($item->STime, FOMAT_DISPLAY_TIME) . ' - ' . FomatDateDisplay($item->ETime, FOMAT_DISPLAY_TIME) }}</td>
            <td>{{ $item->Content }}</td>
            <td>{{ $item->Address }}</td>
            <td>{{ $item->Note }}</td>
            <td>{{ App\User::find($item->UserID)->FullName }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
