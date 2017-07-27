@extends('layouts.master')
@section('header')
    <h2>编辑学校类型</h2>
@endsection
@section('content')
    {!! Form::model($school, [
        'method' => 'put', 
        'id' => 'formSchoolType',
        'data-parsley-validate' => 'true'
    ]) !!}
    @include('school_type.create_edit')
    {!! Form::close() !!}
@endsection