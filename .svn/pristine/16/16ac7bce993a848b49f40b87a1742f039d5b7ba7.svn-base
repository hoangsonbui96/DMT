<div>
     
     @if($check_status == 'Approve')
     Gửi {{ $GENDER }} {{$Name}} đơn của bạn đã được duyệt với nội dung như sau: <br/><br/>
     @elseif($check_status == 'Reject')
     Gửi {{ $GENDER }} {{$Name}} đơn của bạn đã bị từ chối với nội dung như sau: <br/><br/>
     @else
     {{ $Header }}<br/><br/>
     {{ $GENDER }} {{$Name}}  xin phép được đăng ký thêm/thay đổi thiết bị với nội dung như sau:<br/>
     @endif
     
     <table width='100%' style='margin:30px 30px 20px 0;border-collapse:collapse;position: relative;' class='emaltt'>
          @if($check_status == 'Approve')
               <tr>
                    <th style='padding: 8px;border: 1px solid #dddddd'>Mã đơn</th>
                    <th style='padding: 8px;border: 1px solid #dddddd'>Loại thay đổi</th>
                    <th style='padding: 8px;border: 1px solid #dddddd'>Loại thiết bị</th>
                    <th style='padding: 8px;border: 1px solid #dddddd'>Tên thiết bị đổi</th>
                    <th style='padding: 8px;border: 1px solid #dddddd'>Tên thiết bị mới</th>
                    <th style='padding: 8px;border: 1px solid #dddddd'>Số lượng</th>
                    <th style='padding: 8px;border: 1px solid #dddddd'>lý do</th>
               </tr>
               @foreach($array as $srow)
               <tr>
                    <td style='padding: 8px;border: 1px solid #dddddd'>{{$id}}</td>
                    @foreach($srow as $value)
                         <td style='padding: 8px;border: 1px solid #dddddd'>{{$value}}</td>
                    @endforeach
               </tr>
               @endforeach
               </table>

          @else
               <tr>
                    <th style='padding: 8px;border: 1px solid #dddddd'>Mã đơn</th>
                    <th style='padding: 8px;border: 1px solid #dddddd'>Loại thay đổi</th>
                    <th style='padding: 8px;border: 1px solid #dddddd'>Loại thiết bị</th>
                    <th style='padding: 8px;border: 1px solid #dddddd'>Tên thiết bị</th>
                    <th style='padding: 8px;border: 1px solid #dddddd'>Số lượng</th>
                    <th style='padding: 8px;border: 1px solid #dddddd'>lý do</th>
               </tr>
               
               @foreach($array as $srow)
               <tr>
                    <td style='padding: 8px;border: 1px solid #dddddd'>{{$id}}</td>
                    @foreach($srow as $value)
                         <td style='padding: 8px;border: 1px solid #dddddd'>{{$value}}</td>
                    @endforeach
               </tr>
               @endforeach
               </table>
               @if($check_status == 'Reject')
               Tôi xin chân thành cảm ơn! <br>
               @endif
          @endif 
     <br>
     Trân trọng
</div>