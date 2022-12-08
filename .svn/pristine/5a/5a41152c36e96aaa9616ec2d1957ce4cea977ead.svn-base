@extends('admin.layouts.default.app')

@php
	$canEdit = false;
	$canDelete = false;
	$abc = $stt;
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

<style>
	.view-img {
		width: 50px;
		height: 50px;
		/*border-radius: 50%;*/
	}
	.view-img:hover { cursor: pointer; }
	.notifications-menu .dropdown-toggle { height: 50px; padding-top: 16px; }
	#btn-search { padding-top: 9px; padding-bottom: 9px;  }
	.form-inline { margin-bottom: 0px; }

	#form-search .form-group:not(:last-child) { margin-right: 3px; }
	#form-search .form-group.pull-right { margin-right: 0px; }

	#tutorial {
		z-index: 99999;
	}
	#tutorial:hover {
		cursor: pointer;
	}
</style>
@section('content')
<section class="content-header">
	<h1 class="page-header">@lang('admin.user.users_management')</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<form class="form-inline" id="form-search" action="" method="">
                <div class="form-group pull-left">
					<select class="selectpicker show-tick show-menu-arrow" id="select-type1" name="col" data-size="6" tabindex="-98">
						<option value="" {{ !isset($request['col']) && $request['col'] == '' ? 'selected' : '' }}>@lang('admin.user.all_col')</option>
						<option value="Birthday" {{ isset($request['col']) && $request['col'] == 'Birthday' ? 'selected' : '' }}>@lang('admin.user.birthday')</option>
						<option value="SDate" {{ isset($request['col']) && $request['col'] == 'SDate' ? 'selected' : '' }}>@lang('admin.user.join_date')</option>
						<option value="OfficialDate" {{ isset($request['col']) && $request['col'] == 'OfficialDate' ? 'selected' : '' }}>@lang('admin.user.official_date')</option>
					</select>
				</div>
				<div class="form-group has-feedback pull-left">
                    <div class="input-group search">
						<input type="search" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
						<i class="form-control-feedback fa fa-info-circle" id="tutorial" aria-hidden="true"></i>
                    </div>
				</div>
				<div class="form-group pull-left">
					<select class="selectpicker show-tick show-menu-arrow" id="select-type" name="Active" data-size="6" tabindex="-98">
						<option value="2" {{ isset($request['Active']) && $request['Active'] == 2 ? 'selected' : '' }}>Tất cả</option>
						<option value="1" {{ isset($request['Active']) && $request['Active'] == 1 ? 'selected' : '' }}>Hoạt động</option>
						<option value="0" {{ isset($request['Active']) && $request['Active'] == 0 ? 'selected' : '' }}>Không hoạt động</option>
					</select>
				</div>
				<div class="form-group pull-left">
					<button type="submit" class="btn btn-primary btn-search" id="btn-search">@lang('admin.btnSearch')</button>
				</div>
				<div class="form-group pull-left">
					<input type="checkbox" @if($view == 'default') checked @endif data-toggle="toggle" data-on="View Default"
							data-off="View Detail" data-onstyle="primary" data-offstyle="success" id="viewMode">
				</div>
				<div class="form-group pull-right">
					@can('action', $add)
						<button type="button" class="btn btn-primary" id="add-new-user-btn">@lang('admin.user.add_new_user')</button>
					@endcan
					@can('action', $export)
						<a class="btn btn-success" id="btn-export-user" data="{{ isset($request['search']) ? $request['search'] : null  }}">@lang('admin.export-excel')</a>
					@endcan
				</div>
				<div class="clearfix"></div>
			</form>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			@component('admin.component.table')
				@slot('columnsTable')
				<tr>
					<th class="width3pt"><a class="sort-link" data-link="{{ route("admin.Users") }}{{$view == 'detail' ? '/detail' : '/default'}}/id/" data-sort="{{ $sort_link }}">@lang('admin.stt')</a></th>
{{--					<th class="width3pt"><a class="sort-link" data-link="{{ route("admin.Users") }}{{$view == 'detail' ? '/detail' : '/default'}}/IDFM/" data-sort="{{ $sort_link }}">@lang('admin.user.idfm')</a></th>--}}
					<th class="width5"><a class="sort-link" data-link="{{ route("admin.Users") }}{{$view == 'detail' ? '/detail' : '/default'}}/avatar/" data-sort="{{ $sort_link }}">@lang('admin.user.avatar')</a></th>
					@if($view == 'detail')
					<th class="width12"><a class="sort-link" data-link="{{ route("admin.Users") }}/detail/username/" data-sort="{{ $sort_link }}">@lang('admin.user.username')</a></th>
					@endif
					<th class="width12"><a class="sort-link" data-link="{{ route("admin.Users") }}{{$view == 'detail' ? '/detail' : '/default'}}/FullName/" data-sort="{{ $sort_link }}">@lang('admin.user.full_name')</a></th>
					<th class="width5"><a class="sort-link" data-link="{{ route("admin.Users") }}{{$view == 'detail' ? '/detail' : '/default'}}/Tel/" data-sort="{{ $sort_link }}">@lang('admin.user.phone_number_short')</a></th>
					@if($view == 'detail')
					<th><a class="sort-link" data-link="{{ route("admin.Users") }}/detail/email/" data-sort="{{ $sort_link }}">@lang('admin.user.email')</a></th>
					@endif
					<th class="width5"><a class="sort-link" data-link="{{ route("admin.Users") }}{{$view == 'detail' ? '/detail' : '/default'}}/Birthday/" data-sort="{{ $sort_link }}">@lang('admin.user.birthday')</a></th>
					<th class="width3"><a class="sort-link" data-link="{{ route("admin.Users") }}{{$view == 'detail' ? '/detail' : '/default'}}/Birthday/" data-sort="{{ $sort_link }}">@lang('admin.user.age')</a></th>
					<th class="width8"><a class="sort-link" data-link="{{ route("admin.Users") }}{{$view == 'detail' ? '/detail' : '/default'}}/RoomId/" data-sort="{{ $sort_link }}">@lang('admin.user.room')</a></th>
					@if($view == 'default')
					<th class="width5"><a class="sort-link" data-link="{{ route("admin.Users") }}{{$view == 'detail' ? '/detail' : '/default'}}/SDate/" data-sort="{{ $sort_link }}">@lang('admin.user.join_date')</a></th>
					<th class="width5"><a class="sort-link" data-link="{{ route("admin.Users") }}{{$view == 'detail' ? '/detail' : '/default'}}/OfficialDate/" data-sort="{{ $sort_link }}">@lang('admin.user.official_date_short')</a></th>
					<th class="width5"><a class="sort-link" data-link="{{ route("admin.Users") }}{{$view == 'detail' ? '/detail' : '/default'}}/STimeOfDay/" data-sort="{{ $sort_link }}">@lang('admin.user.work_time')</a></th>
					<th class="width5"><a class="sort-link" data-link="{{ route("admin.Users") }}{{$view == 'detail' ? '/detail' : '/default'}}/ETimeOfDay/" data-sort="{{ $sort_link }}">@lang('admin.user.end_time_of_day')</a></th>
					@endif
					@if($view == 'detail')
					<th class="width5"><a class="sort-link" data-link="{{ route("admin.Users") }}/detail/Active/" data-sort="{{ $sort_link }}">@lang('admin.user.status')</a></th>
					@endif
					@if ($canEdit || $canDelete)
					<th class="width5">@lang('admin.action')</th>
					@endif
				</tr>
				@endslot
				@slot('dataTable')
				@foreach($users as $user)
					<tr class="even gradeC">
						<td class="text-center">{{ $sort == 'asc' ? ++$stt : $stt-- }}</td>
{{--						<td class="text-center">{{ $user->IDFM }}</td>--}}
						<td class="text-center">
						@if($user->avatar != '')
							<img class="view-img" src="{{ url($user->avatar) }}" onerror="this.onerror=null;this.src='{{ asset('imgs/user-blank.jpg') }}'" />
						@else
							<img class="view-img" src="{{ asset('imgs/user-blank.jpg') }}" />
						@endif
						</td>
						@if($view == 'detail')
						<td>{{ $user->username }}</td>
						@endif
						<td class="">{{ $user->FullName }}</td>
						<td>{{ $user->Tel }}</td>
						@if($view == 'detail')
						<td class="left-important "><a href="{{ 'mailto:'.$user->email }}">{{ $user->email }}</a></td>
						@endif
						<td class="text-center">{{ isset($user->Birthday) ? FomatDateDisplay($user->Birthday, FOMAT_DISPLAY_DAY) : '' }}</td>
						<td class="text-center">{{ isset($user->Birthday) ? \Carbon\Carbon::parse($user->Birthday)->age : ''}}</td>
						<td>{{ $user->Name }}</td>
						@if($view == 'default')
						<td class="text-center">{{ isset($user->SDate) ? FomatDateDisplay($user->SDate, FOMAT_DISPLAY_DAY) : '' }}</td>
						<td class="text-center">{{ isset($user->OfficialDate) ? FomatDateDisplay($user->OfficialDate, FOMAT_DISPLAY_DAY) : '' }}</td>
						<td class="text-center">{{ isset($user->STimeOfDay) ? FomatDateDisplay($user->STimeOfDay, FOMAT_DISPLAY_TIME) : '' }}</td>
						<td class="text-center">{{ isset($user->ETimeOfDay) ? FomatDateDisplay($user->ETimeOfDay, FOMAT_DISPLAY_TIME) : '' }}</td>
						@endif
						@if($view == 'detail')
						<td class="text-center">
							<input type="checkbox" class="checkActive" user-id="{{$user->id}}"
									{{ (isset($user->Active) && $user->Active == 1) ? 'checked' : ''}} {{$canEdit == false ? 'disabled' : ''}} />
						</td>
						@endif
						@if ($canEdit || $canDelete)
							<td class="text-center">
								@if ($canEdit)
									<span class="action-col update edit update-user" user-id="{{ $user->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
								@endif
								@if ($canDelete)
									<span class="action-col update delete delete-user"  user-id="{{ $user->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
								@endif
							</td>
						@endif
					</tr>
				@endforeach
				@endslot
				@slot('pageTable')
					<input type="hidden" value="{{ $view }}" id="view">
					<input type="hidden" value="{{isset($query_array['page']) ? $query_array['page'] : ''}}" id="numberPage">
					{{ $users->appends($query_array)->links() }}
				@endslot
			@endcomponent
		</div>
	</div>
