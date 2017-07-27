@extends('layouts.master')
@section('header')
    <h1>添加角色</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post','id' => 'formGroup','data-parsley-validate' => 'true']) !!}
    @include('group.create_edit')
    {!! Form::close() !!}
@endsection
