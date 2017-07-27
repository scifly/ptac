@extends('layouts.master')
@section('header')
    <h1>添加新学期</h1>
@endsection
@section('content')
    {!! Form::open([
        'method' => 'post',
        'id' => 'formSemester',
        'data-parsley-validate' => 'true'
    ]) !!}
    @include('semester.create_edit')
    {!! Form::close() !!}
@endsection