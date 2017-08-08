@extends('layouts.master')
@section('header')
    <h1>添加新班级</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post', 'id' => 'formSquad', 'data-parsley-validate' => 'true']) !!}
    @include('class.create_edit')
    {!! Form::close() !!}
@endsection