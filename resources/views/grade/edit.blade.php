@extends('layouts.master')
@section('header')
    <h2>编辑年级</h2>
@endsection
@section('content')
    {!! Form::model($grade, [ 'method' => 'put',  'id' => 'fromGrade']) !!}
    @include('grade.create_edit')
    {!! Form::close() !!}
@endsection