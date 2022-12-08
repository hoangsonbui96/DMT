$(document).ready(function () {
    $(function () {
        $('#sDate, #birthday, #eDate, #officalDate, .dtpkTime').datetimepicker({
            format: 'YYYY/MM/DD',
        });
        $('#sTime,#eTime').datetimepicker({
            allowInputToggle: true,
            format: 'HH:mm',
            stepping: 15
        });
        $(".selectpicker").selectpicker();
    });

});

function calculateTime(sdate, stime, edate, etime, stimeofday, etimeofday, arrCompensatedWorkingDay) {
    var date_ = [], before = "", now = "", res;

    var prevDate = new Date(moment(sdate, "YYYYMMDD", true).format());
    var nextDate = new Date(moment(edate, "YYYYMMDD", true).format());

    var lstCompensatedWorkingDay = [];
    if(arrCompensatedWorkingDay.length > 0) {
        $.each(arrCompensatedWorkingDay, function(index, el) {
            if(el.StartDate == el.EndDate) {
                lstCompensatedWorkingDay.push(new Date(moment(el.StartDate, "YYYYMMDD", true).format()));
            } else {
                for (var i = el.StartDate; i <= el.EndDate; i++) {
                    lstCompensatedWorkingDay.push(new Date(moment(i, "YYYYMMDD", true).format()));
                }
            }
        });
    }

    if(lstCompensatedWorkingDay.length > 0) {
        if(lstCompensatedWorkingDay.length == 1) {
            while (moment(prevDate).isSameOrBefore(nextDate)) {
                if(moment(prevDate).isoWeekday() !== 6 && moment(prevDate).isoWeekday() !== 7) {
                    date_.push(moment(prevDate).format("YYYYMMDD"));
                } else {
                    if(moment(prevDate).isSame(lstCompensatedWorkingDay[0])) {
                        if(date_.indexOf(moment(prevDate).format("YYYYMMDD")) === -1) {
                            date_.push(moment(prevDate).format("YYYYMMDD"));
                        }
                    }
                }
                prevDate = new Date(moment(prevDate, "YYYYMMDD", true).add('days', 1).format());
            }
        } else {
            while (moment(prevDate).isSameOrBefore(nextDate)) {
                if(moment(prevDate).isoWeekday() !== 6 && moment(prevDate).isoWeekday() !== 7) {
                    date_.push(moment(prevDate).format("YYYYMMDD"));
                } else {
                    $.each(lstCompensatedWorkingDay, function(i, e) {
                        if(moment(prevDate).isSame(e)) {
                            if(date_.indexOf(moment(prevDate).format("YYYYMMDD")) === -1) {
                                date_.push(moment(prevDate).format("YYYYMMDD"));
                            }
                        }
                    });
                }
                prevDate = new Date(moment(prevDate, "YYYYMMDD", true).add('days', 1).format());
            }
        }
    } else {
        while (moment(prevDate).isSameOrBefore(nextDate)) {
            if(moment(prevDate).isoWeekday() !== 6 && moment(prevDate).isoWeekday() !== 7) {
                date_.push(moment(prevDate).format("YYYYMMDD"));
            }
            prevDate = new Date(moment(prevDate, "YYYYMMDD", true).add('days', 1).format());
        }
    }

    if(date_.length == 1) {
        var arrTime = checkInterval(stime, etime, stimeofday, etimeofday);

        before = sdate + " " + arrTime['STime'];
        now = edate + " " + arrTime['ETime'];

        sum = moment.utc(moment(now,"YYYYMMDD HH:mm:ss").diff(moment(before,"YYYYMMDD HH:mm:ss"))).format("HH:mm");
        res = sumTime(sum, "00:00");
    } else if(date_.length == 2) {
        //first day
        var arrTimeFirst = checkInterval(stime, etimeofday, stimeofday, etimeofday);
        var firstDay_before = date_[0] + " " + arrTimeFirst['STime'];
        var firstDay_now = date_[0] + " " + arrTimeFirst['ETime'];
        res_first = moment.utc(moment(firstDay_now,"YYYYMMDD HH:mm:ss").diff(moment(firstDay_before,"YYYYMMDD HH:mm:ss"))).format("HH:mm");

        //last day
        var arrTimeLast = checkInterval(stimeofday, etime, stimeofday, etimeofday);
        var lastDay_before = date_[1] + " " + arrTimeLast['STime'];
        var lastDay_now = date_[1] + " " + arrTimeLast['ETime'];
        res_last = moment.utc(moment(lastDay_now,"YYYYMMDD HH:mm:ss").diff(moment(lastDay_before,"YYYYMMDD HH:mm:ss"))).format("HH:mm");

        res = sumTime(res_first, res_last);
    } else {
        //first day
        var arrTimeFirst = checkInterval(stime, etimeofday, stimeofday, etimeofday);
        var firstDay_before = date_[0] + " " + arrTimeFirst['STime'];
        var firstDay_now = date_[0] + " " + arrTimeFirst['ETime'];
        res_first = moment.utc(moment(firstDay_now,"YYYYMMDD HH:mm:ss").diff(moment(firstDay_before,"YYYYMMDD HH:mm:ss"))).format("HH:mm");

        //last day
        var arrTimeLast = checkInterval(stimeofday, etime, stimeofday, etimeofday);
        var lastDay_before = date_[date_.length - 1] + " " + arrTimeLast['STime'];
        var lastDay_now = date_[date_.length - 1] + " " + arrTimeLast['ETime'];
        res_last = moment.utc(moment(lastDay_now,"YYYYMMDD HH:mm:ss").diff(moment(lastDay_before,"YYYYMMDD HH:mm:ss"))).format("HH:mm");

        //between
        var timeTemp = (date_.length - 2) * 8;

        res = sumTime(res_first, res_last) + timeTemp * 60;
    }
    return res;
}

