// Open menu sort
const openSortMenu = () => {
    $('#main-menu').addClass('hide');
    $('#sort-menu').removeClass('hide');
}
// Comeback main menu
const openMainMenu = () => {
    $('#sort-menu').addClass('hide');
    $('#main-menu').removeClass('hide');
}
// Open main menu
const openMenu = (item, id) => {
    let menu = $('#list-action');
    let ul_main = $(menu).find('#main-menu');
    let ul_sort = $(menu).find('#sort-menu');
    switch (id) {
        case 1:
            $(ul_main).css("border", "solid 1px #e68b85");
            $(ul_sort).css("border", "solid 1px #e68b85");
            break;
        case 2:
            $(ul_main).css("border", "solid 1px #095494");
            $(ul_sort).css("border", "solid 1px #095494");
            break;
        case 3:
            $(ul_main).css("border", "solid 1px orange");
            $(ul_sort).css("border", "solid 1px orange");
            break;
        case 4:
            $(ul_main).css("border", "solid 1px #65ba6e");
            $(ul_sort).css("border", "solid 1px #65ba6e");
            break;
    }
    $(menu).toggleClass("hide");
    $(menu).attr('data-item', id);
    $(item).addClass('active-list');
    $(menu).position({
        of: $(item),
        my: 'left+25 top+25',
        at: 'left top'
    });
    let parent = $(item).parents()[3];
    let ul = $(parent).find('ul');
    if ($($(ul).find('li')).length === 0) {
        $($(menu).find('a')[2]).onclick = null;
    }
}

const beforeAddCurrent = item => {
    addCurrentTask(($((item.parent().parent())[0]).attr('data-item')));
}

const addCurrentTask = item => {
    ajaxGetServerWithLoaderAPI(ajaxUrl, headers, 'GET', [{name: "Status-id", value: item}], function (data) {
        $('#popupModal').empty().html(data);
        $('.modal-title').html(newTitle);
        $('.detail-modal').modal('show');
        $('.loadajax').hide();
        loadProjectInfo();
    }, function (error) {
        showErrors(error.error);
    })
}

const sortTask = e => {
    let name = $(e).attr('name');
    let order_by = '';
    let sort_by = '';
    switch (name) {
        case 'sort-important':
            order_by = 'Important';
            sort_by = 'desc';
            break;
        case 'sort-start-close':
            order_by = 'StartDate';
            sort_by = 'desc';
            break;
        case 'sort-start-far':
            order_by = 'StartDate';
            sort_by = 'asc';
            break;
        case 'sort-end-close':
            order_by = 'EndDate';
            sort_by = 'desc'
            break;
        case 'sort-end-far':
            order_by = 'EndDate';
            sort_by = 'asc';
            break;
        case 'sort-alphabet':
            order_by = 'Name';
            sort_by = 'asc';
            break;
    }
    let menu = $('#list-action');
    const data_request = [
        {name: 'OrderBy', value: order_by},
        {name: 'SortBy', value: sort_by},
        {name: 'Status', value: $(menu).attr('data-item')},
        {name: 'Keywords', value: $('input[name="Keywords"]').val()}
    ];
    let list_items = $('ul.list-items[data-item="' + $(menu).attr('data-item') + '"]');
    $(list_items).empty();
    loadData(data_request);
    $(menu).addClass('hide');
}

const saveNewPosition = () => {
    let positions = [];
    $(".update-position").each(function () {
        positions.push([$(this).attr("data-index"), $(this).attr("data-position")]);
        $(this).removeClass("update-position");
    })
    // let url = '{{ route("admin.ApiChangeStatus") }}';
    $.ajax({
        method: 'POST',
        url: urlSaveNewPos,
        data: JSON.stringify({
            Positions: positions
        }),
        headers: headers,
    });
}

const displayDropIt = (old_parent, new_parent) => {
    if ($('#' + old_parent).find('li').length > 0) {
        $('#' + old_parent).find('.drop-it').css('display', 'none')
    } else {
        $('#' + old_parent).find('.drop-it').css('display', 'block')
    }
    if ($('#' + new_parent).find('li').length > 0) {
        $('#' + new_parent).find('.drop-it').css('display', 'none')
    } else {
        $('#' + new_parent).find('.drop-it').css('display', 'block')
    }
}

const tiltDirection = item => {
    let left_pos = item.position().left,
        move_handler = e => {
            if (e.pageX >= left_pos) {
                item.addClass("right");
                item.removeClass("left");
            } else {
                item.addClass("left");
                item.removeClass("right");
            }
            left_pos = e.pageX;
        };
    $("html").bind("mousemove", move_handler);
    item.data("move_handler", move_handler);
}

