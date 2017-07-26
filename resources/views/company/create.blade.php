@extends('layouts.master')
@section('header')
    <h1>添加新运营者公司</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post', 'id' => 'formCompany', 'data-parsley-validate' => 'true']) !!}
    @include('company.create_edit')
    {!! Form::close() !!}
@endsection