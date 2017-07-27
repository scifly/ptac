@extends('layouts.master')
@section('header')
    <h2>编辑流程步骤</h2>
@endsection
@section('content')
    {!! Form::model($procedureStep, [
        'method' => 'put',
        'id' => 'formProcedureStep',
        'data-parsley-validate' => 'true'
    ]) !!}
    @include('procedure_step.create_edit')
    {!! Form::close() !!}
@endsection