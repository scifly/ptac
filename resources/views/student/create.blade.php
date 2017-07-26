@extends('layouts.master')
@section('header')
    <h1>添加新学员</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post','id' => 'formStudent','data-parsley-validate' => 'true']) !!}
    @include('student.create_edit')
    {!! Form::close() !!}
@endsection