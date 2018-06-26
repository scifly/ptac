page.loadCss(plugins.fullcalendar.css);
$.getMultiScripts([plugins.fullcalendar.js, plugins.fullcalendar_moment.js, plugins.jqueryui.js]) .done(function() {
$(function () {
    //后台传过来的user_id
    var id = $('input[name="user_id"]').val();
    //前端判断是否为管理员
    var isAdmin = $('input[name="isAdmin"]').val();
    // 初始化列表事件
    function init_events(ele) {
        ele.each(function () {
            var eventObject = {
                title: $.trim($(this).find('span').text()), // use the element's text as the event title
                id: $(this).find('span').attr('id'),
                user_id: id
            };
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
    init_events($('#external-events li.external-event'));

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
            var $startPicker = $(".start-datepicker");
            var $endPicker = $(".end-datepicker");
            //弹窗显示表单
            $('#modal-create-event').modal({backdrop: true});
            //初始化表单中的开始时间和结束时间
            $startPicker.val(moment(date).format('YYYY-MM-DD HH:mm:ss'));
            $endPicker.val(moment(date).format('YYYY-MM-DD HH:mm:ss'));

            copiedEventObject._token = page.token();
            //调用日期时间选择器，格式化时间
            $startPicker.datepicker("destroy");
            $endPicker.datepicker("destroy");
            $startPicker.datepicker( $.datepicker.regional[ "zh-CN" ] );
            $endPicker.datepicker( $.datepicker.regional[ "zh-CN" ] );
            $startPicker.datetimepicker({
                dateFormat: "yy-mm-dd",
                showSecond: true,
                timeFormat: 'hh:mm:ss'
            });
            $endPicker.datetimepicker({
                dateFormat: "yy-mm-dd",
                showSecond: true,
                timeFormat: 'hh:mm:ss'
            });
            //点击保存后获取时间值 因页面未刷新需要结束上次on(click)事件
            $('#confirm-add-time').off('click').click(function () {
                copiedEventObject.start = $startPicker.val();
                copiedEventObject.end = $endPicker.val();
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '../events/drag_events',
                    data: copiedEventObject,
                    success: function (result) {
                            //重新获取events
                        $('#calendar').fullCalendar('refetchEvents');
                        page.inform(result.title, result.message, page.success);
                    }
                });
            });
        },

        // /**
        //  *
        //  * 日历事件ajax请求
        //  */
        // ajaxRequest: function(requestType,url,data){
        //     $.ajax({
        //         type: requestType,
        //         dataType: 'json',
        //         url: url,
        //         data: data
        //     });
        // },
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
                        $('.start-datepicker').datepicker( $.datepicker.regional[ "zh-CN" ] );
                        $('.end-datepicker').datepicker( $.datepicker.regional[ "zh-CN" ] );
                        $(".start-datepicker").datetimepicker({

                            dateFormat: "yy-mm-dd",
                            showSecond: true,
                            timeFormat: 'hh:mm:ss'
                        });
                        $(".end-datepicker").datetimepicker({
                            dateFormat: "yy-mm-dd",
                            showSecond: true,
                            timeFormat: 'hh:mm:ss'
                        });

                        $('#confirm-update').on("click", function () {
                            if ($('#formEventEdit').parsley().validate()) {
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
                                            $('#calendar').fullCalendar('refetchEvents');
                                        }
                                        page.inform(
                                            '操作结果', result.message,
                                            result.statusCode === 200 ? page.success : page.failure
                                        );
                                    }
                                });
                            } else {
                                return false;
                            }
                        });
                        $('#confirm-delete-event').on("click", function () {
                            var ret = confirm("确定删除当前日程事件？");
                            if (ret) {
                                $.ajax({
                                    type: 'DELETE',
                                    url: '../events/delete/' + event.id,
                                    data: {_token: page.token()},
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
         * 拖动更新,实时保存
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
                    _token: page.token()
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

        /**
         *拉长缩放更新时间
         *
         * @param event
         * @param delta
         * @param revertFunc
         * @returns {boolean}
         */
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
                    _token: page.token(),
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
    $('#add-new-event').on("click", function (e) {
        e.preventDefault();
        var event = $('<li />');
        event.addClass('external-event');
        $('#modal-show-event').modal({backdrop: true});

        if (isAdmin == 0) {
            $('.ispublic-form input[name="ispublic"]').attr("disabled", true);
        }

        $('.ispublic-form input[name="ispublic"]').change(function () {
            if ($('.ispublic-form  input[name="ispublic"]:checked').val() == 1) {
                $(".iscourse-form").show();
            } else {
                $(".iscourse-form").hide();
            }
        });

        $('.iscourse-form input[name="iscourse"]').change(function () {
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
            //前端进行表单验证,若通过
            if ($('#formEvent').parsley().validate()) {
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
                            event.append("<span id= '" + $obj.id + "'>" + $obj.title + "</span>");
                            event.append('<div class="tools"><i class="fa fa-trash-o trash-list"></i></div>');
                            $('#external-events').append(event);
                            init_events(event);
                        }
                        $('#formEvent')[0].reset();
                    }
                })
            } else {
                return false;
            }
        })
    });

    /**
     * 删除列表
     */
    var listId, row;
    $(document).on('click', '.trash-list', function () {
        listId = $(this).parent().prev('span').attr('id');
        row = $(this).parent().parent('li');
        $('#modal-dialog').modal({backdrop: true});
    });
    $('#confirm-delete').on('click', function () {
        $.ajax({
            type: 'DELETE',
            dataType: 'json',
            url: '../events/delete/' + listId,
            data: {_token: page.token()},
            success: function (result) {
                if (result.statusCode === 200) {
                    row.remove();
                }
            }
        })
    })
});
});
