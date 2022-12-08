<div class="modal fade" id="timeKeeping-info" role="dialog">
    <div class="modal-dialog modal-xs ui-draggable">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">@lang('admin.timekeeping.add_timekeeping')</h4>
            </div>
            <div class="modal-body">
                <form action="" id="timekeeping-form" name="" method="post">
                    @if(isset($timekeepingInfo->id))
                        <input type="hidden" name="id" value="{{ $timekeepingInfo->id }}" id="timekeeping_id">
                    @endif
                <div class="form-group">
                    <label class="control-label" for="nameVi">@lang('admin.Staffs_name') &nbsp;<sup class="text-red">*</sup>:</label>
                    <div class="input-group search">
                        <select class='selectpicker show-tick show-menu-arrow' id='select-user-modal' name="UserID" data-live-search="true" data-size="5"
                                data-live-search-placeholder="Search">
                            @if(isset($checkUser->role_group) && $checkUser->role_group == 3))
                                <option value="{{$checkUser->id}}">{{$checkUser->FullName}}</option>
                            @else
                                {!! GenHtmlOption($users, 'id', 'FullName', isset($searchUser) ? $searchUser :  Auth::user()->id) !!}
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">@lang('admin.day') &nbsp;<sup class="text-red">*</sup>:</label>
                    <div class="input-group search">
                        <div class='input-group date datetime_txtBox' id='Date'>
                            <input type="text" class="form-control" id="dates" placeholder="Date" name="Date" autocomplete="off"
                                   value="{{isset($timekeepingInfo) ? FomatDateDisplay($timekeepingInfo->Date, FOMAT_DISPLAY_DAY) : ''}}">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">@lang('admin.times') &nbsp;<sup class="text-red">*</sup>:</label>
                    <div class="input-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class='input-group search marginBot10 date' id='checkin'>
                                    <input type="text" class="form-control" id="checkin-input" placeholder="Check in" name="TimeIn"
                                           value="{{isset($timekeepingInfo) ? $timekeepingInfo->TimeIn : config('settings.start_time')}}">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class='input-group search marginBot10 date' id='checkout'>
                                    <input type="text" class="form-control" id="checkout-input" placeholder="Check out" name="TimeOut"
                                           value="{{isset($timekeepingInfo) ? $timekeepingInfo->TimeOut : config('settings.end_time')}}">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                <button type="button" class="btn btn-primary" id="saveTimekeeping">@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" async>
    $(document).ready(function () {
        $(".selectpicker").selectpicker();
        SetTimePicker($('#checkin,#checkout'));
        SetDatePicker($('#Date'),{
            enableOnReadonly: true,
            todayHighlight: true,
            multidate: true,
        });
        $( ".ui-draggable" ).draggable();
    });
    $('#saveTimekeeping').click(function () {
        ajaxGetServerWithLoader("{{route('admin.TimekeepingSave')}}", 'POST', $('#timekeeping-form').serializeArray(),function (data) {
            if (typeof data.errors !== 'undefined') {
                showErrors(data.errors);
                return ;
            }

            locationPage();
        });
    });

</script>
