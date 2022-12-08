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
    <h1 class="page-header">@lang('admin.calendar.management')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <form class="form-inline" id ="meeting-search-form">
                <div class="form-group pull-left margin-r-5">
                    <div class="input-group">
                        <input type="search" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
                    </div>
                </div>
                <div class="form-group pull-left" >
                    <div class="input-group">
                        <button type="button" class="btn btn-primary btn-search" id="btn-search-meeting">@lang('admin.btnSearch')</button>
                    </div>
                </div>
                @can('action',$add)
                    <div class="form-group pull-right">
                        <button type="button" class="btn btn-primary btn-detail" id="add-new-room-btn">@lang('admin.calendar.add')</button>
                    </div>
                @endcan
                <div class="clearfix"></div>
            </form>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            @component('admin.component.table')
                @slot('columnsTable')
                        <tr>
                            <th class="width5"><a class="sort-link" data-link="{{ route("admin.CalendarData") }}/ID/" data-sort="{{ $sort_link }}">@lang('admin.stt')</a></th>
                            <th><a class="sort-link" data-link="{{ route("admin.CalendarData") }}/Name/" data-sort="{{ $sort_link }}">@lang('admin.calendar.name')</a></th>
                            <th><a class="sort-link" data-link="{{ route("admin.CalendarData") }}/Title/" data-sort="{{ $sort_link }}">@lang('admin.calendar.title')</a></th>
                            @can('action',$edit)
                            <th class="width8"><a class="sort-link" data-link="{{ route("admin.CalendarData") }}/Active/" data-sort="{{ $sort_link }}">@lang('admin.calendar.active')</a></th>
                            @endcan
                            <th class="width8">@lang('admin.action')</th>
                        </tr>
                @endslot
                @slot('dataTable')
                        @foreach($calendarData as $item)
                        <tr class="even gradeC" data-id="10184">
                            <td class="text-center">{{ $sort == 'desc' ? ++$stt : $stt-- }}</td>
                            <td class = "left-important">{{ $item->Name }}</td>
                            <td class = "left-important">{{ $item->Title }}</td>
                            @can('action',$edit)
                            <td class="text-center">
                                <input class='action-col activeCalendar' item-id="{{ $item->id }}" type='checkbox' value="{{ $item->Active }}" {{ (isset($item->Active) && $item->Active == 1) ? 'checked' : ''}}>
                            </td>
                            @endcan
                            <td class="text-center">
                                <span class="action-col update edit view-one-calendar" item-id="{{ $item->id }}" item-active="{{ $item->Active }}" ><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                @can('action',$edit)
                                <span class="action-col update edit update-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                @endcan
                                @can('action',$delete)
                                <span class="action-col update delete delete-one"  item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    @endslot
                @slot('pageTable')
                    {{ $calendarData->appends($query_array)->links() }}
                @endslot
            @endcomponent
        </div>
    </div>
</section>
@endsection
@section('js')
<script type="text/javascript" async>
    var ajaxUrl = "{{ route('admin.CalendarDataItem') }}";
    var newTitle = 'Thêm lịch làm việc';
    var updateTitle = 'Cập nhật lịch làm việc';
    var edit = <?php echo $canEdit ?  $canEdit : 0 ?>;
    if(edit == 0){
        $("input[class=activeCalendar]").prop("disabled", true);
    }
    $(function () {
        $('.btn-search').click(function () {
            $('#meeting-search-form').submit();
        });
    })
</script>
@endsection
