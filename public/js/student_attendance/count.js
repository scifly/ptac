page.initSelect2();
page.loadCss(page.plugins.daterangepicker.css);
$.getMultiScripts([page.plugins.echarts.js], page.siteRoot());
$.getMultiScripts([page.plugins.moment.js], page.siteRoot()).done(function(){
	$.getMultiScripts([page.plugins.daterangepicker.js], page.siteRoot()).done(function(){
		$('#reservation').daterangepicker({
		    "locale": {
		                format: 'YYYY-MM-DD',
		                separator: ' ~ ',
		                applyLabel: "应用",
		                cancelLabel: "取消",
		                resetLabel: "重置",
		                daysOfWeek: ['日', '一', '二', '三', '四', '五', '六'],
					    monthNames: ['一月', '二月', '三月', '四月', '五月', '六月',
					     '七月', '八月', '九月', '十月', '十一月', '十二月'
					    ],
					    customRangeLabel: "日历",
		            },
			ranges : {
		        '最近7日': [moment().subtract('days', 6), moment()],
		    },
		    startDate: moment().subtract('days', 6),
		    endDate: moment(),
		
		});
		getdata();
	});
});
/** 选择年级班级 */
var item = 'student_attendances/';
var type = 'count';
if (typeof custodian === 'undefined') {
    $.getMultiScripts(['js/custodian.relationship.js'], page.siteRoot())
        .done(function() { custodian.init(item, type, ''); });
} else { custodian.init(item, type, ''); }



var $search = $('#search');
var $token = $('#csrf_token');

$search.click(function () {
	$('#data-table tbody').html('');
    getdata();
});
//模拟图标数据

function getdata(){
	var time = $('#reservation').val();
	var time_arr = time.split('~');
    var formData = new FormData();
    var days = diy_time($.trim(time_arr[0]),$.trim(time_arr[1])); // 获取总共多少天
    formData.append('_token', $token.attr('content'));
    formData.append('class_id', $('#classId').val());
    formData.append('start_time', $.trim(time_arr[0]));
    formData.append('end_time', $.trim(time_arr[1]));
    formData.append('days', days);
	$.ajax({
        url: page.siteRoot() + "student_attendances/count",
        type: 'POST',
        cache: false,
        data: formData,
        processData: false,
        contentType: false,
        success: function (result) {
            var arrayTime = new Array();
            arrayTime.length = 0;
			var html = '';
            $.each(result, function (index, obj) {
                var datacon = obj;
            	html += '<tr>'+
            				'<td class="attendances-date">'+datacon.date+'</td>' +
							'<td class="attendances-data">'+
								'<a class="js-show-list normal" data-toggle="modal" data-target="#student-list" data-type="normal">'+datacon.normal+'</a>'+
								'/<a class="js-show-list abnormal" data-toggle="modal" data-target="#student-list" data-type="abnormal">'+datacon.abnormal+'</a>'+
								'/<a class="js-show-list surplus" data-toggle="modal" data-target="#student-list" data-type="surplus">'+datacon.surplus+'</a>'+
								'/<a class="all">'+datacon.all+'</a>'+
							'</td>' +
							'<td>'+
								'<div id="main'+index+'" style="height: 80px;width: 160px;"></div>'+
							'</td>'+
						'</tr>';
            });
            $('#data-table tbody').html(html);
            show_list();
            $.each(result, function (index, obj) {
                var datacon = obj;

                //饼图数据

                arrayTime.length=0;
                var json1 = {
                    value: datacon.normal,
                    name: '打卡'
                };
                var json2 = {
                    value: datacon.abnormal,
                    name: '异常'
                };
                var json3 = {
                    value: datacon.surplus,
                    name: '未打'
                };
                arrayTime.push(json1);
                arrayTime.push(json2);
                arrayTime.push(json3);
                showtable_pie(index,arrayTime);
            });
		}

	});

}

function diy_time(time1,time2){
    time1 = Date.parse(new Date(time1));
    time2 = Date.parse(new Date(time2));
    return time3 = Math.abs(parseInt((time2 - time1)/1000/3600/24) + 1);
}

function showtable_pie(index,arrayTime){
	var myChart = echarts.init(document.getElementById('main'+index));
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

function show_list(){
	$('.js-show-list').click(function(){
        var formData = new FormData();
        formData.append('_token', $token.attr('content'));
        var mydate = $(this).parent().prev().text();
        var type = $(this).attr('data-type');
        formData.append('class_id', $('#classId').val());
        formData.append('type', type);
        formData.append('date', mydate);
        $.ajax({
            url: page.siteRoot() + "student_attendances/student",
            type: 'POST',
            cache: false,
            data: formData,
            processData: false,
            contentType: false,
            success: function (result) {
                var html = '';
                for(var i=0;i<result.length;i++){
                    var data = result[i];
                    console.log
                }

            }
	    });
	});
}
