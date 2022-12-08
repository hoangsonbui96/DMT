<div class="modal draggable fade in detail-modal" id="" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-ms ui-draggable">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="save-errors"></div>
                <form class="form-horizontal" action="" method="POST" id="form-menu">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            @if(isset($record->id))
                                <input type="hidden" name="id" value="{{ $record->id }}" id="id">
                                <input type="hidden" value="{{ $record->ParentId }}" id="menuParent">
                            @endif
                            <div class="form-group">
                                <label class="control-label col-sm-4">@lang('admin.menu.name_menu') <sup class="text-red">*</sup>:</label>
                                <div class="col-sm-6">
                                    <input type="hidden" name="LangKey"
                                           value="{{ isset($record->LangKey) ? $record->LangKey : null }}">
                                    <input type="text" class="form-control" id="name_menu" maxlength="30" placeholder="@lang('admin.menu.name_menu')" name="name_menu"
                                           value="@if(isset($record->LangKey)) @lang('menu.'.$record->LangKey) @else '' @endif">
                                </div>
                            </div>

                            @if($record->ParentId == '' || $record->ParentId == null)
                                <div class="form-group">
                                    <label class="control-label col-sm-4">@lang('admin.menu.name_fontawesome'):</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" placeholder="@lang('admin.menu.name_fontawesome')" name="FontAwesome"
                                               value="{{ isset($record->FontAwesome) ? $record->FontAwesome : null }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">Vị trí <sup class="text-red">*</sup>:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" placeholder="Vị trí" name="Order" id="order"
                                               value="{{ isset($record->Order) ? $record->Order : null }}">
                                    </div>
                                </div>
                            @endif

                            @if($record->RouteName != '')
                                <div class="form-group">
                                    <label class="control-label col-sm-4">Menu cha:</label>
                                    <div class="col-sm-6">
                                        <select class="selectpicker show-tick show-menu-arrow" id="parentMenu" data-size="5" name="ParentId"
                                                data-live-search="true" data-live-search-placeholder="Search" data-width="270px">
                                            <option value="">Chọn menu cha</option>
                                            @foreach($menuParent as $item)
                                                <option value="{{ $item->id }}" {{ $record->ParentId == $item->id ? 'selected' : '' }}>@lang('menu.'.$item->LangKey)</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm" id="save" >@lang('admin.btnSave')</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
            </div>
        </div>
    </div>
</div>
<div id="popupModal1"></div>
<script type="text/javascript" async>
    $(function () {
        $(".selectpicker").selectpicker();
    });

    $('#save').click(function () {
        if($('#name_menu').val() == ''){
            showErrors('Tên menu không được để trống!');
            return;
        }
        if($('#menuParent').val() =='' && $('#order').val() == ''){
            showErrors('Vị trí không được để trống!');
            return;
        }
        // if($('#parentMenu option:selected') != '' && $('#parentMenu option:selected') != 'undefined'){
        //
        //     console.log('abc');
        // }
        // return;

        ajaxGetServerWithLoader("{{ route('admin.saveMenu') }}", "POST", $('#form-menu').serializeArray(), function (data) {
            if (typeof data.errors !== 'undefined') {
                showErrors(data.errors);
                return ;
            }
            // return;
            locationPage();
        });

        ajaxServer("{{ route('admin.changeLangMenu') }}", "POST", $('#form-menu').serializeArray(), function () {

        });

    });

    // Jquery draggable
    $('.modal-dialog').draggable({
        handle: ".modal-header"
    });
</script>

