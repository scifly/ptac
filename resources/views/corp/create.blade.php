@extends('layouts.master')
@section('header')
    <h1>添加新企业</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post', 'id' => 'formCorp', 'data-parsley-validate' => 'true']) !!}
    @include('corp.create_edit')
    {!! Form::close() !!}
@endsection