<link rel="stylesheet" type="text/css" href="{{ asset('css/evo-calendar/evo-calendar.css') }}"/>
<link rel="stylesheet" type="text/css" href="{{ asset('css/evo-calendar/evo-calendar.royal-navy.css') }}"/>
<script src="{{ asset('js/evo-calendar/evo-calendar.js') }}"></script>
<style>
    .evo-calendar {
        position: relative;
        background-color: #fbfbfb;
        color: #5a5a5a;
        width: 100%;
        margin: 0 auto;
        overflow: hidden;
        z-index: 999999999;
        -webkit-box-shadow: 0 10px 50px -20px;
        box-shadow: 0 10px 50px -20px;
    }

    .royal-navy th[colspan="7"] {
        color: #3c8dbc;
        background: white;
    }

    .royal-navy .calendar-sidebar {
        background-color: #3c8dbc;
        -webkit-box-shadow: 5px 0 18px -3px #3c8dbc;
        box-shadow: 5px 0 18px -3px #3c8dbc;
    }

    .royal-navy .calendar-events {
        padding-top: 50px;
        padding-bottom: 40px;
        background-color: #3c8dbc;
        color: #fff;
    }

    .event-container > .event-info > p.event-title {
        position: relative;
        font-size: 18px;
        font-weight: 600;
    }

    .royal-navy .calendar-sidebar {
        background-color: #222d32;
    }

    button[data-year-val] {
        display: none !important;
    }
    .royal-navy th[colspan="7"] {
        background-color: transparent;
    }
</style>
<div class="modal modal-noti draggable fade in " role="dialog" id="modalNoti" style="padding-left: 0!important;">
    <div class="modal-dialog ui-draggable" style="width: 100%; margin: 0">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move; padding: 5px 15px">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
            </div>
            <div id="calendar"></div>
        </div>
    </div>
