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
    .mainClass {
        border: .5px solid gray;
        border-radius: 5px;
    }
    .new-main {
        border: 2px solid #3c8dbc !important;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .custom-file-input {
        display: flex;
        width: 100% !important;
        height:80px;
        margin-top: 10px;
        border-radius: 10px;
    }
    .main {
        padding: 5px 0;
    }
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
	.tbl-top { margin-top: 0; }
    .td-user { display:block; }
</style>
<section class="content-header left232 daily-header top49">
    <h1 class="page-header"> Danh sách báo cáo quản lý dự án</h1>
</section>
<section class="content">
    <div class="col-lg-8 col-md-8 col-sm-8 daily-content top100" style="padding: 0">
        @include('admin.includes.meeting-weekly-search')
    </div>

    <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top: 85px; padding:0">
        @component('admin.component.table')
				@slot('columnsTable')
					<tr>
						<th class="width3pt">@lang('admin.stt')</th>
                        <th>Tên tiêu đề</th>
						<th>Thời gian bắt đầu</th>
                        <th>Thời gian kết thúc</th>
						<th>Nhận xét chung</th>
						<th class="width12">Người tham gia</th>
                        <th>Người nhận báo cáo</th>
                        <th>Người tạo</th>
						<th>Thời gian tạo</th>
						<th>Hạn nộp</th>
                        @if ($canEdit || $canDelete)
							<th class="width5pt">@lang('admin.action')</th>
						@endif
					</tr>
				@endslot
                @slot('dataTable')
					@foreach($t_meetings as $index => $t_meeting)
                        <tr class="even gradeC
                            {{ $t_meeting->IsColor
                            ? 'info' : ''  }}" data-id="" >
							<td>{{ ($t_meetings->currentPage() - 1) * $t_meetings->perPage() + $index+1 }}</td>
                            <td class ="" style="text-align: left"> {!! nl2br(e($t_meeting->MeetingName)) !!}</td>
                            <td class="text-center">{{FomatDateDisplay($t_meeting->MeetingTimeFrom, FOMAT_DISPLAY_DAY)}}</td>
							<td class="text-center">{{FomatDateDisplay($t_meeting->MeetingTimeTo, FOMAT_DISPLAY_DAY)}}</td>
                            @if($t_meeting->IsComment)
                                <td class ="center-important">
                                    <span class="label label-success open-comment" style="cursor:pointer;" data-item="{{ $t_meeting->id }}" title="Xem nhận xét">Đã nhận xét</span>
                                </td>
                            @else
                            <td class ="center-important ">
                                <span class="label label-danger">Chưa nhận xét</span>
                            </td>
                            @endif
                            <td style="text-align:left;"> {!! $t_meeting->Members !!}</td>
                            <td class ="center-important"> {{ \App\User::find($t_meeting->ChairID)->FullName}}</td>
                            <td class ="center-important"> {{ \App\User::find($t_meeting->RegisterId)->FullName}}</td>
                            <td class ="center-important"> {{ FomatDateDisplay($t_meeting->created_at, FOMAT_DISPLAY_CREATE_DAY) }}</td>
                            <td class ="center-important"> {{ isset($t_meeting->TimeEnd) ? FomatDateDisplay($t_meeting->TimeEnd, FOMAT_DISPLAY_DATE_TIME) : '' }}</td>
                            <td class="text-center">
                                @if ($t_meeting->can_join == true || $t_meeting->ChairID == auth()->id())
                                    <a href="{{ route("admin.MonthlyReports", $t_meeting->id) }}" >
                                        <i class="fa fa-eye" aria-hidden="true" style="color:black;" data-toggle="tooltip" data-placement="bottom" title="Chi tiết"></i>
                                    </a>
                                @endif
                                @if ($t_meeting->RegisterId == auth()->id() || $t_meeting->ChairID == auth()->id())
                                    <span class="action-col update edit update-meeting" item-id="{{ $t_meeting->id }}">
                                        <i class="fa fa-pencil-square-o" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Chỉnh sửa"></i>
                                    </span>
                                    <span class="action-col update delete delete-one"  item-id="{{ $t_meeting->id }}">
                                        <i class="fa fa-times" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Xóa"></i>
                                    </span>
                                @endif

                            </td>
						</tr>
					@endforeach
				@endslot
                @slot('pageTable')
                    {{ $t_meetings->appends($query_array)->links() }}
                @endslot
			@endcomponent
     </div>
</section>
@endsection
@section('js')
<script type="text/javascript" async>
     var ajaxUrl = "{{ route('admin.MeetingWeeksDetail') }}";
     var updateTitle = "Chỉnh sửa báo cáo cuộc họp";


    $(".open-comment").click(event => {
        event.preventDefault();
        let id = $(event.target).attr("data-item");
        if (id === undefined || id === "")
            return null;
        let url = "{{ route("admin.ProjectComment", ":id") }}";
        url = url.replace(":id", id);
        ajaxGetServerWithLoader(url, "GET", null, response => {
            $('#popupModal').empty().html(response);
            $('.modal').modal('show');
        }, error => {
            showErrors(error.responseJSON);
        })
    });

    $(document).ready(function() {
        const member_td = $("table[name='table']").find("tbody>tr>td:nth-child(6)");
        $(member_td).each(function (index, element) {
            let usernames = $(element).find(".td-user");
            $(usernames).each(function (i, e) {
                if (i > 2)
                    $(e).addClass("hide");
            });
            if (usernames.length > 3) {
                $(element).append(`<a href="javascript:void(0)" class="show-more">xem thêm...</a>`);
            }
        });

        $(".show-more").click(function (event) {
            event.preventDefault();
            let td = $(event.target).closest("td");
            let hide = $(td).find(".hide");
            if (hide.length !== 0) {
                $(td).find(".hide").removeClass("hide");
                $(event.target).text("ẩn bớt");
            } else {
                let usernames = $(td).find(".td-user");
                $(usernames).each(function (i, e) {
                    if (i > 2)
                        $(e).addClass("hide");
                    $(event.target).text("xem thêm...");
                })
            }
        })
    })
</script>
@endsection
