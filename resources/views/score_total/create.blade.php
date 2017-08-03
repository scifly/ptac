@extends('layouts.master')
@section('header')
    <h1>添加总成绩记录</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post','id' => 'formScoreTotal','data-parsley-validate' => 'true']) !!}
    @include('score_total.create_edit')
    {!! Form::close() !!}
@endsection