</section>
@endsection
@section('js')
	<script type="text/javascript" async>
		var ajaxUrl = '{{ route('admin.UserInfo') }}';
		var req = '';
		var view = '';

		$(function () {
			$(".selectpicker").selectpicker();
			@if($errors->any())
			showErrors('{{$errors->first()}}');
			@endif

			var search = $('input[name=search]').val();
			var Active = $('#select-type option:selected').val();
			var numberPage = $('#numberPage').val();
			var wlSearch = window.location.search;
			var reqActive = wlSearch.search('Active');
			var col = $('#select-type1 option:selected').val();

			if(wlSearch == '' && Active == 2) {
				$('#select-type').val(1).selectpicker('refresh');
			}

			if(reqActive < 0) $('#select-type').val(1).selectpicker('refresh');

			if(search != '' || Active != '' || numberPage != '')
				req = '?col='+col+'&search='+search+'&Active='+Active+'&page='+numberPage;

			if(reqActive < 0) req = '?col='+col+'&search='+search+'&Active=1'+'&page='+numberPage;

			if($('#view').val() != undefined)
				view = '/'+$('#view').val();

			//click button export
			$('#btn-export-user').click(function (e) {
				e.preventDefault();
				window.location.href = '{{ route('export.users') }}'+view+req;
			});

			//change active user
			$('.checkActive').on('change',function () {
				var UserId = $(this).attr('user-id');
				var active = $(this).prop("checked") == true ? 1 : 0;

				ajaxGetServerWithLoader("{{ route('admin.CheckboxActive') }}/"+ UserId +'/'+ active, 'GET');
			});

			//change view default/detail
			$('#viewMode').change(function(event) {
				$(this).prop("checked") == true
				? locationPage('{{ route('admin.Users') }}/default'+req)
				: locationPage('{{ route('admin.Users') }}/detail'+req);
			});

			//click image
			$('.view-img').on('click', function(e) {
				e.preventDefault();
				let url_img = $(this).attr('src');
				$.alert({
					title: ` `,
					content: `<div style="width: 100%; text-align: center;"><img src="${url_img}" onerror="this.src='/imgs/user-blank.jpg'" /></div>`,
					buttons: {
						close: function () { },
					}
				});
			});

			//
            $('#tutorial').hide();
            $('#select-type1').change(function () {
                if($(this).val() == 'Birthday'){
                    $('#tutorial').show();
                    $('#tutorial').css('pointer-events','all');
                }else {
                    $('#tutorial').hide();
                    $('#tutorial').css('pointer-events', 'none');
                }
            });

            //sau khi search
            if($('#select-type1 option:selected').val() == 'Birthday'){
                $('#tutorial').show();
                $('#tutorial').css('pointer-events','all');
            }

            $('#tutorial').click(function () {
                showTutorial('Tìm kiếm theo ngày điền dạng: "ngày/" VD: 14/ <br> Tìm kiếm theo tháng điền dạng: "/tháng" VD: /02 <br> Tìm kiếm theo năm điền dạng: "/năm" hoặc "năm" VD: /2020 hoặc 2020 <br>')
            });

        });
        function showTutorial(data, fncOK, fncCancel) {
            $.confirm({
                title: 'Hướng dẫn tìm kiếm',
                // icon: 'fa fas fa-question',
                content: data + '',
                buttons: {
                    cancel: {
                        text: 'Đóng',
                        btnClass: 'btn width5',
                        keys: ['esc'],
                        action: function(){
                            if (IsFunction(fncCancel)){
                                fncCancel();
                            }
                        }
                    }
                }
            });
        }

		$(document).on('change', '.role-item', function() {
			var id = $(this).attr('data-id');
			var t = $(this).prop('checked');
			groupId = $("[name='userId']").val();
			userId = $("[name='userId']").val();

			$.ajax({
				url: "{{ route('admin.AjaxRoleScreenDetailInput') }}/"+groupId+'/'+t+'/'+id+'/'+userId,
				type: 'get',
				success: function (data) {
					if (typeof data.errors !== 'undefined') {
						showErrors(data.errors);
					} else {
						// console.log(data);
						// listRole(groupId);
					}
				},
				fail: function (error) {
					console.log(error);
				}
			});
		});


		$(document).on('change', '.checkAll', function() {
			var checked = $(this).prop('checked');
			var arrRole = [];
			data = [];
			$('.role-item').each(function() {
				if($(this).prop('checked') != checked) {
					data.push($(this).attr('data-id'));
				}

			});
			arrRole.push({name: 'data', value: data});
			arrRole.push({name: 'userId', value: $("[name='userId']").val()});
			arrRole.push({name: 'checked', value: checked});
			if(checked == true) {
				$('.role-item').prop('checked', true);
			} else {
				$('.role-item').prop('checked', false);
			}
			$.ajax({
				url: "{{ route('admin.AjaxRoleScreenDetailInputPost') }}",
				type: 'post',
				data: arrRole,
				success: function (data) {
					console.log(data);
					if (typeof data.errors !== 'undefined') {
						showErrors(data.errors);
					} else {
						// console.log(data);
						// listRole(groupId);
					}
				},
				fail: function (error) {
					console.log(error);
				}
			});
		});

		$(document).on('click', '.paginate_button', function(e) {
			$('.checkAll').prop('checked', false);
		});
	</script>
@endsection
