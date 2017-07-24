@extends('layouts.master')
@section('header')
    <h1>添加新运营者公司</h1>
@endsection
@section('content')
    {!! Form::open(['url' => '/companies/store', 'method' => 'post', 'id' => 'formCompany']) !!}
    @include('company.create_edit')
    {!! Form::close() !!}
@endsection