@extends('admin.layouts.default.app')
<link href="{{ asset('css/work-task/style.css') }}" rel="stylesheet">
@push('pageJs')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ajax-bootstrap-select/1.4.5/js/ajax-bootstrap-select.min.js" integrity="sha512-HExUHcDgB9r00YwaaDe5z9lTFmTChuW9lDkPEnz+6/I26/mGtwg58OHI324cfqcnejphCl48MHR3SFQzIGXmOA==" crossorigin="anonymous"></script>
@endpush
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ajax-bootstrap-select/1.4.5/css/ajax-bootstrap-select.min.css" integrity="sha512-k9D6Fzp2d9BxewMk+gYYmlGYxv7DLVC46DiCRv3DrAwBkbjSBZCnhBhWCugLuhkTS36QgQ3h7BwkkkfkJk7cXQ==" crossorigin="anonymous" />
<style>
    a.text-black{
        color: black;
        font-size: 15px;
    }
</style>
@section('content')
<section class="content-header">
    <h1 class="page-header">@lang('admin.task-working.all_project')</h1>
</section>

<section class="content">
    <div id="app">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                @include('admin.includes.taskwork.task-working-search')
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                @component('admin.component.table')
                    @slot('columnsTable')
                        <tr>
                            <th class="width3">@lang('admin.task-working.stt')</th>
                            <th>
                                <a class="text-black" href="javascript:void(0)" onclick="sortColumn(this)" order="NameVi" sort="desc" >
                                    @lang('admin.task-working.project_name')
                                    <i class="fa fa-caret-down"></i>
                                </a>
                            </th>
                            <th>
                                <a class="text-black" href="javascript:void(0)" onclick="sortColumn(this)" order="Customer" sort="desc">
                                    @lang('admin.task-working.customer')
                                    <i class="fa fa-caret-down"></i>
                                </a>
                            </th>
                            <th>
                                <a class="text-black" href="javascript:void(0)" onclick="sortColumn(this)" order="Members" sort="desc">
                                    @lang('admin.task-working.member')
                                    <i class="fa fa-caret-down"></i>
                                </a>
                            </th>
                            <th>
                                <a class="text-black" href="javascript:void(0)" onclick="sortColumn(this)" order="TaskNotFinish" sort="desc">
                                    @lang('admin.task-working.column_unfinished')
                                    <i class="fa fa-caret-down"></i>
                                </a>
                            </th>

                            <th>
                                <a class="text-black" href="javascript:void(0)" onclick="sortColumn(this)" order="TaskWorking" sort="desc">
                                    @lang('admin.task-working.column_working')
                                    <i class="fa fa-caret-down"></i>
                                </a>
                            </th>
                            <th>
                                <a class="text-black" href="javascript:void(0)" onclick="sortColumn(this)" order="TaskFinish" sort="desc">
                                    @lang('admin.task-working.column_finished')
                                    <i class="fa fa-caret-down"></i>
                                </a>
                            </th>
                            <th>
                                <a class="text-black" href="javascript:void(0)" onclick="sortColumn(this)" order="TaskReview" sort="desc">
                                    @lang('admin.task-working.column_review')
                                    <i class="fa fa-caret-down"></i>
                                </a>
                            </th>
                            <th>
                                 <a class="text-black" href="javascript:void(0)" onclick="sortColumn(this)" order="TotalTask" sort="desc">
                                    @lang('admin.task-working.task_total')
                                    <i class="fa fa-caret-down"></i>
                                 </a>
                            </th>
                            <th>
                                 <a class="text-black" href="javascript:void(0)" onclick="sortColumn(this)" order="TotalHours" sort="desc">
                                    @lang('admin.task-working.total_time')
                                    <i class="fa fa-caret-down"></i>
                                 </a>
                            </th>
                            <th>
                                 <a class="text-black" href="javascript:void(0)" onclick="sortColumn(this)" order="Progress" sort="desc">
                                    @lang('admin.task-working.progress')
                                    <i class="fa fa-caret-down"></i>
                                 </a>
                            </th>
                            <th>
                                 <a class="text-black" href="javascript:void(0)" onclick="sortColumn(this)" order="StartDate" sort="desc">
                                    @lang('admin.startDate')
                                    <i class="fa fa-caret-down"></i>
                                 </a>
                            </th>
                            <th>
                                 <a class="text-black" href="javascript:void(0)" onclick="sortColumn(this)" order="EndDate" sort="desc">
                                    @lang('admin.endDate')
                                    <i class="fa fa-caret-down"></i>
                                 </a>
                            </th>
                            <th class="width3"><span style=" font-size: 15px;">@lang('admin.action')</span></th>
                        </tr>
                    @endslot
                    @slot('dataTable')
                        <tr></tr>
                    @endslot
                    @slot('pageTable')
                        <div id="page-selection"></div>
                    @endslot

                @endcomponent

            </div>
        </div>
    </div>
