@extends('layouts.master')
@section('header')
    <h1>编辑科目</h1>
@endsection
@section('content')
    {!! Form::model($subjectModules, ['method' => 'put', 'id' => 'formSubjectModule']) !!}
    @include('subject_module.create_edit')
    {!! Form::close() !!}
@endsection