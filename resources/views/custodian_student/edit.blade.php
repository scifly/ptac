@extends('layouts.master')
@section('header')
    <h1>编辑科目</h1>
@endsection
@section('content')
    {!! Form::model($subject, ['method' => 'put', 'id' => 'formCustodianStudent']) !!}
    @include('custodian_student.create_edit')
    {!! Form::close() !!}
@endsection