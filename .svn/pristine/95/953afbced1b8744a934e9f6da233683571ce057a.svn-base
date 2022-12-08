@extends('admin.layouts.default.app')
@section('content')
<style>
    .table.table-bordered th,
    .table.table-bordered td {
        border: 1px solid #bdb9b9 !important;
        vertical-align: middle !important;
        background-color: #fff;
    }

    .SummaryMonth .table.table-bordered tr th {
        background-color: #dbeef4;
    }

    .tbl-dReport .table.table-bordered tr th {
        background-color: #c6e2ff;
    }

    .tbl-top {
        margin-top: 20px;
    }

    .download_cv {
        color: #333;
        cursor: pointer;
    }

    .table {
        min-width: 1260px !important;
    }

    .sort-link {
        cursor: pointer;
    }

    .d-none{
        display: none;
    }
</style>
<section class="content-header">
    @if($jobId != null)
    <h1 class="page-header">@lang('admin.interview.list-candidate') [{{ $job_name }} ]</h1>
    @else
    <h1 class="page-header">@lang('admin.interview.list-candidate')</h1>
    @endif
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <form class="form-inline" id="form_search_candidate">
                <div class="form-group pull-left margin-r-5">
                    <input type="search" class="form-control" id="search"
                        placeholder="@lang('admin.interview.title-search')" name="search"
                        value="{{ isset($request['search']) ? $request['search'] : null }}">
                </div>
                <div class="form-group pull-left margin-r-5">
                    <select class="form-control" id="interview_time" name="interview_time">
                        <option value="">@lang('admin.interview.interview-schedule')</option>
                        <option value="1">Đã có lịch</option>
                        <option value="0">Chưa có lịch</option>
                    </select>
                </div>
                <div class="form-group pull-left margin-r-5">
                    <select class="form-control" id="evaluation" name="evaluation">
                        <option value="">Đánh giá ứng viên</option>
                        <option value="1">Đã đánh giá</option>
                        <option value="0">Chưa đánh giá</option>
                    </select>
                </div>
                <div class="form-group pull-left margin-r-5">
                    <select class="form-control" id="approve" name="approve">
                        <option value="">Kết quả phỏng vấn</option>
                        <option value="0">Không phỏng vấn</option>
                        <option value="1">Thông qua phỏng vấn</option>
                        <option value="2">Trượt phỏng vấn</option>
                    </select>
                </div>
                @if($jobId != null)
                <div class="form-group">
                    <input type="hidden" id="interviewJob" name="interviewJob" value="{{ $jobId }}">
                    <button type="button" class="btn btn-primary btn-search margin-r-5" id="icon_search_candidate"
                        data-job="{{ $jobId }}">@lang('admin.btnSearch')</button>
                </div>
                @else
                <div class="form-group pull-left margin-r-5">
                    <select class="form-control" id="interviewJob" name="interviewJob">
                        <option value="">Chọn công việc</option>
                        @foreach($list_job_interview as $job)
                        <option value="{{ $job->id }}">{{ $job->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-primary btn-search margin-r-5" id="icon_search_candidate"
                        data-job="0">@lang('admin.btnSearch')</button>
                </div>
                @endif
                @if($jobId != null)
                <div class="form-group pull-right">
                    <button type="button" class="btn btn-primary" id="add_candidate"
                        data-job="{{ $jobId }}">@lang('admin.interview.add-candidate')</button>
                    {{-- <button type="button" class="btn btn-success" id="btn-export">
                        <div id="downloading" class="hide">
                            <i class="fa fa-spinner fa-spin"></i>
                            <span>Đang tải</span>
                        </div>
                        <span id="content">Xuất excel</span>
                    </button> --}}
                </div>
                @else
                <div class="form-group">
                    @can('action', $add)
                    <button type="button" class="btn btn-primary" id="add_candidate"
                        data-job="0">@lang('admin.interview.add-candidate')</button>
                    @endcan
                </div>
                @endif
                <div class="clearfix"></div>
                <input type="hidden" id="order_by" name="order_by" value='id'>
                <input type="hidden" id="sort_by" name="sort_by" value='desc'>
            </form>
        </div>
        <div id="loadDataCandidate" class="col-md-12 col-sm-12 col-xs-12">
            <div class="box tbl-top">
                <div class="box-body table-responsive no-padding table-scroll">
                    <table class="table table-bordered table-striped" name="table">
                        <thead class="thead-default">
                            <tr id="tHead">
                                <th>@lang('admin.stt')</th>
                                <th>@lang('admin.partner.full_name')</th>
                                <th>@lang('admin.partner.tel')</th>
                                <th>@lang('admin.partner.email')</th>
                                @if($jobId == null)
                                    <th id="col_name_job">@lang('admin.interview.name-job')</th>
                                @endif
                                <th><span class="sort-link" order-by="Experience"
                                        sort-by='desc'>@lang('admin.interview.experience')<i
                                            class="fa fa-caret-down"></i></span></th>
                                <th><span class="sort-link" order-by="InterviewDate"
                                        sort-by='desc'>@lang('admin.interview.interview-date')<i
                                            class="fa fa-caret-down"></i></span>
                                </th>
                                <th>@lang('admin.absence.remark')</th>
                                <th>@lang('admin.interview.evaluation')</th>
                                <th>@lang('admin.interview.last-update')</th>
                                <th>@lang('admin.action')</th>
                            </tr>
                        </thead>
                        <tbody id="ShowDataCadidate">
                            @php $temp = 0 @endphp
                            @foreach($candidates as $candidate)
                            @php
                            $temp++;
                            @endphp
                            <tr class="even gradeC" data-id="">
                                <td class="text-center" style="width: 20px;">{{ $temp }}</td>
                                <td class="text-left" style="width: 150px;">{{ $candidate->FullName }}</td>
                                <td class="text-center" style="width: 80px;">{{ $candidate->Tel }}</td>
                                <td class="text-left" style="width: 180px;">
                                    {{ $candidate->Email }}
                                </td>
                                @if($jobId == null)
                                    <td class="text-left" style="width: 150px;">{{ $candidate->Name }}</td>
                                @endif
                                <td class="text-center" style="width: 30px;">{{ $candidate->Experience }}</td>
                                <td class="text-center" style="width: 90px;">
                                    @if($candidate->Status == null) chưa xử lý @endif
                                    @if($candidate->Status == 1 && $candidate->Approve == null) {{ FomatDateDisplay($candidate->InterviewDate,'d/m/Y H:i') }} @endif
                                    @if($candidate->Status == 1 && $candidate->Approve == 1) Thông qua phỏng vấn @endif
                                    @if($candidate->Status == 1 && $candidate->Approve == 2) Trượt phỏng vấn @endif
                                    @if($candidate->Status == 2) không<br>phỏng vấn @endif
                                </td>
                                <td class="text-left width12">
                                    {{ $candidate->Note }}
                                </td>
                                <td class="text-left width12">
                                    {{ $candidate->Evaluate }}
                                </td>
                                <td class="text-center" style="width: 90px;">
                                    {{ FomatDateDisplay($candidate->updated_at,'d/m/Y H:i') }}
                                </td>
                                <td class="text-center" @if($jobId == null)style="width: 140px;" @else style="width: 110px;" @endif>
                                    <a href="{{ route('admin.candidates.show_cv', [$candidate->JobID,$candidate->id]) }}" class="popup{{ $candidate->id }}"></a>
                                    <span class="action-col show_cv" data-file="{{ $candidate->CVpath }}" data-candidate="{{ $candidate->id }}">
                                        <i class="fa fa-eye" aria-hidden="true"></i></span>
                                    @can('action',$add)
                                    @if($candidate->InterviewDate == null)
                                    <span class="action-col update edit add_interview"
                                        data-job="{{ $candidate->JobID }}" data-candidate="{{ $candidate->id }}" 
                                        data-candidate-status = {{ $candidate->Status }}><i
                                            class="fa fa-calendar-plus-o" aria-hidden="true"></i></span>
                                    @else
                                    <span class="action-col update edit edit_interview"
                                        data-interview="{{ $candidate->interview_id }}"
                                        data-job="{{ $candidate->JobID }}" data-candidate="{{ $candidate->id }}"><i
                                            class="fa fa-calendar-minus-o" aria-hidden="true"></i></span>
                                    @endif
                                    @endcan
                                    <span class="action-col download_cv" data-file="{{ $candidate->CVpath }}"><i
                                            class="fa fa-download"></i></span>
                                    @can('action',$edit)
                                    <span class="action-col update edit edit_candidate"
                                        data-job="{{ $candidate->JobID }}" data-candidate="{{ $candidate->id }}"><i
                                            class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                    @endcan
                                    @can('action',$delete)
                                    <span class="action-col update delete delete_candidate"
                                        data-job="{{ $candidate->JobID }}" data-candidate="{{ $candidate->id }}"><i
                                            class="fa fa-times" aria-hidden="true"></i></span>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="data_pagination">
                {{ $candidates->links() }}
            </div>
            <div id="popupModal"></div>
        </div>
    </div>
</section>
@endsection
@section('js')
<script>
    var urlListCandidate = "{{ route('admin.candidates.list') }}";
    var urlAddCandidate = "{{ route('admin.candidates.add') }}";
    var urlEditCandidate = "{{ route('admin.candidates.edit') }}";
    var urlDeleteCandidate = "{{ route('admin.candidates.delete') }}";
    var urlAddInterviewShedule = "{{ route('admin.interviewShedule.add') }}";
    var urlEditInterviewShedule = "{{ route('admin.interviewShedule.edit') }}";
    var urlDownloadCV = "{{ route('admin.candidates.download_cv') }}";
    var urlCheckCV = "{{ route('admin.candidates.check_cv') }}";
    var is_busy = false;
    var page = 1;
    var icon_search = document.getElementById("search");

    icon_search.addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            document.getElementById("icon_search_candidate").click();
        }
    });

    $(document).on('click','.show_cv',function(e){
        $('.loadajax').show();
        if(is_busy == true){
            return false;
         }
         let file = $(this).attr('data-file');
         let data_candidate = $(this).attr('data-candidate');
         let formData = new FormData();
         formData.append('file',file);
         is_busy = true;
         jQuery.ajax({
            type: "post",
            url: urlCheckCV,
            data: formData,
            contentType:false,
            processData:false,
            xhrFields: {
                responseType: 'blob'
            },
            success: function (res) {
                if ($.isEmptyObject(res.errors)) {
                    let classes = '.popup' + data_candidate;
                    let url = $(classes).attr('href');
                    var windowName = 'xem cv';
                    var width = 1000;
                    var height = 650;
                    var left = (screen.width - width)/2;
                    var top = (screen.height - height)/2;
                    window.open(url, "windowName", "width="+ width +",height="+height +",left="+left +",right="+left +",top="+ top);
                } else {
                    showErrors(res.errors);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 408) {
                    showErrors('Không tìm thấy file');
                    return;
                }
                showErrors('Không tìm thấy file');
            }
        }).always(function(jqXHR, textStatus) {
            $('.loadajax').hide();
            is_busy = false;
        });
    })

    $('#add_candidate').on('click',function(){
        if(is_busy == true){
            return false;
         }
         let job_id = $(this).attr('data-job');
        is_busy = true;
        ajaxGetServerWithLoader(urlAddCandidate,"POST",{job_id:job_id },function(rst){
            $('.loadajax').hide();
            $('#popupModal').html(rst);
            $('#info-candidate').modal('show');
        },function(){
             alert('lỗi');
        });
        is_busy = false;
    });

    $(document).on('click','.edit_candidate',function(){
        if(is_busy == true){
            return false;
         }
         let id = $(this).attr('data-candidate');
         is_busy = true;
        ajaxGetServerWithLoader(urlEditCandidate,"POST",{id:id},function(rst){
            $('.loadajax').hide();
            $('#popupModal').html(rst);
            $('#edit-info-candidate').modal('show');
             is_busy = false;
        },function(){
             alert('lỗi');
        });
    })

    $(document).on('click','.delete_candidate',function(){
        let content = "Bạn muốn xóa bản ghi này?";
        let id = $(this).attr('data-candidate');
        let order_by = $('#order_by').val();
        let sort_by = $('#sort_by').val();
        showConfirm(content,function(){
            ajaxGetServerWithLoader(urlDeleteCandidate,"POST",{id:id},function(rst){
                $('.loadajax').hide();
                if ($.isEmptyObject(rst.errors)) {
                    showSuccessConfirm(rst.success,function(){
                        loadCandidate(order_by,sort_by);
                    });
             } else {
                showErrors(rst.errors);
             }
            },function(){
                 alert('lỗi');
            });
        });
    })

    $(document).on('click','#icon_search_candidate',function(){
        $('.loadajax').show();
        if(is_busy == true){
            return false;
         }
        let data = $('#form_search_candidate').serializeArray();
        let job_id = $(this).attr('data-job');
        data.push({name: 'job_id', value: job_id});
        is_busy = true;
        ajaxGetServerWithLoader(urlListCandidate,"GET",data,function(rst){
            $('.loadajax').hide();
            $('#ShowDataCadidate').html(rst.view);
            $('.data_pagination').html(rst.pagination);
            is_busy = false;
        },function(){
             alert('lỗi');
        });
    })

    $(document).on('click','#icon_search_candidate',function(){
        var interviewJob = $("#interviewJob").val();
        var jobId = "{{ $jobId }}";
        const col_name_job = document.querySelector('#col_name_job');
        if(interviewJob != null && jobId == ''){
            col_name_job.classList.add("d-none");
        }
        if(interviewJob == ''){
            col_name_job.classList.remove("d-none");
        }
    })

    $(document).on('click','ul.pagination li a',function(e){
        e.preventDefault();
        if(is_busy == true){
            return false;
         }
         let page = $(this).attr('href').split('page=')[1];
         let order_by = $('#order_by').val();
         let sort_by = $('#sort_by').val();
         is_busy = true;
        loadCandidate(order_by,sort_by,page);
    });

      $(document).on('click','.download_cv',function(e){
        $('.loadajax').show();
        if(is_busy == true){
            return false;
         }
         let file = $(this).attr('data-file');
         let formData = new FormData();
         formData.append('file',file);
         is_busy = true;
         jQuery.ajax({
            type: "post",
            url: urlDownloadCV,
            data: formData,
            contentType:false,
            processData:false,
            xhrFields: {
                responseType: 'blob'
            },
            success: function (res) {
                if ($.isEmptyObject(res.errors)) {
                    var a = document.createElement('a');
                    var url = window.URL.createObjectURL(res);
                    a.href = url;
                    a.download = `${file}`;
                    document.body.append(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                } else {
                    showErrors(res.errors);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 408) {
                    showErrors('Không tìm thấy file');
                    return;
                }
                showErrors('Không tìm thấy file');
            }
        }).always(function(jqXHR, textStatus) {
            $('.loadajax').hide();
            is_busy = false;
        });
    });

    $(document).on('click','.add_interview',function(){
        if(is_busy == true){
            return false;
         }
         let job_id = $(this).attr('data-job');
         let candidate_id = $(this).attr('data-candidate');
         var candidate_status = $(this).attr('data-candidate-status');
        is_busy = true;
        ajaxGetServerWithLoader(urlAddInterviewShedule,"POST",{job_id:job_id,candidate_id:candidate_id,candidate_status:candidate_status},function(rst){
            $('#popupModal').html(rst);
            $('#interviewShedule').modal('show');
             is_busy = false;
        },function(){
             alert('lỗi');
        });
    })

    $(document).on('click','.edit_interview',function(){
        if(is_busy == true){
            return false;
         }
         let interview_id = $(this).attr('data-interview');
         let job_id = $(this).attr('data-job');
         let candidate_id = $(this).attr('data-candidate');
        is_busy = true;
        ajaxGetServerWithLoader(urlEditInterviewShedule,"POST",
        {job_id:job_id,candidate_id:candidate_id,interview_id:interview_id},function(rst){
            $('.loadajax').hide();
            $('#popupModal').html(rst);
            $('#interviewSheduleEdit').modal('show');
             is_busy = false;
        },function(){
             alert('lỗi');
        });
    });

    $(document).on('click','.sort-link', function (e){
		$('.loadajax').show();
		$('#tHead').children('th').children('span').not(this).children('i').attr('class','fa fa-caret-down');
		$('#tHead').children('th').children('span').not(this).attr('sort-by','asc');
		$(this).children('i').toggleClass("fa-caret-down fa-caret-up");
		$(this).attr('sort-by',$(this).attr('sort-by')==='desc'?'asc':'desc' );
		let order = $(this).attr('order-by');
		let sort = $(this).attr('sort-by');
        $('#order_by').val(order);
        $('#sort_by').val(sort);
		loadCandidate(order,sort);
    });

    function loadCandidate(order_by,sort_by,page){
        let search = $("#search").val();
        let job_id = $('#icon_search_candidate').attr('data-job');
        let interviewJob = $('#interviewJob').val();
        let interview_time = $('#interview_time').val();
        let evaluation = $('#evaluation').val();
        if(interviewJob != undefined){
           var url = urlListCandidate + '?search=' + search + '&interview_time='+ interview_time + '&evaluation=' + evaluation + '&interviewJob=' + interviewJob + '&job_id='+ job_id + '&order_by='+ order_by +'&sort_by='+ sort_by +'&page=' + page;
        }else{
          var url = urlListCandidate + '?search=' + search +'&interview_time='+ interview_time + 'evaluation=' + evaluation +'&job_id='+ job_id+'&order_by='+ order_by +'&sort_by='+ sort_by +'&page=' + page;
        }
        ajaxGetServerWithLoader(
            url,
            "GET",null,function(rst){
            $('.loadajax').hide();
            $('#ShowDataCadidate').html(rst.view);
            $('.data_pagination').html(rst.pagination);
            is_busy = false;
        },function(){
             alert('lỗi');
        });
    }

    function showSuccessConfirm(data, fncOK) {
        $.alert({
            title: 'Thành công',
            icon: 'fa fa-check',
            type: 'blue',
            content: data + '',
            buttons: {
                confirm: {
                    text: 'OK',
                    btnClass: 'btn-blue',
                    action: function () {
                        if (IsFunction(fncOK)) {
                            fncOK();
                        }
                    }
                }
            }
        });
    };
</script>
<script>
    $(document).mousemove(function(event){
        var load = localStorage.getItem('load');
        if(load == 1){
            document.getElementById("icon_search_candidate").click();
            localStorage.removeItem('load');
            event.preventDefault();
        }
    });
</script>
@endsection
