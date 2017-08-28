$(function () {
    //var id = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);
    //后台传过来的user_id
    var id = $('input[name="user_id"]').val();
    //前端判断是否为管理员
    var isAdmin = $('input[name="isAdmin"]').val();
    function init_events(ele) {
        ele.each(function () {
            // create an Event Object
            var eventObject = {
                title: $.trim($(this).text()), // use the element's text as the event title
                id: $(this).attr('id'),
                user_id: id,
                allDay:true
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
        editable: true,
        events: './calendar_events/' + id,
        /**
         * 拖动插入
         */
        droppable: true, // this allows things to be dropped onto the calendar !!!
        drop: function (date) {
            var originalEventObject = $(this).data('eventObject');
            var copiedEventObject = $.extend({}, originalEventObject);

            // console.log(copiedEventObject);
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
            //获取一个开始时间让拖动固定到日历上
            copiedEventObject.start = moment(date).format('YYYY-MM-DD HH:mm:ss');
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
                    url: './drag_events',
                    data: copiedEventObject,
                    success: function (result) {
                        if (result.statusCode === 200) {

                            //重新获取events
                            $('#calendar').fullCalendar('refetchEvents');
                        }
                    }
                });
            });
        },

        /**
         * 日程事件单击事件
         * @param calEvent
         * @returns {boolean}
         */
        eventClick: function (event) {

            $.ajax({
                type: 'GET',
                dataType: 'json',
                url: './edit/' + event.id,
                data: {ispublic: event.ispublic, userId: id},
                success: function (result) {
                    if (result.statusCode === 200) {
                        $('.show-form').html(result.data);
                        $('#confirm-update').on("click", function () {
                            var data = $('#formEventEdit').serialize();
                            data += "&" + "user_id" + "=" + id;
                            $.ajax({
                                type: 'PUT',
                                dataType: 'json',
                                url: './update/' + event.id,
                                data: data,
                                success: function (result) {
                                    if (result.statusCode === 200) {
                                        //保存成功
                                    }
                                }
                            });
                        });
                        $('#confirm-delete').on("click", function () {
                            alert('确定删除当前日程事件？');
                            $.ajax({
                                type: 'DELETE',
                                url: './delete/' + event.id,
                                data: {_token: $('#csrf_token').attr('content')},
                                success: function (result) {
                                    if (result.statusCode === 200) {
                                        //删除成功
                                        $('#calendar').fullCalendar('removeEvents', event.id);
                                    }
                                }
                            });
                        });
                    } else {
                        alert(result.message);
                        return;
                    }
                    $('#modal-edit-event').modal({backdrop: true});
                }
            });
            if (event.url) {
                // window.open(event.url);
                return false;
            }
        },

        /**
         * 拖动更新
         * @param event
         * @param dayDelta
         * @param minuteDelta
         * @param revertFunc
         */
        eventDrop: function (event, delta, revertFunc) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: './update_time',
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
                        //保存成功
                        // crud.inform('操作结果', result['message'], crud.success);
                    } else {
                        revertFunc();
                        //crud.inform('操作结果', result['message'], crud.failure);
                    }
                }
            });
        },
        eventResize: function (event, delta, revertFunc) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: './update_time',
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
                        //保存成功
                        // crud.inform('操作结果', result['message'], crud.success);
                    } else {
                        revertFunc();
                        //crud.inform('操作结果', result['message'], crud.failure);
                    }
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

        if(isAdmin == 0){
            $('.ispublic-form input[name="ispublic"]').attr("disabled",true);
        }
        $('.ispublic-form input[name="ispublic"]').change(function () {
            //console.log($('input[name="iscourse"]:checked').val());
            if ($(".iscourse-form").css("display") === "none") {
                $(".iscourse-form").show();
            } else {
                $(".iscourse-form").hide();
            }
        });

        $('.iscourse-form input[name="iscourse"]').change(function () {
            //console.log($('input[name="iscourse"]:checked').val());
            if ($(".educator_id-form").css("display") === "none") {
                $(".educator_id-form").show();
                $(".subject_id-form").show();
            } else {
                $(".educator_id-form").hide();
                $(".subject_id-form").hide();
            }
        });
        $('.alertable-form input[name="alertable"]').change(function () {
            //console.log($('input[name="iscourse"]:checked').val());
            if ($(".alert_mins").css("display") === "none") {
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
                url: './store',
                data: data,
                success: function (result) {
                    if (result.statusCode === 200) {
                        var $obj = eval(result.listDate);
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

// $('#add-new-event').on("click", function () {
//     $('#modal-show-event').modal({backdrop: true});
//     $('.iscourse-from input[name="iscourse"]').change(function () {
//         //console.log($('input[name="iscourse"]:checked').val());
//         if ($(".educator_id-from").css("display") === "none") {
//             $(".educator_id-from").show();
//             $(".subject_id-from").show();
//         } else {
//             $(".educator_id-from").hide();
//             $(".subject_id-from").hide();
//         }
//     });
//     $('.alertable-from input[name="alertable"]').change(function () {
//         //console.log($('input[name="iscourse"]:checked').val());
//         if ($(".alert_mins").css("display") === "none") {
//             $(".alert_mins").show();
//         } else {
//             $(".alert_mins").hide();
//         }
//     });
//
//     $('#confirm-create').on("click", function () {
//         var data = $('#formEvent').serialize();
//         data += "&" + "user_id" + "=" + id;
//         $.ajax({
//             type: 'POST',
//             dataType: 'json',
//             url: '../store',
//             data: data,
//             success: function (result) {
//                 if (result.statusCode === 200) {
//                     window.location.reload();
//                 }
//
//             }
//         });
//     })
// });