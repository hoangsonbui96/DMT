<form class="form-inline" id="working-schedule-search-form">
	<div class="form-group pull-left">
		<div class="input-group search">
			<input type="text" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
		</div>
	</div>

	<div class="form-group pull-left">
		<div class="input-group search date">
			<input type="text" class="form-control dtpicker" id="s-date" placeholder="Ngày bắt đầu" name="Date" value="{{ isset($request['Date']) ? $request['Date'] : \Carbon\Carbon::now()->startOfMonth()->format(FOMAT_DISPLAY_DAY) }}">
			<div class="input-group-addon">
				<span class="glyphicon glyphicon-th"></span>
			</div>
		</div>
	</div>

	<div class="form-group pull-left">
		<select class="selectpicker show-tick show-menu-arrow" id="select-user" name="UID[]" data-live-search="true" data-size="5"
				data-live-search-placeholder="Search" data-width="220px" data-actions-box="true" tabindex="-98" multiple>
{{--			<option value="">@lang('admin.staff')</option>--}}
			{!! GenHtmlOption($users, 'id', 'FullName', isset($request['UID']) ? $request['UID'] : '') !!}
		</select>
	</div>

	<div class="form-group pull-left">
		<select class="selectpicker show-tick show-menu-arrow" id="select-user" name="timeWorking" data-size="5"
				data-width="auto" data-actions-box="true" tabindex="-98">
			<option value="">Thời gian</option>
			<option value="1" {{ isset($request['timeWorking']) && $request['timeWorking'] == 1 ? 'selected' : '' }}>1 Ngày</option>
			<option value="2" {{ isset($request['timeWorking']) && $request['timeWorking'] == 2 ? 'selected' : '' }}>1 Tuần</option>
			<option value="3" {{ isset($request['timeWorking']) && $request['timeWorking'] == 3 ? 'selected' : '' }}>1 Tháng</option>
		</select>
	</div>
	<div class="form-group pull-left">
	    <select class="selectpicker show-tick show-menu-arrow" id="select-datavalue" name="dataValue" data-live-search="true"
            data-size="5" data-live-search-placeholder="Search" data-width="220px" data-actions-box="true" tabindex="-98">
            <option value="">Chức vụ</option>
			{!! GenHtmlOption($groupDataKey, 'DataValue', 'Name',isset($request['dataValue'])? $request['dataValue'] : '') !!}
        </select>
	</div>
	<div class="form-group pull-left">
		<div class="input-group">
			<button type="button" class="btn btn-primary" id="btn-search-working-schedule">@lang('admin.btnSearch')</button>
		</div>
	</div>

	<div class="form-group pull-right">
		<div class="input-group" id="area-btn">
			@can('action', $export)
				<button type="button" class="btn btn-success" id="export-working-schedule">@lang('admin.export-excel')</button>
			@endcan
			@can('action',$add)
				<button type="button" class="btn btn-primary" id="add-new-working-schedule">@lang('admin.working-schedule.add')</button>
			@endcan
		</div>
	</div>
</form>
<style>
	#working-schedule-search-form .form-group:not(:last-child), #area-btn button:not(:last-child) { margin-right: 3px; }
</style>
<script>
	var ajaxUrl = "{{ route('admin.WorkingScheduleInfo') }}";
	var newTitle = "@lang('admin.working-schedule.add')";
	var updateTitle = "@lang('admin.working-schedule.edit')";

	SetDatePicker($('.date'));
	$(".selectpicker").selectpicker({
		noneSelectedText : "Nhân viên"
	});
	$('#btn-search-working-schedule').click(function () {
		$('#working-schedule-search-form').submit();
	});

	$("#add-new-working-schedule").click(function () {
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

	$('#export-working-schedule').click(function (e) {
		e.preventDefault();
		var search = $('input[name=search]').val();
		var Date = $('#s-date').val();
		var req = '?search='+search+'&Date='+Date;
		var reqSearch = window.location.search;
		ajaxGetServerWithLoader(
			'{{ route('export.WorkingSchedule') }}'+reqSearch,
			'GET',
			$('#working-schedule-search-form').serializeArray(),
			function (data) {
				if (typeof data.errors !== 'undefined'){
					showErrors(data.errors);
				}else{
					window.location.href = '{{ route('export.WorkingSchedule') }}'+reqSearch;
				}
			}
		);
	});
</script>
