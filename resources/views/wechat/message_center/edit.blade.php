@extends('wechat.layouts.master')
@section('title')
    <title>消息中心</title>
@endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('/css/wechat/message_center/message.css') !!}">
@endsection
@section('content')
    {!! Form::open(['method' => 'put', 'id' => 'formMessage']) !!}
    @include('wechat.message_center.create_edit')
    {!! Form::close() !!}
@endsection
@section('script')
    <script src="{!! asset('/js/wechat/message_center/edit.js') !!}"></script>
@endsection