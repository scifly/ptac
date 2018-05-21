$.getMultiScripts(['js/score/score.js']).done(function () {
    $.score().stat();
});
var type = 'student';
$('input[name="type"]').on('ifClicked', function () {
    alert("You clicked " + this.value);
});
// page.initSelect2();
// page.initMinimalIcheck();
// page.loadCss('css/score/stat.css');
// $.getMultiScripts([plugins.echarts_common.js]);
//
// var token = $('#csrf_token').attr('content'),
//     $type = $('#type'),
//     $exam = $('#exam'),
//     $class = $('#class'),
//     $student = $('#student'),
//     $examId = $('#exam_id'),
//     $classId = $('#class_id'),
//     $studentId = $('#student_id'),
//     $result = $('#result'),
//     $stat = $('#stat');
//
// $examId.on('change', function () {
//     list('class', $examId.val());
// });
//
// $classId.on('change', function () {
//     list('student', $classId.val());
// });
//
// $stat.off('click').on('click', function () {
//     var type = $type.val(),
//         data = {
//             _token: token,
//             classId: $classId.val()
//         };
//
//     $.ajax({
//         type: 'POST',
//         data: $.extends(data, type === 0 ? {examId: $examId.val()} : {studentId: $studentId.val()}),
//         url: '../scores/stat',
//         success: function (result) {
//             $result.html(result.message);
//             type === 0 ? getData() : getstudentdata();
//         },
//         error: function (e) {
//             page.errorHandler(e);
//         }
//     });
// });
//
// //模拟数据
// function getdata() {
//
//     var title = $('#sumscore').prev().text(),
//         $data = $('#sumscore tbody tr td');
//     var arrayTime = [];
//     var legendData = [];
//     var sum = $data.eq(1).text();
//     $data.each(function (i, vo) {
//
//         if (i === 0 || i === 1) {
//         } else {
//             var val = $(vo).text();
//             var percent = (Math.round(val / sum * 10000) / 100.00).toFixed(2) + '%';
//             var name = $('#sumscore thead tr th').eq(i).text() + '(' + percent + ')';
//             var json1 = {
//                 'name': name,
//                 'value': val
//             };
//             legendData.push(name);
//             arrayTime.push(json1);
//
//         }
//     });
//     pieChart(arrayTime, legendData, title);
//
// }
//
// function pieChart(arrayTime, legendData, title) {
//     var myChart = echarts.init($('.table-pie')[0]);
//     var option = {
//         title: {
//             text: title,
//             x: 'center',
//             top: 0
//         },
//         tooltip: {
//             trigger: 'item',
//             formatter: "{a} <br/>{b} : {c} ({d}%)"
//         },
//         legend: {
//             show: true,
//             bottom: 10,
//             left: 'center',
//             data: legendData
//         },
//
//         series: [
//             {
//                 name: '成绩总分',
//                 type: 'pie',
//                 radius: '55%',
//                 center: ['50%', '50%'],
//                 data: arrayTime,
//                 itemStyle: {
//                     emphasis: {
//                         shadowBlur: 10,
//                         shadowOffsetX: 0,
//                         shadowColor: 'rgba(0, 0, 0, 0.5)'
//                     }
//                 }
//             }
//         ]
//     };
//     myChart.setOption(option);
// }
//
// function list(type, id) {
//     $.ajax({
//         type: 'GET',
//         data: { _token: token },
//         url: '../scores/stat/' + type + '/' + id,
//         success: function (result) {
//             var $typeId = $('#' + type + '_id'),
//                 $typeNext = $typeId.next(),
//                 $typePrev = $typeId.prev();
//
//             $typeNext.remove();
//             $typeId.remove();
//             $typePrev(result['html']);
//
//             page.initSelect2();
//         }
//     });
// }
//
// function getstudentdata() {
//
//     var $classranke = $('#classranke'),
//         $graderanke = $('#graderanke'),
//         subjectNum = parseInt($('#sub_number').val()) + 1, // 科目数量（包括总分）
//         $data = $('#scores tbody tr'),
//         subjects = [],
//         classRanks = [],
//         gradeRanks = [],
//         exams = [];
//     for (var i = 0; i < $data.length; i++) {
//         exams.push($data.eq(i).find('.testName').text());
//     }
//     for (var p = 0; p < subjectNum; p++) {
//         classRanks[p] = [];
//         gradeRanks[p] = [];
//         for (var q = 0; q < $data.length; q++) {
//             var $datacon = $data.eq(q);
//             var name = $datacon.find('.testName').text();
//             var classval = $datacon.find('.classrankeItem').eq(p).text();
//             var json1 = {
//                 'name': name,
//                 'value': classval
//             };
//             classRanks[p].push(json1);
//             var gradeval = $datacon.find('.graderankeItem').eq(p).text();
//             var json2 = {
//                 'name': name,
//                 'value': gradeval
//             };
//             gradeRanks[p].push(json2);
//         }
//
//         subjects.push($('#scores thead tr .subjectName').eq(p).text());
//         //班级排名图表
//         $classranke.append('<div class="linetableitem" id="class-' + p + '">');
//         var classtmp = 0;
//         for (var k = 0; k < classRanks[p].length; k++) {
//             if (classRanks[p][k].value !== '——') {
//                 classtmp = 1;
//             }
//         }
//         if (classtmp === 1) {
//             chart(classRanks[p], subjects[p], exams, 'class', p);
//         } else {
//             $('#class-' + [p]).remove();
//         }
//         //年级排名图表
//         $graderanke.append('<div class="linetableitem" id="grade-' + p + '">');
//         var gradetmp = 0;
//         for (var j = 0; j < gradeRanks[p].length; j++) {
//             if (gradeRanks[p][j].value !== '——') {
//                 gradetmp = 1;
//             }
//         }
//         if (gradetmp === 1) {
//             chart(gradeRanks[p], subjects[p], exams, 'grade', p);
//         } else {
//             $('#grade-' + [p]).remove();
//         }
//
//     }
// }
//
// function chart(data, subject, exam, type, i) {
//     var myChart = echarts.init($('#' + type + '-' + i)[0]);
//     var option = {
//         title: {
//             x: 'center',
//             text: subject,
//             textStyle: {
//                 fontWeight: '100',
//                 fontSize: '16'
//             },
//             top: 15
//         },
//         grid: { bottom: '80' },
//         tooltip: { trigger: 'axis' },
//         xAxis: {
//             type: 'category',
//             data: exam,
//             boundaryGap: false,
//         },
//         yAxis: {
//             type: 'value',
//             axisLabel: { formatter: '{value}' },
//         },
//
//         series: [
//             {
//                 name: '排名',
//                 type: 'line',
//                 data: data,
//                 connectNulls: true,
//             }
//         ]
//     };
//     myChart.setOption(option);
// }
