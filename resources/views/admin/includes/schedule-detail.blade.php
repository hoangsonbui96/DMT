<div class="modal draggable fade in detail-modal" id="schedule-info" role="dialog">
    <div class="modal-dialog modal-xs ui-draggable">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="save-errors"></div>
                <form class="form-horizontal" action="" method="POST" id="schedule-form">
                    @csrf
                    @if(isset($candidateInfo->id))
                        <input type="hidden" id="id" name="id" value="{{$candidateInfo->id}}">
                    @endif
                    <div class="form-group">
                        <label class="control-label col-xs-3" for="Name">@lang('admin.interview.name-job'):</label>
                        <div class="col-xs-9">
                            <select class='selectpicker show-tick show-menu-arrow' id='select-job' name="JobID" data-size="5">
                                <option value="" selected>@lang('admin.user.please_select')</option>
                                {!!  GenHtmlOption($jobs, 'id', 'Name', isset($candidateInfo->JobID) ? $candidateInfo->JobID : '')!!}
                           </select>
                       </div>
                    </div>
{{--                    @if(!isset($candidateInfo->id))--}}
                    <div class="modeInsert">
{{--                        <div class="form-group">--}}
{{--                           <div class="col-xs-3"></div>--}}
{{--                           <div class="col-xs-9" id="add-candidate">--}}
{{--                               <span><i class="action-col fa fa-plus-circle" aria-hidden="true"></i></span>--}}
{{--                           </div>--}}
{{--                        </div>--}}
                        <div class="form-group" id="candidate-item">
                            <label class="control-label col-xs-3" for="Name">@lang('admin.interview.name-inter'):</label>
                            <div class="col-xs-9 classAbc" style="padding: 0px;">
                                <div class="col-xs-6">
                                    <select class='selectpicker show-tick show-menu-arrow sl-candidate' id='select-candidate'
                                        name="CandidateID" data-width="100%">
                                        @if(isset($candidateInfo->id))
                                            {!!  GenHtmlOption($Candidate, 'id', 'FullName', isset($candidateInfo->CandidateID) ? $candidateInfo->CandidateID : '')!!}
                                        @endif
                                    </select>
                                </div>
                                <div class="col-xs-6">
                                    <div class='input-group date dateInterview'>
                                        <input type="text" class="form-control dateInterview-input" id="interviewDate"
                                           name="InterviewDate" placeholder="Ngày phỏng vấn" autocomplete="off"
                                               value="{{isset($candidateInfo->InterviewDate) ? FomatDateDisplay($candidateInfo->InterviewDate, FOMAT_DISPLAY_DATE_TIME)  : ''}}">
                                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    </div>
                                </div>
{{--                                    <div class="col-xs-2" id="control-remove">--}}
{{--                                        <div class="action-col remove-candidateItem">--}}
{{--                                            <i class="fa fa-times" aria-hidden="true"></i>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                            </div>
                    </div>
{{--                    @else--}}
{{--                    <div class="modeUpdate">--}}
{{--                        <div class="form-group">--}}
{{--                            <label class="control-label col-xs-3" for="Name">Tên ứng viên:</label>--}}
{{--                            <div class="col-xs-9">--}}
{{--                                <div class="form-group">--}}
{{--                                    <div class="col-xs-6">--}}
{{--                                        <select class='selectpicker show-tick show-menu-arrow sl-candidate' id='select-candidate' data-width="100%">--}}
{{--                                        </select>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-xs-6">--}}
{{--                                        <div class='input-group date'>--}}
{{--                                            <input type="text" class="form-control dateInterview-input" id="" placeholder="Date interview">--}}
{{--                                            <span class="input-group-addon">--}}
{{--                                                <span class="glyphicon glyphicon-calendar"></span>--}}
{{--                                            </span>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    @endif--}}
                    <div class="form-group">
                        <label class="control-label col-xs-3" for="Name">@lang('admin.note'):</label>
                        <div class="col-xs-9">
                            <textarea class="form-control" id="note" rows="4" name="Note">{{ isset($candidateInfo->Note) ? $candidateInfo->Note : '' }}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-3" for="Name">@lang('admin.interview.Interviewer'):</label>
                        <div class="col-xs-9">
                            <select class='selectpicker show-tick show-menu-arrow' data-actions-box="true" data-live-search="true" data-live-search-placeholder="Search" data-size="5" id='select-user-interview' name="UserInterviews[]" multiple>
                                {!!  GenHtmlOption($userInters, 'id', 'FullName', isset($candidateInfo->UserInterviews) ? $candidateInfo->UserInterviews : '')!!}
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm" id="save">@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>
<div class="Temp_Candidate" style="display: none;">
    <div class="" id="candidate-item">
        <div class="col-xs-5">
            <select class='selectpicker show-tick show-menu-arrow sl-candidate' id='select-candidate' name="CandidateID[]" data-width="100%">
            </select>
        </div>
        <div class="col-xs-5">
            <div class='input-group date dateInterview'>
                <input type="text" class="form-control dateInterview-input" id="interviewDate" name="InterviewDate[]" placeholder="Ngày phỏng vấn" autocomplete="off">
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
        <div class="col-xs-2" id="control-remove">
            <div class="action-col remove-candidateItem">
                <i class="fa fa-times" aria-hidden="true"></i>
            </div>
        </div>
    </div>
</div>
<script !src="">
    $(function () {
        $(".selectpicker").selectpicker();
        SetDateTimePicker($('.date'),{
            format: 'DD/MM/YYYY HH:mm',
            stepping: 5,
        });

        //click save form
        $('#save').click(function () {
            ajaxGetServerWithLoader("/admin/interview-storeSchedule", 'POST', $('#schedule-form').serializeArray(),
                function (data) {
                    if (typeof data.errors !== 'undefined'){
                        showErrors(data.errors);
                        return;
                    }

                    locationPage();
                }
            );
        });
    });

    var jobId = $('#select-job option:selected').val();
    $('#select-job').change(function() {
        jobId =$('#select-job option:selected').val();
        GetUserOfJob(jobId);
    });

    //func lấy danh sách người ứng tuyển theo công việc
    function GetUserOfJob(jobId) {
        $.ajax({
            url: '/admin/getUser/'+jobId,
            success: function (data) {
                if(data != ''){
                    $('.modeInsert #select-candidate').html('');
                    var option = '';
                    $.each(data.candidates, function(i, e) {
                        option += `<option value="${e['id']}">${e['FullName']}</option>`;
                    });
                    $('.modeInsert #select-candidate').append(option).selectpicker('refresh');
                }
            },
            fail: function (error) {
                console.log(error);
            }
        })
    }

    // add candidate
    var a = 0;
    $('#add-candidate span i').click(function(event) {
        var HTML_INTERVIEW = $('.Temp_Candidate').html();
        var a = $('.modeInsert #candidate-item').length;
        var b = $('.modeInsert #candidate-item  select#select-candidate:first option').length;
        if(a < b) {
            $('.modeInsert .classAbc').append(HTML_INTERVIEW);
            var id = $('#select-job option:selected').val();
            GetUserOfJob(id);
        }
        changeCandidateItem();
        SetDateTimePicker($('.dateInterview'),{format: 'DD/MM/YYYY HH:mm'});
    });

    function changeCandidateItem() {
        $('#candidate-item .remove-candidateItem i').click(function(event) {
            var a = $('.modeInsert #candidate-item').length;
            if(a > 1) {
                $(this).parents().parents('#candidate-item').remove();
            }
        });
        $('.Temp_Candidate #candidate-item .remove-candidateItem i').click(function(event) {
            var a = $('.Temp_Candidate #candidate-item').length;
            if(a > 1) {
                $(this).parents().parents('#candidate-item').remove();
            }
        });
    }
    // Jquery draggable
    $('.modal-dialog').draggable({
        handle: ".modal-header"
    });

</script>

