<style>
    .form-group label{
        text-align: right;
    }
    /* .detail-form .form-group{
        margin-bottom: 10px;
    } */
</style>
<div class="modal draggable fade in detail-modal" id="user-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable width550">

        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">

                <div class="save-errors"></div>
                <form class="detail-form" role="form" action="" method="POST">
                    @csrf
                    <div class="box-body">
                        <div class="form-group row">
                            <label class="control-label col-sm-4">@lang('admin.spending.date')&nbsp;<sup class="text-red">*</sup>:</label>
                            <div class="col-sm-8">
                                <div class="input-group date">
                                    <input type="text" class="form-control datetimepicker" name="date" autocomplete="off" value="{{ isset($itemInfo->date) ? FomatDateDisplay($itemInfo->date, FOMAT_DISPLAY_DAY) : null }}" required>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-4">@lang('admin.spending.expense')&nbsp;<sup class="text-red">*</sup>:</label>
                            <div class="col-sm-8">
                                <input name="expense" type="text" class="form-control number-separator" value="{{ isset($itemInfo->expense) ? $itemInfo->expense : null }}" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-4">@lang('admin.spending.categoryName') <sup class="text-red">*</sup>:</label>
                            <div class="col-sm-8">
                            <select class="selectpicker show-tick show-menu-arrow form-control" id="finance-cat" name="finance_category" data-size="6" tabindex="-98">
                                <option value="">Chọn danh mục</option>
                                @foreach($cats as $cat)
                                <option value="{{ $cat->DataValue }}" {{isset($itemInfo->finance_category) && $itemInfo->finance_category == $cat->DataValue ? 'selected'  : '' }}>{{ $cat->Name }}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-4">@lang('admin.spending.user_chi') <sup class="text-red">*</sup>:</label>
                            <div class="col-sm-8">
                            <select class="selectpicker show-tick show-menu-arrow form-control" id="finance-cat" name="user_spend" data-size="6" tabindex="-98">
                                <option value="">Chọn người chi</option>
                                {!! GenHtmlOption($spendingUsers, 'id', 'FullName', isset($itemInfo->user_spend) ? $itemInfo->user_spend : '') !!}
                            </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-4">Mô tả:</label>
                            <div class="col-sm-8">
                                <textarea name="desc" class="form-control">{{ isset($itemInfo->desc) ? $itemInfo->desc : null }}</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-4">@lang('admin.note'):</label>
                            <div class="col-sm-8">
                            <input name="note" type="text" class="form-control" value="{{ isset($itemInfo->note) ? $itemInfo->note : null }}">
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    @if(isset($itemInfo->id))
                    <input type="hidden" name="id" value="{{ $itemInfo->id }}">
                    @endif
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm save-form">@lang('admin.btnSave')</button>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript" async>
    var ajaxUrl = "{{ route('admin.spendingDetail') }}";

    $(function () {
        SetDatePicker($('.date'),{endDate: new Date(),});

        $('.selectpicker').selectpicker();
        $( ".ui-draggable" ).draggable();
        $('input[name=Active]').bootstrapToggle();
        $('input[name=MeetingRoomFlag]').bootstrapToggle();

        $('.save-form').click(function () {
            data = $('.detail-form').serializeArray();
            data = validateData(data);
            ajaxGetServerWithLoader(ajaxUrl, 'POST', data, function (data) {
                if (typeof data.errors !== 'undefined'){
                    showErrors(data.errors);
                    return;
                }
                locationPage();
            })
        });

        displaySeparator();
    });

    function validateData(data){
        $(data).each( (index, item) => {
            console.log(item['name'])
            if (item['name'] === 'expense'){
                item['value'] = convertToFloat(item['value'])
            }
        });
        return data;
    }

    function convertToFloat(stringNumber){
        if (typeof stringNumber !== 'undefined') {
            return parseFloat(stringNumber.replaceAll(',', ''));
        } else {
            return 0;
        }
    }

    function displaySeparator(){
        var list = $('.number-separator');
        for (let n = 0; n < list.length; ++n) {
            if (/^[0-9.,]+$/.test(list[n].value)) {
                list[n].value = convertToFloat(list[n].value).toLocaleString('en');
            } else {
                list[n].value = list[n].value.substring(0, list[n].value.length - 1);
            }
        }
    }

</script>

