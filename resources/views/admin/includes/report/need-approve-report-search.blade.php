<form id="daily-search-form" class="form-inline" action="{{ route('admin.NeedApproveReports') }}" method="GET">
	{{-- @can('admin', $menu) --}}
	<div class="form-group pull-left margin-r-5">
		<div class="btn-group bootstrap-select show-tick show-menu-arrow" id="action-select-user">
			<select class="selectpicker show-tick show-menu-arrow" id="select-user" name="UserID[]" multiple
				title="@lang('admin.chooseUser')" data-live-search="true" data-size="5"
				data-live-search-placeholder="Search" data-actions-box="true" tabindex="-98">
				{!! GenHtmlOption($selectUser, 'id', 'FullName', isset($request['UserID']) ? $request['UserID'] : null)
				!!}
			</select>
		</div>
	</div>
	<div class="form-group pull-left margin-r-5">
		<div class="btn-group bootstrap-select show-tick show-menu-arrow" id="action-select-project">
			<select class="selectpicker show-tick show-menu-arrow" id="select-project" name="ProjectID[]" multiple
				title="@lang('admin.daily.chooseProject')" data-live-search="true" data-size="5"
				data-live-search-placeholder="Search" data-actions-box="true" tabindex="-98">
				{!! GenHtmlOption($selectProject, 'id', 'NameVi', isset($request['ProjectID']) ? $request['ProjectID'] :
				null) !!}
			</select>
		</div>
	</div>
	<div class="form-group pull-left margin-r-5">
		<div class="btn-group bootstrap-select show-tick show-menu-arrow" id="action-select-reportStatus">
			<select class="selectpicker show-tick show-menu-arrow" id="ReportStatus" name="ReportStatus"
				data-live-search="true" data-size="5" data-live-search-placeholder="Search" data-actions-box="true"
				tabindex="-98">
				<option value="0">@lang('admin.daily.Need Approve')</option>
				<option value="1" {{isset($reportStatus) && $reportStatus==1 ? 'selected' : '' }}>
					@lang('admin.daily.Rewrite')</option>
			</select>
		</div>
	</div>
	{{-- @endcan --}}
	<div class="form-group pull-left margin-r-5">
		<div class="input-group search date" id="date-daily-report">
			<input type="text" class="form-control" id="date-input" name="time"
				value="{{!isset($request['time']) ? Carbon\Carbon::now()->format(FOMAT_DISPLAY_MONTH) : $request['time'] }}">
			<div class="input-group-addon">
				<span class="glyphicon glyphicon-th"></span>
			</div>
		</div>
	</div>
	<div class="form-group pull-left margin-r-5">
		<button type="button" class="margin-r-5 btn btn-primary btn-search"
			id="btn-search-daily">@lang('admin.btnSearch')</button>
	</div>
</form>

