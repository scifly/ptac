@extends('layouts.wap')
@section('title') 消息中心 @endsection
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