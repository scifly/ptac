@extends('layouts.master')
@section('header')
    <h2>编辑消息类型</h2>
@endsection
@section('content')
    {!! Form::model($messageType, [ 'method' => 'put', 'id' => 'formMessageType', 'data-parsley-validate' => 'true']) !!}
    @include('message_type.create_edit')
    {!! Form::close() !!}
@endsection