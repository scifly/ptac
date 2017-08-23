@extends('layouts.master')
@section('header')
    <h2>个人账户管理</h2>
@endsection
@section('content')
    {!! Form::model($personalInfo, ['method' => 'put', 'id' => 'formPersonalInfo', 'data-parsley-validate' => 'true']) !!}
    @include('personal_info.create_edit')
    {!! Form::close() !!}
@endsection