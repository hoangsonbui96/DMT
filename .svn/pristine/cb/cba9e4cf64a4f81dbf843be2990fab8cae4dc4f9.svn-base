<div class="modal draggable fade in detail-modal modal-css" role="dialog">
    <div class="modal-dialog modal-lg ui-draggable">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="save-errors"></div>
                <form class="form-horizontal detail-form">
                    @csrf
                    <div class="form-group">
                    @if(isset($PartnerInfo->id))
                    <input type="hidden" name="id" value="{{ $PartnerInfo->id }}">
                    @endif
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="RoomId">@lang('admin.partner.nameCompany')&nbsp;<sup class="text-red">*</sup>:</label>
                        <div class="bootstrap-select col-sm-8">
                            <input type="text" class="form-control" placeholder="Nhập tên công ty / khách hàng" name="full_name" value="{{ isset($PartnerInfo->full_name) ? $PartnerInfo->full_name : null }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="RoomId">@lang('admin.partner.InfoRepresentatives')&nbsp;<sup class="text-red">*</sup>:</label>
                        <div class="bootstrap-select col-sm-8">
                            {{-- <select class='selectpicker show-tick show-menu-arrow' name="department_id" data-size="5">
                            <option value="">[@lang('admin.partner.department_id')]</option>
                                @foreach($master_data as $master_data)
                                <option value="{{ $master_data->DataValue }}" {{ isset($PartnerInfo-> department_id) && $PartnerInfo->department_id == $master_data->DataValue ? 'selected' : '' }}>{{ $master_data->Name }}</option>
                            @endforeach
                            </select> --}}
                            <input type="text" class="form-control" placeholder="@lang('admin.partner.InfoRepresentatives')" name="InfoRepresentatives" value="{{ isset($PartnerInfo->InfoRepresentatives) ? $PartnerInfo->InfoRepresentatives : null }}">
                        </div>
                    </div>
                    {{-- <div class="form-group">
                        <label class="control-label col-sm-4" for="sDate">@lang('admin.user.birthday')<sup>*</sup>:</label>
                        <div class="col-sm-8">
                            <div class="input-group date datetime_txtBox datetime_txtBox_overtime" id="sDate">
                                <input type="text" class="form-control" id="sDate-input" placeholder="Ngày sinh" name="birthday" autocomplete="off" value="{{ isset($PartnerInfo->birthday) ? \Carbon\Carbon::parse($PartnerInfo->birthday)->format('d/m/Y') : null }}">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div> --}}
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="RoomId">@lang('admin.partner.tel') &nbsp;<sup class="text-red">*</sup>:</label>
                        <div class="bootstrap-select col-sm-8">
                            <input type="text" name="tel" class="form-control" placeholder="Số điện thoại" value="{{ isset($PartnerInfo->tel) ? $PartnerInfo->tel : null }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="RoomId">@lang('admin.partner.email') :</label>
                        <div class="bootstrap-select col-sm-8">
                            <input type="email" name="email" class="form-control" placeholder="Địa chỉ email" value="{{ isset($PartnerInfo->email) ? $PartnerInfo->email : null }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="RoomId">@lang('admin.partner.address') :</label>
                        <div class="bootstrap-select col-sm-8">
                            <input type="text" name="address" class="form-control" placeholder="Địa chỉ" value="{{ isset($PartnerInfo->address) ? $PartnerInfo->address : null }}">
                        </div>
                    </div>
                    {{-- <div class="form-group">
                        <label class="control-label col-sm-4" for="RoomId">@lang('admin.partner.sectors'):</label>
                        <div class="bootstrap-select col-sm-8">
                            <input type="text" name="sectors" class="form-control" placeholder="Ngành hàng" value="{{ isset($PartnerInfo->sectors) ? $PartnerInfo->sectors : null }}">
                        </div>
                    </div> --}}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm save-form">@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>

<script type="text/javascript" async>
    $(function () {
        SetDatePicker($('#sDate'));
        $('#toggle-one').bootstrapToggle();
        $('.draggable').draggable();
        $('.selectpicker').selectpicker();
        $('.save-form').click(function () {
            ajaxGetServerWithLoader("{{ route('admin.Partner') }}", 'POST', $('.detail-form').serializeArray(), function (data) {
                if (typeof data.errors !== 'undefined'){
                    showErrors(data.errors);
                    return ;
                }
                locationPage()
            });
        });
    });
</script>

