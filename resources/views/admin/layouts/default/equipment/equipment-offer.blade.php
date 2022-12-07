@extends('admin.layouts.default.app')

@push('pageJs')
	<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('js/easy-number-separator.js') }}"></script>
	<script src="{{ asset('js/absence.js') }}"></script>
@endpush

@section('content')

@php
	$canEdit = false;
	$canDelete = false;
	$canExport = false;
	$canAppr = false;
@endphp

@can('action', $edit)
	@php
		$canEdit = true;
	@endphp
@endcan

@can('action', $export)
	@php
		$canExport = true;
	@endphp
@endcan

@can('action', $delete)
	@php
		$canDelete = true;
	@endphp
@endcan

@can('action', $appr)
	@php
		$canAppr = true;
	@endphp
@endcan

<style>
	/*.table-scroll th, .table-scroll td {*/
	/*	background: none !important;*/
	/*}*/

	/*.table-striped>tbody>tr:nth-of-type(odd) {*/
	/*	background-color: #cfcfcf !important;*/
	/*}*/

	/*#table1 {*/
	/*	margin-bottom: 10px;*/
	/*	margin-top: 20px;*/
	/*}*/

	/*#table1 th, #table1 td {*/
	/*	border: 1px solid #bdb9b9 !important;*/
	/*	text-align: center;*/
	/*	vertical-align: middle !important;*/
	/*	background-color: #fff;*/
	/*}*/

	/*#table1 {*/
	/*	width: 50%;*/
	/*	margin-left: 25%;*/
	/*}*/
</style>

