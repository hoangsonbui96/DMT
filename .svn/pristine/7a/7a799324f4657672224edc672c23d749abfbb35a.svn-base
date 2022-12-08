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
<style>
    .selected {
        border: 2px solid #3c8dbc;border-radius: 5px;
    }
    .mainClass {
        border: 0.5px solid gray;border-radius: 5px;
    }
    .new-main {
        border: 2px solid #3c8dbc !important;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .custom-file-input {
        display: flex;
        width: 100% !important;
        height:80px;margin-top: 10px;
        border-radius: 10px;
    }
    .main {
        padding: 5px 0;
    }
    /* .form-control {
        height:40px;
        border-radius: 10px;
        padding: 12px 20px;
        margin: 8px 0;
    } */
    .table.table-bordered th, .table.table-bordered td {
        border: 1px solid #bdb9b9 !important;
        text-align: center;
        vertical-align: middle !important;
        background-color: #fff;
    }

    .flex-row {
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        align-content: center;
        justify-content: center;
        align-items: center;
    }

    .SummaryMonth .table.table-bordered tr th { background-color: #dbeef4; }
    .tbl-dReport .table.table-bordered tr th { background-color: #c6e2ff; }
	.tbl-top { margin-top: 0px; }

</style>
<section class="content-header left232 daily-header top49">
    <h1 class="page-header">Yêu cầu thực hiện công việc</h1>
</section>
<section class="content">
    <div class="col-lg-8 col-md-8 col-sm-8 daily-content top100" style="padding: 0">
        @include('taskrequest::task-request-search')
    </div>
     <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top: 85px; padding:0">
        @component('admin.component.table')
				@slot('columnsTable')
					<tr>
						<th class="width3pt">@lang('admin.stt')</th>
						<th>Người yêu cầu</th>
						<th>Yêu cầu lúc</th>
						<th>Nội dung yêu cầu</th>
						<th>Dự án</th>
                        <th>Người nhận</th>
						<th>Nội dung phản hồi</th>
                        <th>Phản hồi lúc</th>
                        @if ($canEdit || $canDelete)
							<th class="width5pt">@lang('admin.action')</th>
						@endif
					</tr>
				@endslot
                @slot('dataTable')
					@foreach($task_request_list as $index => $task_request)
					<tr class="{{($task_request->needResponse == true) ? 'info' : ''}}">
							<td>{{ ($task_request_list->currentPage() - 1) * $task_request_list->perPage() + $index+1 }}</td>
                            <td class ="left-important"> {{ \App\User::find($task_request->requestUserID)->FullName}}</td>
                            <td class ="center-important"> {{ FomatDateDisplay($task_request->requestTime, "d/m/Y H:i")}}</td>
                            <td class ="left-important"> {!! nl2br(e($task_request->sumaryContent)) !!}</td>
                            <td class ="left-important"> {{ \App\Project::find($task_request->projectID)->NameVi}}</td>
                            <td class ="left-important"> {{ \App\User::find($task_request->receiveUserID)->FullName}}</td>

                            @if($task_request->responseContent != null)
                                <td class ="center-important ">
                                    <span class="label label-success data-toggle=">Đã Phản Hồi</span>
                                </td>
                            @else
                                <td class ="center-important ">
                                    <span class="label label-danger data-toggle=">Chưa Phản Hồi</span>
                                </td>
                            @endif

                            @if($task_request->responseTime != null)
                                <td class ="center-important"> {{ FomatDateDisplay($task_request->responseTime, "d/m/Y H:i")}}</td>
                            @else
                                <td class ="center-important"> - </td>
                            @endif

                            @if ($canEdit || $canDelete)
								<td class="text-center">
                                    @if ($canEdit)
                                        <span class="action-col update edit review-request-task" item-id="{{ $task_request->id }}">
                                            <i class="fa fa-eye" aria-hidden="true" title="Chi tiết"></i></span>
                                        @if($task_request->DeleteorEdit == true)
                                            <span class="action-col update edit update-request-task" item-id="{{ $task_request->id }}">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true" title="Chỉnh sửa"></i></span>
                                        @endif
                                    @endif
                                    @if ($canDelete)
                                        @if($task_request->DeleteorEdit == true)
                                            <span class="action-col update delete delete-request-task"  item-id="{{ $task_request->id }}">
                                                <i class="fa fa-times" aria-hidden="true" title="Xóa"></i></span>
                                        @endif
                                    @endif
								</td>
							@endif
						</tr>
					@endforeach
				@endslot
                @slot('pageTable')
                    {{ $task_request_list->links() }}
                @endslot
			@endcomponent
     </div>
</section>
@endsection
@section('js')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript" async>
    let orderNumber = 0;
    let temp = 0;
    let listEditor = [];

    var UrlUpdateRequestTask = "{{ route('admin.TaskRequestDetail') }}";
    var UrlReviewRequestTask = "{{ route('admin.TaskRequestReview') }}";

    const actionMain = (item) => {
        $(this).find(".mainClass").addClass('new-main');

        let parent = $('.new-main');
        let parent_offset = $(parent).offset();

        $("#control-tab").remove();

        html = `<div id="control-tab" class = "column" style="
                margin-top: 5px;
                height: 90px;
                width: 45px;
                border-radius: 5px;
                border: 1px solid gray;
                padding: 1px;
                align-items: center;text-align: center;
                display: flex;
                flex-direction: column;
                justify-content: space-around;
                margin-left:2px;z-index: 99;right: 4%;position: absolute;" class="col-sm-1">
                <i class="fa fa-plus-circle fa-2x" aria-hidden="true" onclick="addProject('b${item}')"></i>
				<i class="fa fa-trash fa-2x" aria-hidden="true" onclick="removeForm('b${item}',this)"></i>
            </div>`;

        $("#b"+item).append(html);

        $('.main').click(function (e) {
            $("#control-tab").remove();
            var b = $(this).attr('data-id');
            $("#"+b).append(html);
            $('.fa.fa-plus-circle.fa-2x').remove();
            $('.fa.fa-trash.fa-2x').remove();
            $('#control-tab').append('<i class="fa fa-plus-circle fa-2x" aria-hidden="true" onclick="addProject(`'+b+'`)"></i>');
			$('#control-tab').append('<i class="fa fa-trash fa-2x" aria-hidden="true" onclick="removeForm(`'+b+'`,this)"></i>');
            $('.new-main').removeClass('new-main');
            $(this).find(".mainClass").addClass('new-main');
            let parent = $('.new-main');
            let parent_offset = $(parent).offset();
        })
    }
</script>
@endsection
