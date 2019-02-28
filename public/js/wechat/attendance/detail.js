var $year, $month,
    months = {
        '一月': '01', '二月': '02', '三月': '03', '四月': '04', '五月': '05', '六月': '06',
        '七月': '07', '八月': '08', '九月': '09', '十月': '10', '十一月': '11', '十二月': '12',
    };

// 初始化日历控件
$.when($("#inline-calendar").calendar({
    container: "#inline-calendar",
})).then(function () {
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: 'detail',
        success: function (result) {
            $year = $('.current-year-value');
            $month = $('.current-month-value');

            var days = result['days'],
                sYear = $year.text(),
                sMonth = $month.text(),
                iMonth = parseInt(months[sMonth]);

            $.each(days['nDays'], function () {
                var date = sYear + '-' + (iMonth - 1) + '-' + parseInt(this.substring(8, 10));
                $("[data-date= " + date + "]").addClass('picker-calendar-day-normal')
            });
            $.each(days['aDays'], function () {
                var date = sYear + '-' + (iMonth - 1) + '-' + parseInt(this.substring(8, 10));
                $("[data-date= " + date + "]").addClass('picker-calendar-day-abnormal')
            });
        },
        error: function (e) {
            wap.errorHandler(e);
        }
    });
});

// 按月统计
$(document).on('click', '.picker-calendar-year-picker a, .picker-calendar-month-picker a', function () {
    var $year = $('.current-year-value'),
        $month = $('.current-month-value');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'detail',
        data: {
            id: $('#id').val(),
            date: $year.text() + '-' + months[$month.text()] + '-01',
            type: 'month',
            _token: wap.token()
        },
        success: function (result) {
            var str = '',
                sYear = $year.text(),
                sMonth = $month.text();

            $.each(result['data']['nDays'], function () {
                var date = sYear + '-' + (parseInt(months[sMonth]) - 1) + '-' + parseInt(this.substring(8, 10));
                $("[data-date = " + date + "]").addClass('picker-calendar-day-normal');
            });
            $.each(result['data']['aDays'], function () {
                var date = sYear + '-' + (parseInt(months[sMonth]) - 1) + '-' + parseInt(this.substring(8, 10));
                $("[data-date = " + date + "]").addClass('picker-calendar-day-abnormal');
            });
            $('.picker-calendar-month-current .picker-calendar-day').eq(11).addClass('picker-calendar-day-leave');
            str +=
                '<tr>' +
                    '<td>' +
                        '<div class="kaoqin-date-circle okstatus"></div>' +
                        '<span class="pl10">正常:</span>' +
                        '<span>' + result['data']['nSum'] + '天</span>' +
                    '</td>' +
                    '<td>' +
                        '<div class="kaoqin-date-circle notstatus"></div>' +
                        '<span class="pl10">异常:</span>' +
                        '<span>' + result['data']['aSum'] + '天</span>' +
                    '</td>' +
                    '<td>' +
                        '<div class="kaoqin-date-circle reststatus"></div>' +
                        '<span class="pl10">请假:</span>' +
                        '<span>0 天</span>' +
                    '</td>' +
                '</tr>';
            $('.kaoqin-tongji tbody').html(str);
        },
        error: function (e) {
            wap.errorHandler(e);
        }
    });
});

// 按天统计
$('.picker-calendar-day').on('click', function () {
    var year = $(this).attr('data-year'),
        month = pad(parseInt($(this).attr('data-month')) + 1, 2),
        day = pad($(this).attr('data-day'), 2);

    $.ajax({
        type: 'post',
        dataType: 'json',
        url: 'detail',
        data: {
            id: $('#id').val(),
            date: year + '-' + month + '-' + day,
            type: 'day',
            _token: wap.token()
        },
        success: function (result) {
            var html = '',
                status = '',
                clocked_at = '',
                template =
                    '<div class="mt20 history-list-con" style="">' +
                    '<span class="js-kaoqin-status-morning" style="display:inline-block">%d</span>' +
                    '<span class="kaoqin-detail-status c-83db74">%s</span>' +
                    '<span class="time">%p</span>' +
                    '</div>';

            html += '<div class="js-kaoqin-detail-date kaoqin-detail-date">' + result['date'] + '</div>';
            if (result['ins'].length > 0) {
                for (var i = 0; i < result['ins'].length; i++) {
                    status = result['ins'][i]['status'] === 1 ? '正常' : '异常';
                    clocked_at = result['ins'][i]['clocked_at'];
                    html += template
                        .replace('%d', '上班')
                        .replace('%s', status)
                        .replace('%p', clocked_at);
                }
            } else {
                html += template
                    .replace('%d', '上班')
                    .replace('%s', 'n/a')
                    .replace('%p', 'n/a');
            }
            if (result['outs'].length > 0) {
                for (var j = 0; j < result['outs'].length; j++) {
                    status = result['outs'][i]['status'] === 1 ? '正常' : '异常';
                    clocked_at = result['outs'][i]['clocked_at'];
                    html += template
                        .replace('%d', '下班')
                        .replace('%s', status)
                        .replace('%p', clocked_at);
                }
            } else {
                html += template
                    .replace('%d', '下班')
                    .replace('%s', 'n/a')
                    .replace('%p', 'n/a');
            }
            $('.kaoqin-day-detail').html(html)
        },
        error: function (e) {
            wap.errorHandler(e);
        }
    });
});

function pad(n, width, z) {
    z = z || '0';
    n = n + '';
    return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}