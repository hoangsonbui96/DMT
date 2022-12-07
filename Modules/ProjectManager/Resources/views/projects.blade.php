@extends('admin.layouts.default.app')
@section('content')

<section class="content-header">
	<h1 class="page-header">@lang('admin.project.projects_management')</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<form class="form-inline" id="search-fm">
				<div class="input-group pull-left margin-r-5">
					<input type="search" class="form-control" placeholder="@lang('admin.search-placeholder')"
						name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
				</div>
				@if ($permissions['create'])
					<div class="form-group pull-left margin-r-5" id="select-user-block">
						<div class="btn-group bootstrap-select show-tick show-menu-arrow" id="action-select-user" style="width:auto">
							<select class="selectpicker show-tick show-menu-arrow" id="select-user" name="userIds[]"
								data-done-button="true" multiple title="@lang('admin.chooseUser')"
								data-live-search="true" data-size="5" data-live-search-placeholder="Tìm kiếm theo nhân viên"
								data-actions-box="true" tabindex="-98">
								@if(isset($users))
									@foreach ($users['active'] as $user)
										<option value="{{$user->id}}" {{$request->userIds && in_array($user->id,$request->userIds) ? 'selected' : ''}}>{{$user->FullName}}</option>
									@endforeach
									@foreach ($users['inactive'] as $user)
										<option value="{{$user->id}}" {{$request->userIds && in_array($user->id,$request->userIds) ? 'selected' : ''}} title="{{$user->FullName}}">{{$user->FullName}} - Không hoạt động </option>
									@endforeach
									{{-- @foreach ($users['deleted'] as $user)
										<option value="{{$user->id}}" {{$request->userIds && in_array($user->id,$request->userIds) ? 'selected' : ''}} title="{{$user->FullName}}">{{$user->FullName}} - Đã nghỉ việc </option>
									@endforeach --}}
                                    @endif
							</select>
						</div>
					</div>
				@endif
				
				<div class="form-group pull-left margin-r-5">
					<div class="input-group search date" id="sDate">
						<input type="text" class="form-control datepicker" id="st-date"
							placeholder=" Ngày bắt đầu" autocomplete="off" name="Date[]"
							value="{{ isset($request['Date']) ? $request['Date'][0] : '' }}">
						<div class="input-group-addon">
							<span class="glyphicon glyphicon-th"></span>
						</div>
					</div>
				</div>
				<div class="form-group pull-left margin-r-5" name="endDate">
					<div class="input-group search date" id="eDate">
						<input type="text" class="form-control datepicker" id="ed-date"
							placeholder=" Ngày kết thúc" autocomplete="off" name="Date[]"
							value="{{ isset($request['Date']) ? $request['Date'][1] : '' }}">
						<div class="input-group-addon">
							<span class="glyphicon glyphicon-th"></span>
						</div>
					</div>
				</div>
				<div class="form-group pull-left margin-r-5">
					<input type="checkbox" id="switch-toggle-status" value="1" name="Active" checked data-toggle="toggle" data-on="Đang hoạt động"
							data-off="Không hoạt động" data-onstyle="primary" data-offstyle="danger" data-width="135px">
				</div>
				<div class="input-group pull-left">
					<button type="button" class="btn btn-primary"
						id="btn-search">@lang('admin.btnSearch')</button>
				</div>
				@if ($permissions['create'])
					<div class="form-group pull-right">
						<button type="button" class="btn btn-primary btn-detail"
							id="add-new-room-btn">@lang('admin.project.add_new_project')</button>
					</div>
				@endif
				@if ($permissions['create'])
					<div class="form-group pull-left" style="padding-left: 5px">
					<button type="button" class="btn btn-success" id="btn-export">
						<div id="downloading" class="hide">
							<i class="fa fa-spinner fa-spin"></i>
							<span>Đang tải</span>
						</div>
						<span id="content">@lang('admin.export-excel')</span>
					</button>
					</div>
				@endif
				<div class="clearfix"></div>
			</form>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12" id="projectsList">
			<div class="box tbl-top">
				<div class="box-body table-responsive no-padding table-scroll">
					<table class="table table-bordered table-striped" name="table">
						<thead class="thead-default">
							<tr id="tHead">
								<th class="width3pt"><a class="sort-link" order-by="id"
										sort-by='desc'>@lang('admin.stt')</a>
								</th>

								<th class="width20"><a class="sort-link"
										order-by="NameVi">@lang('projectmanager::admin.project.Name')</a></th>

								<th class="width8"><a class="sort-link"
										order-by="NameShort">@lang('admin.project.name_short')</a></th>

								<th class="width8">@lang('projectmanager::admin.phase.Total')</th>
								<th class="width8">@lang('projectmanager::admin.job.Total')</th>
								<th class="width8">@lang('projectmanager::admin.task.Total')</th>
								<th class="width8">@lang('projectmanager::admin.project.estimatedDuration')</th>
								<th class="width8">@lang('projectmanager::admin.project.workedHours')</th>
								<th class="width8">@lang('projectmanager::admin.project.OTDuration')</th>
								<th class="width8">@lang('projectmanager::admin.Progress')(%)</th>
								<th class="width8">@lang('projectmanager::admin.Members')</th>

								<th class="width15"><a class="sort-link"
										order-by="Customer">@lang('admin.project.customer')</a></th>

								<th class="width8"><a class="sort-link"
										order-by="StartDate">@lang('admin.project.start_date')</a></th>

								<th class="width8"><a class="sort-link"
										order-by="EndDate">@lang('admin.project.end_date')</a></th>

								<th class="width3"><a class="sort-link"
										order-by="Active">@lang('admin.active')</a></th>
								@if ($permissions['create'])
								<th class="width8">@lang('admin.action')</th>
								@endif
							</tr>
						</thead>
						<tbody id="projectBody">
						</tbody>
					</table>
				</div>
			</div>
			<div id="project-page-selection" class="hidden">
			</div>
		</div>
	</div>
