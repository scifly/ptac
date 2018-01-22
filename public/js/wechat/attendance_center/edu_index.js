$("#my-date").calendar();
var token = $('#csrf_token').attr('content');
var data = {'_token': token};
$('#choose .close-popup').on('click', function () {
    var squad = $('#squad').attr('data-values');
    var rule = $('#rule').attr('data-values');
    var date = $('#my-date').val();
    if (!squad) {
        $.alert('请先选择班级！');
        return false;
    }
    if (!rule) {
        $.alert('请先选择规则！');
        return false;
    }
    if (!date) {
        $.alert('请先选择日期！');
        return false;
    }
    var data = {'_token': token, 'squad': squad, 'rule': rule, 'date': date};
    getdata(data);
});
//班级列表
function getclasses(squads) {
    $("#squad").select({
        title: "选择班级",
        items: squads
    });
    classchange();
}
//规则列表
function getrules(rules) {
    $("#rule").select({
        title: "选择规则",
        items: rules
    });
}
//默认显示当天饼图数据
getdata(data);
function getdata(data) {

    $.ajax({
        type: 'POST',
        data: data,
        url: '../public/attendance_charts',
        success: function (result) {
            console.log(result.data);
            if (result.statusCode === 200) {
                //返回数据 渲染饼图
                console.log(result);
                getclasses(result.data.squadnames);
                getrules(result.data.rulenames);
                showtable_pie(result.data.charts, ['打卡', '异常', '未打卡']);
                $('.status-value').each(function (i) {
                    $(this).html(result.data.charts[i].value);
                });
                    $('.modal-content').html(result.data.view);
                }
        }
    });
}

function showtable_pie(arrayTime, legendData) {
    var myChart = echarts.init(document.getElementById('main'));
    var option = {
        title: {
            text: '打卡详情'
        },
        legend: {
            show: true,
            bottom: 10,
            left: 'center',
            data: legendData
        },
        color: ['#83db74', '#fdde52', '#fc7f4e'],
        series: [
            {
                name: '',
                type: 'pie',
                radius: '50%',
                center: ['50%', '40%'],
                data: arrayTime,
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ]
    };
    myChart.setOption(option);
}
$('.kaoqin-tongji .open-popup').click(function () {
    var type = $(this).attr('data-type');
    $('.modal-content .list').hide();
    $('.modal-content .list-' + type).show();
});

//选择班级事件
// function classchange() {
//     $('#squad').change(function () {
//         alert(2222222222222);
//     })
// }
