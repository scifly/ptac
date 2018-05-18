<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <meta name="csrf_token" content="{{ csrf_token() }}" id="csrf_token">
    <title>学生考试列表</title>
    <link rel="stylesheet" href="{{ URL::asset('css/weui.min.css') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/jquery-weui.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/wechat/icon/iconfont.css') }}">
    <style>
        body, html {
            height: 100%;
            width: 100%;
            overflow-x: hidden;
        }
        body{
            margin: 0;
            padding:0;
            background-color: #f2f2f2;
            font-family: "微软雅黑", serif;
        }
        a{
            color: #333;
        }
        ::-webkit-scrollbar {
            width: 0;
        }
        ::-webkit-scrollbar:horizontal {
            height: 0;
        }
        .main{
            height: 100%;
            width: 100%;
            background-color: #f2f2f2;
        }
        .footerTab{
            position: fixed;
            bottom: 0;
            height: 50px;
            width: 100%;
            background-color: #fff;
            opacity: 0.7;
            border-top: 1px solid #ddd;
        }
        .footerTab .btnItem{
            display: inline-block;
            width: 50%;
            float: left;
            height: 100%;
            text-align: center;
            position: relative;
        }
        .footerTab .btnItem i{
            font-size: 24px;
            margin-top: -3px;
        }
        .footerTab .btnItem p{
            font-size: 14px;
            top: 28px;
            position: absolute;
            width: 100%;
        }
        .footer-active {
            color: #1aad19;
        }

        .header{
            background-color: #1aad19;
            width: 94%;
            color: #fff;
            padding: 5px 3% 0px;
        }
        .header .info{
            padding-bottom: 10px;
        }
        .header .info:after , .otherinfo:after , .footerTab:after{
            content: "\0020";
            display: block;
            height: 0;
            clear: both;
        }


        .subject-title{
            font-size: 12px;
        }
        .header .subject-title{
            color: #ddd;
        }
        .otherinfo .subject-title{
            color: #999;
        }
        .time ,.subject ,.test{
            /*display: inline-block;*/
            float: left;
        }
        .header .time{
            width: 22%;
            border-right: 1px solid #ddd;
        }
        .header .subject{
            width: 25%;
            margin-left: 10px;
        }
        .header .test{
            width: calc(53% - 21px);
            margin-left: 10px;
        }
        .header .subject .subject-choose{
            border: 1px solid #fff;
            width: 90%;
        }
        .header .time .days{
            font-size: 20px;
            margin-top: -5px;
        }
        .header .score{
            height: 40px;
            line-height: 40px;
            font-size: 20px;
            border-top: 1px solid #ccc;
            text-align: center;
        }
        .otherinfo{
            width: 94%;
            margin:10px 3%;
            text-align: center;
        }
        .otherinfo .average ,.otherinfo .ranke{
            background-color: #fff;
            float: left;
            padding: 5px 0;
        }
        .otherinfo .average{
            width: 40%;
        }
        .otherinfo .ranke{
            width:calc(60% - 10px);
            margin-left: 10px;
        }
        .byclass,.byschool{
            width:calc(50% - 1px) ;
            float: left;
        }
        .byclass{
            border-right: 1px solid #ddd;
        }
        .tablemain{
            width: 94%;
            margin: 10px 3%;
            background-color: #fff;
            padding-bottom: 20px;
        }
    </style>
