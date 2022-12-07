<div class="modal fade detail-modal" id="user-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-xs ui-draggable">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">@lang('event::admin.event.vote')</h4>
            </div>
            <div class="modal-body">
                <div class="save-errors"></div>
                <form class="form-horizontal detail-form">
                    @csrf
                    <div class="modal-body">
                        <div id="q-body">
                            <div class="question-border"><span class="q-title">{{ $question->Name }}</span>
                                <div class="q-content">
                                    <p>{!! $question->Question !!}</p>
                                </div>
                            </div>
                        </div>
                        <div id="a-body">
                            <div class="answer-border">
                                <span class="q-title a-title">@lang('event::admin.event.select_the_answer')</span>
                                <ul id="list-answer">
                                    @foreach($answers as $answer)
                                        <li>
                                            <input type="{{ $question->Type == 'SK001' ? 'radio' : 'checkbox' }}"
                                                     name="answer[]"
                                                    value="{{ $answer->id }}" {{ $answer->CheckAnswer ? 'checked' : '' }}>
                                            <label>{{ $answer->Answer }}</label>
                                        </li>
                                    @endforeach
                                </ul>
                                @if($question->Type=="SK003")
                                    <span class="btn btn-success">+</span>
                                    <span id="temp-data" style="display: none;">
                                        <input type="text" name="temp-data" id="temp-answer" value="">
                                        <span class="btn btn-info">@lang('admin.btnSave')</span>
                                        <span class="btn btn-danger" data-dismiss="input"
                                              id="cancel">@lang('admin.btnCancel')
                                        </span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="question" value="{{ $question->id }}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                        id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm save-form">@lang('admin.btnSave')</button>
            </div>
        </div>

    </div>
</div>
<div id="row-form-answer" style="display: none;">
    <li>
        <label>
            <input {{ $question->Type == 0 ? 'type=radio' : 'type=checkbox' }} name="answer[]" value="">
            <p></p>
        </label>
        <span class="toolEdit">
            <i class="fa fa-pencil btnAEdit"  aria-hidden="true"></i>
                        <i class="fa fa-times btnADel" aria-hidden="true"></i>
        </span>
    </li>
</div>
<style>

    .question-border {
        border: 1px solid #2e6da4;
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 10px;
    }

    .q-title {
        font-weight: bold;
        position: absolute;
        top: 5px;
        background: #fff;
        padding: 0px 3px;
    }

    .btn-success {
        margin-left: 14px;
        margin-bottom: 5px;
        font-size: 17px;
    }

    .btn-info {

        margin-bottom: 5px;
        font-size: 17px;
    }

    .btn-danger {

        margin-bottom: 5px;
        font-size: 17px;
    }

    .q-content {
        margin: 10px 0 0 15px;
    }

    #a-body ul {
        margin-top: 25px;
        list-style-type: none;
        padding-left: 15px;
    }

    #temp-answer {
        font-size: 1.5em;

    }

    #a-body label {
        /*width: 100%;*/
        width: 94%;
        font-weight: normal;
    }

    #a-body input[type=radio],
    #a-body input[type=checkbox] {
        float: left;
        margin-right: 5px;
    }

    .a-title {
        top: -11px;
    }

    .answer-border {
        position: relative;
        border: 1px solid #2e6da4;
        border-radius: 10px;
        padding: 10px;
        margin: 20px 0 10px 0;
    }

    #btnAddAnswer {
        cursor: pointer;
    }

    #btnAddAnswer i {
        font-size: 1.3em;
        color: red;
        margin-left: 14px;
        margin-bottom: 20px;
    }
</style>
<script>
    $(function () {
        $(".draggable").draggable();
        $('.btn-add-answer').click(function () {
            $('.row-answer').append($('#row-form-answer').html());
        });
        $('.btn-del-answer').click(function () {
            $(this).closest("div").remove();
        });
        $('.btn-success').click(function () {
            $('#temp-data').show();
            //$(this).hide('.btn-success');
        });

        $('.btn-danger').click(function () {
            $(this).closest('#temp-data').hide();
        });

        $('.btn-info').click(function () {
            var newAnswer = $("#temp-answer").val();
            if (isEmpty(newAnswer)) {
                alert('Câu trả lời không được để trống');
                return;
            }
            // console.log(newAnswer);
            $("#row-form-answer p").html(newAnswer);
            $("#row-form-answer input").val(';' + newAnswer);
            $('#list-answer').append($('#row-form-answer').html());
            $('.btn-success').show();
            $('#temp-data').hide();
            $("#temp-answer").val('');
        });

        $('.modal-dialog').draggable({
            handle: ".modal-header"
        });

    });
    $('.save-form').click(function () {
        var data = $('.detail-form').serializeArray();

        ajaxGetServerWithLoader("{{ route('admin.EventVote') }}", 'POST', data,
            function (data) {
                if (typeof data.errors !== 'undefined') {
                    showErrors(data.errors);
                    return;
                }

                locationPage();
            });

    });

</script>

