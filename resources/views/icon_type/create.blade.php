@extends('layouts.master')
@section('header')
    <h1>添加新Icon类型</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post', 'id' => 'formIconType', 'data-parsley-validate' => 'true']) !!}
    @include('icon_type.create_edit')
    {!! Form::close() !!}
@endsection