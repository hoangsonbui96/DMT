<div class="modal draggable fade in detail-modal" id="modalUser" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-xs ui-draggable">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" value="{{$userChecked->DataDescription}}" id="inputHiden">
                <table width="100%" class="table table-bordered table-hover" id="tableUserNotWriteDaily">
                    <thead class="thead-default">
                    <tr>
                        <th>@lang('admin.stt')</th>
                        <th>@lang('admin.Staffs_name')</th>
                        <th>@lang('admin.status')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($userList as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->FullName }}</td>
                            <td>
                                <input type="checkbox" class="checked-one" name="id[]" data-id="{{ $user->id }}" {{ in_array($user->id, $arrUserChecked) ? 'checked' : '' }}>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm" id="save" >@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>
<script>
    var arrayID = [];

    var string = $('#inputHiden').val();
    string = string.substring(1, string.length-1);
    if(string != ''){
        arrayID = string.split(',');
    }

    //array push if checkbox change
    $(".checked-one").change(function() {
        var uid = $(this).attr('data-id');
        if(this.checked) {
            arrayID.push(uid);
        }else{
            var indexUid = arrayID.indexOf(uid);
            arrayID.splice(indexUid,1);
        }
    });

    $(function () {
        $('.modal-dialog').draggable({
            handle: ".modal-header"
        });

        //save
        $('#save').click(function () {
            arrayID = arrayID.toString();
            ajaxServer("{{ route('admin.saveArrayNWD') }}", "POST", {
                arrayID: arrayID
            }, function (data) {
                if (typeof data.errors !== 'undefined') {
                    showErrors(data.errors);
                    return ;
                }
                showSuccess('Cật nhật thành công');
                $('.detail-modal').modal('toggle');
            });
        });
    })
</script>
