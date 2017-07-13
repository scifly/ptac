@extends('layouts.master')
@section('header')
    <h2>添加新学校</h2>
@endsection
@section('content')
    {!! Form::open(['url' => '/schools', 'method' => 'post']) !!}
    @include('partials.school')
    {!! Form::close() !!}
@endsection