<?php
    $canEdit = false;
    $canDelete = false;
?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('action', $edit)): ?>
    <?php
        $canEdit = true;
    ?>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('action', $delete)): ?>
    <?php
        $canDelete = true;
    ?>
<?php endif; ?>

<?php $__env->startPush('pageCss'); ?>
	<link rel="stylesheet" href="<?php echo e(asset('css/timekeeping.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="content-header">
	<h1 class="page-header"><?php echo app('translator')->get('admin.timekeepings'); ?></h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="time_keeping">
				<?php if(Session::has('success')): ?>
					<h4 style="color:red;"><?php echo Session::get('success'); ?></h4>
				<?php endif; ?>
				<div class="row">
					<div class="col-md-8 col-sm-9 col-xs-12">
						<form class="form-inline" method="get" id="timekeeping-search">
							<div class="form-group pull-left margin-r-5" id="cmbSelectUser">
								<div class="btn-group bootstrap-select show-tick show-menu-arrow user-custom" id="action-select-user">
									<select class="selectpicker show-tick show-menu-arrow user-custom" id="select-user" name="UserID" data-live-search="true" data-live-search-placeholder="Search" data-size="5" tabindex="-98">
										<?php if($checkUser->role_group != 3): ?>
											<?php echo GenHtmlOption($users, 'id', 'FullName', isset($request['UserID']) ? $request['UserID'] : (!isset($request['UserID']) ? Auth::user()->id : '')); ?>

										<?php else: ?>
											<option value="<?php echo e($checkUser->id); ?>" selected><?php echo e($checkUser->FullName); ?></option>
											<?php echo GenHtmlOption($users, 'id', 'FullName', isset($request['UserID']) ? $request['UserID'] : (!isset($request['UserID']) ? Auth::user()->id : '')); ?>

										<?php endif; ?>
									</select>
								</div>
							</div>

							<div class="form-group pull-left margin-r-5 date" id="date">
                                <div class="input-group search date">
                                    <input type="text" class="form-control" id="date-input" name="time" value="<?php echo e(!isset($request['time']) ? Carbon\Carbon::now()->format('m/Y') : $request['time']); ?>" >
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
							</div>
							<div class="form-group pull-left">
								<button type="button" class="btn btn-primary" id="view-dReport" name="view-dReport"><?php echo app('translator')->get('admin.btnSearch'); ?></button>
								<button type="button" class="btn btn-primary" id="displayTimekeeping">Ẩn bảng thống kê</button>
							</div>
						</form>
					</div>

					<div class="col-md-4 col-sm-3 col-xs-12">
						<form class="form-inline" method="POST" action="" style="float:right">
							<div class="form-group">
								<input type="text" hidden="true" value="" name="ym_export_excel" id="ym_export_excel">
								<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('action', $add)): ?>
								<button type="button" class="btn btn-primary" id="add" name="add"><?php echo app('translator')->get('admin.overtime.add_new'); ?></button>
								<?php endif; ?>
								
								<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('action', $export)): ?>
								<a class="btn btn-success" data-target="#exportModal" data-toggle="modal" id="btnExportModal"><?php echo app('translator')->get('admin.export-excel'); ?></a>
								<a class="btn btn-info" data-target="#exportModal" data-toggle="modal" id="btnExportAbsenceModal"><?php echo app('translator')->get('admin.export-excel-absence'); ?></a>
								<?php endif; ?>
							</div>
						</form>
					</div>
				</div>
				<div class="table-responsive table-timekeeping-detail">
					<table class="table data-table" id="table1">
						<thead class="thead-default">
							<tr>
								<th colspan="12" style="background: rgba(255, 225, 0, 0.5); text-align: left !important;">
									<?php echo app('translator')->get('admin.Staffs_name'); ?>:&nbsp; <?php echo e($userSelect->FullName); ?>

								</th>
							</tr>
							<tr>
								<th><?php echo app('translator')->get('admin.timekeeping.work'); ?></th>
								<th colspan="2"><?php echo e(number_format($timekeepings->totalKeeping, 2)); ?></th>
								<th rowspan="4"></th>
                                <th>Số lần làm việc tại công ty</th>
                                <th><?php echo e($timekeepings->checkinAtCompany); ?></th>
                                <th rowspan="4"></th>
                                <th><?php echo app('translator')->get('admin.timekeeping.solantre'); ?></th>
								<th><?php echo e($timekeepings->lateTimes); ?></th>
								<th rowspan="4"></th>
								<th><?php echo app('translator')->get('admin.timekeeping.sogiotre'); ?></th>
								<th><?php echo e(number_format($timekeepings->lateHours/60, 2)); ?></th>

							</tr>
							<tr>
								<th><?php echo app('translator')->get('admin.timekeeping.overtime'); ?></th>
								<th colspan="2"><?php echo e(number_format($timekeepings->overKeeping/60, 2)); ?></th>
                                <th>Số lần làm việc tại nhà</th>
                                <th><?php echo e($timekeepings->checkinAtHome); ?></th>
								<th><?php echo app('translator')->get('admin.timekeeping.solansom'); ?></th>
								<th><?php echo e($timekeepings->soonTimes); ?></th>
								<th><?php echo app('translator')->get('admin.timekeeping.sogiosom'); ?></th>
								<th><?php echo e(number_format($timekeepings->soonHours/60, 2)); ?></th>
							</tr>


















						</thead>
					</table>
				</div>

				<!-- Table daily report detail -->
				<div class="table-responsive table-timekeeping">
					<table class="table data-table" id="table_timekeeping">
						<thead class="thead-default">
							<tr>
								<th class="no-sort thead-th-custom" rowspan="2" style="width:100px !important;"><?php echo app('translator')->get('admin.day'); ?></th>
								<th class="thead-th-custom width5" rowspan="2" style="word-wrap: break-word;"><?php echo app('translator')->get('admin.overtime.week_day'); ?></th>
								<th class="thead-th-custom" colspan="2"><?php echo app('translator')->get('admin.timekeeping.TGvaora'); ?></th>
								<th class="thead-th-custom" rowspan="2"><?php echo app('translator')->get('admin.timekeeping.TimeWork'); ?></th>

								<th class="thead-th-custom" rowspan="2"><?php echo app('translator')->get('admin.timekeeping.late'); ?>
									<br>(phút)</th>
								<th class="thead-th-custom" rowspan="2"><?php echo app('translator')->get('admin.timekeeping.soon'); ?>
									<br>(phút)</th>
								<th class="thead-th-custom" rowspan="2"><?php echo app('translator')->get('admin.timekeeping.T_Gio'); ?></th>
								<th class="thead-th-custom" rowspan="2"><?php echo app('translator')->get('admin.timekeeping.T_GioTT'); ?></th>
								<th class="thead-th-custom" rowspan="2"><?php echo app('translator')->get('admin.timekeeping.total_work'); ?></th>
								
								<th class="thead-th-custom" rowspan="2" style="width:210px"><?php echo app('translator')->get('admin.absences'); ?></th>
								<th class="thead-th-custom" rowspan="2" style="width:200px"><?php echo app('translator')->get('admin.timekeeping.type'); ?></th>
								<?php if($canEdit || $canDelete): ?>
									<th class="thead-th-custom width5pt" rowspan="2"><?php echo app('translator')->get('admin.action'); ?></th>
								<?php endif; ?>
							</tr>
							<tr>
								<th class="thead-th-custom">Vào</th>
								<th class="thead-th-custom">Ra</th>
								
							</tr>
						</thead>
						<tbody>
						<?php $__currentLoopData = $timekeepings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $timekeeping): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr <?php echo e($timekeeping->weekday == 'T7' ? 'class=weekend-7' : ''); ?> <?php echo e($timekeeping->weekday == 'CN' ? 'class=weekend-cn' : ''); ?> >
								<td id="tk-date"><?php echo e(\Carbon\Carbon::parse($timekeeping->Date)->format('d/m/Y')); ?></td>
								<td><?php echo e($timekeeping->weekday); ?></td>
								<td><?php echo e(isset($timekeeping->TimeIn) ? \Carbon\Carbon::parse($timekeeping->TimeIn)->format("H:i:s") : ''); ?></td>
								<td><?php echo e(isset($timekeeping->TimeOut) ? \Carbon\Carbon::parse($timekeeping->TimeOut)->format("H:i:s") : ''); ?></td>
								<td>
									<?php if($timekeeping->STimeOfDay && $timekeeping->ETimeOfDay != null): ?>
										<?php echo e($timekeeping->STimeOfDay); ?> - <?php echo e(isset( $timekeeping->SBreakOfDay) ?  $timekeeping->SBreakOfDay : \Carbon\Carbon::parse(App\MasterData::where('DataValue', 'WT002')->first()->Name)->format("H:i:s")); ?> <br>
										<?php echo e(isset($timekeeping->EBreakOfDay)  ? $timekeeping->EBreakOfDay :  \Carbon\Carbon::parse(App\MasterData::where('DataValue', 'WT002')->first()->DataDescription)->format("H:i:s")); ?> - <?php echo e($timekeeping->ETimeOfDay); ?>


									<?php endif; ?>

								</td>
								<td><?php echo e($timekeeping->late != "00:00:00" ? \Carbon\Carbon::parse($timekeeping->late)->format("H:i:s") : ''); ?></td>
								<td><?php echo e($timekeeping->soon != "00:00:00" ? \Carbon\Carbon::parse($timekeeping->soon)->format("H:i:s") : ''); ?></td>
								<td><?php echo e(round($timekeeping->hours, 2, PHP_ROUND_HALF_UP)); ?></td>
								<td><?php echo e(isset($timekeeping->hoursTT) ? \Carbon\Carbon::parse($timekeeping->hoursTT)->format("H:i:s") : ''); ?></td>
								<td><?php echo e($timekeeping->keeping > 1 ? 1 : number_format($timekeeping->keeping, 2)); ?></td>
								
								
								<td>
									<?php $__currentLoopData = $timekeeping->absence; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $absence): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<?php if($timekeeping->weekday != 'T7'&&$timekeeping->weekday != 'CN'): ?>
										
										

















										
											<a class="action-col view" style="text-decoration: none"><?php echo e($absence->Name); ?> (<?php echo e($absence->STime); ?> - <?php echo e($absence->ETime); ?>)</a><br/>
										<?php endif; ?>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<?php if($timekeeping->calendarEvent): ?>
										<a class="action-col view" style="text-decoration: none">Làm bù (<?php echo e($timekeeping->calendarEvent->StartDate); ?>)</a><br/>
									<?php endif; ?>
								</td>
								<td>
									<?php if(isset($timekeeping->type) && count($timekeeping->type) > 0): ?>
										<?php $__currentLoopData = $timekeeping->type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<span><?php echo e($type->Type); ?></span><br>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<?php elseif($timekeeping->id): ?>
										<span><?php echo app('translator')->get('admin.import'); ?></span>
									<?php endif; ?>
								</td>
								<?php if($canEdit || $canDelete): ?>
									<td class="text-center">
										<?php if($canEdit): ?>
											<span class="action-col update edit update-timekeeping" item-id="<?php echo e($timekeeping->id); ?>" item-date="<?php echo e($timekeeping->Date); ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
										<?php endif; ?>
										<?php if($canDelete && $timekeeping->id): ?>
											<span class="action-col update delete delete-timekeeping"  item-id="<?php echo e($timekeeping->id); ?>"><i class="fa fa-times" aria-hidden="true"></i></span>
										<?php endif; ?>
									</td>
								<?php endif; ?>
							</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
				</div>

				<div id="importModal" class="modal draggable fade in" role="dialog" tabindex="-1">
					<div>
						<div class="modal-dialog modal-lg ui-draggable width550">
							<!-- Modal content-->
							<div class="modal-content drag">
								<form class="form-horizontal" action="<?php echo e(route('admin.importTimekeepingNew')); ?>" method="POST" enctype="multipart/form-data">
									<div class="modal-header ui-draggable-handle" style="cursor: move;">
										<button type="button" class="close" data-dismiss="modal" id="">×</button>
										<h4 class="modal-title">Nhập dữ liệu chấm công</h4>
									</div>
									<div class="modal-body row">
										<div class="save-errors"></div>
										<?php echo csrf_field(); ?>
										<div class="col-sm-3">
											<label><?php echo app('translator')->get('admin.document.select_file'); ?>&nbsp;<i class="text-red">*</i>:</label>
										</div>
										<div class="col-sm-9" style="margin-bottom: 5px;">
											<input type="file" name="file" class="form-control">
										</div>
										<div class="col-sm-3">
											<label>Thay thế&nbsp; : </label>
										</div>
										<div class="col-sm-9" style="margin-bottom: 5px;">
											<div>
												<input type="checkbox" name="Reset" class='action-col' value="0">
											</div>
										</div>
										<div class="col-sm-3" id = 'choosemonth' style="margin-top: 10px;">
											<label><?php echo app('translator')->get('admin.timekeeping.DL_month'); ?>&nbsp;<i class="text-red">*</i>:</label>
										</div>
										<div class="col-sm-9" style="margin-bottom: 10px;" id = 'choosemonths'>
											<div class="input-group date " id="date-timemeeting">
												<input type="text" class="form-control" id="date-timemeeting" name="Date" value="<?php echo e(Carbon\Carbon::now()->format(FOMAT_DISPLAY_MONTH)); ?>">
												<div class="input-group-addon">
													<span class="glyphicon glyphicon-th"></span>
												</div>
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel"><?php echo app('translator')->get('admin.btnCancel'); ?></button>
										
										<button type="button" class="btn btn-primary btn-sm" id="save"><?php echo app('translator')->get('admin.btnSave'); ?></button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<div id="exportModal" class="modal draggable fade in" role="dialog" tabindex="-1">
					<div>
						<div class="modal-dialog modal-lg ui-draggable width550">
							<!-- Modal content-->
							<div class="modal-content drag">
								<form class="detail-form" method="GET" enctype="multipart/form-data">
									<div class="modal-header ui-draggable-handle" style="cursor: move;">
										<button type="button" class="close" data-dismiss="modal" id="">×</button>
										<h4 class="modal-title">Xuất dữ liệu chấm công</h4>
									</div>
									<div class="modal-body">
										<div class="save-errors"></div>
										<?php echo csrf_field(); ?>
										<div class="form-group row">
											<div class="col-sm-4">
												<label><?php echo app('translator')->get('admin.timekeeping.DL_month'); ?>&nbsp;<i class="text-red">*</i>:</label>
											</div>
											<div class="col-sm-8" style="margin-bottom: 5px;">
												<div class="input-group date " id="date-timemeetings">
													<input type="text" class="form-control" id="date-timemeetings" name="Date1" value="<?php echo e(Carbon\Carbon::now()->format(FOMAT_DISPLAY_MONTH)); ?>">
													<div class="input-group-addon">
														<span class="glyphicon glyphicon-th"></span>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-sm-4">
												<label><?php echo app('translator')->get('admin.Active_status'); ?>&nbsp;:</label>
											</div>
											<div class="col-sm-8" style="margin-bottom: 5px;">
												<div class="input-group date " id="statusUser">
													<select class="selectpicker show-menu-arrow form-control" id='select-status' data-live-search='true'data-live-search-placeholder='Search' data-size='6' data-width="336px">
														<option value="">[<?php echo app('translator')->get('admin.all_status'); ?>]</option>
														<option value="1"><?php echo app('translator')->get('admin.on'); ?></option>
														<option value="0"><?php echo app('translator')->get('admin.off'); ?></option>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-sm-4">
												<label><?php echo app('translator')->get('admin.chooseUser'); ?>&nbsp;<i class="text-red">*</i>:</label>
											</div>
											<div class="col-sm-8" style="margin-bottom: 5px;" id="userStatus">
												<div class="input-group date " id="User">
													<select class="selectpicker show-menu-arrow form-control" id='select-userStatus' multiple data-live-search='true' data-live-search-placeholder='Search' data-actions-box="true" data-size='6' data-width="336px">
														<?php echo GenHtmlOption($users1, 'id', 'FullName'); ?>

													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel"><?php echo app('translator')->get('admin.btnCancel'); ?></button>
										<button type="button" class="btn btn-primary btn-sm" id="btn-export-timekeeping"><?php echo app('translator')->get('admin.export-excel'); ?></button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</section>
