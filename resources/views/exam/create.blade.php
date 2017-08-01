@extends('layouts.master')
@section('header')
    <h1>添加新考试</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post', 'id' => 'fromExam', 'data-parsley-validate' => 'true' ]) !!}
    @include('exam.create_edit')
    {!! Form::close() !!}
@endsection