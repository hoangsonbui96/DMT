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
@section('content')
<section class="content-header">
	<h1 class="page-header">@lang('admin.partner.partner_management')</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<form class="form-inline" id="meeting-search-form">
				<div class="form-group pull-left margin-r-5">
					<input type="search" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
				</div>
				<div class="form-group pull-left margin-r-5">
					<button type="button" class="btn btn-primary btn-search" id="btn-search-meeting">@lang('admin.btnSearch')</button>
                </div>
{{--                <div class="form-group pull-left">--}}
{{--                    @can('action', $export)--}}
{{--                        <a class="btn btn-success" id="btn-export-partner" data="{{ isset($request['search']) ? $request['search'] : null  }}">@lang('admin.export-excel')</a>--}}
{{--                    @endcan--}}
{{--                </div>--}}

                <div class="form-group pull-right">
                    @can('action', $export)
                        <a class="btn btn-success" id="btn-export-partner" data="{{ isset($request['search']) ? $request['search'] : null  }}">@lang('admin.export-excel')</a>
                    @endcan
                    @can('action',$add)
                        <button type="button" class="btn btn-primary btn-detail" id="add_new_partner">@lang('admin.partner.add_new_partner')</button>
                    @endcan
                </div>
				@if($errors->any())

				<h4 style="color:red;">{{$errors->first()}}</h4>
				@endif
				@if(Session::has('success'))
					{!! Session::get('success') !!}
				@endif
				<div class="clearfix"></div>
			</form>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			@component('admin.component.table')
				@slot('columnsTable')
					<tr>
						<th class="width5"><a class="sort-link" data-link="{{ route("admin.Partner") }}/id/" data-sort="{{ $sort_link }}">@lang('admin.stt')</a></th>
						<th><a class="sort-link" data-link="{{ route("admin.Partner") }}/full_name/" data-sort="{{ $sort_link }}">@lang('admin.partner.full_name')</a></th>
						{{-- <th><a class="sort-link" data-link="{{ route("admin.Partner") }}/department_id/" data-sort="{{ $sort_link }}">@lang('admin.partner.department_id')</a></th> --}}
						{{-- <th><a class="sort-link" data-link="{{ route("admin.Partner") }}/birthday/" data-sort="{{ $sort_link }}">@lang('admin.partner.birthday')</a></th> --}}
						<th><a class="sort-link" data-link="{{ route("admin.Partner") }}/InfoRepresentatives/" data-sort="{{ $sort_link }}">@lang('admin.partner.InfoRepresentatives')</a></th>
						<th><a class="sort-link" data-link="{{ route("admin.Partner") }}/tel/" data-sort="{{ $sort_link }}">@lang('admin.partner.tel')</a></th>
						<th><a class="sort-link" data-link="{{ route("admin.Partner") }}/email/" data-sort="{{ $sort_link }}">@lang('admin.partner.email')</a></th>
						<th><a class="sort-link" data-link="{{ route("admin.Partner") }}/address/" data-sort="{{ $sort_link }}">@lang('admin.partner.address')</a></th>
						{{-- <th><a class="sort-link" data-link="{{ route("admin.Partner") }}/sectors/" data-sort="{{ $sort_link }}">@lang('admin.partner.sectors')</a></th> --}}
						@if ($canEdit || $canDelete)
						<th class="width8">@lang('admin.action')</th>
						@endif
					</tr>
				@endslot
				@slot('dataTable')
					@foreach($partners as $item)
						<tr class="even gradeC" data-id="">
							<td class="text-center">{{ $sort == 'desc' ? ++$stt : $stt-- }}</td>
							<td  class = "left-important">{{ $item->full_name }}</td>
							{{-- <td> {{ $item->Name }} </td> --}}
							{{-- <td>{{ \Carbon\Carbon::parse($item->birthday)->format('d/m/Y') }}</td> --}}
							<td>{{ $item->InfoRepresentatives }}</td>
							<td>  {{ $item->tel }} </td>
							<td>{{ $item->email }}</td>
							<td>{!! nl2br(e($item->address)) !!}</td>
							{{-- <td> {{ $item->sectors }}</td> --}}
							@if ($canEdit || $canDelete)
							<td class="text-center">
								@can('action', $edit)
								<span class="action-col update edit update-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
								@endcan
								@can('action', $delete)
								<span class="action-col update delete delete-one"  item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
								@endcan
							</td>
							@endif
						</tr>
					@endforeach
				@endslot
				@slot('pageTable')
					{{ $partners->appends($query_array)->links() }}
				@endslot
			@endcomponent
			<div id="popupModal">
			</div>
		</div>
	</div>
</section>
@endsection
@section('js')
	<script type="text/javascript" async>
		var ajaxUrl = "{{ route('admin.PartnerInfo') }}";
		var newTitle = 'Thêm mới đối tác';
		var updateTitle = 'Cập nhật đối tác';

		$(function () {
			var search = $('input[name=search]').val();
			$('#btn-export-partner').click(function (e) {
				e.preventDefault();
				if(search != ''){
					req = '?&search='+search;
					window.location.href = '{{ route('export.Partner') }}'+req;
				} else {
					window.location.href = '{{ route('export.Partner') }}?search='+search;
				}
			});
		});

		$('.btn-search').click(function () {
			$('#meeting-search-form').submit();
		});
	</script>
@endsection
