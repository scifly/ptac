@extends('layouts.master')
@section('header')
    <h1>添加新流程</h1>
@endsection
@section('content')
    {!! Form::open(['url' => 'procedures/store', 'method' => 'post', 'id' => 'formProcedure']) !!}
    @include('procedure.create_edit')
    {!! Form::close() !!}
@endsection