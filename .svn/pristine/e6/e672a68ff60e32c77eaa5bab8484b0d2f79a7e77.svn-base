@extends('admin.layouts.default.app')
@php
	$canEdit = false;
	$canDelete = false;
	$canApprove = false;
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
@can('action', $approve)
    @php
        $canApprove = true;
    @endphp
@endcan
@section('content')
<section class="content-header">
	<h1 class="page-header">@lang('admin.overtime.management')</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			@include('admin.includes.overtime-search')
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			@component('admin.component.table')
				@slot('columnsTable')
					<tr>
						<th class="width4"><a class="sort-link" data-link="{{ route("admin.Overtimes") }}/id/" data-sort="{{ $sort_link }}">@lang('admin.stt')</a></th>
						<th><a class="sort-link" data-link="{{ route("admin.Overtimes") }}/UserID/" data-sort="{{ $sort_link }}">@lang('admin.user.full_name')</a></th>
						<th class="width15">@lang('admin.overtime.time_work')</th>
						<th class="width8" ><a class="sort-link" data-link="{{ route("admin.Overtimes") }}/BreakTime/" data-sort="{{ $sort_link }}">@lang('admin.overtime.break_time')</a></th>
						<th class="width8">@lang('admin.overtime.work_hours')</th>
						<th>@lang('admin.overtime.project')</th>
						<th>@lang('admin.overtime.content')</th>
						<th class="width15">@lang('admin.overtime.time_log_work')</th>
						<th class="width8">@lang('admin.overtime.time_accept_OT')</th>
						<th><a class="sort-link" data-link="{{ route("admin.Overtimes") }}/created_at/" data-sort="{{ $sort_link }}">@lang('admin.overtime.created_date')</a></th>
						<th>@lang('admin.overtime.approved_date') / @lang('admin.overtime.approved_person')</th>
						<th>@lang('admin.overtime.status')</th>
						@if ($canEdit || $canDelete)
						    <th class="width8">@lang('admin.action')</th>
						@endif
					</tr>
				@endslot
				@slot('dataTable')
					@foreach($list as $item)
						<tr class="even gradeC" data-id="10184">
							<td class="text-center">{{ $sort == 'desc' ? ++$stt : $stt-- }}</td>
							<td  class="left-center">{{ $item->FullName }}</td>
							<td class="text-center">
								@if($item->STime != null && $item->ETime != null)
									@if ((\Carbon\Carbon::parse($item->STime)->format(' d/m/Y')) == (\Carbon\Carbon::parse($item->ETime)->format(' d/m/Y')))
										{{ FomatDateDisplay($item->STime, FOMAT_DISPLAY_DAY) }} {{ ' - ' }}
										{{ $weekMap[\Carbon\Carbon::parse($item->STime)->dayOfWeek] }}<br>
										{{ FomatDateDisplay($item->STime, FOMAT_DISPLAY_TIME) }} ~ {{ FomatDateDisplay($item->ETime, FOMAT_DISPLAY_TIME) }}
									@else
										{{  FomatDateDisplay($item->STime, FOMAT_DISPLAY_DATE_TIME) }} <br> ~ <br> {{ FomatDateDisplay($item->ETime, FOMAT_DISPLAY_DATE_TIME) }}
									@endif
								@else
									{{ '???' }}
								@endif
							</td>
							<td class="text-center" >{{ $item->BreakTime+0 }}</td>

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

							<td class="text-center">{{  FomatDateDisplay($item->created_at, FOMAT_DISPLAY_CREATE_DAY) }}</td>
							<td class="text-center">{{ isset($item->ApprovedDate) ? FomatDateDisplay($item->ApprovedDate, FOMAT_DISPLAY_CREATE_DAY): ''}} <br> {{ $item->NameUpdatedBy }}</td>
							<td class="action-col text-center">{!! ApprovedDisplayHtml($item->Approved,'','data-toggle="tooltip" title="'.$item->Note.'"') !!} </td>
							@if ($canEdit || $canDelete)
							<td class="text-center">
                            @can('action', $approve)
                                @if ($canEdit && ($canApprove || (!$canApprove && $item->Approved != 1)))
                                    <span class="action-col update edit update-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                @endif
                                @if ($canDelete && ($canApprove || (!$canApprove && $item->Approved != 1)))
                                    <span class="action-col update delete delete-one"  item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
                                @endif
                            @else
                                @if($item->UserID === Auth::user()->id)
									@if ($item->Approved != 1)
                                    <span class="action-col update edit update-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
									<span class="action-col update delete delete-one"  item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
                                	@endif
								@endif
                            @endcan
							</td>
							@endif
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
@endsection
@section('js')
<script type="text/javascript" async>
	var ajaxUrl = "{{ route('admin.OvertimeInfo') }}";
	var newTitle = 'Thêm giờ làm thêm';
	var updateTitle = 'Cập nhật giờ làm thêm';
	var confirmTxt = 'Bạn có chắc chắn?';
</script>
@endsection
