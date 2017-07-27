@extends('layouts.master')
@section('header')
    <h1>添加新教职员工</h1>
@endsection
@section('content')
    {!! Form::open([ 'method' => 'post', 'id' => 'fromEducator']) !!}
    @include('educator.create_edit')
    {!! Form::close() !!}
@endsection