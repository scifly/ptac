@extends('layouts.master')
@section('header')
    <h2>编辑运营者公司</h2>
@endsection
@section('content')
    {!! Form::model($company, ['method' => 'put', 'id' => 'formCompany']) !!}
    @include('company.create_edit')
    {!! Form::close() !!}
@endsection