@extends('admin.layouts.default.app')
@section('content')
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
	<h1 class="page-header">@lang('admin.daily.need_approve_report') tháng {{
		(\Request::get('time')) ? \Request::get('time') : \Carbon\Carbon::now()->format(FOMAT_DISPLAY_MONTH)
		}} - {{!is_countable($user) ? $user->FullName : '' }}</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8 daily-content top100">
			@include('admin.includes.report.need-approve-report-search')
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12 marginTop90">

		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			@component('admin.component.table')
			@slot('columnsTable')
			<tr>
				<th class="width3pt">@lang('admin.stt')</th>
				<th class="width5 sticky-hz">@lang('admin.daily.Date')</th>
				<th>@lang('admin.daily.Project')</th>
				<th>@lang('admin.user.full_name')</th>
				<th>@lang('admin.daily.Screen_Name')</th>
				<th>@lang('admin.daily.Type_Of_Work')</th>
				<th>@lang('admin.contents')</th>
				<th>@lang('admin.absences')</th>
				<th class="width3">@lang('admin.daily.Working_Time')</th>
				<th class="width5">@lang('admin.daily.progressing')</th>
				<th class="width5">@lang('admin.daily.Date_Create')</th>
				@if (isset($reportStatus) && $reportStatus == 0)
				<th class="width8">@lang('admin.daily.Approve')</th>
				@endif
				{{-- @if ($canEdit || $canDelete) --}}
				<th class="width5pt">@lang('admin.action')</th>
				{{-- @endif --}}
			</tr>
			@endslot
			@slot('dataTable')

			@foreach($dailyReports as $key => $dailyReport)
			<tr class="even gradeC" data-id="">
				<td>{{ $sort == 'desc' ? ++$stt : $stt-- }}</td>

				<th style="font-weight:normal"><a
						href="{{ route('admin.DailyReports',['UserID' => $dailyReport->UserID,'ProjectID'=>$dailyReport->ProjectID,'time'=>FomatDateDisplay($dailyReport->Date,FOMAT_DISPLAY_MONTH)])}}">
						{{ FomatDateDisplay($dailyReport->Date,FOMAT_DISPLAY_DAY) }}</a></th>

				<td class="left-important"> {!! nl2br(e($dailyReport->NameVi)) !!}</td>
				<td class="left-important">{!! nl2br(e($dailyReport->Reporter)) !!}</td>
				<td class="left-important">{!! nl2br(e($dailyReport->ScreenName)) !!}</td>
				<td class="left-important">{{ $dailyReport->Name }}</td>
				<td class="left-important">{!! nl2br(e($dailyReport->Contents)) !!}</td>
				<td>
					@foreach ($dailyReport->Absent as $item)
					@if ($item['STime'] != '')
					<a user-id="{{$dailyReport->UserID}}" report-date="{{$dailyReport->Date}}" class="action-col view"
						style="text-decoration: none">
						{{ $item['Reason'].' ('.$item['STime'].' - '.$item['ETime'].')'}}
					</a>
					<br>
					@endif
					@endforeach
				</td>
				<td>{{ $dailyReport->WorkingTime }}</td>
				<td>{{ $dailyReport->Progressing.' %'}}</td>
				<td>{{ FomatDateDisplay($dailyReport->created_at, FOMAT_DISPLAY_DATE_TIME) }}</td>
				@if (isset($reportStatus) && $reportStatus == 0)
				<td>
					<button class="action-col btn btn-success btn-sm btn-approve" type="button"
						approve-id="{{$dailyReport->DailyReportId}}" issue-value="{{$dailyReport->Issue}}">

						<i class="fa fa-check" aria-hidden="true"></i>
					</button>

					<button class="action-col btn btn-danger btn-sm open-deny" type="button" data-toggle="modal"
						deny-id="{{$dailyReport->DailyReportId}}" issue-value="{{$dailyReport->Issue}}">

						<i class="fa fa-undo" aria-hidden="true"> </i>
					</button>
				</td>
				@endif


				{{-- @if ($canEdit || $canDelete) --}}
				<td class="text-center">
					@if ($dailyReport->TypeReport != 2)
					{{-- @if ($canEdit) --}}
					<span class="action-col update edit update-one" item-id="{{ $dailyReport->DailyReportId }}"><i
							class="fa fa-eye" aria-hidden="true"></i></span>
					{{-- @endif --}}
					{{-- @if ($canDelete) --}}
					{{-- <span class="action-col update delete delete-one" item-id="{{ $dailyReport->DailyReportId }}"><i
							class="fa fa-times" aria-hidden="true"></i></span> --}}
					{{-- @endif --}}
					@endif
				</td>
				{{-- @endif --}}
			</tr>
			@endforeach
			@endslot

			@slot('pageTable')

			{!! $dailyReports->appends($request)->links('admin.includes.paginator') !!}
			@endslot
			@endcomponent
		</div>
