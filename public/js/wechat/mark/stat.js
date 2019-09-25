//班级列表
getdata();
function getdata(){
    var arrayTime = [], legendData = [];

    $('.scoreItem').each(function(i, vo) {
        var val = $(vo).find('.myscore').find('.scoredata').text(),
            name = $(vo).find('.title').text(),
            json1 = { 'name' : name, 'value' : val };

        legendData.push(name);
        arrayTime.push(json1);
    });
    showtable(arrayTime, legendData);
}

function showtable(arrayTime, legendData){
    var myChart = echarts.init($('#main')[0]),
        option = {
        title : { text: '本考次占比', x: 'center', top: 0 },
        tooltip : { trigger: 'item', formatter: "{a} <br/>{b} : {c} ({d}%)" },
        legend: { show:true, bottom: 10, left: 'center', data: legendData},
        series : [{
            name: '成绩占比', type: 'pie', radius : '55%', center: ['50%','45%'], data: arrayTime,
            itemStyle: {
                emphasis: { shadowBlur: 10, shadowOffsetX: 0, shadowColor: 'rgba(0, 0, 0, 0.5)' }
            }
        }]
    };

    myChart.setOption(option);
}
