@extends('layouts.master')
@section('header')
    <h1>添加新流程步骤</h1>
@endsection
@section('content')
    {!! Form::open([
        'method' => 'post',
        'id' => 'formProcedureStep',
        'data-parsley-validate' => 'true'
    ]) !!}
    @include('procedure_step.create_edit')
    {!! Form::close() !!}
@endsection