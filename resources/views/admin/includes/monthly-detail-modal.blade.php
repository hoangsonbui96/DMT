<style>
    .too-long {
        display: inline-block;
        width: 16rem;
        white-space: nowrap;
        overflow: hidden !important;
        text-overflow: ellipsis;
    }
</style>
<div class="modal draggable fade in review-modal" id="monthly-modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable ">
        @php
            $listEditor = [];
        @endphp
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">Thêm mới báo cáo</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" id="daily-form">
                    @csrf
                    <input type="hidden" class="form-control hidden " id="mReport_id"
                           value="{{ isset($weeklyInfo->id) ? $weeklyInfo->id : null }}">
                    <div class="container">
                        <div class="row col-sm-9 col-xs-12">
                            <div class="">
                                <div class="row"
                                     style="display: flex; justify-content: center; flex-direction:column; align-items: center;">
                                    <h3 style="text-transform: uppercase;
                                    font-weight: 600;
                                    ">{{ $weeklyInfo->Content }}</h3>
                                </div>
                            </div>
                            <div class="" style="margin-top: 10px;">
                                <p>
                                    <label>Người báo
                                        cáo:&nbsp;</label>{{ \App\User::find($weeklyInfo->UserId)->FullName }}.&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label>Ngày:&nbsp;</label>
                                    {{ \Illuminate\Support\Carbon::parse($weeklyInfo->StartDate)->format(FOMAT_DISPLAY_DAY) }}
                                    @if(isset($weeklyInfo->EndDate))
                                    -
                                    {{\Illuminate\Support\Carbon::parse($weeklyInfo->EndDate)->format(FOMAT_DISPLAY_DAY)}}
                                    @endif
                                    .
                                </p>
                            </div>
                            <div class="">
                                @if($weeklyInfo->IdReviewer == auth()->id())
                                    <label for="editorR">Nhận xét</label>
                                    <textarea class="form-control" type="text" id="editorR"
                                              name="Note_Report" placeholder="Nhận xét báo cáo..." rows="4"></textarea>
                                    <div class="status"></div>
                                    <label class="checkbox-inline">
                                        <input data-toggle="toggle" name="change_public" type="checkbox"
                                               data-item="{{ $weeklyInfo->id }}" {{ $weeklyInfo->IsPublic == 1 ? 'checked' : '' }}>
                                        Công khai
                                    </label>
                                    <button type="button" class="btn btn-primary pull-right save-comment"
                                            style="margin-top: 10px">Lưu
                                    </button>
                                @else
                                    @if (isset($weeklyInfo->Note_Report))
                                        <p><label for="">Người nhận xét:&nbsp;</label> {{ isset($weeklyInfo->IdReviewer)
                                            ? \App\User::find($weeklyInfo->IdReviewer)->FullName : '' }}</p>
                                        <div>
                                            {!! $weeklyInfo->Note_Report !!}
                                        </div>
                                    @else
                                        <label class="text-danger" for="screen_name">Chưa có nhận xét</label>
                                    @endif
                                @endif
                            </div>
                            <div style="margin-top: 10px">
                                <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 10px;">
                                    @if (isset($weeklyInfo->detail_reports))
                                        @foreach ($weeklyInfo->detail_reports as $i => $item)
                                            <li class="{{$i==0 ? 'active' : ''}}" title="{{$item->NameProject}}">
                                                <a href="#report_{{$item->id}}" data-toggle="tab" class="too-long">
                                                    {{$item->NameProject}}
                                                </a>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                                <div class="tab-content"
                                     style="min-height: 10em; max-height: 25em; overflow: auto; padding: 5px">
                                    @if (isset($weeklyInfo->detail_reports))
                                        @foreach ($weeklyInfo->detail_reports as $i => $item)
                                            <div id="report_{{$item->id}}"
                                                 class="tab-pane fade {{$i==0 ? 'in active' : ''}}">
                                                {!! $item->Note !!}
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            {{--            <div class="modal-footer">--}}
            {{--                <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel"--}}
            {{--                        data-control="">@lang('admin.btnCancel')</button>--}}
            {{--                @if($weeklyInfo->IdReviewer == auth()->id())--}}
            {{--                    <button type="button" class="btn btn-primary save-form" id="saveReport"--}}
            {{--                            data-control="">@lang('admin.btnSave')</button>--}}
            {{--                @endif--}}
            {{--            </div>--}}
        </div>
    </div>
</div>


<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript" async>
    var updateTitle = "";
    var timeout;

    {{--function submit() {--}}
    {{--    let data = $('#daily-form').serializeArray();--}}
    {{--    data = [...data, ...[--}}
    {{--        {name: 'id', value: $('#mReport_id').val()},--}}
    {{--        {name: 'Comments', value: CKEDITOR.instances["editorR"].getData()}--}}
    {{--    ]]--}}
    {{--    ajaxGetServerWithLoader("{{ route('admin.weeklySave') }}", 'POST', data, function (data) {--}}
    {{--        if (typeof data.errors !== 'undefined') {--}}
    {{--            $('.loadajax').hide();--}}
    {{--            showErrors(data.errors);--}}
    {{--            return;--}}
    {{--        }--}}
    {{--        locationPage();--}}
    {{--    });--}}
    {{--}--}}

    function autoSave(obj) {
        $.ajax({
            method: "POST",
            url: "{{ route('admin.weeklySave') }}",
            beforeSend: () => $('.status').html(`<span class="text-success" id="status_mess">Đang lưu...</span>`),
            data: obj,
        }).done(function (res) {
            $('.status').html(`<span class="text-success" id="status_mess">${res}</span>`)
            $('#status_mess').hide();
            $("#status_mess").show().delay(4000).fadeOut();
            $(".save-comment").prop("disabled", false);
        }).fail(function (error) {
            $('.status').html(`<span class="text-danger" id="status_mess">${error.responseJSON}</span>`)
            $('#status_mess').hide();
            $("#status_mess").show().delay(4000).fadeOut();
        });
    }

    $(document).ready(function () {
        $('#saveReport').click(function (e) {
            e.preventDefault();
            if (CKEDITOR.instances["editorR"].document.getBody().getText().trim() === '') {
                showErrors('Nội dung nhận xét không được để trống');
                return false;
            }
        });

        $('.save-comment').click(function () {
            const self = $(this);
            $(self).prop("disabled", true);
            let data = $('#daily-form').serializeArray();
            data = [...data, ...[
                {name: 'id', value: $('#mReport_id').val()},
                {name: 'SendMail', value: 1},
                {name: 'Comments', value: CKEDITOR.instances["editorR"].getData()}
            ]]
            autoSave(data);
        })
    })

    CKEDITOR.config.height = 120;
    CKEDITOR.config.width = 'auto';
    CKEDITOR.config.image_previewText = CKEDITOR.tools.repeat(' ', 1);
    CKEDITOR.replace('editorR', {
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
    content = ``;
    @if(isset($weeklyInfo))
        content = `{!! $weeklyInfo->Note_Report !!}`;
    @endif
        CKEDITOR.instances["editorR"].setData(content);
    $("input[name='change_public']").change(function (e) {
        e.preventDefault();
        let data = {"id": $(this).attr("data-item")};
        ajaxServer("{{ route("admin.ReportPMPublic") }}", "GET", data, function () {
        }, function (error) {
            showErrors(error.errors);
        })
    })

    CKEDITOR.instances["editorR"].on("change", function (event) {
        clearTimeout(timeout);
        let data = $('#daily-form').serializeArray();
        data = [...data, ...[
            {name: 'id', value: $('#mReport_id').val()},
            {name: 'SendMail', value: 0},
            {name: 'Comments', value: CKEDITOR.instances["editorR"].getData()}
        ]]
        timeout = setTimeout(function () {
            autoSave(data);
        }, 1500);
    });


</script>
