var $studentId = $('#student_id'),
    $examId = $('#exam_id');

$('.tab-item').click(function(){
    $('.tab-item').removeClass('active');
    $(this).addClass('active');
    getdata($(this));
});
getdata('-1');
function getdata($active){
    var subject_id = $active === '-1' ? $active : $active.find('input').val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "sc/graph",
        data: {
            _token: wap.token(),
            subject: subject_id,
            student_id: $studentId.val(),
            exam_id: $examId.val()
        },
        success: function (result) {
            // 模拟班排名数据
            // var class_test_name = ['9月月考','10月月考','11月月考','12月月考','1月月考','2月月考','3月月考','4月月考','5月月考','6月月考','7月月考','8月月考','9月月考',''];
            // var class_data = ['1','4','3','1','1','1','1','4','1','3','4','4','5'];
            // showtable_class(class_data,class_test_name);
            // //
            // //模拟年排名数据
            // var grade_test_name = ['9月月考','10月月考','11月月考','12月月考','1月月考','2月月考','3月月考','4月月考','5月月考','6月月考','7月月考','8月月考','9月月考',''];
            // var grade_data = ['1','4','3','1','1','1','1','4','1','3','4','4','5'];
            // showtable_grade(grade_data,grade_test_name);
            showtable_class(result['class_rank'], result['exam']);
            showtable_grade(result['grade_rank'], result['exam']);
        },
        error: function (e) {
            wap.errorHandler(e);
        }
    });
}
function showtable_class(class_data, class_test_name){
    var type = $.trim($('.tab-item.active').text());

    if (type === '总分') { type = ''; }
    var class_title = type+'班排名走势图',
        myChart = echarts.init($('.class-rank')[0]),
        option = {
            title: {
                x: 'center',
                text: class_title,
                textStyle: { fontWeight: '100', fontSize: '16' },
                top: 15,
            },
            grid:{ bottom:'80' },
            tooltip: { trigger: 'axis' },
            legend: { data:['班排名'], x: 'left', left:10, top:10,},
            xAxis:  { type: 'category', data: class_test_name, boundaryGap : false },
            yAxis: { type: 'value', axisLabel: { formatter: '{value}' } },
            dataZoom: [
                { type: 'slider', show: true, xAxisIndex: [0], start: 0, end: 50 }
            ],
            series: [{ name:'班排名', type:'line', data:class_data }]
        };

    myChart.setOption(option);
}

function showtable_grade(grade_data, grade_test_name){
    var type = $.trim($('.tab-item.active').text());
    if (type === '总分') { type = ''; }
    var grade_title = type+'年排名走势图';

    var myChart = echarts.init($('.grade-rank')[0]),
        option = {
            title: {
                x: 'center',
                text: grade_title,
                textStyle: { fontWeight: '100', fontSize: '16' },
                top: 15,
            },
            grid:{ bottom:'80' },
            tooltip: { trigger: 'axis' },
            legend: { data:['年排名'], x: 'left', left:10, top:10 },
            xAxis: { type: 'category', data: grade_test_name, boundaryGap : false },
            yAxis: { type: 'value', axisLabel: { formatter: '{value}' } },
            dataZoom: [{ type: 'slider', show: true, xAxisIndex: [0], start: 0, end: 50 }],
            series: [{ name:'年排名', type:'line', data:grade_data }]
        };

    myChart.setOption(option);
}