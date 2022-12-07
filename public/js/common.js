//Prevent users from submitting a form by hitting Enter
function preventEnter() {
    $(window).keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
}

function checkAllMenu(id) {
    if ($('.menu-' + id).is(":checked")) {
        $('.child-menu-' + id).prop('checked', true);
        $('.grandchild-menu-' + id).prop('checked', true);
    } else {
        $('.child-menu-' + id).prop('checked', false);
        $('.grandchild-menu-' + id).prop('checked', false);
    }
}

function checkAllChildMenu(id) {
    if ($('.father-menu-' + id).is(":checked")) {
        $('.child-menu-item-' + id).prop('checked', true);
    } else {
        $('.child-menu-item-' + id).prop('checked', false);
    }
}

function showSuccessAutoClose(data) {
    $.alert({
        title: 'Thành công!',
        icon: 'fa fa-check',
        type: 'blue',
        content: data + '',
        autoClose: 'OK|300',
        scrollToPreviousElement: false,
        scrollToPreviousElementAnimate: true,
        buttons: {
            OK: {
                text: 'OK!', // text for button
                btnClass: 'btn-blue', // class for the button
                isHidden: false, // initially not hidden
                isDisabled: false, // initially not disabled
                action: function (OK) {
                }
            }

        }
    });
}

function showshowErrorsAutoClose(data) {
    $.alert({
        title: 'Thất bại!',
        icon: 'fa fa-warning',
        type: 'red',
        content: data + '',
        autoClose: 'OK|1500',
        scrollToPreviousElement: false,
        scrollToPreviousElementAnimate: true,
        buttons: {
            OK: {
                text: 'OK!', // text for button
                btnClass: 'btn-blue', // class for the button
                isHidden: false, // initially not hidden
                isDisabled: false, // initially not disabled
                action: function (OK) {
                }
            }

        }
    });
}



function showSuccess(data) {
    $.alert({
        title: 'Thành công',
        icon: 'fa fa-check',
        type: 'blue',
        content: data + ''
    });
};

function showErrors(data) {
    $.alert({
        title: 'Thất bại',
        icon: 'fa fa-warning',
        type: 'red',
        content: data + ''
    });
}

function showConfirm(data, fncOK, fncCancel) {
    $.confirm({
        title: 'Xác nhận?',
        // icon: 'fa fas fa-question',
        content: data + '',
        buttons: {
            ok: {
                text: 'Đồng ý',
                btnClass: 'btn width5',
                keys: ['enter'],
                action: function () {
                    if (IsFunction(fncOK)) {
                        fncOK();
                    }
                }
            },
            cancel: {
                text: 'Đóng',
                btnClass: 'btn width5',
                keys: ['esc'],
                action: function () {
                    if (IsFunction(fncCancel)) {
                        fncCancel();
                    }
                }
            }
        }
    });
}

function iff(condition, a, b) {
    if (condition) return a;
    else return b;
}

function isEmpty(str) {
    return (!str || 0 === str.length);
}

function convertToDateString(timestamp) {
    timestamp = timestamp.substring(0, 10);
    return timestamp;
}

function getHourMinute(timestamp) {
    timestamp = timestamp.substring(11, 16);
    return timestamp;
}

//inArray javascript function
function inArray(needle, haystack) {
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
        if (haystack[i] == needle) return true;
    }
    return false;
}
//luan chuyen thiet bi
function getEquipmentList(eqType, eqOwner) {
    $.ajax({
        url: eqListAjaxUrl,
        type: 'post',
        data: { 'eqType': eqType, 'eqOwner': eqOwner },
        success: function (data) {
            // console.log(data);
            $('.device_list').html('');

            for (key in data) {
                if (data.hasOwnProperty(key) &&
                    /^0$|^[1-9]\d*$/.test(key) &&
                    key <= 4294967294
                ) {
                    eq = [];
                    $("input[name='eq1[]']").each(function () {
                        data_value = $(this).val();
                        // data_name = $(this).attr('data-name');
                        eq.push(data_value);
                    });
                    // console.log(eq);
                    if (!inArray(data[key].code, eq)) {
                        html = `<div class="checkbox"><label><input class="checkboxDevice" type="checkbox" value="` + data[key].code + `" name="eq[]" data-name="` + data[key].name + ` - <i>(` + data[key].code + `)</i>">` + data[key].name + ` - <i>(` + data[key].code + `)</i></label></div>`;
                        $('.device_list').append(html);
                    }
                }
            }
        },
        fail: function (xhr, status, error) {
            console.log(error);
        }
    });
}

