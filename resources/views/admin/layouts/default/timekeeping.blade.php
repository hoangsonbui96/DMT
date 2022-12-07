@extends('admin.layouts.default.app')

@php
    $canEdit = false;
    $canDelete = false;
@endphp

@can('action', $edit)
    @php
        $canEdit = true;
    @endphp
@endcan

@can('action', $delete)
    @php
        $canDelete = true;
    @endphp
@endcan

@push('pageCss')
	<link rel="stylesheet" href="{{ asset('css/timekeeping.css') }}">
@endpush

@section('content')
<section class="content-header">
	<h1 class="page-header">@lang('admin.timekeepings')</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="time_keeping">
				@if(Session::has('success'))
					<h4 style="color:red;">{!! Session::get('success') !!}</h4>
				@endif
				<div class="row">
					<div class="col-md-8 col-sm-9 col-xs-12">
						<form class="form-inline" method="get" id="timekeeping-search">
							<div class="form-group pull-left margin-r-5" id="cmbSelectUser">
								<div class="btn-group bootstrap-select show-tick show-menu-arrow user-custom" id="action-select-user">
									<select class="selectpicker show-tick show-menu-arrow user-custom" id="select-user" name="UserID" data-live-search="true" data-live-search-placeholder="Search" data-size="5" tabindex="-98">
										@if($checkUser->role_group != 3)
											{!! GenHtmlOption($users, 'id', 'FullName', isset($request['UserID']) ? $request['UserID'] : (!isset($request['UserID']) ? Auth::user()->id : '')) !!}
										@else
											<option value="{{ $checkUser->id }}" selected>{{ $checkUser->FullName }}</option>
											{!! GenHtmlOption($users, 'id', 'FullName', isset($request['UserID']) ? $request['UserID'] : (!isset($request['UserID']) ? Auth::user()->id : '')) !!}
										@endif
										{{-- @can('action', $add) --}}
										{{-- {!! GenHtmlOption($users, 'id', 'FullName', isset($request['UserID']) ? $request['UserID'] : (!isset($request['UserID']) ? Auth::user()->id : '')) !!} --}}
										{{-- @endcan --}}
									</select>
								</div>
							</div>

							<div class="form-group pull-left margin-r-5 date" id="date">
                                <div class="input-group search date">
                                    <input type="text" class="form-control" id="date-input" name="time" value="{{ !isset($request['time']) ? Carbon\Carbon::now()->format('m/Y') : $request['time'] }}" >
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
							</div>
							<div class="form-group pull-left">
								<button type="button" class="btn btn-primary" id="view-dReport" name="view-dReport">@lang('admin.btnSearch')</button>
								<button type="button" class="btn btn-primary" id="displayTimekeeping">Ẩn bảng thống kê</button>
							</div>
						</form>
					</div>

					<div class="col-md-4 col-sm-3 col-xs-12">
						<form class="form-inline" method="POST" action="" style="float:right">
							<div class="form-group">
								<input type="text" hidden="true" value="" name="ym_export_excel" id="ym_export_excel">
								@can('action', $add)
								<button type="button" class="btn btn-primary" id="add" name="add">@lang('admin.overtime.add_new')</button>
								@endcan
								@can('action', $import)
								<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#importModal" id="btnImportModal">@lang('admin.import')</button>
								@endcan
								@can('action', $export)
								<a class="btn btn-success" data-target="#exportModal" data-toggle="modal" id="btnExportModal">@lang('admin.export-excel')</a>
								<a class="btn btn-info" data-target="#exportModal" data-toggle="modal" id="btnExportAbsenceModal">@lang('admin.export-excel-absence')</a>
								@endcan
							</div>
						</form>
					</div>
				</div>

				<div class="table-responsive table-timekeeping-detail">
					<table class="table table-striped table-bordered table-hover data-table" id="table1">
						<thead class="thead-default">
							<tr>
								<th colspan="10" style="background: yellow;">
									<div style="float: left;">@lang('admin.timekeeping.userId') : {{ $userSelect->IDFM }}</div>
									<div style="float: right; margin-right: 30px;">@lang('admin.Staffs_name') : {{ $userSelect->FullName }}</div>
								</th>
							</tr>
							<tr>
								<th>@lang('admin.timekeeping.work')</th>
								<th colspan="2">{{ number_format($timekeepings->totalKeeping, 2) }}</th>
								<th rowspan="4"></th>
								<th>@lang('admin.timekeeping.solantre')</th>
								<th>{{ $timekeepings->lateTimes }}</th>
								<th rowspan="4"></th>
								<th>@lang('admin.timekeeping.sogiotre')</th>
								<th>{{ number_format($timekeepings->lateHours, 2) }}</th>
								<th rowspan="4"></th>
							</tr>
							<tr>
								<th>@lang('admin.timekeeping.overtime')</th>
								<th>{{ number_format($timekeepings->overKeeping, 2) }}</th>
								<th>0.00</th>
								<th>@lang('admin.timekeeping.solansom')</th>
								<th>{{ $timekeepings->soonTimes }}</th>
								<th>@lang('admin.timekeeping.sogiosom')</th>
								<th>{{ number_format($timekeepings->soonHours, 2) }}</th>
							</tr>
							<tr>
								<th>@lang('admin.event.day-off')</th>
								<th></th>
								<th></th>
								<th>@lang('admin.timekeeping.absence')(KP)</th>
								<th></th>
								<th>@lang('admin.timekeeping.pheps') (P)</th>
								<th></th>
							</tr>
							<tr>
								<th>@lang('admin.timekeeping.holiday')</th>
								<th></th>
								<th></th>
								<th>@lang('admin.timekeeping.absenceHaveMoney')</th>
								<th></th>
								<th>@lang('admin.timekeeping.absenceNotMoney')</th>
								<th></th>
							</tr>
						</thead>
					</table>
				</div>

				<!-- Table daily report detail -->
				<div class="table-responsive table-timekeeping">
					<table class="table table-striped table-bordered table-hover data-table" id="table_timekeeping">
						<thead class="thead-default">
							<tr>
								<th class="no-sort thead-th-custom" rowspan="2" style="width:100px !important;">@lang('admin.day')</th>
								<th class="thead-th-custom width5" rowspan="2" style="word-wrap: break-word;">@lang('admin.overtime.week_day')</th>
								<th class="thead-th-custom" colspan="2">@lang('admin.timekeeping.TGvaora')</th>
								<th class="thead-th-custom" rowspan="2">@lang('admin.timekeeping.late')
									<br>(phút)</th>
								<th class="thead-th-custom" rowspan="2">@lang('admin.timekeeping.soon')
									<br>(phút)</th>
								<th class="thead-th-custom" rowspan="2">@lang('admin.timekeeping.T_Gio')</th>
								<th class="thead-th-custom" rowspan="2">@lang('admin.timekeeping.total_work')</th>
								{{-- <th class="thead-th-custom" colspan="2">Làm Thêm</th> --}}
								<th class="thead-th-custom" rowspan="2" style="width:210px">@lang('admin.absences')</th>
								@if ($canEdit || $canDelete)
									<th class="thead-th-custom width5pt" rowspan="2">@lang('admin.action')</th>
								@endif
							</tr>
							<tr>
								<th class="thead-th-custom">Vào</th>
								<th class="thead-th-custom">Ra</th>
								{{-- <th class="thead-th-custom">N (phút)</th>
								<th class="thead-th-custom">Đ (phút)</th> --}}
							</tr>
						</thead>
						<tbody>
						@foreach($timekeepings as $timekeeping)
							<tr
								{{ $timekeeping->weekday == 'T7' ? 'class=weekend-7' : '' }}
								{{ $timekeeping->weekday == 'CN' ? 'class=weekend-cn' : '' }}
							>
								<td id="tk-date">{{ \Carbon\Carbon::parse($timekeeping->Date)->format('d/m/Y') }}</td>
								<td>{{ $timekeeping->weekday }}</td>
								<td>{{ isset($timekeeping->TimeIn) ? \Carbon\Carbon::parse($timekeeping->TimeIn)->format(FOMAT_DISPLAY_TIME) : '' }}</td>
								<td>{{ isset($timekeeping->TimeOut) ? \Carbon\Carbon::parse($timekeeping->TimeOut)->format(FOMAT_DISPLAY_TIME) : '' }}</td>
								<td>{{ $timekeeping->late }}</td>
								<td>{{ $timekeeping->soon }}</td>
								<td>{{ number_format($timekeeping->hours, 2) + 0 }}</td>
								<td>{{ $timekeeping->keeping > 1 ? 1 : number_format($timekeeping->keeping, 2) }}</td>
								{{-- <td>{{ $timekeeping->N }}</td>
								<td>0</td> --}}
								<td>
									@foreach($timekeeping->absence as $absence)
										@if($timekeeping->weekday != 'T7'&&$timekeeping->weekday != 'CN')
											<a class="action-col view" style="text-decoration: none">{{ $absence->Name }} ({{ $absence->STime }} - {{ $absence->ETime }})</a><br/>
										@endif
									@endforeach
								</td>
								@if ($canEdit || $canDelete)
									<td class="text-center">
										@if ($canEdit)
											<span class="action-col update edit update-timekeeping" item-id="{{ $timekeeping->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
										@endif
										@if ($canDelete)
											<span class="action-col update delete delete-timekeeping"  item-id="{{ $timekeeping->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
										@endif
									</td>
								@endif
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>

				<div id="importModal" class="modal draggable fade in" role="dialog" tabindex="-1">
					<div>
						<div class="modal-dialog modal-lg ui-draggable width550">
							<!-- Modal content-->
							<div class="modal-content drag">
								<form class="form-horizontal" action="{{ route('admin.importTimekeeping') }}" method="POST" enctype="multipart/form-data">
									<div class="modal-header ui-draggable-handle" style="cursor: move;">
										<button type="button" class="close" data-dismiss="modal" id="">×</button>
										<h4 class="modal-title">Nhập dữ liệu chấm công</h4>
									</div>
									<div class="modal-body row">
										<div class="save-errors"></div>
										@csrf
										<div class="col-sm-3">
											<label>@lang('admin.document.select_file')&nbsp;<i class="text-red">*</i>:</label>
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
											<label>@lang('admin.timekeeping.DL_month')&nbsp;<i class="text-red">*</i>:</label>
										</div>
										<div class="col-sm-9" style="margin-bottom: 10px;" id = 'choosemonths'>
											<div class="input-group date " id="date-timemeeting">
												<input type="text" class="form-control" id="date-timemeeting" name="Date" value="{{Carbon\Carbon::now()->format(FOMAT_DISPLAY_MONTH)}}">
												<div class="input-group-addon">
													<span class="glyphicon glyphicon-th"></span>
												</div>
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
										{{-- <button type="submit" class="btn btn-primary btn-sm" id="save">Save</button> --}}
										<button type="button" class="btn btn-primary btn-sm" id="save">@lang('admin.btnSave')</button>
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
										<button type="button" class="close" data-dismiss="modal" id="close">×</button>
										<h4 class="modal-title">Xuất dữ liệu chấm công</h4>
									</div>
									<div class="modal-body">
										<div class="save-errors"></div>
										@csrf
										<div class="form-group row">
											<div class="col-sm-4">
												<label>@lang('admin.timekeeping.DL_month')&nbsp;<i class="text-red">*</i>:</label>
											</div>
											<div class="col-sm-8" style="margin-bottom: 5px;">
												<div class="input-group date " id="date-timemeetings">
													<input type="text" class="form-control" id="date-timemeetings" name="Date1" value="{{Carbon\Carbon::now()->format(FOMAT_DISPLAY_MONTH)}}">
													<div class="input-group-addon">
														<span class="glyphicon glyphicon-th"></span>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-sm-4">
												<label>@lang('admin.Active_status')&nbsp;:</label>
											</div>
											<div class="col-sm-8" style="margin-bottom: 5px;">
												<div class="input-group date " id="statusUser">
													<select class="selectpicker show-menu-arrow form-control" id='select-status' data-live-search='true'data-live-search-placeholder='Search' data-size='6' data-width="336px">
														<option value="">[@lang('admin.all_status')]</option>
														<option value="1">@lang('admin.on')</option>
														<option value="0">@lang('admin.off')</option>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-sm-4">
												<label>@lang('admin.chooseUser')&nbsp;<i class="text-red">*</i>:</label>
											</div>
											<div class="col-sm-8" style="margin-bottom: 5px;" id="userStatus">
												<div class="input-group date " id="User">
													<select class="selectpicker show-menu-arrow form-control" id='select-userStatus' multiple data-live-search='true' data-live-search-placeholder='Search' data-actions-box="true" data-size='6' data-width="336px">
														{!! GenHtmlOption($users1, 'id', 'FullName') !!}
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="closecanel">@lang('admin.btnCancel')</button>
										<button type="button" class="btn btn-primary btn-sm" id="btn-export-timekeeping">@lang('admin.export-excel')</button>
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
    .weekend-7 { background: #CCCCFF !important; }
    .weekend-cn { background: #FF99CC !important; }
	#table1 { margin-bottom: 0px; }
	#table_timekeeping { margin-top: 20px; }
</style>
@endsection

@section('js')
	<script type="text/javascript" async>
		var idUser = $('#action-select-user option:selected').val() + '';
		var check = {{count($errors) ? $errors->any() : 0}};
		if(check != 0){
			setTimeout(function(){ showErrors('{{$errors->first()}}'); }, 200);
		}
		var statusUser = $('#select-status').val();
		if(statusUser == ''){
			selectUser();
		}
		function selectUser(){
			$('#select-userStatus').html('');
			html = '';
			var arrUser = '<?php echo $users1; ?>';
			$('#select-status option[value=1]').attr('selected','selected');
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
			var arrUser = '<?php echo $users1; ?>';
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
		var ajaxUrl = '{{ route('admin.UserInfo') }}';
		var ajaxUrl_detail = '{{ route('admin.detailTimekeeping') }}';
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
			$('#close,#closecanel').click(function() {
				$('#select-status option[value=1]').attr('selected','selected');
				$('input#date-timemeetings').val($('input#date-timemeetings').attr("value"));
				selectUser();
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
					ajaxServer('{{ route('admin.exportAbsenceTimekeeping') }}/'+timeExport+'/'+User, 'GET',null, function (data) {
						if (typeof data.errors !== 'undefined') {
							$('.loadajax').hide();
							showErrors(data.errors[0]);
						} else {
							window.location.href = '{{ route('admin.exportAbsenceTimekeeping') }}/'+timeExport+'/'+User;
							$('#exportModal').modal('hide');
							$('#select-status option[value=1]').attr('selected','selected');
							selectUser();
							$('.loadajax').hide();
						}
					})
				}else{
					ajaxServer('{{ route('admin.exportTimekeeping') }}/'+timeExport+'/'+User, 'GET',null, function (data) {
						if (typeof data.errors !== 'undefined') {
							$('.loadajax').hide();
							showErrors(data.errors[0]);
						} else {
							window.location.href = '{{ route('admin.exportTimekeeping') }}/'+timeExport+'/'+User;
							$('#exportModal').modal('hide');
							$('#select-status option[value=1]').attr('selected','selected');
							selectUser();
							$('.loadajax').hide();
						}
					})
				}
				// window.location.href = '{{ route('admin.exportTimekeeping') }}/'+timeExport+'/'+User;
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

			if('<?php echo $checkUser->role_group; ?>' != 3 ){
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
                        '{{ route('admin.getUsersByActive') }}',
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
                        '{{ route('admin.getUsersByActive') }}',
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
			var searchUser = $('#select-user').val();
			ajaxGetServerWithLoader(ajaxUrl_detail+'/'+itemId+'?searchUser='+searchUser, 'GET', null, function (data) {
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

			ajaxGetServerWithLoader('{{route('admin.AbsenceTimekeeping')}}', 'POST', {
				date: date,
				UserID: UserID,
			}, function (data) {
				$('#popupModal').empty().html(data);
				$('.modal-title').html(Title);
				$('#modal-absence-list').modal('show');
			});
		});
	</script>
@endsection
