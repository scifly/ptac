@extends('layouts.wap')
@section('title') 消息中心 @endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('message') !!}">
@endsection
@section('content')
    {!! Form::open(['method' => 'put', 'id' => 'formMessage']) !!}
    @include('wechat.message.create_edit')
    {!! Form::close() !!}
@endsection
@section('script')
    <script src="{!! asset('message') !!}"></script>
@endsection