const loadProjectInfo = () => {
    // let id = "{{ $project->id }}";
    // let url = "{{ route('admin.ApiAllProject', ':id') }}";
    // url = url.replace(':id', id);
    $.ajax({
        method: 'GET',
        url: urlLoadProInfo,
        headers: headers,
        async: false,
        data: [{name: "status", value: "on"}],
        success: response => {
            if (response.status_code === 200 && response.success === true) {
                displayProjectInfo(response.data.data_project[0]);
                moreText(50);
            }
        },
        error: (xhr, error) => {
            showErrors(error.error);
        }
    })
}

const displayProjectInfo = value => {
    let table = $('table')[0];
    let tbody = $(table).find('tbody');
    $(tbody).empty().html('');
    let dataHTML = '';
    dataHTML += `<tr>`;
    // dataHTML += `<td class="text-center" name="name-sort-td">
    //         ${(() => {
    //         if (value.NameShort != null){}
    //         return value.NameShort;
    //     }
    // )()}
    //         </td>`;
    dataHTML += `<td class="text-center" name="project-name-td">
            ${(() => {
        if (value.NameJa == null) {
            return '';
        } else {
            return value.NameJa
        }
    })()}
            </td>`

    dataHTML += `<td class="text-center" name="customer-td">${value.Customer}</td>`
    dataHTML += `<td class="width15" name="total-description-td">
            <span class="show-read-more">
                 ${(() => {
        if (value.Description == null) {
            return '';
        } else {
            return value.Description
        }
    })()}
             </span>
            </td>`
    dataHTML += `<td class="text-center" name="total-members-td"><a href="javascript:void(0)" onclick="openModalMember(${value.id}, this)">${value.Members}</a></td>`
    dataHTML += `<td class="text-center" name="total-hours-td">${value.TaskNotFinish + value.TaskWorking + value.TaskFinish + value.TaskReview}
                            </td>`
    dataHTML += `<td class="text-center" name="tks-total-hours-td">${value.TotalHours}</td>`
    dataHTML += `<td class="text-center" name="tks-progress-td">${value.Progress}</td>`
    dataHTML += `<td class="text-center" name="total-start-td">${dateFormatYMDToDMYY(value.StartDate, '/')}</td>`
    dataHTML += `<td class="text-center" name="tks-end-td">${dateFormatYMDToDMYY(value.EndDate, '/')}</td>`
    tbody.html(dataHTML);
}

const changeFlag = (e, event) => {
    event.stopPropagation();
    let id = $(e).attr('data-item');
    // let url = "{{ route('admin.ApiChangeImportant', ':id')}}";
    // url = url.replace(":id", id);
    let path = `api/akb/task-important/${id}`;
    $(e).toggleClass('important');
    $(e).toggleClass('hide-flag');
    $.ajax({
        url: URL + path,
        method: "POST",
        headers: headers,
        success: response => {
            if (response.status_code === 200 && response.success === true) {
                if ($(e).hasClass("important")) {
                    $('li#' + id).find(".fa-star").removeClass("hide-flag").addClass("important");
                }
                if ($(e).hasClass("hide-flag")) {
                    $('li#' + id).find(".fa-star").removeClass("important").addClass("hide-flag");
                }
                return null;
            }
        },
        error: (data) => {
            $(e).removeClass('important');
            $(e).removeClass('hide-flag');
        }
    })
}

const ajaxChangeTask = (e, to_id, items, from, to) => {
    $('.loadajax').show();
    let path = `api/akb/task-change/status`;
    return $.ajax({
        method: 'POST',
        url: URL + path,
        headers: headers,
        data: JSON.stringify({
            'Status': to_id,
            'Items': items,
        }),
        // success: res => {
        //     $('.loadajax').hide();
        //     if (to === 'list-review' && from === 'list-working') {
        //         $('ul.list-items').empty();
        //         loadData();
        //         loadProjectInfo();
        //     }
        //     return null;
        // },
        // error: (res) => {
        //     // $('.loadajax').hide();
        //     // $('.list-items.ui-sortable').empty();
        //     cancelSortable(one, ui)
        //     try {
        //         if (res.responseJSON.data.messages !== '') {
        //             showErrors(res.responseJSON.error + ': ' + (res.responseJSON.data.messages).toString());
        //         } else {
        //             showErrors(res.responseJSON.error);
        //         }
        //     } catch (e) {
        //         console.log(res);
        //     } finally {
        //         reloadTask();
        //     }
        // }
    })
}
const reloadTask = () => {
    $.ajax({
        url: urlLoadData,
        headers: headers,
        method: 'GET',
        success: response => {
            if (response.status_code === 200 && response.success === true) {
                displayTaskTags(response.data.data);
                loadProjectInfo();
                countTask();
            } else {
                $.ajax({
                    url: urlLoadData,
                    headers: headers,
                    method: 'GET',
                    success: response => {
                        if (response.status_code === 200 && response.success === true) {
                            displayTaskTags(response.data.data);
                            loadProjectInfo();
                            countTask();
                        }
                    },
                    error: res => {
                        showErrors(res.error);
                    }
                })
            }
        }
    })
}

