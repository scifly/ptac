@extends('layouts.master')
@section('header')
    <h1>添加新成绩</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post', 'id' => 'formScore', 'data-parsley-validate' => 'true']) !!}
    @include('score.create_edit')
    {!! Form::close() !!}
@endsection