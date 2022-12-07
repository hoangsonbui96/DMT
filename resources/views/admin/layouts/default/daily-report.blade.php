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
<style>
	.table.table-bordered th,
	.table.table-bordered td {
		border: 1px solid #bdb9b9 !important;
		text-align: center;
		vertical-align: middle !important;
		background-color: #fff;
	}

	.SummaryMonth .table.table-bordered tr th {
		background-color: #dbeef4;
	}

	.tbl-dReport .table.table-bordered tr th {
		background-color: #c6e2ff;
	}

	.tbl-top {
		margin-top: 0;
	}
</style>
<section class="content-header left232 daily-header top49">
	<h1 class="page-header">@lang('admin.daily.daily_report') tháng {{
		(\Request::get('time')) ? \Request::get('time') : \Carbon\Carbon::now()->format(FOMAT_DISPLAY_MONTH)
		}} - {{ $user->FullName }}</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8 daily-content top100">
			@include('admin.includes.daily-report-search')
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12 marginTop90">
			<div class="table-responsive SummaryMonth" style="display: none;">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th rowspan="2">No</th>
							<th>ToW%</th>
							@foreach($masterData as $data)
							@php $key = $data->DataValue; @endphp
							<td>{{ $total->totalHours > 0 ? number_format($total->$key/$total->totalHours*100, 2) : 0
								}}%</td>
							@endforeach
							<td></td>
							<th rowspan="2">Project Percent</th>
						</tr>
						<tr>
							<th>@lang('admin.daily.Project')</th>
							@foreach($masterData as $data)
							<th>{{ $data->Name }}</th>
							@endforeach
							<th>Sum</th>
						</tr>
					</thead>
					<tbody>
						@foreach($total as $item)
						<tr>
							<td style="font-weight: bold">{{ $loop->iteration }}</td>
							<td class="pName">{{ $item->NameVi }}</td>
							@foreach($masterData as $data)
							@php $key = $data->DataValue @endphp
							<td>{{ $item->$key }}</td>
							@endforeach
							<td>{{ $item->totalHours }}</td>
							<td>{{ $total->totalHours > 0 ? number_format($item->totalHours/$total->totalHours*100, 2) :
								0 }}</td>
						</tr>
						@endforeach
						<tr>
							<td class="td-last" colspan="{{ count($masterData) + 1 }}"></td>
							<th>Total</th>
							<th>{{ $total->totalHours }}</th>
							<td></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			@component('admin.component.table')
			@slot('columnsTable')
			<tr>
				<th class="width3pt">@lang('admin.stt')</th>
				<th class="width5 sticky-hz">@lang('admin.daily.Date')</th>
				<th>@lang('admin.daily.Project')</th>
				<th>@lang('admin.daily.Screen_Name')</th>
				<th>@lang('admin.daily.Type_Of_Work')</th>
				<th>@lang('admin.contents')</th>
				<th class="width3">@lang('admin.daily.Working_Time')</th>
				<th class="width5">@lang('admin.daily.progressing')</th>
				<th>@lang('admin.daily.Note')</th>
				<th class="width5">@lang('admin.daily.Date_Create')</th>
				<th>@lang('admin.daily.Status')</th>
				@if ($canEdit || $canDelete)
				<th class="width5pt">@lang('admin.action')</th>
				@endif
			</tr>
			@endslot
			@slot('dataTable')
			@if (count($dailyReports) != 0)
			@foreach($dailyReports as $key => $dailyReport)
			<tr class="even gradeC" data-id="">
				<td>{{ $loop->iteration }}</td>
				<th style="font-weight:normal">{{ FomatDateDisplay($dailyReport->Date, FOMAT_DISPLAY_DAY) }}</th>
				<td class="left-important"> {!! nl2br(e($dailyReport->NameVi)) !!}</td>
				<td class="left-important">{!! nl2br(e($dailyReport->ScreenName)) !!}</td>
				<td class="left-important">{{ $dailyReport->Name }}</td>
				<td class="left-important">{!! nl2br(e($dailyReport->Contents)) !!}</td>
				<td>{{ $dailyReport->WorkingTime }}</td>
				<td>{{ $dailyReport->Progressing.' %'}}</td>
				<td class="left-important">{!! nl2br(e($dailyReport->Note)) !!}</td>
				<td>{{ FomatDateDisplay($dailyReport->created_at, FOMAT_DISPLAY_DATE_TIME) }}</td>
				<td>
					@if($dailyReport->Status !== null)
					@switch($dailyReport->Status)
					@case('0')
					<span class="label label-info data-toggle=">@lang('admin.daily.Need Approve')</span>
					@break
					@case(1)
					<a href="" class="report-issue" data-target="#denyModal" data-toggle="modal"
						issue-value="{{isset($dailyReport->Issue) ? $dailyReport->Issue : null}}" item-id="{{$dailyReport->id}}"><span
							class="label label-danger data-toggle=">@lang('admin.daily.Rewrite')</span></a>
					@break
					@case(2)
					@if (isset($dailyReport->Issue))
					<a href="" class="report-issue" data-target="#denyModal" data-toggle="modal"
						issue-value="{{$dailyReport->Issue}}" item-id="{{$dailyReport->id}}"
						item-status="{{$dailyReport->Status}}" onclick="setIssue()"><span
							class="label label-warning data-toggle=">@lang('admin.daily.Approved')</span></a>
					@else
					<span class="label label-success data-toggle=">@lang('admin.daily.Approved')</span>
					@endif
					@break
					@default
					@endswitch
					@else 
					@endif
				</td>
				@if ($canEdit || $canDelete)
				<td class="text-center">
					@if ($dailyReport->TypeReport != 2)
					@if ($canEdit)
					<span class="action-col update edit update-one" item-id="{{ $dailyReport->id }}">@if (
						$dailyReport->Status != 2)
						<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
						@else
						<i class="fa fa-eye" aria-hidden="true"></i>
						@endif
					</span>
					@endif
					@if ($canDelete)
					@if (
					$dailyReport->Status != 2)
					<span class="action-col update delete delete-one" item-id="{{ $dailyReport->id }}"><i
							class="fa fa-times" aria-hidden="true"></i></span>
					@endif
					@endif
					@endif
				</td>
				@endif
			</tr>
			@endforeach
			@endif

			@endslot
			{{-- @slot('pageTable')
			@endslot --}}
			@endcomponent
			<div class="modal draggable fade in denyModal" id="denyModal" role="dialog" data-backdrop="static">
				<div class="modal-dialog modal-lg ui-draggable">
					<div class="modal-content drag">
						<div class="modal-header ui-draggable-handle" style="cursor: move;">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">@lang('admin.daily.Deny Report')</h4>
						</div>
						<div class="modal-body">
							<textarea class="form-control" name="issue" id="issue" rows="10" readonly></textarea>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default"
								data-dismiss="modal">@lang('admin.daily.Close')</button>

							<button type="button" id="edit-report"
								class="btn btn-primary action-col update edit update-one"
								item-id="">@lang('admin.daily.Edit Report')</button>
						</div>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
		</div>
	</div>
