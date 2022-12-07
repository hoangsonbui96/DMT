   var clicked = 'add';

   // $(document).ready(function(){
   //      $('.modal.draggable>.modal-dialog').draggable({
   //          cursor: 'move',
   //          handle: '.modal-header'
   //      });
   //      $('.modal.draggable>.modal-dialog>.modal-content>.modal-header').css('cursor', 'move');
   //      $(document).on('click', '#SavemeetingRoom, #cancel, .close', function(event) {
   //          $('body').removeClass('modal-open-2');
   //      });
   //
   //      $(document).on('click', '#closemeeting, #Closemeeting', function(){
   //          $('#exampleModalCenter').modal('hide');
   //          $('#meeting-info').modal('show');
   //          $('body').addClass('modal-open-2');
   //      });
   //      LoadShowUser();
   //      loadTableMeetingSchedule();
   //      ckeckedUser();
   //      setEventClickEditMeeting();
   //      setEventClickCopyMeeting();
   //      getDraftdata();
   //      clickchoosemeeting();
   //  });
   $(document).ready(function(){
        $('#add-meeting').click(function() {
            $('#meetingRoomchoose').prop( "disabled", false );
            resetRoomMeetingchoose();
            $('#meeting-info').modal({backdrop: 'static', keyboard: false});
            $('#meeting-info').modal('show');
            $('#SavemeetingRoom').unbind().prop( "disabled", false );
            $('#checkRoom').unbind().prop("disabled",false );
            $('#checkRoom').click(function() {
                $('#meeting-info').modal('hide');
                $('#exampleModalCenter').modal({backdrop: 'static', keyboard: false});
                $('#exampleModalCenter').modal('show');
            });
        });
   });
    // function ckeckedUser(){
    //     var IDuser = ID;
    //     var arrResuls = {};
    //     $.ajax({
    //         url: './Meeting/meeting_action.php',
    //         type: 'POST',
    //         dataType:"json",
    //         async: false,
    //         data:{
    //             action: 'getAllUser',
    //         },
    //         success: function(arrResult) {
    //             arrResuls = arrResult;
    //         }
    //     })
    //
    //     for (var j = 0; j < arrResuls.length; j++) {
    //         if(arrResuls[j].participant_id == IDuser){
    //             if(arrResuls[j].Groupname != '0'){
    //                 $('#add-meeting').unbind().prop( "disabled", true );
    //                 // $('#tblMeetingList_wrapper .btn-editmeeting').unbind().prop( "disabled", true );
    //                 $('#tblMeetingList_wrapper .btn-copymeeting').unbind().prop( "disabled", true );
    //                 $('#tblMeetingList_wrapper .btn-delemeeting').unbind().prop( "disabled", true );
    //                 $('#meetingContentDraft').unbind().css("display","none");
    //                 $('#SavemeetingRoom').hide();
    //             }
    //             else{
    //                 $('#add-meeting').unbind().prop( "disabled", false );
    //                 $('#tblMeetingList_wrapper .btn-editmeeting').unbind().prop( "disabled", false );
    //                 $('#tblMeetingList_wrapper .btn-copymeeting').unbind().prop( "disabled", false );
    //                 $('#tblMeetingList_wrapper .btn-delemeeting').unbind().prop( "disabled", false );
    //                 $('#meetingContentDraft').unbind().css("display","initial");
    //                 $('#SavemeetingRoom').unbind().show();
    //             }
    //         }
    //     }
    // }
    function clickchoosemeeting(){
        $(document).on('click', '#btn-tick-meeting,#closemeeting', function(){
            var checkedclick = $(this).attr('data-control');
            $('#SavemeetingRoom').attr("data-control",'');
            if(checkedclick == 'close'){
                var activetime  = $('.table-meeting-info tbody tr td button[style="background-color:red"]').attr('data-activetime');
                if(activetime == 'Không khả dụng'){
                    $('#exampleModalCenter #Closemeeting').click();
                    var arr_meeting =  $('#meetingRoomchoose').attr('data-roomidold');
                    var data        = $('#meetingRoomchoose').attr('data-purposeold');
                    var pDate       = $('#meetingRoomchoose').attr('data-meetingdate');
                    var bTimeafter  = $('#meetingRoomchoose').attr('data-btimeold');
                    var roomchangename = $('#meetingRoomchoose').attr('data-roomnameold');
                    var fTimeafter  = $('#meetingRoomchoose').attr('data-ftimeold');
                }else{
                    var arr_meeting = $('.table-meeting-info tbody tr td button[style="background-color:red"]').val();
                    var data        = $('.table-meeting-info tbody tr td button[style="background-color:red"]').attr('data-tableinfomeeting');
                    var pDate       = $('.table-meeting-info tbody tr td button[style="background-color:red"]').attr('data-dateting');
                    var bTimeafter  = $('.table-meeting-info tbody tr td button[style="background-color:red"]').attr('data-bTimeafter');
                    var roomchangename = $('.table-meeting-info tbody tr td button[style="background-color:red"]').attr('data-room');
                    var fTimeafter  = $('.table-meeting-info tbody tr td button[style="background-color:red"]').attr('data-fTimeafter');
                }
            }else{
                var arr_meeting     = $(this).val();
                var data            = $(this).attr('data-tableinfomeeting');
                var pDate           = $(this).attr('data-dateting');
                var bTimeafter      = $(this).attr('data-bTimeafter');
                var roomchangename  = $(this).attr('data-room');
                var fTimeafter      = $(this).attr('data-fTimeafter');
            }
            var pDatachoose     = $('#meetingDate-input').val();
            var bDateTimechoose = $('#bTimechoose-input').val();
            var fDateTimechoose = $('#fTimechoose-input').val();
            var Roomidchoose    = $("#meetingRoomchoose option:checked").val();
            var NameRoomidchoose= $("#meetingRoomchoose").text();
            var purposedatainfo = $('#purpose-infomeeting').val();
            var datainfomett    = $('#name-infomeeting').val();

            if(!!!data && !!!arr_meeting && !!!pDate && !!!bTimeafter && !!!fTimeafter ){

            }else{
                if(data != purposedatainfo){
                    $("#purpose-infomeeting").val(purposedatainfo);
                }

                if(arr_meeting != Roomidchoose){
                    $("#meeting-info #meetingRoomchoose ").selectpicker('val',arr_meeting);
                }
                if(pDate != pDatachoose){
                    $('#meetingDate-input').val(pDate);
                }
                if(bTimeafter != bDateTimechoose){
                    $('#bTimechoose-input').val(bTimeafter);
                }
                if(fTimeafter != fDateTimechoose){
                    $('#fTimechoose-input').val(fTimeafter);
                }

                if((fTimeafter != fDateTimechoose) || (fTimeafter != fDateTimechoose))
                {
                    $('#diffTime0').val(calulateTotalTime(bTimeafter, fTimeafter));
                }
            }
            $('#exampleModalCenter').modal('hide');
            $('#meeting-info').modal('show');
        });
    }
    // function LoadShowUser() {
    //     $.ajax({
    //         url: './Meeting/meeting_action.php',
    //         type: 'POST',
    //         dataType:"json",
    //         async: false,
    //         data:{
    //             action: 'getAllUser',
    //         },
    //         success: function(arrResult) {
    //             $('#showUser tbody').html('');
    //             GenHtmlAppendForShowUser(arrResult)
    //         }
    //     });
    // }

    function addParticipant(idRemove) {
        if(idRemove == '') return false;
        var td1 = $('#showUser tr[data-id=' + idRemove + ']').find('td:eq(0)').text();
        var td2 = $('#showUser tr[data-id=' + idRemove + ']').find('td:eq(1)').text();

        if(td2 =='' && idRemove !=''){
            $.ajax({
                url: './Meeting/meeting_action.php',
                type: 'POST',
                dataType:"json",
                async: false,
                data:{
                    action: 'getAllUser',
                },
                success: function(arrResult) {
                    for (var i = 0; i < arrResult.length; i++) {
                        if(arrResult[i].participant_id == idRemove){
                            td2 = arrResult[i].name_participant;
                            // console.log(td2);
                        }
                    }
                }
            });
        }
        // console.log(td2);
        var htmlAppend = `
            <tr data-id="`+ idRemove +`">
                <td class='participant1'>`+ td2 +`</td>
                <td>
                    <input name="meetingHost" class="meetingHost" type="radio" value="`+ idRemove +`" data-hostname ="`+ td2 +`" >
                </td>
                <td>
                    <span class='removeParticipant' onclick="removeParticipant('` + idRemove +`')"><i class='fa fa-times' aria-hidden='true'></i></span>
                </td>
            </tr>
        `;
        $("#listMeetingParticipant tbody").append(htmlAppend);
        $('#showUser tr[data-id=' + idRemove + ']').hide();
    }

    function removeParticipant(idRemoveParticipant) {
        var participantName = $('#listMeetingParticipant tr[data-id=' + idRemoveParticipant + ']').find('td.participant1').text();
        $('#showUser tr[data-id=' + idRemoveParticipant + ']').show();

        $('#listMeetingParticipant tr[data-id=' + idRemoveParticipant + ']').remove();
    }

    function GenHtmlAppendForShowUser(arrResult) {
        var html = '';
        clearDataTable('showUser');
        for (var i = 0; i < arrResult.length; i++) {
            if(arrResult[i].Deleted == 'F'){
                html +=`
                <tr data-id="`+ arrResult[i].participant_id +`">
                    <td>`+ arrResult[i].participant_id +`</td>
                    <td>`+ arrResult[i].name_participant +`</td>
                    <td>
                        <button type="button" class='addParticipant' onclick="addParticipant('`+ arrResult[i].participant_id +`')">+</button>
                    </td>
                </tr>
                `;
            }
        }
        $("#showUser tbody").append(html);
        setTableeemploy('showUser');
    }

    function GetAllMeetingSchedules(RegisterIDtable,MeetingHostIDtable,RoomMeetingtable,MeetingDatetable,MeetingFDatetable,Actionmeetingtable,Participanttable,TimeMeetingTimeFrom) {
        var arrResults = {};
        $.ajax({
            url: './Meeting/meeting_action.php',
            type: 'POST',
            dataType:"json",
            async: false,
            // cache: false,
            data:{
                action: 'getAllMeetingRoom',
                meeting_id: null,
                RegisterIDtable    :RegisterIDtable,
                MeetingHostIDtable :MeetingHostIDtable,
                RoomMeetingtable   :RoomMeetingtable,
                MeetingDatetable   :MeetingDatetable,
                MeetingFDatetable  :MeetingFDatetable,
                Actionmeetingtable :Actionmeetingtable,
                Participanttable   :Participanttable,
                TimeMeetingTimeFrom:TimeMeetingTimeFrom
            },
            success: function(arrResult) {
                // console.log(arrResult)
                if (arrResult != "") {
                    arrResults = arrResult;
                }
            }
        });
        return arrResults;
    }
    $(document).ready(function ShowDiffTime (arrResult){

            var html = '';

        // alert(CalculateTime ("8:00", "15:25"));
        var bDateTimechoose = $('#bTimechoose-input').val();
        var fDateTimechoose = $('#fTimechoose-input').val();
        $('#diffTime0').val(CalculateTime(bDateTimechoose, fDateTimechoose));
        // alert(bDateTimechoose);
        // alert(fDateTimechoose);
        // alert(calulateTotalTime(bDateTimechoose, fDateTimechoose));
        // alert($('#diffTime0').val());
        $('#diffTime0').val(calulateTotalTime(bDateTimechoose, fDateTimechoose));
        // console.log(CalculateTime(bTimechoose-input, fTimechoose-input));
    });

    function CnvStringToInt(str) {
        // console.log(typeof str);
        try {
            return parseInt(str);
        } catch {
            return 0;
        }
    }

    function CalculateTime(sTime, eTime) {

        var rst = 0;
        var splitSTime = sTime.split(':');
        var splitETime = eTime.split(':');

        // console.log(splitSTime);
        // console.log(splitETime);

        var minuteSTime = CnvStringToInt(splitSTime[0]) * 60 + CnvStringToInt(splitSTime[1]);
        var minuteETime = CnvStringToInt(splitETime[0]) * 60 + CnvStringToInt(splitETime[1]);
        var diffTime = minuteETime - minuteSTime;

        return Math.floor(diffTime / 60) + ":" + diffTime % 60 ;
    }

    function formatDate(strDate) {

        if (!!!strDate) return '';

        var date = new Date(strDate);
        var day = date.getDate();
        var monthIndex = date.getMonth() + 1;
        if (monthIndex < 10) {
            monthIndex = '0' + monthIndex;
        }
        var year = date.getFullYear();

        return (day >= 10 ? day : ("0" + day)) + '/' + monthIndex + '/' + year;
    }

    function formatTime(time) {

        if (!!!time) return '';

        var d = new Date('01/01/2019 ' + time);
        var hr = d.getHours();
        var min = d.getMinutes();
        if (hr < 10) {
            hr = "0" + hr;
        }
        if (min < 10) {
            min = "0" + min;
        }

        return hr + ':' + min;
    }

    function calulateTotalTime(sTime, eTime)
    {
        var bMeetingTimeFrom = formatTime(sTime).split(':');
        var fMeetingTimeTo = formatTime(eTime).split(':');

        var totalhour       = '';
        var totalMinu       = '';
        var total           = '';
        if(fMeetingTimeTo[1] >= bMeetingTimeFrom[1]){
            var totalhour       =  fMeetingTimeTo[0] - bMeetingTimeFrom[0] ;
            var totalMinu       =  fMeetingTimeTo[1] - bMeetingTimeFrom[1] ;
            var total           =   (totalhour >= 10 ? totalhour : ('0' + totalhour))+':'+(totalMinu >= 10 ? totalMinu : ('0' + totalMinu));
        }else if(fMeetingTimeTo[1] < bMeetingTimeFrom[1]){
            var totalhour       =  fMeetingTimeTo[0] - bMeetingTimeFrom[0] -1;
            var totalMinu       =  fMeetingTimeTo[1] - bMeetingTimeFrom[1] +60;
            var total           =   (totalhour >= 10 ? totalhour : ('0' + totalhour)) +':'+(totalMinu >= 10 ? totalMinu : ('0' + totalMinu));

        }

        return total;
    }

    function fomatStatusMeeting(status) {
        switch(status) {
            case '0':
                return '<i class="actionmeeting" style="color: #006600">Đang diễn ra</i>';
            case '1':
                return '<i class="actionmeeting" style="color: #184785 ">Sắp diễn ra</i>';
            case '2':
                return '<i>Chưa diễn ra</i>';
            case '3':
                return '<i class="actionmeeting" style="color: #A9A9A9 ">Đã kết thúc</i>';
            case '4':
                return '<i class="actionmeeting" style="color: #DF0029 ">Bắt đầu cuộc họp!</i>';
            default:
                return '';
        }
    }

    function getNumberOfParticipant(participant) {
        if (!!!participant) return '';
        // console.log((participant.match(/,/g) || []).length);
        return (participant.match(/,/g) || []).length -1;
    }

    function loadTableMeetingSchedule() {
        var RegisterIDtable    = '';
        var MeetingHostIDtable = '';
        var RoomMeetingtable   = '';
        var Participanttable   = '';
        var TimeMeetingTimeFrom= '';
        $('#select-actionmeeting').selectpicker('val', '1');
        var now          = new Date();
        var moth         = now.getMonth() + 1;
        var day          = now.getDate();
        var lastDate     = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        var lastDay      = lastDate.getDate();
        var test5         = (day >= 10 ? day : ('0' + day)) + '/' + (moth >= 10 ? moth : ('0' + moth)) + '/' + now.getFullYear();
        var initDateEnd         = (lastDay >= 10 ? lastDay : ('0' + lastDay)) + '/' + (moth >= 10 ? moth : ('0' + moth)) + '/' + now.getFullYear();
        var arMeetingDatetable   = $('#date-timemeeting-input').val(test5);
        var arMeetingFDatetable   = $('#date-ftimemeeting-input').val(initDateEnd);
        var arryMeetingFDatetable = initDateEnd.split('/');
        var arryMeetingBDatetable = test5.split('/');
        var MeetingDatetable     = arryMeetingBDatetable[2]+'-'+arryMeetingBDatetable[1]+'-'+arryMeetingBDatetable[0];
        var Actionmeetingtable = $('#select-actionmeeting option:selected').val();
        var MeetingFDatetable     = arryMeetingFDatetable[2]+'-'+arryMeetingFDatetable[1]+'-'+arryMeetingFDatetable[0];

        var arrResult = {};

        arrResult = GetAllMeetingSchedules(RegisterIDtable,MeetingHostIDtable,RoomMeetingtable,MeetingDatetable,MeetingFDatetable,Actionmeetingtable,Participanttable,TimeMeetingTimeFrom);
        loadDataListMetting(arrResult);
    }

    function loadDataListMetting(arrResult) {
        clearDataTable('tblMeetingList');

        $('#tblMeetingList tbody').html("");

        if (!!!arrResult) {
            return;
        }


        var html = '';

        for (var i = 0, count = arrResult.length; i < count; i++) {
            var total = calulateTotalTime(arrResult[i].MeetingTimeFrom, arrResult[i].MeetingTimeTo);
            // console.log(total)
            absentDate = (arrResult[i].AbsentDate != null) ? arrResult[i].AbsentDate_ : '';
            html += `
                <tr>
                    <td>`+ (i+1) +`</td>
                    <td data-id="`+arrResult[i].ID+`">`+ arrResult[i].Purpose +`</td>
                    <td>`+ arrResult[i].register_user_name +`</td>
                    <td data-idhost=`+ arrResult[i].MeetingHostID +`>`+ arrResult[i].hosting_user_name +`</td>
                    <td style="text-align: center" data-Partici="`+arrResult[i].Participant+`">`+ getNumberOfParticipant(arrResult[i].Participant) +`</td>
                    <td data-roomid="`+arrResult[i].RoomID+`">`+ arrResult[i].Name +`</td>
                    <td style="text-align: center" data-meetingdate="`+ formatDate(arrResult[i].MeetingDate)+`">`+ formatDate(arrResult[i].MeetingDate) +`</td>
                    <td style="text-align: center" data-meetingtimefrom="`+ formatTime(arrResult[i].MeetingTimeFrom) +`">`+ formatTime(arrResult[i].MeetingTimeFrom) +` - `+ formatTime(arrResult[i].MeetingTimeTo) +`</td>
                    <td style="text-align: center" data-meetingtimeto="`+ formatTime(arrResult[i].MeetingTimeTo) +`">`+total+`</td>
                    <td data-actionmeeting="`+ arrResult[i].StatusMeeting +`">` + fomatStatusMeeting(arrResult[i].StatusMeeting) + `</td>
                    <td style='text-align: center'>
                        <button class='btn btn-success btn-xs btn-editmeeting' data-toggle="tooltip" data-control = 'edit' title='Edit'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></button>
                        <button class='btn btn-info btn-xs btn-copymeeting' data-control='copy'><i class='fa fa-copy' data-toggle='tooltip' data-placement='top' title='Copy'></i></button>
                        <button class='btn btn-danger btn-xs btn-delemeeting'" data-toggle="tooltip" title='Delete'><i class='fa fa-times' aria-hidden='true'></i></button>
                    </td>
                </tr>
            `;
        }

        $("#tblMeetingList tbody").append(html);
        /* Action */
        $('#meetingList table tbody tr').click(function() {
            $(this).parent().find('tr').not(this).removeClass('row_select');
            $(this).toggleClass("row_select");
        });
        setTable('tblMeetingList', 10);
        // $('#dataTables-absent-Request thead tr th:eq(0)').removeClass('sorting_asc');
    }

    $(function (){

        $('#date-timemeeting').datetimepicker({
            allowInputToggle: true,
            format: 'DD/MM/YYYY',
            showClear: true,
        });

        $('#date-ftimemeeting').datetimepicker({
            allowInputToggle: true,
            format: 'DD/MM/YYYY',
            showClear: true,
        });

        $('#time-timemeeting').datetimepicker({
            allowInputToggle: true,
            format: 'HH:mm',
            stepping: 1
         });
        $('#ftime-timemeeting').datetimepicker({
            allowInputToggle: true,
            format: 'HH:mm',
            stepping: 1
         });

        $('#meetingDate').datetimepicker({
            allowInputToggle: true,
            format: 'DD/MM/YYYY',
            showClear: true,
        });

        $('#bTimechoose').datetimepicker({
            allowInputToggle: true,
            format: 'HH:mm',
            stepping: 1
         });

        $('#fTimechoose').datetimepicker({
            allowInputToggle: true,
            format: 'HH:mm',
            stepping: 1
         });
        // SearchMeetingRom('','','','');

        $('#btn-search-meeting').unbind().click(function(a) {
            var RegisterIDtable    = '';
            var RoomMeetingtable   = $('#select-roommeeting option:selected').val();
            var MeetingDatetable   = $('#date-timemeeting-input').val();
            var MeetingHostIDtable = '';
            var Actionmeetingtable = $('#select-actionmeeting option:selected').val();
            var Participanttable   = '';
            var TimeMeetingTimeFro = $('#time-timemeeting-input').val();
            var arrtimemeetingfro  = '';
            var TimeMeetingTimeFrom= '';
            var MeetingFDatetable  = $('#date-ftimemeeting-input').val();
            if(!!!TimeMeetingTimeFro){
                TimeMeetingTimeFrom = '';
            }else{
                arrtimemeetingfro  = TimeMeetingTimeFro.split(':');
                TimeMeetingTimeFrom= arrtimemeetingfro[0]+':'+arrtimemeetingfro[1]+':00.0000';
            }

            var arrResult = {};
            var arr      = $('#select-MeetingHostID').parent('div.bootstrap-select').find('ul li.selected');
            var array    = $('#select-nameRegister').parents('div.bootstrap-select').find('ul li.selected');
            var arrayPar = $('#select-Participant').parents('div.bootstrap-select').find('ul li.selected');

            $('#tblMeetingList thead tr').find('th:nth-last-child(11)').addClass('withtable5');
            $('#tblMeetingList thead tr').find('th:nth-last-child(10)').addClass('withtable10');
            $('#tblMeetingList thead tr').find('th:nth-last-child(9)').addClass('withtable10');
            $('#tblMeetingList thead tr').find('th:nth-last-child(8)').addClass('withtable10');
            $('#tblMeetingList thead tr').find('th:nth-last-child(7)').addClass('withtable8');
            $('#tblMeetingList thead tr').find('th:nth-last-child(6)').addClass('withtable9');
            $('#tblMeetingList thead tr').find('th:nth-last-child(5)').addClass('withtable9');
            $('#tblMeetingList thead tr').find('th:nth-last-child(4)').addClass('withtable8');
            $('#tblMeetingList thead tr').find('th:nth-last-child(3)').addClass('withtable8');
            $('#tblMeetingList thead tr').find('th:nth-last-child(2)').addClass('withtable9');

            if (!!!arr) {
                MeetingHostIDtable = '';
            } else {

                var arrSelected = [];

                $.each(arr, function(key, value) {
                  var indexValue = $(this).data('original-index');
                  var seletedValue = $('#select-MeetingHostID option:eq(' + indexValue + ')').val();

                  arrSelected[key] = seletedValue;
                });

                MeetingHostIDtable = arrSelected.join(',');
            }

            if (!!!array) {
                RegisterIDtable = '';
            } else {

                var arrSelectedRegister = [];

                $.each(array, function(key, value) {
                  var indexValueRegister = $(this).data('original-index');
                  var seletedValueRegister = $('#select-nameRegister option:eq(' + indexValueRegister + ')').val();
                  arrSelectedRegister[key] = seletedValueRegister;
                });

                RegisterIDtable = arrSelectedRegister.join(',');
            }

            if (!!!arrayPar) {
                Participanttable = '';
            } else {

                var arrParti = [];

                $.each(arrayPar, function(key, value) {
                  var indexValueParticipant = $(this).data('original-index');
                  var seletedValueRegister = $('#select-Participant option:eq(' + indexValueParticipant + ')').val();
                  arrParti[key] = seletedValueRegister;
                });

                Participanttable = arrParti.join(',');
            }

            if(MeetingDatetable != ''){
                var MeetingDatetable = formatDate1(MeetingDatetable);
            }

            if(MeetingFDatetable != ''){
                var MeetingFDatetable = formatDate1(MeetingFDatetable);
            }

            arrResult = GetAllMeetingSchedules(RegisterIDtable,MeetingHostIDtable,RoomMeetingtable,MeetingDatetable,MeetingFDatetable,Actionmeetingtable,Participanttable,TimeMeetingTimeFrom);

            loadDataListMetting(arrResult);
        });
    });

    function clearDataTable(id) {

        var idTable = '#' + id;

        if ($.fn.DataTable.isDataTable(idTable)) {
          $(idTable).DataTable().clear().destroy();
        }
    }

    function setTable(id, numberRecord) {

        var idTable = '#' + id;

        $(idTable).DataTable({
            // "searching": false,         // Ẩn ô search
            "ordering": true,           // Ẩn sắp xếp các cột
            // "lengthChange": true,      // Ẩn phần chỉnh số lượng hiển thị
            "info": false,              // Ẩn phần info (Ex: Showing 1 to 20 of 100 entries)
            "pageLength": numberRecord,           // Mặc định hiển thị 20
            "columnDefs": [
                { "targets": 'no-sort', "orderable": false}
            ],
            "sDom": '<"row view-filter"<"col-sm-12"<"pull-left"l><"pull-right"f><"clearfix">>>t<"row view-pager"<"col-sm-12"<"text-center"ip>>>',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Tìm kiếm...",
                lengthMenu:" Hiển thị _MENU_ bản ghi ", //lengthMenu:" _MENU_ ",
                paginate: {
                    previous: '‹',
                    next:     '›'
                },
                aria: {
                    paginate: {
                        previous: 'Previous',
                        next:     'Next'
                    }
                }
            }
        });
    }

    function setTableeemploy(id) {

        var idTableemploy = '#' + id;

        $('#' + id).DataTable({
            // "searching": false,         // Ẩn ô search
            "ordering": true,           // Ẩn sắp xếp các cột
            // "lengthChange": true,      // Ẩn phần chỉnh số lượng hiển thị
            "info": false,              // Ẩn phần info (Ex: Showing 1 to 20 of 100 entries)
            "pageLength": 100000,           // Mặc định hiển thị 20
            "columnDefs": [
                { "targets": 'no-sort', "orderable": false}
            ],
            "sDom": '<"row view-filter"<"col-sm-12"<"pull-right"f>>>t',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Tìm kiếm..."

            }
        });
    }

    function resetRoomMeetingchoose(){
        $('#msg-box-modal #savedraft').hide();
        $('#checkRoom').hide();
        $("#meetingDate-input").val("");
        $("#bTimechoose-input").val("");
        $("#fTimechoose-input").val("");
        $("#diffTime0").val("");
        $("#name-infomeeting").val('');
        $('#purpose-infomeeting').val("");
        $("#listMeetingParticipant tbody").html("");
        $('#listMeetingParticipant tbody tr').remove();
        $('#meetingDate').data("DateTimePicker").date(new Date());
        var now          = new Date();
        var Hour         = now.getHours();
        var Minutes      = now.getMinutes()+15;
        var Minuter      = now.getMinutes()+30;
        var test1        = '';
        var test2        = '';
        if(Minutes >= 60){
            var Minutit = Minutes - 60;
            var Minutiter = Minutit + 30;
            var Hourter = now.getHours()+1;
            test1        = (Hourter > 9 ? Hourter : ('0' + Hourter))+':'+(Minutit > 9 ? Minutit : ('0' + Minutit));
            test2        = (Hourter > 9 ? Hourter : ('0' + Hourter))+':'+(Minutiter > 9 ? Minutiter : ('0' + Minutiter));
        }else if(Minuter >= 60){
            var Minutimeter = Minuter - 60;
            var Hourter = now.getHours() + 1;
            var test1        = (Hour > 9 ? Hour : ('0' + Hour))+':'+(Minutes > 9 ? Minutes : ('0' + Minutes));
            var test2        = (Hourter > 9 ? Hourter : ('0' + Hourter))+':'+(Minutimeter > 9 ? Minutimeter : ('0' + Minutimeter));
        }else if(Minutes<60 && Minuter <60){
            var test1        = (Hour > 9 ? Hour : ('0' + Hour))+':'+(Minutes > 9 ? Minutes : ('0' + Minutes));
            var test2        = (Hour > 9 ? Hour : ('0' + Hour))+':'+(Minuter > 9 ? Minuter : ('0' + Minuter));
        }
        $("#meetingRoomchoose").selectpicker('val','').selectpicker('refresh');
        $("#bTimechoose-input").val(test1);
        $("#fTimechoose-input").val(test2);
        $(document).each(function(){
            var bDateTimechoose = $('#bTimechoose-input').val();
            var fDateTimechoose = $('#fTimechoose-input').val();

            $('#diffTime0').val(CalculateTime(bDateTimechoose, fDateTimechoose));
            $('#diffTime0').val(calulateTotalTime(bDateTimechoose, fDateTimechoose));
         });
        $('#SavemeetingRoom').attr("data-control",'');
        $('#cancel').attr("data-control",'');
        $('#meeting-info .ui-draggable-handle .close').attr("data-control",'');
        LoadShowUser();
    }

    $('#bTimechoose-input,#fTimechoose-input').focusout( function(){
        $(document).each(function(){
            var bDateTimechoose = $('#bTimechoose-input').val();
            var fDateTimechoose = $('#fTimechoose-input').val();

            $('#diffTime0').val(CalculateTime(bDateTimechoose, fDateTimechoose));
            $('#diffTime0').val(calulateTotalTime(bDateTimechoose, fDateTimechoose));
         });
    });

    function formatDate1(str, el) {
        if(str=='') return '';
        return str.substring(6,10)+ "-" + str.substring(3,5) + "-" + str.substring(0,2);
    }

    $(document).on('click', '#add-meeting, .btn-editmeeting,.btn-copymeeting,.btn-editdraftmeeting', function(){
        clicked = $(this).attr('data-control');
        // console.log(clicked)
    });

    $(document).on('click', '#SavemeetingRoom', function(){
            var modal           = $("#meeting-info");
            var RegisterID      = ID;
            var dateme          = $("#meetingDate-input").val();
            var datemett        = dateme.split('/');
            var MeetingDate     = datemett[2] +'-'+ datemett[1] +'-'+ datemett[0];
            var MeeTimeFrom     = $("#bTimechoose-input").val().trim();
            var arrabTime       = MeeTimeFrom.split(':');
            var MeetingTimeFrom = arrabTime[0]+':'+arrabTime[1]+':00.0000';
            var MeeTimeTo       = $("#fTimechoose-input").val().trim();
            var arrafTime       = MeeTimeTo.split(':');
            var MeetingTimeTo   = arrafTime[0]+':'+arrafTime[1]+':00.0000';
            var RoomID          = $("#meetingRoomchoose option:selected").val();
            var RomNameID       = $("#meetingRoomchoose option:selected").text();
            var Description     = $("#name-infomeeting").val();
            var Purpose         = $("#purpose-infomeeting").val();
            var Meetingcoderoom = '';
            var now             = new Date();
            var moth            = now.getMonth() + 1;
            var day             = now.getDate();
            var RegisterDate    = now.getFullYear()+ '-' + (moth >= 10 ? moth : ('0' + moth)) + '-' + (day >= 10 ? day : ('0' + day));
            var MeetingHostID   = $("#listMeetingParticipant tbody td input:checked").val();
            var MeetingHostIDname = $("#listMeetingParticipant tbody td input:checked").attr('data-hostname');
            var i = 0;
            var Participant = ',';
            var Participanter = '';
            var checkpDate   = parseDate($("#meetingDate-input").val().trim()).getTime();
            var now          = new Date();
            var moth         = now.getMonth() + 1;
            var day          = now.getDate();
            var test         = (day >= 10 ? day : ('0' + day)) + '/' + (moth >= 10 ? moth : ('0' + moth)) + '/' + now.getFullYear();
            var checkpDate2  = parseDate(test.trim()).getTime();
            var datadatetime = new Date();
            var testtime     = (datadatetime.getHours() >= 10 ? datadatetime.getHours() : ('0' + datadatetime.getHours()))+':'+(datadatetime.getMinutes() >= 10 ? datadatetime.getMinutes() : ('0' + datadatetime.getMinutes()));
            var arraytesttime=testtime.split(':');
            var testtime1    = arraytesttime[0]+':'+arraytesttime[1]+':00.0000';
            $("#listMeetingParticipant tbody tr").each(function() {
                {
                    Participant+= $(this).attr("data-id") +',';
                    Participanter += $(this).attr("data-id") +',';
                }
                i++;
            });

            if(checkpDate < checkpDate2){

                ErrDialog(globalTitleMsg,"Bạn không thể đặt vào ngày này!");
                return false;
            }
            if(arrabTime>=arrafTime){

                ErrDialog(globalTitleMsg,"Thời gian bắt đầu/kết thúc không hợp lệ!");
                return false;
            }

            if(!!!MeetingHostID ||Purpose == '' || MeetingDate ==''|| MeetingTimeFrom ==''|| MeetingTimeTo ==''|| RoomID ==''|| Participant ==''){

                ErrDialog(globalTitleMsg,"Bạn phải điền đầy đủ thông tin!");
                return false;
            }
            if(clicked == 'add' || clicked == 'copy'){
                if(checkpDate == checkpDate2){
                    if( MeeTimeFrom < testtime){

                        ErrDialog(globalTitleMsg,"Thời gian bắt đầu không hợp lệ!");
                        return false;
                    }
                }
                var data = {
                    'RegisterID'   : RegisterID,
                    'Purpose'      : Purpose,
                    'MeetingDate'  : dateme,
                    'MeetingTimeFrom': MeeTimeFrom,
                    'MeetingTimeTo': MeeTimeTo,
                    'RoomID'       : RoomID,
                    'Description'  : Description,
                    'RegisterDate' : RegisterDate,
                    'MeetingHostID': MeetingHostID,
                    'Participant'  :Participant,
                    'RomNameID'    :RomNameID,
                    'Participanter':Participanter,
                    'MeetingHostIDname':MeetingHostIDname,

                };
                $.ajax({
                    url: 'Meeting/meeting_action.php',
                    type: 'POST',
                    dataType: 'json',
                    async: false,
                    data:{
                        action: 'checkRoommeeting',
                        'RegisterID'   : RegisterID,
                        'Purpose'      : Purpose,
                        'MeetingDate'  : MeetingDate,
                        'MeetingTimeFrom': MeetingTimeFrom,
                        'MeetingTimeTo': MeetingTimeTo,
                        'RoomID'       : RoomID,
                        'Description'  : Description,
                        'RegisterDate' : RegisterDate,
                        'MeetingHostID': MeetingHostID,
                        'Participant'  :Participant,
                        'Meetingcoderoom':Meetingcoderoom,
                    },
                    success: function(result){
                        // console.log(result);
                        if(result == '') {
                           $.ajax({
                                url: 'Meeting/meeting_action.php',
                                type: 'POST',
                                dataType: 'text',
                                async: false,
                                data:{
                                    action: 'insertMeeting',
                                    'RegisterID'   : RegisterID,
                                    'Purpose'      : Purpose,
                                    'MeetingDate'  : MeetingDate,
                                    'MeetingTimeFrom': MeetingTimeFrom,
                                    'MeetingTimeTo': MeetingTimeTo,
                                    'RoomID'       : RoomID,
                                    'Description'  : Description,
                                    'RegisterDate' : RegisterDate,
                                    'MeetingHostID': MeetingHostID,
                                    'Participant'  :Participant
                                },
                                success: function(result){
                                    // console.log(result);
                                    if(result == '') {
                                       InfoDialog(globalTitleMsg,'Lưu thành công!');

                                       $('#meeting-info').modal('hide');
                                       loadTableMeetingSchedule();
                                       sendMailReg("addMeeting", data);
                                    }
                                    else alertify.error(result);

                                },
                                error: function (request, status, error) {
                                   alertify.error('Có lỗi xảy ra!', 1.5);
                                }
                            })
                        }
                        else {
                            $('#msg-box-modal #savedraft').show();
                            ConfirmDialog(globalTitleMsg,'Không thể đăng ký phòng họp do lịch họp bị trùng. Bạn có muốn xem trạng thái các phòng họp trong khung giờ họp bạn chọn không không?',function(choose){
                                if(choose == rstYes){
                                    $('#SavemeetingRoom').attr("data-control",'save');
                                    $('#meeting-info').modal('hide');
                                    $('#exampleModalCenter').modal('show');
                                    resetRoomMeeting();
                                    var pDatachoose     = $('#meetingDate-input').val();
                                    var bDateTimechoose = $('#bTimechoose-input').val();
                                    var fDateTimechoose = $('#fTimechoose-input').val();
                                    var Roomidchoose    = $("#meetingRoomchoose").attr("data-id");
                                    var NameRoomidchoose= $("#meetingRoomchoose").val();
                                    var checkSelected   = false;

                                    if(pDatachoose != ''){
                                        $("#datameeting-input").val(pDatachoose);
                                    }
                                    if(bDateTimechoose != ''){
                                        $("#Btimemeeting-input").val(bDateTimechoose);
                                    }
                                    if(fDateTimechoose != ''){
                                        $("#Ftimemeeting-input").val(fDateTimechoose);
                                    }


                                    var save = $('#SavemeetingRoom').attr('data-control');
                                    // console.log(save);
                                    if(save == 'save'){
                                        $('#searchmeeting').click();
                                    }
                                    // $(".table-meeting-info tbody td").remove();
                                }
                            });
                        }
                    },
                    error: function (request, status, error) {
                       alertify.error('Có lỗi xảy ra!', 1.5);
                    }
                })
            }
            else if(clicked == 'editdraft'){
                var Id = $('.btn-editdraftmeeting').closest('tr').find('td:eq(0)').data('id');
                var Meetingcoderoom = Id;
                if(checkpDate == checkpDate2){
                    if( MeeTimeFrom < testtime){

                        ErrDialog(globalTitleMsg,"Thời gian bắt đầu không hợp lệ!");
                    return false;
                    }
                }
                var data = {
                    'RegisterID'   : RegisterID,
                    'Purpose'      : Purpose,
                    'MeetingDate'  : dateme,
                    'MeetingTimeFrom': MeeTimeFrom,
                    'MeetingTimeTo': MeeTimeTo,
                    'RoomID'       : RoomID,
                    'Description'  : Description,
                    'RegisterDate' : RegisterDate,
                    'MeetingHostID': MeetingHostID,
                    'Participant'  :Participant,
                    'RomNameID'    :RomNameID,
                    'Participanter':Participanter,
                    'MeetingHostIDname':MeetingHostIDname,

                };
                $.ajax({
                    url: 'Meeting/meeting_action.php',
                    type: 'POST',
                    dataType: 'json',
                    async: false,
                    data:{
                        action: 'checkRoommeeting',
                        'RegisterID'   : RegisterID,
                        'Purpose'      : Purpose,
                        'MeetingDate'  : MeetingDate,
                        'MeetingTimeFrom': MeetingTimeFrom,
                        'MeetingTimeTo': MeetingTimeTo,
                        'RoomID'       : RoomID,
                        'Description'  : Description,
                        'RegisterDate' : RegisterDate,
                        'MeetingHostID': MeetingHostID,
                        'Participant'  :Participant,
                        'Meetingcoderoom':Meetingcoderoom,
                    },
                    success: function(result){
                        if(result == '') {
                            $.ajax({
                                url: 'Meeting/meeting_action.php',
                                type: 'POST',
                                dataType: 'text',
                                async: false,
                                data:{
                                    action: 'updateDraftMeeting',
                                    'RegisterID' : RegisterID,
                                    'Purpose'      : Purpose,
                                    'MeetingDate': MeetingDate,
                                    'MeetingTimeFrom': MeetingTimeFrom,
                                    'MeetingTimeTo': MeetingTimeTo,
                                    'RoomID': RoomID,
                                    'Description': Description,
                                    'RegisterDate': RegisterDate,
                                    'MeetingHostID': MeetingHostID,
                                    'Participant':Participant,
                                    'Meetingcoderoom':Meetingcoderoom
                                },
                                success: function(result){

                                    if(result == '') {
                                        InfoDialog(globalTitleMsg,'Lưu thành công!');

                                       $('#meeting-info').modal('hide');
                                       getDraftdata();
                                       loadTableMeetingSchedule();
                                       sendMailReg("addMeeting", data);
                                    }
                                    else {
                                        alertify.error(result);

                                    }
                                },
                                error: function (request, status, error) {
                                   alertify.error('Có lỗi xảy ra!', 1.5);
                                }
                            })
                        } else {
                            ConfirmDialog(globalTitleMsg,'Không thể đăng ký phòng họp do lịch họp bị trùng. Bạn có muốn xem trạng thái các phòng họp trong khung giờ họp bạn chọn không không?',function(choose){
                                if(choose == rstYes){
                                    $('#SavemeetingRoom').attr("data-control",'save');
                                    $('#exampleModalCenter #savedraft').hide();
                                    $('#meeting-info').modal('hide');
                                    $('#exampleModalCenter').modal('show');
                                    resetRoomMeeting();
                                    var pDatachoose     = $('#meetingDate-input').val();
                                    var bDateTimechoose = $('#bTimechoose-input').val();
                                    var fDateTimechoose = $('#fTimechoose-input').val();
                                    var Roomidchoose    = $("#meetingRoomchoose").attr("data-id");
                                    var NameRoomidchoose= $("#meetingRoomchoose").val();
                                    var checkSelected   = false;

                                    if(pDatachoose != ''){
                                        $("#datameeting-input").val(pDatachoose);
                                    }
                                    if(bDateTimechoose != ''){
                                        $("#Btimemeeting-input").val(bDateTimechoose);
                                    }
                                    if(fDateTimechoose != ''){
                                        $("#Ftimemeeting-input").val(fDateTimechoose);
                                    }

                                    var save = $('#SavemeetingRoom').attr('data-control');
                                    console.log(save);
                                    if(save == 'save'){
                                        $('#searchmeeting').click();
                                    }
                                }
                            });
                        }
                    },
                    error: function (request, status, error) {
                       alertify.error('Có lỗi xảy ra!', 1.5);
                    }
                })
            }
            else if(clicked == 'edit'){
                var Roomidchooseupd = $("#meetingRoomchoose").attr("data-roomidold");
                var Meetingcoderoom = $("#meetingRoomchoose").attr("data-idcodemeeting");
                var actiontable     = $("#meetingRoomchoose").attr("data-actionmeeting");
                var purposeold      = $("#meetingRoomchoose").attr("data-purposeold");
                var nameold         = $("#meetingRoomchoose").attr("data-nameold");
                var meetingdate     = $("#meetingRoomchoose").attr("data-meetingdate");
                var btimeold        = $("#meetingRoomchoose").attr("data-btimeold");
                var ftimeold        = $("#meetingRoomchoose").attr("data-ftimeold");
                var participantold  = $("#meetingRoomchoose").attr("data-participantold");

                var data = {
                    'RegisterID'   : RegisterID,
                    'Purpose'      : Purpose,
                    'MeetingDate'  : dateme,
                    'MeetingTimeFrom': MeeTimeFrom,
                    'MeetingTimeTo': MeeTimeTo,
                    'RoomID'       : RoomID,
                    'Description'  : Description,
                    'RegisterDate' : RegisterDate,
                    'MeetingHostID': MeetingHostID,
                    'Participant'  :Participant,
                    'RomNameID'    :RomNameID,
                    'Participanter':Participanter,
                    'participantold':participantold,
                    'purposeold'    :purposeold,
                    'meetingdate'   :meetingdate,
                    'Roomidchooseupd':Roomidchooseupd,
                    'btimeold'      :btimeold,
                    'ftimeold'      :ftimeold,
                    'nameold'       :nameold,
                    'MeetingHostIDname':MeetingHostIDname,

                };

                if(actiontable == 0 && Roomidchooseupd != RoomID){

                     ConfirmDialog(globalTitleMsg,"Bạn có muốn thay đổi phòng họp không?",function(changeroom){
                        if(changeroom == rstYes){
                            $.ajax({
                                url: 'Meeting/meeting_action.php',
                                type: 'POST',
                                dataType: 'json',
                                async: false,
                                data:{
                                    action: 'checkRoommeeting',
                                    'RegisterID'   : RegisterID,
                                    'Purpose'      : Purpose,
                                    'MeetingDate'  : MeetingDate,
                                    'MeetingTimeFrom': MeetingTimeFrom,
                                    'MeetingTimeTo': MeetingTimeTo,
                                    'RoomID'       : RoomID,
                                    'Description'  : Description,
                                    'RegisterDate' : RegisterDate,
                                    'MeetingHostID': MeetingHostID,
                                    'Participant'  :Participant,
                                    'Meetingcoderoom':Meetingcoderoom,
                                },
                                success: function(result){
                                    // console.log(result);
                                    if(result == '') {
                                        $.ajax({
                                            url: 'Meeting/meeting_action.php',
                                            type: 'POST',
                                            dataType: 'text',
                                            async: false,
                                            data:{
                                                action: 'insertMeeting',
                                                'RegisterID'   : RegisterID,
                                                'Purpose'      : Purpose,
                                                'MeetingDate'  : MeetingDate,
                                                'MeetingTimeFrom': MeetingTimeFrom,
                                                'MeetingTimeTo': MeetingTimeTo,
                                                'RoomID'       : RoomID,
                                                'Description'  : Description,
                                                'RegisterDate' : RegisterDate,
                                                'MeetingHostID': MeetingHostID,
                                                'Participant'  :Participant
                                            },
                                            success: function(result){
                                                // console.log(result);
                                                if(result == '') {
                                                   InfoDialog(globalTitleMsg,'Lưu thành công!');
                                                   $('#meeting-info').modal('hide');

                                                   loadTableMeetingSchedule();
                                                   sendMailReg("addMeeting", data);
                                                }
                                                else alertify.error(result);

                                            },
                                            error: function (request, status, error) {
                                               alertify.error('Có lỗi xảy ra!', 1.5);
                                            }
                                        })
                                    }
                            else {

                                   ConfirmDialog(globalTitleMsg,'Không thể đăng ký phòng họp do lịch họp bị trùng. Bạn có muốn xem trạng thái các phòng họp trong khung giờ họp bạn chọn không không?',function(choose){
                                        if(choose == rstYes){
                                            $('#SavemeetingRoom').attr("data-control",'save');
                                            $('#meeting-info').modal('hide');
                                             $('#exampleModalCenter #savedraft').hide();
                                            $('#exampleModalCenter').modal('show');
                                            resetRoomMeeting();
                                            var pDatachoose     = $('#meetingDate-input').val();
                                            var bDateTimechoose = $('#bTimechoose-input').val();
                                            var fDateTimechoose = $('#fTimechoose-input').val();
                                            var Roomidchoose    = $("#meetingRoomchoose").attr("data-id");
                                            var NameRoomidchoose= $("#meetingRoomchoose").val();
                                            var checkSelected   = false;

                                            if(pDatachoose != ''){
                                                $("#datameeting-input").val(pDatachoose);
                                            }
                                            if(bDateTimechoose != ''){
                                                $("#Btimemeeting-input").val(bDateTimechoose);
                                            }
                                            if(fDateTimechoose != ''){
                                                $("#Ftimemeeting-input").val(fDateTimechoose);
                                            }

                                            if(clicked == 'add' || clicked == 'copy'){
                                                if(Roomidchoose != '' && NameRoomidchoose != ''){
                                                    $("#roommeeting").val(Roomidchoose).selectpicker('refresh');
                                                    checkSelected = true;
                                                }
                                                if(checkSelected)
                                                {
                                                    $('.table-meeting-info tbody tr td button').removeClass("red");
                                                    $('.table-meeting-info tbody tr td button[data-room = "'+NameRoomidchoose+'"]').addClass("red");
                                                }else {
                                                    $('.table-meeting-info tbody tr td button[data-room = "'+NameRoomidchoose+'"]').removeClass("red");
                                                }
                                            }else if(clicked == 'edit'){
                                                if(Roomidchoose != '' && NameRoomidchoose != ''){
                                                    $("#roommeeting").val('').selectpicker('refresh');
                                                }
                                            }
                                            var save = $('#SavemeetingRoom').attr('data-control');
                                            // console.log(save);
                                            if(save == 'save'){
                                                $('#searchmeeting').click();
                                            }
                                        }
                                    });
                                }
                            },
                            error: function (request, status, error) {
                               alertify.error('Có lỗi xảy ra!', 1.5);
                            }
                        })
                       }
                    });
                }
                else{
                    if(Roomidchooseupd==RoomID && purposeold==Purpose && nameold==Description && meetingdate==dateme &&btimeold==MeeTimeFrom &&  ftimeold==MeeTimeTo && participantold==Participant){
                        alertify.warning('Không có gì mới cả ! ', 1.5);
                    }else{
                        $.ajax({
                            url: 'Meeting/meeting_action.php',
                            type: 'POST',
                            dataType: 'json',
                            async: false,
                            data:{
                                action: 'checkRoommeeting',
                                'RegisterID'   : RegisterID,
                                'Purpose'      : Purpose,
                                'MeetingDate'  : MeetingDate,
                                'MeetingTimeFrom': MeetingTimeFrom,
                                'MeetingTimeTo': MeetingTimeTo,
                                'RoomID'       : RoomID,
                                'Description'  : Description,
                                'RegisterDate' : RegisterDate,
                                'MeetingHostID': MeetingHostID,
                                'Participant'  :Participant,
                                'Meetingcoderoom':Meetingcoderoom,
                            },
                            success: function(result){
                                // console.log(result);
                                if(result == '') {
                                    $.ajax({
                                        url: 'Meeting/meeting_action.php',
                                        type: 'POST',
                                        dataType: 'text',
                                        async: false,
                                        data:{
                                            action: 'updateMeeting',
                                            'RegisterID' : RegisterID,
                                            'Purpose'      : Purpose,
                                            'MeetingDate': MeetingDate,
                                            'MeetingTimeFrom': MeetingTimeFrom,
                                            'MeetingTimeTo': MeetingTimeTo,
                                            'RoomID': RoomID,
                                            'Description': Description,
                                            'RegisterDate': RegisterDate,
                                            'MeetingHostID': MeetingHostID,
                                            'Participant':Participant,
                                            'Meetingcoderoom':Meetingcoderoom
                                        },
                                        success: function(result){

                                            if(result == '') {
                                               InfoDialog(globalTitleMsg,'Lưu thành công!');

                                               $('#meeting-info').modal('hide');
                                               loadTableMeetingSchedule();
                                               if(nameold!=Description && Roomidchooseupd==RoomID && purposeold==Purpose  && meetingdate==dateme &&btimeold==MeeTimeFrom &&  ftimeold==MeeTimeTo && participantold==Participant ){
                                               }
                                               else{
                                                    if(participantold==Participant) {
                                                        if(Roomidchooseupd!=RoomID || purposeold!=Purpose  || meetingdate!=dateme || btimeold!=MeeTimeFrom || ftimeold!=MeeTimeTo){
                                                            sendMailReg("editMeeting", data);
                                                        }
                                                    }else if(participantold!=Participant) {
                                                        if( Roomidchooseupd==RoomID && purposeold==Purpose  && meetingdate==dateme &&btimeold==MeeTimeFrom &&  ftimeold==MeeTimeTo){
                                                            sendMailReg("editMeetingmemberother", data);
                                                        }
                                                        else{
                                                            sendMailReg("editMeetingmemberother", data);
                                                        }
                                                    }
                                               }

                                            }
                                            else {
                                                    alertify.error(result);

                                                }
                                        },
                                        error: function (request, status, error) {
                                           alertify.error('Có lỗi xảy ra!', 1.5);
                                        }
                                    })
                                    }
                            else {

                                    ConfirmDialog(globalTitleMsg,'Không thể đăng ký phòng họp do lịch họp bị trùng. Bạn có muốn xem trạng thái các phòng họp trong khung giờ họp bạn chọn không không?',function(choose){
                                        if(choose == rstYes){
                                            $('#SavemeetingRoom').attr("data-control",'save');
                                            $('#meeting-info').modal('hide');
                                            $('#exampleModalCenter #savedraft').hide();
                                            $('#exampleModalCenter').modal('show');
                                            resetRoomMeeting();
                                            var pDatachoose     = $('#meetingDate-input').val();
                                            var bDateTimechoose = $('#bTimechoose-input').val();
                                            var fDateTimechoose = $('#fTimechoose-input').val();
                                            var Roomidchoose    = $("#meetingRoomchoose").attr("data-id");
                                            var NameRoomidchoose= $("#meetingRoomchoose").val();
                                            var checkSelected   = false;

                                            if(pDatachoose != ''){
                                                $("#datameeting-input").val(pDatachoose);
                                            }
                                            if(bDateTimechoose != ''){
                                                $("#Btimemeeting-input").val(bDateTimechoose);
                                            }
                                            if(fDateTimechoose != ''){
                                                $("#Ftimemeeting-input").val(fDateTimechoose);
                                            }

                                            if(clicked == 'add' || clicked == 'copy'){
                                                if(Roomidchoose != '' && NameRoomidchoose != ''){
                                                    $("#roommeeting").val(Roomidchoose).selectpicker('refresh');
                                                    checkSelected = true;
                                                }
                                                if(checkSelected)
                                                {
                                                    $('.table-meeting-info tbody tr td button').removeClass("red");
                                                    $('.table-meeting-info tbody tr td button[data-room = "'+NameRoomidchoose+'"]').addClass("red");
                                                }else {
                                                    $('.table-meeting-info tbody tr td button[data-room = "'+NameRoomidchoose+'"]').removeClass("red");
                                                }
                                            }else if(clicked == 'edit'){
                                                if(Roomidchoose != '' && NameRoomidchoose != ''){
                                                    $("#roommeeting").val('').selectpicker('refresh');
                                                }
                                            }
                                            var save = $('#SavemeetingRoom').attr('data-control');
                                            // console.log(save);
                                            if(save == 'save'){
                                                $('#searchmeeting').click();
                                            }
                                            // $(".table-meeting-info tbody td").remove();
                                        }
                                    });
                                }
                            },
                            error: function (request, status, error) {
                               alertify.error('Có lỗi xảy ra!', 1.5);
                            }
                        })
                    }
                }
            }
        });

    function LoadDataMeetingInfo(IDmeeting,RoomID,MeetingDate,MeetingTimeFrom,MeetingTimeTo){
        var arrResults = new Array();
        $.ajax({
            url: './Meeting/meeting_action.php',
            type: 'POST',
            dataType: 'json',
            async: false,
            data: {
                action: 'getupdatemeetinginfo',
                IDmeeting : IDmeeting,
                RoomID:RoomID,
                MeetingDate:MeetingDate,
                MeetingTimeFrom:MeetingTimeFrom,
                MeetingTimeTo:MeetingTimeTo
            },
            success: function(arrResult){
                // console.log(arrResult)
                if(arrResult !=''){
                    arrResults = arrResult;
                }
            },
            error: function (request, status, error) {
               alertify.error('Có lỗi xảy ra!', 1.5);
            }
        });
        return arrResults;
    }

    function gettitleEquipmentMeeting(el){
        var control             = el.attr('data-control');
        var modal               = $("#meeting-info");
        var title               = modal.find('.modal-title');
        resetRoomMeetingchoose();
        if (control=='add') {
            title.text('Đăng ký phòng họp');
            $('#meetingRoomchoose').prop( "disabled", false );
            $('#bTimechoose-input').prop( "disabled", false );
            $('#fTimechoose-input').prop( "disabled", false );
            $('#meetingDate-input').prop( "disabled", false );
            $("input[id=purpose-infomeeting]").prop('disabled', false);
            $("textarea[id=name-infomeeting]").prop('disabled', false);
            $('#cancel').attr("data-control",'');
            $('#meeting-info .ui-draggable-handle .close').attr("data-control",'');
        }
        else if (control=='edit' || control=='copy' || control == 'editdraft') {
            console.log(control)
            if(control == 'edit'){
                title.text('Sửa nội dung cuộc họp');
                $('#cancel').attr("data-control",'draftcancel');
                $('#meeting-info .ui-draggable-handle .close').attr("data-control",'draftcancel');
                getModalEquipmentMeeting(el);
            }
            if(control=='editdraft'){
                title.text('Sửa nội dung bản nháp');
                $('#meetingRoomchoose').prop( "disabled", false );
                $('#bTimechoose-input').prop( "disabled", false );
                $('#fTimechoose-input').prop( "disabled", false );
                $('#meetingDate-input').prop( "disabled", false );
                $("input[id=purpose-infomeeting]").prop('disabled', false);
                $("textarea[id=name-infomeeting]").prop('disabled', false);
                $('#cancel').attr("data-control",'');
                $('#meeting-info .ui-draggable-handle .close').attr("data-control",'');
                getModalEquimentMeetingDraft();
            }
            else if(control=='copy'){
                title.text('Sao chép nội dung đăng ký phòng họp');
                $('#cancel').attr("data-control",'');
                $('#meeting-info .ui-draggable-handle .close').attr("data-control",'');
                getModalEquipmentMeeting(el);
            }
            $('#meeting-info').modal({backdrop: 'static', keyboard: false});
            $('#meeting-info').modal('show');

        }
    }
    function getModalEquimentMeetingDraft(){
        $('#meetingRoomchoose').prop( "disabled", false );
        $('#bTimechoose-input').prop( "disabled", false );
        $('#fTimechoose-input').prop( "disabled", false );
        $('#meetingDate-input').prop( "disabled", false );
        $("input[id=purpose-infomeeting]").prop('disabled', false);
        $("textarea[id=name-infomeeting]").prop('disabled', false);
        $('#cancel').attr("data-control",'draftcancel');
        $('#meeting-info .ui-draggable-handle .close').attr("data-control",'draftcancel');
        $('#DraftModalCenter').modal('hide');
        $('body').addClass('modal-open-2');
        $('#meeting-info').modal('show');
        $('#meeting-info').modal({backdrop: 'static', keyboard: false});
        var Id = $('.btn-editdraftmeeting').closest('tr').find('td:eq(0)').data('id');
        var Purpose =  $('.btn-editdraftmeeting').closest('tr').find('td:eq(0)').data('purpose');
        var Description = $('.btn-editdraftmeeting').closest('tr').find('td:eq(0)').data('description');
        var Participanter = $('.btn-editdraftmeeting').closest('tr').find('td:eq(0)').data('participant');
        var arrayParticiter     = Participanter.split(',');
        var MeetingHostID = $('.btn-editdraftmeeting').closest('tr').find('td:eq(0)').data('meetinghostid');
        $("#meeting-info #meetingRoomchoose").attr("data-purposeold",''+Purpose+'');
        $("#meeting-info #meetingRoomchoose").attr("data-nameold",''+Description+'');
        $("#meeting-info #meetingRoomchoose").attr("data-participantold",''+Participant+'');
        $("#meeting-info #meetingRoomchoose").attr("data-idcodemeeting",''+Id+'');

        for (i in arrayParticiter) {
            if(arrayParticiter[i]!=''){
                var Participant         = arrayParticiter[i];
                addParticipant(Participant);
            }
        }
        $('#listMeetingParticipant tr[data-id='+MeetingHostID+'] input[type=radio]').prop("checked", true);
        $("#meeting-info #name-infomeeting").val(Description);
        $("#meeting-info #purpose-infomeeting").val(Purpose);
    }
    function getModalEquipmentMeeting(el){
        var data = $('#tblMeetingList').DataTable().row(el.parents('tr')).data();

        var control             = el.attr('data-control');
        // console.log(control);
        var modal               = $("#meeting-info");
        var title               = modal.find('.modal-title');
        var RoomID              = el.closest('tr').find('td:nth-last-child(6)').data('roomid');
        var IDmeeting           = el.closest('tr').find('td:nth-last-child(10)').data('id');
        var MeetingDatertime    = el.parents('tr').find('td[data-meetingdate]').data('meetingdate');
        var MeetingTimeb        = el.parents('tr').find('td:eq(7)').data('meetingtimefrom');
        var MeetingTimef        = el.parents('tr').find('td:eq(8)').data('meetingtimeto');
        var ActiomTime          = el.parents('tr').find('td[data-actionmeeting]').data('actionmeeting');
        var arraydatemeeting    = MeetingDatertime.split('/');
        var MeetingDate         = arraydatemeeting[2] +'-'+arraydatemeeting[1]+'-'+arraydatemeeting[0];
        var beginTime           = MeetingTimeb.split(':');
        var MeetingTimeFrom     = beginTime[0]+':'+beginTime[1]+':00.0000';
        var funtTime            = MeetingTimef.split(':');
        var MeetingTimeTo       = funtTime[0]+':'+funtTime[1]+':00.0000';
        var now                 = new Date();
        var moth21              = now.getMonth()+1;
        var day21               = now.getDate();
        var day3                = now.getDate()+1;
        var test6               = (day21 >= 10 ? day21 : ('0' + day21)) + '/' + (moth21 >= 10 ? moth21 : ('0' + moth21)) + '/' + now.getFullYear();
        var Hour                = now.getHours();
        var Minutes             = now.getMinutes();
        var test7               =(Hour > 9 ? Hour : ('0' + Hour))+':'+(Minutes >= 10 ? Minutes : ('0' + Minutes));

        var arrResults = new Array();
        arrResults = LoadDataMeetingInfo(IDmeeting,RoomID,MeetingDate,MeetingTimeFrom,MeetingTimeTo);
        // console.log(arrResults)

        var IDmee               = arrResults[0].IDmeeting;
        var Datemee             = arrResults[0].MeetingDate;
        var arraymeetin         = Datemee.split('-');
        var MeetingDate         = arraymeetin[2] +'/'+arraymeetin[1]+'/'+arraymeetin[0];
        var Datemeetimeb        = arrResults[0].MeetingTimeFrom;
        var Datemeetimef        = arrResults[0].MeetingTimeTo;
        var biginTime           = Datemeetimeb.split(':');
        var funTime             = Datemeetimef.split(':');
        var MeetingTimeFrom     = biginTime[0]+':'+biginTime[1];
        var MeetingTimeTo       = funTime[0]+':'+funTime[1];
        var Particiter          = arrResults[0].Participant;
        var arrayParticiter     = Particiter.split(',');
        var MeetingHostID       = arrResults[0].MeetingHostID;

        $("#meeting-info #name-infomeeting").val(arrResults[0].Description);
        $("#meeting-info #purpose-infomeeting").val(arrResults[0].Purpose);

        if(control == 'edit'){
            $("#meeting-info #meetingRoomchoose ").selectpicker('val', arrResults[0].RoomID);
            $("#meeting-info #meetingRoomchoose").attr("data-id",''+arrResults[0].RoomID+'');
            $("#meeting-info #meetingRoomchoose").attr("data-roomidold",''+arrResults[0].RoomID+'');
            $("#meeting-info #meetingRoomchoose").attr("data-purposeold",''+arrResults[0].Purpose+'');
            $("#meeting-info #meetingRoomchoose").attr("data-nameold",''+arrResults[0].Description+'');
            $("#meeting-info #meetingRoomchoose").attr("data-roomnameold",''+arrResults[0].RoomName+'');
            $("#meeting-info #meetingRoomchoose").attr("data-meetingdate",''+MeetingDate+'');
            $("#meeting-info #meetingRoomchoose").attr("data-btimeold",''+MeetingTimeFrom+'');
            $("#meeting-info #meetingRoomchoose").attr("data-ftimeold",''+MeetingTimeTo+'');
            $("#meeting-info #meetingRoomchoose").attr("data-participantold",''+arrResults[0].Participant+'');
            $("#meeting-info #meetingRoomchoose").attr("data-idcodemeeting",''+IDmeeting+'');
            $("#meeting-info #meetingRoomchoose").attr("data-actionmeeting",''+ActiomTime+'');
            $("#meeting-info #meetingDate-input").val(MeetingDate);
            $("#meeting-info #bTimechoose-input").val(MeetingTimeFrom);
            $("#meeting-info #fTimechoose-input").val(MeetingTimeTo);
            $("#meeting-info #diffTime0").val(calulateTotalTime(MeetingTimeFrom, MeetingTimeTo));
        }else if(control == 'copy'){
            $("#meeting-info #meetingRoomchoose").attr("data-id",'');
            $("#meeting-info #meetingRoomchoose").val('');
            $("#meeting-info #meetingRoomchoose").attr("data-idcodemeeting",'');
            // $("#meeting-info #meetingDate-input").val('');
            // $("#meeting-info #bTimechoose-input").val('');
            // $("#meeting-info #fTimechoose-input").val('');
            // $("#meeting-info #diffTime0").val('');
        }

         var arrResuls = {};
        $.ajax({
            url: './Meeting/meeting_action.php',
            type: 'POST',
            dataType:"json",
            async: false,
            data:{
                action: 'getAllUser',
            },
            success: function(arrResult) {
                if(arrResult != ""){
                    arrResuls = arrResult;
                }
            }
        });
        // console.log(arrayParticiter);
        // console.log(arrResuls);
        for (var j = 0; j < arrayParticiter.length; j++) {
            if(arrayParticiter[j] != '') {
                var Participant = '';
                if(control =='edit') {
                    Participant = arrayParticiter[j];
                }
                else if(control == 'copy') {
                    for (var i = 0; i < arrResuls.length; i++) {
                        if(arrResuls[i].participant_id == arrayParticiter[j]){
                            if(arrResuls[i].Deleted === 'F') {
                                Participant = arrayParticiter[j];
                                // console.log(Participant);
                            }
                        }
                    }
                }
                addParticipant(Participant);
            }
        }
        $('#listMeetingParticipant tr[data-id='+MeetingHostID+'] input[type=radio]').prop("checked", true);
    }

    $(document).on('click', '#add-meeting, .btn-editmeeting, .btn-copymeeting,.btn-editdraftmeeting', function(){
        gettitleEquipmentMeeting($(this));
    });

    $(document).on('dblclick', '#tblMeetingList tbody tr', function(){
        var el = $(this).find('.btn-editmeeting');
        if(el.length) gettitleEquipmentMeeting(el);
        el.click();
    });

    function setEventClickEditMeeting(){
        $('.btn-editmeeting').unbind().click(function(te) {
            te.preventDefault();
            resetRoomMeetingchoose();
            var data         = $('#tblMeetingList').DataTable().row($(this).parents('tr')).data();

            var now          = new Date();
            var moth         = now.getMonth() + 1;
            var day          = now.getDate();
            var test         = (day >= 10 ? day : ('0' + day)) + '/' + (moth >= 10 ? moth : ('0' + moth)) + '/' + now.getFullYear();
            var pDatasearchc = $(this).parents('tr').find('td[data-meetingdate]').data('meetingdate');
            var FMeetingsearchc= $(this).parents('tr').find('td[data-meetingtimeto]').data('meetingtimeto');
            var Hour         = now.getHours();
            var Minutes      = now.getMinutes();
            var test1        =(Hour > 9 ? Hour : ('0' + Hour))+':'+(Minutes > 9 ? Minutes : ('0' + Minutes));
            var actioncheck  = $(this).parents('tr').find('td[data-actionmeeting]').data('actionmeeting');

            var boolpDatasearchc = parseDate(pDatasearchc).getTime();
            var booltest = parseDate(test).getTime();
            if(boolpDatasearchc >= booltest){
                if(boolpDatasearchc == booltest && test1 > FMeetingsearchc){
                    $('#checkRoom').unbind().prop( "disabled", true );
                    $('#SavemeetingRoom').unbind().prop( "disabled", true );
                }else{
                    $('#SavemeetingRoom').unbind().prop( "disabled", false);
                    $('#checkRoom').unbind().prop("disabled",false );
                    if(actioncheck == 0){
                        $('#meetingRoomchoose').prop("disabled", true );
                        $('#bTimechoose-input').prop("disabled", true );
                        $('#meetingDate-input').prop("disabled", true );
                        $("input[id=purpose-infomeeting]").unbind().prop('disabled', true);
                        $("textarea[id=name-infomeeting]").unbind().prop('disabled', true);
                    }else{
                        $('#meetingRoomchoose').prop( "disabled", false );
                        $('#meetingDate-input').prop( "disabled", false );
                        $('#bTimechoose-input').prop( "disabled", false );
                        $('#fTimechoose-input').prop( "disabled", false );
                        $("input[id=purpose-infomeeting]").unbind().prop('disabled', false);
                        $("textarea[id=name-infomeeting]").unbind().prop('disabled', false);
                    }

                    $('#checkRoom').unbind().click(function() {
                        $('#meeting-info').modal('hide');
                        $('#exampleModalCenter').modal({backdrop: 'static', keyboard: false});
                        $('#exampleModalCenter').modal('show');
                    });
                }

            }
            else{
                $('#checkRoom').unbind().prop( "disabled", true );
                $('#SavemeetingRoom').unbind().prop( "disabled", true );
            }
        });
    }
    function setEventClickCopyMeeting(){
         $('.btn-copymeeting').unbind().click(function(cop){
            cop.preventDefault();
            resetRoomMeetingchoose();
            $('#checkRoom').prop( "disabled", false );
            $('#SavemeetingRoom').prop( "disabled", false );
            $('#meetingRoomchoose').prop( "disabled", false );
            $('#bTimechoose-input').prop( "disabled", false );
            $('#meetingDate-input').prop( "disabled", false );
            $('#fTimechoose-input').prop( "disabled", false );
            $("input[id=purpose-infomeeting]").prop('disabled', false);
            $("textarea[id=name-infomeeting]").prop('disabled', false);
            $('#checkRoom').click(function() {
                $('#meeting-info').modal('hide');
                $('#exampleModalCenter').modal({backdrop: 'static', keyboard: false});
                $('#exampleModalCenter').modal('show');
            });
            clickchoosemeeting();
         });
    }


    function parseDate(str) {
      var mdy = str.split('/');
      return new Date(mdy[2], mdy[1], mdy[0]);
    }
    $(document).on('click', '.btn-delemeeting', function(){
       var modal   = $("#tblMeetingList");
       var IDdete = $(this).parents('tr').find('td[data-id]').data('id');
       var action = $(this).parents('tr').find('td[data-actionmeeting]').data('actionmeeting');
       var Participantdele = $(this).parents('tr').find('td[data-partici]').data('partici');
       var roomiddele = $(this).parents('tr').find('td[data-roomid]').data('roomid');
       var roomnamedele = $(this).parents('tr').find('td:eq(5)').text();
       var RomNameID       = $("#meetingRoomchoose").val();
       var meetingdatedele = $(this).parents('tr').find('td[data-meetingdate]').data('meetingdate');
       var meetingtimefromdele = $(this).parents('tr').find('td[data-meetingtimefrom]').data('meetingtimefrom');
       var meetingtimetodele = $(this).parents('tr').find('td[data-meetingtimeto]').data('meetingtimeto');
       var Purposedele   =  $(this).parents('tr').find('td:eq(1)').text();
       var MeetingHostIDname = $(this).parents('tr').find('td:eq(3)').text();

       if(action == '0' || action == '3' || action == '4'){

            ErrDialog(globalTitleMsg,"Không thể xóa!");
       }else{

            ConfirmDialog(globalTitleMsg,"Bạn có muốn xóa không?",function(dele){
                if(dele == rstYes) {
                    var data = {
                        'Participantdele':Participantdele,
                        'roomiddele':roomiddele,
                        'roomnamedele':roomnamedele,
                        'meetingdatedele':meetingdatedele,
                        'meetingtimefromdele':meetingtimefromdele,
                        'meetingtimetodele':meetingtimetodele,
                        'Purposedele':Purposedele,
                        'MeetingHostIDname':MeetingHostIDname,
                    };
                     $.ajax({
                        url: './Meeting/meeting_action.php',
                        type: 'POST',
                        dataType: 'text',
                        async: false,
                        data:{
                            action: 'deleteMeeting',
                            'IDdete': IDdete
                        },success: function(result){
                            if (result == '') {
                                // InfoDialog(globalTitleMsg,'Xóa thành công!');
                                loadTableMeetingSchedule();
                                sendMailReg("deleteMeeting", data);
                            }
                            else alertify.error(result);
                        },
                        error: function (request, status, error) {
                           alertify.error('Có lỗi xảy ra!', 1.5);
                        }
                    });
                }
            });
       }

    });

    function sendMailReg(event, data) {

    $.ajax({
        url: './Device/sendMail.php',
        type: 'POST',
        async: false,
        data: {
            event: event,
            data: data
        },
        success: function(result) {
            // console.log(result)
        }
    });
}

