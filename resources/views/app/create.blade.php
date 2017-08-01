@extends('layouts.master')
@section('header')
    <h1>添加新应用</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post', 'id' => 'formApp', 'data-parsley-validate' => 'true']) !!}
    @include('app.create_edit')
    {!! Form::close() !!}
@endsection