<form class="form-inline" id="equipment-offer-search-form">
	<div class="form-group pull-left">
		<div class="input-group search">
			<input type="text" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
		</div>
	</div>
	<div class="form-group pull-left">
		<select class="selectpicker show-tick show-menu-arrow" id="select-user" name="UID" data-live-search="true" data-size="5"
				data-live-search-placeholder="Tìm kiếm" data-width="220px" data-actions-box="true" tabindex="-98">
			<option value="">@lang('admin.staff')</option>
			{!! GenHtmlOption($users, 'id', 'FullName', isset($request['UID']) ? $request['UID'] : '') !!}
		</select>
	</div>
	<div class="form-group pull-left">
		<select class="selectpicker show-tick show-menu-arrow" id="select-user" name="approved" data-width="220px" data-actions-box="true" tabindex="-98">
			<option value="">Tất cả trạng thái</option>
			<option value="0" {{ isset($request['approved']) && $request['approved'] == 0 ? 'selected' : '' }}>Chưa duyệt</option>
			<option value="1" {{ isset($request['approved']) && $request['approved'] == 1 ? 'selected' : '' }}>Đã duyệt</option>
			<option value="2" {{ isset($request['approved']) && $request['approved'] == 2 ? 'selected' : '' }}>Đã hủy</option>
			<option value="3" {{ isset($request['approved']) && $request['approved'] == 3 ? 'selected' : '' }}>Chưa hoàn thành</option>
			<option value="4" {{ isset($request['approved']) && $request['approved'] == 4 ? 'selected' : '' }}>Hoàn thành</option>
		</select>
	</div>
	<div class="form-group pull-left">
		<div class="input-group search date">
			<input type="text" class="form-control dtpicker" id="s-date" placeholder="Ngày bắt đầu" name="Date[]" autocomplete="off"
					value="{{ isset($request['Date']) ? $request['Date'][0] : '' }}">
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
		<div class="input-group">
			<button type="button" class="btn btn-primary btn-search" id="btn-search-equipment-offer">@lang('admin.btnSearch')</button>
		</div>
	</div>

	<div class="form-group pull-right">
		<div class="input-group" id="area-btn">
{{--			@can('action', $export)--}}
{{--				<button type="button" class="btn btn-success" id="export-equipment-offer">@lang('admin.export-excel')</button>--}}
{{--			@endcan--}}
			@can('action',$add)
				<button type="button" class="btn btn-primary" id="add-new-equipment-offer">@lang('admin.equipment-offer.add')</button>
			@endcan
		</div>
	</div>
</form>
<style>
	#equipment-offer-search-form .form-group:not(:last-child), #area-btn button:not(:last-child) { margin-right: 3px; }
</style>
<script>
	var ajaxUrl = "{{ route('admin.EquipmentOfferInfo') }}";
	var newTitle = "@lang('admin.equipment-offer.add')";
	var updateTitle = "@lang('admin.equipment-offer.edit')";

	SetDatePicker($('.date'));
	$(".selectpicker").selectpicker({
		noneSelectedText : "@lang('admin.equipment-offer.approved-status')"
	});
	$('#btn-search-equipment-offer').click(function () {
		var sDate = moment($('#s-date').val(),'DD/MM/YYYY').format('YYYYMMDD');
		var eDate = moment($('#e-date').val(),'DD/MM/YYYY').format('YYYYMMDD');
		var repSDate = sDate.replace(/\D/g, "");
		var repEDate = eDate.replace(/\D/g, "");

		if (repSDate > repEDate && repSDate != '' && repEDate != ''){
			showErrors(['Ngày tìm kiếm không hợp lệ']);
		} else {
			$('#equipment-offer-search-form').submit();
		}
	});

	$("#add-new-equipment-offer").click(function () {
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

	$('#export-equipment-offer').click(function (e) {
		e.preventDefault();
		var search = $('input[name=search]').val();
		var sDate = $('#s-date').val();
		var eDate = $('#e-date').val();


		var req = '?search='+search+'&Date[0]='+sDate+'&Date[1]='+eDate;
		ajaxGetServerWithLoader(
			'{{ route('export.EquipmentOffer') }}'+req,
			'GET',
			$('#equipment-offer-search-form').serializeArray(),
			function (data) {
				if (typeof data.errors !== 'undefined'){
					showErrors(data.errors);
				}else{
					window.location.href = '{{ route('export.EquipmentOffer') }}'+req;
				}
			}
		);
	});
</script>