$(document).on('click','#msg-box-modal #savedraft,#exampleModalCenter #savedraft',function(){
    var clicked = $(this).attr('data-control');
    console.log(clicked)
    var purpose     = $('#purpose-infomeeting').val();
    var Description = $('#name-infomeeting').val();
    var MeetingHostID   = $("#listMeetingParticipant tbody td input:checked").val();
    var RegisterID      = ID;
    var now             = new Date();
    var moth            = now.getMonth() + 1;
    var day             = now.getDate();
    var RegisterDate    = now.getFullYear()+ '-' + (moth >= 10 ? moth : ('0' + moth)) + '-' + (day >= 10 ? day : ('0' + day));
    var i = 0;
    var Participant = ',';
    $("#listMeetingParticipant tbody tr").each(function() {
        {
            Participant+= $(this).attr("data-id") +',';
        }
        i++;
    });
        if(!!!MeetingHostID){
            MeetingHostID = 0;
        }
        if( purpose != '' && Participant !=''){
                $.ajax({
                    url: 'Meeting/meeting_action.php',
                    type: 'POST',
                    dataType: 'json',
                    async: false,
                    data: {
                        action: 'adddraft',
                        'purpose':purpose,
                        'Description':Description,
                        'MeetingHostID':MeetingHostID,
                        'Participant':Participant,
                        'RegisterDate':RegisterDate,
                        'RegisterID':RegisterID,
                    },
                    success: function(result){
                        if(result == '') {
                            $('#exampleModalCenter').modal('hide');
                            $('#meeting-info').modal('hide');
                            $('#msg-box-modal').hide();
                            getDraftdata();
                            resetRoomMeetingchoose();
                        }
                    },
                    error: function (request, status, error) {
                        alertify.error('Có lỗi xảy ra!', 1.5);
                    }
                })
        }else{
            $('#meeting-info').modal('hide');
        }
    $(this).unbind('click');
});