const reportTask = (id, name, event, isFastReport = null, one = null, ui = null) => {
    if (!event.ctrlKey) {
        $('.loadajax').show();
        $(".modal").modal("hide");
        let path = `akb/report-task/${id}`;
        $.ajax({
            url: URL + path,
            headers: headers, data: {'is_fast_report': isFastReport}
        }).done(data => {
            $('#popupModal').html(data);
            $('.modal-title').html(name);
            $('.detail-modal').modal('show');
            $('.loadajax').hide();
            if (one != null && ui != null) {
                $('#cancel').click(() => cancelSortable(one, ui));
                $('#close-user-form').click(() => cancelSortable(one, ui));
            }
        }).fail((jqXHR, status, error) => {
            $('.loadajax').hide();
            showErrors(jqXHR.responseJSON.messages);
        });
    }
}

const mainTask = (id, event) => {
    if (!event) var event = window.event;
    event.cancelBubble = true;
    if (event.stopPropagation) event.stopPropagation();
    // event.stopPropagation();
    if (!event.ctrlKey) {
        let path = `akb/task-modal/${id}`;
        ajaxGetServerWithLoader(URL + path, "GET", null, function (data) {
            $('#popupModal').html(data);
            $('.detail-modal').modal('show');
        }, function (data) {
            showErrors(data.responseJSON);
        });
        // $('.loadajax').show();
        // let path = `akb/task-modal/${id}`;
        // $.ajax({
        //     url: URL + path,
        //     // headers: headers,
        //     success: data => {
        //         $('#popupModal').html(data);
        //         $('.detail-modal').modal('show');
        //         $('.loadajax').hide();
        //     },
        //     error: data => {
        //         $('.loadajax').hide();
        //         showErrors(data.responseJSON);
        //     }
        // });
    }
}

const detailTask = (id, event, name, id_pro) => {
    $('#modalDetail').modal('hide');
    $('.loadajax').show();
    // let url = '{{ route(\'admin.TaskWorkAdd\', "param") }}';
    // url = url.replace("param", '{{ $project->id }}' +'/' + id);
    let path = `akb/add-task/form/${id_pro}/${id}`;
    $(".modal").modal("hide");
    $.ajax({
        url: URL + path,
        headers: headers,
        success: function (data) {
            $('#popupModal').empty().html(data);
            $('.modal-title').html(name);
            $('.detail-modal').modal('show');
            $('.loadajax').hide();
        }
    });
}

const formatDate = string_time => {
    if (string_time === null || string_time === '') {
        return null;
    } else {
        return new Date(string_time);
    }
}

const displayDate = (start, end) => {
    let tmpl_start = dateFormatYMDToDMYYTask(start);
    if (end === null) {
        return tmpl_start;
    }
    let tmpl_end = dateFormatYMDToDMYYTask(end);
    return tmpl_start + ` - ` + tmpl_end;
}

const classDate = (start_d, end_d) => {
    const deadline = 4;
    if (end_d === null) {
        return '';
    } else {
        let now = new Date();
        let day = (end_d.getTime() - now.getTime()) / (1000 * 3600 * 24);
        if (day >= deadline) {
            return 'time-do success-akb';
        } else if (day >= deadline / 2 && day < deadline) {
            return 'time-do warning-akb';
        } else {
            return 'time-do danger-akb';
        }
    }
}

