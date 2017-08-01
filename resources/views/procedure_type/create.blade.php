@extends('layouts.master')
@section('header')
    <h1>添加新流程类型</h1>
@endsection
@section('content')
    {!! Form::open([
        'method' => 'post',
        'id' => 'formProcedureType',
        'data-parsley-validate' => 'true'
    ]) !!}
    @include('procedure_type.create_edit')
    {!! Form::close() !!}
@endsection