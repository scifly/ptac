@extends('layouts.master')
@section('header')
    <h2>编辑流程记录</h2>
@endsection
@section('content')
    {!! Form::model($procedure, [
        'method' => 'put',
        'id' => 'formProcedure',
        'data-parsley-validate' => 'true'
    ]) !!}
    @include('procedure.create_edit')
    {!! Form::close() !!}
@endsection