const displayImportant = (id, value_id, class_flag = '', size = 14) => {
    if (_uid == id) {
        return `<span style="margin-left: 5px"><i style="font-size: ${size}px" class="fa fa-star ${class_flag}" data-toggle="tooltip" data-item="${value_id}" data-placement="top" title="Đánh dấu quan trọng" aria-hidden="true" onclick="changeFlag(this, event)"></i></span>`;
    }
    return ``;
}
const renderDateTemplate = (start, end) => {
    let start_date = formatDate(start);
    let end_date = formatDate(end);
    let class_date_status = classDate(start_date, end_date);
    let template = displayDate(start_date, end_date);
    return `<span class="${class_date_status}" name="time">
                <span>${template}</span>
            </span>`
}

const displayTaskTags = json_data => {
    $.each(json_data, function (index, value) {
        let disable_mov = value['Member'].length !== 0 ? '' : 'disable-mov';
        let class_flag = (value['Important'] === 1) ? "important" : "hide-flag";
        let reportToday = value['ReportToday'] ? '<span><i class="fa fa-check" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Đã báo cáo"></i></span>' : ''
        let member = value['Member'].length !== 0 ? `<span style="font-size: 12px; border: 1px solid #4d4d4d; border-radius: 3px; padding: .2em"  data-toggle="tooltip" data-placement="right" title="${value['Member'][0].FullName}">${renderNameMember(value['Member'][0].FullName)}</span>` : ``
        let dateTask = value['StartDate'] !== null ? `<span style="font-size: 12px ">${renderDateTemplate(value['StartDate'], value['EndDate'])}</span>` : '';
        let important = value['Member'].length !== 0 ? displayImportant(value.Member[0].id, value.id, class_flag) : '';
        let count_comments = value['documents'].length;
        let commentTask = count_comments !== 0 ? `<span><i class="fa fa-comment-o" aria-hidden="true"></i> ${count_comments}</span>` : '';
        let tags = value['Tags'];
        let numberReturn = value['NumberReturn'] === 0 ? '' : ` <span><i class="fa fa-undo" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="Task bị trả lại ${value.NumberReturn} lần">${value.NumberReturn}</i></span>`
        let border_li = '';
        if (value['NumberReturn'] < 3) {
            border_li = '';
        } else if (value['NumberReturn'] >= 3 && value['NumberReturn'] <= 5) {
            border_li = 'border:1.5px solid #ffa500';
        } else {
            border_li = 'border: 1.5px solid #e68b85';
        }
        let arr_tags = '';
        let html_tags = '';
        if (tags) {
            arr_tags = tags.split(',');
            arr_tags.pop();
            arr_tags.shift();
        }
        if (arr_tags != null) {
            $.each(arr_tags, function (index, item) {
                html_tags += `<a href="javascript:void(0)"  class="tags" onclick="searchTags(this.text, event)">${item}</a>`;
            })
        }
        let li_tag = $(`
                    <li name="li-task" class="board-item ui-sortable-handle ${disable_mov}" style="${border_li}" draggable="true" id="${value.id}" data-name="${value.Name}"  data-index="${value.id}" data-position="${value['Position']}" onclick="mainTask(${value.id},  event)">
                        <div class="card none-border">
                            <div class="card-body">
                                <div style="display: flex; justify-content: space-between">
                                   <span class="card-title">${value.Name}</span>
                                   <div style="display: flex; flex-direction: row">
                                        ${numberReturn}
                                        ${important}
                                    </div>
                                </div>
                                <div style="display: flex; justify-content: space-between; flex-wrap: wrap; align-items: center; margin: 5px 0;">
                                   <div class="info-task" style="display: flex; flex-direction: row; align-items: center">
                                        ${member}
                                        ${dateTask}
                                        ${commentTask}
                                        ${reportToday}
                                    </div>
                                </div>
                                <div class="" style="display: flex; justify-content: space-between; align-items: center">
                                    <div style="word-break: break-word; margin-right: 5px"> ${html_tags}</div>
                                </div>
                            </div>
                        </div>
                    </li>
                `);
        switch (value.Status) {
            case not_finish:
                $('#list-not-finish:last').append(li_tag);
                $('#list-not-finish h3').css('display', 'none');
                break;
            case working:
                $('#list-working:last').append(li_tag);
                $('#list-working h3').css('display', 'none');
                break;
            case review:
                $('#list-review:last').append(li_tag);
                $('#list-review h3').css('display', 'none');
                break;
            case finish:
                $('#list-finish:last').append(li_tag);
                $('#list-finish h3').css('display', 'none');
                break;
            default:
                break;
        }
    });
}

const loadData = (data = null) => {
    ajaxGetServerWithLoaderAPI(urlLoadData, headers, 'GET', data, function (response) {
        if (response.status_code === 200 && response.success === true) {
            displayTaskTags(response.data.data);
            countTask();
        }
    }, function (errors) {
        showErrors(errors.responseJSON.error);
    });
}

