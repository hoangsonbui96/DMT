<style>
    .nav-tabs{
        margin-bottom: 10px;
    }
</style>
<div class="modal draggable fade in detail-modal" id="daily-modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable">

        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" action="" method="POST" id="daily-form">
                    <input type="hidden" class="form-control hidden " id="dReport_id">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="active"><a href="#dReport_1" data-toggle="tab">Work 1</a><span> <i class="fa fa-times" aria-hidden="true"></i> </span>
                        </li>
                        <li><a href="#" id="add-work"><i class="fa fa-plus" aria-hidden="true"></i></a></li>
                    </ul>
                    <div class="save-errors"></div>
                    <div class="tab-content">
                        <input type="hidden" name="reqID" value="" id="req-one">
                        <div id="dReport_1" class="tab-pane fade in active">

                            @csrf
                            <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                    @if(isset($dailyInfo->id))
                                        <input type="hidden" name="id[]" value="{{ $dailyInfo->id }}" id="id">
                                    @endif
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="Date">@lang('admin.working-day') &nbsp;<sup class="text-red">*</sup>:</label>
                                        <div class="col-sm-8">
                                            <div class="input-group date" id="sDate">
                                                <input type="text" class="form-control date-input" id="modal-date-input" name="Date[]" placeholder="@lang('admin.working-day')" value="{{
                                                    isset($dailyInfo->Date) ? FomatDateDisplay($dailyInfo->Date, FOMAT_DISPLAY_DAY) : ''
                                                }}">
                                                <div class="input-group-addon">
                                                    <span class="glyphicon glyphicon-th"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="Project">@lang('admin.daily.Project') &nbsp;<sup class="text-red">*</sup>:</label>
                                        <div class="col-sm-8 project" >
                                            <select class="selectpicker show-tick show-menu-arrow sl-user select_project" id="select-project" data-size="5" name="ProjectID[]" data-live-search="true"
                                                    data-live-search-placeholder="Search" data-width="100%" data-size="5" >
                                                <option value="">[@lang('admin.daily.chooseProject')]</option>
                                                {!!
                                                    GenHtmlOption($projects, 'id', 'NameVi', isset($dailyInfo->ProjectID) ? $dailyInfo->ProjectID : '')
                                                !!}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="screen_name">@lang('admin.daily.Screen_Name'):</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control screen_name" id="" placeholder="Tên màn hình" name="ScreenName[]" maxlength="100">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="Type Of Work">@lang('admin.daily.Type_Of_Work') &nbsp;<sup class="text-red">*</sup>:</label>
                                        <div class="col-sm-8 type_work" id="">
                                            <select class="selectpicker show-tick show-menu-arrow sl-user select_type_work" id="type_work" data-size="5" name="TypeWork[]" data-live-search="true"
                                                    data-live-search-placeholder="Search" data-width="100%" data-size="5" >
                                                <option value="">[@lang('admin.daily.chooseWorkType')]</option>
                                                {!!
                                                    GenHtmlOption($masterDatas, 'DataValue', 'Name',
                                                        isset($dailyInfo->TypeWork) ? $dailyInfo->TypeWork : '')
                                                !!}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="note">@lang('admin.contents')&nbsp;<sup class="text-red">*</sup>:</label>
                                        <div class="col-sm-8" maxlength="200">
                                            <textarea class="form-control" rows="3" id="contents" name="Contents[]" placeholder="Nội dung">{{
                                                isset($dailyInfo->Contents) ? $dailyInfo->Contents : null
                                            }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="perAddress">@lang('admin.daily.time_working')&nbsp;<sup class="text-red">*</sup>:</label>
                                        <div class="col-sm-8 div-work-time">
                                            <input type="text" class="form-control working_time" placeholder="@lang('admin.daily.time_working')" name="WorkingTime[]"
                                                   value="{{ isset($dailyInfo->WorkingTime) ? $dailyInfo->WorkingTime : null }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="curAddress">@lang('admin.daily.progressing')&nbsp;<sup class="text-red">*</sup>:</label>
                                        <div class="col-sm-8 div-progressing">
                                            <input type="text" class="form-control progressing" placeholder="Tiến độ - (80.5%)" name="Progressing[]"
                                                   value="{{ isset($dailyInfo->Progressing) ? $dailyInfo->Progressing : null }}">
                                        </div>
                                    </div>
{{--                                    <div class="form-group">--}}
{{--                                        <label class="control-label col-sm-4" for="Delay">Giờ trễ (h) :</label>--}}
{{--                                        <div class="col-sm-8 div-delay">--}}
{{--                                            <input type="text" class="form-control delay" placeholder="Delay - (2.5h) " name="Delay[]" max="10"--}}
{{--                                                   value="{{ isset($dailyInfo->Delay) ? $dailyInfo->Delay : null }}">--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="note">@lang('admin.note') :</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control note" rows="5" id="note" maxlength="200" name="Note[]" placeholder="@lang('admin.note')">{{
                                             isset($dailyInfo->Note) ? $dailyInfo->Note : null
                                             }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
<script type="text/javascript" async>
    var CONTENT_FORM = $( ".tab-pane" ).html();
    var COUNT_TAB_WORK = 1;

    $('.selectpicker').selectpicker();

    SetDatePicker($('#sDate'), {
        format: FOMAT_DATE.toLowerCase(),
        endDate: new Date(),
        enableOnReadonly: true,
        todayHighlight: true,
        multidate: true,
    });

    $('#save').click(function () {
        ajaxGetServerWithLoader("{{ route('admin.DailyInfoOne') }}", 'POST', $('#daily-form').serializeArray(), function (data) {
            if (typeof data.errors !== 'undefined'){
                showErrors(data.errors);
                return;
            }

            locationPage();
        });
    });

    $('.modal').modal({ keyboard: false,
        show: true
    });
    // Jquery draggable
    $('.modal-dialog').draggable({
        handle: ".modal-header"
    });
    $('input').attr('autocomplete', 'off');
    $('#add-work').click(function () {
        COUNT_TAB_WORK = COUNT_TAB_WORK + 1;
        var id = $('.nav-tabs').children().length;
        if (id <= 9) {
            var tabId = "dReport_" + id;

            $(this).closest('li').before('<li><a href="#'+ tabId + '" data-toggle="tab">Work '+COUNT_TAB_WORK+'</a><span><i class="fa fa-times" aria-hidden="true"></i></span></li>');
            $('.tab-content').append('<div class="tab-pane tab-work fade" id="'+ tabId +'">'+ CONTENT_FORM +'</div>');

            $('.select_project, .select_type_work').selectpicker();
            $(".nav-tabs li:nth-child("+ id +") a").click();

            addAttrForItem(tabId);
            $("#"+tabId+"_date-input").val('');
            var dateID = "#"+tabId+"_sDate";
            SetDatePicker($(dateID), {
                format: FOMAT_DATE.toLowerCase(),
                endDate: new Date(),
                enableOnReadonly: true,
                todayHighlight: true,
                multidate: true,
            });
            $('input').attr('autocomplete', 'off');
        }
    });
    function addAttrForItem(parentID){
        $("#daily-modal #"+parentID+" .date").attr("id",parentID+"_sDate");
        $("#daily-modal #"+parentID+" .date-input").attr("id",parentID+"_date-input");
        $("#daily-modal #"+parentID+" .select_project").attr("id",parentID+"_select_project");
        $("#daily-modal #"+parentID+" .screen_name").attr("id",parentID+"_screen_name");
        $("#daily-modal #"+parentID+" .select_type_work").attr("id",parentID+"_select_type_work");
        $("#daily-modal #"+parentID+" .note").attr("id",parentID+"_note");
        $("#daily-modal #"+parentID+" .working_time").attr("id",parentID+"_working_time");
        $("#daily-modal #"+parentID+" .progressing").attr("id",parentID+"_progressing");
        $("#daily-modal #"+parentID+" .delay").attr("id",parentID+"_delay");
        $("#daily-modal #"+parentID+" .content").attr("id",parentID+"_content");
    }
    $(".nav-tabs")
        .on("click", "span", function () {
            var anchor = $(this).siblings('a');
            $(anchor.attr('href')).remove();
            $(this).parent().remove();
            $(".nav-tabs li").children('a').first().click();
        });
</script>

