@extends('layouts.master')
@section('header')
    <h1>编辑消息</h1>
@endsection
@section('content')
    {!! Form::model($message, ['method' => 'put', 'id' => 'formMessage']) !!}
    @include('message.create_edit')
    {!! Form::close() !!}
@endsection