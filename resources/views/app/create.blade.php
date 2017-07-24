@extends('layouts.master')
@section('header')
    <h1>添加新应用</h1>
@endsection
@section('content')
    {!! Form::open(['url' => '/apps/store', 'method' => 'post', 'id' => 'formApp']) !!}
    @include('app.create_edit')
    {!! Form::close() !!}
@endsection