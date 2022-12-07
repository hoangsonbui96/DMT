@extends('admin.layouts.default.app')
@section('content')
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
<section class="content-header">
	<h1 class="page-header">@lang('admin.project.projects_management')</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<form class="form-inline">
				<div class="input-group pull-left margin-r-5">
					<input type="search" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
				</div>
                <div class="input-group pull-left">
                    <button type="submit" class="btn btn-primary" id="btn-searchAbReport" >@lang('admin.btnSearch')</button>
                </div>
				<div class="form-group pull-right">
					@can('action', $add)
						<button type="button" class="btn btn-primary btn-detail" id="add-new-room-btn">@lang('admin.project.add_new_project')</button>
					@endcan
				</div>
				<div class="clearfix"></div>
			</form>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			@component('admin.component.table')
				@slot('columnsTable')
					<tr>
						<th class="width3pt"><a class="sort-link" data-link="{{ route("admin.Projects") }}/id/" data-sort="{{ $sort_link }}">@lang('admin.stt')</a></th>
						<th><a class="sort-link" data-link="{{ route("admin.Projects") }}/NameVi/" data-sort="{{ $sort_link }}">@lang('admin.project.name')</a></th>
						<th><a class="sort-link" data-link="{{ route("admin.Projects") }}/NameShort/" data-sort="{{ $sort_link }}">@lang('admin.project.name_short')</a></th>
						<th><a class="sort-link" data-link="{{ route("admin.Projects") }}/Customer/" data-sort="{{ $sort_link }}">@lang('admin.project.customer')</a></th>
						<th class="width5"><a class="sort-link" data-link="{{ route("admin.Projects") }}/StartDate/" data-sort="{{ $sort_link }}">@lang('admin.project.start_date')</a></th>
						<th class="width5"><a class="sort-link" data-link="{{ route("admin.Projects") }}/EndDate/" data-sort="{{ $sort_link }}">@lang('admin.project.end_date')</a></th>
						<th class="width3"><a class="sort-link" data-link="{{ route("admin.Projects") }}/Active/" data-sort="{{ $sort_link }}">@lang('admin.active')</a></th>
						@if ($canEdit || $canDelete)
						<th class="width8">@lang('admin.action')</th>
						@endif
					</tr>
				@endslot
				@slot('dataTable')
					@foreach($projects as $item)
						<tr class="even gradeC" data-id="10184">
							<td class="text-center">{{ $sort == 'desc' ? ++$stt : $stt-- }}</td>
							<td class="left-important">{{ $item->NameVi }}</td>
							<td class="left-important">{{ $item->NameShort }}</td>
							<td class="left-important">{{ $item->Customer }}</td>
							<td class="text-center">{{ FomatDateDisplay($item->StartDate, FOMAT_DISPLAY_DAY) }}</td>
							<td class="text-center">{{ FomatDateDisplay($item->EndDate, FOMAT_DISPLAY_DAY) }}</td>
							<td class="text-center"><input class="action-col checkActive" user-id="{{$item->id}}" type='checkbox'
															value="" {{ (isset($item->Active) && $item->Active == 1) ? 'checked' : ''}} {{$canEdit == false ? 'disabled' : ''}}></td>
							@if ($canEdit || $canDelete)
							<td class="text-center">
								@if ($canEdit)
								<span class="action-col update edit update-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
								@endif
								@if ($canDelete)
								<span class="action-col update delete delete-one"  item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
								@endif
							</td>
							@endif
						</tr>
					@endforeach
				@endslot
				@slot('pageTable')
					{{ $projects->appends($query_array)->links() }}
				@endslot
			@endcomponent
		</div>
	</div>
</section>
@endsection

@section('js')
	<script type="text/javascript" async>
		var ajaxUrl = "{{ route('admin.ProjectInfo') }}";
		var newTitle = 'Thêm dự án mới';
		var updateTitle = 'Cập nhật dự án';

		$(function () {
			$('.checkActive').on('change',function () {
				$('.loadajax').show();
				var projecId = $(this).attr('user-id');
				var active = $(this).prop("checked") === true ? 1 : 0;

				$.ajax({
					url: "{{ route('admin.CheckboxActiveProject') }}/"+ projecId +'/'+ active,
					success: function (data) {
						$('.loadajax').hide();
						return true;
					},
					fail: function (error) {
						console.log(error);
					}
				});
			});
		});
	</script>
@endsection
