@extends('admin.layouts.default.app')
@php
	$canAdd = false;
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

@can('action', $add)
	@php
		$canAdd = true;
	@endphp
@endcan
<style>
	#meeting-search-form .form-group {
		margin-top: 5px;
	}
    .notifications-menu .dropdown-toggle { height: 50px; padding-top: 16px; }
    #btn-search-meeting{
        height: 33.99px;
    }
	table {
		font-size: 14px !important;
	}
</style>

@push('pageJs')
	<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
@endpush

@section('content')
<section class="content-header">
	<h1 class="page-header">@lang('admin.room.meeting_room')</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			@include('admin.includes.meeting-search')
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			@component('admin.component.table')
				@slot('columnsTable')
					<tr>
						<th class="width5pt">@lang('admin.stt')</th>
						<th><a class="sort-link" data-link="{{ route("admin.MeetingSchedules") }}/Purpose/" data-sort="{{ $sort_link }}">@lang('admin.meeting.purpose')</a></th>
						<th class="width12"><a class="sort-link" data-link="{{ route("admin.MeetingSchedules") }}/RegisterID/" data-sort="{{ $sort_link }}">@lang('admin.meeting.register')</a></th>
						<th class="width12"><a class="sort-link" data-link="{{ route("admin.MeetingSchedules") }}/MeetingHostID/" data-sort="{{ $sort_link }}">@lang('admin.meeting.host')</a></th>
						<th class="width12"><a class="sort-link" data-link="{{ route("admin.MeetingSchedules") }}/RoomID/" data-sort="{{ $sort_link }}">@lang('admin.meeting.meeting_room')</a></th>
						<th class="width8"><a class="sort-link" data-link="{{ route("admin.MeetingSchedules") }}/MeetingDate/" data-sort="{{ $sort_link }}">@lang('admin.meeting.day_meeting')</a></th>
						<th class="width8">@lang('admin.meeting.hour_meeting')</th>
						<th class="width5pt">@lang('admin.meeting.total_time_meeting')</th>
						<th class="width8">@lang('admin.status')</th>
						@if ($canEdit || $canDelete || $canAdd)
							<th class="width8pt">@lang('admin.action')</th>
						@endif
					</tr>
					@endslot
				@slot('dataTable')
					@foreach($meetings as $item)
						<tr class="even gradeC" data-id="10184">
							<td class="text-center">{{ $sort == 'desc' ? ++$stt : $stt-- }}</td>
							<td>{!! nl2br(e($item->Purpose)) !!}</td>
							<td>{{ $item->register }} </td>
							<td>{{ $item->host }}</td>
							<td>{{ $item->roomName }}</td>
							<td class="text-center">{{FomatDateDisplay($item->MeetingDate, FOMAT_DISPLAY_DAY)}}</td>
							<td class="text-center">{{FomatDateDisplay($item->MeetingTimeFrom,FOMAT_DISPLAY_TIME)}}
								- {{FomatDateDisplay($item->MeetingTimeTo,FOMAT_DISPLAY_TIME)}}</td>
							<td class="text-center">{{ $item->diffHours }}</td>
							{{-- @if($ckeck == '0') --}}
							@if((\Carbon\Carbon::parse($item->MeetingDate.' '.$item->MeetingTimeFrom)->greaterThan(\Carbon\Carbon::now())))
								<td>Chưa bắt đầu</td>
							@elseif(\Carbon\Carbon::parse($item->MeetingDate.' '.$item->MeetingTimeTo)->lessThan(\Carbon\Carbon::now()))
								<td>Đã kết thúc</td>
							@else
								<td>Đang diễn ra</td>
							@endif
							{{-- @elseif($ckeck == '2')
								<td>Đã kết thúc</td>
							@else
								<td>{{$ckeck == '1' ? "Đang diễn ra" : "Chưa bắt đầu"}}</td>
							@endif --}}
							@if ($canEdit || $canDelete ||$canAdd)
								<td class="text-center">
									@if ($canEdit)
										@if(!(\Carbon\Carbon::parse($item->MeetingDate.' '.$item->MeetingTimeTo)->lessThan(\Carbon\Carbon::now())))
										<span class="action-col update edit update-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o"></i></span>
										@endif
									@endif
									@if ($canAdd)
									<span class="action-col update copy-one"  item-id="{{ $item->id }}"><i class="fa fa-copy"></i></span>
									@endif
									@if ($canDelete)
										@if(!(\Carbon\Carbon::parse($item->MeetingDate.' '.$item->MeetingTimeTo)->lessThan(\Carbon\Carbon::now())))
										<span class="action-col update delete delete-one"  item-id="{{ $item->id }}"><i class="fa fa-times"></i></span>
										@endif
									@endif
								</td>
							@endif
						</tr>
					@endforeach
					@endslot
				@slot('pageTable')
					{{ $meetings->appends($query_array)->links() }}
				@endslot
			@endcomponent
		</div>
	</div>
</section>

@endsection
@section('js')
	<script type="text/javascript" async>
		var ajaxUrl = "{{ route('admin.MeetingInfo') }}";
		var newTitle = 'Thêm ca họp mới';
		var updateTitle = 'Cập nhật phòng';
		var copyTitle = 'Sao chép phòng họp';
	</script>
@endsection