<section class="content-header">
	<h1 class="page-header">@lang('admin.equipment-offer.header')</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12">
					@include('admin.includes.equipment.equipment-offer-search')
				</div>
			</div>
		</div>

		<div class="col-md-12 col-sm-12 col-xs-12">
			<!-- /.box-header -->

			@component('admin.component.table')
				@slot('columnsTable')
					<tr>
						<th>@lang('admin.equipment-offer.unit-price-total') (VNĐ)</th>
						<th>@lang('admin.equipment-offer.final-price-total') (VNĐ)</th>
{{--						<th>@lang('admin.equipment-offer.price-total') (VNĐ)</th>--}}
						<th rowspan="4"></th>
						<th>@lang('admin.equipment-offer.number-unit-total')</th>
						<th>@lang('admin.equipment-offer.number-final-total')</th>
						<th>@lang('admin.equipment-offer.number-total')</th>
					</tr>
				@endslot
				@slot('dataTable')
					<tr>
						<td class="text-center">{{ number_format($equipment_offer->totalUnitPrice, 0, '.', ',') }}</td>
						<td class="text-center">{{ number_format($equipment_offer->totalFinalPrice, 0, '.', ',') }}</td>
{{--						<td class="text-center">{{ number_format($equipment_offer->totalUnitPrice + $equipment_offer->totalFinalPrice, 0, '.', ',') }}</td>--}}
						<td></td>
						<td class="text-center">{{ $equipment_offer->totalUnitNumber }}</td>
						<td class="text-center">{{ $equipment_offer->totalFinalNumber }}</td>
						<td class="text-center">{{ $equipment_offer->totalFinalNumber + $equipment_offer->totalUnitNumber }}</td>
					</tr>
				@endslot
			@endcomponent

			@component('admin.component.table')
				@slot('columnsTable')
					<tr>
						<th class="width3"><a class="sort-link" data-link="{{ route("admin.EquipmentOffer") }}/id/" data-sort="{{ $sort_link }}">@lang('admin.stt')</a></th>
						<th class="width9">@lang('admin.equipment-offer.content')</th>
						<th class="width5"><a class="sort-link" data-link="{{ route("admin.EquipmentOffer") }}/id/" data-sort="{{ $sort_link }}">@lang('admin.equipment-offer.name')</a></th>
						<th class="width5"><a class="sort-link" data-link="{{ route("admin.EquipmentOffer") }}/OfferDate/" data-sort="{{ $sort_link }}">@lang('admin.equipment-offer.offer-date')</a></th>
						<th class="width5"><a style="color: black">@lang('admin.equipment-offer.total-price')</a></th>
						<th class="width8"><a class="sort-link" data-link="{{ route("admin.EquipmentOffer") }}/OfferUserID/" data-sort="{{ $sort_link }}">@lang('admin.equipment-offer.user-id')</a></th>
						<th class="width9"><a class="sort-link" data-link="{{ route("admin.EquipmentOffer") }}/Approved/" data-sort="{{ $sort_link }}">@lang('admin.equipment-offer.approved')</a></th>
						<th class="width5"><a style="color: black">@lang('admin.equipment-offer.buy-date')</a></th>
						<th class="width5"><a style="color: black">@lang('admin.equipment-offer.status-id')</a></th>
						@if ($canAppr)
						<th class="width5">@lang('admin.approved')</th>
						@endif
						<th class="width5">@lang('admin.action')</th>
					</tr>
				@endslot
				@slot('dataTable')
					@foreach($equipment_offer as $item)
						<tr class="even gradeC" data-id="">
							<td class="text-center">{{ $sort == 'desc' ? ++$stt : $stt-- }}</td>
							<td class="text-left">{{ $item->Content??'' }}</td>
							<td class="text-center" id="offer-number">{{ \App\Http\Controllers\Admin\Equipment\EquipmentOfferController::createOfferNumber($item->id) }}</td>
							<td class="text-center">{{ FomatDateDisplay($item->created_at, FOMAT_DISPLAY_CREATE_DAY) }}</td>
							<td class="text-right">{{ number_format($item->totalPrice, 0, '.', ',') }}</td>
							<td class="text-left">{{ isset($item->OfferUserID) && $item->OfferUserID != 0 ? (!empty(App\User::find($item->OfferUserID))?App\User::find($item->OfferUserID)->FullName:'') : '' }}</td>
							<td class="text-center">{{ isset($item->ApprovedDate) ? FomatDateDisplay($item->ApprovedDate, FOMAT_DISPLAY_CREATE_DAY) : '' }}<br>{{ isset($item->ApprovedUserID) && $item->ApprovedUserID != 0 ? (!empty(App\User::find($item->ApprovedUserID))?App\User::find($item->ApprovedUserID)->FullName:''): '' }}</td>
							<td class="text-center">{{ isset($item->buyDate) ? FomatDateDisplay($item->buyDate->BuyDate, FOMAT_DISPLAY_DAY) : '' }}</td>
							<td class="text-center">
								@if ($item->Approved == 1)
									@if ($item->countBuy == $item->countAll)
										<span class="label label-primary">Hoàn thành</span>
									@elseif( 0 < $item->countBuy && $item->countBuy < $item->countAll )
										<span class="label label-info">Chưa hoàn thành - {{ $item->countBuy . '/' . $item->countAll }}</span>
									@else
										<span class="label label-success">{{ \App\Http\Controllers\Admin\Equipment\EquipmentOfferController::approved_list[$item->Approved] }}</span>
									@endif
								@elseif ($item->Approved == 2)
									<span class="label label-danger">{{ \App\Http\Controllers\Admin\Equipment\EquipmentOfferController::approved_list[$item->Approved] }}</span>
								@else
									<span class="label label-default">{{ \App\Http\Controllers\Admin\Equipment\EquipmentOfferController::approved_list[$item->Approved] }}</span>
								@endif
							</td>
							@if ($canAppr)
							<td class="text-center">
								@if($item->Approved != 1 && $item->Approved != 2)
									<button class="action-col btn btn-success btn-sm btnApr" id="btnApr" id-apr="{{ $item->id }}" type="submit"><i class="fa fa-check" aria-hidden="true"></i></button>
									<button class="action-col btn btn-danger btn-sm btnDel" id="btnDel" id-apr="{{ $item->id }}" type="submit"><i class="fa fa-times" aria-hidden="true"></i></button>
								@endif
							</td>
							@endif
							<td class="text-center">
								@if ($canEdit)
									<span class="action-col update edit update-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true" title="Edit"></i></span>
								@endif
								@if ($canExport)
									<span class="action-col update export export-one" item-id="{{ $item->id }}"><i class="fa fa-file-excel-o" aria-hidden="true" title="Export"></i></span>
								@endif
								@if ($canDelete && (\Illuminate\Support\Facades\Auth::user()->role_group == 2 || $item->OfferUserID == \Illuminate\Support\Facades\Auth::user()->id))
									<span class="action-col update delete delete-one" item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true" title="Delete"></i></span>
								@endif
							</td>
						</tr>
					@endforeach

				@endslot
				@slot('pageTable')
					{{ $equipment_offer->appends($query_array)->links() }}
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

	var ApproveUrl = "{{ route('admin.AprEquipmentOffer') }}";
	var unApproveUrl = "{{ route('admin.UnApprove') }}";

	$('.btnApr').click(function () {
		var itemId = $(this).attr('id-apr');
		showConfirm('Bạn có chắc chắn duyệt?',
			function () {
				ajaxGetServerWithLoader(ApproveUrl + '/' + itemId, 'GET', null, function (data) {
					if (typeof data.errors !== 'undefined') {
						showErrors(data.errors);
						return;
					}
					showSuccess(data.success);
					locationPage();
				});
			}
		);
	});

	$('.btnDel').click(function () {
		var itemId = $(this).attr('id-apr');
		var offerNumber = $('#offer-number').html();
		ajaxServer(unApproveUrl + '?task=equipment-offer&id=' + offerNumber, 'GET', null, function (data) {
			$('#popupModal').empty().html(data);
			$('#req-id').val(itemId);
			$('.detail-modal').modal('show');
		})
	});

	$('.export-one').click(function (e) {
		e.preventDefault();
		var reqId = $(this).attr('item-id');
		ajaxGetServerWithLoader('{{ route('export.EquipmentOffer') }}' + '?id=' + reqId, 'GET', null,
			function (data) {
				if (typeof data.errors !== 'undefined'){
					showErrors(data.errors);
				}else{
					window.location.href = '{{ route('export.EquipmentOffer') }}' + '?id=' + reqId;
				}
			}
		);
	});

</script>
@endsection



