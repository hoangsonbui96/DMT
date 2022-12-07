@extends('admin.layouts.default.app')
@section('content')
<section class="content-header">
	@foreach($list as $item)
    <h1 class="page-header">Thiết bị {{$item->code}} của 
    	{{isset($item->user_owner) && ($item->user_owner == 0) ? 'Văn phòng' : $item->FullName}}</h1>
    @endforeach
</section>
<section class="content">
	<div class="row">
	 @foreach($list as $item)
	 	 <div class="form-group" >
	 	 	<div class="col-sm-4">
	 	 		<span>@lang('admin.equipment.code') : </span>
	 	 		<span>{{isset($item->code)?$item->code:''}}</span>
	 	 	</div>
	 	 	<div class="col-sm-4">
	 	 		<span>@lang('admin.equipment.name') : </span>
	 	 		<span>{{isset($item->name)?$item->name:''}}</span>
	 	 	</div>
	 	 	<div class="col-sm-4">
	 	 		<span>@lang('admin.equipment.select_type') : </span>
	 	 		<span>{{isset($item->type_name)?$item->type_name:''}}</span>
	 	 	</div>
	 	 	<br>
	 	 	<br>
	 	 	<div class="col-sm-4">
	 	 		<span>@lang('admin.equipment.Device_status') :</span>
	 	 		<span>{{isset($item->Name)?$item->Name:''}}</span>
	 	 	</div>
	 	 	<div class="col-sm-4">
	 	 		<span>@lang('admin.equipment.provider') :</span>
	 	 		<span>{{isset($item->provider)?$item->provider:''}}</span>
	 	 	</div>
	 	 	<div class="col-sm-4">
	 	 		<span>@lang('admin.equipment.unit_price') :</span>
	 	 		<span>{{isset($item->unit_price)?$item->unit_price.' đồng':''}}</span>
	 	 	</div>
	 	 	<br>
	 	 	<br>
	 	 	<div class="col-sm-4">
	 	 		<span>@lang('admin.equipment.buy_date') :</span>
	 	 		<span>{{FomatDateDisplay($item->buy_date, FOMAT_DISPLAY_DAY)}}</span>
	 	 	</div>
	 	 	<div class="col-sm-4">
	 	 		<span>@lang('admin.equipment.period_date') :</span>
	 	 		<span>{{FomatDateDisplay($item->period_date, FOMAT_DISPLAY_DAY)}}</span>
	 	 	</div>
	 	 	<div class="col-sm-4">
	 	 		<span>@lang('admin.equipment.deal_date') :</span>
	 	 		<span>{{FomatDateDisplay($item->deal_date, FOMAT_DISPLAY_DAY)}}</span>
	 	 	</div>
	 	 	<br>
	 	 	<br>
	 	 	<div class="col-sm-4">
	 	 		<span>@lang('admin.equipment.info') :</span>
	 	 		<span>{{isset($item->info)?$item->info:''}}</span>
	 	 	</div>
	 	 	<div class="col-sm-4">
	 	 		<span>@lang('admin.equipment.serial_number') :</span>
	 	 		<span>{{isset($item->info)?$item->serial_number:''}}</span>
	 	 	</div>
	 	 	<div class="col-sm-4">
	 	 		<span>@lang('admin.equipment.note') :</span>
	 	 		<span>{{isset($item->info)?$item->note:''}}</span>
	 	 	</div>
	 	 </div>
	 @endforeach
	</div>
</section>
@endsection