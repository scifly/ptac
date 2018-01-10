
page.initSelect2();
/** 选择年级班级 */
var item = 'student_attendances/';
var type = 'count';
if (typeof custodian === 'undefined') {
    $.getMultiScripts(['js/custodian.relationship.js'], page.siteRoot())
        .done(function() { custodian.init(item, type, ''); });
} else { custodian.init(item, type, ''); }


// $('#reservation').daterangepicker();
$('.select2').select2();
$('#reservation').daterangepicker({
    format: 'YYYY-MM-DD',
	ranges : {
        '最近7日': [moment().subtract('days', 6), moment()],
    },
    startDate: moment().subtract('days', 6),
    endDate: moment(),

});
var $search = $('#search');
var $token = $('#csrf_token');
$search.click(function () {
	$('#data-table tbody').html('');
    getdata();
});
//模拟图标数据
getdata();
function getdata(){
	var time = $('#reservation').val();
	// console.log(time);
	var time_arr = time.split('-');
    var formData = new FormData();

    formData.append('_token', $token.attr('content'));
    formData.append('class_id', $('#classId').val());
    formData.append('start_time', $.trim(time_arr[0]));
    formData.append('end_time', $.trim(time_arr[1]));


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
            	html += '<tr><td>'+datacon.date+'</td>' +
							'<td>'+datacon.normal+'/'+datacon.abnormal+'/'+datacon.surplus+'</td>' +
							'<td><div id="main'+index+'" style="height: 80px;width: 160px;"></div></td></tr>';
                $('#data-table tbody').html(html);
            });
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