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
        margin-top: 25px;
    }

    .inter-view {
        color: #333;
    }
</style>
<section class="content-header">
    <h1 class="page-header">@lang('admin.interview.interview-jobs')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <form class="form-inline" id="form_search_job">
                <div class="form-group pull-left margin-r-5">
                    <input type="search" class="form-control" id="search"
                        placeholder="@lang('admin.interview.title-search')" name="search"
                        value="{{ isset($request['search']) ? $request['search'] : null }}">
                </div>
                <div class="form-group pull-left margin-r-5">
                    <div class="input-group search date" id="">
                        <input type="text" class="form-control datepicker" id="start_date" name="start_date" value=""
                            placeholder="@lang('admin.overtime.stime')">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-th"></span>
                        </div>
                    </div>
                </div>
                <div class="form-group pull-left margin-r-5">
                    <div class="input-group search date" id="">
                        <input type="text" class="form-control datepicker" id="end_date" name="end_date" value=""
                            placeholder="@lang('admin.overtime.etime')">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-th"></span>
                        </div>
                    </div>
                </div>
                <div class="form-group pull-left margin-r-5">
                    <select class="form-control" id="status_job" name="active">
                        <option value="">Trạng thái</option>
                        <option value="0">Không hoạt động</option>
                        <option value="1" selected>Đang hoạt động</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-primary btn-search margin-r-5"
                        id="search_job">@lang('admin.btnSearch')</button>
                </div>
                <div class="form-group">
                    @can('action',$add)
                    <button type="button" class="btn btn-primary" id="add_job">@lang('admin.interview.add-job')</button>
                    @endcan
                </div>
                <div class="clearfix"></div>
            </form>
        </div>
        <div id="loadDataInterviewJob" class="col-md-12 col-sm-12 col-xs-12">
            @component('admin.component.table')
            @slot('columnsTable')
            <tr>
                <th>@lang('admin.stt')</th>
                <th>@lang('admin.interview.name-job')</th>
                <th>@lang('admin.project.start_date')</th>
                <th>@lang('admin.project.end_date')</th>
                <th>@lang('admin.interview.number-applicant')</th>
                <th>@lang('admin.interview.status')</th>
                <th>@lang('admin.interview.last-update')</th>
                <th>@lang('admin.action')</th>
            </tr>
            @endslot
            @if($interviewJobs->isNotEmpty())
            @php
            $temp = 0;
            @endphp
            @slot('dataTable')
            @foreach($interviewJobs as $interviewJob)
            @php
            $temp++;
            @endphp
            <tr class="even gradeC" data-id="">
                <td class="text-center" width="5%">{{ $temp }}</td>
                <td class="text-left" width="30%">{{ $interviewJob->name }}</td>
                <td class="text-center" width="10%">{{ FomatDateDisplay($interviewJob->start_date,'d/m/Y') }}</td>
                <td class="text-center" width="10%">{{ FomatDateDisplay($interviewJob->end_date,'d/m/Y') }}</td>
                <td class="text-center" width="10%">
                    {{ $interviewJob->num_candides }}
                </td>
                @if($interviewJob->active == 1)
                <td class="text-center" width="5%">
                    <input type="checkbox" id="check_active" class="checkActive" data-job="{{ $interviewJob->id }}"
                        value="{{ $interviewJob->active }}" checked>
                </td>
                @else
                <td class="text-center" width="10%">
                    <input type="checkbox" id="check_active" class="checkActive" data-job="{{ $interviewJob->id }}"
                        value="{{ $interviewJob->active }}">
                </td>
                @endif
                <td class="text-center" width="10%">{{ FomatDateDisplay($interviewJob->updated_at,'d/m/Y H:i') }}</td>
                <td class="text-center" width="10%">
                    <a href="{{ route('admin.candidates.list',$interviewJob->id) }}"
                        class="action-col update edit inter-view" data-job="{{ $interviewJob->id }}"><i
                            class="fa fa-address-book-o" aria-hidden="true"></i></a>
                    @can('action',$edit)
                    <span class="action-col update edit edit_job" data-job="{{ $interviewJob->id }}"><i
                            class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                    @endcan
                    @can('action',$delete)
                    <span class="action-col update delete delete_job" rmb-id="0" data-job="{{ $interviewJob->id }}"><i
                            class="fa fa-times" aria-hidden="true"></i></span>
                    @endcan
                </td>
            </tr>
            @endforeach
            @endslot
            @slot('pageTable')
            {{ $interviewJobs->links() }}
            @endslot
            @else
            <tr class="even gradeC">
                <td class='col-7'>Chưa có dữ liệu</td>
            </tr>
            @endif
            @endcomponent
            <div id="popupModal"></div>
        </div>
    </div>
