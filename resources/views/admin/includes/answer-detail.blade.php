<div class="modal draggable fade in detail-modal" id="user-info" role="dialog">
    <div class="modal-dialog modal-lg ui-draggable">

        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">

                <div class="save-errors"></div>
                <form class="form-horizontal detail-form">
                    @csrf
                    @if(isset($itemInfo->id))
                        <input type="hidden" name="id" value="{{ $itemInfo->id }}">
                    @endif


                    <div class="form-group">
                        <label class="control-label col-sm-3" for="desc">@lang('admin.answer-detail.question-content'):</label>
                        <div class="col-sm-9">
                            <textarea id="editor" style="width:100%;height: 100%;" rows="30" name="Answer"></textarea>
                        </div>
                    </div>


                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm save-form">@lang('admin.btnSave')</button>
            </div>
        </div>

    </div>
</div>


<script>
    $('.save-form').click(function () {

        data =$('.detail-form').serializeArray();

        data.push({ name: 'Question', value: CKEDITOR.instances.editor.getData() });

        $.ajax({
            url: "{{ route('admin.Events') }}",
            type: 'post',
            data: data,
            success: function (data) {

                if (typeof data.errors !== 'undefined'){
                    showErrors(data.errors);

                }else{
                    // console.log(data);
                    window.location.reload();
                }

            },
            fail: function (error) {
                console.log(error);
            }
        })
    });


    CKEDITOR.config.height = 200;
    CKEDITOR.config.width = 'auto';
    CKEDITOR.config.image_previewText = CKEDITOR.tools.repeat( ' ', 1 );
    CKEDITOR.replace( '
    ', {

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
    data = `{!!  isset($itemInfo->Question) ? $itemInfo->Question : ''  !!}`;
    CKEDITOR.instances.editor.setData(data);

    $('.btn-add-answer').click(function () {
        $('.row-answer').append($('#row-form-answer').html());
        $('.btn-del-answer').click(function () {
            $(this).closest("div").remove();
        });
    });
    $('.btn-del-answer').click(function () {
        $(this).closest("div").remove();
    });
</script>

