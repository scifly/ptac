var $classId = $('#class_id'),
    $sasId = $('#sas_id'),
    $startDate = $('#start_date'),
    today = wap.today();

$startDate.calendar({
    value: [today],
    dateFormat: 'yyyy-mm-dd'
});
$startDate.calendar('setValue', ['2018-08-28']);
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
        $.alert('请选择班级/规则/日期！');
        return false;
    }
    attendances({
        _token: wap.token(),
        classId: classId,
        sasId: sasId,
        startDate: startDate
    });
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
            showPie(result['chart'], ['打卡', '异常', '未打卡']);
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
function showPie(data, legend) {
    echarts.init(document.getElementById('main')).setOption({
        title: { text: '' },
        legend: {
            show: true,
            bottom: 10,
            left: 'center',
            data: legend
        },
        color: ['#83db74', '#fdde52', '#fc7f4e'],
        series: [
            {
                name: '',
                type: 'pie',
                radius: '50%',
                center: ['50%', '40%'],
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
            $.alert(result['message']);
        },
        error: function (e) {
            wap.errorHandler(e);
        }
    });
}