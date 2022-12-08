const workStartTime = '08:30';
const lunchStartTime = '12:00';
const workEndTime = '17:30';
const lunchEndTime = '13:00';

const arrWorkStartTime = [8, 30];
const arrLunchStartTime = [12, 00];
const arrWorkEndTime = [17, 30];
const arrLunchEndTime = [13, 00];

const DEFAULT_COLOR = 'rgb(135,206,250,0.3)';
const WARNING_COLOR = 'rgba(255, 0, 0, 0.3)';
const OVERDUE_COLOR = 'rgb(135,206,250,0.3)';
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
    }, function (error) {
        showErrors(error.error);
    })
}

const sortTask = e => {
    // let name = $(e).attr('name');
    let name = $(e).val();

    let order_by = '';
    let sort_by = '';
    switch (name) {
        case 'sort-user':
            order_by = 'UserId';
            break;
        case 'sort-phase':
            order_by = 'PhaseId';
            break;
        case 'sort-job':
            order_by = 'JobId';
            break;   
        case 'sort-start-close':
            order_by = 'StartDate';
            break;
        case 'sort-start':
            order_by = 'StartDate';
            break;
        case 'sort-end-close':
            order_by = 'EndDate';
            break;
        case 'sort-end-far':
            order_by = 'EndDate';
            break;
        case 'sort-alphabet':
            order_by = 'Name';
            break;
    }
    $(`#sort_list${$(e).attr('data-status')}`).val(order_by)
    // let menu = $('#list-action');
    const data_request = [
        {name: 'projectId', value: projectId},
        {name: 'phaseId', value: $('#select-phase').val()},
        {name: 'jobId', value: $('#select-job').val()},
        {name: 'OrderBy', value: order_by},
        {name: 'SortBy', value: sort_by},
        {name: 'Status', value: $(e).attr('data-status')},
        {name: 'Keywords', value: $('input[name="Keywords"]').val()}
    ];
    // let list_items = $('ul.list-items[data-item="' + $(menu).attr('data-item') + '"]');
    let list_items = $('#' + $(e).attr('data-item'));
    $(list_items).empty();
    loadData(data_request);
    // $(menu).addClass('hide');
}

