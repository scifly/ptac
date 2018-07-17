@extends('wechat.layouts.master')
@section('title')
    <title>成绩中心</title>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/wechat/score/stat.css') }}">
@endsection
@section('content')
    <div class="header">
        <div class="info">
            <div class="time">
                <div class="subject-title">{{ date('Y-m', strtotime($examDate)) }}</div>
                <div class="days">{{ date('d', strtotime($examDate)) }}日</div>
            </div>
            <div class="test">
                <div class="subject-title">考试名</div>
                <div class="testName">{{ $examName }}</div>
            </div>
        </div>
        <div class="score">{{ $data['total']['total_score'] }}分</div>
    </div>
    <div class="otherinfo">
        <div class="average">
            <div class="byclass">
                <p>{{ $data['total']['class_avg'] }}</p>
                <p class="subject-title">班平均</p>
            </div>
            <div class="byschool">
                <p>{{ $data['total']['grade_avg'] }}</p>
                <p class="subject-title">年平均</p>
            </div>
        </div>
        <div class="ranke">
            <div class="byclass">
                <p>{{ $data['total']['class_rank'] }} / {{ $data['total']['class_count'] }}</p>
                <p class="subject-title">班排名</p>
            </div>
            <div class="byschool">
                <p>{{ $data['total']['grade_rank'] }} / {{ $data['total']['grade_count'] }}</p>
                <p class="subject-title">年排名</p>
            </div>
        </div>
    </div>
    <div class="tablemain">
        <div id="main"></div>
    </div>
    <div class="scorelist">
        @foreach($data['single'] as $single)
            <div class="scoreItem">
                <div class="title">{{ $single['sub'] }}</div>
                <div class="myscore">
                    <span class="subject-title">得分</span>
                    <span class="scoredata">{{ $single['score'] }}</span>
                </div>
                <div class="avescore">
                    <span class="subject-title">均分</span>{{ $single['avg'] }}
                </div>
            </div>
        @endforeach
    </div>
    <div style="height: 70px; width: 100%;"></div>
    <div class="footerTab">
        <a class="btnItem exam-link"
           href='{{ url($acronym . "/sc/detail?examId=". $examId ."&targetId=". $studentId . '&student=1') }}'>
            <i class="icon iconfont icon-document"></i>
            <p>单科</p>
        </a>
        <a class="btnItem footer-active" href="#">
            <i class="icon iconfont icon-renzheng7"></i>
            <p>综合</p>
        </a>
    </div>
@endsection
@section('script')
    <script src="{{ asset('js/wechat/score/stat.js') }}"></script>
@endsection
