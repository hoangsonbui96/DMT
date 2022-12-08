@extends('admin.layouts.default.app')

@section('content')
    <div id="container">
        <div class="group-top">
            <div class="col-lg-12">
                <h1 class="page-header">@lang('admin.user_groups.infoUser')</h1>
            </div>
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-8 col-sm-12 col-xs-12">

                </div>
                <div class="col-md-4 col-sm-12 col-xs-12">
                    <div class="add-dReport">
                        <form action="">
                            <button type="button" class="btn btn-primary" id="add-new-group-btn" data-toggle="modal" data-target="#new-user-group">@lang('admin.add_new_group')</button>
                        </form>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>

        <!-- Table daily report detail -->
        <div class="table-responsive tbl-dReport">
            <table width="100%" class="table table-striped table-bordered table-hover table-user-groups">
                <thead class="thead-default">
                <tr>
                    <th>@lang('aadmin.stt')</th>
                    <th>@lang('admin.group_name')</th>
                    <th>@lang('admin.created_at')</th>
                    <th>@lang('admin.updated_at')</th>
                    <th>@lang('admin.action')</th>

                </tr>
                </thead>
                <tbody>
                @foreach($userGroups as $userGroup)
                <tr class="even gradeC" data-id="10184">
                    <td class="text-center">#{{ $userGroup->id }}</td>
                    <td>{{ $userGroup->Name }}</td>
                    <td>{{ $userGroup->created_at }}</td>
                    <td>{{ $userGroup->updated_at }}</td>
                    <td style="width: 120px;">
                        <span class="update edit update-user-group" data-toggle="modal" data-target="#new-user-group" group-id="{{ $userGroup->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                        <span class="update delete delete-user-group"  group-id="{{ $userGroup->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>


        <div class="modal draggable fade in" id="new-user-group" role="dialog">
            <div class="modal-dialog modal-lg ui-draggable" style="width:90%;min-width: 900px; right: auto; height: 466px; bottom: auto; left: -1px; top: 26px;">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header ui-draggable-handle" style="cursor: move;">
                        <button type="button" class="close" data-dismiss="modal">×</button>
                        <h4 class="modal-title">Add record</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" method="post" action="" id="form-user-group">
                            @csrf
                            <input type="hidden" name="id" value="">
                            <div class="row">
                                <div class="col-sm-6 col-xs-12">

                                    <div class="form-group">
                                        <label class="control-label col-sm-4" for="Project">@lang('admin.group_name'):</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="Name" maxlength="30">
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="birthday">@lang('admin.group.manager'):</label>
                                <div class="col-sm-9">
                                    <label class="switch">
                                        <input type="checkbox" name="Manager">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <table class="table-bordered table-new-user-group">
                                    <thead>
                                    <th></th>
                                    @foreach($roles as $role)
                                        <th>@lang($role->Name)</th>
                                    @endforeach
                                    </thead>

                                    <tbody>
                                        @foreach($menus as $menu)
                                            @if($menu->childMenus->count() >0)
                                                <tr>
                                                    <td colspan="{{ $roles->count()+1 }}" style="text-align: left">
                                                        @lang('menu.'.$menu->LangKey)
                                                        <input type="checkbox" class="menu-{{ $menu->id }}" onclick="checkAllMenu({{ $menu->id }})">
                                                    </td>
                                                </tr>
                                                @foreach($menu->childMenus as $menuItem)
                                                    <tr>
                                                        <td style="text-align: left;padding-left:40px;">
                                                            @lang('menu.'.$menuItem->LangKey)
                                                            <input type="checkbox" class="child-menu-{{ $menu->id }} father-menu-{{ $menuItem->id }}" onclick="checkAllChildMenu({{ $menuItem->id }})">
                                                        </td>
                                                        @foreach($roles as $role)
                                                            <td>
                                                                <input type="checkbox" class="grandchild-menu-{{ $menu->id }} child-menu-item-{{ $menuItem->id }}" name="menu[{{ $menuItem->id }}][{{ $role->id }}]">
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td style="text-align: left;">
                                                        @lang('menu.'.$menu->LangKey)
                                                        <input type="checkbox" class="menu-{{ $menu->id }} " onclick="checkAllMenu({{ $menu->id }})">
                                                    </td>
                                                    @foreach($roles as $role)
                                                        <td>
                                                            <input type="checkbox" class="grandchild-menu-{{ $menu->id }}" name="menu[{{ $menu->id }}][{{ $role->id }}]">
                                                        </td>
                                                    @endforeach
                                                </tr>

                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                        <button type="button" class="btn btn-primary" id="save-group">@lang('admin.btnSave')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="popupModal">

    </div>
@endsection
