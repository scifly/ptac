@extends('layouts.master')
@section('header')
    <h2>编辑学期</h2>
@endsection
@section('content')
    {!! Form::model($school, [
        'method' => 'put', 
        'id' => 'formSemester',
        'data-parsley-validate' => 'true'
    ]) !!}
    @include('semester.create_edit')
    {!! Form::close() !!}
@endsection