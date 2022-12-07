@extends('admin.layouts.default.app')

@section('content')
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.checkboxes.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.responsive.js') }}"></script>

<section class="content-header">
    <h1 class="page-header">@lang('admin.role.viewRole')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="pull-left">
                <form class="form-inline">
                    <div class="form-group pull-left">
                        <div class="input-group">
                            <select class="selectpicker show-tick show-menu-arrow" id="select" name="type" data-live-search="true" data-size="5"
                                    data-live-search-placeholder="Search" data-width="180px" data-actions-box="true" tabindex="-98">
                                <option value="1" {{ (isset($request['type']) && $request['type'] == 1) ? 'selected' : '' }}>Theo người dùng</option>
                                <option value="2" {{ (isset($request['type']) && $request['type'] == 2) ? 'selected' : '' }}>Theo nhóm</option>
                            </select>
                        </div>
                    </div>
                    <div class="input-group">
                        <button type="submit" class="btn btn-primary btn-search" id="btn-search" >@lang('admin.btnSearch')</button>
                    </div>
                </form>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            @component('admin.component.table')
                @slot('columnsTable')
                    <tr>
                        <th class="width5">@lang('admin.stt')</th>
                        <th class="width15">@lang('admin.role.nameScreen')</th>
                        <th>@lang('admin.role.isRole')</th>
                        <th>@lang('admin.role.notRole')</th>
                        <th class="width8">@lang('admin.role.function')</th>
                    </tr>
                @endslot
                @slot('dataTable')
                    @if(isset($request['type']) && $request['type'] == 2)
                        @foreach($screen as $item)
                            <tr item-alias="{{ $item->alias }}" data-name="{{$item->name}}">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{!! strlen($item->FullName) > 60 ? substr($item->FullName, 0, 70).'...' : $item->FullName !!}</td>
                                <td>{!! strlen($item->NameUserNotRole) > 60 ? substr($item->NameUserNotRole, 0, 70).'...' : $item->NameUserNotRole !!}</td>
                                @if(strlen($item->FullName) != 0)
                                    <td class="text-center">
                                        <button class="btn btn-default btn-refresh"><span class="action-col refresh" data-select="{{ $request['type'] }}" item-id="{{ $item->alias }}"><i class="fa fa-refresh" aria-hidden="true"></i> Xóa</span></button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        @foreach($screen as $item)
                            <tr item-alias="{{ $item->alias }}" data-name="{{$item->name}}">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $item->name }}</td>
                                <td class="" uid_role="{{ $item->uid }}">{!! isset($item->coQuyen) ? $item->coQuyen : '' !!}</td>
                                <td class="" uid_notrole="{{ $item->idNotRole }}">{!! isset($item->khongQuen) ? $item->khongQuen : '' !!}</td>
                                @if($item->uid != '')
                                    <td class="text-center">
                                        <button class="btn btn-default btn-refresh"><span class="action-col refresh" u-id="{{ $item->uid }}" data-select="{{ $request['type'] }}" item-id="{{ $item->alias }}"><i class="fa fa-refresh" aria-hidden="true"></i> Xóa</span></button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                @endslot
                @slot('pageTable')
                @endslot
            @endcomponent
            <div id="popupModal">
                <div class="modal draggable fade in detail-modal" id="modalViewRole" role="dialog" data-backdrop="static">
                    <div class="modal-dialog modal-xs ui-draggable">
                        <!-- Modal content-->
                        <div class="modal-content drag">
                            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                                <button type="button" class="close" data-dismiss="modal" id="">×</button>
                                <h4 class="modal-title"></h4>
                            </div>
                            <div class="modal-body">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                                {{--                <button type="submit" class="btn btn-primary btn-sm" id="save" >@lang('admin.btnSave')</button>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script !src="">
    var ajaxUrl = "{{ route('admin.AbsenceInfo') }}";
    var newTitle = '';
    var updateTitle = '';
    var ALIAS;
    var NEW_TITLE;
    var INDEX;
    var UID;

    $(".selectpicker").selectpicker();

    $(function () {

        //hover btn refresh
        $(".btn-refresh").hover(function(){
            $(this).find('.fa-refresh').addClass("fa-spin");
            $(this).addClass("btn-warning");
        }, function(){
            $(this).find('.fa-refresh').removeClass("fa-spin");
            $(this).removeClass("btn-warning");
        });

        //event click refresh
        $('.refresh').click(function () {
            var itemId  = $(this).attr('item-id');
            var type    = $(this).attr('data-select');
            var user_id = $(this).attr('u-id');
            if(type == 2){
                showConfirm('Xóa quyền của nhóm?',
                    function(){
                        ajaxGetServerWithLoader("{{ route('admin.refreshRoleScreen') }}?nameScreen="+itemId+"&type="+type, 'GET', null, function (data) {
                            if (typeof data.errors !== 'undefined'){
                                showErrors(data.errors);
                                return;
                            }
                            locationPage();
                        });
                    }
                );
            }else {
                showConfirm('Xóa quyền của người dùng?',
                    function(){
                        ajaxGetServerWithLoader("{{ route('admin.refreshRoleScreen') }}?nameScreen="+itemId+'&uId='+user_id, 'GET', null, function (data) {
                            if (typeof data.errors !== 'undefined'){
                                showErrors(data.errors);
                                return;
                            }
                            locationPage();
                        });
                    }
                );
            }
        });

        // $('tbody tr td').hover(function () {
        //     $(this).addClass("bg-red");
        // }, function(){
        //     $(this).removeClass("bg-red");
        // });

        //xem chi tiết (dùng ajax để khi refresh sẽ tự cập nhật lại danh sách)
        // $('tbody > tr > td:nth-child(3),tbody > tr > td:nth-child(4)').on('click', function () {
        //     var newTitle = $(this).parent().attr('data-name');
        //     // let data = $(this).attr('data-role');
        //     var alias = $(this).parent().attr('item-alias');
        //     var uid = '';
        //     //get parameter in url
        //     var type = getUrlParameter('type');
        //
        //     // lấy index của hàng
        //     var index = $(this).index();
        //     //nếu click vào td của cột không quyền
        //
        //     if (index == 3) {
        //         newTitle = newTitle + ' (Không có quyền)';
        //         uid = $(this).attr('uid_notrole');
        //     }else{
        //         newTitle = newTitle + ' (Có quyền)';
        //         uid = $(this).attr('uid_role');
        //     }
        //
        //     //if select group getDataIsGroup else getDataIsUser
        //     if(type == 2){
        //         getDataIsGroup(alias,newTitle,index);
        //     }else {
        //         getDataIsUser(alias,newTitle,uid,index);
        //     }
        //
        //     //set value global
        //     ALIAS       = alias;
        //     NEW_TITLE   = newTitle;
        //     INDEX       = index;
        //     UID         = uid;
        // });
    });

    // close modal reload page
    $('#popupModal').on('hide.bs.modal', function (e) {
        locationPage();
    });

    $("tbody > tr > td:nth-child(3),tbody > tr > td:nth-child(4)").hover(function(){
        $(this).css("cursor","pointer");
    }, function(){
        $(this).css("cursor","");
    });

    //function get info when select user
    function getDataIsUser(alias, newTitle, uid, index) {
        ajaxGetServerWithLoader("{{ route('admin.getRoleUserScreen') }}", "POST", {
                alias: alias,
                uid: uid,
            },
            function (data) {
                var data = JSON.parse(data);
                let html = ``;
                let stt = 1;
                html += `<div class="table-responsive"><table class="table table-bordered table-hover" id="tableViewRole">`;
                if(index == 3){
                    html += `<thead>
                            <tr>
                                <th class="width5">STT</th>
                                <th>Họ và tên</th>
                            </tr>
                        </thead>
                        <tbody>
                    `;

                    $.each(data, function (i, e) {
                        html += `
                        <tr>
                            <td class="text-center">${stt++}</td>
                            <td class="center-important">${e.FullName}</td>
                        </tr>
                        `;
                    });
                }else{
                    html += `<thead>
                            <tr>
                                <th class="width5">STT</th>
                                <th>Họ và tên</th>
                                <th class="width8">Chức năng</th>
                            </tr>
                        </thead>
                        <tbody>
                    `;

                    $.each(data, function (i, e) {
                        html += `
                        <tr>
                            <td class="text-center">${stt++}</td>
                            <td class="center-important">${e.FullName}</td>
                            <td class="center-important">
                                <button class="btn btn-default btn-refresh refreshInAlert" data-idUser="${e.id}" item-alias="${alias}">
                                <span class="action-col">
                                    <i class="fa fa-refresh" aria-hidden="true"></i> Xóa
                                </span></button>
                            </td>
                        </tr>
                        `;
                    });
                }

                html += `</tbody></table></div>`;
                $('#modalViewRole .modal-body').empty();
                $('.modal-title').html(newTitle);
                $('#modalViewRole .modal-body').append(html);
                setDataTable('tableViewRole', 10);
                $('.detail-modal').modal('show');

            }
        );
    }

    // function get info when select group
    function getDataIsGroup(alias, newTitle, index) {
        ajaxGetServerWithLoader("{{ route('admin.getRoleGroup') }}", "POST", {
                alias: alias,
            },
            function (data) {
                var data = JSON.parse(data);
                var dataNameGroup = '';
                let RoleGroupId = '';
                let html = ``;

                if (index == 3) {
                    dataNameGroup = data[0]['NameGroupNotRole'];

                    html += `<div class="table-responsive"><table class="table table-bordered table-hover" id="tableViewRole">`;
                    html += `<thead>
                                        <tr>
                                            <th class="width5">STT</th>
                                            <th>Nhóm</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                `;
                    let stt = 1;

                    $.each(dataNameGroup, function (i, e) {
                        html += `
                                <tr>
                                    <td class="text-center">${stt++}</td>
                                    <td class="center-important">${e}</td>
                                </tr>
                            `;
                    });

                } else {
                    dataNameGroup = data[0]['NameGroup'];
                    if (data[0]['RoleGroupId'] != null){
                        RoleGroupId = data[0]['RoleGroupId'].split(',');
                    }

                    html += `<div class="table-responsive"><table class="table table-bordered table-hover" id="tableViewRole">`;
                    html += `<thead>
                                <tr>
                                    <th class="width5">STT</th>
                                    <th>Nhóm</th>
                                    <th class="width8">Chức năng</th>
                                </tr>
                            </thead>
                            <tbody>
                        `;
                    let stt = 1;

                    $.each(dataNameGroup, function (i, e) {
                        html += `
                            <tr>
                                <td class="text-center">${stt++}</td>
                                <td class="center-important">${e}</td>
                                <td class="center-important">
                                    <button class="btn btn-default btn-refresh refreshInAlert" data-idUser="${RoleGroupId[i]}" item-alias="${alias}">
                                    <span class="action-col">
                                        <i class="fa fa-refresh" aria-hidden="true"></i> Xóa
                                    </span></button>
                                </td>
                            </tr>
                            `;
                    });
                }
                html += `</tbody></table></div>`;
                $('#modalViewRole .modal-body').empty();
                $('.modal-title').html(newTitle);
                $('#modalViewRole .modal-body').append(html);
                setDataTable('tableViewRole', 10);
                $('.detail-modal').modal('show');
            }
        );
    }

    //function get request in url
    function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
    };

    //open modal ...
    $('#popupModal').on('shown.bs.modal', function (e) {

        //xóa quyền của 1 người dùng
        $('.refreshInAlert').click(function () {
            var id = $(this).attr('data-idUser');
            var alias = $(this).attr('item-alias');
            var type = getUrlParameter('type');

            if(type != 2){
                let string = UID;
                let arr = string.split(',');
                arr = jQuery.grep(arr, function(value) {
                    return value != id;
                });
                UID = arr.toString();
            }

            if (type == 2){
                ajaxGetServerWithLoader("{{route('admin.deleteOne')}}", "POST", {
                        id: id,
                        nameScreen: alias,
                        type: type,
                    },
                    function (data) {
                        getDataIsGroup(ALIAS, NEW_TITLE, INDEX);
                    });
            }else{
                ajaxGetServerWithLoader("{{route('admin.deleteOne')}}", "POST", {
                        id: id,
                        nameScreen: alias
                    },
                    function (data) {
                        getDataIsUser(ALIAS, NEW_TITLE,UID, INDEX);
                    });
            }
        });

        // hover in button refresh
        $(".btn-refresh").hover(function(){
            $(this).find('.fa-refresh').addClass("fa-spin");
            $(this).addClass("btn-warning");
        }, function(){
            $(this).find('.fa-refresh').removeClass("fa-spin");
            $(this).removeClass("btn-warning");
        });
    });

</script>
@endsection

