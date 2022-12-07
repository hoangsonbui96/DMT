<div class="modal draggable fade in detail-modal" id="monthly-modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable ">
        @php
            $listEditor = [];
        @endphp
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">Thêm báo cáo [{{ \auth()->user()->FullName }}]</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" id="daily-form">
                    @csrf
                    <input type="hidden" class="form-control hidden " id="mReport_id"
                           value="{{ isset($weeklyInfo->id) ? $weeklyInfo->id : null }}">
                    <input type="hidden" class="form-control hidden " id="idRequest"
                           value="{{ isset($weeklyInfo->IdMeeting) ? $weeklyInfo->IdMeeting : null }}">
                    <input type="hidden" class="form-control hidden " id="idReviewer"
                           value="{{ isset($weeklyInfo->id) ? $weeklyInfo->IdReviewer : null }}">
                    <div class="form-group">
                        <label class="control-label col-md-3 col-lg-3" for="ProjectID">Tiêu đề báo cáo&nbsp;<sup
                                class="text-red">*</sup>:</label>
                        <div class="col-md-8 col-lg-8 col-xs-8">
                            <input class="form-control" type="text" maxlength="100"
                                   value="{{ isset($weeklyInfo->Content) ? $weeklyInfo->Content : $contentNote }}" id=""
                                   name="Content" placeholder="Tiêu đề báo cáo...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-lg-3" for="ProjectID">Thời gian báo cáo&nbsp;<sup
                                class="text-red">*</sup>:</label>
                        <div class="col-md-8 col-lg-8 col-xs-8" style="display: flex; justify-content: space-between">
                            <div class="input-group date" style="width: 48%;">
                                <input type="text" class="form-control datepicker" placeholder="Thời gian bắt đầu"
                                       name="StartDate" autocomplete="off"
                                       value="{{isset($weeklyInfo->StartDate) ? \Illuminate\Support\Carbon::create($weeklyInfo->StartDate)->format(FOMAT_DISPLAY_DAY) : $time_from  }}">
                                <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                            </div>
                            <div class="input-group date col-6" style="width: 48%;">
                                <input type="text" class="form-control datepicker" placeholder="Thời gian kết thúc"
                                       style="margin-top: 0;" name="EndDate" autocomplete="off"
                                       value="{{ isset($weeklyInfo->EndDate) ?\Illuminate\Support\Carbon::create($weeklyInfo->EndDate)->format(FOMAT_DISPLAY_DAY) : $time_to }}">
                                <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                            </div>
                        </div>
                    </div>
                    <ul class="nav nav-tabs" role="tablist">
                        @if(isset($weeklyInfo->detail_reports))
                            @foreach($weeklyInfo->detail_reports as $i => $item)
                                <li class="li-tab {{ $i==0 ? 'active' : '' }}">
                                    <a href="#task_{{$i}}" data-toggle="tab">Công việc {{$i+1}}</a>
                                    @if($i!=0)
                                        <span>
                                            <i class="fa fa-times remove" aria-hidden="true"
                                               onclick="removeTab(this)"></i>
                                        </span>
                                    @endif
                                </li>
                            @endforeach
                        @else
                            <li class="li-tab active">
                                <a href="#task_0" data-toggle="tab">Công việc 1</a>
                            </li>
                        @endif
                        <li><a href="#" id="add-work"><i class="fa fa-plus" aria-hidden="true"></i></a></li>
                    </ul>
                    @php
                        $listEditor = [];
                    @endphp
                    <div class="tab-content">
                        @if(isset($weeklyInfo->detail_reports))
                            @foreach($weeklyInfo->detail_reports as $i=>$item)
                                <div id="task_{{$i}}" class="tab-pane fade {{$i==0 ? 'in active' : ''}}">
                                    <div class="">
                                        <label for="NameProject{{$i}}"></label>
                                        <input id="NameProject{{$i}}" class="form-control" type="text"
                                               value="{{ $item->NameProject }}"
                                               name="NameProject[]" placeholder="Đầu mục công việc" required>
                                    </div>
                                    <div style="margin-top: 10px">
                                        <textarea id="editor{{$i}}"></textarea>
                                    </div>
                                </div>
                                @php
                                    $listEditor[] = [
                                        "id" => "editor$i",
                                        "value" => $item->Note,
                                    ];
                                @endphp
                            @endforeach
                        @else
                            <div id="task_0" class="tab-pane fade in active">
                                <div class="">
                                    <label for="NameProject"></label>
                                    <input id="NameProject" class="form-control" type="text"
                                           value=""
                                           name="NameProject[]" placeholder="Đầu mục công việc" required>
                                </div>
                                <div style="margin-top: 10px">
                                    <textarea id="editor0"></textarea>
                                </div>
                            </div>
                            @php
                                $listEditor[] = [
                                    "id" => "editor0",
                                    "value" => "",
                                ];
                            @endphp
                        @endif
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel"
                        data-control="">@lang('admin.btnCancel')</button>
                <button type="button" class="btn btn-primary save-form" id="saveReport"
                        data-control="">@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript" async>

    //Ckeditor setup
    var updateTitle = "Chỉnh sửa báo cáo " + "{{ isset($weeklyInfo) ? \App\User::find($weeklyInfo->UserId)->FullName : \auth()->user()->FullName }}";
    listEditor = {!! json_encode($listEditor, true) !!};
    listEditor.forEach(item => {
        makeCKEditor(item.id, item.value);
    });

    //Handles data post
    function dataPost() {
        let data = [
            {name: "id", value: $('#mReport_id').val()},
            {name: "idMeeting", value: $('#idRequest').val()},
            {name: "IdReviewer", value: $('#idReviewer').val()}
        ]
        let data_ckeditor = $.map($(".tab-content>.tab-pane"), function (item) {
            const id_ckeditor = $(item).find("textarea").attr("id");
            return {
                name: 'dataNote[]',
                value: CKEDITOR.instances[id_ckeditor].getData(),
            }
        })
        return [...$('#daily-form').serializeArray(), ...data, ...data_ckeditor];
    }

    //Validate data before save
    function validate(data) {
        let error = "";
        let data_note = [];
        let data_project = [];

        data = $.grep(data, function (el, i) {
            if (el["name"] === "dataNote[]") {
                data_note.push(el["value"]);
                return false;
            }
            if (el["name"] === "NameProject[]") {
                data_project.push(el["value"]);
                return false;
            }
            return true;
        })

        $(data_project).each(function (index, name) {
            if (name === "" && data_note[index] === "") {
                delete data_project[index];
                delete data_note[index];
            }
        })
        data_project = data_project.filter(function (e) {
            return e != null;
        })
        data_note = data_note.filter(function (e) {
            return e != null;
        })
        if (data_project.length === 0) error = "Chưa nhập dữ liệu Đầu mục công việc"
        $(data_project).each(function (index, name) {
            if (name === "" && data_note[index] !== "") {
                error = "Chưa nhập dữ liệu Đầu mục công việc";
                return false;
            } else if (name !== "" && data_note[index] === "") {
                error = "Chưa nhập dữ liệu Nội dung công việc";
                return false;
            }
        })
        $(data_project).each(function (index, name) {
            data.push({name: "NameProject[]", value: name});
            data.push({name: "dataNote[]", value: data_note[index]});
        })
        return [error, data];
    }

    //Remove tab
    function removeTab(self) {
        $('#add-work').attr("disabled", false);
        const li_tag = $(self).closest("li");
        const nth = $(li_tag).index();
        if (nth === 0) {
            return;
        }
        const tab_content = $('.tab-content');
        //Remove active
        $(tab_content).find(".active.in").removeClass("active in");
        $(li_tag).closest("ul").find(".active").removeClass("active");
        //Add active
        $(li_tag).prev().addClass("active");
        $(tab_content.find('.tab-pane')[nth - 1]).addClass("active in");
        //Remove item
        $(li_tag).remove();
        $(tab_content.find('.tab-pane')[nth]).remove();
        //Rewrite
        $("ul.nav-tabs").find(".li-tab").each(function (index, element) {
            $(element).find("a").text(`Công việc ${index + 1}`);
        })
    }

    $(document).ready(function () {
        let CONTENT_FORM = $(".tab-pane").html();
        SetDatePicker($('.date'), {
            todayHighlight: true,
        });
        $(".selectpicker").selectpicker();
        $(".datepicker").datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
        });

        //Add new tab
        $('#add-work').click(function () {
            let nav_tabs = $('.nav-tabs');
            if ($(nav_tabs).find('.li-tab').length > 9) {
                $('#add-work').attr("disabled", true);
                return;
            }
            let index_li = $(nav_tabs).find('.li-tab').length
            let tabId = `task_${index_li}`;
            let tab_content = $(".tab-content");

            //Remove class active
            $("li.active").removeClass("active");
            $(tab_content).find(".active").removeClass("active");

            //Append HTML
            $(this).closest("li").before(`
                <li class="li-tab active">
                    <a href="#${tabId}" data-toggle="tab">Công việc ${index_li + 1}</a>
                    <span><i class="fa fa-times" onclick="removeTab(this)" aria-hidden="true"></i></span>
                </li>
            `);
            $(tab_content).append(`<div id="${tabId}" class="tab-pane fade active in">${CONTENT_FORM}</div>`);

            //Setup ckeditor
            const tab_pane = $(tab_content).find(".tab-pane.active");
            const textarea = $(tab_pane).find("textarea");
            $(tab_pane).find("input").val("");
            $(textarea).next().remove();
            $(textarea).attr("id", `editor${index_li}`);
            makeCKEditor($(textarea).attr("id"), "");
        });

        // Save report
        $('#saveReport').click(function (e) {
            e.preventDefault();
            const [error, data] = validate(dataPost());
            if (error !== "") {
                showErrors(error);
            } else {
                showConfirm('Có muốn lưu hay không?', function () {
                    ajaxGetServerWithLoader("{{ route('admin.MonthlySave') }}", 'POST', data, function () {
                        locationPage();
                    }, function (error) {
                        console.log(data)
                        showErrors(error.responseJSON.errors)
                    });
                });
            }
        });
    });
</script>
