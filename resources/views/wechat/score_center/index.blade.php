@extends('layouts.wap')
@section('title') 成绩中心 @endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('/css/wechat/score_center/index.css') !!}">
@endsection
@section('content')
    <header class='wechat-header'>
        <h1 class="wechat-title">成绩中心</h1>
        <p class='wechat-sub-title'>教职员工</p>
    </header>
    <div class="multi-role">
        <div class="header">
            <div class="switchclass-item clearfix">
                <div class="switchclass-head">
                    <div class="weui-cell weui-cell_select weui-cell_select-after">
                        <div class="weui-cell__hd">
                            <label class="weui-label">班级</label>
                        </div>
                        <div class="weui-cell__bd title-name">
                            {!! Form::select('target_id', $targets, null, [
                                'id' => 'target_id',
                                'class' => 'weui-select'
                            ] ) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="weui-search-bar" id="searchBar">
                {!! Form::open(['method' => 'post', 'class' => 'weui-search-bar__form']) !!}
                <div class="weui-search-bar__box">
                    <i class="weui-icon-search"></i>
                    {!! Form::search('search', null, [
                        'id' => 'search',
                        'class' => 'weui-search-bar__input',
                        'placeholder' => '搜索',
                    ]) !!}
                    <a href="#" class="weui-icon-clear" id="searchClear"></a>
                </div>
                <label class="weui-search-bar__label" id="searchText">
                    <i class="weui-icon-search"></i>
                    <span>搜索</span>
                </label>
                {!! Form::close() !!}
                <a href="#" class="weui-search-bar__cancel-btn" id="searchCancel">取消</a>
            </div>
        </div>
        <!--考试列表-->
        @if (!empty($exams))
            <div class="weui-cells" style="margin-top: 0;">
                @foreach ($exams as $exam)
                    <a class="weui-cell weui-cell_access exam-link"
                       href="#" data-type="{!! $type !!}"
                       data-value="{!! $exam['id'] !!}"
                    >
                        <div class="weui-cell__bd"><p>{!! $exam['name'] !!}</p></div>
                        <div class="weui-cell__ft time">{!! $exam['start_date'] !!}</div>
                    </a>
                @endforeach
            </div>
        @endif
        <div class="loadmore">
            <span class="weui-loadmore__tips">
                @if (!empty($exams))
                    <i class="icon iconfont icon-shuaxin"> 加载更多</i>
                @else
                    暂无数据
                @endif
            </span>
        </div>
    </div>
@endsection
@section('script')
    <script src="{!! asset('/js/wechat/score_center/index.js') !!}"></script>
@endsection