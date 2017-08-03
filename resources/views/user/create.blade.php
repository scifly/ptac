@extends('layouts.master')
@section('header')
    <h1>添加新用户</h1>
@endsection
@section('content')
    {!! Form::open([
        'method' => 'post',
        'id' => 'formUser',
        'data-parsley-validate' => 'true'
    ]) !!}
    @include('user.create_edit')
    {!! Form::close() !!}
@endsection