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

    .red{
        background-color: red !important;
    }
    .blue{
        background-color: blue !important;
    }
    .bootstrap-select{
        width: 100% !important
    }
</style>

<div class="modal draggable fade in review-modal" id="task-request-detail" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable ">
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">Thêm mới yêu cầu công việc</h4>
            </div>
            <div class="modal-body">

                <form class="form-horizontal detail-form" action="" method="POST" id="task-request-form">
                    <input type="hidden" class="form-control hidden " name="id" value="{{ isset($taskRequest->id) ? $taskRequest->id : null }}">
                    <div class="col-md-12" style="padding-bottom: 1.5rem">
                        <label class="control-label col-md-3" for="">@lang('taskrequest::admin.task-request.assign-id')&nbsp;<sup class="text-red">*</sup>:</label>
                        <div class="col-md-9 select-abreason">
                            <select class='selectpicker show-tick show-menu-arrow' id="selectUser" data-size="5" name="assignID"
                                    data-live-search="true" data-live-search-placeholder="Search" {{'disabled}'}}>
                                    <option value="">@lang('taskrequest::admin.task-request.choose-user')</option>
                                    {!!  GenHtmlOption($users, 'id', 'FullName', isset($taskRequest->receiveUserID) ? $taskRequest->receiveUserID : '')!!}
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12" style="padding-bottom: 1.5rem">
                        <label class="control-label col-md-3" for="">@lang('taskrequest::admin.task-request.project') &nbsp;<sup class="text-red">*</sup>:</label>
                        <div class="col-md-9">
                            <select class="selectpicker show-tick show-menu-arrow" data-live-search="true" data-live-search-placeholder="Search" name="ProjectID" id="SelectProjectID"  data-size="5">
                                    <option value="">@lang('taskrequest::admin.task-request.choose-project')</option>
                                    {!!  GenHtmlOption($projects, 'id', 'NameVi', isset($taskRequest->projectID) ? $taskRequest->projectID : '')!!}
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12" style="padding-bottom: 1.5rem">
                        <label class="control-label col-md-3" for="">@lang('taskrequest::admin.task-request.sumary-content') &nbsp;<sup class="text-red">*</sup>:</label>
                        <div class="col-md-9">
                            <input  class="form-control"
                                    maxlength="150"
                                    id="text-sumarry"
                                    value="{{ isset($taskRequest->sumaryContent) ? $taskRequest->sumaryContent : null }}"
                                    name="sumaryContent">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <input id="editor0"
                               value="{{ isset($taskRequest->requestContent) ? $taskRequest->requestContent : null }}"
                               style="width:100%; height: 40%;" rows="30" name="Note">
                    </div>

                    <div class="col-md-12" style="padding-bottom: 1.5rem">
                        <label for="is_private" class="checkbox-inline">
                        <input type="checkbox" id="is_private" name="isPrivate" {{(isset($taskRequest->isPrivate) && $taskRequest->isPrivate == 1) ? "checked" : ""}}>
                            Để yêu cầu công việc ở chế độ riêng tư
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel" data-control="">@lang('admin.btnCancel')</button>
                <button type="button" class="btn btn-primary save-form" id="saveReport" data-control="">@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript" async>
    function makeCKEditor(id, value) {
        CKEDITOR.config.height = 200;
        CKEDITOR.config.width = 'auto';
        CKEDITOR.config.image_previewText = CKEDITOR.tools.repeat( ' ', 1 );
        // CKEDITOR.instances.editor_name.document.on('focus', function(event) {
        //         actionMain(0);
        // });
        var editor = CKEDITOR.replace( id, {
            // Remove unused plugins.
            removePlugins: 'bidi,dialogadvtab,div,filebrowser,flash,format,forms,horizontalrule,iframe,justify,liststyle,pagebreak,showborders,stylescombo,templates',
            // Width and height are not supported in the BBCode format, so object resizing is disabled.
            disableObjectResizing: true,
            // Define font sizes in percent values.
            fontSize_sizes: "50/50%;100/100%;120/120%;150/150%;200/200%;300/300%",
            toolbar: [
                [ 'Bold', 'Italic', 'Underline' ],
                [ 'FontSize' ],
                [ 'TextColor' ],
                [ 'NumberedList', 'BulletedList', '-', 'Blockquote' ],
                [ 'Maximize' ]
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
        editor.on( 'change', function( evt ) {
            // getData() returns CKEditor's HTML content.
            // console.log( 'Total bytes: ' + evt.editor.getData() );
            $('#editor0').val(evt.editor.getData())
        });
        if (value) {
            CKEDITOR.instances[id].setData(value);
            //editor.setData(value);
        }
    }

    $(document).ready(function(){
        $(".selectpicker").selectpicker();
        $('#saveReport').click(function (e) {
            e.preventDefault();
            data = $('#task-request-form').serializeArray();
            ajaxGetServerWithLoader("{{ route('admin.Insert') }}", 'POST', data, function (data) {
                if (typeof data.errors !== 'undefined') {
                    $('.loadajax').hide();
                    showErrors(data.errors);
                    return;
                }
                locationPage();
            });
        });

        makeCKEditor('editor0', $('#editor0').val());
    });

    $('.modal-dialog').draggable({
        handle: ".modal-header"
    });
</script>
