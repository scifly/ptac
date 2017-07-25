@extends('layouts.master')
@section('header')
    <h1>添加新科目</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post','id' => 'formSubject','data-parsley-validate' => 'true']) !!}
    @include('subject.create_edit')
    {!! Form::close() !!}
@endsection