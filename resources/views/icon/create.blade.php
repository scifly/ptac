@extends('layouts.master')
@section('header')
    <h1>添加新Icon</h1>
@endsection
@section('content')
    {!! Form::open([
        'method' => 'post',
        'id' => 'formIcon',
        'data-parsley-validate' => 'true'
    ]) !!}
    @include('icon.create_edit')
    {!! Form::close() !!}
@endsection