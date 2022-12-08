<form id="daily-search-form" class="form-inline" action="{{ route("admin.MonthlyReports", $idRequest) }}" method="GET">
	<div class="form-group pull-left margin-r-5">
			<div class="btn-group bootstrap-select show-tick show-menu-arrow" id="action-select-user">
				<select class="selectpicker show-tick show-menu-arrow" id="select-user" name="UserID" data-live-search="true" data-size="5" data-live-search-placeholder="Search" data-actions-box="true" tabindex="-98">
					<option value="">@lang('admin.chooseUser')</option>
					{!! GenHtmlOption($selectUser, 'id', 'FullName', isset($request['UserID']) ? $request['UserID'] : null) !!}
				</select>
			</div>
	</div>
	<div class="form-group pull-left margin-r-5">
		<button type="button" class="margin-r-5 btn btn-primary btn-search" id="btn-search-daily" >@lang('admin.btnSearch')</button>
	</div>

	@can('action', $add)
        <div class="form-group pull-left margin-r-5">
            <button type="button" class="btn btn-primary" id="add_daily" req="{{\Request::get('UserID')}}">
                @lang('admin.daily.add_daily')
            </button>
        </div>
	@endcan
</form>

<script type="text/javascript" async>
	 SetDatePicker($('.date'), {
        todayHighlight: true,
    });

    $('.selectpicker').selectpicker();
    $(".datepicker").datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true,
    });

	var idUser = $('#action-select-user option:selected').val() + '';

	function getUsersByActive(val) {
		ajaxServer(genUrlGet([
			'{{ route('admin.getUsersByActive') }}',
			'/' + val,
		]), 'GET', null, function(data) {
			html = ``;
			html += `<option value="">Chọn nhân viên</option>`;
			for(key in data) {
				var strSelected = '';
				if(data[key].id == idUser) {
					strSelected = 'selected';
				}
				html += `<option value="`+data[key].id+`" ${strSelected}>`+data[key].FullName+`</option>`;
			}
			$('#select-user').html(html);
			$('#select-user').selectpicker('refresh');
		});
	}

    $('#select-user').selectpicker();

    $('.btn-search').click(function () {
        var userId = $("#select-user option:selected").val() + '';
        console.log('userId ', userId);
        var sDate = moment($('#date-input').val(),'DD/MM/YYYY').format('YYYYMMDD');
        console.log(sDate);
        var eDate = moment($('#date-input_end').val(),'DD/MM/YYYY').format('YYYYMMDD');
        console.log(eDate);
        var repSDate = sDate.replace(/\D/g, "");
        var repEDate = eDate.replace(/\D/g, "");

        if (repSDate > repEDate){
            showErrors(['Ngày tìm kiếm không hợp lệ']);
        }else{
            $('#daily-search-form').submit();
        }

    });

    $("select[name='UserID']").on('change', function() {
        idUser = $('#action-select-user option:selected').val() + '';
    });

	$('#add_daily').click(function (event) {
		event.preventDefault();
		let data = { t_meeting: {{$t_meeting_week->id}} };
		ajaxGetServerWithLoader("{{ route('admin.MonthlyDetail') }}", "GET", data, function (data) {
			$('#popupModal').empty().html(data);
			$('#idRequest').attr('value',"{{$idRequest}}");
			$('#idReviewer').attr('value',"{{$ChairID}}");
			$('.detail-modal').modal('show');
		});
	})


</script>
