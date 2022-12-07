
@if($view == 'detail')
    <table>
        <thead>
        <tr>
            <th colspan="9">@lang('admin.user.users_management')</th>
        </tr>
        <tr>
            <th >@lang('admin.stt')</th>
            <th >@lang('admin.user.idfm')</th>
            <th >@lang('admin.user.username')</th>
            <th >@lang('admin.user.full_name')</th>
            <th >@lang('admin.user.phone_number')</th>
            <th >@lang('admin.user.email')</th>
            <th>@lang('admin.user.birthday')</th>
            <th >@lang('admin.user.age')</th>
            <th >@lang('admin.user.status')</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td >{{ $loop->iteration }}</td>
                <td >{{ $user->IDFM }}</td>
                <td >{{ $user->username }}</td>
                <td >{{ $user->FullName }}</td>
                <td >{{ $user->Tel }}</td>
                <td >{{ $user->email }}</td>
                <td >{{ FomatDateDisplay($user->Birthday, FOMAT_DISPLAY_DAY) }}</td>
                <td >{{ \Carbon\Carbon::parse($user->Birthday)->age }}</td>
                <td >{{ $user->Active }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <table>
        <thead>
        <tr>
            <th colspan="11">@lang('admin.user.users_management')</th>
        </tr>
        <tr>
            <th>@lang('admin.stt')</th>
            <th>@lang('admin.user.idfm')</th>
            <th>@lang('admin.user.full_name')</th>
            <th>@lang('admin.user.phone_number')</th>
            <th>@lang('admin.user.birthday')</th>
            <th>@lang('admin.user.age')</th>
            <th>@lang('admin.user.room')</th>
            <th>@lang('admin.user.join_date')</th>
            <th>@lang('admin.user.official_date')</th>
            <th>@lang('admin.user.work_time')</th>
            <th>@lang('admin.user.end_time_of_day')</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td >{{ $loop->iteration }}</td>
                <td >{{ $user->IDFM }}</td>
                <td >{{ $user->FullName }}</td>
                <td >{{ $user->Tel }}</td>
                <td >{{ FomatDateDisplay($user->Birthday, FOMAT_DISPLAY_DAY) }}</td>
                <td >{{ \Carbon\Carbon::parse($user->Birthday)->age }}</td>
                <td >{{ 'Phòng '.$user->Name }}</td>
                <td >{{ FomatDateDisplay($user->SDate, FOMAT_DISPLAY_DAY) }}</td>
                <td >{{ FomatDateDisplay($user->OfficialDate, FOMAT_DISPLAY_DAY) }}</td>
                <td >{{ isset($user->STimeOfDay) ? \Carbon\Carbon::parse($user->STimeOfDay)->format('H:i') : ''}}</td>
                <td >{{ isset($user->ETimeOfDay) ? \Carbon\Carbon::parse($user->ETimeOfDay)->format('H:i') : ''}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif


