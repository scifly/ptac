@extends('layouts.master')
@section('header')
    <h1>添加新考试类型</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post', 'id' => 'fromExamType', 'data-parsley-validate' => 'true' ]) !!}
    @include('exam_type.create_edit')
    {!! Form::close() !!}
@endsection