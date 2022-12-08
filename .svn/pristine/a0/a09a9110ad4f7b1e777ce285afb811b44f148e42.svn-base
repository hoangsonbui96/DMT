<div class="modal draggable fade in detail-modal modal-css" role="dialog">
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
                    <div class="row">
                        <div class="col-md-12 col-sm-8 col-xs-12">
                            <div class="form-group">
                                @if(isset($RoomReportInfo->id))
                                <input type="hidden" name="id" value="{{ $RoomReportInfo->id }}">
                                @endif
                           </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="RoomId">@lang('admin.room_report.room') &nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-sm-9">
                                    <select class='selectpicker show-tick show-menu-arrow' name="RoomID" data-size="5">
                                    <option value="">[@lang('admin.room_report.room')]</option>
                                         @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" {{ isset($RoomReportInfo-> RoomID) && $RoomReportInfo->RoomID == $room->id ? 'selected' : '' }}>{{ $room->Name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="sDate">@lang('admin.times')&nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-sm-3">
                                    <div class="input-group date datetime_txtBox" id="sDate" >
                                        <input type="text" class="form-control" id="sDate-input" placeholder="Từ ngày" name="SDate" autocomplete="off" value="{{ isset($RoomReportInfo->SDate) ? FomatDateDisplay($RoomReportInfo->SDate, FOMAT_DISPLAY_DAY) : null }}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="input-group date datetime_txtBox" id="eDate" >
                                        <input type="text" class="form-control" id="eDate-input" placeholder="Đến ngày" name="EDate" autocomplete="off" value="{{ isset($RoomReportInfo->EDate) ? FomatDateDisplay($RoomReportInfo->EDate, FOMAT_DISPLAY_DAY) : null }}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="RoomId">@lang('admin.room_report.week_work') &nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control note" rows="3" maxlength="200" id="reason" placeholder="Công việc tuần" name="week_work">{{ isset($RoomReportInfo->week_work) ? $RoomReportInfo->week_work : null }}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="RoomId">@lang('admin.room_report.unfinished_work') &nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-sm-9">

                                    <textarea class="form-control note" rows="3" maxlength="200" id="reason" placeholder="Công việc tồn đọng" name="unfinished_work">{{ isset($RoomReportInfo->unfinished_work) ? $RoomReportInfo->unfinished_work : null }}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="RoomId">@lang('admin.room_report.Contents') :</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control note" rows="3" maxlength="200" id="reason" placeholder="Đề xuất" name="Contents">{{ isset($RoomReportInfo->Contents) ? $RoomReportInfo->Contents : null }}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="RoomId">@lang('admin.room_report.noted'):</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control note" rows="3" maxlength="200" id="reason" placeholder="Ghi chú" name="noted">{{ isset($RoomReportInfo->noted) ? $RoomReportInfo->noted : null }}</textarea>
                                </div>
                            </div>
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

<script type="text/javascript" async>
    $(function () {
        SetDatePicker($('#sDate, #eDate'));
        $('#toggle-one').bootstrapToggle();
        $('.draggable').draggable();
        $('.selectpicker').selectpicker();
        $('.save-form').click(function () {
            ajaxGetServerWithLoader("{{ route('admin.RoomReports') }}", 'POST', $('.detail-form').serializeArray(), function (data) {
                if (typeof data.errors !== 'undefined'){
                    showErrors(data.errors);
                    return ;
                }
                locationPage()
            });
        });
    });

    // $(function () {
    //     $(".selectpicker").selectpicker();
    //     const fm = FOMAT_DATE;
    //     $("#Date-input,#sDate-input,#eDate-input").datetimepicker({    format: fm ,   });
    //     $('#Date-input, #sDate-input,#eDate-input').on('dp.change', function (e) {
    //         value = $("#Date-input, #sDate-input,#eDate-input").val();
    //         SDate = moment(value, fm).day(1).format(fm);
    //         EDate =  moment(value, fm).day(7).format(fm);
    //         $("#Date-input").val( "Từ " +SDate + " đến " +EDate);
    //         $("#sDate-input").val(SDate);
    //         $("#eDate-input").val(EDate);

    //     });
    //     $( ".draggable" ).draggable();
    // });
</script>

