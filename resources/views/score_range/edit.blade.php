@extends('layouts.master')
@section('header')
    <h1>编辑成绩统计项</h1>
@endsection
@section('content')
    {!! Form::model($scoreRange, ['method' => 'put', 'id' => 'formScoreRange']) !!}
    @include('score_range.create_edit')
    {!! Form::close() !!}
@endsection