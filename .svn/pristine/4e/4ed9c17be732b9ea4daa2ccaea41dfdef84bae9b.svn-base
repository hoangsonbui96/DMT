<div class="modal draggable fade in detail-modal" id="user-info" role="dialog">
    <div class="modal-dialog modal-lg ui-draggable">

        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">@lang('event::admin.room.add_new_room')</h4>
            </div>
            <div class="modal-body">

                <div class="save-errors"></div>
                <form class="form-horizontal detail-form">
                    @csrf
                    @if(isset($itemInfo->id))
                        <input type="hidden" name="id" value="{{ $itemInfo->id }}">
                    @endif
                    <div class="form-group">
                        <label class="control-label col-sm-3">@lang('event::admin.event.name') &nbsp;<sup class="text-red">*</sup>:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="Name" maxlength="200" placeholder="@lang('event::admin.event.name')"
                                   value="{{ isset($itemInfo->Name) ? $itemInfo->Name : null }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3" for="sDate">@lang('admin.times') &nbsp;<sup class="text-red">*</sup>:</label>
                        <div class="col-sm-9">
                            <div class="input-group marginBot10 date" id="sDate">
                                <input type="text" class="form-control" id="sDate-input" name="SDate" autocomplete="off" placeholder="@lang('admin.startDate')"
                                       value="{{ isset($itemInfo->SDate) ? \Carbon\Carbon::parse( $itemInfo->SDate)->format(FOMAT_DISPLAY_DAY) : null }}">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            <div class="input-group marginBot10 date" id="eDate">
                                <input type="text" class="form-control" id="eDate-input" autocomplete="off" name="EDate" placeholder="@lang('admin.endDate')"
                                       value="{{ isset($itemInfo->EDate) ? \Carbon\Carbon::parse( $itemInfo->EDate)->format(FOMAT_DISPLAY_DAY) : null }}">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3" for="desc">@lang('event::admin.answer-detail.question-content')&nbsp;<sup class="text-red">*</sup>:</label>
                        <div class="col-sm-9">
                            <textarea id="editor" style="width:100%;height: 100%;" rows="30" name="Question"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3" for="desc">@lang('event::admin.event.ansers'):</label>
                        <div class="col-sm-9">
                            <span class="btn btn-info btn-add-answer">@lang('event::admin.event.add_ansers')</span>
                            <div class="row-answer" style="margin: 10px 15px;">
                                @if(isset($itemInfo->answers))
                                @foreach($itemInfo->answers as $answer)
                                <div class="answer-form row">

                                    <input type="text" class="form-control" name="Answer[{{ $answer->id }}][]" maxlength="200" value="{{ $answer->Answer }}">

                                    <span class="btn btn-danger btn-del-answer">-</span>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="desc">@lang('event::admin.question-detail.Kind_of_question'):</label>
                        <div class="col-sm-9">
                            <select class="selectpicker show-tick show-menu-arrow" data-actions-box="true" data-size="5" name="Type" data-live-search-placeholder="Search" id="select-type" tabindex="-98">
                                @foreach($typeQuestion as $type)
                                    @if($type->DataValue=='SK002' || $type->DataValue=='SK001')
                                        <option value="{{ $type->DataValue }}" {{ isset($itemInfo->Type) && ($type->DataValue == $itemInfo->Type) ? 'selected' : '' }}>{{ $type->Name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="desc">@lang('event::admin.question-detail.Related_questions'):</label>
                        <div class="col-sm-9">
                            <select class="selectpicker show-tick show-menu-arrow" data-actions-box="true" data-size="5" name="QLink[]" multiple="" data-live-search-placeholder="Search" id="select-type" tabindex="-98">
                                @foreach($relateQuestions as $question)
                                <option value="{{ $question->id }}" {{ isset($itemInfo->QLink) && in_array($question->id, $itemInfo->QLink) ? 'selected' : '' }}>{{ $question->Name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="desc">@lang('admin.status'):</label>
                        <div class="col-sm-9">
                            <label class="switch">
{{--                                <input type="checkbox" name="Status" {{ isset($itemInfo->Status) && $itemInfo->Status ? 'checked' : null }}>--}}
                                <input type="checkbox" {{ isset($itemInfo->Status) && $itemInfo->Status == 1 || !isset($itemInfo->Status) ? 'checked value="1"' : 'value="0"' }}
                                data-toggle="toggle" id="toggle-one" data-on="Hoạt động" data-off="Không hoạt động" data-width="150" name="Status">
                                <span class="slider round"></span>
                            </label>
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
<div id="row-form-answer" style="display: none;margin:10px 15px;">
    <div class="answer-form row">
        <input type="text" class="form-control"  name="Answer[0][]" maxlength="200" value="">
        <span class="btn btn-danger btn-del-answer">-</span>
    </div>
</div>
<style>
    .answer-form input{
        float: left;
        width: 90%;
        margin-right: 20px;
    }
    .answer-form{
        margin-top: 10px;
    }
    .datetime_txtBox{
        width: 40%;
        float: left;
    }
    .datetime_txtBox:nth-child(2){
        float: right;
    }
</style>
<script src="{{ asset('js/ckeditor/ckeditor.js') }}" defer></script>
<script>
    $('.save-form').click(function () {
        data =$('.detail-form').serializeArray();
        data.push({ name: 'Question', value: CKEDITOR.instances.editor.getData() });

        ajaxGetServerWithLoader("{{ route('admin.Events') }}",'POST', data,function (data) {
            if (typeof data.errors !== 'undefined') {
                showErrors(data.errors);
                return;
            }

            locationPage();
        });
    });
    $(function () {
        SetDatePicker($('#sDate,#eDate'));
        $('#toggle-one').bootstrapToggle();
        // Jquery draggable (cho phép di chuyển popup)
        $('.modal-dialog').draggable({
            handle: ".modal-header"
        });

        $(".selectpicker").selectpicker();

    });

    CKEDITOR.config.height = 200;
    CKEDITOR.config.width = 'auto';
    CKEDITOR.config.image_previewText = CKEDITOR.tools.repeat( ' ', 1 );
    CKEDITOR.replace( 'editor', {

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

