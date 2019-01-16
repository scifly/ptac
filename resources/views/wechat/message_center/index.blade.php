@extends('layouts.wap')
@section('title') 消息中心 @endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('/css/wechat/message_center/index.css') !!}">
@endsection
@section('content')
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
                    'placeholder' => '请在此输入关键词搜索消息',
                    'class' => 'weui-input',
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
    <div id="filters" class="weui-popup__container popup-bottom">
        <div class="weui-popup__overlay"></div>
        <div class="weui-popup__modal">
            <div class="toolbar">
                <div class="toolbar-inner">
                    <a href="#" class="picker-button close-popup">关闭</a>
                    <h1 class="title">消息过滤</h1>
                </div>
            </div>
            <div class="modal-content">
                <div class="weui-cells weui-cells_form">
                    <div class="weui-cell weui-cell_select weui-cell_select-after">
                        <div class="weui-cell__hd">
                            {!! Form::label('message_type', '消息类型', ['class' => 'weui-label']) !!}
                        </div>
                        <div class="weui-cell__bd">
                            {!! Form::select('message_type', $messageTypes, null, ['class' => 'weui-select']) !!}
                        </div>
                    </div>
                    <div class="weui-cell weui-cell_select weui-cell_select-after">
                        <div class="weui-cell__hd">
                            {!! Form::label('media_type', '消息格式', ['class' => 'weui-label']) !!}
                        </div>
                        <div class="weui-cell__bd">
                            {!! Form::select('media_type', $mediaTypes, null, ['class' => 'weui-select']) !!}
                        </div>
                    </div>
                    <div class="weui-cell">
                        <div class="weui-cell__hd">
                            {!! Form::label('start', '开始日期', ['class' => 'weui-label']) !!}
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
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{!! asset('/js/wechat/message_center/index.js') !!}"></script>
@endsection