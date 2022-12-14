<form id="daily-search-form" class="form-inline" action="<?php echo e(route('admin.DailyReports')); ?>" method="GET">
	<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin', $menu)): ?>
	<div class="form-group pull-left margin-r-5">
		<div class="btn-group bootstrap-select show-tick show-menu-arrow" id="action-select-user">
			<select class="selectpicker show-tick show-menu-arrow" id="select-user" name="UserID" data-live-search="true" data-size="5" data-live-search-placeholder="Search" data-actions-box="true" tabindex="-98">
				<option value=""><?php echo app('translator')->get('admin.chooseUser'); ?></option>
				<?php echo GenHtmlOption($selectUser, 'id', 'FullName', isset($request['UserID']) ? $request['UserID'] : Auth::user()->id); ?>

			</select>
		</div>
	</div>
	<div class="form-group pull-left margin-r-5">
		<div class="btn-group bootstrap-select show-tick show-menu-arrow" id="action-select-project">
			<select class="selectpicker show-tick show-menu-arrow" id="select-project" name="ProjectID"
				data-live-search="true" data-size="5" data-live-search-placeholder="Search" data-actions-box="true"
				tabindex="-98">
				<option value=""><?php echo app('translator')->get('admin.daily.chooseProject'); ?></option>
				<?php echo GenHtmlOption($selectProject, 'id', 'NameVi', isset($request['ProjectID']) ? $request['ProjectID'] :
				''); ?>

			</select>
		</div>
	</div>
	<?php endif; ?>
	<div class="form-group pull-left margin-r-5">
		<div class="input-group search date" id="date-daily-report">
			<input type="text" class="form-control" id="date-input" name="time" value="<?php echo e(!isset($request['time']) ? Carbon\Carbon::now()->format(FOMAT_DISPLAY_MONTH) : $request['time']); ?>">
			<div class="input-group-addon">
				<span class="glyphicon glyphicon-th"></span>
			</div>
		</div>
	</div> 
	<div class="form-group pull-left margin-r-5">
		<button type="button" class="margin-r-5 btn btn-primary btn-search" id="btn-search-daily" ><?php echo app('translator')->get('admin.btnSearch'); ?></button>
		<button type="button" class="btn btn-primary btn-show-summary"><?php echo app('translator')->get('admin.daily.show_daily_report'); ?></button>
	</div>

	<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('action', $add)): ?>
	<div class="form-group pull-left margin-r-5">
		<button type="button" class="btn btn-primary" id="add_daily" req="<?php echo e(\Request::get('UserID')); ?>"><?php echo app('translator')->get('admin.daily.add_daily'); ?></button>

	</div>
	<?php endif; ?>
	<div class="form-group">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('action', $dailyExport)): ?>  
        	<button class="btn btn-success" id="btn-export-daily-reports"><?php echo app('translator')->get('admin.export-excel'); ?></button>
        <?php endif; ?>
    </div>
</form>

<script language="javascript" async>
	var idUser = $('#action-select-user option:selected').val() + '';
	var idProject = $('#action-select-project option:selected').val() + '';

	function getUsersByActive(val) {
		ajaxServer(genUrlGet([
			'<?php echo e(route('admin.getUsersByActive')); ?>',
			'/' + val,
		]), 'GET', null, function(data) {
			html = ``;
			html += `<option value="">Ch???n nh??n vi??n</option>`;
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
			'<?php echo e(route('admin.getProjectsByActive')); ?>',
			'/' + val,
		]), 'GET', null, function(data) {
			console.log(data);
			html = ``;
			html += `<option value="">Ch???n d??? ??n</option>`;
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

		$('#select-project').selectpicker();
		$('#select-user').selectpicker();

		$('.btn-search').click(function () {
			var userId = $("#select-user option:selected").val() + '';
			if (StringIsNullOrEmpty(userId)) {
				showErrors(['Ch??a ch???n nh??n vi??n']);
				return;
			}
			$('#daily-search-form').submit();
		});

		$('#btn-export-daily-reports').click(function(e) {
			e.preventDefault();
            var reqSearch = window.location.search==''?'?time='+ (new Date().getMonth()+1)+'/'+ new Date().getFullYear():window.location.search;
            ajaxGetServerWithLoader('<?php echo e(route('export.exportDailyReports')); ?>'+reqSearch, 'GET', null, function (data) {
                if (typeof data.errors !== 'undefined'){
                    showErrors(data.errors);
                    return;
                }
                window.location.href = '<?php echo e(route('export.exportDailyReports')); ?>'+reqSearch;
            });
		});

		$('.btn-show-summary').click(function () {
			var html = $(this).html();
			$(this).html(html === 'Hi???n t???ng h???p b??o c??o' ? '???n t???ng h???p b??o c??o' : 'Hi???n t???ng h???p b??o c??o');
			$('.SummaryMonth').toggle('show');
		});

		$("select[name='UserID']").on('change', function() {
			idUser = $('#action-select-user option:selected').val() + '';
		});

		var html =``;
		html +=`<div class="bs-actionsbox">`;
		html +=`<div class="btn-group btn-group-sm btn-block">`;
		html +=`<button type="button" class="actions-btn btn btn-default" id="btnAll" val="1" style="width: 90px;">T???t c???</button>`;
		html +=`<button type="button" class="actions-btn btn btn-default" id="btnOn" val="2" style="width: 100px; background-color: #d8d8d8">Ho???t ?????ng</button>`;
		html +=`<button type="button" class="actions-btn btn btn-default" id="btnOff" val="3" style="width: 120px;">Ng???ng ho???t ?????ng</button>`;
		html +=`</div></div>`;
		$('.dropdown-menu.open').append(html);

		$('#action-select-user').click(function () {
			$('.dropdown-menu.open').css({"max-height":"220px","width":"330px"});

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
			html +=`<div class="btn-group btn-group-sm btn-block">`;
			html +=`<button type="button" class="actions-btn btn btn-default" id="projectAll" val="0" style="width: 90px;">T???t c???</button>`;
			html +=`<button type="button" class="actions-btn btn btn-default" id="projectOn" val="1" style="width: 100px; background-color: #d8d8d8">Ho???t ?????ng</button>`;
			html +=`<button type="button" class="actions-btn btn btn-default" id="projectOff" val="2" style="width: 120px;">Ng???ng ho???t ?????ng</button>`;
			html +=`</div>`;

		$('#action-select-project .bs-actionsbox').empty().append(html);
		
		$('#action-select-project').click(function () {
			$('.dropdown-menu.open').css({"max-height":"220px","width":"330px"});

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
<?php /**PATH D:\DMT\resources\views/admin/includes/daily-report-search.blade.php ENDPATH**/ ?>