</section>
@endsection

@section('js')
<script>
	$(".selectpicker").selectpicker();
</script>
<script type="text/javascript" src="{{ Module::asset('ProjectManager:js/tasks.js')}}"></script>
<script src="{{ asset('js/bootpag.js') }}"></script>
<script type="text/javascript" async>
	let page = 1;
	let order_by = 'id';
	let sort_by = 'desc';
	setSelectPicker();

	SetDatePicker($('.date'), {
        format: "dd/mm/yyyy",
        todayHighlight: true,
        autoclose: true,
    });

	$(document).ready(() => {
		showProjects(1, null, null,{'action':'firstLoad'});
		$('.loadajax').show();
		$('#tHead').children('th').children('a').children('i').first().attr('class','fa fa-caret-up');
		$("#paginator").empty().html("");

		$(document).on('click','.show-members-btn',function(e) {
			e.preventDefault();
			let url = "{{ route('admin.showMembers') }}"
			let projectId = $(this).attr('project-id');
			openModalMember(url,projectId,null,null)
  		});

		$(document).on('click','.delete-project',function (e) {
			e.preventDefault();
			var obj = $(this);
			var ajaxUrl = "{{ route('admin.showProjectDetail') }}";
			destroy(e,ajaxUrl,obj)
		});
	});

	$('#btn-search').on('click', function (e){
		e.preventDefault();
		$('.loadajax').show();
		showProjects(page, order_by, sort_by,{'action':'search'});
	});
	$('#switch-toggle-status').on('change', function (e){
		e.preventDefault();
		$('.loadajax').show();
		showProjects(page, order_by, sort_by,{'action':'search'});
	});
	$('.bs-donebutton').children('div').children('button').on('click', function (e){
		e.preventDefault();
		$('.loadajax').show();
		showProjects(page, order_by, sort_by,{'action':'search'});
	});
	
	$('#btn-export').click(function (e) {
		e.preventDefault();
        $('#content').addClass('hide');
		$('#downloading').removeClass('hide');
		let data = $('#search-fm').serializeArray();
		let request = '';
		data.push({ name: "export", value: true });
		data.forEach(function(element,index){
			 request += `${element.name}=${element.value}&`
		})
		$.ajax({
			url: "{{ route('admin.exportProjects') }}",
			type: 'GET',
			data: data,
			success: function (res) {
				if (typeof res.errors !== 'undefined'){
					showErrors(res.errors);
				}else{
					window.location.href = '{{ route('admin.exportProjects') }}?'+request;
				}
				$('#downloading').addClass('hide');
        		$('#content').removeClass('hide');
			}
		});
	});

	$('.sort-link').on('click', function (e){
		$('.loadajax').show();
		e.preventDefault();
		$('#tHead').children('th').children('a').not(this).children('i').attr('class','fa fa-caret-down');
		$('#tHead').children('th').children('a').not(this).attr('sort-by','asc');
		$(this).children('i').toggleClass("fa-caret-down fa-caret-up");
		$(this).attr('sort-by',$(this).attr('sort-by')==='desc'?'asc':'desc' );
		order_by = $(this).attr('order-by');
		sort_by = $(this).attr('sort-by'); 
		showProjects(page, order_by, sort_by);
	});
	
	var ajaxUrl = "{{ route('admin.showProjectDetail') }}";
	var newTitle = 'Thêm dự án mới';
	var updateTitle = 'Cập nhật dự án';

	$('#project-page-selection').on('page', async  (event, num) => {
		$('.loadajax').show();
		event.preventDefault();
		page = num;
		await showProjects(num, order_by, sort_by);
	});

	function destroy(e,ajaxUrl,obj){
		var projectId = obj.attr('project-id');
		showConfirm(confirmMsg, function () {
			$('.loadajax').show();
			$.ajax({
				url: ajaxUrl,
				type: 'GET',
				data : {
					projectId: projectId,
					del: 'del'
				},
				success: function(data) {
					if(data.success != ''){
						$('.loadajax').hide();
						$(`#project${projectId}`).fadeOut();
						showSuccessAutoClose('Đã xóa Dự án!')
						showProjects(page, order_by, sort_by,{'action':'del','updateId':projectId});
					}
				}
			});
		});
	}

	function showProjects(page,order_by,sort_by,action = []){
		let data = $('#search-fm').serializeArray();
		const url = "{{ route('admin.ProjectManager') }}";
		data.push({ name: "page", value: page });
		data.push({ name: "orderBy", value: order_by });
		data.push({ name: "sortBy", value: sort_by });
		data.push({ name: "action", value: action.action });
		ajaxShowProjects(data,url,'project',action);
	}

	function ajaxShowProjects(data,url,target,action){
		$.ajax({
			url: url,
			data: data,
			type: 'get',
			success: function (res) {
				if(res.errors == ''){
					const{
						page,sortBy,lastPage
					} = res
					$(`#projectBody`).empty().html(res.view);
					$(`#project-page-selection`).bootpag({
						total: lastPage,
						page: page,
						maxVisible: 4,
						lastClass: 'last',
						firstClass: 'first',
						nextClass: 'next',
						prevClass: 'prev',
						next: '›',
						prev: '‹',
						first: '«',
						last: '»',
						firstLastUse: true,
						leaps: true,
						wrapClass: 'pagination',
						activeClass: 'active',
						disabledClass: 'disabled'
					});
					if(action.action == 'new'){
						$('.loadajax').hide();
						let e = $(`#project${res.last}`);
						highlinghtElement(e);
					}else if(action.action == 'update'){
						$('.loadajax').hide();
						let e = $(`#project${action.updateId}`).children();
						highlinghtElement(e);
					}
					else if(action.action == 'search'){
						$('.loadajax').hide();
						$('#projectBody').fadeIn();
					}

					else if(action.action == 'del'){
						$('.loadajax').hide();
						let e = $(`#project${action.updateId}`);
						e.fadeOut();
					}
					else{
						$('.loadajax').hide();
						$(`#projectBody`).css('display','none');
						$('#projectBody').fadeIn();
					}
					// if(res.lastPage > 1){
					// 	$(`#project-page-selection`).show();
						
					// }else{
					// 	$(`#project-page-selection`).hide();
					// }
				}else{
					if(res.errors.code == '000'){
						$(`#project-page-selection`).bootpag({
							total: 1,
							page
						});
						$(`#projectBody`).empty().html('')
						$('.loadajax').hide();
						showErrors(res.errors.mes)
					}
				}
				if(res.lastPage > 1){
					$('#project-page-selection').removeClass('hidden');
				};
			},
			fail: function (error) {
			}
		});
	}

	function highlinghtElement(e){
		e.fadeOut();
		e.fadeIn();
		// originalColor = e.css("background");
		// e.css("background", "lightyellow");
		// e.css("transition", "3");
		// e.css("background", originalColor);
	}

</script>
@endsection