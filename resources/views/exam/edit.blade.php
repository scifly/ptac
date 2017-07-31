@extends('layouts.master')
@section('header')
    <h2>编辑考试</h2>
@endsection
@section('content')
    {!! Form::model($exam, [ 'method' => 'put', 'id' => 'fromExam', 'data-parsley-validate' => 'true']) !!}
    @include('exam.create_edit')
    {!! Form::close() !!}
@endsection