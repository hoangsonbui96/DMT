@extends('admin.layouts.default.app')
@php
    $canEdit = false;
    $canDelete = false;
@endphp

@can('action', $edit)
    @php
        $canEdit = true;
    @endphp
@endcan

@can('action', $delete)
    @php
        $canDelete = true;
    @endphp
@endcan
@section('content')
<section class="content-header">
    <h1 class="page-header">@lang('admin.interview.interview-schedule')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <form class="form-inline">
                <div class="form-group pull-left margin-r-5">
                    <input type="search" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
                </div>
                <div class="form-group pull-left">
                    <button type="submit" class="btn btn-primary btn-search" id="btn-search">@lang('admin.btnSearch')</button>
                </div>
                <div class="pull-right">
                    @can('action', $add)
                        <button type="button" class="btn btn-primary btn-detail" id="add_interview">@lang('admin.interview.add-interview')</button>
                    @endcan
                </div>
                <div class="clearfix"></div>
            </form>
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12">
            @component('admin.component.table')
                @slot('columnsTable')
                    <tr>
                        <th class="width5pt">@lang('admin.stt')</th>
                        <th class="width12"><a class="sort-link" data-link="{{ route("admin.InterviewJob") }}/FullName/" data-sort="{{ $sort_link }}">@lang('admin.interview.name-inter')</a></th>
                        <th ><a class="sort-link" data-link="{{ route("admin.InterviewJob") }}/Name/" data-sort="{{ $sort_link }}">@lang('admin.interview.name-job')</a></th>
                        <th class="width12"><a class="sort-link" data-link="{{ route("admin.InterviewJob") }}/InterviewDate/" data-sort="{{ $sort_link }}">@lang('admin.interview.date')</a></th>
                        <th><a class="sort-link" data-link="{{ route("admin.InterviewJob") }}/Description/" data-sort="{{ $sort_link }}">@lang('admin.note')</a></th>
                        <th class="width8"><a class="sort-link" data-link="{{ route("admin.InterviewJob") }}/Description/" data-sort="{{ $sort_link }}">@lang('admin.active')</a></th>
                        @if ($canEdit || $canDelete)
                        <th class="width8">@lang('admin.action')</th>
                        @endif
                    </tr>
                @endslot
                @slot('dataTable')
                    @foreach($schedules as $item)
                        <tr class="even gradeC" data-id="">
                            <td class="text-center">{{ $item->id }}</td>
                            <td class ="left-important">{{ $item->FullName }}</td>
                            <td class ="left-important">{{ $item->Name }}</td>
                            <td>{{ FomatDateDisplay($item->InterviewDate, FOMAT_DISPLAY_DATE_TIME) }}</td>
                            <td>{!! nl2br(e($item->Note)) !!}</td>
                            <td class="text-center">
                                @if($item->Approve == 0)
                                    <span class="label label-default">@lang('admin.Unfinished')</span>
                                @else
                                    <span class="label label-success">@lang('admin.Finish')</span>
                                @endif
                            </td>
                            @if ($canEdit || $canDelete)
                            <td class="text-center">
                                @if($item->Approve == 0)
                                <span class="action-col approved-schedule" item-id="{{ $item->id }}" data-toggle="tooltip" title="@lang('admin.interview.approved')"><i class="fa fa-check-square-o" aria-hidden="true"></i></span>
                                @endif
                                @if($canEdit)
                                <span class="action-col update edit update-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                @endif
                                @if($canDelete)
                                <span class="action-col update delete delete-one" item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
                                @endif
                            </td>
                            @endif
                        </tr>
                    @endforeach
                @endslot
                @slot('pageTable')
                    {{ $schedules->appends($query_array)->links() }}
                @endslot
            @endcomponent
            <div id="popupModal"></div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script type="text/javascript" async>
    var ajaxUrl = "{{ route('admin.ScheduleDetail') }}";
    var newTitle = 'Thêm lịch phỏng vấn';
    var updateTitle = 'Sửa lịch phỏng vấn';

    $('.approved-schedule').click(function () {
        var itemId = $(this).attr('item-id');
        ajaxGetServerWithLoader('/admin/changeApproveSchedule/'+itemId, 'POST');
        locationPage();
    });
</script>
@endsection