</div>
<script>
    var username = "{{ \Illuminate\Support\Facades\Auth::user()->FullName }}";
    var time_now = moment();
    var myEvents = [];
    var options = {
        theme: 'Royal Navy',
        language: 'vn',
        todayHighlight: true,
        format: 'yyyy-mm-dd',
        eventHeaderFormat: 'dd/mm/yyyy',
        calendarEvents: "",
        firstDayOfWeek: 1,
        sidebarDisplayDefault: true,
        sidebarToggler: true,
        selectYear: time_now.format('yyyy'),
    }

    function callApiNotification(current) {
        const headers = {
            'Authorization': 'Bearer {{ \Illuminate\Support\Facades\Session::get('api-user') }}',
            'Content-type': 'application/json',
        }
        return $.ajax({
            url: "{{ route('admin.NotificationAPIYear') }}",
            method: 'GET',
            data: {
                dateNow: current.format('YYYY/MM/DD'),
            },
            headers: headers,
        })
    }

    function makeEvents(datas) {
        let events = [];
        let events2 = [];
        let events3 = [];
        // Lich nghi
        $(datas.absenceList).each(function (index, item) {
            var event = {
                id: "",
                name: "",
                badge: "",
                date: [],
                description: "",
                type: "event",
                color: ""
            }
            event.id = "absence-" + item.id;
            event.name = "Vắng mặt";
            event.date = [formatWithoutTime(item.SDate), formatWithoutTime(item.EDate)];
            event.badge = moment(item.SDate, "YYYY-MM-DD HH:mm:ss").format("HH:mm") + " - " + moment(item.EDate, "YYYY-MM-DD HH:mm:ss").format("HH:mm");
            event.description = [item.Reason + showApproved(item.Approved)];
            event.color = "#222";
            event.type = 'holiday';
            events.push(event);
        });
        // Lich lam viec
        $(datas.workingshedule).each(function (index, item) {
            var event = {
                id: "",
                name: "",
                badge: "",
                date: [],
                description: "",
                type: "event",
                color: ""
            }
            if (item.in_out === 0 || item.in_out == null) {
                event.id = "workingschedule-" + item.id;
                event.name = "Họp";
                event.color = "#1b8d10";
                event.type = "birthday";
                event.date = formatWithoutTime(item.Date);
                event.badge = moment(item.STime, "HH:mm:ss").format("HH:mm") + " - " + moment(item.ETime, "HH:mm:ss").format("HH:mm");
                if (item.roomsName == null) {
                    event.description = item.Note != null ? [item.Content + '<br>' + item.Address + '<br>Note: ' + item.Note] : [item.Content + '<br>' + item.Address];

                } else {
                    event.description = item.Note != null ? [item.Content + '<br>' + item.roomsName + '<br>Note: ' + item.Note] : [item.Content + '<br>' + item.roomsName];
                }
            } else {
                event.id = "workingschedule-" + item.id;
                event.name = "Công tác";
                event.color = "#ff7575";
                event.date = formatWithoutTime(item.Date);
                event.badge = moment(item.STime, "HH:mm:ss").format("HH:mm") + " - " + moment(item.ETime, "HH:mm:ss").format("HH:mm");
                event.description = item.Note != null ? [item.Content + '<br>' + item.Address + '<br>Note: ' + item.Note] : [item.Content + '<br>' + item.Address];
            }
            events2.push(event);
        })
        // Lich lam them
        $(datas.overtimeList).each(function (index, item) {
            var event = {
                id: "",
                name: "",
                badge: "",
                date: [],
                description: "",
                type: "event",
                color: ""
            }
            event.id = "overtimeList-" + item.id;
            event.name = "Làm thêm";
            event.date = [formatWithoutTime(item.SDate), formatWithoutTime(item.EDate)];
            event.badge = moment(item.STime, "HH:mm:ss").format("HH:mm") + " - " + moment(item.ETime, "HH:mm:ss").format("HH:mm");
            event.description = [item.Content + showApproved(item.Approved)];
            event.color = "#8773c1";
            event.type = "birthday";
            events3.push(event);
        })
        return events.concat(events2, events3);
    }

    function showApproved(value) {
        switch (value) {
            case 0:
                return `<span style="margin:0 1rem;" class='label label-danger'>@lang('admin.not_approved')</span>`;
            case 1:
                return `<span style="margin:0 1rem;" class='label label-success'>@lang('admin.equipment.Approved')</span>`;
            case 2:
                return `<span style="margin:0 1rem;" class='label label-default'>@lang('admin.Cancel')</span>`;
            default:
                return '';
        }
    }

    function showStringDate(date) {
        return moment(date).format('YYYY') === moment().format("YYYY")
            ? moment(date, "YYYY-MM-DD HH:mm:ss").format("DD/MM HH:mm") : moment(date, "YYYY-MM-DD HH:mm:ss").format("DD/MM//YYYY HH:mm");
    }

    function formatWithoutTime(date) {
        return moment(date, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD");
    }

    function drawCalendar(events) {
        let calendar = $('#calendar');
        options.calendarEvents = makeEvents(events);
        return $(calendar).evoCalendar(options);
    }

    function clickEvent (activeEvent) {
        // console.log(activeEvent);
        // const match = activeEvent.id.match(/(\w+)-(\d+)/);
        // if (match == null) return null;
        // switch (match[1]) {
        //     case "workingschedule":
        //         // location.href = ""
        //
        //         break;
        //     case "absence":
        //         break;
        //     case "overtimeList":
        //         break;
        // }
    }

    $(document).ready(async function () {
        if (!getCookie('showNoti')) {
            setCookie('showNoti', true, 2 / 24);
            var myEvents = await callApiNotification(time_now);
            drawCalendar(myEvents.data).on('selectEvent', (event, activeEvent) => clickEvent(activeEvent));
            $('.calendar-year').find('p').text('AKB ' + time_now.format('YYYY'));
            // $('#calendar').addClass('event-hide');
            // $('#calendar').addClass('sidebar-hide');
            $('#modalNoti').modal('show');
        }
    })
</script>