const saveNewPosition = () => {
    let positions = [];
    $(".update-position").each(function () {
        positions.push([$(this).attr("data-index"), $(this).attr("data-position")]);
        $(this).removeClass("update-position");
    })
    // let url = '{{ route("admin.ApiChangeStatus") }}';
    $.ajax({
        method: 'GET',
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
    // $.ajax({
    //     method: 'GET',
    //     url: urlLoadProInfo,
    //     headers: headers,
    //     async: false,
    //     data: [{ name: "status", value: "on" }],
    //     success: response => {
    //         if (response.status_code === 200 && response.success === true) {
    //             displayProjectInfo(response.data.data_project[0]);
    //             moreText(50);
    //         }
    //     },
    //     error: (xhr, error) => {
    //         showErrors(error.error);
    //     }
    // })
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
    // let path = `api/akb/task-change/status`;
    return $.ajax({
        method: 'POST',
        url: urlSaveNewPos,
        headers: headers,
        data: JSON.stringify({
            'Status': to_id,
            'Items': items,
            'projectId': projectId,
        }),
        success: res => {
            if (res.success == true) {
                $('.loadajax').hide();
                if (to === 'list-review' && from === 'list-working') {
                    // $('ul.list-items').empty();
                    // loadData();
                    loadProjectInfo();
                }
                return null;
            } else {
                // cancelSortable(one, ui)
                // try {
                //     if (res.mes !== '') {
                //         showErrors(res.error + ': ' + res.mes);
                //     } else {
                //         showErrors(res.error);
                //     }
                // } catch (e) {
                //     console.log(res);
                // } finally {
                //     reloadTask();
                // }
            }

        },
        error: (res) => {
            // $('.loadajax').hide();
            // $('.list-items.ui-sortable').empty();
            cancelSortable(one, ui)
            try {
                if (res.responseJSON.data.messages !== '') {
                    showErrors(res.responseJSON.error + ': ' + (res.responseJSON.data.messages).toString());
                } else {
                    showErrors(res.responseJSON.error);
                }
            } catch (e) {
            } finally {
                reloadTask();
            }
        }
    })
}
const reloadTask = () => {
    $.ajax({
        url: urlLoadData,
        headers: headers,
        method: 'GET',
        success: response => {
            if (response.status_code === 200 && response.success === true) {
                displayTaskTags(response.data.tasks,response.data.permissions);
                loadProjectInfo();
                countTask();
            } else {
                $.ajax({
                    url: urlLoadData,
                    headers: headers,
                    method: 'GET',
                    success: response => {
                        if (response.status_code === 200 && response.success === true) {
                            displayTaskTags(response.data.tasks,response.data.permissions);
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
        $.ajax({
            url: urlTaskReport,
            data: {
                'is_fast_report': isFastReport,
                'taskId': id,
                'projectId': projectId
            }
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

const reportErrorTask = (id, name, event, one = null, ui = null) => {
    if (!event.ctrlKey) {
        $('.loadajax').show();
        $(".modal").modal("hide");
        $.ajax({
            url: urlErrorTaskReport,
            data: {
                'taskId': id,
                'projectId': projectId
            }
        }).done(data => {
            $('#popupModal').html(data);
            $('.modal-title').html('Báo lỗi ' + name);
            $('#modal-error-review').modal('show');
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
    if (start === null) {
        return 'Chưa đặt thời gian';
    }
    let tmpl_start = 'Bắt đầu: ' + formatDateTime(start);
    if (end === null) {
        return tmpl_start;
    }
    let tmpl_end = ` - Kết thúc: ` + formatDateTime(end);
    return tmpl_start + tmpl_end;
}


function formatDateTime(string_date) {
    let date_now = new Date();
    if (string_date == null) {
        return '';
    }
    let date = new Date(string_date);
    let d = date.getUTCDate();
    let m = date.getUTCMonth() + 1;
    let y = date.getUTCFullYear();
    let h = date.getHours() ?? null;
    h = ("0" + h).slice(-2);
    let i = date.getMinutes() ?? null;
    i = ("0" + i).slice(-2);
    if (y == date_now.getUTCFullYear()) {
        return h + ' : ' + i + " ngày " + d + (m <= 9 ? ' tháng ' + m : ' Th' + m);
    }
    return h + ' : ' + i + " ngày " + d + (m <= 9 ? ' tháng ' + m : " Th" + m);
}

function calculateHours(start_date, end_date) {
    const lunchDuration = 1;
    let dDate1 = createDateTime(start_date);
    let dDate2 = createDateTime(end_date);
    let countWeekend = 0;
    let countWorkDays = -1;
    let current = dDate1;

    while (current <= dDate2) // while the "current" date is <= the end date
    {
        if (current.getDay() === 6 || current.getDay() === 0) {
            countWeekend++;
        } else {
            countWorkDays++;
        }
        current.setDate(current.getDate() + 1); // increase current date by 1 day
    }
    dDate1 = createDateTime(start_date);
    let hours = ((dDate2 - dDate1) / 36e5) - (countWeekend) * 24 - countWorkDays * 17;
    if (dDate1.getHours() <= 12 && dDate2.getHours() >= 13) {
        hours = hours - 1;
    }
    return hours.toFixed(2);
}

//create Date from d/m/Y h:i string

function createDateTime(datetime) {
    let date = datetime.split(' ');
    let time = date[1].split(':');
    if (parseInt(time[0]) <= arrWorkStartTime[0] && parseInt(time[1]) < arrWorkStartTime[1]) {
        time = ['08', '30'];
    } else if (parseInt(time[0] >= arrWorkEndTime[0]) && parseInt(time[1]) > arrWorkEndTime[1]) {
        time = ['17', '30'];
    }
    date = date[0].split('/');
    let result = new Date(
        date[2],
        date[1] - 1,
        date[0],
        time[0],
        time[1]
    );
    if (result.getHours() == 12) {
        result.setHours(12, 0);
    }
    return result
}

function dateTimeToString(dateTime) {
    dateTime = ('0' + dateTime.getDate()).slice(-2) + '/'
        + ('0' + (dateTime.getMonth() + 1)).slice(-2) + '/'
        + dateTime.getFullYear() + ' '
        + ('0' + dateTime.getHours()).slice(-2) + ':'
        + ('0' + dateTime.getMinutes()).slice(-2);
    return dateTime;
}

// get EndTime

function getEndTime(start_time, duration) {
    let time = createDateTime(start_time);
    time.setTime(time.getTime() + (duration * 60 * 60 * 1000));
    return time;
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

const compare2Date = (date1, date2, type = 'minute') => {
    const diffTime = date2 - date1;
    let n = 60000
    if (type === 'date') {
        n = 24*n*60
    }
    else if (type === 'month') {
        n = 24 * n * 30
    }
    return diffTime / (n); 
}

const displayTaskTags = (json_data,permissions) => {
    // let class_flag = (json_data['Importance'] === 1) ? "important" : "hide-flag";
    let class_flag = "hide-flag";
    // let reportToday = json_data['ReportToday'] ? '<span><i class="fa fa-check" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Đã báo cáo"></i></span>' : '';
    let reportToday = '';
    let currentTime = new Date().getTime();
    $.each(json_data, function (index, value) {
        let taskEndTimeColor = DEFAULT_COLOR; 
        let taskEndTime = value.EndDate  ? new Date(`${value.EndDate}`).getTime() : null;
        if(taskEndTime && (!value.Status || value.Status < 3)){
            let compare = compare2Date(currentTime,taskEndTime,'date');
            if(compare <= 0){
                taskEndTimeColor = WARNING_COLOR;
            }else if(compare <= 1 ){
                taskEndTimeColor = OVERDUE_COLOR;
            }else{
                taskEndTimeColor = DEFAULT_COLOR;
            }
        }

        let progress = value['Progress'] ?? 0;
        let disable_mov = value['UserId'] !== null ? '' : 'disable-mov';
        if(permissions.review == false && value['Status'] == 3){
            disable_mov = 'disable-mov';
        }
        let member = value['member'] !== null ? `<span style="font-size: 12px; border-radius: 3px; padding: .2em"  data-toggle="tooltip" data-placement="right" title="${value['member'].FullName}">${renderNameMember(value['member'].FullName)}</span>` : ``;
        let dateTask = value['StartDate'] !== null ? `<span style="font-size: 12px ">${renderDateTemplate(value['StartDate'], value['EndDate'])}</span>` : '';

        // let important = json_data['users'].length !== 0 ? displayImportant(json_data.users[0].id, value.id, class_flag) : '';
        let important = '';
        let count_comments = 0
            //  value['documents'].length
        ;
        let commentTask = count_comments !== 0 ? `<span><i class="fa fa-comment-o" aria-hidden="true"></i> ${count_comments}</span>` : '';
        let tags = value['Tags'];
        let countIssues = value.issues ? value.issues.length : 0;
        let numberReturn = countIssues === 0 ? '' : ` <span><i style="color: #DC143C;" class="fa fa-undo" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="Task bị trả lại ${countIssues} lần"> ${countIssues}</i></span>`
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
            arr_tags.shift();
        }
        if (arr_tags != null) {
            $.each(arr_tags, function (index, item) {
                html_tags += `<a href="javascript:void(0)" onclick="searchTags(this.text, event)">#${item}</a>`;
            })
        }
        let startTime = value.startTime 
                ? 
                    `<div class="task-prop" data-toggle="tooltip" data-placement="right" title="Thời gian bắt đầu" style="background-color: rgb(135,206,250,0.3);">
                        <div class="task_prop_left">
                            <i class="fa fa-calendar-plus-o fa-lg" style=""></i>
                        </div>
                        <div class="task_prop_right task-start">
                            ${value.startTime}
                        </div>
                    </div>` 
                : '';

        let endTime = '';
        if(value.endTime){
            endTime = ` <div class="task-prop task-endtime" data-toggle="tooltip" data-placement="right" title="Thời gian kết thúc" style="background-color:${taskEndTimeColor};">
                            <div class="task_prop_left">
                                <i class="fa fa-calendar-check-o fa-lg"></i>
                            </div>
                            <div class="task_prop_right">
                                ${value.endTime}
                            </div>
                        </div>`
        }
        let duration = '';
        if(value.Duration){
            duration = `<div class="task-prop" data-toggle="tooltip" data-placement="right" title="Số giờ thực hiện thực tế/ Số giờ dự kiến hoàn thành" style="background-color: rgb(255,165,0,0.1);">
                            <div class="task_prop_left">
                                <i class="fa fa-clock-o fa-lg"></i>
                            </div>
                            <div class="task_prop_right data-duration" data-duration="${value.Duration}">
                                ${value.WorkedTime ?? 0}/${value.Duration} (h)
                            </div>    
                        </div>`

        }
        let progressDisplay = '';
        if(progress > 0){
            progressDisplay =   `
                                <div class="task-prop" data-toggle="tooltip" data-placement="right" title="Tiến độ" style="background-color: rgb(199,21,133,0.1);">
                                    <div class="task_prop_left">
                                        <i class="fa fa-percent fa-lg"></i>
                                    </div>
                                    <div class="task_prop_right">
                                        ${progress}
                                    </div> 
                                </div>
                                `
        }
        let parentTask = value.ParentId ?
                                `<div class="task-child" data-toggle="tooltip" data-placement="right" title="Task chính: ${value.parent_task.Name}">
                                </div>`
                                : '';
        let displayPhase = '';
        if( value['phase']){
            displayPhase = `<div class="numberPhase" style="background-color: ${value['phase'].color}" data-toggle="tooltip" data-placement="right" title="Phase: ${value['phase'].name}">${value['phase'].order}.${value.tpOrder}</div>`
        }  
        let displayJob = '';
        if( value['job']){
            displayJob =    `<div class="numberJob" style="background-color: ${value['job'].color}" data-toggle="tooltip" data-placement="right" title="Job: ${value['job'].name}">
                            ${value['job'].order}.${value.tjOrder}
                            </div>`
        }                            
        let li_tag = $(`
                    <li name="li-task" class="board-item ui-sortable-handle ${disable_mov} " style="${border_li}" draggable="true" id="${value.id}" data-name="${value.Name}"  data-index="${value.id}" data-position="${value.Position}" onclick="showTaskDetail(${value.id})">
                        <div class="card none-border">
                            <div class="card-body">
                                <div class="task-info">
                                    <div style=""><span class="label member-name label-default pb-5" style="border-radius:5px;margin-bottom:5px;background-color: rgba(220,220,220, .3);color:#000; font: 13px;">${value.member ? value.member.FullName : ''}</span></div>
                                    ${numberReturn}
                                </div>
                                <div class="card-title task-name task-info" data-toggle="tooltip" data-placement="left" id="task${value.id}"><span class="ellipsis-name">${value.Name}</span></div>

                                <div class="task-info" style="margin-top:1.25rem">
                                    ${startTime} 
                                    ${endTime}
                                </div>
                                <div class="task-info" style="margin-bottom:1.25rem">
                                    ${duration}
                                    ${progressDisplay}
                                </div>

                                <div class="task-info">
                                    <div class="tags">
                                        ${html_tags}
                                    </div>
                                    <div class="tags">
                                        ${parentTask}
                                        ${displayPhase}
                                        ${displayJob}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                `);
        switch (value.Status) {
            case 1:
                $('#list-not-finish:last').append(li_tag);
                $('#list-not-finish h3').css('display', 'none');
                break;
            case 2:
                $('#list-working:last').append(li_tag);
                $('#list-working h3').css('display', 'none');
                break;
            case 3:
                $('#list-review:last').append(li_tag);
                $('#list-review h3').css('display', 'none');
                break;
            case 4:
                $('#list-finish:last').append(li_tag);
                $('#list-finish h3').css('display', 'none');
                break;
            default:
                $('#list-not-finish:last').append(li_tag);
                $('#list-not-finish h3').css('display', 'none');
                break;
        }
    });
}

const loadData = (data) => {
    ajaxGetServerWithLoaderAPI(urlLoadData, headers, 'GET', data, function (response) {
        if (response.status_code === 200 && response.success === true) {
            displayTaskTags(response.data.tasks,response.data.permissions);
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
        ajaxGetServerWithLoaderAPI(urlDeleteTask, headers, "POST", JSON.stringify({Items: [id]}),
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
        $(item.parentElement).find('span[name="amount_task"]')[0].innerHTML = $($(item).find('li')).length;
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
            }
            else if (to === "list-working") {
            // openModalErrorReview(one, ui, event);
            reportErrorTask($(ui.item[0]).attr('id'), $(ui.item[0]).attr('data-name'), event,one, ui)
            }
            else {
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
    if (from == 'list-review' || to == 'list-review') {
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
                    // $('ul.list-items').empty();
                    // loadData({
                    //     'projectId': projectId,
                    //     'phaseId': phaseId,
                    //     'jobId': jobId
                    // });
                    // loadProjectInfo();
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
    } else {
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
                // $('ul.list-items').empty();
                // loadData({
                //     'projectId': projectId,
                //     'phaseId': phaseId,
                //     'jobId': jobId
                // });
                // loadProjectInfo();
                return null;
            })
            .fail(error => {
                if (error.responseJSON.data.messages !== '') {
                    showErrors(error.responseJSON.error + ': ' + (error.responseJSON.data.messages).toString());
                } else {
                    showErrors(error.responseJSON.error);
                }
                $('.loadajax').hide();
                cancelSortable(one, ui);
            });
    }

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

//Thanh Project Management

const setSelectPicker = () => {
    $(".selectpicker").selectpicker();
    $('.bs-select-all').text('Chọn hết');
    $('.bs-deselect-all').text('Bỏ chọn hết');
    $('.bs-donebutton').children('div').children('button').text('OK');
}

const myDateTimePicker = (obj, config) => {
    !!!config ? obj.datetimepicker() : obj.datetimepicker(config);
    $(obj).find('input[type=text]').click(function (e) {
        obj.find('span.input-group-addon').click();
    });
}

function openModalMember(url, projectId, phaseId, jobId) {
    let data = {
        projectId: projectId,
        phaseId: phaseId,
        jobId: jobId,
    }
    ajaxGetServerWithLoader(url, 'GET', data, (response) => {
        $('#popupModal').empty().html(response);
        $('#modal-list-user').modal('toggle');
    }, (error) => {
    })
}

function updateProject(projectId, phaseId, jobId) {
    projectId = projectId;
    phaseId = phaseId;
    jobId = jobId;
    if (phaseId) {
        updateTitle = 'Cập nhật Phase';
    } else if (jobId) {
        updateTitle = 'Cập nhật Job';
    }

    ajaxGetServerWithLoader(
        genUrlGet([
            ajaxUrl, '/', null
        ]),
        'GET',
        {
            projectId: projectId,
            phaseId: phaseId,
            jobId: jobId
        },
        function (res) {
            $('#popupModal').empty().html(res);
            $('.modal-title').html(updateTitle);
            $('.detail-modal').modal('show');
        }
    );
}

$(document).ready(() => {
    $(document).on('click', '.update-project', function (e) {
        e.preventDefault();
        ajaxGetServerWithLoader(
            genUrlGet([
                ajaxUrl, '/', null
            ]),
            'GET',
            {
                projectId: $(this).attr('project-id'),
                phaseId: $(this).attr('phase-id'),
                jobId: $(this).attr('job-id')
            },
            function (data) {
                $('#popupModal').empty().html(data);
                $('.modal-title').html(updateTitle);
                $('.detail-modal').modal('show');
            }
        );
    });
});
