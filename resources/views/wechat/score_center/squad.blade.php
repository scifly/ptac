@extends('layouts.wap')
@section('title')
    <title>成绩中心</title>
@endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('css/wechat/score_center/squad.css') !!}">
@endsection
@section('content')
    <div class="header">
        <div class="title">{!! $data['exam'] !!}</div>
        <div class="myclass">{!! $data['exam'] !!}</div>
    </div>
    <div class="weui-search-bar" id="searchBar">
        <form class="weui-search-bar__form" action="">
            <div class="weui-search-bar__box">
                <i class="weui-icon-search"></i>
                <input type="search" class="weui-search-bar__input" name="student" id="searchInput" placeholder="搜索"
                       required="">
                <a href="javascript:" class="weui-icon-clear" id="searchClear"></a>
            </div>
            <label class="weui-search-bar__label" id="searchText"
                   style="transform-origin: 0 0 0; opacity: 1; transform: scale(1, 1);">
                <i class="weui-icon-search"></i>
                <span>搜索</span>
            </label>
        </form>
        <a href="javascript:" class="weui-search-bar__cancel-btn" id="searchCancel">取消</a>
    </div>
    <div class="main">
        <table class="tongji-table" style="width: 100%;" cellspacing="0">
            <thead>
            <tr>
                <td width="40">姓名</td>
                <td width="40">学号</td>
                <td width="40">班排</td>
                <td width="40">年排</td>
                <td width="40">总分</td>
                <td width="80">成绩详情</td>
            </tr>
            </thead>
            <tbody>
                @foreach($data['items'] as $item)
                    <tr class="tongji-item" data-s="{!! $item['student_id'] !!}" data-e="{!! $item['exam_id'] !!}">
                        <td>{!! $item['realname'] !!}</td>
                        <td>{!! $item['student_number'] !!}</td>
                        <td>{!! $item['class_rank'] !!}</td>
                        <td>{!! $item['grade_rank'] !!}</td>
                        <td>{!! $item['total'] !!}</td>
                        <td>
                            @foreach($item['detail'] as $detail)
                                <div>
                                    <span class="subj">{!! $detail['subject'] !!}</span>
                                    <span class="score">{!! $detail['score'] !!}</span>
                                    <div style="clear: both;"></div>
                                </div>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="height: 70px;width: 100%;"></div>
    <div class="footerTab">
        <a class="btnItem footer-active">
            <i class="icon iconfont icon-document"></i>
            <p>详情</p>
        </a>
        <a class="btnItem" href='{!! url($acronym . "/score_centers/analyze?examId=". $examId ."&classId=". $classId) !!}'>
            <i class="icon iconfont icon-renzheng7"></i>
            <p>统计</p>
        </a>
        <div style="clear: both;"></div>
    </div>
@endsection
@section('script')
    <script>
        $('.tongji-item').click(function () {
            var studentId = $(this).attr('data-s'),
                examId = $(this).attr('data-e');
            window.location.href = 'detail?targetId=' + studentId + '&examId=' + examId + '&student=1';
        });
    </script>
@endsection