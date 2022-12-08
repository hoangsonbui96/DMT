<table>
    <thead >
        <tr>
            <th colspan="4">CÔNG TY TNHH LIÊN DOANH PHẦN MỀM <br> AKB SOFTWARE</th>
            <th colspan="1"></th>
            <th colspan="5">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM <br> Độc lập – Tự do – Hạnh phúc</th>
        </tr>
        <tr>
            <th colspan="4">-------------------</th>
            <th colspan="1"></th>
            <th colspan="5">-------------------</th>
        </tr>
        <tr></tr>
        <tr>
            <th colspan="10">GIẤY ĐỀ NGHỊ THANH TOÁN</th>
        </tr>
        <tr></tr>
        <tr>
            <th colspan="1">Họ tên: </th>
            <th colspan="4">{{ \App\User::find($equipment_offer->OfferUserID)->FullName }}</th>
            <th colspan="2">Thuộc phòng ban: </th>
            <th colspan="3">{{ \App\Room::find(\App\User::find($equipment_offer->OfferUserID)->RoomId)->Name }}</th>
        </tr>
        <tr>
            <th colspan="1">Nội dung: </th>
            <th colspan="9">{{ $equipment_offer->Content }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th colspan="2">Chứng từ</th>
            <th colspan="3" rowspan="2">Diễn giải</th>
            <th colspan="1" rowspan="2">Số lượng</th>
            <th colspan="1" rowspan="2">Tiền hàng <br>(Đồng)</th>
            <th rowspan="2">Thuế VAT (Đồng)</th>
            <th colspan="2" rowspan="2">Giá thanh toán <br>(Đồng)</th>
        </tr>
        <tr>
            <th colspan="1">số</th>
            <th colspan="1">Ngày</th>
        </tr>
        @php
            $total = 0;
        @endphp
        @foreach($equipment_offer_detail as $item)
            @php
                if ($item->Status != 2) {
                    $total += $item->Price;
                }
            @endphp
            <tr>
                <th>{{ $loop->iteration }}</th>
                <th>{{ isset($item->BuyDate) && $item->BuyDate !== '0000-00-00' ? FomatDateDisplay($item->BuyDate, FOMAT_DISPLAY_DAY) : '' }}</th>
                <th colspan="3" >{{ $item->Description }}</th>
                <th colspan="1">{{ $item->Quantity }}</th>
                <th></th>
                <th></th>
                <th colspan="2">@if ($item->Status != 2) {{ number_format($item->Price, 0, '.', ',') }} @else 0 @endif</th>
            </tr>
        @endforeach
        <tr>
            <th></th>
            <th></th>
            <th colspan="3">Cộng</th>
            <th colspan="2"></th>
            <th></th>
            <th colspan="2">{{ number_format($total, 0, '.', ',') }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th colspan="1">Bằng chữ: </th>
            <th colspan="9">{{ ucfirst(\App\Http\Controllers\Admin\AdminController::translateToWords((int)$total)) }} đồng</th>
        </tr>
        <tr>
            <th colspan="7"></th>
            <th colspan="3">Ngày {{ \Illuminate\Support\Carbon::now()->format('d') }} Tháng {{ \Illuminate\Support\Carbon::now()->format('m') }} Năm {{ \Illuminate\Support\Carbon::now()->format('Y') }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th colspan="2">Thủ trưởng duyệt</th>
            <th colspan="1"></th>
            <th colspan="3">Kế toán trưởng</th>
            <th colspan="1"></th>
            <th colspan="3">Người đề nghị</th>
        </tr>
        <tr></tr>
        <tr>
            <th colspan="2">{{ \App\User::find($equipment_offer->ApprovedUserID)->FullName }}</th>
            <th colspan="1"></th>
            <th colspan="3"></th>
            <th colspan="1"></th>
            <th colspan="3">{{ \App\User::find($equipment_offer->OfferUserID)->FullName }}</th>
        </tr>
    </thead>
</table>
