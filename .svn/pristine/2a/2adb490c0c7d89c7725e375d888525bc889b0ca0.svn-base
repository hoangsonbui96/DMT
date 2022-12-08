<table>
    <thead >
        <tr>
            <th colspan = "{{$columnumber}}">Báo cáo tổng hợp vắng mặt năm {{$Year}}</th>
        </tr>
        <tr>
            <th rowspan = "3">STT</th>
            <th rowspan = "3" style="width: 20px;">Họ và tên</th>
            <th rowspan = "3">Tổng giờ</th>
            <th rowspan = "3">Tổng lần</th>
            <th colspan = "8" style="text-align: center;">Tổng</th>
            @foreach($arrayMoth as $Moth)
                <th colspan = "8" style="text-align: center;">{{$Moth}}</th>
            @endforeach
        </tr>
        <tr>
            @for($i = 0; $i <= count($arrayMoth); $i++)
                @foreach($types as $type)
                    <td colspan = "2">{{$type}}</td>
                @endforeach
            @endfor
        </tr>
        <tr>
            @for($i = 0; $i <= count($arrayMoth); $i++)
                @foreach($types as $type)
                    @foreach($calculations as $calculation)
                        <td>{{$calculation}}</td>
                    @endforeach
                @endforeach
            @endfor
        </tr>
    </thead>
    <tbody>
        <?php
            $stt = 1;
        ?>
        @foreach($User as $user)
            <tr>
                <td>{{$stt}}</td>
                <td>{{$user->FullName}}</td>
                <td>=ROUND(E{{$stt+4}}+G{{$stt+4}}+I{{$stt+4}}+K{{$stt+4}},2)</td>
                <td>=ROUND(F{{$stt+4}}+H{{$stt+4}}+J{{$stt+4}}+L{{$stt+4}},2)</td>
                <td><?php $html='';
                    $so = $stt+4;
                    $total = count($arrayOffWorkH);
                    $l =0;
                    $html.='=';
                    foreach($arrayOffWorkH as $row){
                        if($l+1 == $total)
                            $html.=$row.$so; 
                        else
                            $html.=$row.$so.'+'; 
                        $l++;
                    }
                    echo $html;
                    ?>
                </td>
                <td><?php $html='';
                    $so = $stt+4;
                    $total = count($arrayOffWorkT);
                    $l =0;
                    $html.='=';
                    foreach($arrayOffWorkT as $row){
                        if($l+1 == $total)
                            $html.=$row.$so; 
                        else
                            $html.=$row.$so.'+'; 
                        $l++;
                    }
                    echo $html;
                    ?>
                </td>
                <td>
                    <?php $html='';
                    $so = $stt+4;
                    $total = count($arrayOutH);
                    $l =0;
                    $html.='=';
                    foreach($arrayOutH as $row){
                        if($l+1 == $total)
                            $html.=$row.$so; 
                        else
                            $html.=$row.$so.'+'; 
                        $l++;
                    }
                    echo $html;
                    ?>
                </td>
                <td>
                    <?php $html='';
                    $so = $stt+4;
                    $total = count($arrayOutT);
                    $l =0;
                    $html.='=';
                    foreach($arrayOutT as $row){
                        if($l+1 == $total)
                            $html.=$row.$so; 
                        else
                            $html.=$row.$so.'+'; 
                        $l++;
                    }
                    echo $html;
                    ?>
                </td>
                <td>
                    <?php $html='';
                    $so = $stt+4;
                    $total = count($arrayLateH);
                    $l =0;
                    $html.='=';
                    foreach($arrayLateH as $row){
                        if($l+1 == $total)
                            $html.=$row.$so; 
                        else
                            $html.=$row.$so.'+'; 
                        $l++;
                    }
                    echo $html;
                    ?>
                </td>
                <td>
                    <?php $html='';
                    $so = $stt+4;
                    $total = count($arrayLateT);
                    $l =0;
                    $html.='=';
                    foreach($arrayLateT as $row){
                        if($l+1 == $total)
                            $html.=$row.$so; 
                        else
                            $html.=$row.$so.'+'; 
                        $l++;
                    }
                    echo $html;
                    ?>
                </td>
                <td>
                    <?php $html='';
                    $so = $stt+4;
                    $total = count($arrayEarlyH);
                    $l =0;
                    $html.='=';
                    foreach($arrayEarlyH as $row){
                        if($l+1 == $total)
                            $html.=$row.$so; 
                        else
                            $html.=$row.$so.'+'; 
                        $l++;
                    }
                    echo $html;
                    ?>
                </td>
                <td>
                    <?php $html='';
                    $so = $stt+4;
                    $total = count($arrayEarlyT);
                    $l =0;
                    $html.='=';
                    foreach($arrayEarlyT as $row){
                        if($l+1 == $total)
                            $html.=$row.$so; 
                        else
                            $html.=$row.$so.'+'; 
                        $l++;
                    }
                    echo $html;
                    ?>
                </td>
                @foreach($arraydata[$user->id] as $array)
                    <td>{{$array==0?'':$array}}</td>
                @endforeach
            </tr>
            <?php $stt++;?>
        @endforeach
        
    </tbody>
</table>