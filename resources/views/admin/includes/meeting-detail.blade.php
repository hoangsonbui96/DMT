<div class="modal draggable fade in detail-modal"id="meeting-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable ">

        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">@lang('admin.room.register_meeting_room')</h4>
            </div>
            <div class="modal-body">

                <div class="save-errors"></div>
                <form class="detail-form" role="form" action="" method="POST">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label>@lang('admin.meeting.meeting_room')&nbsp;<sup class="text-red">*</sup>:</label>
                            <select class="form-control selectpicker show-tick show-menu-arrow" placeholder="Phòng họp" name="RoomID" id="meetingRoomchoose" data-live-search="true" data-live-search-placeholder="@lang('admin.meeting.search')" data-size="6" data-purposeold="" data-nameold="" data-meetingdate="" data-btimeold="" data-ftimeold="" data-participantold="" data-roomnameold="" data-id="" data-idcodemeeting="" data-roomidold="" data-actionmeeting="" tabindex="-98" required>
                                <option value="">[@lang('admin.meeting.please_select')]</option>
                                {!!
                                    GenHtmlOption($meetingRooms, 'id', 'Name', isset($itemInfo->RoomID) ? $itemInfo->RoomID : '')
                                !!}
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('admin.meeting.purpose')&nbsp;<sup class="text-red">*</sup>:</label>
                             <input type="text" class="form-control" id="purpose-infomeeting" name="Purpose" maxlength="100" placeholder="Mục đích chính của cuộc họp" value="{{ isset($itemInfo->Purpose) ? $itemInfo->Purpose : '' }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('admin.meeting.content')&nbsp;:</label>
                             <textarea rows="5" cols="20" class="form-control" id="name-infomeeting" placeholder="Nội dung chính của cuộc họp" name="Description" maxlength="100">{{ isset($itemInfo->Description) ? $itemInfo->Description : '' }}</textarea>
                        </div>
                        <div class="form-group row">
                            <div class=" col-sm-6">
                                <label>@lang('admin.meeting.day_meeting')&nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="input-group date " id="date-timemeeting">
                                    <input type="text" class="form-control" id="meetingDate-input" name="MeetingDate" value="{{ isset($itemInfo->MeetingDate) && ($copy == 0) ? FomatDateDisplay($itemInfo->MeetingDate, FOMAT_DISPLAY_DAY): '' }}">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                </div>
                            </div>
                            <div class=" col-sm-6">
                                <div class="input-group date" >
                                    <label>@lang('admin.meeting.total_time_meeting'):</label>
                                    <div id="total-time">
                                        <input type="text" class="form-control" id="diffTime0" disabled="disabled" value="{{ isset($itemInfo->diffHours) ? $itemInfo->diffHours : '' }}">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>@lang('admin.meeting.time_start_meeting') &nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="input-group time" id="bTimechoose">
                                    <input type="text" class="form-control" id="bTimechoose-input" name="MeetingTimeFrom" value="{{ isset($itemInfo->MeetingTimeFrom) ? $itemInfo->MeetingTimeFrom : '' }}">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </div>
                        <!-- </div>
                        <div class="form-group"> -->
                            <div class="col-sm-6">
                                <label>@lang('admin.meeting.time_end_meeting') &nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="input-group time" id="fTimechoose">
                                    <input type="text" class="form-control" id="fTimechoose-input" name="MeetingTimeTo" value="{{ isset($itemInfo->MeetingTimeTo) ? $itemInfo->MeetingTimeTo : '' }}">
                                    <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                            <div class="box alltable-meeting-show">
                                <label for="Creater" class="title-ceater">@lang('admin.meeting.meeting_users'):</label>
                            </div>
                            <div class="row alltable-meeting-show2 floatmeeting1">
                                <div class="col-lg-12 col-sm-6 allshowemployees" id="allshowemployees" style="height: 200px;overflow: auto;margin-top: 29px;">
                                        <div class="col-sm-12"></div>
                                        <table width="100%" class="table no-footer dataTable" id="showUser" role="grid" style="width: 100%;">
                                            <thead>

                                            <tr role="row">
                                                <th class="sorting_asc" tabindex="0"  rowspan="1" colspan="1" aria-sort="ascending" style="width: 70px;">ID</th>
                                                <th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 170px;">@lang('admin.meeting.employer')</th>
                                                <th style="width: 150px;">@lang('admin.meeting.action')</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($users as $user)
                                            <tr data-id="{{ $user->id }}" role="row" class="odd">
                                                <td class="sorting_1"  style="text-align: center">{{ $user->id }}</td>
                                                <td style="text-align: center">{{ $user->FullName }}</td>
                                                <td style="text-align: center">
                                                    <button type="button" class="addParticipant" onclick="addParticipant('{{ $user->id }}')">+</button>
                                                </td>
                                            </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
    {{--                                </div>--}}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                           <div class="row alltable-meeting-show2 floatmeeting2">
                                <div class="col-lg-12 col-sm-6 allshowemployees show-employees2" style="height: 200px;overflow: auto;margin-top: 62px;">
                                    <h3 class="title-tbl"></h3>
                                    <table width="100%" class="table" id="listMeetingParticipant">
                                        <thead>
                                        <tr>
                                            <th style="width: 200px;">@lang('admin.meeting.meeting_users_2')</th>
                                            <th class="no-sort" style="width: 170px;">@lang('admin.meeting.meeting_host') &nbsp;<sup class="text-red">*</sup>:
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="Participant" id="Participant">
                    <!-- /.box-body -->
                    @if(isset($itemInfo->id) && ($copy == 0))
                        <input type="text" class="form-control hidden" name="id" value="{{ $itemInfo->id }}">
                    @endif
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel" data-control="">@lang('admin.btnCancel')</button>
                <button type="button" class="btn btn-primary save-form" id="SavemeetingRoom" data-control="">@lang('admin.btnSave')</button>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript" async>
    $('.save-form').click(function() {
        idmeeting = $('input[name="id"]').val();
        str = $('#diffTime0').val();
        res = str.match(/-/g);
        var MDate = $('#meetingDate-input').val();
        var arrDate = MDate.split('/');
        var MDate2 = arrDate[2]+'-'+arrDate[1]+'-'+arrDate[0];
        var Mroom = $('#meetingRoomchoose').val();
        var Mbtime = $('#bTimechoose-input').val()+':00';
        var Mftime = $('#fTimechoose-input').val()+':00';
         $('.loadajax').show();
         var unApproveUrl = "{{ route('admin.MeetingSchedules') }}";
         ajaxServer(unApproveUrl, 'post',  $('.detail-form').serializeArray(), function (data) {
            if (typeof data.errors !== 'undefined'){
                $('.loadajax').hide();
                showErrors(data.errors[0]);
            }else{
                $('.loadajax').hide();
                window.location.reload();
            }
        })

    });
    $(function() {
        SetDatePicker($('#meetingDate-input,#date-timemeeting'), {
            format: FOMAT_DATE.toLowerCase(),
            startDate: new Date()
        });
        // $('#meetingDate-input,#date-timemeeting').datetimepicker({
        //     format: FOMAT_DATE,
        //     minDate: new Date(),
        // });
        $('#bTimechoose-input,#fTimechoose-input').datetimepicker({
            allowInputToggle: true,
            format: 'HH:mm',
            // stepping: 15
        });
        $('#bTimechoose,#fTimechoose').datetimepicker({
            allowInputToggle: true,
            format: 'HH:mm',
            // stepping: 15
        });
        $( ".ui-draggable" ).draggable();
        $('.selectpicker').selectpicker();
        $('#bTimechoose-input, #fTimechoose-input').datetimepicker(
            {format: 'YYYY/MM/DD' }).on('dp.change', function (e) {
                var start = $("#bTimechoose-input").val();
                var end = $("#fTimechoose-input").val();
                if (typeof start != 'undefined' && typeof end != 'undefined' && start != '' && end != '') {
                    $("#diffTime0").val(diff(start, end));
                }
        });
        $('#bTimechoose,#fTimechoose').datetimepicker(
            {format: 'YYYY/MM/DD' }).on('dp.change', function (e) {
                var start = $("#bTimechoose-input").val();
                var end = $("#fTimechoose-input").val();
                if (typeof start != 'undefined' && typeof end != 'undefined' && start != '' && end != '') {
                    $("#diffTime0").val(diff(start, end));
                }
        });
    });

    function addParticipant(idRemove) {
        var hostId = {{ isset($itemInfo->MeetingHostID) ? $itemInfo->MeetingHostID : 0 }};
        if(!isEmpty(idRemove)){
            var td1 = $('#showUser tr[data-id=' + idRemove + ']').find('td:eq(0)').text();
            var td2 = $('#showUser tr[data-id=' + idRemove + ']').find('td:eq(1)').text();

            var dataValue = $('#Participant').val();
            if(dataValue == ''){
                dataValue += ','+idRemove+',';
            }else{
                dataValue += idRemove+',';
            }
            $('#Participant').val(dataValue);
            if(td2.length>0){
                var htmlAppend = `
                <tr data-id="`+ idRemove +`">
                    <td class='participant1'>`+ td2 +`</td>
                    <td>
                        <input name="MeetingHostID" class="meetingHost" type="radio" value="`+ idRemove +`" data-hostname ="`+ td2 +`"`+iff(idRemove == hostId, "checked='checked'", "")+`>
                    </td>
                    <td>
                        <span class='removeParticipant' onclick="removeParticipant('` + idRemove +`')"><i class='fa fa-times' aria-hidden='true'></i></span>
                    </td>
                </tr>
            `;
                $("#listMeetingParticipant tbody").append(htmlAppend);
                $('#showUser tr[data-id=' + idRemove + ']').hide();
            }
        }


    }

    function removeParticipant(idRemoveParticipant) {
        var participantName = $('#listMeetingParticipant tr[data-id=' + idRemoveParticipant + ']').find('td.participant1').text();
        $('#showUser tr[data-id=' + idRemoveParticipant + ']').show();

        $('#listMeetingParticipant tr[data-id=' + idRemoveParticipant + ']').remove();
        var dataValue = $('#Participant').val();
        dataValue = dataValue.replace(idRemoveParticipant+',', '');
        if(dataValue.length <=1) dataValue = '';
        $('#Participant').val(dataValue);
    }
    // $('.dataTable').dataTable();
    $('.dataTable').DataTable({
        // "searching": false,
        "ordering": true,
        // "lengthChange": true,
        "info": false,
        // "pageLength": 10,
        "columnDefs": [
            { "targets": 'no-sort', "orderable": false}
        ],
        "paging": false,
        // "sDom": '<"row view-filter"<"col-sm-12"<"pull-left"l><"pull-right"f><"clearfix">>>t<"row view-pager"<"col-sm-12"<"text-center"ip>>>',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Tìm kiếm",
        }
    });

    @if(isset($itemInfo->Participant))

    var listEmployers = '{{ $itemInfo->Participant }}'.split(",");
    // console.log(listEmployers);
    for(key in listEmployers){
        if (listEmployers.hasOwnProperty(key) &&
            /^0$|^[1-9]\d*$/.test(key) &&
            key <= 4294967294
        ) {
            // console.log(key);
            addParticipant(listEmployers[key]);
        }
    }

    @endif
    function isNumber(n) { return /^-?[\d.]+(?:e-?\d+)?$/.test(n); }
    $('#showUser_filter input').addClass('searchmeeting');
</script>
