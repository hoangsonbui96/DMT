<div class="modal fade" id="modal-absence-list">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modal-date"></h4>
			</div>
			<div class="modal-body">
				<div class="box">
					<div class="box-body table-responsive no-padding">
						<table class="table table-bordered table-hover" id="tbl-absent">
							<thead class="thead-default">
								<tr>
									<th scope="col">@lang('admin.stt')</th>
									<th scope="col">Kiểu nghỉ</th>
									<th scope="col">Bắt đầu</th>
									<th scope="col">Kết thúc</th>
									<th scope="col">@lang('admin.times') (h)</th>
									<th scope="col">@lang('admin.absence.reason')</th>
									<th scope="col">@lang('admin.note')</th>
									<th scope="col">@lang('admin.status')</th>
								</tr>
							</thead>
							<div style="display:none" id="addData"></div>
							<tbody class="t-body" current-page="1" total-page="{{$absenceLeave->lastPages}}">

								<input type="hidden" id="userId" userId="{{$absenceLeave->userId}}">
								<input type="hidden" id="searchDate" searchDate="{{$absenceLeave->searchDate}}">
								<input type="hidden" id="ODate" ODate="{{$absenceLeave->OfficeDate}}">
								{{-- <input type="hidden" id="StartDate" StartDate="{{$absenceLeave->StartDate}}">	 --}}

								@foreach($absenceLeave as $item)
									<tr class="text-center">
										<td scope="row">{{ $loop->iteration }}</td>
										<td class="modal-name">{{ $item->Name }}</td>
										<td class="modal-stime">{{ $item->SDate }}</td>
										<td class="modal-etime">{{ $item->EDate }}</td>
										<td class="modal-totaltimeoff">{{ number_format($item->TotalTimeOff, 2) }}</td>
										<td class="modal-reason">{{ $item->Reason }}</td>
										<td class="modal-remark">{{ $item->Remark }}@if($item->errorReport == true) <span style="color: red;">báo cáo không đúng</span> @endif</td>
										<td class="modal-approved">{!! isset($item->Approved) && $item->Approved == 0 ? '<span class="label label-default">Chưa duyệt</label>' : '<span class="label label-success">Đã duyệt</label>' !!}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						<div class="pos-abs">
                            {!! $absenceLeave->links() !!}
                        </div>
					</div>
				</div>
			</div>
			<div class="modal-footer mt-15">
				<button type="button" class="btn btn-default" data-dismiss="modal">@lang('admin.btnCancel')</button>
			</div>
		</div>
	</div>
</div>


