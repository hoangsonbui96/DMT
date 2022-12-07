<div class="modal draggable fade in detail-modal" id="user-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable">

        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">@lang('admin.calendar_event.add')</h4>
            </div>
            <div class="modal-body">

                <div class="save-errors"></div>
                <form class="form-horizontal detail-form" action="" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="control-label" for="sDate">@lang('admin.startDate')<sup
                                            class="text-red">*</sup>:</label>
                                    <div class="input-group date datetime_txtBox datetime_txtBox_overtime">
                                        <input type="text" class="form-control" id="sDate-input"
                                               placeholder="@lang('admin.startDate')" name="StartDate"
                                               value="{{ isset($itemInfo->StartDate) ? FomatDateDisplay( $itemInfo->StartDate,FOMAT_DISPLAY_DAY) : null }}">
                                        <span class="input-group-addon">
		                                    <span class="glyphicon glyphicon-calendar"></span>
		                                </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="control-label" for="eDate">@lang('admin.endDate')<sup
                                            class="text-red">*</sup>:</label>
                                    <div class="input-group date datetime_txtBox datetime_txtBox_overtime">
                                        <input type="text" class="form-control" id="eDate-input"
                                               placeholder="@lang('admin.startDate')" name="EndDate"
                                               value="{{ isset($itemInfo->EndDate) ?FomatDateDisplay( $itemInfo->EndDate,FOMAT_DISPLAY_DAY) : '' }}">
                                        <span class="input-group-addon">
		                                    <span class="glyphicon glyphicon-calendar"></span>
		                                </span>
                                    </div>
                                </div>
                            </div>

                            <label for="comment">@lang('admin.event.type-event')<sup class="text-red">*</sup>:</label>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    @foreach (\App\Calendar::EVENT_VN as $key => $description)
                                    <div class="radio form-inline">
                                        <label class="radio-inline">
                                            <input type="radio" name="Type" value="{{ $key }}">{{ $description }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div id="color-event">
                                <label for="comment">Chọn màu cho sự kiện<sup class="text-red">*</sup>:</label>
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div id="jaColor" class="input-group colorpicker-component">
                                            <input type="text"
                                                   value="{{ isset($itemInfo->jaColor) ? $itemInfo->jaColor : '#0dd0fc' }}"
                                                   class="form-control" id="hexColor" name="jaColor"/>
                                            <span class="input-group-addon" id="addon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <label for="comment">@lang('admin.event.event-content')&nbsp;<sup class="text-red">*</sup>:</label>
                            <textarea class="form-control" rows="5" name="Content" id="content"
                                      maxlength="200">{{ isset($itemInfo->Content) ? $itemInfo->Content : null }}</textarea>

                            <input type="hidden" name="CalendarID" value="" id="CalendarID">
                            @if(isset($itemInfo->id))
                                <input type="hidden" name="id" value="{{ $itemInfo->id }}">
                            @endif
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                        id="cancel">@lang('admin.btnCancel')</button>
                @if(isset($itemInfo->id))
                    <button type="submit" class="btn btn-warning btn-sm update delete delete-one"
                            data-id="{{ isset($itemInfo->id) ? $itemInfo->id : null }}">@lang('admin.btnDelete')</button>
                @endif
                @if(isset($itemInfo->id))
                    @can('action',$editC)
                        <button type="submit" class="btn btn-primary btn-sm save-form">@lang('admin.btnSave')</button>
                    @endcan
                @else
                    @can('action',$addC)
                        <button type="submit" class="btn btn-primary btn-sm save-form">@lang('admin.btnSave')</button>
                    @endcan
                @endif
            </div>
        </div>
    </div>

</div>
<script type="text/javascript" async>
    $(function () {
        SetDatePicker($('.date'));
        $(".selectpicker").selectpicker();
        $(".ui-draggable").draggable();

        $('#jaColor').click(function () {
            $('#addon').click();
        });
        $('#color-event').hide();
        $('input[type=radio]').on('change',function () {
            if($(this).val() == 2){
                $('#color-event').show();
            }else{
                $('#color-event').hide();
            }
        });

    });

    var idcalenda = $('input[name="id"]').val();
    if (!!!idcalenda) {
    } else {
        $('input:radio[name=Type][value="<?php echo isset($itemInfo->Content) ? $itemInfo->Type : '' ?>"]').prop('checked', 'checked');
    }
    if (!!!$('input[name="id"]').val()) {
        $('.delete-one').prop("disabled", true);
    } else {
        $('.delete-one').prop("disabled", false);
    }
    $(function () {
        $('#jaColor').colorpicker();
        var CalendarID = $("#select-calendar").val().trim();
        if (!!!CalendarID) {
            CalendarID = 1;
        }
        $('#CalendarID').attr('value', CalendarID);
    });

    $('.save-form').click(function () {
        ajaxGetServerWithLoader("{{ route('admin.Calendar') }}", 'post', $('.detail-form').serializeArray(), function (data) {
            if (typeof data.errors !== 'undefined') {
                showErrors(data.errors[0]);
                return;
            }
            locationPage();
        })
    });

    $('.delete-one').click(function () {
        var itemId = $('.delete-one').attr('data-id');
        var ajaxUrl = "{{ route('admin.CalendarItem') }}";
        showConfirm('Bạn có chắc muốn xóa?', function () {
            ajaxServer(ajaxUrl + '/' + itemId + '/del', 'GET', null, function (data) {
                if (data == 1) {
                    window.location.reload();
                }
            })
        });
    });
</script>
