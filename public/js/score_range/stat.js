//年级班级联动显示
$('#class').on('click', function () {
    $('.class_div').show();
    $('.grade_div').hide();
});
$('#grade').on('click', function () {
    $('.grade_div').show();
    $('.class_div').hide();
});
//提交表单
$('#submit').on('click', function () {
    $('.chart').show();
    var $tbody = $('#data-table tbody'),
        data = $('#form').serialize();
    $tbody.html('');
    $.ajax({
        type: 'POST',
        url: 'statistics',
        dataType: 'json',
        data: data,
        success: function (result) {
            var labels = [];
            var dataset = [];
            $.each(result, function () {
                $tbody.append(
                    "<tr>" +
                        "<td>" + this['id'] + "</td>" +
                        "<td>" + this['name'] + "</td>" +
                        "<td>" + this['number'] + "</td>" +
                        "<td>" + this['precentage'] + "</td>" +
                    "</tr>"
                );
                labels.push(this['name'] + ' 统计人数：' + this['number']);
                dataset.push(this['precentage']);
            });
            chart(labels, dataset);
            return false;
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });
});
// echarts
function chart(labels, data) {
    var myChart = echarts.init(document.getElementById('barChart'));

    // 指定图表的配置项和数据
    var option = {
        color: ['#3398DB'],
        title: {
            text: '成绩分析'
        },
        tooltip: {
            formatter: function (params) {
                var style =
                    'display:inline-block; ' +
                    'margin-right:5px; ' +
                    'border-radius:50%; ' +
                    'width:9px; height:9px; ' +
                    'background-color:#3398DB';
                return '<span style="' + style + '"></span>' +
                    params.name + '<br>' + '所占百分比：' + params.value + '%';
            }
        },
        xAxis: {
            data: labels
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: '{value} % '
            }
        },
        series: [{
            name: '成绩分析',
            type: 'bar',
            data: data
        }]
    };
    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);
}