</section>
@endsection
@section('js')
    <script src="{{ asset('js/bootpag.js') }}"></script>
    <script type="text/javascript">
        const is_check_flag = false;
        const ajaxUrl = "{{ route('admin.TaskWorkAdd') }}";
        const newTitle = 'Thêm mới task';
        const updateTitle = 'Đây là tên của Task';
        const headers = {
            'Content-type': 'application/json',
            'Authorization': 'Bearer {{ \Illuminate\Support\Facades\Session::get('api-user') }}'
        };
        $(document).ready(() => {
            $("#paginator").empty().html("");
            $('#btn-search-meeting').click();
            $('#switch-toggle-status').change(function(e) {
                e.preventDefault();
                submitForm($('#list-project-task-form').serializeArray(), 1);
            });

            $('#page-selection').on('page', async  (event, num) => {
                const res = await getAjax($('#list-project-task-form').serializeArray(), num, 'id', 'asc');
                if (!res.success){
                    showErrors(res.error);
                }else{
                    functionSuccess(res.data.data_project, res.data.current);
                }
            });
        });

        $('#list-project-task-form').submit(e => {
            e.preventDefault();
            submitForm($('#list-project-task-form').serializeArray(), 1);
        });

        const getAjax  = async (body, page, order_by, sort_by) => {
            return ajaxGetServerWithLoaderAPI(`{{ route('admin.ApiAllProject') }}/all/${order_by}/${sort_by}?page=${page}`, headers, 'GET', body, () => {}, () => {});
        }

        const submitForm = async (body, page=1, order_by='id', sort_by='asc') => {
            try {
                const response = await getAjax(body, page, order_by, sort_by);
                functionSuccess(response.data.data_project, response.data.current);
                $('#page-selection').bootpag({
                    total: parseInt(response.data.last),
                    page: 1,
                    maxVisible: 4,
                    lastClass: 'last',
                    firstClass: 'first',
                    nextClass: 'next',
                    prevClass: 'prev',
                    next: '›',
                    prev: '‹',
                    first: '«',
                    last: '»',
                    firstLastUse: true,
                    leaps: true,
                    wrapClass: 'pagination',
                    activeClass: 'active',
                    disabledClass: 'disabled'
                });
                if (parseInt(response.data.last) === 1){
                    $('.bootpag').hide();
                }
            } catch (e) {
                showErrors(e.responseJSON.error);
            }
        }

        //Function success when call ajax
        const functionSuccess = (data_return, page_number) => {
            let table = $('table')[0];
            let tbody = $(table).find('tbody');
            tbody.empty();
            let dataHTML = '';
            $.each(data_return , function (index, value) {
                let stt = (parseInt(page_number)-1)*10 + parseInt(index) + 1
                dataHTML += `<tr>`
                dataHTML += `<td class="text-center" name="index">${stt}</td>`
                dataHTML += `<td class="left-important" name="project-name-td">${value.NameVi}</td>`
                dataHTML += `<td class="left-important" name="customer-td">${value.Customer}</td>`
                dataHTML += `<td class="text-center" name="total-members-td"><a href="javascript:void(0)" onclick="openModalMember(${value.id}, this)">${value.Members}</a></td>`
                dataHTML += `<td class="text-center" name="tks-not-finish-td">${value.TaskNotFinish}</td>`
                dataHTML += `<td class="text-center" name="tks-working-td">${value.TaskWorking}</td>`
                dataHTML += `<td class="text-center" name="tks-finish-td">${value.TaskFinish}</td>`
                dataHTML += `<td class="text-center" name="tks-review-td">${value.TaskReview}</td>`
                dataHTML += `<td class="text-center" name="tks-total-td">${value.TaskNotFinish + value.TaskWorking + value.TaskFinish + value.TaskReview }
                            </td>`
                dataHTML += `<td class="text-center" name="tks-total-hours-td">${value.TotalHours}</td>`
                dataHTML += `<td class="text-center" name="tks-progress-td">${value.Progress}</td>`
                dataHTML += `<td class="text-center" name="tks-startdate-td">${dateFormatYMDToDMYY(value.StartDate, '/')}</td>`
                dataHTML += `<td class="text-center" name="tks-enddate-td">${dateFormatYMDToDMYY(value.EndDate, '/')}</td>`
                dataHTML += `
                         <td class="text-center" name="action">
                              <span class="action-col update edit" item-id="${value.id}" data-toggle="tooltip" data-placement="left" title="Chi tiết">
                               <i class="fa fa-eye" aria-hidden="true"></i>
                              </span>
                        </td>`
                dataHTML += '</tr>'
            })
            tbody.html(dataHTML)
            detailTD()
            // $('#paginator').find('ul').attr('class', 'pagination')
        }

        //display error
        // const functionError = (data) => {
        //     showErrors(data.responseJSON.error);
        // }

        //function click detail td edit
        const detailTD = () =>{
            const viewSpans = $('td[name="action"]').find('span.action-col.update.edit')
            viewSpans.each(function () {
                $(this).click(() => {
                    let id = $(this).attr('item-id')
                    let url =  "{{ route('admin.TaskWorkDetail', ':id') }}"
                    url = url.replace(':id', id)
                    location.href = url
                })
            })
        }

        const openModalMember = (id, e) => {
            ajaxGetServerWithLoader("{{ route('admin.TaskWorkPopupMember') }}", 'GET', null, (response) =>{
                $('#popupModal').html(response);
                $('#modal-list-user').modal('toggle');
                detailMember(id);
                $('#modal-list-user #title-modal').text($($($($(e)).parents()[1]).find("td")[1]).html())
            }, (error) => {
            })
        }

        //function call detail members in a project
        const detailMember = (id) => {
            let taskURL = "{{ route('admin.ApiMembers', ':id') }}"
            taskURL = taskURL.replace(':id', id)
            $.ajax({
                url: taskURL,
                async: false,
                headers: headers,
                success: (res) => {
                    if (res.status_code === 200 && res.success === true){
                        let modal = $('#modal-list-user ')
                        let ul = $(modal).find('ul')
                        $(ul).html('<li></li>')
                        $.each(res.data.members, function (index, item) {
                            let src = "{{ asset('imgs/user-blank.jpg') }}"
                            $(ul).find('li:last').after(
                            `
                                <li class="list-group-item">
                                    <img class="view-img mr-1 img_user" src="${src}"
                                                     data-toggle="tooltip" data-placement="right" title="${item['FullName']+' ('+item['username']+')'}" />
                                    <span name="full_name">${item['FullName']}</span>
                                    ${(() => {
                                        if (item['leader'] === true){
                                            return `<span class="pull-right"><small>Quản lý</small></span>`
                                        }else{
                                            return ``
                                        }
                                    })()}
                                </li>
                            `
                            )
                        })
                    }
                }
            })
        }

        const searchMember = () => {
            let input, filter, ul, li, a, i, txtValue;
            input = $('#modal-list-user #search')
            filter = $(input).val().toUpperCase();
            ul = $('#modal-list-user ul')
            li = $(ul).find("li");
            for (i = 0; i < li.length; i++) {
                a = $(li[i]).find("span[name='full_name']")[0];
                txtValue = $(a).text()
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        }

        //function to sort column
        const sortColumn =  one => {
            let page = $('ul.pagination').find('li.active');
            $(one).attr('sort',  $(one).attr('sort') === 'desc' ? 'asc' : 'desc');
            let i_tag = $(one.parentElement).find('i');
            $(i_tag).attr('class', $(one).attr('sort') === 'desc' ? 'fa fa-caret-down' : 'fa fa-caret-up');
            // loadDataFirstTime($('#list-project-task-form').serializeArray(), $(page).attr('data-lp'), $(one).attr('order'), $(one).attr('sort'));
            submitForm($('#list-project-task-form').serializeArray(), $(page).attr('data-lp'), $(one).attr('order'), $(one).attr('sort'));
        }
    </script>
@endsection
