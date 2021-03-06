var $classId = $('#class_id'),
    $sasId = $('#sas_id'),
    $startDate = $('#start_date'),
    $passed = $('#passed'),
    today = wap.today();

$startDate.calendar({
    value: [today],
    dateFormat: 'yyyy-mm-dd'
});
$classId.on('change', function () {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'at/chart',
        data: {
            _token: wap.token(),
            classId: $classId.val(),
            action: true
        },
        success: function (result) {
            $sasId.html(result['options']);
        },
        error: function (e) {
            wap.errorHandler(e);
        }
    });
});
$sasId.on('change', function () { checkRule(); });
$startDate.on('change', function () { checkRule(); });
// 获取考勤数据
$('#choose .close-popup').on('click', function () {
    var classId = $classId.val(),
        sasId = $sasId.val(),
        startDate = $startDate.val();

    if (!classId || !sasId || !startDate) {
        $.toptip('请选择班级/规则/日期！', 'error');
        return false;
    }
    if ($passed.val() === '1') {
        attendances({
            _token: wap.token(),
            classId: classId,
            sasId: sasId,
            startDate: startDate
        });
    } else {
        $.toptip('指定日期与规则对应的星期不匹配', 'warning');
    }
    return true;
});
$('.kaoqin-tongji .open-popup').click(function () {
    var type = $(this).data('type');

    $('.modal-content .list').hide();
    $('.modal-content .list-' + type).show();
});
// 默认显示当天饼图数据
attendances({'_token': wap.token()});

/** Helper functions ------------------------------------------------------------------------------------------------ */
function attendances(data) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        data: data,
        url: 'at/chart',
        success: function (result) {
            var chartTitle = $classId.find(':selected').text() + "\n\n" +
                $sasId.find(':selected').text() + "\n\n" +
                $startDate.val();
            showPie(
                result['chart'],
                ['打卡', '异常', '未打卡'],
                chartTitle
            );
            $('.status-value').each(function (i) {
                $(this).html(result['chart'][i]['value']);
            });
            $('.modal-content').html(result['view']);
        },
        error: function (e) {
            wap.errorHandler(e);
        }
    });
}
// 显示饼图
function showPie(data, legend, title) {
    echarts.init(document.getElementById('main')).setOption({
        title: {
            text: title || '',
            x: 'center',
            textStyle: {
                fontSize: 16,
                color: 'gray',
                fontWeight: 'normal',
                lineHeight: 100,

                // rich: { a: {  }}
            }
        },
        legend: {
            show: true,
            bottom: 5,
            left: '5%',
            data: legend,
            orient: 'vertical'
        },
        color: ['#83db74', '#fdde52', '#fc7f4e'],
        series: [
            {
                name: '',
                type: 'pie',
                radius: '50%',
                center: ['50%', '65%'],
                data: data,
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ]
    });
}
// 验证考勤规则
function checkRule() {
    $.ajax({
        type: 'POST',
        data: {
            _token: wap.token(),
            startDate: $startDate.val(),
            sasId: $sasId.val(),
            action: true
        },
        url: 'at/chart',
        success: function (result) {
            $.toptip(result['message'], 'success');
            $passed.val(1);
        },
        error: function (e) {
            wap.errorHandler(e);
            $passed.val(0);
        }
    });
}