</head>
<body ontouchstart>
<div class="header">
    <div class="info">
        {!! Form::hidden('exam_id', $examId, ['id' => 'exam_id']) !!}
        {!! Form::hidden('student_id', $studentId, ['id' => 'student_id']) !!}
        {!! Form::hidden('names', $total['name'], ['id' => 'names']) !!}
        {!! Form::hidden('scores', $total['score'], ['id' => 'scores']) !!}
        {!! Form::hidden('avgs', $total['avg'], ['id' => 'avgs']) !!}
        <div class="time">
            <div class="subtitle">
                {{ $score ? date('Y-m', strtotime($score->exam->start_date)) : '--' }}
            </div>
            <div class="days">
                {{ $score ? date('d', strtotime($score->exam->start_date) }} 日
            </div>
        </div>
        <div class="subject">
            <div class="subtitle">科目</div>
            {!! Form::select('subject_id', $subjects, ($score ? $score->subject_id : null), [
                'id' => 'subject_id',
                'class' => 'weui-input subject-choose',
            ]) !!}
        </div>
        <div class="test">
            <div class="subtitle">考试名</div>
            <div class="testName">
                {{ $score ? $score->exam->name : '--' }}
            </div>
        </div>
    </div>
    <div class="score">
        {{ $score ? $score->score : '--' }}
    </div>
</div>
<div class="otherinfo">
    <div class="average">
        <div class="byclass">
            <p>{{ $stat['classAvg'] }}</p>
            <p class="subtitle">班平均</p>
        </div>
        <div class="byschool">
            <p>{{ $stat['gradeAvg'] }}</p>
            <p class="subtitle">年平均</p>
        </div>
    </div>
    <div class="ranke">
        <div class="byclass">
            <p>{!! ($score ? $score->class_rank : 'na') . ' / ' . $stat['nClassScores']  !!}</p>
            <p class="subtitle">班排名</p>
        </div>
        <div class="byschool">
            <p>{!! ($score ? $score->grade_rank : 'na') . ' / ' . $stat['nGradeScores']  !!}</p>
            <p class="subtitle">年排名</p>
        </div>
    </div>
</div>

<div class="tablemain">
    <div class="main" style="width: 100%;height: 350px;"></div>
</div>
<div style="height: 70px;width: 100%;"></div>
<div class="footerTab" >
    <a class="btnItem footer-active" href="#">
        <i class="icon iconfont icon-document"></i>
        <p>单科</p>
    </a>
    <a class="btnItem" href='{{url("wechat/score/cus_total?examId=".$examId."&studentId=".$studentId)}}'>
        <i class="icon iconfont icon-renzheng7"></i>
        <p>综合</p>
    </a>
</div>
<script src="{{URL::asset('js/jquery.min.js')}}"></script>
<script src="{{URL::asset('js/fastclick.js')}}"></script>
<script src="{{URL::asset('js/jquery-weui.min.js')}}"></script>
<script src="{{URL::asset('js/plugins/echarts.common.min.js')}}"></script>
<script>
    var $subjectId = $('#subject_id'),
        $examId = $('#exam_id'),
        $studentId = $('#student_id'),
        $names = $('#names'),
        $scores = $('#scores'),
        $avgs = $('#avgs');

    FastClick.attach(document.body);
    showtable($scores.val(), $avgs.val(), $names.val());
    $subjectId.on("change", function () {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '../sc/detail',
            data: {
                examId: $examId.val(),
                studentId: $studentId.val(),
                subject_id : $subjectId.val(),
                _token: $('#csrf_token').attr('content')
            },
            success: function (result) {
                var html= '',
                    score = result['score'],
                    stat = result['stat'],
                    total = result['total'],
                    names = total['name'],
                    scores = total['score'],
                    avgs = total['avg'];

                $('.time .subject-title').html(score['start_date'].substring(0,7));
                $('.time .days').html(score['start_date'].substring(8,10) + '日');
                $('.test .testName').html(score['exam_name']);
                $('.header .score').html(score['score']);
                html +=
                    '<div class="average">' +
                        '<div class="byclass">' + '<p>'+ stat['classAvg'] + '</p>' + '<p class="subtitle">班平均</p>' + '</div>'+
                        '<div class="byschool">' + '<p>'+ stat['gradeAvg'] + '</p>' + '<p class="subtitle">年平均</p>' + '</div>' +
                    '</div>' +
                    '<div class="ranke">' +
                        '<div class="byclass">' +
                            '<p>'+ score['class_rank'] +'/'+ stat['nClassScores'] + '</p>' +
                            '<p class="subtitle">班排名</p>' +
                        '</div>' +
                        '<div class="byschool">' +
                            '<p>'+ score['grade_rank'] +'/'+ stat['nGradeScores']+'</p>' +
                            '<p class="subtitle">年排名</p>' +
                        '</div>' +
                    '</div>';
                $('.otherinfo').html(html);

                showtable(scores, avgs, names);
            }
        });
    });
    function showtable(myscore, class_score, test_name){
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
                legend: { data:['我的成绩','班平均成绩'], x: 'left', left:10, top:45 },
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
</script>
</body>
</html>
