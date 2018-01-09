
page.initSelect2({
    templateResult: page.formatStateImg,
    templateSelection: page.formatStateImg,
    language: "zh-CN",
});
/** 选择年级班级 */
var item = 'student_attendance_statistics/';
var type = 'index';
if (typeof custodian === 'undefined') {
    $.getMultiScripts(['js/custodian.relationship.js'], page.siteRoot())
        .done(function() { custodian.init(item, type, ''); });
} else { custodian.init(item, type, ''); }


$('#reservation').daterangepicker();


$('.select2').select2();
$('#reservation').daterangepicker({
	ranges : {  
        '最近7日': [moment().subtract('days', 6), moment()],  
    },  
    startDate: moment().subtract('days', 6),
    endDate: moment(),
});

//模拟图标数据
getdata();
function getdata(){
	var item1 = {
		daka : 4,
		yichang : 2,
		weida : 1,
	};
	var item2 = {
		daka : 5,
		yichang : 3,
		weida : 2,
	};
	var item3 = {
		daka : 2,
		yichang : 4,
		weida : 2,
	};
	var data = {
		1 : item1,
		2 : item2,
		3 : item3,
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
        showtable_pie(index,arrayTime);
    });
}
function showtable_pie(index,arrayTime){
	var myChart = echarts.init(document.getElementById('main'+index));
	option = {
	    title : {
	        text: '打卡详情',
	    },
	    color:['#9FDABF','#334B5C','#C23531'],
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