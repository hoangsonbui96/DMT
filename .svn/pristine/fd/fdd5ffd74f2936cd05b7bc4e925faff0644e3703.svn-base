<div class="modal draggable fade in detail-modal" role="dialog">
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
                    @can('admin', $menu)

                    <div class="form-group">
                        <label class="control-label col-sm-3" for="RoomId">@lang('admin.overtime.fullname')&nbsp;<sup class="text-red">*</sup>:</label>
                        <div class="bootstrap-select col-sm-4">
                            <select class="form-control selectpicker show-tick show-menu-arrow" name="UserID" id="meetingRoomchoose"
                                    data-live-search="true" data-live-search-placeholder="@lang('admin.meeting.search')" data-size="6" tabindex="-98" required>
                                <option value="">[@lang('admin.overtime.fullname')]</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ isset($itemInfo->UserID) && $itemInfo->UserID == $user->id ? 'selected' : '' }}>{{ $user->FullName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @else
                        <input type="hidden" name="UserID" value="{{ \Illuminate\Support\Facades\Auth::user()->id }}">
                    @endcan
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="sDate">@lang('admin.overtime.time_work') &nbsp;<sup class="text-red">*</sup>:</label>
                        <div class="col-sm-9">
                            <div class="input-group date datetime_txtBox datetime_txtBox_overtime" id="sDate">
                                <input type="text" class="form-control" id="sDate-input" placeholder="Ngày bắt đầu" name="STime"
                                       value="{{ isset($itemInfo->STime) ? \Carbon\Carbon::parse($itemInfo->STime)->format('d/m/Y H:i') : null }}">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            <div class="input-group date datetime_txtBox datetime_txtBox_overtime" id="eDate">
                                <input type="text" class="form-control" id="eDate-input" placeholder="Ngày kết thúc" name="ETime"
                                       value="{{ isset($itemInfo->ETime) ?\Carbon\Carbon::parse($itemInfo->ETime)->format('d/m/Y H:i') : null }}">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3" for="RoomId">@lang('admin.overtime.project') &nbsp;<sup class="text-red">*</sup>:</label>
                        <div class="bootstrap-select col-sm-9">
                            <select class="form-control selectpicker show-tick show-menu-arrow" name="ProjectID" id="meetingRoomchoose"  data-size="6" tabindex="-98" required>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ isset($itemInfo->ProjectID) && $itemInfo->ProjectID == $project->id ? 'selected' : '' }}>{{ $project->NameVi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3" for="RoomId">@lang('admin.overtime.break_time') &nbsp;<sup class="text-red">*</sup>:</label>
                        <div class="bootstrap-select col-sm-9">
                            <input type="number" step="0.1" class="form-control" placeholder="theo giờ" id="brtime" name="BreakTime"
                                   value="{{ isset($itemInfo->BreakTime) ? $itemInfo->BreakTime : null }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3" for="RoomId">@lang('admin.overtime.content') &nbsp;<sup class="text-red">*</sup>:</label>
                        <div class="bootstrap-select col-sm-9">
                            <textarea name="Content" class="form-control" id="content" rows="3" placeholder="Nội dung">{{ isset($itemInfo->Content) ? $itemInfo->Content : null }}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3" for="member">@lang('admin.overtime.request_manager')&nbsp;<sup class="text-red">*</sup>:</label>
                        <div class="col-sm-9">
                            <select class='selectpicker show-tick show-menu-arrow' data-actions-box="true" data-size="5" id='select-leader' name="RequestManager[]" multiple>
                                @foreach($request_manager as $item)
                                    <option value="{{ $item->id }}" {{ isset($itemInfo->UserID) && in_array($item->id, $itemInfo->RequestManager) ? 'selected' : '' }}>{{ $item->FullName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                <button type="button" class="btn btn-primary btn-sm save-form">@lang('admin.btnSave')</button>
            </div>
        </div>

    </div>
</div>

<script>
    $(function () {
        $(".selectpicker").selectpicker();
        $('#sDate,#SDate-input,#eDate, #eDate-input').datetimepicker({
            format: 'DD/MM/YYYY HH:mm',
            stepping: 5,
        });
        $( ".draggable" ).draggable();
    });

    //click save form
    $('.save-form').click(function () {
        $('.save-errors').hide();
        var start_date = $("[name=STime]").val();
        var end_date = $("[name=ETime]").val();
        if(end_date<start_date || isEmpty(end_date) || isEmpty(start_date)){
            alert("Thời gian làm thêm không hợp lệ");
            return;
        }

        ajaxGetServerWithLoader("{{ route('admin.Overtimes') }}", 'POST', $('.detail-form').serializeArray(),
            function (data) {
                if (typeof data.errors !== 'undefined') {
                    showErrors(data.errors);
                    return;
                }

                locationPage();
            }
        );
    });
</script>

