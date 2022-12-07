@extends('admin.layouts.default.app')
@section('content')
<section class="content-header">
    <h1 class="page-header">@lang('admin.overtime.list_overtime')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            @include('admin.includes.overview-overtime-search')
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
        @component('admin.component.table')
            @slot('columnsTable')
                <tr>
                    <th class="width5pt">@lang('admin.stt')</th>
                    <th><a class="sort-link" data-link="{{ route("admin.OverviewOvertimes") }}/UserID/" data-sort="{{ $sort_link }}">@lang('admin.user.full_name')</a></th>
                    <th class="width15">Số lần T2-T7</th>
                    <th class="width15">Số lần CN</th>
                    <th class="width15">@lang('admin.overtime.overtime_days')</th>
                    <th class="width15">@lang('admin.overtime.overtime_hours') (h)</th>
                    <th class="width8">@lang('admin.action')</th>
                </tr>
            @endslot
            @slot('dataTable')
                @foreach($list as $item)
                    <tr class="even gradeC" data-id="10184">
                        <td class="text-center">{{ $sort == 'asc' ? ++$stt : $stt-- }}</td>
                        <td class="left-important">{{ $item->FullName }}</td>
                        <td class="text-center"> {{ $item->countTimeNotIsWeeken.' ('. $item->countHourNotIsWeeken.'h)'}}</td>
                        <td class="text-center"> {{ $item->countTimeIsWeeken.' ('.$item->countHourIsWeeken.'h)'}}</td>
                        <td class="text-center"> {{ $item->time }}</td>
                        <td class="right-important">{{ $item->totalHours }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.Overtimes')}}?UserID={{ $item->UserID }}" class="btn btn-default">Xem</a>
                        </td>
                    </tr>
                    @endforeach
            @endslot
            @slot('pageTable')
                {{ $list->appends($query_array)->links() }}
            @endslot
        @endcomponent
        </div>
    </div>
</section>
<script type="text/javascript" async>
    var sTime = '';
    var eTime = '';
    var ProjectID = '';

    $(function () {
        SetDatePicker($('.date'));

        $('.btn-search').click(function () {
            var sDate = moment($('#sdate').val(),'DD/MM/YYYY').format('YYYYMMDD');
            var eDate = moment($('#edate').val(),'DD/MM/YYYY').format('YYYYMMDD');
            var repSDate = sDate.replace(/\D/g, "");
            var repEDate = eDate.replace(/\D/g, "");

            if (repSDate > repEDate && repSDate != '' && repEDate != ''){
                showErrors(['Ngày tìm kiếm không hợp lệ']);
            }else{
                $('#meeting-search-form').submit();
            }
        });

        if($('#s-date input:text').val() != ''){
            sTime = $('#s-date').val();
        }
        if($('#e-date input:text').val() != ''){
            eTime = $('#e-date').val();
        }
        if ($('#select-ProjectID option:selected').val() != ''){
            ProjectID = $('#select-ProjectID option:selected').val();
        }
        $(' tbody tr td:last-child a.btn-default').each(function(i, e) {
            let valueAttr = $(this).attr('href');
            $(this).attr('href', valueAttr+'&ProjectID='+ProjectID+'&OvertimeDate%5B%5D='+sTime+'&OvertimeDate%5B%5D='+eTime+'&Approved=true');
        });
    });

</script>
@endsection
