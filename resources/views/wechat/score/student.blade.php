@extends('wechat.layouts.master')
@section('title')
    <title>成绩中心</title>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/wechat/score/student.css') }}">
@endsection
@section('content')
<div class="header">
    <div class="info">
        {!! Form::hidden('exam_id', $exam->id, ['id' => 'exam_id']) !!}
        {!! Form::hidden('student_id', $studentId, ['id' => 'student_id']) !!}
        {!! Form::hidden('names', implode(',', $total['names']), ['id' => 'names']) !!}
        {!! Form::hidden('scores', implode(',', $total['scores']), ['id' => 'scores']) !!}
        {!! Form::hidden('avgs', implode(',', $total['avgs']), ['id' => 'avgs']) !!}
        <div class="time">
            <div class="subtitle">
                {!! date('Y-m', strtotime($exam->start_date)) !!}
            </div>
            <div class="days">
                {!! date('d', strtotime($exam->start_date)) . '日' !!}
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
                {!! $exam->name !!}
            </div>
        </div>
    </div>
    <div class="score">
        {{ $score ? $score->score : '(成绩未录入)' }}
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
            <p>{!! ($score ? $score->class_rank : '--') . ' / ' . $stat['nClassScores']  !!}</p>
            <p class="subtitle">班排名</p>
        </div>
        <div class="byschool">
            <p>{!! ($score ? $score->grade_rank : '--') . ' / ' . $stat['nGradeScores']  !!}</p>
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
    <a class="btnItem" href='{{ url($acronym . "/sc/stat?examId=" . $exam->id . "&studentId=" . $studentId) }}'>
        <i class="icon iconfont icon-renzheng7"></i>
        <p>综合</p>
    </a>
</div>
@endsection
@section('script')
    <script src="{{ URL::asset('js/plugins/echarts.common.min.js') }}"></script>
    <script src="{{ URL::asset('js/wechat/score/student.js') }}"></script>
@endsection