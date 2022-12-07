@extends('admin.layouts.default.app')

@section('content')

<section class="content-header">
    <h1 class="page-header">@lang('admin.user_groups')</h1>
</section>
<section class="content">
    <div id="container">
        <div class="group-top">

            <div class="row" style="margin-bottom: 20px;">

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="add-dReport" style="float:right;">
                        <form action="">
                            <button type="button" class="btn btn-primary" id="add-new-group-btn" data-toggle="modal" data-target="#new-user-group">@lang('admin.add_new_group')</button>
                        </form>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>

        <!-- Table daily report detail -->
        <div class="table-responsive tbl-dReport  box tbl-top">
            <table width="100%" class="table table-striped table-bordered table-hover table-user-groups">
                <thead class="thead-default">
                <tr>
                    <th class="width5">@lang('admin.stt')</th>
                    <th>@lang('admin.group_name')</th>
                    <th>@lang('admin.created_at')</th>
                    <th>@lang('admin.updated_at')</th>
                    <th class="width12">@lang('admin.action')</th>

                </tr>
                </thead>
                <tbody>
                @foreach($userGroups as $userGroup)
                    <tr class="even gradeC" data-id="10184">
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $userGroup->name }}</td>
                        <td>{{ FomatDateDisplay($userGroup->created_at, FOMAT_DISPLAY_DAY) }}</td>
                        <td>{{ FomatDateDisplay($userGroup->updated_at, FOMAT_DISPLAY_DAY) }}</td>
{{--                        <td>{{ $userGroup->updated_at }}</td>--}}
                        <td class="text-center">
                            <span class="action-col update edit update-one" item-id="{{ $userGroup->id }}" group-id="{{ $userGroup->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
    <div id="popupModal">

    </div>
    <script>
        var ajaxUrl = "{{ route('admin.RoleGroupDetail') }}";
        var newTitle = 'Thêm nhóm';
        var updateTitle = 'Cập nhật nhóm';
        $(document).on('change', '.role-item', function(){
            var id = $(this).attr('data-id');
            var t = $(this).prop('checked');
            groupId = $("[name='groupId']").val();

            $.ajax({
                url: "{{ route('admin.AjaxRoleScreenDetailInput') }}/"+groupId+'/'+t+'/'+id,
                type: 'get',
                success: function (data) {
                    if (typeof data.errors !== 'undefined'){
                        showErrors(data.errors);
                    }else{
                        // console.log(data);
                        // listRole(groupId);
                    }
                },
                fail: function (error) {
                    console.log(error);
                }
            });
        });
        $(document).on('change', '.checkAll', function(){

            var checked = $(this).prop('checked');
            var arrRole = [];
            data = [];
            $('.role-item').each(function(){
                if($(this).prop('checked') != checked){
                    data.push($(this).attr('data-id'));
                }

            });
            arrRole.push({name: 'data', value: data});
            arrRole.push({name: 'groupId', value: $("[name='groupId']").val()});
            arrRole.push({name: 'checked', value: checked});
            if(checked == true){
                $('.role-item').prop('checked', true);
            }else{
                $('.role-item').prop('checked', false);
            }
            $.ajax({
                url: "{{ route('admin.AjaxRoleScreenDetailInputPost') }}",
                type: 'post',
                data: arrRole,
                success: function (data) {
                    if (typeof data.errors !== 'undefined'){
                        showErrors(data.errors);
                    }else{

                    }
                },
                fail: function (error) {
                    console.log(error);
                }
            });
        });
        $(document).on('click', '.paginate_button', function(e){
            $('.checkAll').prop('checked', false);
        });
    </script>
@endsection
