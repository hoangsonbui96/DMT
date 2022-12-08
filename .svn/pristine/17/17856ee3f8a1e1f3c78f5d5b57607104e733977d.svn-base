@extends('admin.layouts.default.app')
@push('pageCss')
	<style>
		#page-wrapper{
			/* background: #0079bf;
			overflow-x: scroll; */
		}
		.page-header{
			/* color: white;
			border: none; */
		}
	</style>
@endpush
@push('pageJs')

@endpush
@section('content')
	<div id="container">
		<div class="group-top">
			<div class="col-lg-12">
				<h1 class="page-header">@lang('admin.task.management')</h1>
			</div>
			{{-- <div class="row" style="margin-bottom: 20px;">
				<div class="col-md-8 col-sm-12 col-xs-12">

				</div>
				<div class="col-md-4 col-sm-12 col-xs-12">
					<div class="add-dReport">
						<form action="">
							<button type="button" class="btn btn-primary btn-detail" id="add-new-room-btn">@lang('admin.masterdata.add')</button>
						</form>
					</div>
				</div>
				<div class="clear"></div>
			</div> --}}
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="pull-left">
					<div class="dataTables_length" id="dataTables-user_length">
						{{-- <label> Hiển thị
							<select name="dataTables-user_length" class="form-control input-sm num-page-select">
								<option value="10">10</option>
								<option value="25">25</option>
								<option value="50">50</option>
								<option value="100">100</option>
							</select> bản ghi
						</label> --}}
					</div>
				</div>
				<div class="pull-right">
					<div id="dataTables-user_filter" class="dataTables_filter">
						<label>
							<form>
								<input type="search" class="form-control input-sm" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
								<input type='submit' value='Search' style="display: none"/>
							</form>
						</label>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>

		<div class="work-management row">
			@foreach($list as $item)
				<div class="col-md-3 project-grid" data-id="{{ $item->id }}">
					<div class="inside"><div class="project-header">{{ $item->NameVi }}</div></div>
				</div>
			@endforeach
			{{ $list->appends($query_array)->links() }}
		</div>

		<div id="popupModal">
			{{-- @include('admin.includes.user-detail') --}}
		</div>
	</div>
	<script>
		var ajaxUrl = "{{ route('admin.MasterDataItem') }}";
		var newTitle = 'Thêm phòng mới';
		var updateTitle = 'Cập nhật phòng';
		$(".project-grid").click(function() {
			window.location.href = "{{ route('admin.TasksInProject') }}/"+$(this).attr('data-id');
		});
	</script>
@endsection
