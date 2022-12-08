<div class="modal draggable fade in detail-modal" id="job-modal" role="dialog">
    <div class="modal-dialog modal-xs ui-draggable">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="">×</button>
                <h4 class="modal-title">@lang('admin.interview.add-recruitment')</h4>
            </div>
            <div class="modal-body">
                <div class="save-errors"></div>
                <div class="tab-content">
                    <form class="form-horizontal" action="" method="POST" id="job-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                @if(isset($jobInfo->id))
                                    <input type="text" class="form-control hidden" name="id" value="{{$jobInfo->id}}">
                                @endif
                                <div class="form-group">
                                    <label class="control-label col-xs-3" for="Name">@lang('admin.interview.name-job')&nbsp;<sup class="text-red">*</sup>:</label>
                                    <div class="col-xs-9">
                                        <input type="text" class="form-control" placeholder="Name" name="Name" maxlength="200" value="{{ isset($jobInfo->Name) ? $jobInfo->Name : null }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-xs-3" for="Name">@lang('admin.interview.depiction'):</label>
                                    <div class="col-xs-9">
                                        <textarea class="form-control" name="Description" rows="4">{{ isset($jobInfo->Description) ? $jobInfo->Description : null }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label" for="text">@lang('admin.interview.content_detail')&nbsp;<sup class="text-red">*</sup>:</label>
                                    <div class="col-xs-9">
                                        <textarea class="form-control" name="Content" id="content" rows="4">{{ isset($jobInfo->Content) ? $jobInfo->Content : null }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">@lang('admin.status') :</label>
                                    <div class="col-sm-9" style="text-align: left;">
                                        <input type="checkbox" {{ isset($jobInfo->Active) && $jobInfo->Active == 1 || !isset($jobInfo->Active) ? 'checked value="1"' : 'value="0"' }}
                                        data-toggle="toggle" id="toggle-active" data-on="Hoạt động" data-off="Không hoạt động" data-width="150" name="Active">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm" id="save">@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript" async>
    $(function () {
        $('#toggle-active').bootstrapToggle();
    });

    // Jquery draggable
    $('.modal-dialog').draggable({
        handle: ".modal-header"
    });

    $('#save').click(function () {
        data = $('#job-form').serializeArray();
        data.push({ name: 'Content', value: CKEDITOR.instances.content.getData() });
        ajaxGetServerWithLoader("{{ route('admin.InterviewJob') }}", 'POST', data, function (data) {
            if (typeof data.errors !== 'undefined') {
                showErrors(data.errors);
                return;
            }

            locationPage();
        });
    });

    //CK editor
    CKEDITOR.config.height = 200;
    CKEDITOR.config.width = 'auto';
    CKEDITOR.config.image_previewText = CKEDITOR.tools.repeat( ' ', 1 );
    CKEDITOR.replace( 'content', {

        // Remove unused plugins.
        removePlugins: 'bidi,dialogadvtab,div,filebrowser,flash,format,forms,horizontalrule,iframe,justify,liststyle,pagebreak,showborders,stylescombo,templates',
        // Width and height are not supported in the BBCode format, so object resizing is disabled.
        disableObjectResizing: true,
        // Define font sizes in percent values.
        fontSize_sizes: "50/50%;100/100%;120/120%;150/150%;200/200%;300/300%",
        toolbar: [
            [ 'Source', '-', 'Save', 'NewPage', '-', 'Undo', 'Redo' ],
            [ 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat' ],
            [ 'Link', 'Unlink', 'Image', 'Smiley', 'SpecialChar' ],
            '/',
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
    data = `{!!  isset($jobInfo->Content) ? $jobInfo->Content : ''  !!}`;
    CKEDITOR.instances.content.setData(data);
</script>

