<style>
    .nav-tabs {
        margin-bottom: 10px;
    }

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
<div class="modal draggable fade in detail-modal" id="daily-modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="">×</button>
                <h4 class="modal-title">@lang('admin.add-absence')</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" action="" method="POST" id="daily-form">
                    <input type="hidden" class="form-control hidden " id="dReport_id">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="active"><a href="#dReport_1" data-toggle="tab">Work 1</a><span>
                                @if(!isset($dailyInfo->id))<i class="fa fa-times" aria-hidden="true"></i>@endif </span>
                        </li>
                        @if(!isset($dailyInfo->id))
                        <li><a href="#" id="add-work"><i class="fa fa-plus" aria-hidden="true"></i></a></li>
                        @endif
                    </ul>
                    <div class="save-errors"></div>
                    <div class="tab-content">
                        <input type="hidden" name="reqID" value="" id="req-id">
                        <div id="dReport_1" class="tab-pane fade in active">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                    @if(isset($dailyInfo->id))
                                    <input type="hidden" name="id[]" value="{{$dailyInfo->id}}" id="id">
                                    <input type="hidden" name="UserID" value="{{$dailyInfo->UserID}}">
                                    @endif
                                    <div class="form-group">
                                        <label class="control-label col-sm-4"
                                            for="Date">@lang('admin.working-day')&nbsp;<sup
                                                class="text-red">*</sup>:</label>
                                        <div class="col-sm-8">
                                            <div class="input-group date" id="sDate">
                                                <input @if(isset($isOwner) && $isOwner !=1 || (isset($dailyInfo->Status)
                                                && $dailyInfo->Status==2))
                                                disabled @endif type="text" class="form-control date-input"
                                                name="Date[]" placeholder="@lang('admin.working-day')"
                                                autocomplete="off">
                                                <div class="input-group-addon">
                                                    <span class="glyphicon glyphicon-th"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4"
                                            for="Project">@lang('admin.daily.Project')&nbsp;<sup
                                                class="text-red">*</sup>:</label>
                                        <div class="col-sm-8 project">
                                            <select @if(isset($isOwner) && $isOwner !=1 || (isset($dailyInfo->Status) &&
                                                $dailyInfo->Status==2))
                                                disabled @endif class="selectpicker show-tick show-menu-arrow sl-user
                                                select_project" id="" data-size="5" name="ProjectID[]"
                                                data-live-search="true" data-live-search-placeholder="Search"
                                                data-width="100%" data-size="5" >
                                                <option value="">@lang('admin.daily.chooseProject')</option>
                                                @if(isset($dailyInfo->id))
                                                {!!
                                                GenHtmlOption($all_project, 'id', 'NameVi', isset($dailyInfo->ProjectID)
                                                ? $dailyInfo->ProjectID : '')
                                                !!}
                                                @else
                                                {!!
                                                GenHtmlOption($projects, 'id', 'NameVi', isset($dailyInfo->ProjectID) ?
                                                $dailyInfo->ProjectID : '')
                                                !!}
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4"
                                            for="screen_name">@lang('admin.daily.Screen_Name')/chức năng&nbsp;<sup
                                                class="text-red">*</sup>:</label>
                                        <div class="col-sm-8">
                                            <input @if(isset($isOwner) && $isOwner !=1 || (isset($dailyInfo->Status) &&
                                            $dailyInfo->Status==2))
                                            disabled @endif type="text" class="form-control screen_name" id=""
                                            placeholder="@lang('admin.daily.Screen_Name')/chức năng" name="ScreenName[]"
                                            maxlength="100" value="{{ isset($dailyInfo->ScreenName) ?
                                            $dailyInfo->ScreenName : null }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4"
                                            for="Type Of Work">@lang('admin.daily.Type_Of_Work')&nbsp;<sup
                                                class="text-red">*</sup>:</label>
                                        <div class="col-sm-8 type_work" id="">
                                            <select @if(isset($isOwner) && $isOwner !=1 || (isset($dailyInfo->Status) &&
                                                $dailyInfo->Status==2))
                                                disabled @endif class="selectpicker show-tick show-menu-arrow sl-user
                                                select_type_work" id="" data-size="5" name="TypeWork[]"
                                                data-live-search="true" data-live-search-placeholder="Search"
                                                data-width="100%" data-size="5" >
                                                <option value="">@lang('admin.daily.chooseWorkType')</option>
                                                {!!
                                                GenHtmlOption($masterDatas, 'DataValue', 'Name',
                                                isset($dailyInfo->TypeWork) ? $dailyInfo->TypeWork : '')
                                                !!}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="content">@lang('admin.contents')
                                            &nbsp;<sup class="text-red">*</sup>:</label>
                                        <div class="col-sm-8">
                                            <textarea @if(isset($isOwner) && $isOwner !=1 ||
                                                (isset($dailyInfo->Status) && $dailyInfo->Status==2)) disabled @endif class="form-control" rows="3" id="" name="Contents[]" placeholder="@lang('admin.contents')">{{ isset($dailyInfo->Contents) ? $dailyInfo->Contents : null }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label col-sm-4"
                                            for="perAddress">@lang('admin.daily.time_working') &nbsp;<sup
                                                class="text-red">*</sup>:</label>
                                        <div class="col-sm-8 div-work-time">
                                            <input @if(isset($isOwner) && $isOwner !=1 || (isset($dailyInfo->Status) &&
                                            $dailyInfo->Status==2))
                                            disabled @endif type="number" class="form-control working_time" id=""
                                            placeholder="Thời gian làm việc" name="WorkingTime[]"
                                            value="{{ isset($dailyInfo->WorkingTime) ? $dailyInfo->WorkingTime : null
                                            }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4"
                                            for="curAddress">@lang('admin.daily.progressing') &nbsp;<sup
                                                class="text-red">*</sup>:</label>
                                        <div class="col-sm-8 div-progressing">
                                            <input @if(isset($isOwner) && $isOwner !=1 || (isset($dailyInfo->Status) &&
                                            $dailyInfo->Status==2))
                                            disabled @endif type="number" class="form-control progressing" id=""
                                            placeholder="Tiến độ - (80.5%)" name="Progressing[]"
                                            value="{{ isset($dailyInfo->Progressing) ?
                                            number_format($dailyInfo->Progressing, 2, '.', '') : null }}">
                                        </div>
                                    </div>
                                    {{-- <div class="form-group">--}}
                                        {{-- <label class="control-label col-sm-4" for="Delay">Giờ trễ (h) :</label>--}}
                                        {{-- <div class="col-sm-8 div-delay">--}}
                                            {{-- <input type="text" class="form-control delay"
                                                placeholder="Delay - (2.5h) " name="Delay[]" max="10" --}} {{--
                                                value="{{ isset($dailyInfo->Delay) ? $dailyInfo->Delay : null }}">--}}
                                            {{-- </div>--}}
                                        {{-- </div>--}}
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="note">@lang('admin.note') :</label>
                                        <div class="col-sm-8">
                                            <textarea @if(isset($isOwner) && $isOwner !=1 ||
                                                (isset($dailyInfo->Status) && $dailyInfo->Status==2)) disabled @endif class="form-control note" rows="8" id="" maxlength="200" name="Note[]" placeholder="@lang('admin.note')">{{isset($dailyInfo->Note) ? $dailyInfo->Note : null}}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                    id="cancel">@lang('admin.btnCancel') </button>

                @if (
                // (!isset($dailyInfo->Status) && !isset($isOwner)) || ((isset($isOwner) && $isOwner ==1) &&
                // (isset($dailyInfo->Status) && $dailyInfo->Status!=2))
                (!isset($dailyInfo->id)) ||
                ((isset($dailyInfo->id) && $dailyInfo->Status != 2) && (isset($isOwner) && $isOwner ==1))
                )

                <button type="button" class="btn btn-primary btn-sm" id="save">@lang('admin.btnSave') </button>
                @endif
                @if (
                ((isset($dailyInfo->id) && $dailyInfo->Status != 2)) &&
                ((isset($isLeader) && $isLeader == 1) || (isset($isManager) && $isManager == 1))
                )
                <button class="action-col btn btn-success btn-sm btn-approve" type="button"
                    approve-id="{{$dailyInfo->id}}" onclick="approveReport()">
                    <i class="fa fa-check" aria-hidden="true"></i>
                </button>
                <button class="action-col btn btn-danger btn-sm open-deny" type="button" deny-id="{{$dailyInfo->id}}"
                    issue-value="{{$dailyInfo->Issue}}"
                    onclick="openDenyReport('{{route('admin.openDenyReport')}}',{{$dailyInfo->id}})">
                    <i class="fa fa-undo" aria-hidden="true"></i>
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" async>
    var CONTENT_FORM = $( ".tab-pane" ).html();
    var COUNT_TAB_WORK = 1;

    $(document).ready(function () {

        $('.select_project, .select_type_work').selectpicker();
        @if(isset($dailyInfo->id))
            SetDatePicker($('#sDate'), {
                endDate: new Date(),
            });
        @else
            SetDatePicker($('#sDate'), {
                format: FOMAT_DATE.toLowerCase(),
                endDate: new Date(),
                enableOnReadonly: true,
                todayHighlight: true,
                multidate: true,
            });
        @endif

        $('.date-input').val('{{isset($dailyInfo->Date) ? FomatDateDisplay($dailyInfo->Date, FOMAT_DISPLAY_DAY) : null }}');

        $('#save').click(function () {
            console.log($('#daily-form').serializeArray());
            ajaxGetServerWithLoader("{{ route('admin.DailySave') }}", 'POST', $('#daily-form').serializeArray(),function (data) {
                if (typeof data.errors !== 'undefined') {
                    $('.loadajax').hide();
                    showErrors(data.errors);
                    return ;
                }

                locationPage();
            });
        });
    });

    $(".nav-tabs")
        .on("click", "span", function () {
            var anchor = $(this).siblings('a');
            $(anchor.attr('href')).remove();
            $(this).parent().remove();
            $(".nav-tabs li").children('a').first().click();
        });

    $('#add-work').click(function () {
        COUNT_TAB_WORK = COUNT_TAB_WORK + 1;

        var id = $('.nav-tabs').children().length;
        if (id <= 9) {
            var tabId = "dReport_" + id;

            $(this).closest('li').before('<li><a href="#'+ tabId + '" data-toggle="tab">Work '+COUNT_TAB_WORK+'</a><span><i class="fa fa-times" aria-hidden="true"></i></span></li>');
            $('.tab-content').append('<div class="tab-pane tab-work fade" id="'+ tabId +'">'+ CONTENT_FORM +'</div>');

            $('.select_project, .select_type_work').selectpicker();
            $(".nav-tabs li:nth-child("+ id +") a").click();
            SetDatePicker($('.date-input,#sDate'), {
                endDate: new Date()
            });
            addAttrForItem(tabId);
            $("#"+tabId+"_date-input").val('');
        }
    });

    function addAttrForItem(parentID){
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

    // $('.modal').modal({ keyboard: false,
    //     show: true
    // });

    // Jquery draggable
    $('.modal-dialog').draggable({
        handle: ".modal-header"
    });
</script>