</section>
@endsection
@section('js')
<script type="text/javascript" async>
	var ajaxUrl = "{{ route('admin.DailyInfo') }}";
    var newTitle = 'Thêm báo cáo';
    var updateTitle = 'Sửa báo cáo';
    var nameSelected = '';

    $(function () {
		$('.modal-dialog').draggable({
       		handle: ".modal-header"
    	});
        if($("#select-user option:selected").text() != ''){
            nameSelected = '[' + $("#select-user option:selected").text() + ']';
        }

        $("#add_daily").click(function () {
            var reqId = $(this).attr('req');
            ProcessAddDaily(ajaxUrl + '?reqId=' + reqId, reqId, function(data) {
                $('#req-id').val(reqId);
            });
        });

        {{--$("#add_daily_one").click(function () {--}}
        {{--    var reqId = $(this).attr('req-one');--}}
        {{--    ProcessAddDaily("{{ route('admin.DailyInfoOne') }}" + '?reqId='+ reqId, reqId, function(data) {--}}
        {{--        $('#req-one').val(reqId);--}}
        {{--    });--}}
        {{--});--}}
    });

    function ProcessAddDaily(urlPost, reqId, callback) {
        ajaxGetServerWithLoader(urlPost, 'GET', null, function (data) {
            $('#popupModal').empty().html(data);
            $('.modal-title').html(newTitle + ' ' + nameSelected);
            $('.detail-modal').modal('show');

            if (IsFunction(callback)) {
                callback(data);
            }
        });
    }

	$('.report-issue').click(function(){
		id = $(this).attr('item-id');
		let issue = $(this).attr('issue-value');
		status = $(this).attr('item-status');
		if(issue == ''){
			issue = 'Người duyệt không có yêu cầu gì đặc biệt'
		}
		if(status == 2){
			$('#edit-report').hide();
		}else{
			$('#edit-report').show();
		}
		console.log(issue);
		$('#issue').html(issue);
		$('#edit-report').attr('item-id',id);
	});
</script>
@endsection