const searchTags = (text, e) => {
    e.stopPropagation();
    $('input[name="Keywords"]').val(text);
    $('#list-project-task-form').submit();
}

const deleteTasks = (e, one) => {
    e.preventDefault();
    let id = $(one).attr('data-item');
    let ul = $('ul[data-item="' + id + '"]');
    let list_li = $(ul).find('li[name="li-task"]');
    let arr_id = [];
    $.each(list_li, function (index, value) {
        arr_id.push($(value).attr('data-index'));
    })
    if (arr_id.length === 0) {
        return;
    }
    // let url = "{{ route('admin.ApiDeleteTask') }}";
    let path = 'api/akb/task-delete';
    showConfirm("Bạn có chắc muốn xóa toàn bộ không?", () => {
        ajaxGetServerWithLoaderAPI(URL + path, headers, "POST", JSON.stringify({Items: arr_id}),
            function (response) {
                if (response.status_code === 200 || response.success === true) {
                    $.each(list_li, function (index, value) {
                        $(value).remove();
                    })
                    $('#list-action').addClass('hide');
                    loadProjectInfo();
                    countTask();
                    showSuccess('Xóa thành công!');
                }
            },
            function (data) {
                if (data.responseJSON.success === false || data.responseJSON.success === null) {
                    showErrors(data.responseJSON.error);
                    return;
                }
            })
    })
}

const deleteTask = id => {
    // let url = "{{ route('admin.ApiDeleteTask') }}";
    showConfirm("Một khi xóa task, dữ liệu báo cáo và lịch sử liên quan sẽ bị xóa bỏ. Bạn có đồng ý tiếp tục xóa task này không?", function () {
        let path = 'api/akb/task-delete';
        ajaxGetServerWithLoaderAPI(URL + path, headers, "POST", JSON.stringify({Items: [id]}),
            function (response) {
                if (response.status_code === 200 || response.success === true) {
                    $('li[data-index="' + id + '"]').remove();
                    $('#list-action').addClass('hide');
                    $('#modalDetail').modal('hide');
                    loadProjectInfo();
                    showSuccess("Xóa task thành công");
                    countTask();
                    $('.modal').modal('hide');
                }
            },
            function (data) {
                if (data.responseJSON.success === false || data.responseJSON.success === null) {
                    showErrors(data.responseJSON.error);
                    return null;
                }
            })
    });
}


const openModalMember = (id, e) => {
    let path = "akb/modal-member";
    ajaxGetServerWithLoader(URL + path, 'GET', null, (response) => {
        $('#popupModal').html(response);
        $('#modal-list-user').modal('toggle');
        detailMember(id);
        // $('#modal-list-user #title-modal').text($($($($(e)).parents()[1]).find("td")[1]).html())
        $('#modal-list-user #title-modal').text($('.page-header').text())
    }, (error) => {
    })
}

const detailMember = id => {
    // let taskURL = "{{ route('admin.ApiMembers', ':id') }}"
    // taskURL = taskURL.replace(':id', id)
    // let taskURL = `/../api/akb/info-members/${id}`;
    let path = `api/akb/info-members/${id}`;
    $.ajax({
        url: URL + path,
        async: false,
        headers: headers,
        success: (res) => {
            if (res.status_code === 200 && res.success === true) {
                let modal = $('#modal-list-user ');
                let ul = $(modal).find('ul');
                $(ul).html('<li></li>');
                $.each(res.data.members, function (index, item) {
                    // let src = "{{ asset('imgs/user-blank.jpg') }}"
                    // let src = "/../imgs/user-blank.jpg"
                    let src = URL + 'imgs/user-blank.jpg';
                    $(ul).find('li:last').after(
                        `
                                <li class="list-group-item">
                                    <img class="view-img mr-1 img_user" src="${src}"
                                                     data-toggle="tooltip" data-placement="right" title="${item['FullName'] + ' (' + item['username'] + ')'}" />
                                    <span name="full_name">${item['FullName']}</span>
                                    ${(() => {
                            if (item['leader'] === true) {
                                return `<span class="pull-right"><small>Quản lý</small></span>`
                            } else {
                                return ``
                            }
                        })()}
                                </li>
                            `
                    )
                })
            }
        }
    })
}

const countTask = () => {
    $('ul.list-items').each((index, item) => {
        // let length = $($(item).find('li')).length;
        // if (length != 0){
            $(item.parentElement).find('span[name="amount_task"]')[0].innerHTML =  $($(item).find('li')).length;
        // }
    })
}