function checkInterval(strSTime, strETime, stimeofday, etimeofday) {
    var strTimeStart = strSTime.split(":")[0]+strSTime.split(":")[1],
        strTimeEnd = strETime.split(":")[0]+strETime.split(":")[1],
        sTimeofday = stimeofday.split(":")[0]+stimeofday.split(":")[1],
        eTimeofday = etimeofday.split(":")[0]+etimeofday.split(":")[1],
        arrTime = {};
    if(strTimeStart >= sTimeofday && strTimeEnd <= 1200) {
        arrTime['STime'] = strSTime;
        arrTime['ETime'] = strETime;
    }
    if(strTimeStart >= 1300 && strTimeEnd <= eTimeofday) {
        arrTime['STime'] = strSTime;
        arrTime['ETime'] = strETime;
    }
    if(strTimeStart >= sTimeofday && strTimeStart <= 1200 && strTimeEnd >= 1200 && strTimeEnd <= 1300) {
        arrTime['STime'] = strSTime;
        arrTime['ETime'] = "12:00";
    }
    if(strTimeStart >= 1200 && strTimeStart <= 1300 && strTimeEnd >= 1300 && strTimeEnd <= eTimeofday) {
        arrTime['STime'] = "13:00";
        arrTime['ETime'] = strETime;
    }
    if((strTimeStart < 1200  && strTimeStart >=800)  && (strTimeEnd > 1300  && strTimeEnd <= eTimeofday)) {
        arrTime['STime'] = strSTime;
        arrTime['ETime'] = (strETime.split(":")[0]-1)+":"+strETime.split(":")[1];
    }
    return arrTime;
}

function sumTime(time_1, time_2) {
    /* trả về số phút */
    var result = 0,
        sumHours = (+time_1.split(":")[0]) + (+time_2.split(":")[0]),
        sumMinutes = (+time_1.split(":")[1]) + (+time_2.split(":")[1]);
    return result = sumHours * 60 + sumMinutes;
}

function calEndTimeOfDay(time) {
    return result = moment(time,"HH:mm:ss").add(9, 'hours').format("HH:mm");
}

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

function convertMinsToHrsMins(mins) {
    var h = Math.floor(mins / 60);
    var m = mins % 60;
    // h = h < 10 ? '0' + h : h;
    m = m < 10 ? '0' + m : m;
    return h + ' giờ ' + ((m>0) ? (m + ' phút') : '');  //another vesion
    // return `${h}:${m}`;  //ES6 version
}

function getTimeAMPM(time) {
    var timeTemp = time.split(":");
    timeTemp = timeTemp[0] * 60 + parseInt(timeTemp[1]);
    return result = (timeTemp > 720) ? "PM" : "AM";
}

function getValue(id) {
    return $.trim($(id).val());
}

function setTimeStart() {
    var startTime = $("#absent-info #select-user option:selected").attr('start_time');
    var endTime = $("#absent-info #select-user option:selected").attr('end_time');
    startTime = (startTime != '' && typeof startTime !== "undefined") ? startTime.substring(0, 5) : '08:00';
    endTime = (endTime != '' && typeof endTime !== "undefined") ? endTime.substring(0, 5) : '17:00';
    $("#absent-info #sTime-input").val(startTime).prop("disabled", false);
    $("#absent-info #eTime-input").val(endTime).prop("disabled", false);
}


