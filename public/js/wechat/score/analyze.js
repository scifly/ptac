$('.subj-tab .tab-item').click(function(){
    $(this).parent().find('.tab-item').removeClass('cur');
    $(this).addClass('cur');
    var type = $(this).attr('data-type');
    $(this).parent().next().find('.show-item').removeClass('cur');
    $(this).parent().next().find('.'+type).addClass('cur');
    if(type === 'table'){
        var obj = $(this).parent().next().find('.show-item.score-level');
        getdata(obj);
    }
});

//模拟图标数据
function getdata(obj){
    var $data = obj.find('.table-count').find('tr');
    var arrayTime = [];
    var legendData = [];
    var sum = 0;
    $data.each(function(i,vo){
        if (i === 0){
            sum = $(vo).find('td').eq(1).text();
        } else {
            var val = $(vo).find('td').eq(1).text();
            var percent = (Math.round(val / sum * 10000) / 100.00).toFixed(2) + '%';
            if (val === 0){
                percent = 0;
            }
            var name = $(vo).find('td').eq(0).text()+'('+percent+')';
            var json1 = {
                'name' : name,
                'value' : val
            };

            legendData.push(name);
            arrayTime.push(json1);
        }

    });
    obj = obj.parent().parent();
    showtable_pie(arrayTime,legendData,obj);
}
function showtable_pie(arrayTime,legendData,obj){
    var myChart = echarts.init(obj.find('#main')[0]);
    var option = {
        title : { text: '本期成绩占比', x:'center', top:0 },
        tooltip : { trigger: 'item', formatter: "{a} <br/>{b} : {c} ({d}%)" },
        legend: { show:true, bottom: 10, left: 'center', data: legendData },
        series : [
            {
                name: '成绩占比',
                type: 'pie',
                radius : '40%',
                center:['50%','40%'],
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
