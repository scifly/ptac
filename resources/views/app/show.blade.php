@extends('layouts.master')
@section('header')
    <a href="javascript:history.back();">Back to overview</a>
    <h2>
        {{ $app->name }}
    </h2>
    <a href="{{ url('apps/edit/' . $app->id) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('apps/delete/' . $app->id) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $app->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>应用名称：{{ $app->name }}</p>
    <p>应用备注：{{ $app->description }}</p>
    <p>应用id：{{ $app->agentid }}</p>
    <p>推送请求的访问协议和地址：{{ $app->url }}</p>
    <p>用于生成签名的token：{{ $app->token }}</p>
    <p>消息体的加密：{{ $app->encodingaeskey }}</p>
    <p>是否打开地理位置上报：{{ $app->report_location_flag==1 ? '是' : '否' }}</p>
    <p>企业应用头像的mediaid：{{ $app->logo_mediaid }}</p>
    <p>企业应用可信域名：{{ $app->redirect_domain }}</p>
    <p>是否接收用户变更通知：{{ $app->isreportuser==1 ? '是' : '否' }}</p>
    <p>是否上报用户进入应用事件：{{ $app->isreportenter==1 ? '是' : '否' }}</p>
    <p>主页型应用url：{{ $app->home_url }}</p>
    <p>关联会话url：{{ $app->chat_extension_url }}</p>
    <p>应用菜单：{{ $app->menu }}</p>
    <p>是否启用：{{ $app->enabled==1 ? '是' : '否' }}</p>
@endsection