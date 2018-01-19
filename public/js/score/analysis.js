page.initSelect2();
page.initMinimalIcheck();
page.loadCss(page.plugins.analysis_css.css);
$.getMultiScripts([page.plugins.echarts_common.js], page.siteRoot());

var $token = $('#csrf_token');
var $checkTest = $('#byTest');
var $checkStudent = $('#byStudent');
var $Test = $('#Test');
var $Student = $('#Student');
var $show_data = $('#analysis');
var $show_rols = $('#close-data');
var $roles = $('#roles');
var $datas = $('#datas');
var $exam = $('#exam_id');
var $squad = $('#squad');
//初始化班级列表
getSquadList($exam.val());

$checkTest.on('ifChecked', function(event){
	$Test.slideToggle();
	$Student.slideToggle();
});
$checkStudent.on('ifChecked', function(event){
	$Test.slideToggle();
	$Student.slideToggle();
});

$show_data.off('click').click(function() {
    var examId = '';
    var squad = '';
    var classId = '';
    var student = '';
    var data = {};
    var $type = $(".iradio_minimal-blue.checked").find('input').val();
    if($type == 0){
         examId = $exam.val();
         squad = $squad.val();
         data = { '_token': $token.attr('content'), 'exam_id': examId, 'squad_id': squad, 'type': $type };
    } else {
         classId = $('#class_id').val();
         student = $('#student_id').val();
         data = { '_token': $token.attr('content'), 'class_id': classId, 'student_id': student, 'type': $type };
    }
    // 异步填充表格数据
    $.ajax({
        type: 'POST',
        data: data,
        url: '../scores/analysis_data',
        success: function (result) {

            $datas.html(result.message);
            $roles.hide();
            $datas.show();
            // getdata();
        }
    });
});
$show_rols.on('click', function() {
    $roles.show();
    $datas.hide();
});

//模拟数据

function getdata(){
	var title = $('#sumscore').prev().text();
	
	var $data = $('#sumscore tbody tr td');
	var length = $data.length;

	var arrayTime = new Array();
	var legendData = new Array();
	var sum = $data.eq(1).text();
	$data.each(function(i,vo){
	    if(i == 0 || i == 1){
	    	
	    }else{
	    	var val = $(vo).text();
	    	var percent = (Math.round(val / sum * 10000) / 100.00).toFixed(2) + '%';
	    	var name = $('#sumscore thead tr th').eq(i).text()+'('+percent+')';
	    	var json1 = {
	    		'name' : name,
	    		'value' : val,
	    	};
	    	legendData.push(name);
	    	arrayTime.push(json1);
	    }
	    
	});
	
    showtable_pie(arrayTime,legendData,title);
}

function showtable_pie(arrayTime,legendData,title){
	var myChart = echarts.init($('.table-pie')[0]);
	var option = {
	    title : {
	        text: title,
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
	            name: 'score',
	            type: 'pie',
	            radius : '40%',
	            center:['50%','50%'],
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

//根据成绩变动更新班级列表
$exam.on('change', function () {
    var $examId = $(this).val();
    getSquadList($examId);
});
//从后台获取班级列表
function getSquadList($id) {
    var $data = {'_token': $token.attr('content')};
    $.ajax({
        type: 'GET',
        data: $data,
        url: '../scores/clalists/' + $id,
        success: function (result) {
            $('#squad').html(result.message);
        }
    });
}