function getShortName(str) {
    var arr = str.split(" ");
    if (arr.length > 1) {
        return (arr[0].charAt(0) + arr[arr.length - 1].charAt(0)).toUpperCase();
    } else {
        return arr[0].toUpperCase();
    }
}

//diff in times
function diff(start, end) {
    start = start.split(":");
    end = end.split(":");
    var startDate = new Date(0, 0, 0, start[0], start[1], 0);
    var endDate = new Date(0, 0, 0, end[0], end[1], 0);
    var diff = endDate.getTime() - startDate.getTime();
    var hours = Math.floor(diff / 1000 / 60 / 60);
    diff -= hours * 1000 * 60 * 60;
    var minutes = Math.floor(diff / 1000 / 60);

    return (hours < 9 ? "0" : "") + hours + ":" + (minutes < 9 ? "0" : "") + minutes;
}

function coolAlert(str) {
    alert(str);
}

function InitDatePicker() {
    $.fn.datepicker.dates['en'] = {
        days: ["Chủ nhật", "Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7"],
        daysShort: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
        daysMin: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
        months: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
        monthsShort: ["T1", "T2", "T3", "T4", "T5", "T6", "T7", "T8", "T9", "T10", "T11", "T12"],
        today: "Today",
        clear: "Clear",
        format: "dd/mm/yyyy",
        titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
        weekStart: 1
    };
}

function SetDatePicker(obj, config) {
    !!!config ? obj.datepicker() : obj.datepicker(config);
}

function SetDateTimePicker(obj, config) {
    !!!config ? obj.datetimepicker() : obj.datetimepicker(config);
    $(obj).find('input[type=text]').click(function (e) {
        obj.find('span.input-group-addon').click();
    });
}

function SetTimePicker(obj, stepping) {
    obj.datetimepicker({
        allowInputToggle: true,
        format: 'HH:mm',
        stepping: !!!stepping ? 15 : stepping
    });
}

function SetMothPicker(obj) {
    SetDatePicker(obj, {
        format: "mm/yyyy",
        viewMode: "months",
        minViewMode: "months"
    });
}

function InitDatetimepicker(obj, fomat) {

    if (!!!obj) {
        return;
    }

    obj.datetimepicker({
        // format: typeof FOMAT_DATE === 'undefined' || !!!FOMAT_DATE ? 'YYYY/MM/DD' : FOMAT_DATE
        format: !!!fomat ? FOMAT_DATE : fomat
    });
}

function IsFunction(callback) {
    return !IsNullOrEmpty(callback) && typeof callback === 'function';
}

function IsNullOrEmpty(obj) {
    return undefined === obj || null === obj;
}

function StringIsNullOrEmpty(str) {
    return IsNullOrEmpty(str) || '' === str;
}

function ajaxServer(url, method, data, fncSuccess, funcErr) {
    return $.ajax({
        type: method,
        url: url,
        data: data,
        success: function (rst) {
            if (IsFunction(fncSuccess)) {
                fncSuccess(rst);
            }
        },
        error: function (jqXHR, textStatus, err) {

            if (jqXHR.status === 0) return;

            //Check authen then redirect to login
            if (jqXHR.status === 401 || jqXHR.statusCode === 401) {
                window.location.href = LOGIN_URL;
                return;
            }

            if (IsFunction(funcErr)) {
                funcErr(jqXHR, textStatus, err);
            }
        }
    });
}

function ajaxGetServerWithLoader(url, method, data, fncSuccess, funcErr) {

    $('.loadajax').show();

    return ajaxServer(url, method, data, function (rst) {

        $('.loadajax').hide();

        if (IsFunction(fncSuccess)) {
            fncSuccess(rst);
        }
    }, function (jqXHR, textStatus, err) {

        $('.loadajax').hide();

        if (IsFunction(funcErr)) {
            funcErr(jqXHR, textStatus, err);
        }
    });
}

function ajaxGetServerWithLoaderAPI(url, headers, method, data, fncSuccess, funcErr) {
    $('.loadajax').show();

    return ajaxServerAPI(url, headers, method, data, function (rst) {

        $('.loadajax').hide();

        if (IsFunction(fncSuccess)) {
            fncSuccess(rst);
        }
    }, function (jqXHR, textStatus, err) {

        $('.loadajax').hide();

        if (IsFunction(funcErr)) {
            funcErr(jqXHR, textStatus, err);
        }
    });
}

