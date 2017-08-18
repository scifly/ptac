$(function () {

    function init_events(ele) {
        ele.each(function () {
            // create an Event Object
            // it doesn't need to have a start or end
            var eventObject = {
                title: $.trim($(this).text()), // use the element's text as the event title
                id: $(this).attr('id')
            }
            // store the Event Object in the DOM element so we can get to it later
            $(this).data('eventObject', eventObject);
            // make the event draggable using jQuery UI 拖动
            $(this).draggable({
                zIndex: 1070,
                revert: true, // will cause the event to go back to its
                revertDuration: 0  //  original position after the drag
            })

        })
    }

    init_events($('#external-events div.external-event'));
    var id = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);

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
        events: '../select_events/' + id,
        editable: true,

        /**
         * 拖动插入
         */
        droppable: true, // this allows things to be dropped onto the calendar !!!
        drop: function (date) { // this function is called when something is dropped
            // retrieve the dropped element's stored Event Object
            var originalEventObject = $(this).data('eventObject');
            // we need to copy it, so that multiple events don't have a reference to the same object
            var copiedEventObject = $.extend({}, originalEventObject);
            //弹窗显示表单
            $('#modal-create-event').modal({backdrop: true});

            //定义一个参数判断是否删除拖动的这个列表
            var isRemoveList = false;
            if ($('#drop-remove').is(':checked')) {
                // if so, remove the element from the "Draggable Events" list
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
            //点击保存后获取时间值
            $('#confirm-add-time').on("click", function () {
                copiedEventObject.start = $(".start-datepicker").val();
                copiedEventObject.end = $(".end-datepicker").val();
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '../drag_events',
                    data: copiedEventObject,
                    success: function () {
                        if (result.statusCode === 200) {

                        }else {

                        }
                    }
                });
            });
            //立即渲染事件添加到日历中，且固定到日历上（指翻动日历时，刚增加的事件不会消失）
            $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
            // is the "remove after drop" checkbox checked?
        },

        /**
         * 拖动更新
         * @param event
         * @param dayDelta
         * @param minuteDelta
         * @param allDay
         * @param revertFunc
         */
        eventDrop: function (event, dayDelta, minuteDelta, allDay, revertFunc) {
            alert('11111');
        },

        eventClick: function (calEvent) {
            // console.log(event);
            // return;
            console.log(calEvent);
            // $.ajax({
            //     type: 'GET',
            //     dataType: 'json',
            //     url: '../edit' + calEvent.id,
            //     success: function () {
            //         if (result.statusCode === 200) {
            //
            //         }else {
            //
            //         }
            //     }
            // });

            $('#modal-edit-event').modal({backdrop: true});

        }
        /* selectable: true,
         select:function (startDate, endDate) {

         }*/
    });

    /**
     * 添加列表
     */
    $('#add-new-event').on("click", function () {
        $('#modal-show-event').modal({backdrop: true});
        $('.iscourse-from input[name="iscourse"]').change(function () {
            //console.log($('input[name="iscourse"]:checked').val());
            if ($(".educator_id-from").css("display") === "none") {
                $(".educator_id-from").show();
                $(".subject_id-from").show();
            } else {
                $(".educator_id-from").hide();
                $(".subject_id-from").hide();
            }
        });
        $('.alertable-from input[name="alertable"]').change(function () {
            //console.log($('input[name="iscourse"]:checked').val());
            if ($(".alert_mins").css("display") === "none") {
                $(".alert_mins").show();
            } else {
                $(".alert_mins").hide();
            }
        });
        $('#confirm-create').on("click", function () {
            var data = $('#formEvent').serialize();
              data += "&"+"user_id" + "=" + id;
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../store',
                data: data,
                success: function (result) {
                    if (result.statusCode === 200){
                        window.location.reload();
                    }

                }
            });
        })
    });

    /*var currColor = '#3c8dbc';//Red by default
     //Color chooser button
     var colorChooser = $('#color-chooser-btn');
     $('#color-chooser > li > a').click(function (e) {
     e.preventDefault();
     //Save color
     currColor = $(this).css('color');
     //Add color effect to button
     $('#add-new-event').css({'background-color': currColor, 'border-color': currColor})
     });
     $('#add-new-event').click(function (e) {
     e.preventDefault();
     //Get value and make sure it is not null
     var val = $('#new-event').val();
     if (val.length == 0) {
     return
     }

     //Create events
     var event = $('<div />');
     event.css({
     'background-color': currColor,
     'border-color': currColor,
     'color': '#fff'
     }).addClass('external-event');
     event.html(val);
     $('#external-events').prepend(event);

     //Add draggable funtionality
     init_events(event);

     //Remove event from text input
     $('#new-event').val('');
     });*/
});