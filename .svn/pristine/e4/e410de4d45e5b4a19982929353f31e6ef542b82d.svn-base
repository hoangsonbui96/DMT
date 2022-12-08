<style>
    .nav-tabs {
        margin-bottom: 10px;
    }
</style>
<div class="modal draggable fade in detail-modal" id="modal-comment" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title" style=" word-break: break-word;">Nhận xét {{ $meeting_name }}</h4>
            </div>
            @php
                $id_review = isset($t_meeting_week) ? $t_meeting_week->ChairID : (count($content_public) != 0 ? $content_public[0]->IdReviewer : 0)
            @endphp
            <div class="modal-body">
                <form class="form-horizontal detail-form">
                    <ul class="nav nav-tabs" role="tablist">
                        @if(isset($t_meeting_week))
                            <li class="active">
                                <a href="#comment_{{$t_meeting_week->id}}" data-toggle="tab">
                                    Nhận xét chung
                                </a>
                            </li>
                        @endif
                        @foreach($content_public as $index => $content)
                            @if($id_review != auth()->id())
                                @if(!is_null($content->Note_report))
                                    <li class="{{ $index == 0 && !isset($t_meeting_week) ? 'active' : '' }}">
                                        <a href="#comment_{{$content->TReportPmId}}" data-toggle="tab"
                                           title="Nhận xét báo cáo của {{ $content->FullName }}">
                                            {{$content->FullName}}
                                        </a>
                                    </li>
                                @endif
                            @else
                                <li class="{{ $index == 0 && !isset($t_meeting_week) ? 'active' : '' }}">
                                    <a href="#comment_{{$content->TReportPmId}}" data-toggle="tab"
                                       title="Nhận xét báo cáo của {{ $content->FullName }}">
                                        {{$content->FullName}}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                    @php
                        $listEditor = [];
                        if (isset($t_meeting_week)){
                            $listEditor[] = [
                            'id' => "editor_".$t_meeting_week->id,
                            'value' => $t_meeting_week->Evaluation,
                            'id_obj' => $t_meeting_week->id,
                            'key' => 't_meeting'
                            ];
                        }
                    @endphp
                    <div class="tab-content">
                        @if(isset($t_meeting_week))
                            <div id="comment_{{$t_meeting_week->id}}" class="tab-pane fade in active">
                                <label for="">Người nhận xét</label>
                                <p>{{ \App\User::find($t_meeting_week->ChairID)->FullName }}</p>
                                <label for="editor_{{$t_meeting_week->id}}">Nhận xét</label>
                                <textarea id="editor_{{$t_meeting_week->id}}" rows="12" class="editor"></textarea>
                            </div>
                        @endif
                        @foreach($content_public as $index => $content)
                            @if($id_review != auth()->id())
                                @if(!is_null($content->Note_report))
                                    <div id="comment_{{ $content->TReportPmId }}"
                                         class="tab-pane fade {{ $index == 0 && !isset($t_meeting_week) ? 'in active' : '' }}">
                                        <label for="">Người nhận xét</label>
                                        <p>{{ \App\User::find($content->IdReviewer)->FullName }}</p>
                                        <label for="editor_{{$content->TReportPmId}}">Nhận xét</label>
                                        <textarea id="editor_{{$content->TReportPmId}}" class="editor"
                                                  rows="12"></textarea>
                                    </div>
                                    @php
                                        $listEditor[] = [
                                            'id' => "editor_" . $content->TReportPmId,
                                            'value' => isset($content->Note_report) ? $content->Note_report : null,
                                            'id_obj' => $content->TReportPmId,
                                            'key' => 't_report'
                                        ];
                                    @endphp
                                @endif
                            @else
                                <div id="comment_{{ $content->TReportPmId }}"
                                     class="tab-pane fade {{ $index == 0 && !isset($t_meeting_week) ? 'in active' : '' }}">
                                    <label for="">Người nhận xét</label>
                                    <p>{{ \App\User::find($content->IdReviewer)->FullName }}</p>
                                    <label for="editor_{{$content->TReportPmId}}">Nhận xét</label>
                                    <textarea id="editor_{{$content->TReportPmId}}" class="editor" rows="12"></textarea>
                                </div>
                                @php
                                    $listEditor[] = [
                                        'id' => "editor_" . $content->TReportPmId,
                                        'value' => isset($content->Note_report) ? $content->Note_report : null,
                                        'id_obj' => $content->TReportPmId,
                                        'key' => 't_report'
                                    ];
                                @endphp
                            @endif
                        @endforeach
                    </div>
                    <div class="status"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                        id="cancel">@lang('admin.btnCancel')</button>
                @if(isset($t_meeting_week))
                    <a href="{{ route('admin.ProjectCommentDownloadPDF', $t_meeting_week->id) }}"
                       class="btn btn-primary btn-sm save-form">Xuất file PDF</a>
                @endif
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript" async>
    var timeout;

    function makeCKEditor(id, value) {
        CKEDITOR.config.height = 220;
        CKEDITOR.config.width = 'auto';
        CKEDITOR.config.image_previewText = CKEDITOR.tools.repeat(' ', 1);
        var editor = CKEDITOR.replace(id, {
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
        CKEDITOR.config.readOnly = false;
        editor.on('change', function (evt) {
            $('#editor0').val(evt.editor.getData())
        });
        if (value) {
            CKEDITOR.instances[id].setData(value);
        }
    }

    function autoSave(obj) {
        $.ajax({
            method: "POST",
            url: "{{ route('admin.ProjectUpdateComment') }}",
            beforeSend: () => $('.status').html(`<span class="text-success" id="status_mess">Đang lưu...</span>`),
            data: obj,
        }).done(function (res) {
            $('.status').html(`<span class="text-success" id="status_mess">${res}</span>`)
            $('#status_mess').hide();
            $("#status_mess").show().delay(4000).fadeOut();
        }).fail(function (error) {
            $('.status').html(`<span class="text-danger" id="status_mess">${error.responseJSON}</span>`)
            $('#status_mess').hide();
            $("#status_mess").show().delay(4000).fadeOut();
        });
    }

    $(document).ready(function () {
        var listEditor = {!! json_encode($listEditor, true) !!};
        listEditor.map(function (item) {
            makeCKEditor(item.id, item.value);
            @if($id_review == auth()->id())
                CKEDITOR.instances[item.id].on("change", function (event) {
                let self = this;
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    autoSave({id: item.id_obj, value: self.getData(), key: item.key});
                }, 1500);
            });
            @else
                CKEDITOR.config.readOnly = true;
            @endif
        });
    })
</script>
