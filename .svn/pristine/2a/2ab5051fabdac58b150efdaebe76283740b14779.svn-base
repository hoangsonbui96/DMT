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
    <h1 class="page-header">@lang('admin.room.rooms_management')</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<form class="form-inline" id ="meeting-search-form">
				<div class="form-group pull-left margin-r-5">
					<input type="search" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
				</div>
                <div class="form-group pull-left">
                    <button type="button" class="btn btn-primary btn-search" id="btn-search-meeting">@lang('admin.btnSearch')</button>
                </div>
                <div class="form-group pull-right">
					@can('action', $add)
					<button type="button" class="btn btn-primary btn-detail" id="add-new-room-btn">@lang('admin.room.add_new_room')</button>
					@endcan
				</div>
				<div class="clearfix"></div>
			</form>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			@component('admin.component.table')
				@slot('columnsTable')
					<tr>
						<th class="width5"><a class="sort-link" data-link="{{ route("admin.Rooms") }}/ID/" data-sort="{{ $sort_link }}">@lang('admin.stt')</a></th>
						<th><a class="sort-link" data-link="{{ route("admin.Rooms") }}/Name/" data-sort="{{ $sort_link }}">@lang('admin.room.name')</a></th>
						@if ($canEdit)
						<th class="width8"><a class="sort-link" data-link="{{ route("admin.Rooms") }}/MeetingRoomFlag/" data-sort="{{ $sort_link }}">@lang('admin.room.meeting_room')</a></th>
						<th class="width15"><a class="sort-link" data-link="{{ route("admin.Rooms") }}/Active/" data-sort="{{ $sort_link }}">@lang('admin.room.status')</a></th>
						@endif
						@if ($canEdit || $canDelete)
						<th class="width8">@lang('admin.action')</th>
						@endif
					</tr>
				@endslot
				@slot('dataTable')
					@foreach($rooms as $item)
					<tr class="even gradeC" data-id="10184">
						<td class="text-center">{{ $sort == 'desc' ? ++$stt : $stt-- }}</td>
						<td class ="left-important">{{ $item->Name }}</td>
						@if ($canEdit)
						<td class="text-center">
							<input class='action-col activeCalendar' item-id="{{ $item->id }}M" type='checkbox' value="{{ $item->MeetingRoomFlag }}" {{ (isset($item->MeetingRoomFlag) && $item->MeetingRoomFlag == 1) ? 'checked' : ''}}>
						</td>
						<td class="text-center">
							<input class='action-col activeCalendar' item-id="{{ $item->id }}A" type='checkbox' value="{{ $item->Active }}" {{ (isset($item->Active) && $item->Active == 1) ? 'checked' : ''}}>
						</td>
						@endif
						@if ($canEdit || $canDelete)
						<td class="text-center">
							@if ($canEdit)
							<span class="action-col update edit update-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
							@endif
							@if ($canDelete)
							<span class="action-col update delete delete-one"  item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
							@endif
						</td>
						@endif
					</tr>
					@endforeach
				@endslot
				@slot('pageTable')
					{{ $rooms->appends($query_array)->links() }}
				@endslot
			@endcomponent
			<!-- /.box -->
		</div>
	</div>
</section>
@endsection
@section('js')
<script type="text/javascript" async>
	var ajaxUrl = "{{ route('admin.RoomInfo') }}";
	var newTitle = 'Thêm phòng mới';
	var updateTitle = 'Cập nhật phòng';
	var edit = '<?php echo $canEdit ? $canEdit : 0 ?>';
	if(edit == 0) {
		$("input[class=activeCalendar]").prop("disabled", true);
	}
	$(function () {
        $('.btn-search').click(function () {
            $('#meeting-search-form').submit();
        });
    })
</script>
@endsection
