@extends('layouts.master')
@section('header')
    <h2>编辑流程类型</h2>
@endsection
@section('content')
    {!! Form::model($pt, [
        'method' => 'put',
        'id' => 'formProcedureType',
        'data-parsley-validate' => 'true'
    ]) !!}
    @include('procedure_type.create_edit')
    {!! Form::close() !!}
@endsection