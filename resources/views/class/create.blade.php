@extends('layouts.master')
@section('header')
    <h1>添加新班级</h1>
@endsection
@section('content')
    {!! Form::open(['url' => 'classes/store', 'method' => 'post', 'id' => 'fromSquad']) !!}
    @include('class.create_edit')
    {!! Form::close() !!}
@endsection