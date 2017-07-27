@extends('layouts.master')
@section('header')
    <h1>编辑学生</h1>
@endsection
@section('content')
    {!! Form::model($student, ['method' => 'put', 'id' => 'formStudent']) !!}
    @include('student.create_edit')
    {!! Form::close() !!}
@endsection