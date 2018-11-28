@extends('layouts.wap')
@section('title') 成绩中心 @endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('/css/wechat/score_center/index.css') !!}">
@endsection
@section('content')
    <div class="multi-role">
        <div class="header">
            <div class="switchclass-item clearfix">
                <div class="switchclass-head">
                    <div class="weui-cell weui-cell_select weui-cell_select-after">
                        <div class="weui-cell__bd title-name">
                            {!! Form::select('target_id', $targets, null, [
                                'id' => 'target_id',
                                'class' => 'weui-input'
                            ] ) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="weui-search-bar" id="searchBar">
                <form class="weui-search-bar__form" action="#">
                    <div class="weui-search-bar__box">
                        <i class="weui-icon-search"></i>
                        {!! Form::search('search', null, [
                            'id' => 'search',
                            'class' => 'weui-search-bar__input',
                            'placeholder' => '搜索',
                        ]) !!}
                        <a href="#" class="weui-icon-clear" id="searchClear"></a>
                    </div>
                    <label class="weui-search-bar__label" id="searchText"
                           style="transform-origin: 0 0 0; opacity: 1; transform: scale(1, 1);">
                        <i class="weui-icon-search"></i>
                        <span>搜索</span>
                    </label>
                </form>
                <a href="#" class="weui-search-bar__cancel-btn" id="searchCancel">取消</a>
            </div>
        </div>
        <!--考试列表-->
        <div class="weui-cells" style="margin-top: 0;">
            @if (!empty($exams))
                @foreach ($exams as $exam)
                    <a class="weui-cell weui-cell_access exam-link"
                       href="#" data-type="{!! $type !!}"
                       data-value="{!! $exam['id'] !!}"
                    >
                        <div class="weui-cell__bd"><p>{!! $exam['name'] !!}</p></div>
                        <div class="weui-cell__ft time">{!! $exam['start_date'] !!}</div>
                    </a>
                @endforeach
            @else
                暂无数据
            @endif
        </div>
        <div class="loadmore">
            <span class="weui-loadmore__tips"><i class="icon iconfont icon-shuaxin"></i>加载更多 </span>
        </div>
    </div>
@endsection
@section('script')
    <script src="{!! asset('/js/wechat/score_center/index.js') !!}"></script>
@endsection