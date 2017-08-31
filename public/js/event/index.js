$(function () {
    //后台传过来的user_id
    var id = $('input[name="user_id"]').val();
    //前端判断是否为管理员
    var isAdmin = $('input[name="isAdmin"]').val();
    // 初始化列表事件
    function init_events(ele) {
        ele.each(function () {
            var eventObject = {
                title: $.trim($(this).text()), // use the element's text as the event title
                id: $(this).attr('id'),
                user_id: id
            }
            // store the Event Object in the DOM element so we can get to it later
            $(this).data('eventObject', eventObject);
            // make the event draggable using jQuery UI 拖动
            $(this).draggable({
                zIndex: 1070,
                revert: true, // will cause the event to go back to its
                revertDuration: 0  //  original position after the drag
            });
        })
    }

    init_events($('#external-events div.external-event'));

    // /**
    //  * ajax请求
    //  */
    // function ajaxRequests(requestType, ajaxUrl,data) {
    //     $.ajax({
    //         type: requestType,
    //         dataType: 'json',
    //         url: ajaxUrl,
    //         data: data,
    //         success: function (result) {
    //             if (result.statusCode === 200) {
    //                 // switch(requestType) {
    //                 //     case 'POST': obj.reset(); break;
    //                 //     case 'DELETE': obj.remove(); break;
    //                 //     default: break;
    //                 //}
    //             }else {
    //                 revertFunc();//外部不能调用
    //             }
    //             page.inform(
    //                 '操作结果', result.message,
    //                 result.statusCode === 200 ? page.success : page.failure
    //             );
    //             return false;
    //         },
    //         error: function (e) {
    //             var obj = JSON.parse(e.responseText);
    //             page.inform('出现异常', obj['message'], page.failure);
    //         }
    //     });
    // }
    //
    /**
     * 初始化日历事件
     */
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        buttonText: {
            today: 'today',
            month: 'month',
            week: 'week',
            day: 'day'
        },
        eventLimit: true,
        events: '../events/calendar_events/' + id,
        editable: true,
        droppable: true, // this allows things to be dropped onto the calendar !!!

        /**
         * 拖动插入
         */
        drop: function (date) {
            var originalEventObject = $(this).data('eventObject');
            var copiedEventObject = $.extend({}, originalEventObject);
            //弹窗显示表单
            $('#modal-create-event').modal({backdrop: true});
            //定义一个参数判断是否删除拖动的这个列表
            var isRemoveList = false;
            if ($('#drop-remove').is(':checked')) {
                isRemoveList = true;
                $(this).remove();
            }

            copiedEventObject.isRemoveList = isRemoveList;
            //初始化表单中的开始时间和结束时间
            $(".start-datepicker").val(moment(date).format('YYYY-MM-DD HH:mm:ss'));
            $(".end-datepicker").val(moment(date).format('YYYY-MM-DD HH:mm:ss'));

            copiedEventObject._token = $('#csrf_token').attr('content');
            //调用日期时间选择器，格式化时间
            $(".start-datepicker").datetimepicker({
                dateFormat: 'yy-mm-dd'
            });
            $(".end-datepicker").datetimepicker({
                dateFormat: 'yy-mm-dd'
            });
            //点击保存后获取时间值 因页面未刷新需要结束上次on(click)事件
            $('#confirm-add-time').off('click').click(function () {
                copiedEventObject.start = $(".start-datepicker").val();
                copiedEventObject.end = $(".end-datepicker").val();
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '../events/drag_events',
                    data: copiedEventObject,
                    success: function (result) {
                        if (result.statusCode === 200) {
                            //重新获取events
                            $('#calendar').fullCalendar('refetchEvents');
                        }
                        page.inform(
                            '操作结果', result.message,
                            result.statusCode === 200 ? page.success : page.failure
                        );
                    }
                });
            });
        },

        /**
         * 日程事件单击事件
         * @returns {boolean}
         * @param event
         */
        eventClick: function (event) {
            $.ajax({
                type: 'GET',
                dataType: 'json',
                url: '../events/edit/' + event.id,
                data: {ispublic: event.ispublic, userId: id},
                success: function (result) {
                    if (result.statusCode === 200) {
                        $('.show-form').html(result.message);
                        $(".start-datepicker").datetimepicker({
                            dateFormat: 'yy-mm-dd'
                        });
                        $(".end-datepicker").datetimepicker({
                            dateFormat: 'yy-mm-dd'
                        });
                        $('#confirm-update').on("click", function () {
                            var data = $('#formEventEdit').serialize();
                            data += "&" + "user_id" + "=" + id;
                            $.ajax({
                                type: 'PUT',
                                dataType: 'json',
                                url: '../events/update/' + event.id,
                                data: data,
                                success: function (result) {
                                    if (result.statusCode === 200) {
                                        //保存成功
                                    }
                                    page.inform(
                                        '操作结果', result.message,
                                        result.statusCode === 200 ? page.success : page.failure
                                    );
                                }
                            });
                        });
                        $('#confirm-delete-event').on("click", function () {
                            var ret = confirm("确定删除当前日程事件？");
                            if (ret) {
                                $.ajax({
                                    type: 'DELETE',
                                    url: '../events/delete/' + event.id,
                                    data: {_token: $('#csrf_token').attr('content')},
                                    success: function (result) {
                                        if (result.statusCode === 200) {
                                            $('#calendar').fullCalendar('removeEvents', event.id);
                                        }
                                        page.inform(
                                            '操作结果', result.message,
                                            result.statusCode === 200 ? page.success : page.failure
                                        );
                                    }
                                });
                            }
                        });
                    } else {
                        page.inform('提示！', result.message, page.failure);
                        return;
                    }
                    $('#modal-edit-event').modal({backdrop: true});
                }
            });
            if (event.url) {
                return false;
            }
        },

        /**
         * 拖动更新
         * @param event
         * @param delta
         * @param revertFunc
         */
        eventDrop: function (event, delta, revertFunc) {
            if (isAdmin == 0) {
                if (event.ispublic === 1) {
                    alert('此事件需要管理员权限');
                    revertFunc();
                    return false;
                }
            }
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../events/update_time',
                data: {
                    id: event.id,
                    ispublic: event.ispublic,
                    dayDiff: delta._days,
                    hoursDiff: delta._data.hours,
                    minutesDiff: delta._data.minutes,
                    _token: $('#csrf_token').attr('content')
                },
                success: function (result) {
                    if (result.statusCode === 200) {
                    } else {
                        revertFunc();
                    }
                    page.inform(
                        '操作结果', result.message,
                        result.statusCode === 200 ? page.success : page.failure
                    );
                }
            });
        },
        eventResize: function (event, delta, revertFunc) {
            if (isAdmin == 0) {
                if (event.ispublic === 1) {
                    alert('此事件需要管理员权限');
                    revertFunc();
                    return false;
                }
            }
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../events/update_time',
                data: {
                    id: event.id,
                    dayDiff: delta._days,
                    hoursDiff: delta._data.hours,
                    minutesDiff: delta._data.minutes,
                    _token: $('#csrf_token').attr('content'),
                    action: 'resize'
                },
                success: function (result) {
                    if (result.statusCode === 200) {
                    } else {
                        alert(result.message);
                        revertFunc();
                    }
                    page.inform(
                        '操作结果', result.message,
                        result.statusCode === 200 ? page.success : page.failure
                    );
                }
            });
        }
    });

    /**
     * 添加列表
     */
    /* ADDING EVENTS */
    var currColor = '#3c8dbc'; //Red by default
    //Color chooser button
    var colorChooser = $('#color-chooser-btn');

    $('#color-chooser > li > a').click(function (e) {
        e.preventDefault();
        //Save color
        currColor = $(this).css('color');
        //Add color effect to button
        $('#add-new-event').css({'background-color': currColor, 'border-color': currColor})
    });

    $('#add-new-event').on("click", function (e) {
        e.preventDefault();
        var event = $('<div />');
        event.css({
            'background-color': currColor,
            'border-color': currColor,
            'color': '#fff'
        }).addClass('external-event');

        $('#modal-show-event').modal({backdrop: true});

        if (isAdmin == 0) {
            $('.ispublic-form input[name="ispublic"]').attr("disabled", true);
        }

        $('.ispublic-form input[name="ispublic"]').change(function () {
            //console.log($('input[name="iscourse"]:checked').val());
            if ($('.ispublic-form  input[name="ispublic"]:checked').val() == 1) {
                $(".iscourse-form").show();
            } else {
                $(".iscourse-form").hide();
            }
        });

        $('.iscourse-form input[name="iscourse"]').change(function () {
            //console.log($('input[name="iscourse"]:checked').val());
            if ($('.iscourse-form input[name="iscourse"]:checked').val() == 1) {
                $(".educator_id-form").show();
                $(".subject_id-form").show();
            } else {
                $(".educator_id-form").hide();
                $(".subject_id-form").hide();
            }
        });

        $('.alertable-form input[name="alertable"]').change(function () {
            //console.log($('input[name="iscourse"]:checked').val());
            if ($('.alertable-form input[name="alertable"]:checked').val() == 1) {
                $(".alert_mins").show();
            } else {
                $(".alert_mins").hide();
            }
        });

        $('#confirm-create').off('click').click(function () {
            var data = $('#formEvent').serialize();
            data += "&" + "user_id" + "=" + id;
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../events/store',
                data: data,
                success: function (result) {
                    if (result.statusCode === 200) {
                        var $obj = eval(result.message);
                        event.attr('id', $obj.id);
                        event.html($obj.title);
                        $('#external-events').prepend(event);
                        init_events(event);
                    }
                    $('#formEvent')[0].reset();
                }
            })
        })
    })
});