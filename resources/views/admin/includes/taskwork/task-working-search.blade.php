<style>
    div.content {
        min-height: 0 !important;
    }

</style>
<form class="form-inline" id="list-project-task-form" method="GET">
    <div class="form-group pull-left margin-r-5">
        <div class="select-choices" style="max-width: 30rem; min-width: 10rem">
            <select class="selectpicker show-tick show-menu-arrow with-ajax"
                    name="choices[]"
                    data-live-search="true"
                    multiple title="Nhập từ khóa..."
                    data-size="5" size="5"
                    data-actions-box="true">
            </select>
        </div>
    </div>
    <div class="form-group pull-left margin-r-5">
        <div class="input-group search date" id="sDate">
            <input type="text" class="form-control datepicker" id="st-date" placeholder="Ngày bắt đầu"
                   autocomplete="off" name="Date[]"
                   value="{{ isset($request['Date']) ? $request['Date'][0] : '' }}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group pull-left margin-r-5 hide" name="endDate">
        <div class="input-group search date" id="eDate">
            <input type="text" class="form-control datepicker" id="ed-date" placeholder="Khoảng kết thúc"
                   autocomplete="off" name="Date[]"
                   value="{{ isset($request['Date']) ? $request['Date'][1] : '' }}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group pull-left margin-r-5">
        <div class="input-group">
            <input type="text" class="form-control" name="errorIndex" placeholder="Trong khoảng" style="width: 8em">
        </div>
    </div>
    <div class="form-group pull-left margin-r-5">
        <div class="input-group">
            <select class="form-control" name="errorType">
                <option value="day">Ngày</option>
                <option value="month">Tháng</option>
                <option value="year">Năm</option>
            </select>
        </div>
    </div>
    <div class="form-group pull-left margin-r-5">
        <input type="checkbox" id="switch-toggle-status" name="status" checked data-toggle="toggle" data-on="Thực hiện"
               data-off="Tạm dừng" data-onstyle="primary" data-offstyle="danger">
    </div>
    <div class="form-group pull-left margin-r-5">
        <button type="submit" class="btn btn-primary btn-search" id="btn-search-meeting"
                style="height: 3.4rem">@lang('admin.btnSearch')</button>
    </div>
    <div class="form-group pull-right">
        {{--        @can('create-task')--}}
        {{--            <button type="button" class="btn btn-primary btn-detail" id="btn-new-task">@lang('admin.task-working.new-task')</button>--}}
        {{--        @endcan--}}
        @can('export-project')
            <button type="button" class="btn btn-success" id="btn-export">
                <div id="downloading" class="hide">
                    <i class="fa fa-spinner fa-spin"></i>
                    <span>Đang tải</span>
                </div>
                <span id="content">@lang('admin.export-excel')</span>
            </button>
        @endcan
    </div>
</form>
<script type="text/javascript" async>

    SetDatePicker($('.date'), {
        format: "dd/mm/yyyy",
        todayHighlight: true,
        autoclose: true,
    });

    $('#toggle-one').bootstrapToggle();
    $('.draggable').draggable();
    $('.selectpicker').selectpicker({
        selectAllText: 'Chọn tất cả',
        deselectAllText: 'Bỏ chọn tất cả'
    });

    // $(".datepicker").datepicker({
    //     autoclose: true,
    //     todayHighlight: true
    // });

    // $('#btn-search-meeting').click(function (e) {
    //     let sDate = moment($('#st-date').val(), 'DD/MM/YYYY').format('YYYYMMDD');
    //     let eDate = moment($('#ed-date').val(), 'DD/MM/YYYY').format('YYYYMMDD');
    //     let repSDate = sDate.replace(/\D/g, "");
    //     let repEDate = eDate.replace(/\D/g, "");
    //     if (repSDate > repEDate && repSDate != '' && repEDate != '') {
    //         showErrors(['Ngày tìm kiếm không hợp lệ']);
    //         e.preventDefault();
    //     }
    // });

    $('#btn-export').click(function (e) {
        e.preventDefault();
        $(this).addClass('disabled');
        $('#downloading').removeClass('hide');
        $('#content').addClass('hide');
        $.ajax({
            xhrFields: {
                responseType: 'blob',
            },
            method: 'GET',
            headers: {
                'Authorization': 'Bearer {{ \Illuminate\Support\Facades\Session::get('api-user') }}',
                'Content-type': 'application/x-www-form-urlencoded'
            },
            url: '{{ route("admin.ApiExportProject") }}',
            data: $('#list-project-task-form').serializeArray(),
            success: function (result, status, xhr) {
                let disposition = xhr.getResponseHeader('content-disposition');
                // let matches = /"([^"]*)"/.exec(disposition);
                // let filename = (matches != null && matches[1] ? matches[1] : `DanhSachDuAn.xlsx`);
                let arr = disposition.split("=")
                arr.shift();
                let filename = arr.join("");
                // The actual download
                let blob = new Blob([result], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filename;

                document.body.appendChild(link);

                link.click();
                document.body.removeChild(link);
                // $('.loadajax').hide();
                $('#btn-export').removeClass('disabled');
                $('#downloading').addClass('hide');
                $('#content').removeClass('hide');
            },
            error: function (xhr, error) {
                $('#btn-export').removeClass('disabled');
                $('#downloading').addClass('hide');
                $('#content').removeClass('hide');
                showErrors("Không có dữ liệu");
            }
        })
    });
    let options = {
        ajax: {
            headers: {
                'Content-type': 'application/json',
                'Authorization': 'Bearer {{ \Illuminate\Support\Facades\Session::get('api-user') }}'
            },
            url: "{{ route('admin.ApiSuggestAll') }}",
            method: "GET",
            data: {
                q: $('input.input-block-level').val()
            }
        },
        locale: {
            emptyTitle: "Select and Begin Typing"
        },
        log: 3,
        clearOnEmpty: true,
        preprocessData: function (data) {
            data = data.data.data;
            let i, l = data.length, array = [];
            if (l) {
                for (i = 0; i < l; i++) {
                    array.push(
                        $.extend(true, data[i], {
                            text: data[i].value,
                            value: data[i].up,
                            data: {
                                subtext: data[i].key
                            }
                        })
                    );
                }
            }
            // You must always return a valid array when processing data. The
            // data argument passed is a clone and cannot be modified directly.
            return array;
        }
    };

    $(".selectpicker").selectpicker().filter(".with-ajax").ajaxSelectPicker(options);
    $("select").trigger("change");
    $("ul.dropdown-menu.inner").css("max-height", "130px");
    $("ul.dropdown-menu.inner").css("overflow-y", "auto");

    function chooseSelectpicker(index, selectpicker) {
        $(selectpicker).val(index);
        $(selectpicker).selectpicker('refresh');
    }
</script>

