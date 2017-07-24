@extends('layouts.master')
@section('header')
    <h1>添加新企业</h1>
@endsection
@section('content')
    {!! Form::open(['url' => '/corps/store', 'method' => 'post', 'id' => 'formCorp']) !!}
    @include('corp.create_edit')
    {!! Form::close() !!}
@endsection