const draggingTask = (one, ui, from, to, event) => {
    switch (from) {
        case "list-not-finish":
            if (to === "list-review" || to === "list-finish") {
                cancelSortable(one, ui);
                showErrors("Không thể di chuyển vào trạng thái này do task chưa Hoàn thành");
            } else {
                loadDataChangeTask(one, ui, from, to);
            }
            break;
        case "list-working":
            if (to === "list-finish") {
                cancelSortable(one, ui);
                showErrors("Task chưa được duyệt, không thể di chuyển sang trạng thái Hoàn thành");
            } else if (to === "list-review") {
                reportTask($(ui.item[0]).attr('id'), $(ui.item[0]).attr('data-name'), event, true, one, ui);
            } else {
                loadDataChangeTask(one, ui, from, to);
            }
            break;
        case "list-review":
            if (to === "list-not-finish") {
                cancelSortable(one, ui);
                showErrors("Task đang duyệt, không thể di chuyển về trạng thái Chưa thực hiện");
            } else if (to === "list-working") {
                openModalErrorReview(one, ui, event);
            } else {
                loadDataChangeTask(one, ui, from, to);
            }
            break;
        case "list-finish":
            if (to === "list-not-finish") {
                cancelSortable(one, ui);
                showErrors("Task đã hoàn thành, không thể di chuyển về trạng thái Chưa thực hiện");
            } else if (to === 'list-working') {
                cancelSortable(one, ui);
                showErrors("Chức năng đang phát triển dành cho PM");
            } else if (to === "list-review") {
                cancelSortable(one, ui);
                showErrors("Task đã hoàn thành, không thể di chuyển về trạng thái Đang duyệt");
            } else {
                loadDataChangeTask(one, ui, from, to);
            }
            break;
    }
}

const cancelSortable = (one, ui) => {
    $(one).sortable('cancel');
    $("li[name='li-task']").removeClass("hidden");
    ui.item.after(ui.item.data('items'));
    countTask();
}

const loadDataChangeTask = (one, ui, from = null, to = null) => {
    showConfirm('Bạn có muốn chuyển sang trạng thái khác không ?', () => {
        let items = [];
        items.push($(ui.item[0]).attr('id'));
        if (ui.item.data('items').length > 0) {
            $.each(ui.item.data('items'), (i, item) => {
                items.push($(item).attr('id'));
            })
        }
        ajaxChangeTask(this, ui.item.parent().attr("data-item"), items, from, to)
            .done(response => {
                $('.loadajax').hide();
                $('ul.list-items').empty();
                loadData();
                loadProjectInfo();
                return null;
            }).fail(error => {
            if (error.responseJSON.data.messages !== '') {
                showErrors(error.responseJSON.error + ': ' + (error.responseJSON.data.messages).toString());
            } else {
                showErrors(error.responseJSON.error);
            }
            $('.loadajax').hide();
            cancelSortable(one, ui);
        });
    }, () => {
        cancelSortable(one, ui);
    });
}

const openModalErrorReview = (one, ui, event) => {
    $('#modalDetail').modal('hide');
    $('.loadajax').show();
    let id = ui.item[0].getAttribute('id');
    let path = `akb/modal-error-review/${id}`;
    $.ajax({
        url: URL + path,
        headers: headers,
        success: function (data) {
            $('#popupModal').html(data);
            $('#modal-error-review').modal('show');
            $('.loadajax').hide();
            $('#cancel').click(() => cancelSortable(one, ui));
            $('#close-user-form').click(() => cancelSortable(one, ui));
            // $(window).click(event => {
            //     if (!$(event.target).closest('.modal-content').length) {
            //         cancelSortable(one, ui);
            //     }
            // });
        },
        error: function (data) {
            showErrors(data.responseJSON.messages);
            cancelSortable(one, ui);
            $('.loadajax').hide();

        }
    });
}

const searchMember = () => {
    let input, filter, ul, li, a, i, txtValue;
    input = $('#modal-list-user #search')
    filter = $(input).val().toUpperCase();
    ul = $('#modal-list-user ul')
    li = $(ul).find("li");
    for (i = 0; i < li.length; i++) {
        a = $(li[i]).find("span[name='full_name']")[0];
        txtValue = $(a).text()
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}

const renderNameMember = name => {
    let name_arr = name.split(" ");
    name_arr = name_arr.map(item => item.charAt(0));
    return name_arr.join("");
}
