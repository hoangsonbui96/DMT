<div id="setting" class="tab-pane fade in">
    <div class="row">
        <div class="col-sm-10 col-xs-10" id="content-tab" >
            <div class="form-group">
                <label for="" class="control-label col-sm-2">Cảnh báo</label>
                <div class="col-sm-10">
                    <p>Một khi xóa task, dữ liệu báo cáo và lịch sử liên quan sẽ bị xóa bỏ. Bạn có đồng ý tiếp tục xóa task <strong>{{ $task->Name }}</strong> không ?</p>
                    <button type="button" class="btn btn-danger" id="delete-task">Tiếp tục</button>
                </div>
            </div>
{{--            <div class="form-group">--}}
{{--                <label class="control-label col-sm-2" for="Date">Ngày duyệt &nbsp;<sup class="text-red">*</sup>:</label>--}}
{{--                <div class="col-sm-10">--}}
{{--                    <div class="input-group date" id="sDate">--}}
{{--                        <input type="text" class="form-control date-input" id="modal-date-input" name="Date" placeholder="@lang('admin.working-day')"--}}
{{--                               value="{{ isset($reportLast) ? \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $reportLast->DateCreate)->format('H:i d/m/Y') : \Illuminate\Support\Carbon::today()->format('d/m/Y')}}" disabled>--}}
{{--                        <div class="input-group-addon">--}}
{{--                            <span class="glyphicon glyphicon-th"></span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="form-group">--}}
{{--                <label class="control-label col-sm-2" for="accepted_by">Người báo lỗi<sup class="text-red">*</sup>:</label>--}}
{{--                <div class="col-sm-10">--}}
{{--                    <input type="text" class="form-control" id="accepted_by" disabled value="{{ isset($error) ? $error->user->FullName : '' }}">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="form-group">--}}
{{--                <label class="control-label col-sm-2" for="note">Mô tả lỗi &nbsp;<sup class="text-red">*</sup>:</label>--}}
{{--                <div class="col-sm-10" maxlength="200">--}}
{{--                    <textarea class="form-control" rows="4" id="contents" name="Contents" disabled--}}
{{--                              placeholder="Nội dung">{{ isset($error) ? $error->Descriptions : '' }}</textarea>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>
        <div class="col-sm-2 col-xs-2">
           <div class="from-group">
               <label for=""><u>THAO TÁC</u></label>
{{--               <button type="button" name="clone"  class="btn btn-outline-secondary btn-block" style="text-align: left">--}}
{{--                   <span style="margin-right: 5px"><i class="fa fa-clone" aria-hidden="true"></i></span>--}}
{{--                   <span>Bản sao</span></button>--}}
               <button type="button" name="delete" class="btn btn-outline-secondary btn-block"  style="text-align: left">
                <span style="margin-right: 9px">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </span>
                   <span>Xóa</span>
               </button>
           </div>
{{--           <div class="form-group">--}}
{{--               <label for=""><u>THÔNG BÁO</u></label>--}}
{{--               <button type="button" name="mail" class="btn btn-outline-secondary btn-block" style="text-align: left">--}}
{{--                   <span style="margin-right: 5px"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>--}}
{{--                   <span>Gửi mail</span>--}}
{{--               </button>--}}
{{--           </div>--}}
{{--           <div class="form-group">--}}
{{--               <label for=""><u>TIỆN ÍCH</u></label>--}}
{{--               <button type="button" name="history" class="btn btn-outline-secondary btn-block" style="text-align: left">--}}
{{--                   <span style="margin-right: 5px"><i class="fa fa-history" aria-hidden="true"></i></span>--}}
{{--                   <span>Lịch sử</span>--}}
{{--               </button>--}}
{{--           </div>--}}
        </div>
    </div>
</div>
<script type="text/javascript">
    $('button[name="delete"]').click(e => {
        e.preventDefault();
        let content = $('#content-tab');
        $(content).empty();
        $(content).html(`
             <div class="form-group">
                <label for="" class="control-label col-sm-2">Cảnh báo</label>
                <div class="col-sm-10">
                    <p>Một khi xóa task, dữ liệu báo cáo và lịch sử liên quan sẽ bị xóa bỏ. Bạn có đồng ý tiếp tục xóa task <strong>{{ $task->Name }}</strong> không ?</p>
                    <button type="button" class="btn btn-danger" id="delete-task">Tiếp tục</button>
                </div>
            </div>
        `);
    });

    $('#delete-task').click( e => {
        e.preventDefault();
        deleteTask();
    });

    var deleteTask = () => {
        let id = "{{ $task->id }}";
        let url = "{{ route('admin.ApiDeleteTask') }}";
        // let path = 'api/akb/task-delete';
        ajaxGetServerWithLoaderAPI(url, headers, "POST", JSON.stringify({Items: [id]}),
            function (response) {
                if (response.status_code === 200 || response.success === true){
                    $('li[data-index="'+ id +'"]').remove();
                    $('#list-action').addClass('hide');
                    $('#modalDetail').modal('hide');
                    loadProjectInfo();
                    showSuccess("Xóa task thành công");
                    countTask();
                    $('#popupModal').find('.modal').modal('hide');
                }
            },
            function (data) {
                if (data.responseJSON.success === false || data.responseJSON.success === null) {
                    showErrors(data.responseJSON.error);
                    return null;
                }
            })
    }
</script>
