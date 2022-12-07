<div>
    <div>
        {{ $Header }} <br>
        @if($Approved == '')
            Tôi xin phép được làm thêm giờ với nội dung như sau:<br>
        @elseif($Approved != '' && $Approved == 1)
            {{$Gender}} {{ $FullName }} - sẽ làm thêm dự án {{ $ProjectID }} với nội dung như sau:<br>
        @else
            Gửi {{ $FullName }} - {{ $ProjectID }} bị từ chối với lý do sau: {{$Note}}<br>
        @endif

        Họ và tên: {{ $FullName }} - Dự án {{ $ProjectID }}<br>
        Thời gian: {{ $viewTime }} <br>
        Nội dung: {{ $Content }} <br>
        Tôi xin chân thành cảm ơn <br>
        <br>
        Trân trọng
    </div>

</div>
