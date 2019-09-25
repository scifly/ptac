@extends('layouts.wap')
@section('title') 消息中心 @endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('info') !!}">
@endsection
@section('content')
    {!! Form::open(['method' => 'put', 'id' => 'formMessage']) !!}
    @include('wechat.info.create_edit')
    {!! Form::close() !!}
@endsection
@section('script')
    <script src="{!! asset('info') !!}"></script>
@endsection