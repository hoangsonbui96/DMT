<style>
    .select-member .show-menu-arrow:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {
        width: 100%;
    }

    .select-leader .show-menu-arrow:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {
        width: 100%;
    }
</style>
<div class="modal fade in detail-modal" data-backdrop="static" id="user-info" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">@lang('admin.room.add_new_room')</h4>
            </div>
            <div class="modal-body">

                <div class="save-errors"></div>
                <form class="form-horizontal detail-form">
                    @csrf
                    @if(isset($project->id))
                    <input type="hidden" name="id" id="projectId" value="{{ $project->id }}">
                    @endif
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="nameVi">@lang('admin.project.name')&nbsp;<sup
                                class="text-red">*</sup>:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="nameVi" placeholder="Tiếng Việt..."
                                name="NameVi" maxlength="200"
                                value="{{ isset($project->NameVi) ? $project->NameVi : null }}">
                            <input type="hidden" class="form-control" id="nameEn" placeholder="Tiếng Anh..."
                                name="nameEn" maxlength="200"
                                value="{{ isset($project->NameEn) ? $project->NameEn : null }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="nameJa" placeholder="Tiếng Nhật..."
                                        name="NameJa" maxlength="200"
                                        value="{{ isset($project->NameJa) ? $project->NameJa : null }}">
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="nameShort" placeholder="(*) Viết tắt..."
                                        name="NameShort" maxlength="50"
                                        value="{{ isset($project->NameShort) ? $project->NameShort : null }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3" for="customer">@lang('admin.project.customer')&nbsp;<sup
                                class="text-red">*</sup>:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="customer" placeholder="Khách hàng"
                                name="Customer" maxlength="200"
                                value="{{ isset($project->Customer) ? $project->Customer : null }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3" for="sDate">@lang('admin.times')&nbsp;<sup
                                class="text-red">*</sup>:</label>
                        <div class="col-sm-9" id="select-leader">
                            <div class="row">
                                <div class="col-sm-6 col-xs-3">
                                    <div class="input-group date">
                                        <input type="text" class="form-control" id="sDate-input"
                                            placeholder="@lang('admin.startDate')" name="StartDate" autocomplete="off"
                                            value="{{ isset($project->StartDate) ? FomatDateDisplay($project->StartDate, FOMAT_DISPLAY_DAY) : null }}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-3">
                                    <div class="input-group date">
                                        <input type="text" class="form-control" id="eDate-input"
                                            placeholder="@lang('admin.endDate')" name="EndDate" autocomplete="off"
                                            value="{{ isset($project->EndDate) ? FomatDateDisplay($project->EndDate, FOMAT_DISPLAY_DAY) : null }}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"
                            for="desc">@lang('admin.project.describe_project'):</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="desc"
                                placeholder="@lang('admin.project.describe_project')" name="Description" maxlength="500"
                                value="{{ isset($project->Description) ? $project->Description : null }}">
                        </div>
                    </div>
                    {{-- danh sach leader--}}
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="leader">@lang('admin.project.Leader')&nbsp;<sup
                                class="text-red">*</sup>:</label>
                        <div class="col-sm-9">
                            <div class="select-leader">
                                <select class='selectpicker show-tick show-menu-arrow' id='select-leader' data-done-button="true" data-none-selected-text="Chọn trưởng nhóm"
                                    name="Leader[]" data-live-search="true" data-size="5"
                                    data-live-search-placeholder="Chọn trưởng nhóm" multiple>
                                    @if(isset($users))
                                        @foreach ($users as $user)
                                            <option value="{{$user->id}}" {{isset($project) && in_array($user->id,$project->leaders->pluck('id')->toArray()) ? 'selected' : ''}}>{{$user->FullName}}</option>
                                        @endforeach
                                    @endif
                                    @if (isset($project))
                                         @foreach ($project->inactiveUsers as $user)
                                            <option value="{{$user->id}}" {{isset($project) && in_array($user->id,$project->leaders->pluck('id')->toArray()) ? 'selected' : ''}} title="{{$user->FullName}}">{{$user->FullName}} - Không hoạt động</option>
                                        @endforeach
                                        {{-- @foreach ($project->deletedUsers as $user)
                                            <option value="{{$user->id}}" {{isset($project) && in_array($user->id,$project->leaders->pluck('id')->toArray()) ? 'selected' : ''}} title="{{$user->FullName}}">{{$user->FullName}} - Đã nghỉ việc</option>
                                        @endforeach --}}
                                    @endif
                                   
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3" for="member">@lang('admin.project.Member')&nbsp;:</label>
                        <div class="col-sm-9 ">
                            <div class="select-member">
                                <select class='selectpicker show-tick show-menu-arrow' data-live-search="true" data-done-button="true" data-none-selected-text="Chọn thành viên"
                                    name="Member[]" data-size="5" data-live-search-placeholder="Chọn thành viên" multiple="true"
                                    data-actions-box="true">
                                    @if(isset($users))
                                        @foreach ($users as $user)
                                            <option value="{{$user->id}}" {{isset($project) && in_array($user->id,$project->members->pluck('id')->toArray()) ? 'selected' : ''}}>{{$user->FullName}}</option>
                                        @endforeach
            
                                    @endif
                                    @if (isset($project))
                                        @foreach ($project->inactiveUsers as $user)
                                            <option value="{{$user->id}}" {{isset($project) && in_array($user->id,$project->members->pluck('id')->toArray()) ? 'selected' : ''}} title="{{$user->FullName}}">{{$user->FullName}} - Không hoạt động </option>
                                        @endforeach
                                        {{-- @foreach ($project->deletedUsers as $user)
                                            <option value="{{$user->id}}" {{isset($project) && in_array($user->id,$project->members->pluck('id')->toArray()) ? 'selected' : ''}} title="{{$user->FullName}}">{{$user->FullName}} - Đã nghỉ việc </option>
                                        @endforeach --}}
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">@lang('admin.Active_status'):</label>
                        <div class="col-sm-9" style="text-align: left;">
                            <input type="checkbox" name="Active"
                                @if ((isset($project->Active) && $project->Active == 1) || !isset($project->Active))
                                    checked 
                                @endif
                                data-toggle="toggle" id="toggle-one" data-on="Hoạt động" data-off="Không hoạt động"
                                data-width="150" name="Active"
                            >
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                    id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm save-form">@lang('admin.btnSave')</button>
            </div>
        </div>

    </div>
</div>
<script>
	$(".selectpicker").selectpicker();
</script>
<script type="text/javascript" async>
    $(function () {
  	    setSelectPicker();

        SetDatePicker($('.date'), {
            todayHighlight: true,
            autoclose: true,
        });
        $('body').css({'overflow-y': ''});
        $('#toggle-one').bootstrapToggle();
        $('.draggable').draggable();
        $('.save-form').click(function () {
            ajaxGetServerWithLoader("{{ route('admin.ProjectSave') }}", 'POST', $('.detail-form').serializeArray(), function (data) {
                if (typeof data.errors !== 'undefined'){
                    showErrors(data.errors);
                    return ;
                }
                if(data.updateId == null){
                    page = 1;
                    order_by = 'id';
                    sort_by = 'desc'
                }
                showSuccessAutoClose(data.mes);
                showProjects(page,order_by, sort_by,data);
                $('body').css({'overflow-y': 'auto','padding-right':'0'});
                $('.detail-modal').hide();
                $('.modal-backdrop').remove();
            });
        });
        // Jquery draggable
        $('.modal-dialog').draggable({
            handle: ".modal-header"
        });
    });

</script>