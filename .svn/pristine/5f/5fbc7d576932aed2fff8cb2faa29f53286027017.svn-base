<div class="modal fade" id="modal-late-soon-list">
    <div class="modal-dialog">
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
                                    <th>ID</th>
                                    <th scope="col">Ngày</th>
                                    <th scope="col">Giờ vào</th>
                                    <th scope="col">Giờ ra</th>
                                    <th scope="col">Đi muộn (h)</th>
                                    <th scope="col">Về sớm (h)</th>
                                </tr>
                            </thead>
                            <div style="display:none" id="addData"></div>
                            <tbody class="t-body" current-page="1" total-page="{{$filterToGetSoonLateTime->lastPages}}">

                                <input type="hidden" id="userId" userId="{{$filterToGetSoonLateTime->userId}}">
								<input type="hidden" id="searchDate" searchDate="{{$filterToGetSoonLateTime->searchDate}}">
								<input type="hidden" id="ODate" ODate="{{$filterToGetSoonLateTime->OfficeDate}}">

                                @php $temp = 0; @endphp
                                @foreach ($filterToGetSoonLateTime as $item)
                                        @php $temp++; @endphp
                                        <tr class="text-center">
                                            <td>{{ $temp }}</td>
                                            <td>{{ $item->UserID }}</td>
                                            <td>{{ isset($item->Date) ? FomatDateDisplay($item->Date, FOMAT_DISPLAY_DAY) : '' }}</td>                                                                                
                                            <td class="">{{ $item->TimeIn }}</td>
                                            <td class="">{{ $item->TimeOut }}</td>                                                          
                                            @if( isset($item->late) && isset($item->soon))
                                                <td>{{ $item->late }}</td>
                                                <td>{{ $item->soon }}</td>
                                            @elseif (isset($item->late))
                                                <td>{{ $item->late }}</td>
                                                <td></td>
                                            @elseif (isset($item->soon))
                                                <td></td>
                                                <td>{{ $item->soon }}</td>
                                            @endif                                    
                                        </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pos-abs">
                            {!! $filterToGetSoonLateTime->links() !!}
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
        var Type = 2

        $(".page-item").removeClass('active');
        $(this).addClass('active');

        $.ajax({
            url: "{{ route('admin.leave.unregistered_list') }}",
            type:'POST',
            data:{
                pageNums:pageNums,
                Type:Type,
                date:date,
                OfficeDate:OfficeDate,
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
                `
                $.each(data.data, function(index, value){
                    itemNum++;
                    var lateSoon = ""
                    if(value.late != null && value.soon != null){
                        lateSoon = `<td>${value.late}</td><td>${value.soon}</td>`
                    }else if(value.late != null){
                        lateSoon = `<td>${value.late}</td><td></td>`
                    }else if(value.soon != null){
                        lateSoon = `<td></td><td>${value.soon}</td>`
                    }

                    output += `
                    <tr class="data-table text-center">
                        <td>${itemNum}</td>
                        <td>${value.UserID}</td>
                        <td>${value.Date}</td>
                        <td>${value.TimeIn}</td>
                        <td>${value.TimeOut}</td>
                        ${lateSoon}
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
            url: "{{ route('admin.leave.unregistered_list') }}",
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
                `

                $.each(data.data, function(index, value){
                    itemNum++;
                    var lateSoon = ""
                    if(value.late != null && value.soon != null){
                        lateSoon = `<td>${value.late}</td><td>${value.soon}</td>`
                    }else if(value.late != null){
                        lateSoon = `<td>${value.late}</td><td></td>`
                    }else if(value.soon != null){
                        lateSoon = `<td></td><td>${value.soon}</td>`
                    }

                    output += `
                    <tr class="data-table text-center">
                        <td>${itemNum}</td>
                        <td>${value.UserID}</td>
                        <td>${value.Date}</td>
                        <td>${value.TimeIn}</td>
                        <td>${value.TimeOut}</td>
                        ${lateSoon}
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
            url: "{{ route('admin.leave.unregistered_list') }}",
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
                `
                $.each(data.data, function(index, value){
                    itemNum++;
                    var lateSoon = ""
                    if(value.late != null && value.soon != null){
                        lateSoon = `<td>${value.late}</td><td>${value.soon}</td>`
                    }else if(value.late != null){
                        lateSoon = `<td>${value.late}</td><td></td>`
                    }else if(value.soon != null){
                        lateSoon = `<td></td><td>${value.soon}</td>`
                    }

                    output += `
                    <tr class="data-table text-center">
                        <td>${itemNum}</td>
                        <td>${value.UserID}</td>
                        <td>${value.Date}</td>
                        <td>${value.TimeIn}</td>
                        <td>${value.TimeOut}</td>
                        ${lateSoon}
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