@extends('layouts.master')
@section('header')
    <h1>添加新流程类型</h1>
@endsection
@section('content')
    {!! Form::open(['url' => 'procedure_types/store', 'method' => 'post', 'id' => 'formProcedureType']) !!}
    @include('procedure_type.create_edit')
    {!! Form::close() !!}
@endsection