<script>
    $('.page-item').click(function(event) {
        event.preventDefault();
        $('.loadajax').hide();
        var pageNums = $(this).attr('page-nums');
        var userId = $('#userId').attr('userId');
        var date = $('#searchDate').attr('searchDate');
        var OfficeDate = $('#ODate').attr('ODate');
        var StartDate = $('#StartDate').attr('StartDate');
        var Type = 2

        $(".page-item").removeClass('active');
        $(this).addClass('active');

        console.log(date);
        $.ajax({
            url: "{{ route('admin.leaveAbsence') }}",
            type:'POST',
            data:{
                pageNums:pageNums,
                Type:Type,
                date:date,
                OfficeDate:OfficeDate,
                StartDate:StartDate,
                UserID:userId,
            },
            success: function(data) {
                console.log(data);
                var itemNum = 0;
                let addData = '';
                let output ='';
                itemNum = (data.current_page - 1) * data.per_page;
                addData +=
                `
                    <input type="hidden" id="userId" userId="${userId}">
                    <input type="hidden" id="searchDate" searchDate="${date}">
                    <input type="hidden" id="ODate" ODate="${OfficeDate}">
                    <input type="hidden" id="StartDate" StartDate="${StartDate}">
                `
                $.each(data.data, function(index, value){
					var approved = '';
					console.log(value.Approved);
					if(value.Approved == 0){
						approved = '<span class="label label-default">Chưa duyệt</label>'
					}else if(value.Approved == 1){
						approved = '<span class="label label-success">Đã duyệt</label>'
					}

					var remark = value.Remark;
					if(remark === null){
						remark = "";
					}else{
						remark = value.Remark
					}
					
                    itemNum++;
                    output += `
                    <tr class="data-table text-center">
                        <td>${itemNum}</td>
                        <td>${value.Name}</td>
                        <td>${value.SDate}</td>
                        <td>${value.EDate}</td>
                        <td>${value.TotalTimeOff.toFixed(2)}</td>
                        <td>${value.Reason}</td>
                        <td>${remark}</td>
                        <td>${approved}</td>
                    </tr>
                    `;
                })
                $('.t-body').attr("current-page", data.current_page);
                $('#addData').html(addData);
                $('.t-body').html(output);
            }
        })
    })

    $('#nextPage').click(function(event) {
        event.preventDefault();
        var page = $('.t-body').attr("current-page");
        var totalPage = $('.t-body').attr("total-page");

        $('.loadajax').hide();
        var pageNums = $(this).attr('page-nums');
        var userId = $('#userId').attr('userId');
        var date = $('#searchDate').attr('searchDate');
        var OfficeDate = $('#ODate').attr('ODate');
        var StartDate = $('#StartDate').attr('StartDate');
        var Type = 2

        if(parseInt(page) < parseInt(totalPage)){
            var nextPage = parseInt(page) + 1;
        }else{
            return false;
        }

        $(".page-item").removeClass('active');
        $('li[page-nums="'+nextPage+'"]').addClass('active')

        $.ajax({
            url: "{{ route('admin.leaveAbsence') }}",
            type:'POST',
            data:{
                pageNums:nextPage,
                Type:Type,
                date:date,
                OfficeDate:OfficeDate,
                StartDate:StartDate,
                UserID:userId,
            },
            success: function(data) {
                console.log(data);
                var itemNum = 0;
                let addData = '';
                let output ='';
                itemNum = (data.current_page - 1) * data.per_page;
                addData +=
                `
                    <input type="hidden" id="userId" userId="${userId}">
                    <input type="hidden" id="searchDate" searchDate="${date}">
                    <input type="hidden" id="ODate" ODate="${OfficeDate}">
                    <input type="hidden" id="StartDate" StartDate="${StartDate}">
                `
                $.each(data.data, function(index, value){
					var approved = '';

					if(value.Approved == 0){
						approved = '<span class="label label-default">Chưa duyệt</label>'
					}else if(value.Approved == 1){
						approved = '<span class="label label-success">Đã duyệt</label>'
					}

					var remark = value.Remark;
					if(remark === null){
						remark = "";
					}else{
						remark = value.Remark
					}

                    itemNum++;
                    output += `
                    <tr class="data-table text-center">
                        <td>${itemNum}</td>
                        <td>${value.Name}</td>
                        <td>${value.SDate}</td>
                        <td>${value.EDate}</td>
                        <td>${value.TotalTimeOff.toFixed(2)}</td>
                        <td>${value.Reason}</td>
                        <td>${remark}</td>
                        <td>${approved}</td>
                    </tr>
                    `;
                })
                $('.t-body').attr("current-page", data.current_page);
                $('#addData').html(addData);
                $('.t-body').html(output);
            }
        })
    })

    $('#previousPage').click(function(event) {
        event.preventDefault();
        var page = $('.t-body').attr("current-page");

        $('.loadajax').hide();
        var pageNums = $(this).attr('page-nums');
        var userId = $('#userId').attr('userId');
        var date = $('#searchDate').attr('searchDate');
        var OfficeDate = $('#ODate').attr('ODate');
        var StartDate = $('#StartDate').attr('StartDate');
        var Type = 2

        if(parseInt(page) > 1){
            var previousPage = parseInt(page) - 1;
        }else{
            return false;
        }

        $(".page-item").removeClass('active');
        $('li[page-nums="'+previousPage+'"]').addClass('active')

        $.ajax({
            url: "{{ route('admin.leaveAbsence') }}",
            type:'POST',
            data:{
                pageNums:previousPage,
                Type:Type,
                date:date,
                OfficeDate:OfficeDate,
                StartDate:StartDate,
                UserID:userId,
            },
            success: function(data) {
                console.log(data);
                var itemNum = 0;
                let addData = '';
                let output ='';
                itemNum = (data.current_page - 1) * data.per_page;
                addData +=
                `
                    <input type="hidden" id="userId" userId="${userId}">
                    <input type="hidden" id="searchDate" searchDate="${date}">
                    <input type="hidden" id="ODate" ODate="${OfficeDate}">
                    <input type="hidden" id="StartDate" StartDate="${StartDate}">
                `
                $.each(data.data, function(index, value){
					var approved = '';

					if(value.Approved == 0){
						approved = '<span class="label label-default">Chưa duyệt</label>'
					}else if(value.Approved == 1){
						approved = '<span class="label label-success">Đã duyệt</label>'
					}

					var remark = value.Remark;
					if(remark === null){
						remark = "";
					}else{
						remark = value.Remark
					}

                    itemNum++;
                    output += `
                    <tr class="data-table text-center">
                        <td>${itemNum}</td>
                        <td>${value.Name}</td>
                        <td>${value.SDate}</td>
                        <td>${value.EDate}</td>
                        <td>${value.TotalTimeOff.toFixed(2)}</td>
                        <td>${value.Reason}</td>
                        <td>${remark}</td>
                        <td>${approved}</td>
                    </tr>
                    `;
                })
                $('.t-body').attr("current-page", data.current_page);
                $('#addData').html(addData);
                $('.t-body').html(output);
            }
        })
    })
</script>