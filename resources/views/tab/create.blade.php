@extends('layouts.master')
@section('header')
    <h1>添加新卡片</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post','id' => 'formTab','data-parsley-validate' => 'true']) !!}
    @include('tab.create_edit')
    {!! Form::close() !!}
@endsection