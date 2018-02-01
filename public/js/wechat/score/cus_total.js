//班级列表
getdata();
function getdata(){
    var arrayTime = new Array();
    var legendData = new Array();
    $('.scoreItem').each(function(i,vo){
        var val = $(vo).find('.myscore').find('.scoredata').text();
        var name = $(vo).find('.title').text();
        var json1 = {
            'name' : name,
            'value' : val
        };
        legendData.push(name);
        arrayTime.push(json1);
    });
    showtable(arrayTime,legendData);
}

function showtable(arrayTime,legendData){
    var myChart = echarts.init($('#main')[0]);
    var option = {
        title : {
            text: '本考次占比',
            x:'center',
            top:0
        },
        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        legend: {
            show:true,
            bottom: 10,
            left: 'center',
            data: legendData
        },

        series : [
            {
                name: '成绩占比',
                type: 'pie',
                radius : '55%',
                center:['50%','45%'],
                data:arrayTime,
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
