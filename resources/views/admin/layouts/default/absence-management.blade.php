<style>
	.table-absence tr th, .table-absence tr td{
		background: white;
		text-align: center;
	}
	.table-absence tr td{
		font-weight: normal;
	}
	#absence-search-form .form-group:not(:last-child), #area-btn button:not(:last-child) {
		margin-right: 3px;
	}
</style>
@extends('admin.layouts.default.app')

@section('content')
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
<section class="content-header">
	<h1 class="page-header">@lang('admin.absence.absence-management')</h1>
</section>
<section class="content">
	<div class="row">

		<div class="col-lg-12 col-md-12 col-sm-12">
			<form class="form-inline" id="absence-search-form">
				<div class="form-group pull-left">
					<input type="search" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
				</div>
                <div class="form-group pull-left">
                    <div class="input-group search date">
                        <input type="text" class="form-control dtpicker" id="s-date" placeholder="Ngày bắt đầu" name="Date[]" autocomplete="off"
                               value="{{ isset($request['Date']) ? $request['Date'][0] : \Carbon\Carbon::now()->startOfMonth()->format(FOMAT_DISPLAY_DAY) }}">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-th"></span>
                        </div>
                    </div>
                </div>

                <div class="form-group pull-left">
                    <div class="input-group search date">
                        <input type="text" class="form-control dtpicker" id="e-date" placeholder="Ngày kết thúc" name="Date[]" autocomplete="off"
                               value="{{ isset($request['Date']) ? $request['Date'][1] : '' }}">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-th"></span>
                        </div>
                    </div>
                </div>
				<div class="form-group pull-left">
					<button type="submit" class="btn btn-primary form-control" id="btn-search" >@lang('admin.btnSearch')</button>
				</div>
				<div class="form-group pull-right">
					@can('action', $add)
						<button type="button" class="btn btn-primary btn-detail" id="add_new_absence">@lang('admin.add_new_absence')</button>
					@endcan
				</div>
				<div class="clearfix"></div>
			</form>

		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="table-responsive SummaryMonth">
				<table class="table table-bordered table-absence">
					<thead>
						<tr>
							@foreach($absenceTypes as $data)
								<th>{{ $data->Name }}</th>
							@endforeach
							<th>Sum</th>
						</tr>
					</thead>
					<tbody>
					<tr>
					@foreach($absenceTypes as $value)
						<td>{{ array_key_exists($value->DataValue, $totalReport) ? number_format($totalReport[$value->DataValue]/60, 2) + 0 : 0 }}h</td>
					@endforeach
						<td>{{ number_format(array_sum($totalReport)/60, 2) + 0 }}h</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-lg-12 col-md-12 col-sm-12">
			@component('admin.component.table')
				@slot('columnsTable')
					<tr>
						<th class="width5"><a class="sort-link" data-link="{{ route("admin.AbsenceManagement") }}/id/" data-sort="{{ $sort_link }}">@lang('admin.stt')</a></th>
						<th class="width12"><a class="sort-link" data-link="{{ route("admin.AbsenceManagement") }}/SDate/" data-sort="{{ $sort_link }}">@lang('admin.absence.start')</a></th>
						<th class="width12"><a class="sort-link" data-link="{{ route("admin.AbsenceManagement") }}/EDate/" data-sort="{{ $sort_link }}">@lang('admin.absence.end')</a></th>
						<th class="width8"><a class="sort-link" data-link="{{ route("admin.AbsenceManagement") }}/TotalTimeOff/" data-sort="{{ $sort_link }}">@lang('admin.absence.time')</a></th>
						<th><a class="sort-link" data-link="{{ route("admin.AbsenceManagement") }}/Reason/" data-sort="{{ $sort_link }}">@lang('admin.absence.reason')</a></th>
						<th><a class="sort-link" data-link="{{ route("admin.AbsenceManagement") }}/Remark/" data-sort="{{ $sort_link }}">@lang('admin.absence.remark')</a></th>
						<th><a class="sort-link" data-link="{{ route("admin.AbsenceManagement") }}/Approve/" data-sort="{{ $sort_link }}">@lang('admin.absence.approve')</a></th>
						@if ($canEdit || $canDelete)
						<th class="width8">@lang('admin.action')</th>
						@endif
					</tr>
				@endslot
				@slot('dataTable')
					@foreach($absences as $item)
						<tr class="even gradeC" data-id="">
							<td class = "width5 text-center">{{ $sort == 'desc' ? ++$stt : $stt-- }}</td>
							<td class="text-center">{{ FomatDateDisplay($item->SDate, FOMAT_DISPLAY_DATE_TIME) }}</td>
							<td class="text-center">{{ FomatDateDisplay($item->EDate, FOMAT_DISPLAY_DATE_TIME) }}</td>
							<td >{{ number_format($item->TotalTimeOff/60, 2) }}</td>
							<td class="left-important">{{ '('.$item->Name.')'.' '.$item->Reason }}</td>
							<td class="left-important" style="word-break: break-word">{!! nl2br(e($item->Remark)) !!}</td>
							<td class="action-col width8 text-center"> {!! ApprovedDisplayHtml($item->Approved,'','data-toggle="tooltip" title="'.$item->Comment.'"') !!}</td>
							@if ($canEdit || $canDelete)
							<td class="text-center">
								@can('action', $edit)
								<span class="action-col update edit update-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
								@endcan
								@can('action', $delete)
								<span class="action-col update delete delete-one"  item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
								@endcan
							</td>
							@endif
						</tr>
					@endforeach
				@endslot
				@slot('pageTable')
					{{ $absences->appends($query_array)->links() }}
				@endslot
			@endcomponent
			<div id="popupModal">
				{{-- @include('admin.includes.absence-detail')--}}
			</div>
		</div>
	</div>
</section>
<script !src="">
	var ajaxUrl = "{{ route('admin.AbsenceInfo') }}";
	var newTitle = 'Thêm lịch nghỉ';
	var updateTitle = 'Sửa lịch nghỉ';

	$(function () {
        SetDatePicker($('.date'));
        $(".selectpicker").selectpicker();
        $('#btn-search').click(function () {
            $('#absence-search-form').submit();
        });
    });
</script>
@endsection

