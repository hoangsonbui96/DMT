@extends('admin.layouts.default.app')

@push('pageJs')
	<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('js/absence.js') }}"></script>
	{{-- <script type="text/javascript" src="{{ asset('themes/adminlte/bootstrap/dist/js/bootstrap.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('themes/adminlte/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/bootstrap-select.min.js') }}"></script> --}}
@endpush

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
	/* #add_new_absence { margin: 5px 0; } */
</style>

<section class="content-header">
	<h1 class="page-header">@lang('admin.absence.absence')</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12">
					@include('admin.includes.search-absence')
				</div>
				{{-- <div class="col-lg-10 col-md-10 col-sm-10">
					@include('admin.includes.search-absence')
				</div>
				<div class="col-lg-2 col-md-2 col-sm-2" >
					<div class="pull-right">
						@can('action', $export)
							<a class="btn btn-success" id="export-absences">@lang('admin.export-excel')</a>
						@endcan
						@can('action',$add)
							<button type="button" class="btn btn-primary" id="add_new_absence" >@lang('admin.add_new_absence')</button>
						@endcan
					</div>
				</div> --}}
			</div>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<!-- /.box-header -->
			@component('admin.component.table')
				@slot('columnsTable')
					<tr>
						<th class="width5"><a class="sort-link" data-link="{{ route("admin.Absences") }}/id/" data-sort="{{ $sort_link }}">@lang('admin.stt')</a></th>
						<th class="width12"><a class="sort-link" data-link="{{ route("admin.Absences") }}/UID/" data-sort="{{ $sort_link }}">@lang('admin.absence.fullname')</a></th>
						<th class="width8"><a class="sort-link" data-link="{{ route("admin.Absences") }}/SDate/" data-sort="{{ $sort_link }}">@lang('admin.absence.start')</a></th>
						<th class="width8"><a class="sort-link" data-link="{{ route("admin.Absences") }}/EDate/" data-sort="{{ $sort_link }}">@lang('admin.absence.end')</a></th>
						<th><a class="sort-link" data-link="{{ route("admin.Absences") }}/TotalTimeOff/" data-sort="{{ $sort_link }}">@lang('admin.absence.time')</a></th>
						<th class=""><a class="sort-link" data-link="{{ route("admin.Absences") }}/Reason/" data-sort="{{ $sort_link }}">@lang('admin.absence.reason')</a></th>
						<th class="width15"><a class="sort-link" data-link="{{ route("admin.Absences") }}/Remark/" data-sort="{{ $sort_link }}">@lang('admin.absence.remark')</a></th>
						<th><a class="sort-link" data-link="{{ route("admin.Absences") }}/AbsentDate/" data-sort="{{ $sort_link }}">@lang('admin.absence.absentDate')</a></th>
						<th class="width9"><a class="sort-link" data-link="{{ route("admin.Absences") }}/ApprovedDate/" data-sort="{{ $sort_link }}">@lang('admin.absence.approvedDate')</a></th>
						<th><a class="sort-link" data-link="{{ route("admin.Absences") }}/Approved/" data-sort="{{ $sort_link }}">@lang('admin.absence.approve')</a></th>
						@if ($canEdit || $canDelete)
							<th class="width5">@lang('admin.action')</th>
						@endif
					</tr>
				@endslot
				@slot('dataTable')
					@foreach($absence as $item)
						<tr class="even gradeC" data-id="">
							<td class="text-center">{{ $sort == 'desc' ? ++$stt : $stt-- }}</td>
							<td class="left-important ">{{ $item->FullName }}</td>
							<td class="text-center">{{ FomatDateDisplay($item->SDate, FOMAT_DISPLAY_DATE_TIME) }}</td>
							<td class="text-center">{{ FomatDateDisplay($item->EDate, FOMAT_DISPLAY_DATE_TIME) }}</td>
							<td>{{ number_format($item->TotalTimeOff/60, 2) }}</td>
							<td class = "left-important">{{ '('.$item->Name.')'.' '.$item->Reason }}</td>
							<td class = "left-important">{!! nl2br(e($item->Remark)) !!}</td>
							<td class="text-center">{{ FomatDateDisplay($item->created_at, FOMAT_DISPLAY_DATE_TIME) }}</td>
							<td>
								{!! AddSpecial("<br/>", FomatDateDisplay($item->ApprovedDate, FOMAT_DISPLAY_DATE_TIME), e($item->NameUpdateBy)) !!}
							</td>
							<td class = "action-col text-center">{!! ApprovedDisplayHtml($item->Approved, '', '', 'data-toggle="tooltip" title="'.$item->Comment.'"') !!}</td>
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
					{{ $absence->appends($query_array)->links() }}
				@endslot
			@endcomponent
			<div id="popupModal">
			</div>
		</div>
	</div>
</section>

<script>
	var ajaxUrl = "{{ route('admin.AbsenceInfo') }}";
	var ajaxUrlApr = "{{ route('admin.AprAbsence') }}";
	var newTitle = 'Thêm lịch nghỉ';
	var updateTitle = 'Sửa lịch nghỉ';

	$(function() {
		$('[data-toggle="tooltip"]').tooltip();
	});

	$('.btn-search').click(function () {
		$('#absence-search-form').submit();
	});
	$('#export-absences').click(function (e) {
		e.preventDefault();
		var search = $('input[name=search]').val();
		var uid = $('#select-user option:selected').val();
		var dataValue = $('#select-absentreason option:selected').val();
		var sDate = $('#s-date').val();
		var eDate = $('#e-date').val();

		var url_string = window.location.href;
		var url = new URL(url_string);
		var approve = url.searchParams.get('approve');

		var req = '?search='+search+'&UID='+uid+'&MasterDataValue='+dataValue+'&Date[0]='+sDate+'&Date[1]='+eDate+'&approve='+approve;
		ajaxGetServerWithLoader('{{ route('export.Absences') }}'+req,'GET'
			, $('#absence-search-form').serializeArray() ,function (data) {
				if (typeof data.errors !== 'undefined'){
					showErrors(data.errors);
				}else{
					window.location.href = '{{ route('export.Absences') }}'+req;
				}
			});
	});
	
	$("#add_new_absence").click(function () {
		$('.loadajax').show();
		$.ajax({
			url: ajaxUrl,
			success: function (data) {
				$('#popupModal').empty().html(data);
				$('.modal-title').html(newTitle);
				$('.detail-modal').modal('show');
				$('.loadajax').hide();
			}
		});
	});
</script>
@endsection



