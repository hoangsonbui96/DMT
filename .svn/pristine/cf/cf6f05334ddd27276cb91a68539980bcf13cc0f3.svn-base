<style>
    .select-member .show-menu-arrow:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {
        width: 100%;
    }

    .select-leader .show-menu-arrow:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {
        width: 100%;
    }

    .colors ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .colors li {
        display: inline-block;
    }

    .colors label {
        cursor: pointer;
    }

    .colors input {
        display: none;
    }

    .colors input[type="radio"]:checked+.swatch {
        box-shadow: inset 0 0 0 2px white;
        padding: 17px;
    }

    .swatch {
        display: inline-block;
        vertical-align: middle;
        height: 17px;
        width: 17px;
        margin: 0 5px 0 0;
        border: 1px solid #d4d4d4;
    }
</style>
<div class="modal fade in detail-modal" data-backdrop="static" id="user-info" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">@lang('projectmanager::admin.room.add_new_room')</h4>
            </div>
            <div class="modal-body">

                <div class="save-errors"></div>
                <form class="form-horizontal detail-form">
                    @csrf
                    @if(isset($phase->id))
                    <input type="hidden" name="id" value="{{ $phase->id }}">
                    @endif

                    <div class="form-group">
                        <label class="control-label col-sm-3"
                            for="projectName">@lang('projectmanager::admin.project.Name')&nbsp;<sup
                                class="text-red">*</sup>:</label>
                        <div class="col-sm-9">
                            <input disabled class="form-control" id="projectName" placeholder="Tên dự án"
                                name="projectName" maxlength="200"
                                value="{{ isset($project->NameVi) ? $project->NameVi : null }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"
                            for="phaseName">@lang('projectmanager::admin.phase.Name')&nbsp;<sup
                                class="text-red">*</sup>:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="phaseName" placeholder="Tên phase..."
                                name="Name" maxlength="200" value="{{ isset($phase->name) ? $phase->name : null }}">

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"
                            for="type">@lang('projectmanager::admin.phase.Type')&nbsp;<sup
                                class="text-red">*</sup>:</label>
                        <div class="col-sm-9">
                            <div>
                                <select class='selectpicker show-tick show-menu-arrow' data-live-search="false"
                                    name="type" data-size="6" data-live-search-placeholder="Search"
                                    data-actions-box="true" data-width="100%">
                                    <option value="">Chọn loại Phase</option>
                                    {!! GenHtmlOption($phaseTypes, 'DataValue', 'Name', isset($phase->type) ? $phase->type :
                                    '') !!}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"
                            for="type">@lang('projectmanager::admin.Color'):&nbsp;
                            :</label>
                        <div class="colors col-sm-9">
                            <ul>
                                <li>
                                    <label>
                                        <input type="radio" name="color" value="#61bd4f">
                                        <span class="swatch" style="background-color:#61bd4f"></span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="radio" name="color" value="#f2d600">
                                        <span class="swatch" style="background-color:#f2d600"></span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="radio" name="color" value="#ff9f1a">
                                        <span class="swatch" style="background-color:#ff9f1a"></span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="radio" name="color" value="#eb5a46">
                                        <span class="swatch" style="background-color:#eb5a46"></span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="radio" name="color" value="#c377e0">
                                        <span class="swatch" style="background-color:#c377e0"></span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="radio" name="color" value="#0079bf">
                                        <span class="swatch" style="background-color:#0079bf"></span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="radio" name="color" value="#00c2e0">
                                        <span class="swatch" style="background-color:#00c2e0"></span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="radio" name="color" value="#51e898">
                                        <span class="swatch" style="background-color:#51e898"></span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="radio" name="color" value="#ff78cb">
                                        <span class="swatch" style="background-color:#ff78cb"></span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="radio" name="color" value="#344563">
                                        <span class="swatch" style="background-color:#344563"></span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="radio" name="color" value="#000000">
                                        <span class="swatch" style="background-color:#000000"></span>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>    
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="sDate">@lang('projectmanager::admin.Times')&nbsp;<sup
                                class="text-red"></sup>:</label>
                        <div class="col-sm-9" id="select-leader">
                            <div class="row">
                                <div class="col-sm-6 col-xs-3">
                                    <div class="input-group date">
                                        <input type="text" class="form-control" id="sDate-input"
                                            placeholder="@lang('projectmanager::admin.Date Start')" name="StartDate"
                                            autocomplete="off"
                                            value="{{ isset($phase->start_date) ? FomatDateDisplay($phase->start_date, FOMAT_DISPLAY_DAY) : null }}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-3">
                                    <div class="input-group date">
                                        <input type="text" class="form-control" id="eDate-input"
                                            placeholder="@lang('projectmanager::admin.Date End')" name="EndDate"
                                            autocomplete="off"
                                            value="{{ isset($phase->end_date) ? FomatDateDisplay($phase->end_date, FOMAT_DISPLAY_DAY) : null }}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- danh sach leader--}}
                    <div class="form-group">
                        <label class="control-label col-sm-3"
                            for="Leaders">@lang('projectmanager::admin.phase.Leaders')&nbsp;<sup
                                class="text-red"></sup>:</label>
                        <div class="col-sm-9">
                            <div class="select-leader">
                                <select class='selectpicker show-tick show-menu-arrow' id='select-leader'
                                    data-done-button="true" name="leader" data-live-search="true" data-size="5"
                                    data-live-search-placeholder="Search">
                                    <option value="">Chọn Quản lý...</option>
                                    {!! GenHtmlOption($project->users, 'id', 'FullName', $phase->leader_id ?? '') !!}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"
                            for="desc">@lang('projectmanager::admin.Description'):</label>
                        <div class="col-sm-9">
                            <textarea class="form-control description" rows="5" id="desc" maxlength="300"
                                name="Description"
                                placeholder="@lang('projectmanager::admin.Description')">{{ isset($phase->description) ? $phase->description : null }}</textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                    id="cancel">@lang('projectmanager::admin.Cancel')</button>
                <button type="submit"
                    class="btn btn-primary btn-sm save-form">@lang('projectmanager::admin.Save')</button>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript" async>
    $(function () {
        let projectId = '{{$project->id}}';
        let projectStartDate = '{{$project->StartDate}}';
        let projectEndDate = '{{$project->EndDate}}';
        let phaseColor = "{{$phase->color?? ''}}";
        projectStartDateArr = projectStartDate.split('-');
        projectEndDateArr = projectEndDate.split('-');

        $('body').css({'overflow-y': ''});

	    setSelectPicker();

        SetDatePicker($('.date'), {
            todayHighlight: true,
            startDate: projectStartDate ? new Date(projectStartDateArr[0],projectStartDateArr[1]-1,projectStartDateArr[2]) : "",
            endDate: projectEndDate ? new Date(projectEndDateArr[0],projectEndDateArr[1]-1,projectEndDateArr[2]) : "",
            autoclose: true,
        });

        $('#toggle-one').bootstrapToggle();
        $('.draggable').draggable();
        $('.selectpicker').selectpicker();

        $('.save-form').click(function () {
            let data = $('.detail-form').serializeArray()
            data.push({ name: "projectId", value: projectId });
            data.push({ name: "projectStartDate", value: projectStartDate });
            data.push({ name: "projectEndDate", value: projectEndDate });
            ajaxGetServerWithLoader("{{ route('admin.PhaseSave') }}", 'POST', data, function (res) {
                if (typeof res.errors !== 'undefined'){
                    showErrors(res.errors);
                    return ;
                }
                // locationPage();
				showPhases(phasePage,'id', 'asc');
                $('body').css({'overflow-y': 'auto','padding-right':'0'});
                $('.detail-modal').hide();
                $('.modal-backdrop').remove();
                showSuccessAutoClose(res.data.mes);
            });
        });
        // Jquery draggable
        $('.modal-dialog').draggable({
            handle: ".modal-header"
        });

        $('input[name="color"').each(function(){
            if($(this).val() == phaseColor){
                $(this).prop('checked',true);
            }
        });
    });
</script>