@extends('layouts.wap')
@section('title') 消息中心 @endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('/css/wechat/message_center/index.css') !!}">
@endsection
@section('content')
    <div class="weui-cells weui-cells_form color-success" style="margin-top: 0;">
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
            你的内容放在这里...
        </div>
    </div>
@endsection
@section('script')
    <script src="{!! asset('/js/wechat/message_center/index.js') !!}"></script>
@endsection