<style>
	.table th, .table td {
		border: 1px solid #bdb9b9 !important;
		text-align: center;
		vertical-align: middle !important;
		background-color: #fff;
	}
	.table-timekeeping .table tr th {
		background-color: #c6e2ff;
	}
    .weekend-7 td { background: #CCCCFF !important; }
    .weekend-cn td { background: #FF99CC !important; }
	#table1 { margin-bottom: 0px; }
	#table_timekeeping { margin-top: 20px; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
	<script type="text/javascript" async>
		var idUser = $('#action-select-user option:selected').val() + '';
		var check = '<?php echo e(count($errors) ? $errors->any() : 0); ?>';
		if(check != 0){
			setTimeout(function(){ showErrors('<?php echo e($errors->first()); ?>'); }, 200);
		}
		var statusUser = $('#select-status').val();
		if(statusUser == ''){
			$('#select-userStatus').html('');
			html = '';
			var arrUser = '<?php echo e($users1); ?>';
			$('#select-status option[value=1]').attr('selected','selected');
			arrUser = arrUser.replaceAll('&quot;', '"');
			if(arrUser.length != '') {
				try {
					arrUser = JSON.parse(arrUser);
				} catch {
					arrUser = [];
				}
				for (var i = 0; i < arrUser.length; i++) {
					if(arrUser[i]['Active'] == 1) {
						html += '<option value= "'+arrUser[i]['id']+'">'+arrUser[i]['FullName']+'</option>';
					}
				}
			}
			$('#select-userStatus').append(html).selectpicker('refresh');
		}
		$('#select-status').change(function() {
			$('#select-userStatus').html('');
			html = '';
			var statusUser = $('#select-status').val();
			var arrUser = '<?php echo e($users1); ?>';
			arrUser = arrUser.replaceAll('&quot;', '"');
			if(arrUser.length != '') {
				try {
					arrUser = JSON.parse(arrUser);
				} catch {
					arrUser = [];
				}

				if(arrUser.length > 0) {
					if(statusUser == '') {
						for (var i = 0; i < arrUser.length; i++) {
							html += '<option value= "'+arrUser[i]['id']+'">'+arrUser[i]['FullName']+'</option>';
						}
					} else if(statusUser == 0) {
						for (var i = 0; i < arrUser.length; i++) {
							if(arrUser[i]['Active'] == 0) {
								html += '<option value= "'+arrUser[i]['id']+'">'+arrUser[i]['FullName']+'</option>';
							}
						}
					} else if(statusUser == 1) {
						for (var i = 0; i < arrUser.length; i++) {
							if(arrUser[i]['Active'] == 1) {
								html += '<option value= "'+arrUser[i]['id']+'">'+arrUser[i]['FullName']+'</option>';
							}
						}
					}
					$('#select-userStatus').append(html).selectpicker('refresh');
				}
			}
		});

		SetMothPicker($('#date-timemeeting, #date-timemeetings'));
		var ajaxUrl = '<?php echo e(route('admin.UserInfo')); ?>';
		var ajaxUrl_detail = '<?php echo e(route('admin.detailTimekeepingNew')); ?>';
		var updateTitle = 'Sửa chấm công';

		$(function () {
			$('#select-user').selectpicker();

			SetMothPicker($('#date'));
			$('input[name=Reset]').prop('checked', '');
			$('#choosemonth').hide();
			$('#choosemonths').hide();
			$('#view-dReport').click(function () {
				$('#timekeeping-search').submit();
			});

			$('#btnImportModal').on('click', function(e) {
				$('#importModal .modal-title').html('Nhập dữ liệu chấm công');
			});

			$('#btnExportModal').on('click', function(e) {
				$('#exportModal .modal-title').html('Xuất dữ liệu chấm công');
			});

			$('#btnExportAbsenceModal').on('click',function(){
				$('#exportModal .modal-title').html('Xuất dữ liệu vắng mặt');
			});

			$('#btn-export-timekeeping').click(function (e) {
				e.preventDefault();
				if($('input[name=Date1]').val() == '') {
					alert('Bạn chưa chọn tháng');
					return true;
				} else {
					timeExport = $('input[name=Date1]').val();
				}
				if($('#select-userStatus').val() == '') {
					alert('Bạn chưa chọn nhân viên');
					return true;
				} else {
					var arrUser = $('#select-userStatus').val();
					var User = '';
					for (var i = 0; i < arrUser.length; i++) {
						User += (i == arrUser.length-1) ? arrUser[i] : arrUser[i]+',';
					}
				}
				$('.loadajax').show();
				if($('#exportModal .modal-title').text() == 'Xuất dữ liệu vắng mặt'){
					ajaxServer('<?php echo e(route('admin.exportAbsenceTimekeepingNew')); ?>/'+timeExport+'/'+User, 'GET',null, function (data) {
						if (typeof data.errors !== 'undefined') {
							$('.loadajax').hide();
							showErrors(data.errors[0]);
						} else {
							window.location.href = '<?php echo e(route('admin.exportAbsenceTimekeepingNew')); ?>/'+timeExport+'/'+User;
							$('#exportModal').modal('hide');
							// $('#select-status option[value=1]').attr('selected','selected');
							// selectUser();
							$('.loadajax').hide();
						}
					})
				}else{
					ajaxServer('<?php echo e(route('admin.exportTimekeepingNew')); ?>/'+timeExport+'/'+User, 'GET',null, function (data) {
						if (typeof data.errors !== 'undefined') {
							$('.loadajax').hide();
							showErrors(data.errors[0]);
						} else {
							window.location.href = '<?php echo e(route('admin.exportTimekeepingNew')); ?>/'+timeExport+'/'+User;
							$('#exportModal').modal('hide');
							// $('#select-status option[value=1]').attr('selected','selected');
							// selectUser();
							$('.loadajax').hide();
						}
					})
				}
				// window.location.href = '<?php echo e(route('admin.exportTimekeeping')); ?>/'+timeExport+'/'+User;
			});

			$('#save').click(function() {
				if($('input[name=file]').val() == ''){
                    showConfirm('Bạn chưa chọn file?');
                }else{
                    if($('input[name=Reset]').val() == 1){
                        showConfirm('Bạn có chắc muốn thay thế không, lựa chọn này có thể làm thay đổi dữ liệu cũ?',
                        function () {
                            $('.form-horizontal').submit();
                        })
                    }else{
                        $('.form-horizontal').submit();
                    }
                }
			});

			$( "input[type=checkbox]" ).change( function() {
				if($('input[name=Reset]').val() == 1) {
					$('input[name=Reset]').val(0);
					$('#choosemonth').hide();
					$('#choosemonths').hide();
				} else {
					$('input[name=Reset]').val(1);
					$('#choosemonth').show();
					$('#choosemonths').show();
				}
			});

			if('<?php echo e($checkUser->role_group); ?>' != 3 ){
                var html =``;
                html +=`<div class="bs-actionsbox">`;
                html +=`<div class="btn-group btn-group-sm btn-block">`;
                html +=`<button type="button" class="actions-btn btn btn-default btnActionStatus" id="btnAll" val="1" style="width: 90px;">Tất cả</button>`;
                html +=`<button type="button" class="actions-btn btn btn-default btnActionStatus" id="btnOn" val="2" style="width: 100px;background-color: #d8d8d8">Hoạt động</button>`;
                html +=`<button type="button" class="actions-btn btn btn-default btnActionStatus" id="btnOff" val="3" style="width: 120px;">Ngừng hoạt động</button>`;
                html +=`</div></div>`;
                $('#cmbSelectUser .dropdown-menu.open').append(html);

                $('#action-select-user').click(function () {
                    $('#timekeeping-search .dropdown-menu.open').css({"max-height":"220px","width":"330px"});
                });

                $('#timekeeping-search .dropdown-menu.open .btnActionStatus').on('click', function(e) {
                    e.preventDefault();
                    $(this).css("background-color","#d8d8d8");
                    $(this).siblings().css("background-color","#fff");
                    ajaxServer(genUrlGet([
                        '<?php echo e(route('admin.getUsersByActive')); ?>',
                        '/' + $.trim($(this).attr('val')),
                    ]), 'GET', null, function(data) {
                        var html = '';
                        for(key in data) {
                            var strSelected = '';
                            if(data[key].id == idUser) {
                                strSelected = 'selected';
                            }
                            html += `<option value="${data[key].id}" ${strSelected}>${data[key].FullName}</option>`;
                        }
                        $('#select-user').html(html).selectpicker('refresh');
                    });
                });
            }else{
				var html =``;
                html +=`<div class="bs-actionsbox">`;
                html +=`<div class="btn-group btn-group-sm btn-block">`;
                html +=`<button type="button" class="actions-btn btn btn-default btnActionStatus" id="btnAll" val="1" style="width: 90px;">Tất cả</button>`;
                html +=`<button type="button" class="actions-btn btn btn-default btnActionStatus" id="btnOn" val="2" style="width: 100px;background-color: #d8d8d8">Hoạt động</button>`;
                html +=`<button type="button" class="actions-btn btn btn-default btnActionStatus" id="btnOff" val="3" style="width: 120px;">Ngừng hoạt động</button>`;
                html +=`</div></div>`;
                $('#cmbSelectUser .dropdown-menu.open').append(html);

                $('#action-select-user').click(function () {
                    $('#timekeeping-search .dropdown-menu.open').css({"max-height":"220px","width":"330px"});
                });

                $('#timekeeping-search .dropdown-menu.open .btnActionStatus').on('click', function(e) {
                    e.preventDefault();
                    $(this).css("background-color","#d8d8d8");
                    $(this).siblings().css("background-color","#fff");
                    ajaxServer(genUrlGet([
                        '<?php echo e(route('admin.getUsersByActive')); ?>',
                        '/' + $.trim($(this).attr('val')),
                    ]), 'GET', null, function(data) {
                        var html = '';
                        for(key in data) {
                            var strSelected = '';
                            if(data[key].id == idUser) {
                                strSelected = 'selected';
                            }
                            html += `<option value="${data[key].id}" ${strSelected}>${data[key].FullName}</option>`;
                        }
                        $('#select-user').html(html).selectpicker('refresh');
                    });
                });
			}
		});

		$('#add').click(function () {
			var searchUser = $('#select-user').val();
			ajaxGetServerWithLoader(ajaxUrl_detail+'?searchUser='+searchUser, 'GET', null, function (data) {
				$('#popupModal').empty().html(data);
				$('#timeKeeping-info').modal('show');
			});
		});

		$('.update-timekeeping').click(function () {
			var itemId = $(this).attr('item-id');
			var itemDate = $(this).attr('item-date');
			var searchUser = $('#select-user').val();
			ajaxGetServerWithLoader(ajaxUrl_detail+'/'+itemId+'?searchUser='+searchUser+'&date='+itemDate, 'GET', null, function (data) {
				$('#popupModal').empty().html(data);
				$('.modal-title').html(updateTitle);
				$('#timeKeeping-info').modal('show');
			});
		});

		$('.delete-timekeeping').click(function () {
			t = confirm(confirmMsg);
			if(!t)
				return;
			var itemId = $(this).attr('item-id');
			ajaxServer(ajaxUrl_detail+'/'+itemId+'/del', 'GET', null, function (data) {
				if (data == 1) {
					locationPage();
				}
			});
		});

		$('#displayTimekeeping').click(function(e) {
			if ($('#displayTimekeeping').text() =='Ẩn bảng thống kê') {
				$('#displayTimekeeping').text('Hiện bảng thống kê');
				$('.table-timekeeping-detail').slideToggle();
			} else {
				$('#displayTimekeeping').text('Ẩn bảng thống kê');
				$('.table-timekeeping-detail').slideToggle();
			}
		});

		var Title = 'Lý do vắng mặt ngày';
		$('#table_timekeeping tbody tr td a.view').click(function() {
			var dateTr = $(this).closest('tr').find('td:first-child').text();
			var date = dateTr.split("/").reverse().join("-");
			var UserID = $( "#select-user option:selected" ).val();

			ajaxGetServerWithLoader('<?php echo e(route('admin.AbsenceTimekeepingNew')); ?>', 'POST', {
				date: date,
				UserID: UserID,
			}, function (data) {
				$('#popupModal').empty().html(data);
				$('.modal-title').html(Title);
				$('#modal-absence-list').modal('show');
			});
		});
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.default.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\DMT\resources\views/admin/layouts/default/checkin/timekeeping.blade.php ENDPATH**/ ?>