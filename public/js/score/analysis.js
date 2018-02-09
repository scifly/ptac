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
var $analysisType = $('#analysis-type');
var $StudentDatas = $('#student-datas');

var $class_id = $('#class_id');
//初始化班级列表
getSquadList($exam.val());
//初始化学生列表
getStudents($class_id.val());

$checkTest.on('ifChecked', function (event) {
    $Test.slideToggle();
    $Student.slideToggle();
});
$checkStudent.on('ifChecked', function (event) {
    $Test.slideToggle();
    $Student.slideToggle();
});

$show_data.off('click').click(function () {
    var examId = '';
    var squad = '';
    var classId = '';
    var student = '';
    var data = {};
    var $type = $(".iradio_minimal-blue.checked").find('input').val();
    if ($type == 0) {
        examId = $exam.val();
        squad = $squad.val();
        data = {'_token': $token.attr('content'), 'exam_id': examId, 'squad_id': squad, 'type': $type};
        // 异步填充表格数据
        $.ajax({
            type: 'POST',
            data: data,
            url: '../scores/analysis_data',
            success: function (result) {
                if (result.statusCode === 200) {
                    $datas.html(result.message);
                    $roles.hide();
                    $datas.show();
                    $show_rols = $('#close-data');
                    close_data();
                    getdata();
                } else {
                    page.inform('操作结果', result.message, page.failure);
                }
            }
        });
       } else {
        classId = $('#class_id').val();
        student = $('#student_id').val();
        data = {'_token': $token.attr('content'), 'class_id': classId, 'student_id': student, 'type': $type};
        // 异步填充表格数据
        $.ajax({
            type: 'POST',
            data: data,
            url: '../scores/analysis_data',
            success: function (result) {
                if (result.statusCode === 200) {
                    $datas.html(result.message);
                    $roles.hide();
                    $datas.show();
                    $show_rols = $('#close-data');
                    close_data();
                    getstudentdata();
                } else {
                    page.inform('操作结果', result.message, page.failure);
                }
            }
        });
    }
});

function close_data() {
    $show_rols.on('click', function () {
        $roles.show();
        $datas.hide();
    });
}

function close_student_data() {
    $show_rols.on('click', function () {
        $roles.show();
        $StudentDatas.hide();
    });
}

//模拟数据

function getdata() {
    var title = $('#sumscore').prev().text();

    var $data = $('#sumscore tbody tr td');
    var length = $data.length;

    var arrayTime = new Array();
    var legendData = new Array();
    var sum = $data.eq(1).text();
    $data.each(function (i, vo) {
        if (i == 0 || i == 1) {

        } else {
            var val = $(vo).text();
            var percent = (Math.round(val / sum * 10000) / 100.00).toFixed(2) + '%';
            var name = $('#sumscore thead tr th').eq(i).text() + '(' + percent + ')';
            var json1 = {
                'name': name,
                'value': val
            };
            legendData.push(name);
            arrayTime.push(json1);
        }

    });

    showtable_pie(arrayTime, legendData, title);
}

function showtable_pie(arrayTime, legendData, title) {
    var myChart = echarts.init($('.table-pie')[0]);
    var option = {
        title: {
            text: title,
            x: 'center',
            top: 0
        },
        tooltip: {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        legend: {
            show: true,
            bottom: 10,
            left: 'center',
            data: legendData
        },

        series: [
            {
                name: '成绩总分',
                type: 'pie',
                radius: '55%',
                center: ['50%', '50%'],
                data: arrayTime,
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

//根据班级获取学生列表
$class_id.on('change', function () {
    var $class_id = $(this).val();
    getStudents($class_id);
});

function getStudents($claId) {
    var $data = {'_token': $token.attr('content')};
    $.ajax({
        type: 'GET',
        data: $data,
        url: '../scores/clastudents/' + $claId,
        success: function (result) {
            $('#student_id').html(result.message);
        }
    });
}

//学生个人统计
function getstudentdata() {
    var $classranke = $('#classranke');
    var $graderanke = $('#graderanke');
    var subjectNum = parseInt($('#sub_number').val()) + 1; //科目数量（包括总分）
    var $data = $('#scores tbody tr');
    var length = $data.length;
    var subjectName = new Array();
    var classrankes = new Array();
    var graderankes = new Array();
    var testName = new Array();
    for (var i = 0; i < $data.length; i++) {
        testName.push($data.eq(i).find('.testName').text());
    }
    for (var p = 0; p < subjectNum; p++) {
        classrankes[p] = new Array();
        graderankes[p] = new Array();
        for (var q = 0; q < $data.length; q++) {
            var tmp = 1;
            var $datacon = $data.eq(q);
            var name = $datacon.find('.testName').text();
            var classval = $datacon.find('.classrankeItem').eq(p).text();
            var json1 = {
                'name': name,
                'value': classval
            };
            classrankes[p].push(json1);
            var gradeval = $datacon.find('.graderankeItem').eq(p).text();
            var json2 = {
                'name': name,
                'value': gradeval
            };
            graderankes[p].push(json2);
        }

        subjectName.push($('#scores thead tr .subjectName').eq(p).text());
        //班级排名图表
        $classranke.append('<div class="linetableitem" id="class-' + p + '">');
        var classtmp = 0;
        for(var k=0;k<classrankes[p].length;k++){
            if(classrankes[p][k].value !== '——'){
                classtmp = 1;
            }
        }
        if(classtmp === 1){
            showlinetable(classrankes[p], subjectName[p], testName, 'class', p);
        }else{
            $('#class-' + [p]).remove();
        }
        //年级排名图表
        $graderanke.append('<div class="linetableitem" id="grade-' + p + '">');
        var gradetmp = 0;
        for(var j=0;j<graderankes[p].length;j++){
            if(graderankes[p][j].value !== '——'){
                gradetmp = 1;
            }
        }
        if(gradetmp === 1){
            showlinetable(graderankes[p], subjectName[p], testName, 'grade', p);
        }else{
            $('#grade-' + [p]).remove();
        }

    }
}

function showlinetable(data, subjectname, testName, type, i) {
    // console.log(i);
    var myChart = echarts.init($('#' + type + '-' + i)[0]);
    var option = {
        title: {
            x: 'center',
            text: subjectname,
            textStyle: {
                fontWeight: '100',
                fontSize: '16'
            },
            top: 15
        },
        grid: {
            bottom: '80'
        },
        tooltip: {
            trigger: 'axis'
        },

        // xAxis: {
        //     show: false,
        //     type: 'category',
        //     boundaryGap: false,
        //     data: testName,
        //     axisLine: { // 隐藏X轴
        //         show: false
        //     },
        //     axisTick: { // 隐藏刻度线
        //         show: false
        //     },
        //     boundaryGap: false,
        //     axisLabel :{
        //         interval:0
        //     }
        // },
        // yAxis: {
        //     type: 'value',
        //     axisLabel: {
        //         formatter: '{value}'
        //     },
        //     inverse: true
        // },
        xAxis:  {
            type: 'category',
            boundaryGap: false,
            data: testName,
            boundaryGap : false,
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: '{value}'
            },
        },

        series: [
            {
                name: '排名',
                type: 'line',
                data: data,
                connectNulls:true,

            }

        ]
    };
    myChart.setOption(option);
}
