@extends('layouts.master')
@section('header')
    <h1>添加次分类</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post','id' => 'formSubjectModule','data-parsley-validate' => 'true']) !!}
    @include('subject_module.create_edit')
    {!! Form::close() !!}
@endsection