var token = $('#csrf_token').attr('content'),
    $startDate = $('#start-date');

$startDate.calendar({value: []});
// 默认显示当天饼图数据
attendances({'_token': token});
// 初始化日期change事件
onDateChange();
// 获取考勤数据
$('#choose .close-popup').on('click', function () {
    var squad = $('#squad').attr('data-values'),
        rule = $('#rule').attr('data-values'),
        date = $('#start-date').val();

    if (!squad || !rule || !date) {
        $.alert('请选择班级/规则/日期！');
        return false;
    }
    attendances({
        _token: token,
        squad: squad,
        rule: rule,
        date: date
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
            console.log(result.data);
            if (result.statusCode === 200) {
                onClassChange(result['data']['classNames']);
                onRuleChange(result['data']['ruleNames']);
                showPie(result['data']['charts'], ['打卡', '异常', '未打卡']);
                $('.status-value').each(function (i) {
                    $(this).html(result['data']['charts'][i]['value']);
                });
                $('.modal-content').html(result.data.view);
            } else {
                $.alert(result.data);
            }
        },
        error: function (e) {
            wap.errorHandler(e);
        }
    });
}
// 班级列表
function onClassChange(squads) {
    var $class = $("#squad");
    
    $class.select({
        title: "选择班级",
        items: squads
    });
    $class.change(function () {
        var classId = $(this).attr('data-values');
        var $rule = $('#rule');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                _token: token,
                classId: classId,
            },
            url: 'at/chart',
            success: function (result) {
                if (result.statusCode === 200) {
                    $rule.select("update", {items: result.data});
                } else {
                    $.alert(result.data);
                    $rule.select("update", {items: [{}]});
                }
            },
            error: function (e) {
                wap.errorHandler(e);
            }
        });
    });
}
// 规则列表
function onRuleChange(rules) {
    var $rule = $('#rule');
    
    $rule.select({
        title: "选择规则",
        items: rules
    });
    $rule.on('change', function () {
        var grade = $('#squad').attr('data-values');
        if (!grade) {
            $.alert('请先选择班级');
            $(this).val('');
        }
        checkRule();
    });
}
// 日期
function onDateChange() {
    $('#start-date').change(function () {
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
            _token: token,
            date: $('#start-date').val(),
            rule: $('#rule').attr('data-values'),
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