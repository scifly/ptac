@extends('layouts.master')
@section('header')
    <h1>添加消息类型</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post', 'id' => 'formMessageType', 'data-parsley-validate' => 'true' ]) !!}
    @include('message_type.create_edit')
    {!! Form::close() !!}
@endsection