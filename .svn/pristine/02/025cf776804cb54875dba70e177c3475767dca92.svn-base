$(function () {

    $('#save-group').click(function (e) {
        e.preventDefault();
        $.ajax({
            url: 'user-groups/' + $('input[name="id"]').val(),
            type: 'post',
            data: $('#form-user-group').serializeArray(),
            success: function (data) {
                // console.log(data);
                $('#new-user-group').modal('hide');
                locationPage();
            },

            error: function (xhr, status, error) {
                console.log(error);
            }
        });
    });

    $('.update-user-group').click(function () {
        var groupId = $(this).attr('group-id');
        $('.modal-title').html('Edit Record');
        $('input[name="id"]').val(groupId);
        $('#new-user-group input[type="checkbox"]').prop('checked', false);

        $.ajax({
            url: '/admin/user-groups/' + groupId,
            success: function (data) {
                // console.log(data);
                $('input[name="Name"]').val(data[0]);
                $('input[name="Manager"]').prop('checked', data[2]);
                data = data[1];
                for (key in data) {
                    if (data.hasOwnProperty(key) &&
                        /^0$|^[1-9]\d*$/.test(key) &&
                        key <= 4294967294
                    ) {
                        for (key2 in data[key]) {
                            if (data[key].hasOwnProperty(key2) &&
                                /^0$|^[1-9]\d*$/.test(key2) &&
                                key2 <= 4294967294
                            ) {
                                // console.log('input[name="menu['+key+']['+key2+']"');
                                if ($('input[name="menu[' + key + '][' + key2 + ']"').length) {
                                    $('input[name="menu[' + key + '][' + key2 + ']"').prop('checked', true);
                                }
                            }
                        }
                    }
                }
            },
            fail: function (xhr, status, error) {
                console.log(error);
            }
        })
    });

    $('#add-new-group-btn').click(function () {
        $('input[name="Name"]').val('');
        $('#new-user-group input[type="checkbox"]').prop('checked', false);
        $('.modal-title').html('Add Record');
        $('input[name="id"]').val('');
    });

    $('.delete-user-group').click(function () {
        var groupId = $(this).attr('group-id');
        t = confirm('Delete entry?');
        if (t) {
            $.ajax({
                url: 'user-groups/' + groupId + '/del',
                // type: 'post',
                success: function (data) {
                    locationPage();
                },
                fail: function (xhr, status, error) {
                    console.log(error);
                }
            })
        } else {

        }

    });

    $(".update-user").click(function () {
        // $('#user-form')[0].reset();
        $('.loadajax').show();
        var userId = $(this).attr('user-id');
        // $('#user-info').modal('show');
        $.ajax({
            url: ajaxUrl + '/' + userId,
            success: function (data) {
                // $('#user-form')[0].reset();
                $('#popupModal').empty().html(data);
                $('.modal-title').html('Chi tiết thành viên');
                // $('#user-form')[0].reset();
                $('#user-info').modal('show');
                $('.loadajax').hide();
            }
        });
    });

    $("#add-new-user-btn").click(function () {
        // $('#user-form')[0].reset();
        $.ajax({
            url: ajaxUrl + '/',
            success: function (data) {

                $('#popupModal').empty().html(data);
                $('.modal-title').html('Thêm thành viên');
                // $('#user-form')[0].reset();
                $('#user-info').modal('show');

            }
        });
    });

    $(".btn-detail").click(function () {
        // $('#user-form')[0].reset();
        $('.loadajax').show();
        $.ajax({
            url: ajaxUrl,
            success: function (data) {
                $('#popupModal').empty().html(data);
                $('.modal-title').html(newTitle);
                // $('#user-form')[0].reset();
                $('.detail-modal').modal('show');
                $('.loadajax').hide();
            },
            error: function (data) {
                showErrors(data.responseJSON.error);
                $('.loadajax').hide();
            }
        });
    });

    $('.update-one').click(function (e) {
        e.preventDefault();
        ajaxGetServerWithLoader(genUrlGet([
            ajaxUrl, '/', $(this).attr('item-id')
        ]), 'GET', null, function (data) {
            $('#popupModal').empty().html(data);
            $('.modal-title').html(updateTitle);
            $('.detail-modal').modal('show');
        });
    });

    $('.update-meeting').click(function (e) {
        e.preventDefault();
        ajaxGetServerWithLoader(genUrlGet([
            ajaxUrl, '/', $(this).attr('item-id')
        ]), 'GET', null, function (data) {
            $('#popupModal').empty().html(data);
            $('.modal-title').html(updateTitle);
            $('.open-modal').modal('show');
        });
    });

    $('.review-one').click(function (e) {
        e.preventDefault();
        ajaxGetServerWithLoader(genUrlGet([
            ajaxUrlReview, '/', $(this).attr('item-id'),
        ]), 'GET', null, function (data) {
            $('#popupModal').empty().html(data);
            $('.modal-title').html(updateTitle);
            $('.review-modal').modal('show');
        });
    });

    // Request Task
    $('.review-request-task').click(function (e) {
        e.preventDefault();
        ajaxGetServerWithLoader(genUrlGet([
            UrlReviewRequestTask, '/', $(this).attr('item-id')
        ]), 'GET', null, function (data) {
            $('#popupModal').empty().html(data);
            $('.modal-title').html('Xem yêu cầu công việc');
            $('#task-request-respone').modal('show');
        });
    });

    $('.update-request-task').click(function (e) {
        e.preventDefault();
        ajaxGetServerWithLoader(genUrlGet([
            UrlUpdateRequestTask, '/', $(this).attr('item-id')
        ]), 'GET', null, function (data) {
            $('#popupModal').empty().html(data);
            $('.modal-title').html('Chỉnh sửa yêu cầu công việc');
            $('#task-request-detail').modal('show');
        });
    });

    $('.delete-request-task').click(function (e) {
        e.preventDefault();
        var obj = $(this);
        showConfirm(confirmMsg, function () {
            var keyId = obj.attr('item-id');
            ajaxGetServerWithLoader(genUrlGet([
                UrlUpdateRequestTask, '/', keyId, '/del'
            ]), 'GET', null, function (data) {
                if (data == 1) locationPage();
            });
        });
    });
    // Request Task --- end


    $('.delete-one, .delete-user').click(function (e) {
        e.preventDefault();

        var obj = $(this);

        showConfirm(confirmMsg, function () {

            var keyId = '';

            if (hasAttr(obj, 'item-id')) {
                keyId = obj.attr('item-id');
            } else if (hasAttr(obj, 'user-id')) {
                keyId = obj.attr('user-id');
            }

            if (StringIsNullOrEmpty(keyId)) return;

            ajaxGetServerWithLoader(genUrlGet([
                ajaxUrl, '/', keyId, '/del'
            ]), 'GET', null, function (data) {
                if (data == 1) locationPage();
            });
        });
    });

    $('.copy-one').click(function (e) {
        e.preventDefault();
        var itemId = $(this).attr('item-id');
        // $('#user-info').modal('show');
        $('.loadajax').show();
        $.ajax({
            url: ajaxUrl + '/' + itemId,
            data: { copy: itemId },
            success: function (data) {
                // $('#user-form')[0].reset();
                $('#popupModal').empty().html(data);
                $('.modal-title').html(copyTitle);
                // $('#user-form')[0].reset();
                $('.detail-modal').modal('show');
                $('.loadajax').hide();
            }
        });
    });

    $('#searchAll').click(function () {
        $('#searchmmbers').toggle("drop in");
    });

    $('.btnRQ-save').click(function () {
        var itemId = $(this).attr('data-id');
        showConfirm(confirmTxt, function () {
            var data = { 'action': 1, 'item': itemId, 'Note': null };
            $.ajax({
                url: ajaxUrl,
                type: 'post',
                data: data,
                success: function (data) {
                    locationPage();
                }
            });
        });
    });

    $('.btnRQ-del').click(function () {
        $("#reject-modal").modal("show");
        $("#overtime-id").val($(this).attr('data-id'));
    });

    //    $('.draggable').draggable();

    $('.save-reject-form').click(function () {
        showConfirm(confirmTxt, function () {
            var itemId = $("#overtime-id").val();
            var note = $("[name='Note']").val();
            $.ajax({
                url: ajaxUrl,
                type: 'post',
                data: { 'action': 2, 'item': itemId, 'Note': note },
                success: function (data) {
                    locationPage();
                }
            });
        });
    });

    $("#saveProfile").click(function () {
        $('.loadajax').show();
        //Update capicity profile
        // console.log($("#capicity-profile").serializeArray());
        var capicityProfile = $("#capicity-profile").serializeArray();

        $.ajax({
            url: profileUrl,
            type: 'post',
            data: capicityProfile,
            success: function (data) {
                if (typeof data.errors !== 'undefined') {
                    $('.loadajax').hide();
                    showErrors(data.errors);
                } else {
                    locationPage();
                }
            }
        });

    });

    $("#add-training").click(function () {
        html = $('#temp-training').html();
        $('.historyTraning').append(html);

        $('.frm-icon-remove').click(function () {
            $(this).parent().remove();
        });
        $(".sDate-input, .eDate-input").datetimepicker({
            format: 'YYYY/MM/DD',
        });
    });

    $('.frm-icon-remove').click(function () {
        $(this).parent().remove();
    });

    $('.open-vote').click(function () {
        var qid = $(this).attr('data-qid');
        // console.log(qid);
        $('.loadajax').show();
        $.ajax({
            url: voteUrl + '/' + qid,
            success: function (data) {
                // console.log(data);
                $('#popupModal').empty().html(data);
                // $('.modal-title').html(newTitle);
                // $('#user-form')[0].reset();
                $('.detail-modal').modal('show');
                $('.loadajax').hide();
            },
            fail: function (xhr, status, error) {
                console.log(error);
            }
        });
    });



    //begin Tien 18/2/2020
    $(document).on('dblclick', '.thang tbody tr td.notempty', function () {
        var itemId = $(this).attr('item-id');
        var lich = $('#select-calendar').val();
        var dd = $(this).attr("data-day");
        var mm = $(this).attr("data-month");
        var yyyy = $(this).attr("data-year");
        var date = dd + "/" + mm + "/" + yyyy;
        if (!!!lich) {
            if (confirm('Hiện tại chưa có lịch, vui lòng vào tạo lịch!Nhấn vào ok để tiến hành tạo lịch!')) {
                window.location.href = "CalendarManagement";
            }
        } else {
            $('.loadajax').show();
            // $('.detail-modal').modal('show');
            if (!!!itemId) {
                $.ajax({
                    url: ajaxUrl,
                    success: function (data) {
                        $('#popupModal').empty().html(data);
                        $('.modal-title').html(newTitle);
                        $('.detail-modal').modal('show');
                        $("#sDate-input").val(date);
                        $("#eDate-input").val(date);
                        $('.loadajax').hide();
                    }
                });
            } else {
                $.ajax({
                    url: ajaxUrl + '/' + itemId,
                    success: function (data) {
                        $('#popupModal').empty().html(data);
                        $('.modal-title').html(updateTitle);
                        $('.detail-modal').modal('show');
                        $('.loadajax').hide();
                    }
                });
            }
        }


    });

    $(document).on('click', '#copyCalendar', function () {
        var itemId = $(this).attr('item-id');
        var itemId1 = $(this).attr('item-id1');
        $.ajax({
            url: ajaxUrlCopy + '/' + itemId + itemId1,
            success: function (data) {
                $('#popupModal').empty().html(data);
                $('.modal-title').html(copyTitle);
                $('.detail-modal').modal('show');
                $('.loadajax').hide();
            }
        });
    });

    // Đổi định dạng ngày tháng
    $(document).on('click', '#delCalendar', function () {
        var itemId = $(this).attr('item-id');
        var itemId1 = $(this).attr('item-id1');

        showConfirm(confirmMsg, function () {
            $.ajax({
                url: ajaxUrlCopy + '/' + itemId + itemId1 + '/del',
                success: function (data) {
                    if (data == 1) locationPage();
                }
            });
        });
    });


    //end


    // Sắp xếp

    //Tien 8/4/2020
    //chuyển sang lịch AKB khi click vao xem lịch
    $(document).on('click', '.view-one-calendar', function () {
        var itemId = $(this).attr('item-id');
        var active = $(this).attr('item-active');
        var hrefs = window.location.href;
        if (active == 1) {
            locationPage(hrefs.substr(0, hrefs.indexOf('CalendarManagement')) + "Calendar?select-calendar=" + itemId);
        } else {
            alert('Lịch này không hoạt động!');
        }
    });

    //thay doi trang thái hoat động
    $(".activeCalendar").change(function () {
        console.log('true')
        var itemId = $(this).attr('item-id');
        $('.loadajax').show();
        $.ajax({
            url: ajaxUrl + '/' + itemId + '/up',
            success: function (data) {
                if (data == 1) {
                    locationPage();
                } else {
                    alert('Cập nhật thất bại!');
                    locationPage();
                }
            },
            error: function (xhr, status, error) {
                alert('Cập nhật thất bại!');
                locationPage();
            }
        });
    });
    //Tien 8/4/2020
});