function ajaxServerAPI(url, headers, method, data, fncSuccess, funcErr) {
    return $.ajax({
        type: method,
        url: url,
        headers: headers,
        data: data,
        success: function (rst) {
            if (IsFunction(fncSuccess)) {
                fncSuccess(rst);
            }
        },
        error: function (jqXHR, textStatus, err) {

            if (jqXHR.status === 0) return;

            //Check authen then redirect to login
            if (jqXHR.status === 401 || jqXHR.statusCode === 401) {
                window.location.href = LOGIN_URL;
                return;
            }

            if (IsFunction(funcErr)) {
                funcErr(jqXHR, textStatus, err);
            }
        }
    });
}
function dateFormatYMDToDMYY(string_date, dash) {
    if (string_date == null) {
        return '';
    }
    let date = new Date(string_date);
    let d = date.getUTCDate();
    let m = date.getUTCMonth() + 1;
    let y = date.getUTCFullYear();
    return (d <= 9 ? '0' + d : d) + dash + (m <= 9 ? '0' + m : m) + dash + y;
}

function dateFormatYMDToDMYYTask(string_date) {
    let date_now = new Date();
    if (string_date == null) {
        return '';
    }
    let date = new Date(string_date);
    let d = date.getUTCDate();
    let m = date.getUTCMonth() + 1;
    let y = date.getUTCFullYear();
    if (y == date_now.getUTCFullYear()) {
        return d + (m <= 9 ? ' tháng ' + m : ' Th' + m);
    }
    return d + (m <= 9 ? ' tháng ' + m : " Th" + m) + " " + y;
}


function arrayToJson(form) {
    var form_data = form.serializeArray();
    var data = {};
    $(form_data).each(function (index, obj) {
        data[obj.name] = obj.value;
    })
    return data;
}

function locationPage(url) {

    $('.loadajax').show();

    if (!!!url) {
        window.location.reload();
    } else {
        window.location.href = url;
    }
}

function genUrlGet(arr) {
    return encodeURI(arr.join(''));
}

function hasAttr(element, attrName) {
    var attr = element.attr(attrName);
    return typeof attr !== typeof undefined && attr !== false;
}

//set table, datatable, data table
function setDataTable(id, numberRecord) {
    $('#' + id).DataTable({
        "destroy": true,
        // "searching": false,         // Ẩn ô search
        "ordering": true,           // Ẩn sắp xếp các cột
        // "lengthChange": true,      // Ẩn phần chỉnh số lượng hiển thị
        "info": false,              // Ẩn phần info (Ex: Showing 1 to 20 of 100 entries)
        "pageLength": numberRecord,           // Mặc định hiển thị 20
        "columnDefs": [
            { "targets": 'no-sort', "orderable": false }
        ],
        "sDom": '<"row view-filter"<"col-sm-12"<"pull-left"l><"pull-right"f><"clearfix">>>t<"row view-pager"<"col-sm-12"<"text-center"ip>>>',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Tìm kiếm...",
            lengthMenu: " Hiển thị _MENU_ bản ghi ", //lengthMenu:" _MENU_ ",
            paginate: {
                previous: '‹',
                next: '›'
            },
            aria: {
                paginate: {
                    previous: 'Previous',
                    next: 'Next'
                }
            }
        }
    });
}

function moreText(maxLength) {
    $(".show-read-more").each(function () {
        let myStr = $(this).text();
        if ($.trim(myStr).length > maxLength) {
            let newStr = myStr.substring(0, maxLength);
            let removedStr = myStr.substring(maxLength, myStr.length);
            $(this).empty().html(newStr);
            $(this).append(' <a href="javascript:void(0);" class="read-more">thêm...</a>');
            $(this).append('<span class="more-text">' + removedStr.trim() + '</span>');
        }
    });
    $(".read-more").click(function () {
        $(this).siblings(".more-text").contents().unwrap();
        $(this).remove();
    });
}
function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

var qs = (function (a) {
    if (a == "") return {};
    var b = {};
    for (var i = 0; i < a.length; ++i) {
        var p = a[i].split('=', 2);
        if (p.length == 1)
            b[p[0]] = "";
        else
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
    }
    return b;
})(window.location.search.substr(1).split('&'));

function updateMembers(select1, select2, attr) {
    let json_merge = {};
    let json_clean = {};
    json_merge.xxx = {};
    json_clean.xxx = {};
    $(select1).find('option').each(function () {
        let key = $(this).val();
        if (key !== "") {
            json_merge[key] = {};
            let arr_id = $(this).attr(attr).split(",");
            $(arr_id).each(function (index, value) {
                if (value !== "") {
                    json_merge[key][value] = $(select2).find(`option[value="${value}"]`).text();
                    json_clean[key] = {};
                }
            });
        }
    });
    return { members: json_merge, clean: json_clean };
}

function getArraysIntersection(a1, a2) {
    return a1.filter(function (n) {
        return a2.indexOf(n) !== -1;
    });
}
