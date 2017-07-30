@extends('layouts.master')
@section('header')
    <h2>编辑考试类型</h2>
@endsection
@section('content')
    {!! Form::model($examType, [ 'method' => 'put', 'id' => 'fromExamType', 'data-parsley-validate' => 'true']) !!}
    @include('exam_type.create_edit')
    {!! Form::close() !!}
@endsection