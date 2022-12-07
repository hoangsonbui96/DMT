@extends('admin.layouts.default.app')

@push('pageJs')
	<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('js/absence.js') }}"></script>
@endpush

@section('content')

<style>
	/*.table-scroll th, .table-scroll td {*/
	/*	background: none !important;*/
	/*}*/

	/*.table-striped>tbody>tr:nth-of-type(odd) {*/
	/*	background-color: #cfcfcf !important;*/
	/*}*/
</style>

<section class="content-header">
	<h1 class="page-header">@lang('admin.timekeeping.history')</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12">
					@include('admin.includes.checkin.checkin-history-search')
				</div>
			</div>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<!-- /.box-header -->
			@component('admin.component.table')
				@slot('columnsTable')
					<tr>
						<th class="width3"><a class="sort-link" data-link="{{ route("admin.TimekeepingHistory") }}/id/" data-sort="{{ $sort_link }}">@lang('admin.stt')</a></th>
						<th class="width8"><a class="sort-link" data-link="{{ route("admin.TimekeepingHistory") }}/QRCodeID/" data-sort="{{ $sort_link }}">@lang('admin.timekeeping.qr-code')</a></th>
						<th class="width12"><a class="sort-link" data-link="{{ route("admin.TimekeepingHistory") }}/DeviceName/" data-sort="{{ $sort_link }}">@lang('admin.timekeeping.device-name')</a></th>
						<th class="width12"><a class="sort-link" data-link="{{ route("admin.TimekeepingHistory") }}/DeviceInfo/" data-sort="{{ $sort_link }}">@lang('admin.timekeeping.device-info')</a></th>
						<th class="width12"><a class="sort-link" data-link="{{ route("admin.TimekeepingHistory") }}/OsVersion/" data-sort="{{ $sort_link }}">@lang('admin.timekeeping.os-version')</a></th>
						<th class="width8"><a class="sort-link" data-link="{{ route("admin.TimekeepingHistory") }}/Type/" data-sort="{{ $sort_link }}">@lang('admin.timekeeping.type')</a></th>
						<th class="width9"><a class="sort-link" data-link="{{ route("admin.TimekeepingHistory") }}/CheckinTime/" data-sort="{{ $sort_link }}">@lang('admin.timekeeping.checkin-time')</a></th>
						<th class="width8"><a class="sort-link" data-link="{{ route("admin.TimekeepingHistory") }}/MacAddress/" data-sort="{{ $sort_link }}">@lang('admin.timekeeping.mac-address')</a></th>
					</tr>
				@endslot
				@slot('dataTable')
					@foreach($checkin_history as $item)
						<tr class="even gradeC" data-id="">
							<td class="text-center">{{ $sort == 'desc' ? ++$stt : $stt-- }}</td>
							<td class="text-center">{{ isset($item->QRCodeID) && $item->QRCodeID != 0 ? $item->QRCode : '' }}</td>
							<td class="left-important">{{ $item->DeviceName }}</td>
							<td class="left-important">{{ $item->DeviceInfo }}</td>
							<td class="left-important">{{ $item->OsVersion }}</td>
							<td class="text-center">{{ $item->Type }}</td>
							<td class="text-center">{{ FomatDateDisplay($item->CheckinTime, FOMAT_DISPLAY_DATE_TIME) }}</td>
							<td class="text-center">{{ $item->MacAddress }}</td>
						</tr>
					@endforeach
				@endslot
				@slot('pageTable')
					{{ $checkin_history->appends($query_array)->links() }}
				@endslot
			@endcomponent
			<div id="popupModal">
			</div>
		</div>
	</div>
</section>

<script>

	$(function() {
		$('[data-toggle="tooltip"]').tooltip();
	});

</script>
@endsection



