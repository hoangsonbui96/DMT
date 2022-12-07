<form class="form-inline" id="absence-search-form">
	<div class="form-group pull-left">
		<div class="input-group search">
			<input type="text" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
		</div>
	</div>
	<div class="form-group pull-left">
		<div class="input-group search">
			<select class="selectpicker show-tick show-menu-arrow" id="select-user" name="UID" data-live-search="true"
                    data-size="5" data-live-search-placeholder="Search" data-width="220px" data-actions-box="true" tabindex="-98">
				<option value="">@lang('admin.overtime.employer')</option>
				{!! GenHtmlOption($users, 'id', 'FullName', isset($request['UID']) ? $request['UID'] : '') !!}
			</select>
		</div>
	</div>

	<div class="form-group pull-left">
		<div class="input-group search">
            <select class="selectpicker show-tick show-menu-arrow" id="select-absentreason" name="MasterDataValue" data-live-search="true"
                    data-live-search-placeholder="Search" data-size="6" data-width="220px" tabindex="-98">
                <option value="">@lang('admin.absence.absentreason')</option>
                {!!
                    GenHtmlOption($master_datas, 'DataValue', 'Name', isset($request['MasterDataValue']) ? $request['MasterDataValue'] : '')
                !!}
            </select>
        </div>
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
		<div class="input-group">
			<button type="button" class="btn btn-primary" id="btn-search-absence">@lang('admin.btnSearch')</button>
		</div>
	</div>

	<div class="form-group pull-right">
		<div class="input-group" id="area-btn">
			@can('action', $export)
				<button type="button" class="btn btn-success" id="export-absences">@lang('admin.export-excel')</button>
			@endcan
			@can('action',$add)
				<button type="button" class="btn btn-primary" id="add_new_absence">@lang('admin.add_new_absence')</button>
			@endcan
		</div>
	</div>
</form>
<style>
	#absence-search-form .form-group:not(:last-child), #area-btn button:not(:last-child) { margin-right: 3px; }
</style>
<script>
	SetDatePicker($('.date'));
	$(".selectpicker").selectpicker();
	$('#btn-search-absence').click(function () {
		var sDate = moment($('#s-date').val(),'DD/MM/YYYY').format('YYYYMMDD');
		var eDate = moment($('#e-date').val(),'DD/MM/YYYY').format('YYYYMMDD');
		var repSDate = sDate.replace(/\D/g, "");
		var repEDate = eDate.replace(/\D/g, "");

		if (repSDate > repEDate && repSDate != '' && repEDate != ''){
			showErrors(['Ngày tìm kiếm không hợp lệ']);
		} else {
			$('#absence-search-form').submit();
		}
	});
</script>
