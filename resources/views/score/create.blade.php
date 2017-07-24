@extends('layouts.master')
@section('header')
    <h1>添加新学校</h1>
@endsection
@section('content')
    {!! Form::open(['url' => '/scores/store', 'method' => 'post', 'id' => 'formScore']) !!}
    @include('score.create_edit')
    {!! Form::close() !!}
@endsection