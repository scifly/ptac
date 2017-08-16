@extends('layouts.master')
@section('header')
    <h1>添加消息</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post','id' => 'formMessage','data-parsley-validate' => 'true']) !!}
    @include('message.create_edit')
    {!! Form::close() !!}
@endsection