<script language="javascript" async>
	var idUser = $('#action-select-user option:selected').val() + '';
	var idProject = $('#action-select-project option:selected').val() + '';
    $('.selectpicker').selectpicker();

	function getUsersByActive(val) {
		ajaxServer(genUrlGet([
			'{{ route('admin.getUsersByActive') }}',
			'/' + val,
		]), 'GET', null, function(data) {
			html = ``;
			// html += `<option value="">Chọn nhân viên</option>`;
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

	function getProjectsByActive(val) {
		ajaxServer(genUrlGet([
			'{{ route('admin.getProjectsByActive') }}',
			'/' + val,
		]), 'GET', null, function(data) {
			html = ``;
			for(key in data) {
				var strSelected = '';
				if(data[key].id == idProject) {
					strSelected = 'selected';
				}
				html += `<option value="`+data[key].id+`" ${strSelected}>`+data[key].NameVi+`</option>`;
			}
			$('#select-project').html(html);
			$('#select-project').selectpicker('refresh');
		});
	}

	$(function() {
		SetMothPicker($('#date-daily-report'));

		$('#select-user').selectpicker();
		$('#select-project').selectpicker();

		$('.btn-search').click(function () {
			$('#daily-search-form').submit();
		});

		$('#btn-export-daily-reports').click(function(e) {
			e.preventDefault();
            var reqSearch = window.location.search==''?'?time='+ (new Date().getMonth()+1)+'/'+ new Date().getFullYear():window.location.search;
            ajaxGetServerWithLoader('{{ route('export.exportDailyReports') }}'+reqSearch, 'GET', null, function (data) {
                if (typeof data.errors !== 'undefined'){
                    showErrors(data.errors);
                    return;
                }
                window.location.href = '{{ route('export.exportDailyReports') }}'+reqSearch;
            });
		});

		$('.btn-show-summary').click(function () {
			var html = $(this).html();
			$(this).html(html === 'Hiện tổng hợp báo cáo' ? 'Ẩn tổng hợp báo cáo' : 'Hiện tổng hợp báo cáo');
			$('.SummaryMonth').toggle('show');
		});

			
		var html =``;
			html += `<div class="btn-group btn-group-sm btn-block">`
			html +=`<button type="button" class="actions-btn bs-select-all btn btn-default">Chọn hết</button>`
			html +=`<button type="button" class="actions-btn bs-deselect-all btn btn-default">Bỏ chọn hết</button>`
			html +=`</div>`
			html +=`<div class="btn-group btn-group-sm btn-block">`;
			html +=`<button type="button" class="actions-btn btn btn-default" id="btnAll" val="1" style="width: 90px;">Tất cả</button>`;
			html +=`<button type="button" class="actions-btn btn btn-default" id="btnOn" val="2" style="width: 100px; background-color: #d8d8d8">Hoạt động</button>`;
			html +=`<button type="button" class="actions-btn btn btn-default" id="btnOff" val="3" style="width: 120px;">Ngừng hoạt động</button>`;
			html +=`</div>`;

		$('#action-select-user .bs-actionsbox').empty().append(html);

		$('#action-select-user').click(function () {

			$('.dropdown-menu.open').css({"max-height":"300px","width":"330px","height": "300px"});
			$('.dropdown-menu.open .inner').css({"max-height":"180px","width":"330px","height": "180px","overflow-y": "auto"});

			$('.dropdown-menu.open #btnAll').click(function () {
				var all = $('#btnAll').attr('val');
				$('#btnAll').css("background-color","#d8d8d8");
				$('#btnOn').css("background-color","#fff");
				$('#btnOff').css("background-color","#fff");
				getUsersByActive(all);
			});

			$('.dropdown-menu.open #btnOn').click(function () {
				var on = $('#btnOn').attr('val');
				$('#btnOn').css("background-color","#d8d8d8");
				$('#btnAll').css("background-color","#fff");
				$('#btnOff').css("background-color","#fff");
				getUsersByActive(on);
			});

			$('.dropdown-menu.open #btnOff').click(function () {
				var off = $('#btnOff').attr('val');
				$('#btnOff').css("background-color","#d8d8d8");
				$('#btnAll').css("background-color","#fff");
				$('#btnOn').css("background-color","#fff");
				getUsersByActive(off);
			})
		});

		$("select[name='ProjectID']").on('change', function() {
			idProject= $('#action-select-project option:selected').val() + '';
		});

					
		var html =``;
			html += `<div class="btn-group btn-group-sm btn-block">`
			html +=`<button type="button" class="actions-btn bs-select-all btn btn-default">Chọn hết</button>`
			html +=`<button type="button" class="actions-btn bs-deselect-all btn btn-default">Bỏ chọn hết</button>`
			html +=`</div>`
			html +=`<div class="btn-group btn-group-sm btn-block">`;
			html +=`<button type="button" class="actions-btn btn btn-default" id="projectAll" val="0" style="width: 90px;">Tất cả</button>`;
			html +=`<button type="button" class="actions-btn btn btn-default" id="projectOn" val="1" style="width: 100px; background-color: #d8d8d8">Hoạt động</button>`;
			html +=`<button type="button" class="actions-btn btn btn-default" id="projectOff" val="2" style="width: 120px;">Ngừng hoạt động</button>`;
			html +=`</div>`;
				
		$('#action-select-project .bs-actionsbox').empty().append(html);

		$('#action-select-project').click(function () {

			$('.dropdown-menu.open').css({"max-height":"300px","width":"330px","height": "300px"});
			$('.dropdown-menu.open .inner').css({"max-height":"300px","width":"330px","height": "180px","overflow-y": "auto"});

			$('.dropdown-menu.open #projectAll').click(function () {
				var all = $('#projectAll').attr('val');
				$('#projectAll').css("background-color","#d8d8d8");
				$('#projectOn').css("background-color","#fff");
				$('#projectOff').css("background-color","#fff");
				getProjectsByActive(all);
			});

			$('.dropdown-menu.open #projectOn').click(function () {
				var on = $('#projectOn').attr('val');
				$('#projectOn').css("background-color","#d8d8d8");
				$('#projectAll').css("background-color","#fff");
				$('#projectOff').css("background-color","#fff");
				getProjectsByActive(on);
			});

			$('.dropdown-menu.open #projectOff').click(function () {
				var off = $('#projectOff').attr('val');
				$('#projectOff').css("background-color","#d8d8d8");
				$('#projectAll').css("background-color","#fff");
				$('#projectOn').css("background-color","#fff");
				getProjectsByActive(off);
			})
		});
		
	});
</script>