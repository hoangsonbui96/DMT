<div class="table-responsive table-timekeeping-detail">
    <table class="table table-striped table-bordered table-hover data-table">
        <thead class="thead-default">
        	<tr>
	            <th colspan="{{$dateexcel}}">
	                Bảng tổng hợp vắng mặt - Tháng {{$month}}
	            </th>
	        </tr>
	        <tr>
			    <th rowspan = "2">STT</th>
			    <th rowspan = "2">Họ và tên</th>
			    <th rowspan = "2">Kiểu vắng mặt</th>
			  	<th colspan = "2" >Tổng</th>
			  	@foreach($datadate as $key=>$timekeeping)
                    @if($key%2==0)
			   		    <th>{{ FomatDateDisplay($timekeeping, "d")}}</th>
                    @endif
			    @endforeach
		  	</tr>
		  	<tr>
			    <th>Số giờ</th>
			    <th>Số lần</th>
			    @foreach($datadate as $key=>$timekeeping)
                    @if($key%2!=0)
                       <th>{{ $timekeeping }}</th>
                    @endif
			    @endforeach
		  	</tr>
        </thead>
        <tbody>
            <?php
                $stt = 1;
            ?>
            @foreach($datauser as $user)
            	<tr>
            		<td rowspan = "4">{{$stt}}</td>
        			<td rowspan = "4" style="width: 10px">{{$user->FullName}}</td>
        			<td>Muộn</td>
        			@foreach($data as $timekeeping)
                        <?php $num = 0;?>
                        @for ($i = 0; $i < count($timekeeping); $i++)
                            @if($timekeeping[$i]->UserID==$user->id)
                                @if($num == 0)
                                    <td>=ROUND(SUM(F{{$stt*4}}:{{$columexcel}}{{$stt*4}})/60,2)</td>
                                    <td>{{ $timekeeping->lateTimes }}</td>
                                @endif
                                <?php $num++?>
                                @if($num == FomatDateDisplay($timekeeping[$i]->Date, "d"))
                                    @if($timekeeping[$i]->compensate == 1)
                                        <td style="width: 3.5px">{{$timekeeping[$i]->late==0?'':$timekeeping[$i]->late }}</td>
                                    @else
                                        <td style="width: 3.5px">{{ ($timekeeping[$i]->weekday == 'T7'|| $timekeeping[$i]->weekday == 'CN'|| $timekeeping[$i]->late==0) ?'':$timekeeping[$i]->late }}</td>

                                    @endif
                                @else
                                    <td></td>
                                    <?php $i--?>
                                @endif
                            @endif
                        @endfor
    			    @endforeach
            	</tr>
            	<tr>
            		<td>Về sớm</td>
        			@foreach($data as $timekeeping)
                        <?php $num = 0;?>
                        @for ($i = 0; $i < count($timekeeping); $i++)
                            @if($timekeeping[$i]->UserID==$user->id)
                                @if($num == 0)
                                    <td>=ROUND(SUM(F{{$stt*4+1}}:{{$columexcel}}{{$stt*4+1}})/60,2)</td>
                                    <td>{{ $timekeeping->soonTimes }}</td>
                                @endif
                                <?php $num++?>
                                @if($num == FomatDateDisplay($timekeeping[$i]->Date, "d"))
                                    @if($timekeeping[$i]->compensate == 1)
                                        <td style="width: 3.5px">{{$timekeeping[$i]->early==0?'':$timekeeping[$i]->early }}</td>
                                    @else
                                        <td style="width: 3.5px">{{ ($timekeeping[$i]->weekday == 'T7'|| $timekeeping[$i]->weekday == 'CN'|| $timekeeping[$i]->early==0) ?'':$timekeeping[$i]->early }}</td>
                                    @endif
                                @else
                                    <td></td>
                                    <?php $i--?>
                                @endif
                            @endif
                        @endfor
                    @endforeach
            	</tr>
            	<tr>
            		<td>Ra ngoài</td>
        			@foreach($data as $timekeeping)
                        <?php $num = 0;?>
                        @for ($i = 0; $i < count($timekeeping); $i++)
                            @if($timekeeping[$i]->UserID==$user->id)
                                @if($num == 0)
                                    <td>=ROUND(SUM(F{{$stt*4+2}}:{{$columexcel}}{{$stt*4+2}})/60,2)</td>
                                    <td>{{ $timekeeping->outTimes  }}</td>
                                @endif
                                <?php $num++?>
                                @if($num == FomatDateDisplay($timekeeping[$i]->Date, "d"))
                                    @if($timekeeping[$i]->compensate == 1)
                                        <td style="width: 3.5px">{{$timekeeping[$i]->out==0?'':$timekeeping[$i]->out  }}</td>
                                    @else
                                        <td style="width: 3.5px">{{ ($timekeeping[$i]->weekday == 'T7'|| $timekeeping[$i]->weekday == 'CN'|| $timekeeping[$i]->out==0) ?'':$timekeeping[$i]->out }}</td>
                                    @endif
                                @else
                                    <td></td>
                                    <?php $i--?>
                                @endif
                            @endif
                        @endfor
                    @endforeach
            	</tr>
            	<tr>
            		<td>Nghỉ (ngày)</td>
        			@foreach($data as $timekeeping)
                        <?php $num = 0;?>
                        @for ($i = 0; $i < count($timekeeping); $i++)
                            @if($timekeeping[$i]->UserID==$user->id)
                                @if($num == 0)
                                    <td>=ROUND(SUM(F{{$stt*4+3}}:{{$columexcel}}{{$stt*4+3}}),2)</td>
                                    <td>{{ $timekeeping->offWorkTimes   }}</td>
                                @endif
                                <?php $num++?>
                                @if($num == FomatDateDisplay($timekeeping[$i]->Date, "d"))
                                    @if($timekeeping[$i]->compensate == 1)
                                        <td style="width: 3.5px">{{$timekeeping[$i]->offWork==0?'':$timekeeping[$i]->offWork  }}</td>
                                    @else
                                        <td style="width: 3.5px">{{ ($timekeeping[$i]->weekday == 'T7'|| $timekeeping[$i]->weekday == 'CN'|| $timekeeping[$i]->offWork==0) ?'':$timekeeping[$i]->offWork }}</td>
                                    @endif
                                @else
                                    <td></td>
                                    <?php $i--?>
                                @endif
                            @endif
                        @endfor
                    @endforeach
            	</tr>
                <?php $stt++ ?>
            @endforeach
        </tbody>
    </table>
    <br/>
    <br/>
    <table class="table table-striped table-bordered table-hover data-table">
        @foreach($note as $row)
        <tr>
            <td></td>
            <td style="width: 10px">{{$row}}</td>
        </tr>
        @endforeach
    </table>
</div>