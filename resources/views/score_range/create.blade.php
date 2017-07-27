@extends('layouts.master')
@section('header')
    <h1>添加新成绩统计项</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post','id' => 'formScoreRange','data-parsley-validate' => 'true']) !!}
    @include('score_range.create_edit')
    {!! Form::close() !!}
@endsection