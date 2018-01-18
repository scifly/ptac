page.initSelect2();
page.initMinimalIcheck();
page.loadCss(page.plugins.analysis_css.css);
$.getMultiScripts([page.plugins.echarts_common.js], page.siteRoot()).done(function(){
	
});

var $checkTest = $('#byTest');
var $checkStudent = $('#byStudent');
var $Test = $('#Test');
var $Student = $('#Student');
var $show_data = $('#analysis');
var $show_rols = $('#close-data');
var $roles = $('#roles');
var $datas = $('#datas');

$checkTest.on('ifChecked', function(event){
	$Test.slideToggle();
	$Student.slideToggle();
});
$checkStudent.on('ifChecked', function(event){
	$Test.slideToggle();
	$Student.slideToggle();
});

$show_data.on('click', function() {
    $roles.hide();
    $datas.show();
    getdata();
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
	option = {
	    title : {
	        text: title,
	        x:'center',
	        top:0,
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
