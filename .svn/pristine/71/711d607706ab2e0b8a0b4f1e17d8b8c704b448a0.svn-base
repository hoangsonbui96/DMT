@extends('admin.layouts.default.app')
@section('content')
<section class="content-header">
    <h1 class="page-header">@lang('admin.overtime.list_approve')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <form class="form-inline" action="" id="meeting-search-form" method="">
                <div class="form-group pull-left margin-r-5">
                    <input type="search" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-search" id="btn-search-meeting">@lang('admin.btnSearch')</button>
                </div>
            </form>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            @component('admin.component.table')
                @slot('columnsTable')
                    <tr>
                        <th class="width4">@lang('admin.stt')</th>
                        <th><a class="sort-link" data-link="{{ route("admin.OvertimeListApprove") }}/UserID/" data-sort="{{ $sort_link }}">@lang('admin.user.full_name')</a></th>
                        <th class="width15">@lang('admin.overtime.time_work')</th>
                        <th class="width8" ><a class="sort-link" data-link="{{ route("admin.OvertimeListApprove") }}/BreakTime/" data-sort="{{ $sort_link }}">@lang('admin.overtime.break_time')</a></th>
                        <th class="width8">@lang('admin.overtime.work_hours')</th>
                        <th>@lang('admin.overtime.project')</th>
                        <th>@lang('admin.overtime.content')</th>
                        <th class="width15">@lang('admin.overtime.time_log_work')</th>
                        <th class="width8">@lang('admin.overtime.time_accept_OT')</th>
                        <th class="width8"><a class="sort-link" data-link="{{ route("admin.OvertimeListApprove") }}/created_at/" data-sort="{{ $sort_link }}">@lang('admin.overtime.created_date')</a></th>
                        @can('action', $approve)
                        <th class="width8">@lang('admin.PheDuyet')</th>
                        @endcan
                    </tr>
                @endslot
                @slot('dataTable')
                    @foreach($overtime as $item)
                    <tr class="even gradeC" data-id="">
                        <td class="center-important">{{ $sort == 'desc' ? ++$stt : $stt-- }}</td>
                        <td class="left-important">{{ $item->FullName }}</td>
                        <td class="center-important">
                            @if($item->STime != null && $item->ETime != null)
                                @if((\Carbon\Carbon::parse($item->STime)->format(' d/m/Y')) == (\Carbon\Carbon::parse($item->ETime)->format(' d/m/Y')))
                                    {{ FomatDateDisplay($item->STime, FOMAT_DISPLAY_DAY) }} {{ ' - ' }}
                                        {{ $weekMap[\Carbon\Carbon::parse($item->STime)->dayOfWeek] }}<br>
                                    {{ FomatDateDisplay($item->STime, FOMAT_DISPLAY_TIME) }} ~ {{ FomatDateDisplay($item->ETime, FOMAT_DISPLAY_TIME) }}
                                @else
                                    {{  FomatDateDisplay($item->STime, FOMAT_DISPLAY_DATE_TIME) }} <br> ~ <br>
                                    {{ FomatDateDisplay($item->ETime, FOMAT_DISPLAY_DATE_TIME) }}
                                @endif
                            @else
                                {{ '???' }}
                            @endif
                        </td>

                        <td class="center-important">{{ $item->BreakTime+0 }}</td>

                        @if($item->STime != null && $item->ETime != null)
                            @php
                                $OT_time = \Carbon\Carbon::parse($item->STime)->diffInSeconds(\Carbon\Carbon::parse($item->ETime)) /3600 - $item->BreakTime
                            @endphp
                            <td class="center-important">{{ number_format(($OT_time > 0 ? $OT_time : 0), 2)}}</td>
                        @else
                            <td class="center-important">{{'0.00'}}</td>
                        @endif

                        <td>{{ $item->NameVi }}</td>

                        <td>{!! nl2br(e($item->Content)) !!}</td>

                        <td class="center-important">
                            @if($item->STimeLogOT != null && $item->ETimeLogOT != null)
                                @if((\Carbon\Carbon::parse($item->STimeLogOT)->format(' d/m/Y')) == (\Carbon\Carbon::parse($item->ETimeLogOT)->format(' d/m/Y')))
                                    {{ FomatDateDisplay($item->STimeLogOT, FOMAT_DISPLAY_DAY) }} {{ ' - ' }}
                                        {{ $weekMap[\Carbon\Carbon::parse($item->STimeLogOT)->dayOfWeek] }}<br>
                                    {{ FomatDateDisplay($item->STimeLogOT, FOMAT_DISPLAY_TIME) }} ~ {{ FomatDateDisplay($item->ETimeLogOT, FOMAT_DISPLAY_TIME) }}
                                @else
                                    {{  FomatDateDisplay($item->STimeLogOT, FOMAT_DISPLAY_DATE_TIME) }} <br> ~ <br>
                                    {{ FomatDateDisplay($item->ETimeLogOT, FOMAT_DISPLAY_DATE_TIME) }}
                                @endif
                            @else
                                @if((\Carbon\Carbon::parse($item->STime)->format(' d/m/Y')) == (\Carbon\Carbon::parse($item->ETime)->format(' d/m/Y')))
                                    {{ FomatDateDisplay($item->STime, FOMAT_DISPLAY_DAY) }} {{ ' - ' }}
                                        {{ $weekMap[\Carbon\Carbon::parse($item->STime)->dayOfWeek] }}<br>
                                    {{ FomatDateDisplay($item->STime, FOMAT_DISPLAY_TIME) }} ~ {{ FomatDateDisplay($item->ETime, FOMAT_DISPLAY_TIME) }}
                                @else
                                    {{  FomatDateDisplay($item->STime, FOMAT_DISPLAY_DATE_TIME) }} <br> ~ <br>
                                    {{ FomatDateDisplay($item->ETime, FOMAT_DISPLAY_DATE_TIME) }}
                                @endif
                            @endif
                        </td>

                        <td class="center-important">{{ FomatDateDisplay($item->acceptedTimeOT, FOMAT_DISPLAY_CREATE_DAY) }}</td>

                        <td class="center-important">{{ FomatDateDisplay($item->created_at, FOMAT_DISPLAY_CREATE_DAY) }}</td>
                        @can('action', $approve)
                        <td class="center-important">
                            @if($item->Approved == 0 )
                            <button class="btn btn-success btn-sm btnApr" id="btnApr" id-apr="{{ $item->id }}" type="submit"><i class="fa fa-check" aria-hidden="true"></i></button>
                            <button class="btn btn-danger btn-sm btnDel" id="btnDel" id-apr="{{ $item->id }}" type="submit"><i class="fa fa-times" aria-hidden="true"></i></button>
                            @endif
                        </td>
                        @endcan
                    </tr>
                    @endforeach
                @endslot
                @slot('pageTable')
                    {{ $overtime->appends($query_array)->links() }}
                @endslot
            @endcomponent
        </div>
    </div>
</section>
@endsection
@section('js')
<script type="text/javascript" async>
    var ajaxUrlApr = "{{ route('admin.AprOvertime') }}";
    var unApproveUrl = "{{ route('admin.OvertimeUnapprove') }}";

    $(function () {
        $('.btnApr').click(function () {
            var itemId = $(this).attr('id-apr');
            showConfirm('Bạn có chắc chắn không?',
                function () {
                    ajaxGetServerWithLoader(ajaxUrlApr + '/' + itemId, 'GET', null, function (data) {
                        if (typeof data.errors !== 'undefined') {
                            showErrors(data.errors);
                            return;
                        }
                        showSuccess(data.success);
                        locationPage();
                    });
                }
            );
        });
        $('.btnDel').click(function () {
            var itemId = $(this).attr('id-apr');
            ajaxServer(unApproveUrl, 'GET', null, function (data) {
                $('#popupModal').empty().html(data);
                $('#req-id').val(itemId);
                $('.detail-modal').modal('show');
            })
        });
    });
</script>
@endsection
