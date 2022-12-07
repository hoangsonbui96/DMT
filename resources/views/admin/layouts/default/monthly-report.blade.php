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
            border: 0.5px solid gray;
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
            height: 80px;
            margin-top: 10px;
            border-radius: 10px;
        }

        .main {
            padding: 5px 0;
        }

        .table.table-bordered th,
        .table.table-bordered td {
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

        .SummaryMonth .table.table-bordered tr th {
            background-color: #dbeef4;
        }

        .tbl-dReport .table.table-bordered tr th {
            background-color: #c6e2ff;
        }

        .tbl-top {
            margin-top: 10px;
        }

        .box {
            display: flex;
        }

        .content-scroll {
            padding: 5px;
            border: 1px solid #d2d6de;
            border-radius: 1px;
            max-height: 24vh;
            overflow: auto;
        }

        .open-comment {
            cursor: pointer;
        }
    </style>
    <section class="content-header daily-header">
        <h1 class="page-header">
            <a href="javascript:void(0)" class="btn btn-primary" onclick="window.history.go(-1); return false;"
               style="margin-right: 5px;"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
            {{$NameProject}}
        </h1>
    </section>
    <section class="content">
        <div class="col-lg-12 col-md-12 col-sm-12 daily-content" style="padding: 0; top:108px">
            @include('admin.includes.monthly-report-search')
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top: 90px; margin-bottom:10px; padding:0">
            <label class="name_user" for="screen_name">Nhận xét chung</label>
            @if($t_meeting_week->ChairID == auth()->id())
                <textarea class="form-control" type="text" id="editor" style="margin-bottom: 10px;" name="Comment"
                          placeholder="Nhận xét báo cáo..." rows="4">{{ $t_meeting_week->Evaluation }}</textarea>
                <div class="status_main"></div>
                {{--                <button type="button" class="btn btn-primary" style="margin-top:10px" id="add_comment">Lưu nhận xét--}}
                {{--                </button>--}}
            @else
                @if($t_meeting_week->Evaluation == "" || $t_meeting_week->Evaluation == null)
                    <label class="name_user text-danger" for="screen_name">&nbsp;Chưa có nhận xét</label>
                @else
                    <div class="content-scroll">
                        {!! $t_meeting_week->Evaluation !!}
                    </div>
                @endif
        </div>
        @endif
        @component('admin.component.table')
            @slot('columnsTable')
                <tr>
                    <th class="width3pt">@lang('admin.stt')</th>
                    <th>Người báo cáo</th>
                    <th>Tên tiêu đề</th>
                    <th>Thời gian báo cáo</th>
                    <th>Nhận xét</th>
                    <th>Ngày tạo</th>
                    @if ($canEdit || $canDelete)
                        <th class="width5pt">@lang('admin.action')</th>
                    @endif
                </tr>
            @endslot
            @slot('dataTable')
                @foreach($t_reports as $index => $t_report)
                    <tr class="even gradeC" data-id="">
                        <td>{{ ($t_reports->currentPage() - 1) * $t_reports->perPage() + $index + 1 }}</td>
                        <td class="center-important"> {{ \App\User::find($t_report->UserId)->FullName}}</td>
                        <td class="center-important"> {!! nl2br(e($t_report->Content)) !!}</td>
                        <td class="center-important"> {{ FomatDateDisplay($t_report->StartDate, FOMAT_DISPLAY_DAY)}}
                            {{isset($t_report->EndDate) ? "-".FomatDateDisplay($t_report->EndDate, FOMAT_DISPLAY_DAY) : null}}
                        </td>
                        <td class="center-important ">
                            @php
                                $can_open = $t_report->IsPublic == 1
                                || ($t_report->IsPublic == 0 && ($t_report->UserId == auth()->id() || $t_report->IdReviewer == auth()->id()))
                            @endphp
                            @if(isset($t_report->Note_Report))
                                <span class="label label-success data-toggle {{ $can_open ? 'open-comment' : '' }}"
                                      title="Xem nhận xét" {!! $can_open ? 'data-item="'.$t_report->id.'"' : ''  !!}>
                                    Đã nhận xét
                                </span>
                            @else
                                <span class="label label-danger data-toggle">Chưa nhận xét</span>
                            @endif
                        </td>
                        <td class="center-important"> {{ FomatDateDisplay($t_report->created_at, FOMAT_DISPLAY_CREATE_DAY) }}</td>
                        <td class="text-center width8pt">
                            {{-- @if($t_report->IdReviewer == auth()->id() || $t_report->UserId == auth()->id()) --}}
                                <span class="action-col update edit review-one" item-id="{{ $t_report->id }}"
                                      title="Nhận xét"><i class="fa fa-comment-o" aria-hidden="true"></i></span>
                            {{-- @endif --}}
                            {{-- @if($t_report->Note_Report == null && $t_report->UserId == auth()->id()) --}}
							@if($t_report->UserId == auth()->id())
                                <span class="action-col update edit update-one" item-id="{{ $t_report->id }}"
                                      title="Chỉnh sửa"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                <span class="action-col update delete delete-one" item-id="{{ $t_report->id }}"
                                      title="Xóa"><i class="fa fa-times" aria-hidden="true"></i></span>
                            @endif

                        </td>
                    </tr>
                    @endforeach
                    @endslot
                    @slot('pageTable')
                    {{ $t_reports->links() }}
                    @endslot
                    @endcomponent
                    </div>
    </section>
@endsection
@section('js')
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript" async>
        function makeCKEditor(id, value) {
            CKEDITOR.config.height = 210;
            CKEDITOR.config.width = 'auto';
            CKEDITOR.config.editorplaceholder = 'Nhận xét...';
            CKEDITOR.config.image_previewText = CKEDITOR.tools.repeat(' ', 1);
            let editor = CKEDITOR.replace(id, {
                // Remove unused plugins.
                removePlugins: 'bidi,dialogadvtab,div,filebrowser,flash,format,forms,horizontalrule,iframe,justify,liststyle,pagebreak,showborders,stylescombo,templates',
                // Width and height are not supported in the BBCode format, so object resizing is disabled.
                disableObjectResizing: true,
                // Define font sizes in percent values.
                fontSize_sizes: "50/50%;100/100%;120/120%;150/150%;200/200%;300/300%",
                toolbar: [
                    ['Bold', 'Italic', 'Underline'],
                    ['FontSize'],
                    ['TextColor'],
                    ['NumberedList', 'BulletedList', '-', 'Blockquote'],
                    ['Maximize']
                ],
                // Strip CKEditor smileys to those commonly used in BBCode.
                smiley_images: [
                    'regular_smile.png', 'sad_smile.png', 'wink_smile.png', 'teeth_smile.png', 'tongue_smile.png',
                    'embarrassed_smile.png', 'omg_smile.png', 'whatchutalkingabout_smile.png', 'angel_smile.png',
                    'shades_smile.png', 'cry_smile.png', 'kiss.png'
                ],
                smiley_descriptions: [
                    'smiley', 'sad', 'wink', 'laugh', 'cheeky', 'blush', 'surprise',
                    'indecision', 'angel', 'cool', 'crying', 'kiss'
                ]
            });
            editor.on('change', function (evt) {
                $('#editor0').val(evt.editor.getData())
            });
            if (value) {
                CKEDITOR.instances[id].setData(value);
            }
        }

    </script>
    <script type="text/javascript" defer>
        let orderNumber = 0;
        let temp = 0;
        let listEditor = [];
        let ajaxUrl = "{{ route('admin.MonthlyDetail') }}";
        let ajaxUrlReview = "{{ route('admin.MonthlyReview')}}";
        var timeout;

        @if($t_meeting_week->ChairID == auth()->id())
        makeCKEditor("editor", `{!! $t_meeting_week->Evaluation !!}`);
        @endif

        function autoSaveGeneral(obj) {
            $.ajax({
                method: "POST",
                url: "{{ route('admin.MeetingWeeklyCommentSave', $idRequest) }}",
                beforeSend: () => $('.status_main').html(`<span class="text-success" id="status_mess_main">Đang lưu...</span>`),
                data: obj,
            }).done(function (res) {
                $('.status_main').html(`<span class="text-success" id="status_mess_main">${res}</span>`)
                $('#status_mess_main').hide();
                $("#status_mess_main").show().delay(4000).fadeOut();
            }).fail(function (error) {
                $('.status_main').html(`<span class="text-danger" id="status_mess_main">${error.responseJSON}</span>`)
                $('#status_mess_main').hide();
                $("#status_mess_main").show().delay(4000).fadeOut();
            });
        }

        $(".open-comment").click(event => {
            event.preventDefault();
            let id = $(event.target).attr("data-item");
            if (id === undefined || id === "")
                return null;
            let url = "{{ route("admin.ProjectCommentSpecific", ":id") }}";
            url = url.replace(":id", id);
            ajaxGetServerWithLoader(url, "GET", null, response => {
                $('#popupModal').empty().html(response);
                $('.modal').modal('show');
            }, error => {
                showErrors(error.responseJSON);
            })
        });

        {{--$('#add_comment').click(function (event) {--}}
        {{--    event.preventDefault();--}}
        {{--    if (CKEDITOR.instances.editor.document.getBody().getText().trim() === '') {--}}
        {{--        showErrors('Nội dung nhận xét không được để trống');--}}
        {{--        return;--}}
        {{--    }--}}
        {{--    let data = [--}}
        {{--        // {name: 'id', value: $('#mReport_id').val()},--}}
        {{--        {name: 'Comment', value: CKEDITOR.instances.editor.getData()},--}}
        {{--    ]--}}
        {{--    data = [...$('#daily-form').serializeArray(), ...data];--}}
        {{--    ajaxGetServerWithLoader("{{ route('admin.MeetingWeeklyCommentSave', $idRequest) }}", 'POST', data, function (response) {--}}
        {{--        showSuccess(response);--}}
        {{--    });--}}
        {{--})--}}

        CKEDITOR.instances.editor.on("change", function (event) {
            const self = this;
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                const data = [{name: 'Comment', value: self.getData()}];
                autoSaveGeneral(data);
            }, 1500);
        });


    </script>
@endsection
