@extends('layouts.master')
@section('header')
    <h2>编辑流程类型</h2>
@endsection
@section('content')
    {!! Form::model($pt, ['url' => 'procedure_types/update' . $pt->id, 'method' => 'put', 'id' => 'formProcedureType']) !!}
    @include('procedure_type.create_edit')
    {!! Form::close() !!}
@endsection