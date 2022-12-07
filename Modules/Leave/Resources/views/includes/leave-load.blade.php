@php $temp = 0 @endphp
@foreach ($users_leave as $user)
    @php
        $temp++;
    @endphp
    <tr>
        <td class="text-center">{{ $temp }}</td>
        <td>{{ $user->FullName }}</td>
        <td class="text-center">
            {{ isset($user->SDate) ? FomatDateDisplay($user->SDate, FOMAT_DISPLAY_DAY) : '' }}
        </td>
        <td class="text-center">
            {{ isset($user->OfficialDate) ? FomatDateDisplay($user->OfficialDate, FOMAT_DISPLAY_DAY) : '' }}
        </td>
        <td class="text-center">
            {{ $user->last_year_before != 0 ? number_format($user->last_year_before, 2) : 0 }} <br>
        </td>
        <td class="text-center">
            {{ $user->this_year_before != 0 ? number_format($user->this_year_before, 2) : 0 }}
        </td>
        <td class="td-hover {{ $user->AbsenceSearchMonth != 0 ? 'absence under-line' : '' }}" UserID="{{ $user->id }}" style="position: relative" office-load-date={{$user->OfficialDate}}>
            {{ $user->AbsenceSearchMonth != 0 ? number_format($user->AbsenceSearchMonth, 2) : 0 }}
            {{-- @if($user->AbsenceSearchMonth != 0)
            <i class="fas fa-external-link-alt" style="bottom: 7px; right: 10px; position:absolute"></i>
            @endif --}}
        </td>
        <td class="td-hover {{ $user->late_soon != 0 ? 'late-soon under-line' : '' }}" style="position: relative" UserID="{{ $user->id }}" Office-date={{$user->OfficialDate}}>   
            {{ $user->late_soon != 0 ? number_format($user->late_soon, 2) : 0 }}
            {{-- @if($user->late_soon != 0)
            <i class="fas fa-external-link-alt" style="bottom: 7px; right: 10px; position:absolute"></i>
            @endif --}}
        </td>
        <td class="td-hover {{ $user->no_timekeeping != 0 ? 'nokeeping under-line' : '' }}" UserID="{{ $user->id }}" Office-date="{{$user->OfficialDate}}" Start-date="{{$user->SDate}}"> 
            {{ $user->no_timekeeping != 0 ? number_format($user->no_timekeeping, 2) : 0 }}
        </td>
        <td>
            Năm trước: 
            {{ $user->last_year_after != 0 ? number_format($user->last_year_after, 2) : 0 }} 
            <br>
            Hiện tại: 
            {{ $user->this_year_after != 0 ? number_format($user->this_year_after, 2) : 0 }}
            <br>
            Vượt quá: 
            {{ $user->beyond_time != 0 ? number_format($user->beyond_time, 2) : 0 }}
        </td>
        {{-- <td class="text-center">
            @can('action', $lock)
                <a class="btn btn-success" id="">Chốt</a>
            @endcan
        </td> --}}
    </tr>
@endforeach
<script>
    var title_absence = 'Lý do vắng mặt';
    var title_late_soon = 'Đi muộn - Về sớm không đăng kí';
    var title_nokeeping = 'Không chấm công';

    $('.absence').click(function() {
        $('.loadajax').show();
        var dateTr = $("#date-input").val();
        var date = dateTr.split("/").reverse().join("-");
        var UserID = $(this).attr('UserID');
        var OfficeDate = $(this).attr('office-load-date');
        var Type = 1;

        ajaxGetServerWithLoader('{{route('admin.leaveAbsence')}}', 'POST', {
            date: date,
            UserID: UserID,
            OfficeDate: OfficeDate,
            Type: Type,
        }, function (data) {
            $('.loadajax').hide();
            $('#popupModal').empty().html(data);
            $('.modal-title').html(title_absence);
            $('#modal-absence-list').modal('show');
        });
    });

    $('.nokeeping').click(function() {
        $('.loadajax').show();
        var dateTr = $("#date-input").val();
        var date = dateTr.split("/").reverse().join("-");
        var UserID = $(this).attr('UserID');
        var OfficeDate = $(this).attr('Office-date');
        var StartDate = $(this).attr('Start-date');
        var Type = 1;
        console.log(StartDate);

        ajaxGetServerWithLoader('{{route('admin.leave.notimekeeping_list')}}', 'POST', {
            date: date,
            UserID: UserID,
            OfficeDate: OfficeDate,
            StartDate: StartDate,
            Type: Type,
        }, function (data) {
            $('.loadajax').hide();
            $('#popupModal').empty().html(data);
            $('.modal-title').html(title_nokeeping);
            $('#modal-no-keeping-list').modal('show');
        });
    });

    $('.late-soon').click(function() {
        $('.loadajax').show();
        var dateTr = $("#date-input").val();
        var date = dateTr.split("/").reverse().join("-");
        var UserID = $(this).attr('UserID');
        var OfficeDate = $(this).attr('Office-date');

        console.log(OfficeDate);
        ajaxGetServerWithLoader('{{route('admin.leave.unregistered_list')}}', 'POST', {
            date: date,
            UserID: UserID,
            TypeSelect: 2,
            OfficeDate: OfficeDate,
            Type : 1
        }, function (data) {
            $('.loadajax').hide();
            $('#popupModal').empty().html(data);
            $('.modal-title').html(title_late_soon);
            $('#modal-late-soon-list').modal('show');
        });
    });
</script>