@extends('layouts.wap')
@section('title') 消息中心 @endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('/css/wechat/message_center/index.css') !!}">
@endsection
@section('content')
    <div class="weui-cells weui-cells_form" style="margin-top: 0;">
        <div class="weui-cell">
            <div class="weui-cell__hd" style="text-align: left;">
                <img alt="" src="{!! asset("img/nav.png") !!}" style="width: 16px;"/>
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
    <div class="weui-mask weui-actions_mask weui-mask--visible"></div>
    <div class="weui-actionsheet  weui-actionsheet_toggle" id="weui-actionsheet">
        <div class="weui-actionsheet__title">
            <p class="weui-actionsheet__title-text">选择操作</p>
        </div>
        <div class="weui-actionsheet__menu">
            <div class="weui-actionsheet__cell color-primary">收件箱</div>
            <div class="weui-actionsheet__cell color-warning">发件箱</div>
            <div class="weui-actionsheet__cell color-danger">按消息类型过滤</div>
            <div class="weui-actionsheet__cell color-danger">按消息格式过滤</div>
        </div>
        <div class="weui-actionsheet__action">
            <div class="weui-actionsheet__cell weui-actionsheet_cancel">取消</div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{!! asset('/js/wechat/message_center/index.js') !!}"></script>
@endsection