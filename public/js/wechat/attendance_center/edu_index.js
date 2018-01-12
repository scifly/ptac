 //班级列表
    $("#classlist").select({
        title: "选择班级",
        items: ["一年级1班", "一年级2班", "一年级3班", "一年级4班", "一年级5班", "一年级6班"]
    });
//模拟图标数据
getdata();
function getdata(){
    var item1 = {
        daka : 4,
        yichang : 2,
        weida : 1,
    };
    var data = {
        1 : item1,
    };
    var arrayTime = new Array();
    $.each(data, function (index, obj) {
        var datacon = obj;
        //      console.log(datacon)
        arrayTime.length=0;
        var json1 = {
            value:datacon.daka,
            name:'打卡'
        };
        var json2 = {
            value:datacon.yichang,
            name:'异常'
        };
        var json3 = {
            value:datacon.weida,
            name:'未打'
        };
        arrayTime.push(json1);
        arrayTime.push(json2);
        arrayTime.push(json3);
        showtable_pie(arrayTime);
        console.log(arrayTime);
    });
}
function showtable_pie(arrayTime){
    var myChart = echarts.init(document.getElementById('main'));
    option = {
        title : {
            text: '打卡详情',
        },
        color:['#83db74','#fdde52','#fc7f4e'],
        series : [
            {
                name: '访问来源',
                type: 'pie',
                radius : '50%',
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
