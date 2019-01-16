@extends('layouts.wap')
@section('title') 消息中心 @endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('/css/wechat/message_center/index.css') !!}">
@endsection
@section('content')
    <div class="weui-cells weui-cells_form">
        <div class="weui-cell">
            <div class="weui-cell__hd" style="text-align: left;">
                <img alt="" src="{!! asset("img/nav.png") !!}" style="width: 16px;"/>
            </div>
            <div class="weui-cell__bd">
                {!! Form::text('search', null, [
                    'id' => 'search',
                    'placeholder' => '搜索消息',
                    'class' => 'weui-input'
                ]) !!}
            </div>
            <div class="weui-cell__ft">
                <a class="icon iconfont icon-add c-green"
                   href="{!! url($acronym . '/message_centers/create') !!}"
                ></a>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{!! asset('/js/wechat/message_center/index.js') !!}"></script>
@endsection