</section>
@endsection
@section('js')
<script type="text/javascript" async>
	var ajaxUrl = "{{ route('admin.DailyInfo') }}";
	var denyAjaxUrl = "{{ route('admin.openDenyReport') }}";
    var newTitle = 'Thêm báo cáo';
    var updateTitle = 'Sửa báo cáo';
    var nameSelected = '';

    $(function () {
        if($("#select-user option:selected").text() != ''){
            nameSelected = '[' + $("#select-user option:selected").text() + ']';
        }

        $("#add_daily").click(function () {
            var reqId = $(this).attr('req');
            ProcessAddDaily(ajaxUrl + '?reqId=' + reqId, reqId, function(data) {
                $('#req-id').val(reqId);
            });
        });
		
		$(".open-deny").click(function () {
        	var reqId = $(this).attr('deny-id');
        	openDenyReport(denyAjaxUrl + '?reqId=' + reqId, reqId, function(data) {
           	//  $('#req-id').val(reqId);
        	});
    	});

		$('.btn-approve').click(function(){
			url = "{{route('admin.ApproveReport')}}";
			id = $(this).attr('approve-id');
			status = 2;
			showConfirm('Bạn có chắc chắn duyệt?',function () {
        		ajaxGetServerWithLoader(url, 'get', {id:id,status:status,approveBy:{{auth()->id()}}}, function (data) {
           			if (typeof data.errors !== 'undefined') {
                		showErrors(data.errors);
               			return;
            		}
					$.alert({
						title: 'Thành công',
						icon: 'fa fa-check',
						type: 'blue',
						content: data.success,
						buttons: {
							ok: {
								action: () => {
									locationPage();
								}
							}
						}
    				});
           		});
			});
		})	 
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

	function openDenyReport(urlPost, reqId) {
        ajaxGetServerWithLoader(urlPost, 'GET', {reqId:reqId}, function (data) {
            $('#popupModal').empty().html(data);
            $('.modal-title').html('Yêu cầu sửa báo cáo');
			$(".modal-backdrop").remove();
            $('.denyModal').modal('show');
       
        });
    }

	function denyReport(){
		url = "{{route('admin.ApproveReport')}}";
		id = $("#reportID").val();
		issue = $("#issue").val();
		if (issue.trim() == '') {
            showErrors('Nội dung phản hồi không được để chống');
            return;
        }
		status = 1;
        ajaxGetServerWithLoader(url, 'get', {id:id,status:status,issue:issue}, function (data) {
           	if (typeof data.errors !== 'undefined') {
                showErrors(data.errors);
               	return;
            }
			$.alert({
				title: 'Thành công',
				icon: 'fa fa-check',
				type: 'blue',
				content: data.success,
				buttons: {
					ok: {
						action: () => {
							locationPage();
						}
					}
				}
			});
		});
	}

	function approveReport(){
		url = "{{route('admin.ApproveReport')}}";
		id = $('#id').val();
		status = 2;
		showConfirm('Bạn có chắc chắn duyệt?',function () {
			ajaxGetServerWithLoader(url, 'get', {id:id,status:status,approveBy:{{auth()->id()}}}, function (data) {
				if (typeof data.errors !== 'undefined') {
					showErrors(data.errors);
					return;
				}
				$.alert({
					title: 'Thành công',
					icon: 'fa fa-check',
					type: 'blue',
					content: data.success,
					buttons: {
						ok: {
							action: () => {
								locationPage();
							}
						}
					}
				});
			});
		});
	}

		$('a.view').click(function() {
			var Title = 'Lý do vắng mặt ngày';
			var dateTr = $(this).attr('report-date');
			var date = dateTr.split("-").reverse().join("/");
			Title = Title.concat(' ',date);
			var UserID = $( this ).attr('user-id');
			ajaxGetServerWithLoader('{{route('admin.AbsenceTimekeeping')}}', 'POST', {
				date: dateTr,
				UserID: UserID,
			}, function (data) {
				$('#popupModal').empty().html(data);
				$('.modal-title').html(Title);
				$('#modal-absence-list').modal('show');
			});
		});
</script>
@endsection