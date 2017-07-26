@extends('layouts.master')
@section('header')
    <h1>添加新流程</h1>
@endsection
@section('content')
    {!! Form::open([
        'method' => 'post',
        'id' => 'formProcedure',
        'data-parsley-validate' => 'true'
    ]) !!}
    @include('procedure.create_edit')
    {!! Form::close() !!}
@endsection