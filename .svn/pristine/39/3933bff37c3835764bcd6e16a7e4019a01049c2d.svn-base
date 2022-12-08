@extends('admin.layouts.default.app')
<style>
	.modal-open {
		overflow: scroll;
	}
</style>
@section('content')
<section class="content-header">
	<h1 class="page-header">
		{{-- <a href="{{route('admin.ProjectManager')}}"> --}}
			@lang('projectmanager::admin.Project')
		{{-- </a> --}}
		 -
		{{$project->NameVi}} - {{$project->NameShort}}</h1>
</section>
<section class="content">

	<div class="nav-tabs-custom">
		<a href="{{route('admin.ProjectManager')}}" class="btn btn-primary pull-right" {{-- onclick="comeBack()" --}}>
            <i class="fa fa-arrow-left" aria-hidden="true"></i>
        </a>
		<ul class="nav nav-tabs" id="myTab">
			<li class="{{!isset($activeTab) || $activeTab === 'tab_phase' ? 'active' : ''}}"><a href="#tab_phase"
					id="tab_phase_btn" data-toggle="tab" aria-expanded="false">Phases</a></li>
			<li id="tab_job_btn" class="{{isset($activeTab) && $activeTab === 'tab_job' ? 'active' : ''}}"><a
					href="#tab_job" data-toggle="tab" aria-expanded="false">Jobs</a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane {{!isset($activeTab) || $activeTab === 'tab_phase' ? 'active' : ''}}" id="tab_phase">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<form class="form-inline" id="phase-fm">
							<input type="hidden" name="projectId" value="{{$project->id}}">
							<input type="hidden" name="activeTab" value="tab_phase">
							<div class="input-group pull-left margin-r-5">
								<input type="search" class="form-control"
									placeholder="@lang('admin.search-placeholder')" name="phaseSearch"
									value="{{ $request->phaseSearch ?? null }}">
							</div>
							@if ($managePermission)
								<div class="form-group pull-left margin-r-5">
									<div class="btn-group bootstrap-select show-tick show-menu-arrow"
										id="action-select-user">
										<select class="selectpicker show-tick show-menu-arrow" id="select-user"
											data-done-button="true" name="userIds[]" multiple
											title="@lang('admin.chooseUser')" data-live-search="true" data-size="5"
											data-live-search-placeholder="Tìm kiếm theo nhân viên" data-actions-box="true" tabindex="-98">
											{!! GenHtmlOption($project->users, 'id', 'FullName', isset($request['userIds'])
											?
											$request['userIds'] : null)
											!!}
										</select>
									</div>
								</div>
							@endif	
							<div class="form-group pull-left margin-r-5">
								<div class="input-group search date" id="sDate">
									<input type="text" class="form-control datepicker" id="phaseStartDate"
										placeholder=" Ngày bắt đầu" autocomplete="off" name="phaseStartDate"
										value="{{ isset($request['phaseStartDate']) ? $request['phaseStartDate'] : '' }}">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-th"></span>
									</div>
								</div>
							</div>
							<div class="form-group pull-left margin-r-5">
								<div class="input-group search date" id="sDate">
									<input type="text" class="form-control datepicker" id="phaseEndDate"
										placeholder=" Ngày kết thúc" autocomplete="off" name="phaseEndDate"
										value="{{ isset($request['phaseEndDate']) ? $request['phaseEndDate'] : '' }}">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-th"></span>
									</div>
								</div>
							</div>
							<div class="input-group pull-left">
								<button type="button" style="height: 34px" class="btn btn-primary" id="btn-phaseSearch">@lang('admin.btnSearch')
								</button>
							</div>
							@if ($managePermission)
								<div class="form-group pull-right">
									<button type="button" class="btn btn-primary"
										onclick="addPhase()">@lang('projectmanager::admin.phase.Add')
									</button>
								</div>
							@endif
							<div class="clearfix"></div>
						</form>
					</div>
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="box tbl-top">
							<div class="box-body table-responsive no-padding table-scroll">
								<table class="table table-bordered table-striped" name="table" style="display: block">
									<thead class="thead-default">
										<tr>
											<th class="width3pt">
												@lang('admin.stt')
											</th>
											<th class="width8">
												@lang('projectmanager::admin.phase.Type')
											</th>
											<th class="width15">
												@lang('projectmanager::admin.phase.Name')
											</th>
											<th class="width15">
												@lang('projectmanager::admin.phase.Description')
											</th>
											<th class="">
												@lang('projectmanager::admin.Members')
											</th>
											<th>@lang('projectmanager::admin.task.Total')</th>
											<th class="">@lang('projectmanager::admin.task.Todo')</th>
											<th class="">@lang('projectmanager::admin.task.Doing')</th>
											<th class="">@lang('projectmanager::admin.task.Review')</th>
											<th class="">@lang('projectmanager::admin.task.Done')</th>
											
											<th class="width8">@lang('projectmanager::admin.Progress')(%)</th>
											<th class="width8">@lang('projectmanager::admin.GeneralProgress')(%)</th>

											<th class="width8">@lang('admin.project.start_date')</th>
											<th class="width8">@lang('admin.project.end_date')</th>
											@if ($managePermission)
												<th class="width8">@lang('admin.action')</th>
											@endif
										</tr>
									</thead>
									<tbody id="phaseBody">
									</tbody>
								</table>
							</div>
						</div>
						<div id="phase-page-selection" class="hidden"></div>
					</div>
				</div>
			</div>
			<!-- /.tab-pane -->
			<div class="tab-pane {{isset($activeTab) && $activeTab === 'tab_job' ? 'active' : ''}}" id="tab_job">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<form class="form-inline" id="job-fm">
							<input type="hidden" name="projectId" value="{{$project->id}}">
							<input type="hidden" name="activeTab" value="tab_job">
							<div class="input-group pull-left margin-r-5">
								<input type="search" class="form-control"
									placeholder="@lang('admin.search-placeholder')" name="jobSearch"
									value="{{ $request->jobSearch ?? null }}">
							</div>
							@if ($managePermission)
								<div class="form-group pull-left margin-r-5">
									<div class="btn-group bootstrap-select show-tick show-menu-arrow"
										id="action-select-user">
										<select class="selectpicker show-tick show-menu-arrow" id="select-user"
											data-done-button="true" name="userIds[]" multiple
											title="@lang('admin.chooseUser')" data-live-search="true" data-size="5"
											data-live-search-placeholder="Tìm kiếm theo nhân viên" data-actions-box="true" tabindex="-98"
										>
											{!! GenHtmlOption($project->users, 'id', 'FullName', isset($request['userIds'])
											?
											$request['userIds'] : null)
											!!}
										</select>
									</div>
								</div>
							@endif
							<div class="form-group pull-left margin-r-5">
								<div class="input-group search date" id="sDate">
									<input type="text" class="form-control datepicker" id="jobStartDate"
										placeholder=" Ngày bắt đầu" autocomplete="off" name="jobStartDate"
										value="{{ isset($request['jobStartDate']) ? $request['jobStartDate'] : '' }}">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-th"></span>
									</div>
								</div>
							</div>
							<div class="form-group pull-left margin-r-5">
								<div class="input-group search date" id="sDate">
									<input type="text" class="form-control datepicker" id="jobEndDate"
										placeholder=" Ngày kết thúc" autocomplete="off" name="jobEndDate"
										value="{{ isset($request['jobEndDate']) ? $request['jobEndDate'] : '' }}">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-th"></span>
									</div>
								</div>
							</div>
							<div class="input-group pull-left">
								<button type="button" class="btn btn-primary"  style="height: 34px"
									id="btn-jobSearch">@lang('admin.btnSearch')</button>
							</div>
							@if ($managePermission)
								<div class="form-group pull-right">
									<button type="button" class="btn btn-primary "
										onclick="addJob()">@lang('projectmanager::admin.job.Add')</button>
								</div>
								{{-- <a href="javascript:void(0)" data-item="1" class="open-task add-task-btn" item-status="1">
									<span><i class="fa fa-plus" aria-hidden="true"></i></span>
									<span style="margin-left: 1em; font-weight: 100 !important;">Thêm task mới</span>
								</a> --}}
							@endif
							<div class="clearfix"></div>
						</form>
					</div>
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="box tbl-top">
							<div class="box-body table-responsive no-padding table-scroll">
								<table class="table table-bordered table-striped" name="table" style="display: block">
									<thead class="thead-default">
										<tr>
											<th class="width3pt">
												@lang('admin.stt')
											</th>
											<th class="width15">
												@lang('projectmanager::admin.job.Name')
											</th>
											<th class="width15">
												@lang('projectmanager::admin.job.Description')
											</th>
											<th class="width15">
												@lang('projectmanager::admin.job.Phase')
											</th>
											<th class="">
												@lang('projectmanager::admin.Members')
											</th>
											<th>@lang('projectmanager::admin.task.Total')</th>
											<th class="">@lang('projectmanager::admin.task.Todo')</th>
											<th class="">@lang('projectmanager::admin.task.Doing')</th>
											<th class="">@lang('projectmanager::admin.task.Review')</th>
											<th class="">@lang('projectmanager::admin.task.Done')</th>
											<th class="width8">@lang('projectmanager::admin.Progress')(%)</th>
											<th class="width8">@lang('projectmanager::admin.GeneralProgress')(%)</th>
											<th class="width8">@lang('admin.project.start_date')</th>
											<th class="width8">@lang('admin.project.end_date')</th>
											@if ($managePermission)
												<th class="width8">@lang('admin.action')</th>
											@endif
										</tr>
									</thead>
									<tbody id="jobBody">
									</tbody>
								</table>
							</div>
						</div>
						<div id="job-page-selection" class="hidden"></div>
					</div>

				</div>
			</div>
			<!-- /.tab-pane -->
		</div>
		<!-- /.tab-content -->
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
	var ajaxUrl = "{{ route('admin.showProjectDetail') }}";
	var hash = window.location.hash;
	let projectId = "{{$project->id}}";
	let phasePage = 1;
	let jobPage = 1;
	setSelectPicker();

	SetDatePicker($('.date'), {
        format: "dd/mm/yyyy",
        todayHighlight: true,
        autoclose: true,
    });
	
	$(document).ready(() => {
		showPhases(1,null,null)
		showJobs(1,null,null)
		$(document).on('click','.show-members-btn',function(e) {
			e.preventDefault();
			let url = "{{ route('admin.showMembers') }}"
			let phaseId = $(this).attr('phase-id');
			let jobId = $(this).attr('job-id');
			openModalMember(url,null,phaseId,jobId)
  		});
		
		$('#tab_phase_btn,#tab_job_btn').on('click', function(e){
			e.preventDefault();
			$('html,body').scrollTop(0);
		});

		$("#paginator").empty().html("");

	});
	
	$('#phase-page-selection').on('page', async  (event, num) => {
		event.preventDefault();
		await showPhases(num, 'id', 'asc');
	});

	$('#job-page-selection').on('page', async  (event, num) => {
		event.preventDefault();
		await showJobs(num, 'id', 'asc');
	});

	function showPhases(page,order_by,sort_by){
		const data = $('#phase-fm').serializeArray();
		const url = "{{ route('admin.showPhases') }}";
		data.push({ name: "page", value: page });
		data.push({ name: "order_by", value: order_by });
		data.push({ name: "sort_by", value: sort_by });
		ajaxShowPhasesJobs(data,url,'phase');
	}

	function showJobs(page,order_by,sort_by){
		const data = $('#job-fm').serializeArray();
		const url = "{{ route('admin.showJobs') }}";
		data.push({ name: "page", value: page });
		data.push({ name: "order_by", value: order_by });
		data.push({ name: "sort_by", value: sort_by });
		ajaxShowPhasesJobs(data,url,'job');
		
	}
	function ajaxShowPhasesJobs(data,url,target){
		$('.loadajax').show();
		$.ajax({
			url: url,
			data: data,
			type: 'get',
			success: function (res) {
				if(target == 'phase'){
					phasePage = res.page;
					if(res.onPageItems == 0 ){
						page = phasePage - 1;
						if(page > 0){
							showPhases(page, 'id', 'asc')
						}
					}
					if(res.lastPage > 1){
						$('#phase-page-selection').removeClass('hidden');
					}else{
						$('#phase-page-selection').addClass('hidden');
					}
				}
				else {
					jobPage = res.page;
					if(res.onPageItems == 0){
						page = jobPage - 1;
						if(page > 0){
							showJobs(page, 'id', 'asc')
						}
					}
					if(res.lastPage > 1){
						$('#job-page-selection').removeClass('hidden');
					}else{
						$('#job-page-selection').addClass('hidden');
					}

				}
				$(`#${target}Body`).empty().html(res.view);
				$(`#${target}-page-selection`).bootpag({
					total: res.lastPage,
					page: res.page,
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
				$(window).resize();
				$('.loadajax').hide();
			},
			fail: function (error) {
			}
		});
	}

	$('#btn-phaseSearch').on('click', function (e){
		e.preventDefault();
		showPhases(null, 'id', 'asc');
	});

	$('#btn-jobSearch').on('click', function (e){
		e.preventDefault();
		showJobs(null, 'id', 'asc');
	});

	$('#myTab a').click(function(e) {
		e.preventDefault();
		$(this).tab('show');
	});

		// store the currently selected tab in the hash value
	$("ul.nav-tabs > li > a").on("shown.bs.tab", function(e) {
		var id = $(e.target).attr("href").substr(1);
		window.location.hash = id;
	});
		// on load of the page: switch to the currently selected tab
	$('#myTab a[href="' + hash + '"]').tab('show');

	function addPhase(){
		var title = "Thêm Phase mới";
		var action = 'createPhase';
		add(title,ajaxUrl,action);
	}
	function addJob(){
		var title = "Thêm Job mới";
		var action = 'createJob';
		add(title,ajaxUrl,action);
	}

	function add(title,ajaxUrl,action){
		var ajaxUrl = "{{ route('admin.showProjectDetail') }}";
		ajaxGetServerWithLoader(
			genUrlGet([ajaxUrl]),
			'GET',
			{
				projectId: projectId,
				phaseId: null,
				jobId: null,
				action: action
			},
			function(data) {
				$('#popupModal').empty().html(data);
				$('.modal-title').html(title);
				$('.detail-modal').modal('show');
			}
		);
	}

	function destroy(phaseId,jobId,projectId){
		var ajaxUrl = "{{ route('admin.showProjectDetail') }}";
		showConfirm(confirmMsg, function () {
			ajaxGetServerWithLoader(
				genUrlGet([ajaxUrl]),
				'GET',
				{
					projectId: projectId,
					phaseId: phaseId,
					jobId: jobId,
					del: 'del'
				},
				function(data) {
					if(data.success == true){
						if(phaseId != null){
							showPhases(phasePage,'id', 'asc');
						}	else{
							showJobs(jobPage,'id', 'asc');
						}
					showSuccessAutoClose(data.mes)
					} else{
						showErrors(data.mes)
					}
				}
			);
		});
	}

	$(".add-task-btn").click(function (e) {
            e.preventDefault();
            var ajaxUrl = "{{ route('admin.showTaskForm') }}";
            var title = "Thêm Task mới";
            var status = $(this).attr('item-status');
            ajaxGetServerWithLoader(
                genUrlGet([ajaxUrl]),
                'GET',
                {
                    projectId: projectId,
                },
                function (data) {
                    $('#popupModal').empty().html(data);
                    $('.modal-title').html(title);
                    $('#status').val(status);
                    $('.detail-modal').modal('show');
            }
        );
    });

</script>
@endsection