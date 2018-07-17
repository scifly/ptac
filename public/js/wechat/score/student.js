var $subjectId = $('#subject_id'),
    $examId = $('#exam_id'),
    $studentId = $('#student_id'),
    $names = $('#names'),
    $scores = $('#scores'),
    $avgs = $('#avgs');

FastClick.attach(document.body);
if ($.trim($('.score').html()) !== '(成绩未录入)') {
    showtable($scores.val(), $avgs.val(), $names.val());
}
$subjectId.on('change', function () {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../sc/detail',
        data: {
            _token: wap.token(),
            examId: $examId.val(),
            studentId: $studentId.val(),
            subject_id : $subjectId.val(),
            student: 1
        },
        success: function (result) {
            var html= '',
                score = result['score'],
                stat = result['stat'],
                total = result['total'],
                names = total['names'],
                scores = total['scores'],
                avgs = total['avgs'];

            $('.time .subject-title').html(
                score ? score['start_date'].substring(0,7) : '--'
            );
            $('.time .days').html(
                score ? score['start_date'].substring(8,10) + '日' : '--'
            );
            $('.test .testName').html(
                score ? score['exam_name'] : '--'
            );
            $('.header .score').html(
                score ? score['score'] : '(成绩未录入)'
            );
            html +=
                '<div class="average">' +
                    '<div class="byclass">' +
                        '<p>'+ stat['classAvg'] + '</p>' +
                        '<p class="subtitle">班平均</p>' +
                    '</div>'+
                    '<div class="byschool">' +
                        '<p>'+ stat['gradeAvg'] + '</p>' +
                        '<p class="subtitle">年平均</p>' +
                    '</div>' +
                '</div>' +
                '<div class="ranke">' +
                    '<div class="byclass">' +
                        '<p>'+ (score ? score['class_rank'] : '--') + '/' + stat['nClassScores'] + '</p>' +
                        '<p class="subtitle">班排名</p>' +
                    '</div>' +
                    '<div class="byschool">' +
                        '<p>'+ (score ? score['grade_rank'] : '--') + '/' + stat['nGradeScores']+'</p>' +
                        '<p class="subtitle">年排名</p>' +
                    '</div>' +
                '</div>';
            $('.otherinfo').html(html);
            score ? showtable(scores, avgs, names) : $('.main').html('');
        },
        error: function (e) {
            wap.errorHandler(e);
        }
    });
});
function showtable(myscore, class_score, test_name) {
    var myChart = echarts.init($('.main')[0]),
        option = {
            title: {
                x: 'center',
                text: '本考次该科成绩趋势图',
                textStyle: { fontWeight: '100', fontSize: '16' },
                top: 15,
            },
            grid: { y:'80', bottom:'80' },
            tooltip: { trigger: 'axis' },
            legend: { data:['我的成绩', '班平均成绩'], x: 'left', left:10, top:45 },
            xAxis:  { type: 'category', data: test_name, boundaryGap : false },
            yAxis: { type: 'value', axisLabel: { formatter: '{value}' } },
            dataZoom: [
                { type: 'slider', show: true, xAxisIndex: [0], start: 0, end: 50 }
            ],
            series: [
                { name:'我的成绩', type:'line', data:myscore },
                { name:'班平均成绩', type:'line', data:class_score },
            ]
        };

    myChart.setOption(option);
}