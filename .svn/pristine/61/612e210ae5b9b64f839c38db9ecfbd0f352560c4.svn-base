<style>
    .table.table-bordered th, .table.table-bordered td {
        border: 1px solid #bdb9b9 !important;
        text-align: center;
        vertical-align: middle !important;
        background-color: #fff;
    }
    .SummaryMonth .table.table-bordered tr th {
        background-color: #dbeef4;
    }
    .tbl-dReport .table.table-bordered tr th {
        background-color: #c6e2ff;
    }
    .box-content {
        max-height: 25rem;
        overflow: auto;
        padding: 5px;
        border: 1px solid #b0b0b0;
        border-radius: 3px;
        margin-bottom: 10px;
    }
</style>
<div class="modal draggable fade in review-modal" id="task-request-respone" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable ">
        <div class="modal-content drag">
            <div class="modal-body">
                <form class="form-horizontal" method="POST" id="requset-task-form">
                    <div class="container">
                        <input type="hidden" class="form-control hidden " name="id" value="{{$taskRequest->id}}">
                        <div class="row col-sm-9 col-xs-12" style="word-break: break-word">
                            <div style="padding-bottom: 15px">
                                <div class="row" style="display: flex; justify-content: center; flex-direction:column; align-items: center;">
                                    <h2>{{$taskRequest->sumaryContent}}</h2>
                                </div>
                            </div>
                            <div class="">
                                <p>
                                    <label for="" class="control-label">Người yêu cầu:&nbsp;</label>
                                    {{ \App\User::find($taskRequest->requestUserID)->FullName }}.
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                    <label for="" class="control-label">Người nhận yêu cầu:&nbsp;</label>
                                    {{ \App\User::find($taskRequest->receiveUserID)->FullName }}
                                </p>
                                <p>
                                    <label for="" class="control-label">Dự án:&nbsp;</label>
                                    {{ \App\Project::find($taskRequest->projectID)->NameVi }}
                                </p>
                                <p>
                                    <label for="" class="control-label">Yêu cầu lúc:&nbsp;</label>
                                    {{ FomatDateDisplay($taskRequest->requestTime, FOMAT_DISPLAY_DATE_TIME)}}
                                </p>
                            </div>
                            <div class="">
                                <label for="" class="text-bold" style="">Nội dung yêu cầu:</label>
                                <div class="box-content">
                                    {!! $taskRequest->requestContent !!}
                                </div>
                            </div>
                            <div class="">
                                <label for="" class="text-bold" style="">Nội dung phản hồi:</label>
                                @if(isset($taskRequest->responseContent))
                                    <div class="box-content">
                                        {!! $taskRequest->responseContent !!}
                                    </div>
                                    <p>
                                        <label class="control-label">Phản hồi lúc:&nbsp;</label>
                                        {{ FomatDateDisplay($taskRequest->responseTime, FOMAT_DISPLAY_DATE_TIME)}}
                                    </p>
                                @else
                                    @if($taskRequest->needResponse)
                                        <input id="editor1"
                                               value="{{ isset($taskRequest->responseContent) ? $taskRequest->responseContent : null }}"
                                               style="width:100%; height: 40%;" name="NoteRespone">
                                    @else
                                        <td class="center-important ">
                                            <span class="label label-danger data-toggle=">Chưa Phản Hồi</span>
                                        </td>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel"
                        data-control="">@lang('admin.btnCancel')</button>
                @if($taskRequest->needResponse)
                    <button type="button" class="btn btn-primary save-form" id="saveReport"
                            data-control="">@lang('admin.btnSave')</button>
                @endif
            </div>
        </div>
    </div>
</div>


<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript" async>
    var updateTitle = "";
    $(document).ready(function () {
        $('#saveReport').click(function (e) {
            e.preventDefault();
            data = $('#requset-task-form').serializeArray();

            if (CKEDITOR.instances.editor1.document.getBody().getText().trim() == '') {
                showErrors('Nội dung phản hồi không được để chống');
                return;
            }

            showConfirm('Có muốn lưu hay không?', function () {
                ajaxGetServerWithLoader("{{ route('admin.Insert') }}", 'POST', data, function (data) {
                    if (typeof data.errors !== 'undefined') {
                        $('.loadajax').hide();
                        showErrors(data.errors);
                        return;
                    }
                   locationPage();
                });
            });
        });
    });

    makeCKEditor('editor1', '');

    function makeCKEditor(id, value) {
        CKEDITOR.config.height = 200;
        CKEDITOR.config.width = 'auto';
        CKEDITOR.config.image_previewText = CKEDITOR.tools.repeat(' ', 1);
        // CKEDITOR.config.allowedContent = true;
        // CKEDITOR.config.removeFormatAttributes = '';
        // CKEDITOR.config.ignoreEmptyParagraph = false;
        // CKEDITOR.instances.editor_name.document.on('focus', function(event) {
        //         actionMain(0);
        // });
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
            // getData() returns CKEditor's HTML content.
            // console.log( 'Total bytes: ' + evt.editor.getData() );
            $('#editor1').val(evt.editor.getData())
        });
        if (value) {
            CKEDITOR.instances[id].setData(value);
            //editor.setData(value);
        }
    }
</script>
