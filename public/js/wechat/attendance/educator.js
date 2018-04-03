var token = $('#csrf_token').attr('content');

$("#start-date").calendar({value: []});

// 默认显示当天饼图数据
attendances({'_token': token});

// 初始化日期change事件
onDateChange();

// 获取考勤数据
$('#choose .close-popup').on('click', function () {
    var squad = $('#squad').attr('data-values'),
        rule = $('#rule').attr('data-values'),
        date = $('#start-date').val();

    if (!squad) {
        $.alert('请选择班级！');
        return false;
    }
    if (!rule) {
        $.alert('请选择规则！');
        return false;
    }
    if (!date) {
        $.alert('请选择日期！');
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
        url: 'chart',
        success: function (result) {
            console.log(result.data);
            if (result.statusCode === 200) {
                onClassChange(result['data']['squadnames']);
                onRuleChange(result['data']['rulenames']);
                showPie(result['data']['charts'], ['打卡', '异常', '未打卡']);
                $('.status-value').each(function (i) {
                    $(this).html(result['data']['charts'][i]['value']);
                });
                $('.modal-content').html(result.data.view);
            } else {
                $.alert(result.data);
            }
        },
        error: function () {
            $.alert('请加入相应的考勤规则！');
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
            type: 'GET',
            data: token,
            url: 'rule/' + classId,
            success: function (result) {
                if (result.statusCode === 200) {
                    $rule.select("update", {items: result.data});
                } else {
                    $.alert(result.data);
                    $rule.select("update", {items: [{}]});
                }
            },
            error: function () {
                $.alert('该年级未设置考勤规则！');
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

// 日期
function onDateChange() {
    $('#start-date').change(function () {
        checkRule();
    });
}

// 验证考勤规则
function checkRule() {
    $.ajax({
        type: 'GET',
        data: {
            _token: token,
            date: $('#start-date').val(),
            rule: $('#rule').attr('data-values')
        },
        url: 'check',
        success: function (result) {
            if (result.statusCode !== 200) {
                $.alert(result['message']);
            }
        },
        error: function () {
            $.alert('请选择和规则对应的星期！');
        }
    });
}