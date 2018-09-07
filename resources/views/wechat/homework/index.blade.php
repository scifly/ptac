@extends('layouts.wap')
@section('title')
    <title>微信h5支付测试</title>
@endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('/css/wechat/homework/index.css') !!}">
@endsection
@section('content')
    {!! Form::hidden('params', $jsApiParameters, ['id' => 'params']) !!}
    {!! Form::hidden('url', $editAddress, ['id' => 'url']) !!}
    <span style="color: #9ACD32;">
        <strong>
            该笔订单支付金额为
            <span id="amount">1分</span>
            钱
        </strong>
    </span>
    <div style="margin: 0 auto;">
        <button id="pay">立即支付</button>
    </div>
@endsection
@section('script')
    <script src="{!! asset('/js/wechat/homework/index.js') !!}"></script>
@endsection