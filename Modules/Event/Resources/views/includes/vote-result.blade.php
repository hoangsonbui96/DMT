<div class="modal draggable fade in detail-modal" id="vote-result-info" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="row" id="event-info">
                    <div class="col-md-5">
                        <h2 class="text-center">@lang('event::admin.event.General_diagram')</h2>
                        <div id="piechart" style="overflow: hidden;"></div>
                        <div class="clear"></div>
                        @can('admin', $menuCheck)
                            <p>
                            <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample"  aria-expanded="false" aria-controls="collapseExample">
                                Nhân viên chưa vote
                            </a>
                            </p>
                            <div class="collapse" id="collapseExample">
                            <div class="card card-body" style="overflow: scroll;height: 200px">
                                <table class="table table-striped table-bordered table-responsive data-table" id="tbl_0">
                                    @foreach($arrIdNotVote as $itemNoteVote)
                                        <tr class="ev-{{ $itemNoteVote->id }}">
                                            <td class="width15"> {!! \App\User::find($itemNoteVote->id)->FullName !!}</td>
                                        </tr>

                                    @endforeach
                                </table>
                            </div>
                            </div>
                        @endcan
                    </div>
                    <div class="col-md-7" id="summary-detail">
                        <h2 class="text-center">@lang('event::admin.vote.Detailed_results')</h2>
                        <div id="detail-total">

                        @can('admin', $menuCheck)
                            @foreach($answers as $answer)
                            <table class="table table-striped table-bordered table-responsive data-table" id="tbl_0">
                                <tbody>
                                    <tr id="tr-{{ $loop->iteration }}">
                                        <td class="text-center width5">Plan {{ $loop->iteration }}</td>
                                        <td colspan="4">{!! $answer->Answer !!}</td>
                                        {{-- <td class="text-center width5 votes" style="{{ $answer->count == $maxArray ? 'background-color: rgb(92, 184, 92); color: rgb(255, 255, 255);' : 'background-color: rgb(152, 152, 152);; color: rgb(255, 255, 255);' }}"><b>{{ $answer->countMale + $answer->countFemale }}</b> votes</td> --}}
                                        <td class="text-center width5 votes"><b>{{ $answer->countMale + $answer->countFemale }}</b> votes</td>
                                    </tr>

                                    @foreach($answer->list as $item)
                                    <tr class="ev-{{ $answer->id }}-{{ $item->id }}">
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="width15">{{ $item->FullName }}</td>
                                        <td class="text-center width5">{{ $item->Gender ? 'Nữ' : 'Nam' }}</td>
                                        <td class="width5">{{ \Carbon\Carbon::parse($item->Birthday)->diffInYears(\Carbon\Carbon::now()) }}</td>
                                        <td>{{ $item->Tel }}</td>
                                        <td class="text-center"><span class="btn btn-danger btn-xs btnDelVote" aid="{{ $answer->id }}" uid="{{ $item->id }}">@lang('admin.btnDelete')</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endforeach
                        @endcan
                        @cannot('admin', $menuCheck)
                            @foreach($answers as $answer)
                            <table class="table table-striped table-bordered table-responsive table-hover data-table" id="tbl_2">
                                <tbody>
                                <tr style="background-color: rgb(92, 184, 92); color: rgb(255, 255, 255);">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td colspan="3"><p>{{ $answer->Answer }}</p></td>
                                    <td class="text-center"><b>{{ $answer->countMale + $answer->countFemale }}</b> votes</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: middle; text-align: center;" rowspan="2">Số lượng</td>
                                    <td>Nam</td>
                                    <td class="text-center">{{ $answer->countMale }}</td>
                                    <td class="text-center" rowspan="2">Độ tuổi trung bình</td>
                                    <td class="text-center" rowspan="2">{{ $answer->avgAge > 0 ? number_format($answer->avgAge, 2) . ' tuổi' : '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Nữ</td>
                                    <td class="text-center">{{ $answer->countFemale }}</td>
                                </tr>
                                </tbody>
                            </table>
                            @endforeach
                        @endcannot
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                {{-- <button type="submit" class="btn btn-primary btn-sm save-form">Save</button> --}}
            </div>
        </div>
    </div>
</div>
<style>
    #tbl_0, #tbl_2{
        margin-bottom: 10px;
    }
</style>
<script type="text/javascript">
    var colors = ["#3366cc","#dc3912","#ff9900","#109618","#990099","#0099c6","#dd4477","#66aa00","#b82e2e","#316395",
            "#994499","#22aa99","#aaaa11","#6633cc","#e67300","#8b0707","#651067","#329262","#5574a6","#3b3eac","#b77322",
            "#16d620","#b91383","#f4359e","#9c5935","#a9c413","#2a778d","#668d1c","#bea413","#0c5922","#743411"];

    $(function () {
        // Jquery draggable (cho phép di chuyển popup)
        $('.modal-dialog').draggable({
            handle: ".modal-header"
        });

        // delete vote
        $('.btnDelVote').click(function () {
            var aid = $(this).attr('aid');
            var uid = $(this).attr('uid');
            showConfirm(confirmMsg, function () {
                ajaxServer('del-vote/' + aid + '/' + uid, 'GET', '', function (data) {
                    $('.loadajax').hide();
                    $('.ev-' + aid + '-' + uid).remove();
                });
            });
        });

        setTimeout(function(){
            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(drawChart);
        }, 500);

        $('tr[id^=tr-]').each(function(i, e) {
            $(this).css({
                'background-color': colors[i],
                'color': '#fff'
            });
        });
    });

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Plan', 'Votes'],
            @foreach($answers as $answer)
                ['{!! strip_tags($answer->Answer) !!}', {{ $answer->Count }}],
            @endforeach
        ]);
        var options = {
            title: '{{ strip_tags($question->Name) }}',
            // legend: { position: 'left', alignment: 'start' },
            width:400,
            height:250,
            chartArea: {
                left: 20,
                top: 40,
                width: '80%',
                height: '80%'
            },
            colors: colors
            // is3D: true,
        };
        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);
    }
</script>
