@extends('layouts.master')
@section('header')
    <h1>添加监护人/学生关系</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post','id' => 'formCustodianStudent','data-parsley-validate' => 'true']) !!}
    @include('custodian_student.create_edit')
    {!! Form::close() !!}
@endsection