@extends('layouts.master')
@section('header')
    <h1>添加新Action</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post', 'id' => 'formAction', 'data-parsley-validate' => 'true']) !!}
    @include('action.create_edit')
    {!! Form::close() !!}
@endsection