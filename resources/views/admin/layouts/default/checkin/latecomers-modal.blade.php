<style>
    .selected {
        border: 2px solid #3c8dbc;
        border-radius: 5px;
    }

    .mainClass {
        border: 0.5px solid gray;
        border-radius: 5px;
    }

    .new-main {
        border: 2px solid #3c8dbc !important;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .table.table-bordered th, .table.table-bordered td {
        border: 1px solid #bdb9b9 !important;
        text-align: center;
        vertical-align: middle !important;
        background-color: #fff;
    }

    .flex-row {
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        align-content: center;
        justify-content: center;
        align-items: center;
    }

    .SummaryMonth .table.table-bordered tr th {
        background-color: #dbeef4;
    }

    .tbl-dReport .table.table-bordered tr th {
        background-color: #c6e2ff;
    }

    .tbl-top {
        margin-top: 0px;
    }

    .hover-point:hover {
        background: #c6e2ff;
        cursor: pointer;
    }

    #table_timekeeping {
        margin-top: 20px;
    }

    .modal-dialog-scroll {
        overflow-y: auto !important
    }

    .modal-body-scroll {
        max-height: 600px;
        overflow-y: auto;
    }
</style>

<div class="modal draggable fade in review-modal" id="latecomers-modal" role="dialog" data-backdrop="static">
    <div class="vertical-alignment-helper">
        <div
            class="modal-dialog modal-dialog-scroll modal-dialog-centered vertical-align-center modal-lg ui-draggable ">
            <div class="modal-content drag" style="padding:20px 0">
                <div class="modal-header ui-draggable-handle" style="cursor: move;">
                    <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                    <h4 class="modal-title">{{$title}}</h4>
                </div>
                <div class="table-responsive table-timekeeping modal-body-scroll" style="padding: 0 20px;">
                    <table class="table data-table" id="table_timekeeping">
                        <thead class="thead-default">
                        <tr>
                            <th class="thead-th-custom width12" rowspan="2">Tên nhân viên</th>
                            <th class="thead-th-custom width5" rowspan="2">Thời gian vào</th>
                            <th class="thead-th-custom width5" rowspan="2">Thời gian ra</th>
                            <th class="thead-th-custom width5" rowspan="2">Đi muộn (phút)</th>
                            <th class="thead-th-custom width5" rowspan="2">Về sớm (phút)</th>
                            <th class="thead-th-custom" rowspan="2">Vắng mặt</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($timekeepings as $timekeeping)
                            <tr>
                                <td class="thead-th-custom"
                                    rowspan="1">{{ \App\User::find($timekeeping->UserID)->FullName}}</td>
                                <td class="thead-th-custom" rowspan="1">{{$timekeeping->TimeIn}}</td>
                                <td class="thead-th-custom" rowspan="1">{{$timekeeping->TimeOut}}</td>
                                <td class="thead-th-custom" rowspan="1">
                                    @if ($timekeeping->TimeIn != null && $timekeeping->TimeIn > $timekeeping->STimeOfDay)
                                        {{\Carbon\Carbon::parse((\Carbon\Carbon::parse($timekeeping->TimeIn)->diffInSeconds(\Carbon\Carbon::parse($timekeeping->STimeOfDay))))->format("H:i:s")}}
                                    @endif
                                </td>
                                <td class="thead-th-custom" rowspan="1">
                                    @if ($timekeeping->TimeOut != null && $timekeeping->TimeOut < $timekeeping->ETimeOfDay)
                                        {{\Carbon\Carbon::parse((\Carbon\Carbon::parse($timekeeping->ETimeOfDay)->diffInSeconds(\Carbon\Carbon::parse($timekeeping->TimeOut))))->format("H:i:s")}}
                                    @endif
                                </td>
                                <td class="thead-th-custom">
                                    @if ($timekeeping->absence)
                                        @foreach ($timekeeping->absence as $item)
                                            {{$item->Name}} ({{ substr($item->SDate,11,5) }}
                                            -{{substr($item->EDate,11,5)}})
                                            <br>
                                        @endforeach

                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<script language="javascript">
    $('#latecomers-modal').on('show.bs.modal', function (e) {

    })
    $('#close-user-form').click(e => {
        $(".selected-tr").removeClass("selected-tr");
    })
</script>
