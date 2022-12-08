<form class="form-inline" id="meeting-search-form" action="" method="">
    <div class="form-group pull-left">
		<select class="selectpicker show-tick show-menu-arrow" id="select-room" name="RoomID" data-live-search="true" data-size="5"
				data-live-search-placeholder="Search" data-actions-box="true" tabindex="-98">
			<option value="">@lang('admin.room_report.room')</option>
			{!!
				GenHtmlOption($rooms, 'id', 'Name', isset($request['RoomID']) ? $request['RoomID'] : '')
			!!}
		</select>
	</div>
	<div class="form-group pull-left">
		<div class="input-group search date" id="sDate">
			<input type="text" class="form-control dtpicker" id="s-date" placeholder="Ngày bắt đầu" autocomplete="off" name="Date[]"
				value="{{ isset($request['Date']) ? $request['Date'][0] : '' }}">
			<div class="input-group-addon">
				<span class="glyphicon glyphicon-th"></span>
			</div>
		</div>
	</div>

	<div class="form-group pull-left">
		<div class="input-group search date" id="eDate">
			<input type="text" class="form-control dtpicker" id="e-date" placeholder="Ngày kết thúc" autocomplete="off" name="Date[]"
				value="{{ isset($request['Date']) ? $request['Date'][1] : '' }}">
			<div class="input-group-addon">
				<span class="glyphicon glyphicon-th"></span>
			</div>
		</div>
	</div>
	<div class="form-group pull-left">
        <button type="button" class="btn btn-primary btn-search" id="btn-search-meeting">@lang('admin.btnSearch')</button>
		@can('action',$export)
		<button class="btn btn-success" id="btn-export">@lang('admin.export-excel')</button>
		@endcan
	</div>
	@can('action', $add)
	<div class="pull-right">
		<button type="button" class="btn btn-primary btn-detail pull-right" id="add_new_room_report">@lang('admin.room_report.add_new')</button>
	</div>
	@endcan
</form>

<style>
	#meeting-search-form .form-group:not(:last-child) { margin-right: 3px; }
</style>

<script type="text/javascript" async>
    $(function () {
        SetDatePicker($('#sDate,#eDate'));
        $(function () {
            var search = $('input[name=search]').val();
            var RoomID = $('#select-room option:selected').val();
            var sDate = $('#s-date').val();
            var eDate = $('#e-date').val();
            $('#btn-export').click(function (e) {
                e.preventDefault();
                    window.location.href = '{{ route('export.RoomReport') }}?RoomID='+RoomID+'&Date[0]='+sDate+'&Date[1]='+eDate;
                });
            });
        $('.btn-search').click(function () {
            var sDate = moment($('#s-date').val(),'DD/MM/YYYY').format('YYYYMMDD');
            var eDate = moment($('#e-date').val(),'DD/MM/YYYY').format('YYYYMMDD');
            var repSDate = sDate.replace(/\D/g, "");
            var repEDate = eDate.replace(/\D/g, "");
            if (repSDate > repEDate && repSDate != '' && repEDate != ''){
                showErrors(['Ngày tìm kiếm không hợp lệ']);
            }else{
                $('#meeting-search-form').submit();
            }
            });
        });



</script>
