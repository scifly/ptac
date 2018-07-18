var $classId = $('#class_id'),
    $sasId = $('#sas_id'),
    $startDate = $('#start_date');

$startDate.calendar({value: []});
// 默认显示当天饼图数据
attendances({'_token': wap.token()});
// 初始化日期change事件
onDateChange();
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
    var type = $(this).attr('data-type');

    $('.modal-content .list').hide();
    $('.modal-content .list-' + type).show();
});

function attendances(data) {
    $.ajax({
        type: 'POST',
        data: data,
        url: 'at/chart',
        success: function (result) {
            onClassChange(result['data']['classNames']);
            onRuleChange(result['data']['ruleNames']);
            showPie(result['data']['charts'], ['打卡', '异常', '未打卡']);
            $('.status-value').each(function (i) {
                $(this).html(result['data']['charts'][i]['value']);
            });
            $('.modal-content').html(result.data.view);
        },
        error: function (e) {
            wap.errorHandler(e);
        }
    });
}
// 班级列表
function onClassChange() {
    $classId.on('change', function () {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                _token: wap.token(),
                classId: $classId.val(),
            },
            url: 'at/chart',
            success: function (result) {
                $sasId.html(result['options']);
            },
            error: function (e) {
                wap.errorHandler(e);
            }
        });
    });
}
// 规则列表
function onRuleChange() {
    $sasId.on('change', function () {
        checkRule();
    });
}
// 日期
function onDateChange() {
    $startDate.change(function () {
        checkRule();
    });
}
// 显示饼图
function showPie(data, legend) {
    echarts.init(document.getElementById('main')).setOption({
        title: {
            text: '打卡详情'
        },
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
            date: $startDate.val(),
            rule: $sasId.val(),
            check: true
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