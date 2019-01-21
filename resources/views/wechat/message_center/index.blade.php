@extends('layouts.wap')
@section('title') 消息中心 @endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('/css/wechat/message_center/index.css') !!}">
@endsection
@section('content')
    <!-- 搜索 -->
    <div class="weui-cells weui-cells_form" style="margin-top: 0;">
        <div class="weui-cell">
            <div class="weui-cell__hd" style="text-align: left;">
                <a href="#" id="show-actions">
                    <img alt="" src="{!! asset("img/nav.png") !!}" style="width: 16px;"/>
                </a>
            </div>
            <div class="weui-cell__bd">
                {!! Form::text('search', null, [
                    'id' => 'search',
                    'placeholder' => '搜索消息',
                    'class' => 'weui-input',
                    'type' => 'search',
                    'style' => 'padding-left: 8px;'
                ]) !!}
            </div>
            <div class="weui-cell__ft">
                <a class="icon iconfont icon-add c-green"
                   href="{!! url($acronym . '/message_centers/create') !!}"
                ></a>
            </div>
        </div>
    </div>
    <!-- 消息列表 -->
    <div class="page_bd">
        <div class="weui-panel">
            {!! Form::hidden('page', 1, ['id' => 'page']) !!}
            {!! Form::hidden('folder', 'all', ['id' => 'folder']) !!}
            <div class="weui-panel__hd color-primary">所有消息</div>
            <div class="weui-panel__bd" id="msg_list">
                {!! $messages !!}
            </div>
            @if (empty($messages))
                <div class="weui-loadmore weui-loadmore_line">
                    <span class="weui-loadmore__tips">暂无记录</span>
                </div>
            @endif
            <div class="weui-loadmore" style="display: none;">
                <i class="weui-loading"></i>
                <span class="weui-loadmore__tips">正在加载</span>
            </div>
        </div>
    </div>
    <!-- 过滤 -->
    <div id="filters" class="weui-popup__container popup-bottom">
        <div class="weui-popup__overlay"></div>
        <div class="weui-popup__modal">
            <div class="toolbar">
                <div class="toolbar-inner">
                    <h1 class="title">消息过滤</h1>
                </div>
            </div>
            <div class="modal-content">
                <div class="weui-cells weui-cells_form">
                    <div class="weui-cell weui-cell_select weui-cell_select-after">
                        <div class="weui-cell__hd">
                            {!! Form::label('message_type_id', '消息类型', [
                                'class' => 'weui-label'
                            ]) !!}
                        </div>
                        <div class="weui-cell__bd">
                            {!! Form::select('message_type_id', $messageTypes, null, [
                                'class' => 'weui-select'
                            ]) !!}
                        </div>
                    </div>
                    <div class="weui-cell weui-cell_select weui-cell_select-after">
                        <div class="weui-cell__hd">
                            {!! Form::label('media_type_id', '消息格式', [
                                'class' => 'weui-label'
                            ]) !!}
                        </div>
                        <div class="weui-cell__bd">
                            {!! Form::select('media_type_id', $mediaTypes, null, [
                                'class' => 'weui-select'
                            ]) !!}
                        </div>
                    </div>
                    <div class="weui-cell">
                        <div class="weui-cell__hd">
                            {!! Form::label('start', '开始日期', [
                                'class' => 'weui-label'
                            ]) !!}
                        </div>
                        <div class="weui-cell__bd">
                            {!! Form::text('start', null, [
                                'class' => 'weui-input',
                                'data-toggle' => 'date'
                            ]) !!}
                        </div>
                    </div>
                    <div class="weui-cell">
                        <div class="weui-cell__hd">
                            {!! Form::label('end', '截止日期', ['class' => 'weui-label']) !!}
                        </div>
                        <div class="weui-cell__bd">
                            {!! Form::text('end', null, [
                                'class' => 'weui-input',
                                'data-toggle' => 'date'
                            ]) !!}
                        </div>
                    </div>
                </div>
                <div class="weui-btn-area">
                    <a class="weui-btn weui-btn_primary close-popup" href="#" id="filter">
                        确定
                    </a>
                    <a class="weui-btn weui-btn_default close-popup" href="#" id="cancel">
                        取消
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{!! asset('/js/wechat/message_center/index.js') !!}"></script>
@endsection