</section>
@endsection
@section('js')
<script>
    $(".date").datepicker();
</script>
<script>
    var urlList = "{{ route('admin.interviewJob.list') }}";
    var urlAjax = "{{ route('admin.interviewJob.add') }}";
    var urlEditAjax = "{{ route('admin.interviewJob.edit') }}";
    var urlDeleteAjax = "{{ route('admin.interviewJob.delete') }}";
    var urlChangeActiveAjax = "{{ route('admin.interviewJob.changeActive') }}";
    var is_busy = false;

    $(document).on('click','#add_job',function(){
         if(is_busy == true){
            return false;
         }
        is_busy = true;
        ajaxGetServerWithLoader(urlAjax,"GET",null,function(rst){
            $('.loadajax').hide();
            $('#popupModal').html(rst);
            $('#job-modal').modal('show');
             is_busy = false;
        },function(){
             alert('lỗi');
        });
    });

    $(document).on('click','.edit_job',function(){
        let job_id = $(this).data('job');
        if(is_busy == true){
            return false;
         }
        is_busy = true;
        ajaxGetServerWithLoader(urlEditAjax,"GET",{job_id:job_id},function(rst){
            $('.loadajax').hide();
            $('#popupModal').html(rst);
            $('#job-modal-edit').modal('show');
             is_busy = false;
        },function(){
             alert('lỗi');
        });
    });

    $(document).on('click','.delete_job',function(){
        let content = "Bạn muốn xóa bản ghi này?";
        let job_id = $(this).data('job');
        showConfirm(content,function(){
            ajaxGetServerWithLoader(urlDeleteAjax,"POST",{job_id:job_id},function(rst){
                $('.loadajax').hide();
                if ($.isEmptyObject(rst.errors)) {
                    showSuccessConfirm(rst.success,function(){
                        loadInterviewJob();
                    });
                    //locationPage(urlList);
             } else {
                showErrors(rst.errors);
             }
            },function(){
                 alert('lỗi');
            });
        });
    });

    $(document).on('click','.checkActive',function(){
        let job_id = $(this).data('job');
        let active = $(this).val();
        if(is_busy == true){
            return false;
         }
        is_busy = true;
        ajaxGetServerWithLoader(urlChangeActiveAjax,"POST",{job_id:job_id,active:active},function(rst){
            $('.loadajax').hide();
            if ($.isEmptyObject(rst.errors)) {
                showSuccessConfirm(rst.success,function(){
                    loadInterviewJob();
                });
                is_busy = false;
         } else {
            showErrors(rst.errors);
         }
        },function(){
             alert('lỗi');
        });
    })

    $(document).on('click','#search_job',function(){
        $('.loadajax').show();
        let data = $('#form_search_job').serializeArray();
        if(is_busy == true){
            return false;
         }
        is_busy = true;
        ajaxGetServerWithLoader(urlList,"GET",data,function(rst){
            $('.loadajax').hide();
            $('#loadDataInterviewJob').html(rst);
             is_busy = false;
        },function(){
             alert('lỗi');
        });
    })

    $(document).on('click','ul.pagination li a',function(e){
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        if(is_busy == true){
            return false;
         }
        is_busy = true;
        loadInterviewJob(page);
    })

    function loadInterviewJob(page){
        let search = $("#search").val();
        let start_date = $('#start_date').val();
        let end_date = $('#end_date').val();
        let active = $('#status_job').val();
        ajaxGetServerWithLoader(
            urlList +'?search='+search + '&start_date=' + start_date + '&end_date=' +end_date + '&page=' +page + '&active=' + active,
            "GET",null,function(rst){
            $('.loadajax').hide();
            $('#loadDataInterviewJob').html(rst);
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
@endsection
