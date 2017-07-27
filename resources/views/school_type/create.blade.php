@extends('layouts.master')
@section('header')
    <h1>添加新学校</h1>
@endsection
@section('content')
    {!! Form::open([
        'method' => 'post',
        'id' => 'formSchoolType',
        'data-parsley-validate' => 'true'
    ]) !!}
    @include('school_type.create_edit')
    {!! Form::close() !!}
@endsection