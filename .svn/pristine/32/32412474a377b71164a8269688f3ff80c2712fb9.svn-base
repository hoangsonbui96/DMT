<div>
    <div>
        {{ $Header }} <br>
        <br>
        @if($Approved == '')
            Họ và tên: {{ $FullName }} -  {{ $Room }} <br>
            Tôi xin phép được {{ $MasterDataValue }} với nội dung như sau:<br>
            Thời gian nghỉ: {{ $viewDay }} <br>
            Lý do: {{ $Reason }} <br>
            Người duyệt: {{$Management}}<br>
            @if($Remark != '')
                Ghi chú: {{ $Remark }} <br>
            @endif
            Tôi xin chân thành cảm ơn. <br>
        @elseif($Approved != '' && $Approved == 1)
            {{$Gender}} {{ $FullName }} - Phòng {{ $Room }} sẽ {{ $MasterDataValue }} với nội dung như sau:<br>
            Lý do: {{ $Reason }} <br>
            Thời gian nghỉ: {{ $viewDay }} <br>
            Người đã duyệt: {{$UpdateBy}}<br>
            @if($Remark != '')
                Ghi chú: {{ $Remark }} <br>
            @endif
            <br>
            Trân trọng.
        @else
            Gửi {{ $FullName }} - Phòng {{ $Room }} - {{ $MasterDataValue }} bị từ chối với lý do sau:<br>
            {{$Comment}}<br>
            <br>
            Trân trọng
        @endif

    </div>

</div>
